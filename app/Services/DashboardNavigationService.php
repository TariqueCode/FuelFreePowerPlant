<?php

namespace App\Services;

use App\Models\NavigationMenuItem;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Route;

class DashboardNavigationService
{
    public function tree(string $menu = 'dashboard'): Collection
    {
        $items = NavigationMenuItem::query()
            ->where('menu', $menu)
            ->where('area', 'dashboard')
            ->where('is_visible', true)
            ->orderBy('sort_order')
            ->orderBy('id')
            ->get();

        $valid = $items->filter(function (NavigationMenuItem $item): bool {
            if ($item->source_type === 'folder') {
                return true;
            }

            $routeName = (string) ($item->route_name ?: '');
            if ($routeName === '' || ! Route::has($routeName)) {
                return false;
            }

            $permission = $item->permission_key;
            if ($permission && auth()->user() && ! auth()->user()->hasPermission($permission)) {
                return false;
            }

            return true;
        })->values();

        $valid = $this->restoreDefaultCapabilities($valid);

        $children = $valid->groupBy(fn (NavigationMenuItem $item) => $item->parent_id ?? 0);
        $building = [];

        $build = function (int $parentId = 0, int $depth = 0) use (&$build, $children, &$building): Collection {
            if ($depth > 20 || isset($building[$parentId])) {
                return collect();
            }

            $building[$parentId] = true;
            $result = $children->get($parentId, collect())
                ->sortBy(fn (NavigationMenuItem $item) => [(int) $item->sort_order, (int) $item->id])
                ->map(function (NavigationMenuItem $item) use (&$build, $depth): NavigationMenuItem {
                    $item->setRelation('children', $build((int) $item->id, $depth + 1));
                    return $item;
                })
                ->filter(fn (NavigationMenuItem $item): bool => $item->source_type !== 'folder' || $item->children->isNotEmpty())
                ->values();

            unset($building[$parentId]);
            return $result;
        };

        return $build();
    }

    private function restoreDefaultCapabilities(Collection $items): Collection
    {
        $nextId = -1000;
        $nextSort = max(100, (int) $items->max('sort_order') + 1);

        $folders = [
            ['Website', 10],
            ['Operations', 20],
            ['Users & Access', 30],
            ['Communications', 40],
        ];

        foreach ($folders as [$label, $sort]) {
            $folder = $items->first(fn (NavigationMenuItem $item): bool =>
                $item->source_type === 'folder' && strcasecmp(trim((string) $item->label), $label) === 0
            );

            if (!$folder) {
                $folder = $this->makeFolder($label, $nextId--, $sort);
                $items->push($folder);
            }
        }

        $defaults = [
            ['Dashboard', 'admin.dashboard', 'dashboard.view', null],
            ['Homepage', 'admin.homepage-builder.index', 'website.view', 'Website'],
            ['Slider', 'admin.sliders.index', 'website.view', 'Website'],
            ['Highlight Banner', 'admin.site-popups.index', 'website.view', 'Website'],
            ['Profile Builder', 'admin.profile-builder.index', 'website.view', 'Website'],
            ['News & Notices', 'admin.site-content.index', 'website.view', 'Website'],
            ['Gallery', 'admin.gallery.index', 'website.view', 'Website'],
            ['Page Builder', 'admin.page-builder.index', 'cms.view', 'Website'],
            ['Social Media', 'admin.social-links.index', 'social-media.manage', 'Website'],
            ['Menu Builder', 'admin.menu-builder.index', 'website.view', 'Website'],
            ['Documents & Media', 'admin.documents', 'documents.view', 'Website'],
            [(string) config('fuelfree.projects.label', 'Projects & Our Plans'), 'admin.plants.index', 'plants.view', 'Operations'],
            ['Users', 'admin.users.index', 'users.view', 'Users & Access'],
            ['Audit Log', 'admin.audit', 'audit.view', 'Users & Access'],
            ['System Health', 'admin.health', 'health.view', 'Users & Access'],
            ['Help Desk', 'admin.helpdesk', 'mail.view', 'Communications'],
            ['Mail', 'admin.mail', 'mail.view', 'Communications'],
            ['Career Applications', 'admin.career-applications.index', 'career.view', 'Communications'],
            ['Website Inquiries', 'admin.inquiries.index', 'inquiries.view', 'Communications'],
            ['Settings', 'admin.settings', 'settings.manage', null],
        ];

        foreach ($defaults as [$label, $routeName, $permission, $folderLabel]) {
            if (!Route::has($routeName)) {
                continue;
            }
            if ($permission && auth()->user() && !auth()->user()->hasPermission($permission)) {
                continue;
            }
            if ($items->contains(fn (NavigationMenuItem $item): bool => $item->route_name === $routeName)) {
                continue;
            }

            $parentId = null;
            if ($folderLabel !== null) {
                $parent = $items->first(fn (NavigationMenuItem $item): bool =>
                    $item->source_type === 'folder' && strcasecmp(trim((string) $item->label), $folderLabel) === 0
                );
                $parentId = $parent?->id;
            }

            $item = new NavigationMenuItem();
            $item->id = $nextId--;
            $item->menu = 'dashboard';
            $item->parent_id = $parentId;
            $item->label = $label;
            $item->url = route($routeName);
            $item->route_name = $routeName;
            $item->target = '_self';
            $item->is_visible = true;
            $item->sort_order = $nextSort++;
            $item->source_key = 'route:' . $routeName;
            $item->source_type = 'route';
            $item->area = 'dashboard';
            $item->permission_key = $permission;
            $items->push($item);
        }

        return $items->values();
    }

    private function makeFolder(string $label, int $id, int $sort): NavigationMenuItem
    {
        $item = new NavigationMenuItem();
        $item->id = $id;
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
    }
}
