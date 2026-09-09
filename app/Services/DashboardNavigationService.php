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
                if (Str::startsWith($url, ['/admin/site-content', '/admin/plants'])) return false;
                if ($item->permission_key && ! auth()->user()->hasPermission($item->permission_key)) return false;
                return true;
            }

            if (! $item->source_key) return false;

            // NavigationSourceRegistry is the single source of truth for route
            // aliases, canonical routes, permissions and usable destinations.
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

        // Settings remains available when its system destination has not yet
        // been persisted by Menu Builder, but its route metadata comes from the
        // canonical navigation registry rather than a second alias implementation.
        if (auth()->user()->hasPermission('settings.manage') && ! $this->containsSettings($tree)) {
            $source = $registry->resolveAny('route:admin.settings', 'dashboard');
            if ($source) {
                $settings = new NavigationMenuItem([
                    'label' => $source['label'],
                    'url' => $source['url'],
                    'route_name' => $source['route_name'],
                    'target' => '_self',
                    'icon' => 'fa-sliders',
                    'is_visible' => true,
                    'sort_order' => PHP_INT_MAX,
                    'source_key' => $source['key'],
                    'source_type' => $source['type'],
                    'area' => 'dashboard',
                    'permission_key' => $source['permission'],
                ]);
                $settings->setRelation('children', collect());
                $tree->push($settings);
            }
        }

        return $tree;
    }

    private function containsSettings(Collection $items): bool
    {
        foreach ($items as $item) {
            if ($item->route_name === 'admin.settings' || Str::lower(trim((string) $item->label)) === 'settings') return true;
            if ($item->children instanceof Collection && $this->containsSettings($item->children)) return true;
        }

        return false;
    }
}
