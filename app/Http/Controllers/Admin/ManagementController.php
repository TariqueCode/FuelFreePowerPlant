<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\SiteContentItem;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Illuminate\View\View;

class ManagementController extends Controller
{
    public function index(): View { $members=SiteContentItem::query()->where('type','management')->orderBy('sort_order')->orderBy('id')->get(); return view('admin.management.index',compact('members')); }
    public function create(): View { return view('admin.management.form',['member'=>new SiteContentItem(['type'=>'management','status'=>'draft'])]); }
    public function store(Request $request): RedirectResponse { $member=new SiteContentItem();$this->save($member,$request);return redirect()->route('admin.management.index')->with('status','Management profile created.'); }
    public function edit(SiteContentItem $member): View { abort_unless($member->type==='management',404);return view('admin.management.form',compact('member')); }
    public function update(Request $request,SiteContentItem $member): RedirectResponse { abort_unless($member->type==='management',404);$this->save($member,$request);return redirect()->route('admin.management.index')->with('status','Management profile updated.'); }
    public function destroy(SiteContentItem $member): RedirectResponse { abort_unless($member->type==='management',404);foreach([$member->image_path,$member->visiting_card_path] as $path){if($path)Storage::disk('public')->delete($path);} $member->delete();return back()->with('status','Management profile deleted.'); }
    public function toggle(SiteContentItem $member): RedirectResponse { abort_unless($member->type==='management',404); abort_unless(request()->user()->hasPermission('website.publish'), 403, 'Publishing management profiles requires publishing permission.'); $member->status=$member->status==='published'?'draft':'published'; if($member->status==='published' && !$member->published_at)$member->published_at=now(); $member->save(); return back()->with('status',$member->status==='published'?'Profile activated.':'Profile deactivated.'); }
    public function reorder(Request $request): JsonResponse { $data=$request->validate(['order'=>['required','array'],'order.*'=>['integer']]);$members=SiteContentItem::query()->where('type','management')->whereIn('id',$data['order'])->get()->keyBy('id');foreach($data['order'] as $position=>$id){if(isset($members[$id]))$members[$id]->update(['sort_order'=>$position+1]);}return response()->json(['ok'=>true]); }
    private function save(SiteContentItem $member,Request $request): void
    {
        // Publishing/activation is a separate authority from content editing.
        if ($request->input('status') === 'published') {
            abort_unless($request->user()->hasPermission('website.publish'), 403, 'Publishing management profiles requires publishing permission.');
        }
        $data=$request->validate(['title'=>['required','string','max:255'],'designation'=>['required','string','max:255'],'phone'=>['required','string','max:50'],'email'=>['nullable','email','max:255'],'content'=>['nullable','string'],'status'=>['required','in:draft,published'],'published_at'=>['nullable','date'],'profile_photo'=>['nullable','image','mimes:jpg,jpeg,png,webp','max:5120'],'visiting_card'=>['nullable','file','mimes:jpg,jpeg,png,webp,pdf','max:10240'],'remove_profile_photo'=>['nullable','boolean'],'remove_visiting_card'=>['nullable','boolean']]);
        $member->type='management';$member->title=$data['title'];$member->slug=Str::slug($data['title']);$member->designation=$data['designation'];$member->excerpt=$data['designation'];$member->phone=$data['phone'];$member->email=$data['email']??null;$member->content=$data['content']??null;$member->status=$data['status'];if(!$member->exists)$member->sort_order=(int)(SiteContentItem::query()->where('type','management')->max('sort_order')??0)+1;$member->published_at=$data['published_at']??null;
        if($request->boolean('remove_profile_photo')&&!$request->hasFile('profile_photo')){if($member->image_path)Storage::disk('public')->delete($member->image_path);$member->image_path=null;}
        if($request->boolean('remove_visiting_card')&&!$request->hasFile('visiting_card')){if($member->visiting_card_path)Storage::disk('public')->delete($member->visiting_card_path);$member->visiting_card_path=null;}
        if($request->hasFile('profile_photo')){if($member->image_path)Storage::disk('public')->delete($member->image_path);$member->image_path=$request->file('profile_photo')->store('management/photos','public');}
        if($request->hasFile('visiting_card')){if($member->visiting_card_path)Storage::disk('public')->delete($member->visiting_card_path);$member->visiting_card_path=$request->file('visiting_card')->store('management/visiting-cards','public');}
        $member->save();
    }
}
