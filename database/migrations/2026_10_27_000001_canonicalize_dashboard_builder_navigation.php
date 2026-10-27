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

        $this->repairOrphanedParents();
        $this->repairParentCycles();

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

        $this->deduplicateSourceKeys();
        $this->ensureUniqueSourceIndex();
    }

    private function repairOrphanedParents(): void
    {
        if (! Schema::hasColumn('navigation_menu_items', 'parent_id')) {
            return;
        }

        $orphanIds = DB::table('navigation_menu_items as child')
            ->leftJoin('navigation_menu_items as parent', function ($join): void {
                $join->on('child.parent_id', '=', 'parent.id')
                    ->on('child.menu', '=', 'parent.menu');
            })
            ->whereNotNull('child.parent_id')
            ->whereNull('parent.id')
            ->pluck('child.id');

        foreach ($orphanIds as $id) {
            DB::table('navigation_menu_items')
                ->where('id', (int) $id)
                ->update(['parent_id' => null]);
        }
    }

    private function repairParentCycles(): void
    {
        if (! Schema::hasColumn('navigation_menu_items', 'parent_id')) {
            return;
        }

        $rows = DB::table('navigation_menu_items')->get(['id', 'menu', 'parent_id']);
        $menuById = $rows->mapWithKeys(fn (object $row): array => [(int) $row->id => (string) $row->menu])->all();
        $parentById = $rows->mapWithKeys(fn (object $row): array => [
            (int) $row->id => $row->parent_id === null ? null : (int) $row->parent_id,
        ])->all();
        $breakIds = [];

        foreach ($rows as $row) {
            $path = [];
            $positions = [];
            $cursor = (int) $row->id;
            $menu = (string) $row->menu;

            while (isset($parentById[$cursor]) && $parentById[$cursor] !== null) {
                if (isset($positions[$cursor])) {
                    $cycle = array_slice($path, $positions[$cursor]);
                    if ($cycle !== []) {
                        $breakIds[max($cycle)] = true;
                    }
                    break;
                }

                $positions[$cursor] = count($path);
                $path[] = $cursor;
                $next = $parentById[$cursor];

                if (! isset($menuById[$next]) || $menuById[$next] !== $menu) {
                    break;
                }

                $cursor = $next;
            }
        }

        foreach (array_keys($breakIds) as $id) {
            DB::table('navigation_menu_items')
                ->where('id', (int) $id)
                ->update(['parent_id' => null]);
        }
    }

    private function deduplicateSourceKeys(): void
    {
        if (! Schema::hasColumn('navigation_menu_items', 'source_key')) {
            return;
        }

        $duplicates = DB::table('navigation_menu_items')
            ->select('menu', 'source_key')
            ->whereNotNull('source_key')
            ->where('source_key', '!=', '')
            ->groupBy('menu', 'source_key')
            ->havingRaw('COUNT(*) > 1')
            ->get();

        foreach ($duplicates as $duplicateGroup) {
            $records = DB::table('navigation_menu_items')
                ->where('menu', $duplicateGroup->menu)
                ->where('source_key', $duplicateGroup->source_key)
                ->orderBy('id')
                ->get(['id', 'parent_id']);

            if ($records->count() < 2) {
                continue;
            }

            $keep = $records->first();
            $duplicateIds = $records->skip(1)->pluck('id')->map(fn ($id): int => (int) $id)->all();
            $targetParentId = $keep->parent_id === null ? null : (int) $keep->parent_id;

            if ($targetParentId !== null && in_array($targetParentId, $duplicateIds, true)) {
                $targetParentId = null;
            }

            foreach ($duplicateIds as $duplicateId) {
                if (Schema::hasColumn('navigation_menu_items', 'parent_id')) {
                    DB::table('navigation_menu_items')
                        ->where('menu', $duplicateGroup->menu)
                        ->where('parent_id', $duplicateId)
                        ->update(['parent_id' => $targetParentId]);
                }

                DB::table('navigation_menu_items')
                    ->where('id', $duplicateId)
                    ->delete();
            }
        }
    }

    private function ensureUniqueSourceIndex(): void
    {
        if (! Schema::hasColumn('navigation_menu_items', 'menu')
            || ! Schema::hasColumn('navigation_menu_items', 'source_key')) {
            return;
        }

        $exists = collect(Schema::getIndexes('navigation_menu_items'))->contains(
            fn (array $index): bool => (bool) ($index['unique'] ?? false)
                && count($index['columns'] ?? []) === 2
                && in_array('menu', $index['columns'] ?? [], true)
                && in_array('source_key', $index['columns'] ?? [], true)
        );

        if (! $exists) {
            Schema::table('navigation_menu_items', function ($table): void {
                $table->unique(['menu', 'source_key'], 'navigation_menu_items_menu_source_unique');
            });
        }
    }

    public function down(): void
    {
        // Intentionally non-destructive: reversing aliases could reintroduce
        // duplicate navigation entries in production databases.
    }
};
