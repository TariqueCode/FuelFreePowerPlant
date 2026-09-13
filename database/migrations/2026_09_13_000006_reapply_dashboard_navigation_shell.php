<?php

use App\Models\NavigationMenuItem;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        if (!Schema::hasTable('navigation_menu_items')) return;
        $upsert = function(string $label, string $url, string $icon, string $permission, int $order, ?int $parent, ?string $route = null, ?string $source = null) {
            $q = NavigationMenuItem::query()->where('menu','dashboard')->where('area','dashboard');
            if ($source) $q->where(function($x) use ($source,$route){ $x->where('source_key',$source); if($route)$x->orWhere('route_name',$route); });
            else $q->where('url',$url);
            $v=['menu'=>'dashboard','parent_id'=>$parent,'label'=>$label,'url'=>$url,'route_name'=>$route,'target'=>'_self','icon'=>$icon,'is_visible'=>true,'sort_order'=>$order,'source_key'=>$source,'source_type'=>$source?'route':'external_link','area'=>'dashboard','permission_key'=>$permission];
            $i=$q->orderBy('id')->first(); if($i){$i->update($v);return $i;} return NavigationMenuItem::create($v);
        };
        $folder=function(string $label,string $icon,int $order){$i=NavigationMenuItem::query()->where('menu','dashboard')->where('area','dashboard')->whereNull('parent_id')->where('source_type','folder')->where('label',$label)->first();$v=['menu'=>'dashboard','parent_id'=>null,'label'=>$label,'url'=>null,'route_name'=>null,'target'=>'_self','icon'=>$icon,'is_visible'=>true,'sort_order'=>$order,'source_key'=>null,'source_type'=>'folder','area'=>'dashboard','permission_key'=>null];if($i){$i->update($v);return $i;}return NavigationMenuItem::create($v);};
        $d=$upsert('Dashboard','/admin','fa-house','dashboard.view',0,null,'admin.dashboard','route:admin.dashboard');
        $w=$folder('Website','fa-globe',1);
        foreach ([['Homepage','/admin/homepage-builder','fa-house-chimney','admin.homepage-builder.index','route:admin.homepage-builder.index'],['Slider','/admin/sliders','fa-images','admin.sliders.index','route:admin.sliders.index'],['Highlight Banner','/admin/site-popups','fa-rectangle-ad','admin.site-popups.index','route:admin.site-popups.index'],['Profile Builder','/admin/management','fa-user-tie','admin.management.index','route:admin.management.index'],['News & Event','/admin/site-content?type=news','fa-newspaper',null,null],['Gallery','/admin/galleries','fa-images','admin.gallery.index','route:admin.gallery.index'],['Page Builder','/admin/cms','fa-file-lines','admin.cms.index','route:admin.cms.index'],['Social Media','/admin/social-links','fa-share-nodes',null,null],['Menu Builder','/admin/navigation','fa-sitemap',null,null],['Documents & Media','/admin/documents','fa-folder-open',null,null]] as $n=>$r)$upsert($r[0],$r[1],$r[2],$r[0]==='Social Media'?'social-media.manage':($r[0]==='Documents & Media'?'documents.view':'website.view'),$n,$w->id,$r[3],$r[4]);
        $u=$folder('Users & Access','fa-users-gear',2);foreach([['Users','/admin/users','fa-users','users.view'],['Audit Log','/admin/audit','fa-clipboard-list','audit.view'],['Health','/admin/health','fa-heart-pulse','health.view']] as $n=>$r)$upsert($r[0],$r[1],$r[2],$r[3],$n,$u->id);
        $c=$folder('Communications','fa-comments',3);foreach([['Help Desk','/admin/help-desk','fa-headset','mail.view'],['Inquiries','/admin/inquiries','fa-envelope-open-text','inquiries.view'],['Webmail','/admin/mail','fa-envelope','mail.view'],['Career Applications','/admin/career-applications','fa-briefcase','career.view']] as $n=>$r)$upsert($r[0],$r[1],$r[2],$r[3],$n,$c->id);
        $s=$upsert('Settings','/admin/settings','fa-sliders','settings.manage',4,null);
        foreach([$d->id=>0,$w->id=>1,$u->id=>2,$c->id=>3,$s->id=>4] as $id=>$o)NavigationMenuItem::whereKey($id)->update(['parent_id'=>null,'sort_order'=>$o,'is_visible'=>true]);
    }
    public function down(): void {}
};
