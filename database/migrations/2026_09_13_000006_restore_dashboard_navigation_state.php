<?php

use App\Models\NavigationMenuItem;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (! Schema::hasTable('navigation_menu_items')) return;

        $find = static function (?string $sourceKey = null): ?NavigationMenuItem {
            $query = NavigationMenuItem::query()->where('menu', 'dashboard')->where('area', 'dashboard');
            if ($sourceKey !== null) {
                $query->where(function ($q) use ($sourceKey): void {
                    $q->where('source_key', $sourceKey)->orWhere('route_name', substr($sourceKey, 6));
                });
            }
            return $query->orderBy('id')->first();
        };

        $folder = static function (string $label, string $icon, int $order): NavigationMenuItem {
            $item = NavigationMenuItem::query()->where('menu', 'dashboard')->where('area', 'dashboard')
                ->whereNull('parent_id')->where('source_type', 'folder')->where('label', $label)->orderBy('id')->first();
            $values = ['menu'=>'dashboard','parent_id'=>null,'label'=>$label,'url'=>null,'route_name'=>null,'target'=>'_self','icon'=>$icon,'is_visible'=>true,'sort_order'=>$order,'source_key'=>null,'source_type'=>'folder','area'=>'dashboard','permission_key'=>null];
            if ($item) { $item->update($values); return $item->fresh(); }
            return NavigationMenuItem::create($values);
        };

        $item = static function (string $label, string $url, string $icon, string $permission, int $order, int $parentId, ?string $routeName = null, ?string $sourceKey = null) use ($find): NavigationMenuItem {
            $existing = $sourceKey ? $find($sourceKey) : NavigationMenuItem::query()->where('menu','dashboard')->where('area','dashboard')->where('url',$url)->orderBy('id')->first();
            $values = ['menu'=>'dashboard','parent_id'=>$parentId,'label'=>$label,'label_override'=>null,'url'=>$url,'route_name'=>$routeName,'target'=>'_self','icon'=>$icon,'is_visible'=>true,'sort_order'=>$order,'source_key'=>$sourceKey,'source_type'=>$sourceKey ? 'route' : 'external_link','area'=>'dashboard','permission_key'=>$permission];
            if ($existing) { $existing->update($values); return $existing->fresh(); }
            return NavigationMenuItem::create($values);
        };

        // Remove legacy Profile Builder folder entries; profile folders remain data only.
        NavigationMenuItem::query()->where('menu','dashboard')->where('area','dashboard')->where('source_key','like','management_folder:%')->delete();

        $dashboard = $item('Dashboard','/admin','fa-house','dashboard.view',0,0,'admin.dashboard','route:admin.dashboard');
        $dashboard->update(['parent_id'=>null]);

        $website = $folder('Website','fa-globe',1);
        $item('Homepage','/admin/homepage-builder','fa-house-chimney','website.view',0,(int)$website->id,'admin.homepage-builder.index','route:admin.homepage-builder.index');
        $item('Slider','/admin/sliders','fa-images','website.view',1,(int)$website->id,'admin.sliders.index','route:admin.sliders.index');
        $item('Highlight Banner','/admin/site-popups','fa-rectangle-ad','website.view',2,(int)$website->id,'admin.site-popups.index','route:admin.site-popups.index');
        $item('Profile Builder','/admin/management','fa-user-tie','website.view',3,(int)$website->id,'admin.management.index','route:admin.management.index');
        $item('News & Event','/admin/site-content?type=news','fa-newspaper','website.view',4,(int)$website->id);
        $item('Gallery','/admin/galleries','fa-images','website.view',5,(int)$website->id,'admin.gallery.index','route:admin.gallery.index');
        $item('Page Builder','/admin/cms','fa-file-lines','website.view',6,(int)$website->id,'admin.cms.index','route:admin.cms.index');
        $item('Social Media','/admin/social-links','fa-share-nodes','social-media.manage',7,(int)$website->id);
        $item('Menu Builder','/admin/navigation','fa-sitemap','website.view',8,(int)$website->id);
        $item('Documents & Media','/admin/documents','fa-folder-open','documents.view',9,(int)$website->id);

        $users = $folder('Users & Access','fa-users-gear',2);
        $item('Users','/admin/users','fa-users','users.view',0,(int)$users->id);
        $item('Audit Log','/admin/audit','fa-clipboard-list','audit.view',1,(int)$users->id);
        $item('Health','/admin/health','fa-heart-pulse','health.view',2,(int)$users->id);

        $communications = $folder('Communications','fa-comments',3);
        $item('Help Desk','/admin/help-desk','fa-headset','mail.view',0,(int)$communications->id);
        $item('Inquiries','/admin/inquiries','fa-envelope-open-text','inquiries.view',1,(int)$communications->id);
        $item('Webmail','/admin/mail','fa-envelope','mail.view',2,(int)$communications->id);
        $item('Career Applications','/admin/career-applications','fa-briefcase','career.view',3,(int)$communications->id);

        $settings = $item('Settings','/admin/settings','fa-sliders','settings.manage',4,0);
        $settings->update(['parent_id'=>null]);

        cache()->forget('fuelfree.dashboard_navigation');
        cache()->forget('fuelfree.public_navigation');
    }

    public function down(): void {}
};
