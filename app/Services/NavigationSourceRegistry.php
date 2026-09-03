<?php

namespace App\Services;

use App\Models\CmsPage;
use App\Models\NavigationMenuItem;
use Illuminate\Routing\Route;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Route as RouteFacade;
use Illuminate\Support\Str;

class NavigationSourceRegistry
{
    private const EXCLUDED_ROUTE_NAMES = [
        'favicon', 'login', 'login.store', 'logout', 'profile', 'profile.update',
        'career.store', 'career.chunks', 'contact.store', 'cms.page', 'gallery.show',
        'news.show', 'projects.show', 'resources.show', 'resources.download',
        'resources.shared-download', 'documents.shared-download', 'webmail.redirect',
        'admin.navigation.index',
        'admin.navigation.store', 'admin.navigation.update', 'admin.navigation.destroy', 'admin.navigation.reorder',
    ];

    private const NAVIGABLE_PREFIXES = ['site.', 'news.index', 'resources.index', 'sustainability', 'contact', 'management', 'home', 'dashboard', 'admin.'];

    private const LABELS = [
        'home' => 'Home', 'site.about' => 'About Us', 'site.plants' => 'Projects & Our Plans',
        'site.future-project' => 'Future Project', 'site.solutions' => 'Solutions',
        'site.gallery' => 'Gallery', 'management' => 'Board of Directors',
        'news.index' => 'News & Events', 'resources.index' => 'Resources',
        'sustainability' => 'Sustainability', 'contact' => 'Contact', 'site.career' => 'Career',
        'dashboard' => 'Dashboard', 'admin.dashboard' => 'Dashboard',
        'admin.users.index' => 'Users', 'admin.plants.index' => 'Power Plants',
        'admin.plants.performance.index' => 'Plant Performance',
        'admin.site-content.index' => 'Site Content', 'admin.site-popups.index' => 'Site Popups',
        'admin.sliders' => 'Sliders', 'admin.management.index' => 'Management',
        'admin.gallery.index' => 'Gallery', 'admin.helpdesk' => 'Help Desk',
        'admin.mail' => 'Mail', 'admin.career-applications.index' => 'Career Applications',
        'admin.inquiries.index' => 'Inquiries', 'admin.audit' => 'Audit Log',
        'admin.health' => 'System Health', 'admin.documents' => 'Documents',
        'admin.homepage-builder.index' => 'Homepage Builder', 'admin.design.index' => 'Design Builder',
        'admin.theme.index' => 'Theme Builder', 'admin.cms.index' => 'CMS',
        'admin.settings' => 'Settings', 'admin.social-links.index' => 'Social Links',
    ];

    public function available(string $area = 'public', string $menu = 'main'): Collection
    {
        $used = $this->usedSourceKeys($menu);

        $routes = collect(RouteFacade::getRoutes()->getRoutes())
            ->filter(fn (Route $route): bool => $this->eligibleRoute($route, $area))
            ->map(fn (Route $route): array => $this->routeSource($route, $area))
            ->filter(function (array $source): bool {
                return $source['permission'] === null || ! auth()->check() || auth()->user()->hasPermission($source['permission']);
            });

        $cms = $area === 'public'
            ? CmsPage::query()->where('is_published', true)->orderBy('title')
                ->get(['id', 'title', 'slug'])
                ->map(fn (CmsPage $page): array => [
                    'key' => 'cms_page:'.$page->id, 'type' => 'cms_page',
                    'label' => (string) $page->title,
                    'url' => route('cms.page', ['slug' => $page->slug]),
                    'route_name' => 'cms.page', 'area' => 'public', 'permission' => null,
                    'meta' => ['cms_page_id' => $page->id, 'slug' => $page->slug],
                ])
            : collect();

        return $routes->concat($cms)
            ->reject(fn (array $source): bool => $used->contains($source['key']))
            ->sortBy(fn (array $source): string => mb_strtolower($source['label']))
            ->values();
    }

    public function resolve(string $key, string $area = 'public', string $menu = 'main'): ?array
    {
        return $this->available($area, $menu)->firstWhere('key', $key);
    }

    public function resolveAny(string $key, string $area = 'public'): ?array
    {
        if (Str::startsWith($key, 'route:')) {
            $name = Str::after($key, 'route:');
            foreach (RouteFacade::getRoutes()->getRoutes() as $route) {
                if ($route->getName() === $name && $this->eligibleRoute($route, $area)) {
                    return $this->routeSource($route, $area);
                }
            }
        }

        if (Str::startsWith($key, 'cms_page:') && $area === 'public') {
            $id = (int) Str::after($key, 'cms_page:');
            $page = CmsPage::query()->whereKey($id)->where('is_published', true)->first();
            if ($page) {
                return [
                    'key' => $key, 'type' => 'cms_page', 'label' => (string) $page->title,
                    'url' => route('cms.page', ['slug' => $page->slug]), 'route_name' => 'cms.page',
                    'area' => 'public', 'permission' => null,
                    'meta' => ['cms_page_id' => $page->id, 'slug' => $page->slug],
                ];
            }
        }

        return null;
    }

    private function usedSourceKeys(string $menu): Collection
    {
        return NavigationMenuItem::query()->where('menu', $menu)->whereNotNull('source_key')->pluck('source_key');
    }

    private function eligibleRoute(Route $route, string $area): bool
    {
        $name = $route->getName();
        $uri = ltrim($route->uri(), '/');

        if (! $name || ! in_array($route->methods()[0] ?? null, ['GET', 'HEAD'], true)) return false;
        if ($this->isNavigationBuilderRoute($name)) return false;
        if (str_contains($uri, '{') || in_array($name, self::EXCLUDED_ROUTE_NAMES, true)) return false;

        if ($area === 'public') return ! str_starts_with($uri, 'admin/');
        if ($area === 'dashboard') return str_starts_with($uri, 'admin/') || $name === 'dashboard';

        return false;
    }

    private function isNavigationBuilderRoute(string $name): bool
    {
        return Str::startsWith($name, 'admin.navigation.');
    }

    private function routeSource(Route $route, string $area): array
    {
        $name = (string) $route->getName();
        $permission = collect($route->gatherMiddleware())
            ->first(fn (string $middleware): bool => Str::startsWith($middleware, 'permission:'));

        return [
            'key' => 'route:'.$name, 'type' => 'route',
            'label' => self::LABELS[$name] ?? $this->humanize($name),
            'url' => route($name), 'route_name' => $name, 'area' => $area,
            'permission' => $permission ? Str::after($permission, 'permission:') : null,
            'meta' => [],
        ];
    }

    private function humanize(string $name): string
    {
        $name = Str::replace(['admin.', '.index', '.'], ['admin ', '', ' '], $name);
        return Str::headline($name);
    }
}
