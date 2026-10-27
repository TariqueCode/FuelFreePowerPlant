<?php

namespace App\Services;

use App\Models\NavigationMenuItem;
use Illuminate\Support\Collection;
use Illuminate\Support\Str;

class DashboardNavigationService
{
    private const SOURCE_KEY_ALIASES = [
        'admin.management.index' => 'admin.profile-builder.index',
        'admin.page-builder.index' => 'admin.cms.index',
        'admin.menu-builder.index' => 'admin.navigation.index',
        'admin.site-content.index' => 'admin.news_and_event.index',
        'admin.news_and_event' => 'admin.news_and_event.index',
    ];

    private const GROUPS = [
        'Website' => 10,
        'Operations' => 20,
        'Users & Access' => 30,
        'Communications' => 40,
    ];

    public function tree(string $menu = 'dashboard'): Collection
    {
        $allItems = NavigationMenuItem::query()
            ->where('menu', $menu)
            ->where('area', 'dashboard')
            ->orderBy('sort_order')
            ->orderBy('id')
            ->get();

        $items = $allItems->filter(fn (NavigationMenuItem $item): bool => (bool) $item->is_visible)->values();
        $registry = app(NavigationSourceRegistry::class);

        $valid = $items->filter(function (NavigationMenuItem $item) use ($registry): bool {
            if ($item->source_type === 'folder') {
                return true;
            }

            if ($item->source_type === 'external_link') {
                if (trim((string) $item->url) === '') {
                    return false;
                }

                return ! $item->permission_key
                    || (auth()->check() && auth()->user()->hasPermission($item->permission_key));
            }

            $sourceKey = $this->normalizeSourceKey($item->source_key, $item->route_name);
            if (! $sourceKey) {
                return false;
            }

            $source = $registry->resolveAny($sourceKey, 'dashboard');
            if (! $source) {
                return false;
            }

            $permission = $source['permission'] ?? null;
            if ($permission && (! auth()->check() || ! auth()->user()->hasPermission($permission))) {
                return false;
            }

            $item->source_key = $sourceKey;
            $item->label = $source['label'];
            $item->url = $source['url'];
            $item->route_name = $source['route_name'];
            $item->permission_key = $permission;

            if ($sourceKey === 'route:admin.dashboard') {
                $item->label = 'Dashboard';
            } elseif ($sourceKey === 'route:admin.news_and_event.index') {
                $item->label = 'News & Event';
            } elseif ($sourceKey === 'route:admin.plants.index') {
                $item->label = (string) config('fuelfree.projects.label', 'Projects & Our Plans');
            }

            return true;
        })->values();

        $valid = $this->deduplicateLiveSources($valid);

        if ($menu === 'dashboard') {
            $valid = $this->restoreDefaultCapabilities($valid, $allItems, $registry);
        }

        $children = $valid->groupBy(fn (NavigationMenuItem $item) => $item->parent_id ?? 0);
        $building = [];

        $build = function (int $parentId = 0, int $depth = 0) use (&$build, $children, &$building): Collection {
            if ($depth > 20 || isset($building[$parentId])) {
                return collect();
            }

            $building[$parentId] = true;
            $result = $children->get($parentId, collect())
                ->sortBy(fn (NavigationMenuItem $item) => [(int) $item->sort_order, (int) $item->getKey()])
                ->map(function (NavigationMenuItem $item) use (&$build, $depth): NavigationMenuItem {
                    $item->setRelation('children', $build((int) $item->getKey(), $depth + 1));

                    return $item;
                })
                ->filter(fn (NavigationMenuItem $item): bool =>
                    $item->source_type !== 'folder' || $item->children->isNotEmpty()
                )
                ->values();

            unset($building[$parentId]);

            return $result;
        };

        return $build();
    }

    private function restoreDefaultCapabilities(
        Collection $items,
        Collection $allItems,
        NavigationSourceRegistry $registry
    ): Collection {
        $nextId = -1000;
        $hiddenKeys = $allItems
            ->filter(fn (NavigationMenuItem $item): bool => ! (bool) $item->is_visible)
            ->map(fn (NavigationMenuItem $item): ?string => $this->normalizeSourceKey($item->source_key, $item->route_name))
            ->filter()
            ->flip();

        $items = $this->deduplicateBuiltinFolders($items);
        $allFolders = $allItems->filter(fn (NavigationMenuItem $item): bool => $item->source_type === 'folder');
        $folderIds = [];
        $hiddenGroups = [];

        foreach (self::GROUPS as $label => $sortOrder) {
            $visibleFolder = $items->first(fn (NavigationMenuItem $item): bool =>
                $item->source_type === 'folder'
                && strcasecmp(trim((string) $item->label), $label) === 0
            );

            if ($visibleFolder) {
                $folderIds[$label] = (int) $visibleFolder->getKey();
                continue;
            }

            $hasHiddenFolder = $allFolders->contains(fn (NavigationMenuItem $item): bool =>
                ! (bool) $item->is_visible
                && strcasecmp(trim((string) $item->label), $label) === 0
            );

            if ($hasHiddenFolder) {
                $hiddenGroups[$label] = true;
                continue;
            }

            $folder = $this->makeFolder($label, $nextId--, $sortOrder);
            $items->push($folder);
            $folderIds[$label] = (int) $folder->getKey();
        }

        $defaults = [
            ['Dashboard', 'route:admin.dashboard', 'fa-house', 0, null],
            ['Homepage', 'route:admin.homepage-builder.index', 'fa-house', 10, 'Website'],
            ['Slider', 'route:admin.sliders.index', 'fa-images', 20, 'Website'],
            ['Highlight Banner', 'route:admin.site-popups.index', 'fa-bullhorn', 30, 'Website'],
            ['Profile Builder', 'route:admin.profile-builder.index', 'fa-user-tie', 40, 'Website'],
            ['News & Event', 'route:admin.news_and_event.index', 'fa-newspaper', 50, 'Website'],
            ['Gallery', 'route:admin.gallery.index', 'fa-images', 60, 'Website'],
            ['Page Builder', 'route:admin.cms.index', 'fa-file-lines', 70, 'Website'],
            ['Social Media', 'route:admin.social-links.index', 'fa-share-nodes', 80, 'Website'],
            ['Menu Builder', 'route:admin.navigation.index', 'fa-sitemap', 90, 'Website'],
            ['Documents & Media', 'route:admin.documents', 'fa-folder-open', 100, 'Website'],
            [(string) config('fuelfree.projects.label', 'Projects & Our Plans'), 'route:admin.plants.index', 'fa-leaf', 10, 'Operations'],
            ['Users', 'route:admin.users.index', 'fa-users', 10, 'Users & Access'],
            ['Audit Log', 'route:admin.audit', 'fa-clipboard-list', 20, 'Users & Access'],
            ['System Health', 'route:admin.health', 'fa-heart-pulse', 30, 'Users & Access'],
            ['Help Desk', 'route:admin.helpdesk', 'fa-headset', 10, 'Communications'],
            ['Mail', 'route:admin.mail', 'fa-envelope', 20, 'Communications'],
            ['Career Applications', 'route:admin.career-applications.index', 'fa-briefcase', 30, 'Communications'],
            ['Website Inquiries', 'route:admin.inquiries.index', 'fa-inbox', 40, 'Communications'],
            ['Settings', 'route:admin.settings', 'fa-sliders', 100, null],
        ];

        $existingKeys = $items
            ->filter(fn (NavigationMenuItem $item): bool => ! in_array($item->source_type, ['folder', 'external_link'], true))
            ->map(fn (NavigationMenuItem $item): ?string => $this->normalizeSourceKey($item->source_key, $item->route_name))
            ->filter()
            ->flip();

        foreach ($defaults as [$label, $sourceKey, $icon, $sortOrder, $groupLabel]) {
            $sourceKey = $this->normalizeSourceKey($sourceKey);
            if (! $sourceKey || $hiddenKeys->has($sourceKey)) {
                continue;
            }

            if ($groupLabel !== null && (! isset($folderIds[$groupLabel]) || isset($hiddenGroups[$groupLabel]))) {
                continue;
            }

            $source = $registry->resolveAny($sourceKey, 'dashboard');
            if (! $source) {
                continue;
            }

            $permission = $source['permission'] ?? null;
            if ($permission && (! auth()->check() || ! auth()->user()->hasPermission($permission))) {
                continue;
            }

            $existing = $items->first(function (NavigationMenuItem $item) use ($sourceKey): bool {
                if (in_array($item->source_type, ['folder', 'external_link'], true)) {
                    return false;
                }

                return $this->normalizeSourceKey($item->source_key, $item->route_name) === $sourceKey;
            });

            $parentId = $groupLabel === null ? null : $folderIds[$groupLabel];

            if ($existing) {
                $existing->source_key = $sourceKey;
                $existing->route_name = $source['route_name'];
                $existing->url = $source['url'];
                $existing->permission_key = $permission;

                $existing->parent_id = $parentId;

                if (trim((string) $existing->label_override) === '') {
                    $existing->label = $label;
                }

                continue;
            }

            if ($existingKeys->has($sourceKey)) {
                continue;
            }

            $item = new NavigationMenuItem([
                'menu' => 'dashboard',
                'parent_id' => $parentId,
                'label' => $label,
                'label_override' => null,
                'url' => $source['url'],
                'route_name' => $source['route_name'],
                'target' => '_self',
                'icon' => $icon,
                'is_visible' => true,
                'sort_order' => $sortOrder,
                'source_key' => $sourceKey,
                'source_type' => $source['type'],
                'area' => 'dashboard',
                'permission_key' => $permission,
            ]);
            $item->id = $nextId--;
            $item->exists = false;
            $items->push($item);
            $existingKeys->put($sourceKey, true);
        }

        return $items->values();
    }

    private function deduplicateLiveSources(Collection $items): Collection
    {
        $items = $items->sortBy(fn (NavigationMenuItem $item) => (int) $item->getKey())->values();
        $keptByKey = [];
        $duplicates = [];

        foreach ($items as $item) {
            if (in_array($item->source_type, ['folder', 'external_link'], true)) {
                continue;
            }

            $key = $this->normalizeSourceKey($item->source_key, $item->route_name);
            if (! $key) {
                continue;
            }

            $item->source_key = $key;
            if (! isset($keptByKey[$key])) {
                $keptByKey[$key] = $item;
                continue;
            }

            $duplicates[(int) $item->getKey()] = (int) $keptByKey[$key]->getKey();
        }

        if ($duplicates === []) {
            return $items;
        }

        $items->each(function (NavigationMenuItem $item) use ($duplicates): void {
            $parentId = (int) ($item->parent_id ?? 0);
            if (isset($duplicates[$parentId])) {
                $item->parent_id = $duplicates[$parentId];
            }
        });

        return $items
            ->reject(fn (NavigationMenuItem $item): bool => isset($duplicates[(int) $item->getKey()]))
            ->values();
    }

    private function deduplicateBuiltinFolders(Collection $items): Collection
    {
        $duplicates = [];

        foreach (array_keys(self::GROUPS) as $label) {
            $matches = $items
                ->filter(fn (NavigationMenuItem $item): bool =>
                    $item->source_type === 'folder'
                    && strcasecmp(trim((string) $item->label), $label) === 0
                )
                ->sortBy(fn (NavigationMenuItem $item) => (int) $item->getKey())
                ->values();

            if ($matches->count() < 2) {
                continue;
            }

            $keepId = (int) $matches->first()->getKey();
            foreach ($matches->skip(1) as $duplicate) {
                $duplicates[(int) $duplicate->getKey()] = $keepId;
            }
        }

        if ($duplicates === []) {
            return $items;
        }

        $items->each(function (NavigationMenuItem $item) use ($duplicates): void {
            $parentId = (int) ($item->parent_id ?? 0);
            if (isset($duplicates[$parentId])) {
                $item->parent_id = $duplicates[$parentId];
            }
        });

        return $items
            ->reject(fn (NavigationMenuItem $item): bool => isset($duplicates[(int) $item->getKey()]))
            ->values();
    }

    private function normalizeSourceKey(?string $sourceKey, ?string $routeName = null): ?string
    {
        $sourceKey = trim((string) $sourceKey);
        if ($sourceKey === '' && trim((string) $routeName) !== '') {
            $sourceKey = 'route:'.trim((string) $routeName);
        }

        if ($sourceKey === '') {
            return null;
        }

        if (! Str::startsWith($sourceKey, 'route:')) {
            return $sourceKey;
        }

        $name = Str::after($sourceKey, 'route:');

        return 'route:'.(self::SOURCE_KEY_ALIASES[$name] ?? $name);
    }

    private function makeFolder(string $label, int $id, int $sortOrder): NavigationMenuItem
    {
        $folder = new NavigationMenuItem([
            'menu' => 'dashboard',
            'parent_id' => null,
            'label' => $label,
            'url' => '#',
            'route_name' => null,
            'target' => '_self',
            'is_visible' => true,
            'sort_order' => $sortOrder,
            'source_key' => null,
            'source_type' => 'folder',
            'area' => 'dashboard',
            'permission_key' => null,
        ]);
        $folder->id = $id;
        $folder->exists = false;

        return $folder;
    }
}
