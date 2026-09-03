<?php

namespace App\Services;

use App\Models\NavigationMenuItem;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Cache;

class PublicNavigationService
{
    public function tree(string $menu = 'main'): Collection
    {
        $cacheKey = "public.navigation.v3.{$menu}";
        $ids = Cache::remember($cacheKey, 600, fn (): array => NavigationMenuItem::query()
            ->where('menu', $menu)
            ->where('is_visible', true)
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
            if ($item->source_type === 'folder') return true;
            if (! $item->source_key) return false;
            $source = $registry->resolveAny($item->source_key, 'public');
            if (! $source) return false;

            $item->label = $source['label'];
            $item->url = $source['url'];
            $item->route_name = $source['route_name'];
            $item->permission_key = $source['permission'] ?? null;
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
        Cache::forget("public.navigation.v3.{$menu}");
        Cache::forget("public.navigation.v2.{$menu}");
        Cache::forget("public.navigation.{$menu}");
    }
}
