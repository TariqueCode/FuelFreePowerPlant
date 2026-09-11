<?php

namespace App\Services;

use App\Models\NavigationMenuItem;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Str;

class PublicNavigationService
{
    public function tree(string $menu = 'main'): Collection
    {
        abort_unless(in_array($menu, ['main', 'dashboard'], true), 404);
        $cacheKey = "public.navigation.v4.{$menu}";
        $ids = Cache::remember($cacheKey, 600, fn (): array => NavigationMenuItem::query()
            ->where('menu', $menu)
            ->where('is_visible', true)
            ->where(function ($query): void {
                $query->whereNull('source_key')->orWhere('source_key', '!=', '');
            })
            ->orderBy('sort_order')->orderBy('id')
            ->pluck('id')->map(fn ($id): int => (int) $id)->all());

        if ($ids === []) return collect();

        $items = NavigationMenuItem::query()
            ->whereIn('id', $ids)
            ->where('menu', $menu)
            ->where('is_visible', true)
            ->orderBy('sort_order')->orderBy('id')
            ->get();

        $registry = app(NavigationSourceRegistry::class);
        $valid = $items->filter(function (NavigationMenuItem $item) use ($registry): bool {
            if ($item->source_type === 'folder') {
                if (Str::startsWith((string) $item->source_key, 'management_folder:')) {
                    return $registry->resolveAny((string) $item->source_key, 'public') !== null;
                }
                return trim((string) $item->label) !== '';
            }

            if ($item->source_type === 'external_link') {
                return trim((string) $item->url) !== '' && preg_match('~^(?:https?://|/(?!/)|#)~i', (string) $item->url) === 1;
            }

            $sourceKey = $item->source_key ?: ($item->route_name ? 'route:'.$item->route_name : null);
            if (! $sourceKey) return false;
            $source = $registry->resolveAny($sourceKey, $item->area);
            if (! $source) return false;

            if ($item->label_override !== null && trim((string) $item->label_override) !== '') {
                $item->label = (string) $item->label_override;
            } elseif (trim((string) $item->label) === '') {
                $item->label = $source['label'];
            }
            $item->url = $source['url'];
            $item->route_name = $source['route_name'];
            $item->permission_key = $source['permission'] ?? null;
            $item->setAttribute('source_key', $source['key']);
            $item->setAttribute('source_type', $source['type']);
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
                })->values();
            unset($building[$parentId]);
            return $result;
        };
        return $build();
    }

    public function clear(string $menu = 'main'): void
    {
        Cache::forget("public.navigation.v4.{$menu}");
        Cache::forget("public.navigation.v3.{$menu}");
        Cache::forget("public.navigation.v2.{$menu}");
        Cache::forget("public.navigation.{$menu}");
    }
}
