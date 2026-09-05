<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ManagementProfileFolder;
use App\Models\NavigationMenuItem;
use App\Models\SiteContentItem;
use App\Services\PublicNavigationService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Illuminate\View\View;

class ManagementController extends Controller
{
    public function index(): View
    {
        $folders=ManagementProfileFolder::query()->withCount('profiles')->with('profiles')->orderBy('sort_order')->orderBy('id')->get();
        return view('admin.management.index',compact('folders'));
    }
    public function folderCreate(): View { return view('admin.management.folder-form',['folder'=>new ManagementProfileFolder(['status'=>'published'])]); }
    public function folderStore(Request $request): RedirectResponse
    {
        $data=$request->validate(['name'=>['required','string','max:120'],'status'=>['required','in:draft,published']]);
        $folder=new ManagementProfileFolder();$folder->name=$data['name'];$folder->slug=$this->uniqueFolderSlug($data['name']);$folder->status=$data['status'];$folder->sort_order=(int)(ManagementProfileFolder::query()->max('sort_order')??0)+1;$folder->save();
        $this->syncFolderNavigation($folder,true);
        return redirect()->route('admin.profile-builder.index')->with('status','Profile folder created.');
    }
    public function folderEdit(ManagementProfileFolder $folder): View { return view('admin.management.folder-form',compact('folder')); }
    public function folderUpdate(Request $request,ManagementProfileFolder $folder): RedirectResponse
    {
        $data=$request->validate(['name'=>['required','string','max:120'],'status'=>['required','in:draft,published']]);$folder->name=$data['name'];$folder->slug=$this->uniqueFolderSlug($data['name'],$folder->id);$folder->status=$data['status'];$folder->save();$this->syncFolderNavigation($folder,false);
        return redirect()->route('admin.profile-builder.index')->with('status','Profile folder updated.');
    }
    public function folderDestroy(ManagementProfileFolder $folder): RedirectResponse
    {
        if($folder->profiles()->exists()) return back()->withErrors(['folder'=>'This folder contains profiles. Move or delete its profiles before deleting the folder.']);
        NavigationMenuItem::query()->where('source_key','management_folder:'.$folder->id)->delete();$folder->delete();app(PublicNavigationService::class)->clear('main');
        return back()->with('status','Profile folder deleted.');
    }
    public function folderReorder(Request $request): JsonResponse
    {
        $data=$request->validate(['order'=>['required','array'],'order.*'=>['integer']]);$folders=ManagementProfileFolder::query()->whereIn('id',$data['order'])->get()->keyBy('id');
        foreach($data['order'] as $position=>$id)if(isset($folders[$id]))$folders[$id]->update(['sort_order'=>$position+1]);
        return response()->json(['ok'=>true]);
    }
    public function create(): View { return view('admin.management.form',['member'=>new SiteContentItem(['type'=>'management','status'=>'draft']),'folders'=>ManagementProfileFolder::query()->orderBy('sort_order')->orderBy('id')->get()]); }
    public function store(Request $request): RedirectResponse { $member=new SiteContentItem();$this->save($member,$request);return redirect()->route('admin.profile-builder.index')->with('status','Profile created.'); }
    public function edit(SiteContentItem $member): View { abort_unless($member->type==='management',404);return view('admin.management.form',['member'=>$member,'folders'=>ManagementProfileFolder::query()->orderBy('sort_order')->orderBy('id')->get()]); }
    public function update(Request $request,SiteContentItem $member): RedirectResponse { abort_unless($member->type==='management',404);$this->save($member,$request);return redirect()->route('admin.profile-builder.index')->with('status','Profile updated.'); }
    public function destroy(SiteContentItem $member): RedirectResponse { abort_unless($member->type==='management',404);foreach([$member->image_path,$member->visiting_card_path] as $path)if($path)Storage::disk('public')->delete($path);$member->delete();return back()->with('status','Profile deleted.'); }
    public function toggle(SiteContentItem $member): RedirectResponse { abort_unless($member->type==='management',404);abort_unless(request()->user()->hasPermission('website.publish'),403,'Publishing profiles requires publishing permission.');$member->status=$member->status==='published'?'draft':'published';if($member->status==='published'&&!$member->published_at)$member->published_at=now();$member->save();return back()->with('status',$member->status==='published'?'Profile activated.':'Profile deactivated.'); }
    public function reorder(Request $request): JsonResponse { $data=$request->validate(['order'=>['required','array'],'order.*'=>['integer']]);$members=SiteContentItem::query()->where('type','management')->whereIn('id',$data['order'])->get()->keyBy('id');foreach($data['order'] as $position=>$id)if(isset($members[$id]))$members[$id]->update(['sort_order'=>$position+1]);return response()->json(['ok'=>true]); }

    private function syncFolderNavigation(ManagementProfileFolder $folder,bool $create): void
    {
        $key='management_folder:'.$folder->id;
        $item=NavigationMenuItem::query()->where('source_key',$key)->first();
        if(!$item && $create){
            // Reuse the existing Management navigation slot when present; otherwise
            // append a top-level item without disturbing the user's menu hierarchy.
            $item=NavigationMenuItem::query()->where('menu','main')->where(function($q){$q->where('source_key','route:management')->orWhere('route_name','management');})->first();
        }
        if(!$item && $create){
            $item=new NavigationMenuItem();$item->menu='main';$item->group=(string)(NavigationMenuItem::query()->where('menu','main')->value('group')??'main');$item->parent_id=null;$item->target='_self';$item->icon='fa-solid fa-users';$item->is_visible=true;$item->sort_order=(int)(NavigationMenuItem::query()->where('menu','main')->max('sort_order')??-1)+1;$item->area='public';
        }
        if(!$item)return;
        $item->label=$folder->name;$item->label_override=null;$item->url='/'.$folder->slug;$item->route_name=null;$item->source_key=$key;$item->source_type='external_link';$item->permission_key=null;$item->area='public';$item->save();
        app(PublicNavigationService::class)->clear('main');
    }

    private function save(SiteContentItem $member,Request $request): void
    {
        if($request->input('status')==='published')abort_unless($request->user()->hasPermission('website.publish'),403,'Publishing profiles requires publishing permission.');
        $data=$request->validate(['management_profile_folder_id'=>['required','integer','exists:management_profile_folders,id'],'title'=>['required','string','max:255'],'designation'=>['required','string','max:255'],'phone'=>['required','string','max:50'],'email'=>['nullable','email','max:255'],'content'=>['nullable','string'],'status'=>['required','in:draft,published'],'published_at'=>['nullable','date'],'profile_photo'=>['nullable','image','mimes:jpg,jpeg,png,webp','max:5120'],'visiting_card'=>['nullable','file','mimes:jpg,jpeg,png,webp,pdf','max:10240'],'remove_profile_photo'=>['nullable','boolean'],'remove_visiting_card'=>['nullable','boolean']]);
        $member->type='management';$member->management_profile_folder_id=(int)$data['management_profile_folder_id'];$member->title=$data['title'];$member->slug=Str::slug($data['title']);$member->designation=$data['designation'];$member->excerpt=$data['designation'];$member->phone=$data['phone'];$member->email=$data['email']??null;$member->content=$data['content']??null;$member->status=$data['status'];$member->published_at=$data['published_at']??null;
        if(!$member->exists)$member->sort_order=(int)(SiteContentItem::query()->where('type','management')->where('management_profile_folder_id',$member->management_profile_folder_id)->max('sort_order')??0)+1;
        if($request->boolean('remove_profile_photo')&&!$request->hasFile('profile_photo')){if($member->image_path)Storage::disk('public')->delete($member->image_path);$member->image_path=null;}
        if($request->boolean('remove_visiting_card')&&!$request->hasFile('visiting_card')){if($member->visiting_card_path)Storage::disk('public')->delete($member->visiting_card_path);$member->visiting_card_path=null;}
        if($request->hasFile('profile_photo')){if($member->image_path)Storage::disk('public')->delete($member->image_path);$member->image_path=$request->file('profile_photo')->store('management/photos','public');}
        if($request->hasFile('visiting_card')){if($member->visiting_card_path)Storage::disk('public')->delete($member->visiting_card_path);$member->visiting_card_path=$request->file('visiting_card')->store('management/visiting-cards','public');}
        $member->save();
    }
    private function uniqueFolderSlug(string $name,?int $ignoreId=null): string
    {
        $base=Str::slug($name)?:'profile-folder';$slug=$base;$counter=2;while(ManagementProfileFolder::query()->where('slug',$slug)->when($ignoreId!==null,fn($q)=>$q->where('id','!=',$ignoreId))->exists())$slug=$base.'-'.($counter++);return $slug;
    }
}
