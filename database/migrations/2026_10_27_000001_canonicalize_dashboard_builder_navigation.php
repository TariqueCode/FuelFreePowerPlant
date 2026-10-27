<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (! Schema::hasTable('navigation_menu_items')) {
            $this->createNavigationTable();
        }

        $this->reconcileColumns();
        $this->repairOrphanedParents();
        $this->repairParentCycles();
        $this->ensureParentForeignKey();

        $groups = [
            [
                'key' => 'route:admin.profile-builder.index',
                'keys' => ['route:admin.profile-builder.index', 'route:admin.management.index'],
                'routes' => ['admin.profile-builder.index', 'admin.management.index'],
                'route' => 'admin.profile-builder.index',
                'url' => '/admin/profile-builder',
                'urls' => ['/admin/management', '/admin/profile-builder'],
                'permission' => 'website.view',
            ],
            [
                'key' => 'route:admin.cms.index',
                'keys' => ['route:admin.cms.index', 'route:admin.page-builder.index'],
                'routes' => ['admin.cms.index', 'admin.page-builder.index'],
                'route' => 'admin.cms.index',
                'url' => '/admin/page-builder',
                'urls' => ['/admin/cms', '/admin/page-builder'],
                'permission' => 'cms.view',
            ],
            [
                'key' => 'route:admin.navigation.index',
                'keys' => ['route:admin.navigation.index', 'route:admin.menu-builder.index'],
                'routes' => ['admin.navigation.index', 'admin.menu-builder.index'],
                'route' => 'admin.menu-builder.index',
                'url' => '/admin/menu-builder',
                'urls' => ['/admin/navigation', '/admin/menu-builder'],
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
                'urls' => ['/admin/site-content', '/admin/site-content?type=news', '/admin/news_and_Event', '/admin/news-and-Event'],
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

                    if (! empty($group['urls'])) {
                        $query->orWhereIn('url', $group['urls']);
                    }
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
            if (Schema::hasColumn('navigation_menu_items', 'target')) {
                $updates['target'] = '_self';
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

    private function createNavigationTable(): void
    {
        Schema::create('navigation_menu_items', function (Blueprint $table): void {
            $table->id();
            $table->string('menu', 60)->default('main')->index();
            $table->string('group', 100)->nullable()->index();
            $table->foreignId('parent_id')->nullable()->constrained('navigation_menu_items')->nullOnDelete();
            $table->string('label', 160)->default('');
            $table->string('label_override', 160)->nullable();
            $table->string('url', 500)->nullable();
            $table->string('route_name', 160)->nullable();
            $table->string('target', 20)->default('_self');
            $table->string('icon', 100)->nullable();
            $table->boolean('is_visible')->default(true)->index();
            $table->unsignedInteger('sort_order')->default(0)->index();
            $table->string('source_key', 255)->nullable();
            $table->string('source_type', 40)->default('custom');
            $table->string('area', 20)->default('public')->index();
            $table->string('permission_key', 120)->nullable()->index();
            $table->timestamps();
            $table->index(['menu', 'parent_id', 'sort_order']);
            $table->unique(['menu', 'source_key'], 'navigation_menu_items_menu_source_unique');
        });
    }

    private function reconcileColumns(): void
    {
        $definitions = [
            'menu' => fn (Blueprint $table) => $table->string('menu', 60)->default('main')->index(),
            'group' => fn (Blueprint $table) => $table->string('group', 100)->nullable()->index(),
            'parent_id' => fn (Blueprint $table) => $table->unsignedBigInteger('parent_id')->nullable()->index(),
            'label' => fn (Blueprint $table) => $table->string('label', 160)->default(''),
            'label_override' => fn (Blueprint $table) => $table->string('label_override', 160)->nullable(),
            'url' => fn (Blueprint $table) => $table->string('url', 500)->nullable(),
            'route_name' => fn (Blueprint $table) => $table->string('route_name', 160)->nullable(),
            'target' => fn (Blueprint $table) => $table->string('target', 20)->default('_self'),
            'icon' => fn (Blueprint $table) => $table->string('icon', 100)->nullable(),
            'is_visible' => fn (Blueprint $table) => $table->boolean('is_visible')->default(true)->index(),
            'sort_order' => fn (Blueprint $table) => $table->unsignedInteger('sort_order')->default(0)->index(),
            'source_key' => fn (Blueprint $table) => $table->string('source_key', 255)->nullable(),
            'source_type' => fn (Blueprint $table) => $table->string('source_type', 40)->default('custom'),
            'area' => fn (Blueprint $table) => $table->string('area', 20)->default('public')->index(),
            'permission_key' => fn (Blueprint $table) => $table->string('permission_key', 120)->nullable()->index(),
            'created_at' => fn (Blueprint $table) => $table->timestamp('created_at')->nullable(),
            'updated_at' => fn (Blueprint $table) => $table->timestamp('updated_at')->nullable(),
        ];

        foreach ($definitions as $column => $definition) {
            if (! Schema::hasColumn('navigation_menu_items', $column)) {
                Schema::table('navigation_menu_items', $definition);
            }
        }

        DB::table('navigation_menu_items')
            ->where('menu', 'dashboard')
            ->where(function ($query): void {
                $query->whereNull('area')->orWhere('area', '!=', 'dashboard');
            })
            ->update(['area' => 'dashboard']);

        $this->deduplicateRouteNames();

        DB::table('navigation_menu_items')
            ->where(function ($query): void {
                $query->whereNull('source_key')->orWhere('source_key', '');
            })
            ->whereNotNull('route_name')
            ->where('route_name', '!=', '')
            ->get(['id', 'route_name', 'menu'])
            ->each(function (object $item): void {
                DB::table('navigation_menu_items')
                    ->where('id', (int) $item->id)
                    ->update([
                        'source_key' => 'route:'.$item->route_name,
                        'source_type' => 'route',
                        'area' => $item->menu === 'dashboard' ? 'dashboard' : 'public',
                    ]);
            });

        DB::table('navigation_menu_items')
            ->where(function ($query): void {
                $query->whereNull('source_key')->orWhere('source_key', '');
            })
            ->where(function ($query): void {
                $query->whereNull('route_name')->orWhere('route_name', '');
            })
            ->where(function ($query): void {
                $query->whereNull('url')->orWhere('url', '')->orWhere('url', '#');
            })
            ->update(['source_type' => 'folder']);

        // Empty strings are not source identities; NULL is safe with the
        // unique (menu, source_key) index and supports folders/custom URLs.
        DB::table('navigation_menu_items')
            ->where('source_key', '')
            ->update(['source_key' => null]);
    }

    private function ensureParentForeignKey(): void
    {
        if (! Schema::hasColumn('navigation_menu_items', 'parent_id')) {
            return;
        }

        try {
            $exists = collect(Schema::getForeignKeys('navigation_menu_items'))->contains(
                fn (array $foreign): bool => in_array('parent_id', $foreign['columns'] ?? [], true)
                    && ($foreign['foreign_table'] ?? null) === 'navigation_menu_items'
            );

            if (! $exists) {
                Schema::table('navigation_menu_items', function (Blueprint $table): void {
                    $table->foreign('parent_id')
                        ->references('id')
                        ->on('navigation_menu_items')
                        ->nullOnDelete();
                });
            }
        } catch (Throwable $exception) {
            // Some legacy MySQL metadata configurations do not permit FK introspection.
            // Orphan references are still repaired and verified independently.
        }
    }

    private function deduplicateRouteNames(): void
    {
        if (! Schema::hasColumn('navigation_menu_items', 'route_name')) {
            return;
        }

        $duplicates = DB::table('navigation_menu_items')
            ->select('menu', 'route_name')
            ->whereNotNull('route_name')
            ->where('route_name', '!=', '')
            ->groupBy('menu', 'route_name')
            ->havingRaw('COUNT(*) > 1')
            ->get();

        foreach ($duplicates as $group) {
            $records = DB::table('navigation_menu_items')
                ->where('menu', $group->menu)
                ->where('route_name', $group->route_name)
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
                        ->where('menu', $group->menu)
                        ->where('parent_id', $duplicateId)
                        ->update(['parent_id' => $targetParentId]);
                }

                DB::table('navigation_menu_items')
                    ->where('id', $duplicateId)
                    ->delete();
            }
        }
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
