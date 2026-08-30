<?php
namespace App\Http\Controllers\Admin;
use App\Http\Controllers\Controller;
use App\Models\SystemSetting;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class ThemeBuilderController extends Controller
{
 public function index(): View {
  $keys=['primary','accent','background','surface','text','muted','max_width','radius','base_font'];
  $saved=SystemSetting::whereIn('key',array_map(fn($k)=>'theme.'.$k,$keys))->pluck('value','key');
  $defaults=['primary'=>'#48d8f1','accent'=>'#72dfbf','background'=>'#031018','surface'=>'#071b26','text'=>'#effcff','muted'=>'#91aeb8','max_width'=>'1280px','radius'=>'16px','base_font'=>'Inter, system-ui, sans-serif'];
  $theme=[]; foreach($defaults as $k=>$v)$theme[$k]=$saved->get('theme.'.$k,$v);
  return view('admin.theme.index',compact('theme'));
 }
 public function update(Request $request): RedirectResponse {
  $data=$request->validate([
   'primary'=>['required','regex:/^#[0-9A-Fa-f]{6}$/'],'accent'=>['required','regex:/^#[0-9A-Fa-f]{6}$/'],
   'background'=>['required','regex:/^#[0-9A-Fa-f]{6}$/'],'surface'=>['required','regex:/^#[0-9A-Fa-f]{6}$/'],
   'text'=>['required','regex:/^#[0-9A-Fa-f]{6}$/'],'muted'=>['required','regex:/^#[0-9A-Fa-f]{6}$/'],
   'max_width'=>['required','regex:/^[0-9]{3,4}px$/'],'radius'=>['required','regex:/^[0-9]{1,2}px$/'],
   'base_font'=>['required','string','max:120']
  ]);
  foreach($data as $key=>$value) SystemSetting::updateOrCreate(['key'=>'theme.'.$key],['value'=>$value]);
  return back()->with('status','Theme settings saved successfully.');
 }
}