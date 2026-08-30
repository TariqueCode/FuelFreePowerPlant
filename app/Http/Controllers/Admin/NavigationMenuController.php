<?php
namespace App\Http\Controllers\Admin;
use App\Http\Controllers\Controller;
use App\Models\NavigationMenuItem;
use App\Models\CmsPage;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;
class NavigationMenuController extends Controller {
 public function index(Request $request):View{$menu=$request->string('menu')->toString()?:'main';$items=NavigationMenuItem::where('menu',$menu)->whereNull('parent_id')->with('children.children')->orderBy('sort_order')->orderBy('id')->get();$all=NavigationMenuItem::where('menu',$menu)->orderBy('sort_order')->get();$pages=CmsPage::orderBy('title')->get(['id','title','slug','is_published']);return view('admin.navigation.index',compact('items','all','pages','menu'));}
 public function store(Request $request):RedirectResponse{$data=$this->validateItem($request);$data['menu']=$request->string('menu')->toString()?:'main';$data['sort_order']=(int)(NavigationMenuItem::where('menu',$data['menu'])->where('parent_id',$data['parent_id']??null)->max('sort_order')??-1)+1;NavigationMenuItem::create($data);return back()->with('status','Menu item added.');}
 public function update(Request $request,NavigationMenuItem $item):RedirectResponse{$data=$this->validateItem($request);$data['menu']=$item->menu;$data['sort_order']=$item->sort_order;$item->update($data);return back()->with('status','Menu item updated.');}
 public function destroy(NavigationMenuItem $item):RedirectResponse{NavigationMenuItem::where('parent_id',$item->id)->update(['parent_id'=>$item->parent_id]);$item->delete();return back()->with('status','Menu item deleted.');}
 public function reorder(Request $request):JsonResponse{$data=$request->validate(['menu'=>['required','string','max:60'],'ids'=>['required','array'],'ids.*'=>['integer','distinct']]);$items=NavigationMenuItem::where('menu',$data['menu'])->whereIn('id',$data['ids'])->get()->keyBy('id');abort_unless($items->count()===count($data['ids']),422,'Invalid menu items.');foreach($data['ids'] as $i=>$id)$items[$id]->update(['sort_order'=>$i]);return response()->json(['ok'=>true]);}
 private function validateItem(Request $request):array{return $request->validate(['label'=>['required','string','max:160'],'url'=>['nullable','string','max:500'],'route_name'=>['nullable','string','max:160'],'group'=>['nullable','string','max:100'],'parent_id'=>['nullable','integer','exists:navigation_menu_items,id'],'target'=>['required','in:_self,_blank'],'icon'=>['nullable','string','max:100'],'is_visible'=>['nullable','boolean']])+['is_visible'=>$request->boolean('is_visible')];}
}