<?php

namespace App\Services;

use App\Models\NavigationMenuItem;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Cache;

class PublicNavigationService
{
    public function tree(string $menu = 'main'): Collection
    {
        return Cache::remember("public.navigation.{$menu}", 600, function () use ($menu): Collection {
            $items = NavigationMenuItem::query()
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
        });
    }

    public function clear(string $menu = 'main'): void
    {
        Cache::forget("public.navigation.{$menu}");
    }
}
