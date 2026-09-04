<?php

namespace App\Services;

use App\Models\NavigationMenuItem;
use Illuminate\Support\Collection;

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
            if (! $item->source_key) return false;
            $source = $registry->resolveAny($item->source_key, 'dashboard');
            if (! $source) return false;

            $permission = $source['permission'] ?? null;
            if ($permission && auth()->user() && ! auth()->user()->hasPermission($permission)) return false;

            $item->label = $source['label'];
            $item->url = $source['url'];
            $item->route_name = $source['route_name'];
            $item->permission_key = $permission;
            return true;
        })->values();

        // An empty dashboard menu must never leave the administrator with
        // dead-looking builder entries. Build the same capability-aware
        // navigation from live route sources until a custom dashboard menu
        // has been configured. This also keeps the three builders reachable
        // when an older installation has no dashboard navigation rows yet.
        if ($valid->isEmpty()) {
            $valid = $this->defaultNavigation($registry);
        }

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

    private function defaultNavigation(NavigationSourceRegistry $registry): Collection
    {
        $id = -1;
        $makeLink = function (string $label, string $routeName, ?string $permission = null, int $parentId = 0) use ($registry, &$id): ?NavigationMenuItem {
            $source = $registry->resolveAny('route:'.$routeName, 'dashboard');
            if (! $source) return null;
            if ($permission && auth()->user() && ! auth()->user()->hasPermission($permission)) return null;
            $item = new NavigationMenuItem();
            $item->id = $id--;
            $item->menu = 'dashboard';
            $item->parent_id = $parentId ?: null;
            $item->label = $label;
            $item->url = $source['url'];
            $item->route_name = $source['route_name'];
            $item->target = '_self';
            $item->is_visible = true;
            $item->sort_order = abs($id);
            $item->source_key = $source['key'];
            $item->source_type = 'route';
            $item->area = 'dashboard';
            $item->permission_key = $source['permission'] ?? $permission;
            return $item;
        };

        $folder = function (string $label, int $sort) use (&$id): NavigationMenuItem {
            $item = new NavigationMenuItem();
            $item->id = $id--;
            $item->menu = 'dashboard';
            $item->parent_id = null;
            $item->label = $label;
            $item->url = '#';
            $item->route_name = null;
            $item->target = '_self';
            $item->is_visible = true;
            $item->sort_order = $sort;
            $item->source_key = null;
            $item->source_type = 'folder';
            $item->area = 'dashboard';
            return $item;
        };

        $result = collect();
        if ($item = $makeLink('Dashboard', 'admin.dashboard', 'dashboard.view')) $result->push($item);

        $website = $folder('Website', 10);
        $website->setRelation('children', collect());
        foreach ([
            ['Homepage', 'admin.homepage-builder.index', 'website.view'],
            ['Slider', 'admin.sliders.index', 'website.view'],
            ['Highlight Banner', 'admin.site-popups.index', 'website.view'],
            ['Profile Builder', 'admin.profile-builder.index', 'website.view'],
            ['News & Notices', 'admin.site-content.index', 'website.view'],
            ['Gallery', 'admin.gallery.index', 'website.view'],
            ['Page Builder', 'admin.page-builder.index', 'cms.view'],
            ['Social Media', 'admin.social-links.index', 'social-media.manage'],
            ['Menu Builder', 'admin.menu-builder.index', 'website.view'],
            ['Documents & Media', 'admin.documents', 'documents.view'],
        ] as [$label, $routeName, $permission]) {
            if ($item = $makeLink($label, $routeName, $permission, $website->id)) $website->children->push($item);
        }
        if ($website->children->isNotEmpty()) $result->push($website);

        $operations = $folder('Operations', 20);
        $operations->setRelation('children', collect());
        if ($item = $makeLink((string) config('fuelfree.projects.label', 'Projects & Our Plans'), 'admin.plants.index', 'plants.view', $operations->id)) $operations->children->push($item);
        if ($operations->children->isNotEmpty()) $result->push($operations);

        $access = $folder('Users & Access', 30);
        $access->setRelation('children', collect());
        foreach ([
            ['Users', 'admin.users.index', 'users.view'],
            ['Audit Log', 'admin.audit', 'audit.view'],
            ['System Health', 'admin.health', 'health.view'],
        ] as [$label, $routeName, $permission]) {
            if ($item = $makeLink($label, $routeName, $permission, $access->id)) $access->children->push($item);
        }
        if ($access->children->isNotEmpty()) $result->push($access);

        $communications = $folder('Communications', 40);
        $communications->setRelation('children', collect());
        foreach ([
            ['Help Desk', 'admin.helpdesk', 'mail.view'],
            ['Mail', 'admin.mail', 'mail.view'],
            ['Career Applications', 'admin.career-applications.index', 'career.view'],
            ['Website Inquiries', 'admin.inquiries.index', 'inquiries.view'],
        ] as [$label, $routeName, $permission]) {
            if ($item = $makeLink($label, $routeName, $permission, $communications->id)) $communications->children->push($item);
        }
        if ($communications->children->isNotEmpty()) $result->push($communications);

        if ($item = $makeLink('Settings', 'admin.settings', 'settings.manage')) $result->push($item);

        return $result;
    }
}
