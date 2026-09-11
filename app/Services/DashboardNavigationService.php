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

            // Manually entered links are valid dashboard destinations without a
            // registry source. This keeps the dashboard navigation consistent
            // with NavigationMenuController, which explicitly supports URL items.
            if ($item->source_type === 'external_link') {
                if (trim((string) $item->url) === '') return false;
                if ($item->permission_key && ! auth()->user()->hasPermission($item->permission_key)) return false;

                // News & Event is the canonical dashboard destination. Existing
                // external-link records may still carry the former News & Notices
                // label, so normalize the rendered label by destination URL too.
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

            // News & Event is now the canonical dashboard destination. Older
            // dashboard records may still contain the former "News & Notices"
            // label override, so clear it at render time rather than requiring
            // a manual database migration for every existing navigation item.
            if ($item->source_key === 'route:admin.site-content.index'
                || $item->route_name === 'admin.news_and_event.index'
                || $item->route_name === 'admin.news_and_event') {
                $item->label_override = null;
                $item->label = 'News & Event';
            }

            // Profile Builder is an admin-only builder label. Its public
            // destination remains the dynamically named management folder.
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

        return $build();
    }
}
