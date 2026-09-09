<?php

namespace App\Services;

use App\Models\NavigationMenuItem;
use Illuminate\Support\Collection;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Route as RouteFacade;

class DashboardNavigationService
{
    private const BUILDER_ROUTE_ALIASES = [
        'admin.management.index' => ['admin.profile-builder.index', 'Profile Builder'],
        'admin.cms.index' => ['admin.page-builder.index', 'Page Builder'],
        'admin.navigation.index' => ['admin.menu-builder.index', 'Menu Builder'],
    ];

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
            $source = $registry->resolveAny($item->source_key, 'dashboard');

            // The navigation database stores legacy builder source keys while
            // the actual dashboard routes use their canonical builder names.
            if (! $source && isset(self::BUILDER_ROUTE_ALIASES[$item->source_key])) {
                [$routeName, $label] = self::BUILDER_ROUTE_ALIASES[$item->source_key];
                $route = RouteFacade::getRoutes()->getByName($routeName);
                if ($route) {
                    $source = [
                        'label' => $label,
                        'url' => $route->uri() === '/' ? '/' : '/'.ltrim($route->uri(), '/'),
                        'route_name' => $routeName,
                        'permission' => $this->routePermission($route),
                    ];
                }
            }

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

        // Settings is a system destination. Add it only when the database
        // navigation does not already contain a Settings destination by route
        // or label, preventing the duplicate entry seen during recovery.
        if (auth()->user()->hasPermission('settings.manage') && ! $this->containsSettings($tree)) {
            $settings = new NavigationMenuItem([
                'label' => 'Settings',
                'url' => route('admin.settings'),
                'route_name' => 'admin.settings',
                'target' => '_self',
                'icon' => 'fa-sliders',
                'is_visible' => true,
                'sort_order' => PHP_INT_MAX,
                'source_key' => 'route:admin.settings',
                'source_type' => 'route',
                'area' => 'dashboard',
                'permission_key' => 'settings.manage',
            ]);
            $settings->setRelation('children', collect());
            $tree->push($settings);
        }

        return $tree;
    }

    private function routePermission($route): ?string
    {
        return collect($route->gatherMiddleware())
            ->map(fn ($middleware): string => (string) $middleware)
            ->first(fn (string $middleware): bool => Str::startsWith($middleware, 'permission:'))
            ?->after('permission:');
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
