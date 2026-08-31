<?php

namespace App\Services;

use App\Models\NavigationMenuItem;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Cache;

class PublicNavigationService
{
    public function tree(string $menu = 'main'): Collection
    {
        // Cache only scalar IDs. Never serialize hydrated Eloquent models,
        // which can become __PHP_Incomplete_Class after deployments.
        $cacheKey = "public.navigation.v2.{$menu}";

        $ids = Cache::remember($cacheKey, 600, function () use ($menu): array {
            return NavigationMenuItem::query()
                ->where('menu', $menu)
                ->where('is_visible', true)
                ->orderBy('sort_order')
                ->orderBy('id')
                ->pluck('id')
                ->map(static fn ($id): int => (int) $id)
                ->all();
        });

        if ($ids === []) {
            return collect();
        }

        $items = NavigationMenuItem::query()
            ->whereIn('id', $ids)
            ->where('menu', $menu)
            ->where('is_visible', true)
            ->orderBy('sort_order')
            ->orderBy('id')
            ->get();

        $children = $items->groupBy(fn (NavigationMenuItem $item) => $item->parent_id ?? 0);
        $building = [];

        $build = function (int $parentId = 0, int $depth = 0) use (&$build, $children, &$building): Collection {
            if ($depth > 20 || isset($building[$parentId])) {
                return collect();
            }

            $building[$parentId] = true;

            $result = ($children->get($parentId, collect()))
                ->map(function (NavigationMenuItem $item) use (&$build, $depth): NavigationMenuItem {
                    $item->setRelation('children', $build((int) $item->id, $depth + 1));
                    return $item;
                })
                ->values();

            unset($building[$parentId]);

            return $result;
        };

        return $build();
    }

    public function clear(string $menu = 'main'): void
    {
        Cache::forget("public.navigation.v2.{$menu}");
        // Remove the legacy key too, so stale serialized models cannot be reused.
        Cache::forget("public.navigation.{$menu}");
    }
}
