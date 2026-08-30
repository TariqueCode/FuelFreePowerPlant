<?php
namespace App\Http\Controllers\Admin;
use App\Http\Controllers\Controller;
use App\Models\PowerPlant;
use App\Models\SiteContentItem;
use App\Models\SystemSetting;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class HomepageBuilderController extends Controller
{
 public function index(): View {
  $defaults=['hero','welcome','statistics','projects','news','gallery','cta'];
  $saved=SystemSetting::query()->whereIn('key',['home.section_order','home.hero_enabled','home.welcome_enabled','home.statistics_enabled','home.projects_enabled','home.news_enabled','home.gallery_enabled','home.cta_enabled'])->pluck('value','key')->all();
  $order=json_decode($saved['home.section_order']??'[]',true); $order=is_array($order)&&count($order)===count($defaults)&&!array_diff($order,$defaults)?$order:$defaults;
  $sections=[]; foreach($order as $key)$sections[$key]=filter_var($saved['home.'.$key.'_enabled']??'1',FILTER_VALIDATE_BOOLEAN);
  $counts=['projects'=>PowerPlant::count(),'news'=>SiteContentItem::whereIn('type',['news','announcement'])->where('status','published')->count(),'gallery'=>SiteContentItem::where('type','gallery')->where('status','published')->count()];
  return view('admin.homepage-builder.index',compact('order','sections','counts'));
 }
 public function update(Request $request): RedirectResponse {
  $allowed=['hero','welcome','statistics','projects','news','gallery','cta'];
  $order=$request->input('section_order',[]); if(!is_array($order)||count($order)!==count($allowed)||array_diff($order,$allowed)) return back()->withErrors(['section_order'=>'Invalid homepage section order.']);
  foreach($allowed as $key) SystemSetting::updateOrCreate(['key'=>'home.'.$key.'_enabled'],['value'=>$request->boolean('sections.'.$key)?'1':'0']);
  SystemSetting::updateOrCreate(['key'=>'home.section_order'],['value'=>json_encode(array_values($order))]);
  return back()->with('status','Homepage layout saved successfully.');
 }
}