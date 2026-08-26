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
    private array $types=['company','management','news','sustainability','gallery','resource','announcement','solution'];
    private array $labels=['company'=>'Company & About','management'=>'Management','news'=>'News & Notices','sustainability'=>'Sustainability','gallery'=>'Gallery','resource'=>'Resources','announcement'=>'Announcements','solution'=>'Solutions'];
    public function index(Request $request): View
    {
        $type=$request->string('type')->toString();abort_unless($type===''||in_array($type,$this->types,true),404);
        $items=SiteContentItem::query()->when($type,function($q)use($type){return $type==='news'?$q->whereIn('type',['news','announcement']):$q->where('type',$type);})->orderBy('sort_order')->latest()->paginate(20)->withQueryString();
        $title=$type?($this->labels[$type]??ucfirst($type)).' CMS':'Website Content';
        return view('admin.site-content.index',compact('items','type','title'))->with('types',$this->types)->with('labels',$this->labels);
    }
    public function create(Request $request): View{$type=$request->string('type')->toString();abort_unless($type===''||in_array($type,$this->types,true),404);$item=new SiteContentItem(['type'=>$type]);return view('admin.site-content.create',['item'=>$item,'types'=>$this->types,'labels'=>$this->labels,'lockedType'=>$type?:null]);}
    public function store(Request $request): RedirectResponse{$item=new SiteContentItem();$this->saveItem($item,$request);$redirectType=in_array($item->type,['news','announcement'],true)?'news':$item->type;return redirect()->route('admin.site-content.index',['type'=>$redirectType])->with('status',($this->labels[$redirectType]??'Website').' content created successfully.');}
    public function edit(SiteContentItem $item): View{abort_unless(in_array($item->type,$this->types,true),404);$lockedType=in_array($item->type,['news','announcement'],true)?'news':$item->type;return view('admin.site-content.create',['item'=>$item,'types'=>$this->types,'labels'=>$this->labels,'lockedType'=>$lockedType]);}
    public function update(Request $request,SiteContentItem $item): RedirectResponse{$this->saveItem($item,$request);$redirectType=in_array($item->type,['news','announcement'],true)?'news':$item->type;return redirect()->route('admin.site-content.index',['type'=>$redirectType])->with('status','Content updated successfully.');}
    public function destroy(SiteContentItem $item): RedirectResponse{$type=in_array($item->type,['news','announcement'],true)?'news':$item->type;$item->delete();return redirect()->route('admin.site-content.index',['type'=>$type])->with('status','Content deleted successfully.');}
    public function uploadMedia(Request $request): JsonResponse{$data=$request->validate(['media'=>['required','file','mimes:jpg,jpeg,png,webp,gif,mp4,webm','max:20480']]);$file=$data['media']->store('site-content/media','public');return response()->json(['url'=>Storage::disk('public')->url($file),'mime'=>$data['media']->getMimeType(),'name'=>$data['media']->getClientOriginalName()]);}
    private function saveItem(SiteContentItem $item,Request $request): SiteContentItem{$data=$request->validate(['type'=>['required','in:company,management,news,announcement,sustainability,gallery,resource,solution'],'title'=>['required','string','max:255'],'slug'=>['nullable','string','max:255'],'excerpt'=>['nullable','string','max:1000'],'content'=>['nullable','string'],'image_path'=>['nullable','string','max:500'],'status'=>['required','in:draft,published'],'sort_order'=>['nullable','integer','min:0'],'published_at'=>['nullable','date']]);if(($data['slug']??'')==='')$data['slug']=str($data['title'])->slug();$data['sort_order']??=0;$item->fill($data)->save();return $item;}
}
