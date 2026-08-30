<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\GalleryMedia;
use App\Models\SiteContentItem;
use App\Models\SystemSetting;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Illuminate\View\View;

class GalleryController extends Controller
{
    public function index(): View
    {
        $galleries=SiteContentItem::query()->where('type','gallery')->withCount('galleryMedia')->orderBy('sort_order')->latest('created_at')->paginate(20);$publishedGalleries=SiteContentItem::query()->where('type','gallery')->where('status','published')->count();
        return view('admin.gallery.index',compact('galleries','publishedGalleries'));
    }
    public function create(): View{return view('admin.gallery.form',['gallery'=>new SiteContentItem(['type'=>'gallery','status'=>'draft'])]);}
    public function store(Request $request): RedirectResponse{$gallery=new SiteContentItem();$this->saveGallery($gallery,$request);return redirect()->route('admin.gallery.index')->with('status','Gallery created successfully.');}
    public function edit(SiteContentItem $gallery): View{abort_unless($gallery->type==='gallery',404);$gallery->load('galleryMedia');return view('admin.gallery.form',compact('gallery'));}
    public function update(Request $request,SiteContentItem $gallery): RedirectResponse{abort_unless($gallery->type==='gallery',404);$this->saveGallery($gallery,$request);return redirect()->route('admin.gallery.index')->with('status','Gallery updated successfully.');}
    public function destroy(SiteContentItem $gallery): RedirectResponse{abort_unless($gallery->type==='gallery',404);$gallery->load('galleryMedia');foreach($gallery->galleryMedia as $media)Storage::disk('public')->delete($media->path);if($gallery->image_path)Storage::disk('public')->delete($gallery->image_path);$gallery->delete();return redirect()->route('admin.gallery.index')->with('status','Gallery deleted successfully.');}
    public function uploadMedia(Request $request): JsonResponse{$data=$request->validate(['media'=>['required','file','mimes:jpg,jpeg,png,webp,gif,mp4,webm,mov','max:204800']]);$file=$data['media'];$path=$file->store('galleries/media','public');$type=str_starts_with((string)$file->getMimeType(),'video/')?'video':'image';return response()->json(['path'=>$path,'url'=>Storage::disk('public')->url($path),'type'=>$type,'name'=>$file->getClientOriginalName()]);}
    public function deleteMedia(GalleryMedia $media): JsonResponse{Storage::disk('public')->delete($media->path);$media->delete();return response()->json(['ok'=>true]);}
    private function saveGallery(SiteContentItem $gallery,Request $request): void
    {
        $data=$request->validate(['title'=>['required','string','max:255'],'excerpt'=>['nullable','string','max:500'],'cover_image'=>['nullable','image','mimes:jpg,jpeg,png,webp,gif','max:51200'],'status'=>['required','in:draft,published'],'sort_order'=>['nullable','integer','min:0'],'published_at'=>['nullable','date'],'media'=>['nullable','array'],'media.*.path'=>['required_with:media','string','max:500'],'media.*.type'=>['required_with:media','in:image,video'],'media.*.name'=>['nullable','string','max:255']]);
        if($request->hasFile('cover_image')){if($gallery->image_path)Storage::disk('public')->delete($gallery->image_path);$data['image_path']=$request->file('cover_image')->store('galleries/covers','public');}
        $baseSlug=Str::slug($data['title']);$slug=$baseSlug?:'gallery';$i=2;while(SiteContentItem::query()->where('slug',$slug)->where($gallery->getKeyName(),'!=',$gallery->getKey())->exists())$slug=$baseSlug.'-'.$i++;
        $gallery->fill(['type'=>'gallery','title'=>$data['title'],'slug'=>$slug,'excerpt'=>$data['excerpt']??null,'content'=>null,'image_path'=>$data['image_path']??$gallery->image_path,'status'=>$data['status'],'sort_order'=>$data['sort_order']??0,'published_at'=>$data['published_at']??null])->save();
        foreach(($data['media']??[]) as $index=>$media)if(!GalleryMedia::query()->where('gallery_id',$gallery->id)->where('path',$media['path'])->exists())GalleryMedia::create(['gallery_id'=>$gallery->id,'type'=>$media['type'],'path'=>$media['path'],'original_name'=>$media['name']??null,'sort_order'=>$index]);
    }
}
