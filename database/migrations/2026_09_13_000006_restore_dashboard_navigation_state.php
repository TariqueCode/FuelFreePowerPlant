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

        $ensureFolder = static function (string $label, string $icon, int $sortOrder, ?int $parentId = null): NavigationMenuItem {
            $item = NavigationMenuItem::query()
                ->where('menu', 'dashboard')->where('area', 'dashboard')
                ->where('source_type', 'folder')->where('label', $label)->where('parent_id', $parentId)
                ->orderBy('id')->first();
            if (! $item) {
                return NavigationMenuItem::create([
                    'menu' => 'dashboard', 'parent_id' => $parentId, 'label' => $label, 'url' => null,
                    'route_name' => null, 'target' => '_self', 'icon' => $icon, 'is_visible' => true,
                    'sort_order' => $sortOrder, 'source_key' => null, 'source_type' => 'folder',
                    'area' => 'dashboard', 'permission_key' => null,
                ]);
            }
            $item->update(['icon' => $icon, 'is_visible' => true, 'sort_order' => $sortOrder, 'parent_id' => $parentId]);
            return $item->fresh();
        };

        $ensureItem = static function (
            string $label, string $url, string $icon, string $permission, int $sortOrder,
            ?int $parentId, ?string $routeName = null, ?string $sourceKey = null
        ) use ($find): NavigationMenuItem {
            $item = $sourceKey !== null ? $find($sourceKey) : NavigationMenuItem::query()
                ->where('menu', 'dashboard')->where('area', 'dashboard')
                ->where(function ($q) use ($url, $routeName, $label): void {
                    $q->where('url', $url);
                    if ($routeName) $q->orWhere('route_name', $routeName);
                    $q->orWhere('label', $label);
                })->orderBy('id')->first();

            $values = [
                'menu' => 'dashboard', 'parent_id' => $parentId, 'label' => $label, 'label_override' => null,
                'url' => $url, 'route_name' => $routeName, 'target' => '_self', 'icon' => $icon,
                'is_visible' => true, 'sort_order' => $sortOrder, 'source_key' => $sourceKey,
                'source_type' => $sourceKey !== null ? 'route' : 'external_link',
                'area' => 'dashboard', 'permission_key' => $permission,
            ];
            if ($item) {
                $item->update($values);
                return $item->fresh();
            }
            return NavigationMenuItem::create($values);
        };

        // Remove legacy profile-folder navigation duplicates without touching profile data.
        NavigationMenuItem::query()
            ->where('menu', 'dashboard')->where('area', 'dashboard')
            ->where('source_key', 'like', 'management_folder:%')->delete();

        $dashboard = $ensureItem('Dashboard', '/admin', 'fa-house', 'dashboard.view', 0, null, 'admin.dashboard', 'route:admin.dashboard');

        $website = NavigationMenuItem::query()
            ->where('menu', 'dashboard')->where('area', 'dashboard')->whereNull('parent_id')
            ->where('source_type', 'folder')->where('label', 'Website')->orderBy('id')->first();
        if (! $website) {
            $website = NavigationMenuItem::create([
                'menu' => 'dashboard', 'parent_id' => null, 'label' => 'Website', 'url' => null,
                'route_name' => null, 'target' => '_self', 'icon' => 'fa-globe', 'is_visible' => true,
                'sort_order' => 1, 'source_key' => null, 'source_type' => 'folder', 'area' => 'dashboard',
                'permission_key' => null,
            ]);
        } else {
            $website->update(['icon' => 'fa-globe', 'is_visible' => true, 'sort_order' => 1, 'parent_id' => null]);
            $website = $website->fresh();
        }

        $websiteItems = [
            ['Homepage', '/admin/homepage-builder', 'fa-house-chimney', 'website.view', 0, 'admin.homepage-builder.index', 'route:admin.homepage-builder.index'],
            ['Slider', '/admin/sliders', 'fa-images', 'website.view', 1, 'admin.sliders.index', 'route:admin.sliders.index'],
            ['Highlight Banner', '/admin/site-popups', 'fa-rectangle-ad', 'website.view', 2, 'website.view', 2, 'admin.site-popups.index', 'route:admin.site-popups.index'],
        ];

        foreach ($websiteItems as $row) {
            $ensureItem($row[0], $row[1], $row[2], $row[3], $row[4], (int) $website->id, $row[5], $row[6]);
        }

        $ensureItem('Profile Builder', '/admin/management', 'fa-user-tie', 'website.view', 3, (int) $website->id, 'admin.management.index', 'route:admin.management.index');
        $ensureItem('News & Event', '/admin/site-content?type=news', 'fa-newspaper', 'website.view', 4, (int) $website->id);
        $ensureItem('Gallery', '/admin/galleries', 'fa-images', 'website.view', 5, (int) $website->id, 'admin.gallery.index', 'route:admin.gallery.index');
        $ensureItem('Page Builder', '/admin/cms', 'fa-file-lines', 'website.view', 6, (int) $website->id, 'admin.cms.index', 'route:admin.cms.index');
        $ensureItem('Social Media', '/admin/social-links', 'fa-share-nodes', 'social-media.manage', 7, (int) $website->id);
        $ensureItem('Menu Builder', '/admin/navigation', 'fa-sitemap', 'website.view', 8, (int) $website->id);
        $ensureItem('Documents & Media', '/admin/documents', 'fa-folder-open', 'documents.view', 9, (int) $website->id);

        $users = $ensureFolder('Users & Access', 'fa-users-gear', 2);
        $ensureItem('Users', '/admin/users', 'fa-users', 'users.view', 0, (int) $users->id);
        $ensureItem('Audit Log', '/admin/audit', 'fa-clipboard-list', 'audit.view', 1, (int) $users->id);
        $ensureItem('Health', '/admin/health', 'fa-heart-pulse', 'health.view', 2, (int) $users->id);

        $communications = $ensureFolder('Communications', 'fa-comments', 3);
        $ensureItem('Help Desk', '/admin/help-desk', 'fa-headset', 'mail.view', 0, (int) $communications->id);
        $ensureItem('Inquiries', '/admin/inquiries', 'fa-envelope-open-text', 'inquiries.view', 1, (int) $communications->id);
        $ensureItem('Webmail', '/admin/mail', 'fa-envelope', 'mail.view', 2, (int) $communications->id);
        $ensureItem('Career Applications', '/admin/career-applications', 'fa-briefcase', 'career.view', 3, (int) $communications->id);

        $settings = $ensureItem('Settings', '/admin/settings', 'fa-sliders', 'settings.manage', 4, null);

        foreach ([$dashboard->id => 0, $website->id => 1, $users->id => 2, $communications->id => 3, $settings->id => 4] as $id => $order) {
            NavigationMenuItem::query()->whereKey($id)->update(['parent_id' => null, 'sort_order' => $order, 'is_visible' => true]);
        }

        cache()->forget('fuelfree.dashboard_navigation');
        cache()->forget('fuelfree.public_navigation');
    }

    public function down(): void
    {
        // Production repair migration: intentionally non-destructive.
    }
};
