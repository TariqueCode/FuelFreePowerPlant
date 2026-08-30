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
  foreach($items as $i=>$item)NavigationMenuItem::create($item+['menu'=>'main','target'=>$item['target']??'_self','is_visible'=>true,'sort_order'=>$i]);
 }
 public function down():void{NavigationMenuItem::where('menu','main')->delete();}
};