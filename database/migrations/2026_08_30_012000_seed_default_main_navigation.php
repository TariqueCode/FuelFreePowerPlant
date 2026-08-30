<?php
use Illuminate\Database\Migrations\Migration;
use App\Models\NavigationMenuItem;
return new class extends Migration {
 public function up():void{
  if(NavigationMenuItem::query()->where('menu','main')->exists())return;
  $items=[
   ['label'=>'Home','route_name'=>'home'],
   ['label'=>'Company','group'=>'Company'],
   ['label'=>'Management Team','route_name'=>'management'],
   ['label'=>'Gallery','route_name'=>'site.gallery'],
   ['label'=>'News & Notices','route_name'=>'news.index'],
   ['label'=>'Career','route_name'=>'site.career'],
   ['label'=>'Contact','route_name'=>'contact'],
   ['label'=>'Webmail','route_name'=>'webmail.redirect','target'=>'_blank'],
  ];
  $parents=[]; foreach($items as $i=>$item)$parents[$item['label']]=NavigationMenuItem::create($item+['menu'=>'main','target'=>$item['target']??'_self','is_visible'=>true,'sort_order'=>$i]);
  $company=$parents['Company'];
  foreach([['About Us','site.about'],['Our Plants','site.plants'],['Future Project','site.future-project'],['Solutions','site.solutions']] as $j=>$child)NavigationMenuItem::create(['menu'=>'main','parent_id'=>$company->id,'label'=>$child[0],'route_name'=>$child[1],'target'=>'_self','is_visible'=>true,'sort_order'=>$j]);
 }
 public function down():void{NavigationMenuItem::where('menu','main')->delete();}
};