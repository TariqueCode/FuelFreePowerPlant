<?php
namespace App\Http\Controllers\Admin;
use App\Http\Controllers\Controller;
use App\Models\SystemSetting;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class DesignBuilderController extends Controller
{
 private array $defaults=[
  'header'=>['logo'=>true,'navigation'=>true,'social'=>true,'mobile'=>true,'portal'=>true],
  'footer'=>['columns'=>true,'links'=>true,'social'=>true,'contact'=>true,'copyright'=>true],
 ];
 public function index(Request $request): View {
  $area=$request->query('area','header'); abort_unless(isset($this->defaults[$area]),404);
  $keys=array_keys($this->defaults[$area]); $saved=SystemSetting::whereIn('key',array_map(fn($k)=>'design.'.$area.'.'.$k.'_enabled',$keys))->pluck('value','key');
  $settings=[]; foreach($keys as $k)$settings[$k]=filter_var($saved->get('design.'.$area.'.'.$k.'_enabled','1'),FILTER_VALIDATE_BOOLEAN);
  return view('admin.design.index',compact('area','settings'));
 }
 public function update(Request $request): RedirectResponse {
  $area=$request->input('area','header'); abort_unless(isset($this->defaults[$area]),404);
  foreach(array_keys($this->defaults[$area]) as $key) SystemSetting::updateOrCreate(['key'=>'design.'.$area.'.'.$key.'_enabled'],['value'=>$request->boolean('settings.'.$key)?'1':'0']);
  return back()->with('status',ucfirst($area).' layout saved successfully.');
 }
}