<?php

use App\Models\NavigationMenuItem;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        if (!Schema::hasTable('navigation_menu_items')) return;
        $items = [
            ['Dashboard','/admin','fa-house','dashboard.view',0,null,'admin.dashboard','route:admin.dashboard'],
            ['Website',null,'fa-globe',null,1,null,null,null],
            ['Users & Access',null,'fa-users-gear',null,2,null,null,null],
            ['Communications',null,'fa-comments',null,3,null,null,null],
            ['Settings','/admin/settings','fa-sliders','settings.manage',4,null,null,null],
        ];
        $folders=[];
        foreach ($items as $row) {
            $q=NavigationMenuItem::query()->where('menu','dashboard')->where('area','dashboard')->whereNull('parent_id')->where('label',$row[0]);
            $item=$q->first();
            $data=['menu'=>'dashboard','parent_id'=>null,'label'=>$row[0],'url'=>$row[1],'route_name'=>$row[6],'target'=>'_self','icon'=>$row[2],'is_visible'=>true,'sort_order'=>$row[4],'source_key'=>$row[7],'source_type'=>$row[7]?'route':($row[1]?'external_link':'folder'),'area'=>'dashboard','permission_key'=>$row[3]];
            if($item)$item->update($data);else$item=NavigationMenuItem::create($data);
            $folders[$row[0]]=$item->id;
        }
        $children=[
            'Website'=>[
                ['Homepage','/admin/homepage-builder','fa-house-chimney','website.view',0,'admin.homepage-builder.index','route:admin.homepage-builder.index'],
                ['Slider','/admin/sliders','fa-images','website.view',1,'admin.sliders.index','route:admin.sliders.index'],
                ['Highlight Banner','/admin/site-popups','fa-rectangle-ad','website.view',2,'admin.site-popups.index','route:admin.site-popups.index'],
                ['Profile Builder','/admin/management','fa-user-tie','website.view',3,'admin.management.index','route:admin.management.index'],
                ['News & Event','/admin/site-content?type=news','fa-newspaper','website.view',4,null,null],
                ['Gallery','/admin/galleries','fa-images','website.view',5,'admin.gallery.index','route:admin.gallery.index'],
                ['Page Builder','/admin/cms','fa-file-lines','website.view',6,'admin.cms.index','route:admin.cms.index'],
                ['Social Media','/admin/social-links','fa-share-nodes','social-media.manage',7,null,null],
                ['Menu Builder','/admin/navigation','fa-sitemap','website.view',8,null,null],
                ['Documents & Media','/admin/documents','fa-folder-open','documents.view',9,null,null],
            ],
            'Users & Access'=>[
                ['Users','/admin/users','fa-users','users.view',0,null,null],['Audit Log','/admin/audit','fa-clipboard-list','audit.view',1,null,null],['Health','/admin/health','fa-heart-pulse','health.view',2,null,null],
            ],
            'Communications'=>[
                ['Help Desk','/admin/help-desk','fa-headset','mail.view',0,null,null],['Inquiries','/admin/inquiries','fa-envelope-open-text','inquiries.view',1,null,null],['Webmail','/admin/mail','fa-envelope','mail.view',2,null,null],['Career Applications','/admin/career-applications','fa-briefcase','career.view',3,null,null],
            ],
        ];
        foreach($children as $parent=>$rows){foreach($rows as $r){$q=NavigationMenuItem::query()->where('menu','dashboard')->where('area','dashboard')->where('parent_id',$folders[$parent])->where('label',$r[0]);$item=$q->first();$data=['menu'=>'dashboard','parent_id'=>$folders[$parent],'label'=>$r[0],'url'=>$r[1],'route_name'=>$r[5],'target'=>'_self','icon'=>$r[2],'is_visible'=>true,'sort_order'=>$r[4],'source_key'=>$r[6],'source_type'=>$r[6]?'route':'external_link','area'=>'dashboard','permission_key'=>$r[3]];if($item)$item->update($data);else NavigationMenuItem::create($data);}}
    }
    public function down(): void {}
};
