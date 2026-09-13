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
                if (trim((string) $item->url) === '') return false;
                if ($item->permission_key && ! auth()->user()->hasPermission($item->permission_key)) return false;

                $navigationUrl = trim((string) $item->url);
                $navigationPath = parse_url($navigationUrl, PHP_URL_PATH) ?: $navigationUrl;
                if (trim($navigationPath, '/') === 'admin/news_and_Event') {
                    $item->label_override = null;
                    $item->label = 'News & Event';
                }

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

            if ($item->source_key === 'route:admin.site-content.index'
                || $item->route_name === 'admin.news_and_event.index'
                || $item->route_name === 'admin.news_and_event') {
                $item->label_override = null;
                $item->label = 'News & Event';
            }

            // Board of Directors is the canonical live profile destination.
            // Profile Builder remains a separate dashboard tool and is never
            // used as the rendered label for the public management route.
            if ($item->source_key === 'route:management' || $item->route_name === 'management') {
                $item->label_override = null;
                $item->label = 'Board of Directors';
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

        return $build();
    }
}
