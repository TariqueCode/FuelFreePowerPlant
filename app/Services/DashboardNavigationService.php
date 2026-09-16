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
            if ($item->source_key === 'route:admin.dashboard') $item->label = 'Dashboard';
            if ($item->source_key === 'route:admin.site-content.index' || $item->route_name === 'admin.news_and_event.index' || $item->route_name === 'admin.news_and_event') { $item->label_override = null; $item->label = 'News & Event'; }
            return true;
        })->values();

        $existingKeys = $valid->pluck('source_key')->filter()->flip();
        $website = $valid->first(fn (NavigationMenuItem $item): bool => $item->source_type === 'folder' && strcasecmp(trim((string) $item->label), 'Website') === 0);
        $virtual = collect();
        $addVirtualRoute = function (string $sourceKey, string $fallbackLabel, string $fallbackIcon, int $sortOrder, ?int $parentId) use (&$virtual, $existingKeys, $registry): void {
            if ($existingKeys->has($sourceKey)) return;
            $source = $registry->resolveAny($sourceKey, 'dashboard');
            if (! $source) return;
            $permission = $source['permission'] ?? null;
            if ($permission && (! auth()->check() || ! auth()->user()->hasPermission($permission))) return;
            $item = new NavigationMenuItem(['menu'=>'dashboard','parent_id'=>$parentId,'label'=>$source['label'] ?: $fallbackLabel,'label_override'=>null,'url'=>$source['url'],'route_name'=>$source['route_name'],'target'=>'_self','icon'=>$fallbackIcon,'is_visible'=>true,'sort_order'=>$sortOrder,'source_key'=>$sourceKey,'source_type'=>'route','area'=>'dashboard','permission_key'=>$permission]);
            if ($sourceKey === 'route:admin.dashboard') $item->label = $fallbackLabel;
            $item->exists = false;
            $virtual->push($item);
            $existingKeys->put($sourceKey, true);
        };
        $addVirtualRoute('route:admin.dashboard', 'Dashboard', 'fa-house', 0, null);
        if ($website?->getKey() !== null) {
            $addVirtualRoute('route:admin.profile-builder.index', 'Profile Builder', 'fa-user-tie', 3, (int) $website->getKey());
            $addVirtualRoute('route:admin.cms.index', 'Page Builder', 'fa-file-lines', 6, (int) $website->getKey());
            $addVirtualRoute('route:admin.header-footer.index', 'Header & Footer', 'fa-window-maximize', 9, (int) $website->getKey());
        }
        $valid = $valid->concat($virtual)->sortBy(fn (NavigationMenuItem $item): array => [(int) ($item->parent_id ?? 0), (int) $item->sort_order, (int) $item->getKey()])->values();
        $children = $valid->groupBy(fn (NavigationMenuItem $item) => $item->parent_id ?? 0);
        $building = [];
        $build = function (int $parentId = 0, int $depth = 0) use (&$build, $children, &$building): Collection {
            if ($depth > 20 || isset($building[$parentId])) return collect();
            $building[$parentId] = true;
            $result = $children->get($parentId, collect())->map(function (NavigationMenuItem $item) use (&$build, $depth): NavigationMenuItem { $item->setRelation('children', $build((int) $item->id, $depth + 1)); return $item; })->filter(fn (NavigationMenuItem $item): bool => $item->source_type !== 'folder' || $item->children->isNotEmpty())->values();
            unset($building[$parentId]);
            return $result;
        };
        return $build();
    }
}
