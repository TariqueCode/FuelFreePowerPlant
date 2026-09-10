<?php

namespace App\Services;

use App\Models\NavigationMenuItem;
use Illuminate\Support\Collection;
use Illuminate\Support\Str;

class DashboardNavigationService
{
    public function tree(string $menu = 'dashboard'): Collection
    {
        $items = NavigationMenuItem::query()
            ->where('menu', $menu)
            ->where('area', 'dashboard')
            ->where('is_visible', true)
            ->orderBy('sort_order')->orderBy('id')->get();

        $registry = app(NavigationSourceRegistry::class);

        $valid = $items->filter(function (NavigationMenuItem $item) use ($registry): bool {
            if ($item->source_type === 'folder') return true;

            if ($item->source_type === 'external_link') {
                $url = trim((string) $item->url);
                if ($url === '') return false;
                if (Str::startsWith($url, ['/admin/site-content', '/admin/plants', '/plants', '/future-project', '/solutions'])) return false;
                if ($item->permission_key && ! auth()->user()->hasPermission($item->permission_key)) return false;
                return true;
            }

            if (! $item->source_key) return false;

            $source = $registry->resolveAny($item->source_key, 'dashboard');
            if (! $source) return false;

            $permission = $source['permission'] ?? null;
            if ($permission && ! auth()->user()->hasPermission($permission)) return false;

            $item->label = $source['label'];
            $item->url = $source['url'];
            $item->route_name = $source['route_name'];
            $item->permission_key = $permission;

            if (Str::startsWith((string) $item->source_key, 'management_folder:') || $item->route_name === 'management') {
                $item->label_override = 'Profile Builder';
            }

            return true;
        })->values();

        $children = $valid->groupBy(fn (NavigationMenuItem $item) => $item->parent_id ?? 0);
        $building = [];

        $build = function (int $parentId = 0, int $depth = 0) use (&$build, $children, &$building): Collection {
            if ($depth > 20 || isset($building[$parentId])) return collect();
            $building[$parentId] = true;

            $result = $children->get($parentId, collect())
                ->map(function (NavigationMenuItem $item) use (&$build, $depth): NavigationMenuItem {
                    $item->setRelation('children', $build((int) $item->id, $depth + 1));
                    return $item;
                })
                ->filter(function (NavigationMenuItem $item): bool {
                    return $item->source_type !== 'folder' || $item->children->isNotEmpty();
                })->values();

            unset($building[$parentId]);
            return $result;
        };

        $tree = $build();

        $this->appendCapabilitySource($tree, $registry, 'route:admin.settings', 'settings.manage', 'Settings', 'fa-sliders');
        $this->appendCapabilitySource($tree, $registry, 'route:admin.users.index', 'users.view', 'Users', 'fa-users');

        return $tree;
    }

    private function appendCapabilitySource(Collection $tree, NavigationSourceRegistry $registry, string $sourceKey, string $permission, string $label, string $icon): void
    {
        if (! auth()->user()->hasPermission($permission) || $this->containsRoute($tree, $registry->resolveAny($sourceKey, 'dashboard')['route_name'] ?? null)) return;

        $source = $registry->resolveAny($sourceKey, 'dashboard');
        if (! $source) return;

        $item = new NavigationMenuItem([
            'label' => $label,
            'url' => $source['url'],
            'route_name' => $source['route_name'],
            'target' => '_self',
            'icon' => $icon,
            'is_visible' => true,
            'sort_order' => PHP_INT_MAX,
            'source_key' => $source['key'],
            'source_type' => $source['type'],
            'area' => 'dashboard',
            'permission_key' => $source['permission'] ?? $permission,
        ]);
        $item->setRelation('children', collect());
        $tree->push($item);
    }

    private function containsRoute(Collection $items, ?string $routeName): bool
    {
        if (! $routeName) return false;
        foreach ($items as $item) {
            if ($item->route_name === $routeName) return true;
            if ($item->children instanceof Collection && $this->containsRoute($item->children, $routeName)) return true;
        }
        return false;
    }
}
