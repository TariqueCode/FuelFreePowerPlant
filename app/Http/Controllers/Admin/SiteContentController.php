<?php
namespace App\Http\Controllers\Admin;
use App\Http\Controllers\Controller;
use App\Models\SiteContentItem;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\View\View;

class SiteContentController extends Controller
{
    private array $types=['company','management','news','gallery','announcement'];
    private array $labels=['company'=>'Company & About','management'=>'Management','news'=>'News & Notices','gallery'=>'Gallery','announcement'=>'Announcements'];

    public function index(Request $request): View|RedirectResponse
    {
        $type=$request->string('type')->toString();
        if($type==='gallery') return redirect()->route('admin.gallery.index');
        abort_unless($type===''||in_array($type,$this->types,true),404);
        $items=SiteContentItem::query()
            ->when($type,function($q)use($type){return $type==='news'?$q->whereIn('type',['news','announcement']):$q->where('type',$type);})
            ->when($type==='company',fn($q)=>$q->orderByRaw('CASE WHEN navigation_order IS NULL THEN 1 ELSE 0 END')->orderBy('navigation_order')->orderByDesc('created_at'))
            ->when($type!=='company',fn($q)=>$q->latest('created_at'))
            ->paginate(20)->withQueryString();
        $title=$type?($this->labels[$type]??ucfirst($type)).' CMS':'Website Content';
        return view('admin.site-content.index',compact('items','type','title'))->with('types',$this->types)->with('labels',$this->labels);
    }

    public function create(Request $request): View|RedirectResponse
    {
        $type=$request->string('type')->toString();
        if($type==='gallery') return redirect()->route('admin.gallery.create');
        abort_unless($type===''||in_array($type,$this->types,true),404);
        $item=new SiteContentItem(['type'=>$type]);
        return view('admin.site-content.create',['item'=>$item,'types'=>$this->types,'labels'=>$this->labels,'lockedType'=>$type?:null]);
    }

    public function store(Request $request): RedirectResponse
    {
        $item=new SiteContentItem();
        $this->saveItem($item,$request);
        $redirectType=in_array($item->type,['news','announcement'],true)?'news':$item->type;
        return redirect()->route('admin.site-content.index',['type'=>$redirectType])->with('status',($this->labels[$redirectType]??'Website').' content created successfully.');
    }

    public function edit(SiteContentItem $item): View|RedirectResponse
    {
        abort_unless(in_array($item->type,$this->types,true),404);
        if($item->type==='gallery') return redirect()->route('admin.gallery.edit',$item);
        $lockedType=in_array($item->type,['news','announcement'],true)?'news':$item->type;
        return view('admin.site-content.create',['item'=>$item,'types'=>$this->types,'labels'=>$this->labels,'lockedType'=>$lockedType]);
    }

    public function update(Request $request,SiteContentItem $item): RedirectResponse
    {
        $this->saveItem($item,$request);
        $redirectType=in_array($item->type,['news','announcement'],true)?'news':$item->type;
        return redirect()->route('admin.site-content.index',['type'=>$redirectType])->with('status','Content updated successfully.');
    }

    public function destroy(SiteContentItem $item): RedirectResponse
    {
        $type=in_array($item->type,['news','announcement'],true)?'news':$item->type;
        $item->delete();
        return redirect()->route('admin.site-content.index',['type'=>$type])->with('status','Content deleted successfully.');
    }

    public function toggleNavigation(Request $request,SiteContentItem $item): JsonResponse
    {
        abort_unless($item->type==='company',404);
        $enabled=$request->boolean('enabled');
        $item->show_in_navigation=$enabled;
        if($enabled){
            $item->navigation_order=(int)(SiteContentItem::query()->where('type','company')->where('show_in_navigation',true)->whereKeyNot($item->id)->min('navigation_order') ?? 1)-1;
        }else{
            $item->navigation_order=null;
        }
        $item->save();
        return response()->json(['ok'=>true,'enabled'=>$enabled]);
    }

    public function reorderNavigation(Request $request): JsonResponse
    {
        $data=$request->validate(['ids'=>['required','array'],'ids.*'=>['integer','distinct','exists:site_content_items,id']]);
        $items=SiteContentItem::query()->where('type','company')->where('show_in_navigation',true)->whereIn('id',$data['ids'])->get()->keyBy('id');
        abort_unless($items->count()===count($data['ids']),422,'Invalid navigation items.');
        foreach($data['ids'] as $position=>$id){$items[$id]->update(['navigation_order'=>$position+1]);}
        return response()->json(['ok'=>true]);
    }

    public function uploadMedia(Request $request): JsonResponse
    {
        $data=$request->validate(['media'=>['required','file','mimes:jpg,jpeg,png,webp,gif,mp4,webm,mov','max:102400']]);
        $file=$data['media']->store('site-content/media','public');
        return response()->json(['url'=>Storage::disk('public')->url($file),'mime'=>$data['media']->getMimeType(),'name'=>$data['media']->getClientOriginalName()]);
    }

    private function saveItem(SiteContentItem $item,Request $request): SiteContentItem
    {
        $data=$request->validate([
            'type'=>['required','in:company,management,news,announcement,gallery'],
            'title'=>['required','string','max:255'],
            'slug'=>['nullable','string','max:255'],
            'excerpt'=>['nullable','string','max:1000'],
            'content'=>['nullable','string'],
            'image_path'=>['nullable','string','max:500'],
            'status'=>['required','in:draft,published'],
            'published_at'=>['nullable','date'],
            'show_in_navigation'=>['nullable','boolean'],
        ]);
        if(($data['slug']??'')==='') $data['slug']=str($data['title'])->slug();
        $data['show_in_navigation']=($data['type']==='company') && (bool)($data['show_in_navigation']??false);
        $wasInNavigation=$item->exists && $item->type==='company' && (bool)$item->show_in_navigation;
        if($data['type']==='company' && $data['show_in_navigation'] && !$wasInNavigation){
            $data['navigation_order']=(int)(SiteContentItem::query()->where('type','company')->where('show_in_navigation',true)->min('navigation_order') ?? 1)-1;
        }
        if($data['type']!=='company') $data['show_in_navigation']=false;
        $item->fill($data)->save();
        return $item;
    }
}
