<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (! Schema::hasTable('navigation_menu_items')
            || ! Schema::hasColumn('navigation_menu_items', 'menu')
            || ! Schema::hasColumn('navigation_menu_items', 'source_key')
            || ! Schema::hasColumn('navigation_menu_items', 'route_name')) {
            return;
        }

        $groups = [
            [
                'key' => 'route:admin.profile-builder.index',
                'keys' => ['route:admin.profile-builder.index', 'route:admin.management.index'],
                'routes' => ['admin.profile-builder.index', 'admin.management.index'],
                'route' => 'admin.profile-builder.index',
                'url' => '/admin/profile-builder',
                'permission' => 'website.view',
            ],
            [
                'key' => 'route:admin.cms.index',
                'keys' => ['route:admin.cms.index', 'route:admin.page-builder.index'],
                'routes' => ['admin.cms.index', 'admin.page-builder.index'],
                'route' => 'admin.cms.index',
                'url' => '/admin/page-builder',
                'permission' => 'cms.view',
            ],
            [
                'key' => 'route:admin.navigation.index',
                'keys' => ['route:admin.navigation.index', 'route:admin.menu-builder.index'],
                'routes' => ['admin.navigation.index', 'admin.menu-builder.index'],
                'route' => 'admin.menu-builder.index',
                'url' => '/admin/menu-builder',
                'permission' => 'website.view',
            ],
            [
                'key' => 'route:admin.news_and_event.index',
                'keys' => [
                    'route:admin.site-content.index',
                    'route:admin.news_and_event',
                    'route:admin.news_and_event.index',
                ],
                'routes' => ['admin.site-content.index', 'admin.news_and_event', 'admin.news_and_event.index'],
                'route' => 'admin.news_and_event.index',
                'url' => '/admin/news-and-Event',
                'permission' => 'website.view',
            ],
        ];

        $websiteFolderId = null;
        if (Schema::hasColumn('navigation_menu_items', 'source_type')
            && Schema::hasColumn('navigation_menu_items', 'label')
            && Schema::hasColumn('navigation_menu_items', 'is_visible')) {
            $websiteFolderId = DB::table('navigation_menu_items')
                ->where('menu', 'dashboard')
                ->where('source_type', 'folder')
                ->where('is_visible', true)
                ->orderBy('id')
                ->get(['id', 'label'])
                ->first(fn (object $item): bool => strcasecmp(trim((string) $item->label), 'Website') === 0)
                ?->id;
        }

        foreach ($groups as $group) {
            $records = DB::table('navigation_menu_items')
                ->where('menu', 'dashboard')
                ->where(function ($query) use ($group): void {
                    $query->whereIn('source_key', $group['keys'])
                        ->orWhereIn('route_name', $group['routes']);
                })
                ->orderBy('id')
                ->get();

            if ($records->isEmpty()) {
                continue;
            }

            $keep = $records->first();
            $childParentId = $websiteFolderId !== null ? (int) $websiteFolderId : (int) $keep->id;

            foreach ($records->skip(1) as $duplicate) {
                if (Schema::hasColumn('navigation_menu_items', 'parent_id')) {
                    DB::table('navigation_menu_items')
                        ->where('menu', 'dashboard')
                        ->where('parent_id', (int) $duplicate->id)
                        ->update(['parent_id' => $childParentId]);
                }

                DB::table('navigation_menu_items')
                    ->where('id', (int) $duplicate->id)
                    ->delete();
            }

            $updates = [
                'source_key' => $group['key'],
                'route_name' => $group['route'],
            ];

            if (Schema::hasColumn('navigation_menu_items', 'url')) {
                $updates['url'] = $group['url'];
            }
            if (Schema::hasColumn('navigation_menu_items', 'source_type')) {
                $updates['source_type'] = 'route';
            }
            if (Schema::hasColumn('navigation_menu_items', 'area')) {
                $updates['area'] = 'dashboard';
            }
            if (Schema::hasColumn('navigation_menu_items', 'permission_key')) {
                $updates['permission_key'] = $group['permission'];
            }
            if ($websiteFolderId !== null && Schema::hasColumn('navigation_menu_items', 'parent_id')) {
                $updates['parent_id'] = (int) $websiteFolderId;
            }

            DB::table('navigation_menu_items')
                ->where('id', (int) $keep->id)
                ->update($updates);
        }
    }

    public function down(): void
    {
        // Intentionally non-destructive: reversing aliases could reintroduce
        // duplicate navigation entries in production databases.
    }
};
