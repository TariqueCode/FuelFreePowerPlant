<?php

namespace App\Services;

use App\Models\CmsPage;
use Illuminate\Routing\Route;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Route as RouteFacade;
use Illuminate\Support\Str;

class NavigationSourceRegistry
{
    private const EXCLUDED_ROUTE_NAMES = [
        'favicon',
        'login',
        'login.store',
        'logout',
        'profile',
        'profile.update',
        'career.store',
        'career.chunks',
        'contact.store',
        'cms.page',
        'gallery.show',
        'news.show',
        'projects.show',
        'resources.show',
        'resources.download',
        'resources.shared-download',
        'documents.shared-download',
        'webmail.redirect',
    ];

    private const LABELS = [
        'home' => 'Home',
        'site.about' => 'About Us',
        'site.plants' => 'Projects & Our Plans',
        'site.future-project' => 'Future Project',
        'site.solutions' => 'Solutions',
        'site.gallery' => 'Gallery',
        'management' => 'Board of Directors',
        'news.index' => 'News & Events',
        'resources.index' => 'Resources',
        'sustainability' => 'Sustainability',
        'contact' => 'Contact',
        'site.career' => 'Career',
        'dashboard' => 'Dashboard',
        'admin.dashboard' => 'Dashboard',
        'admin.users.index' => 'Users',
        'admin.plants.index' => 'Power Plants',
        'admin.plants.performance.index' => 'Plant Performance',
        'admin.navigation.index' => 'Navigation',
        'admin.site-content.index' => 'Site Content',
        'admin.site-popups.index' => 'Site Popups',
        'admin.sliders' => 'Sliders',
        'admin.management.index' => 'Management',
        'admin.gallery.index' => 'Gallery',
        'admin.helpdesk' => 'Help Desk',
        'admin.mail' => 'Mail',
        'admin.career-applications.index' => 'Career Applications',
        'admin.inquiries.index' => 'Inquiries',
        'admin.audit' => 'Audit Log',
        'admin.health' => 'System Health',
        'admin.documents' => 'Documents',
        'admin.homepage-builder.index' => 'Homepage Builder',
        'admin.design.index' => 'Design Builder',
        'admin.theme.index' => 'Theme Builder',
        'admin.cms.index' => 'CMS',
        'admin.settings' => 'Settings',
        'admin.social-links.index' => 'Social Links',
        'portal.dashboard' => 'Client Portal',
    ];

    public function available(string $area = 'public', string $menu = 'main'): Collection
    {
        $used = $this->usedSourceKeys($menu);

        $routes = collect(RouteFacade::getRoutes()->getRoutes())
            ->filter(fn (Route $route): bool => $this->eligibleRoute($route, $area))
            ->map(fn (Route $route): array => $this->routeSource($route, $area))
            ->filter(fn (?array $source): bool => $source !== null);

        $cms = $area === 'public'
            ? CmsPage::query()
                ->where('is_published', true)
                ->orderBy('title')
                ->get(['id', 'title', 'slug'])
                ->map(fn (CmsPage $page): array => [
                    'key' => 'cms_page:'.$page->id,
                    'type' => 'cms_page',
                    'label' => (string) $page->title,
                    'url' => route('cms.page', ['slug' => $page->slug]),
                    'route_name' => 'cms.page',
                    'area' => 'public',
                    'permission' => null,
                    'meta' => ['cms_page_id' => $page->id, 'slug' => $page->slug],
                ])
            : collect();

        return $routes
            ->concat($cms)
            ->reject(fn (array $source): bool => $used->contains($source['key']))
            ->sortBy(fn (array $source): string => mb_strtolower($source['label']))
            ->values();
    }

    public function resolve(string $key, string $area = 'public'): ?array
    {
        return $this->available($area)->firstWhere('key', $key);
    }

    private function usedSourceKeys(string $menu): Collection
    {
        return \App\Models\NavigationMenuItem::query()
            ->where('menu', $menu)
            ->whereNotNull('source_key')
            ->pluck('source_key');
    }

    private function eligibleRoute(Route $route, string $area): bool
    {
        $name = $route->getName();
        $uri = ltrim($route->uri(), '/');

        if (! $name || ! in_array($route->methods()[0] ?? null, ['GET', 'HEAD'], true)) {
            return false;
        }

        if (str_contains($uri, '{') || in_array($name, self::EXCLUDED_ROUTE_NAMES, true)) {
            return false;
        }

        if ($area === 'public') {
            if (str_starts_with($uri, 'admin/') || str_starts_with($uri, 'mail.')) {
                return false;
            }

            return ! Str::contains($name, [
                '.store', '.update', '.destroy', '.toggle', '.reorder',
                '.create', '.edit', '.download', '.show',
            ]);
        }

        if ($area === 'dashboard') {
            if (! str_starts_with($uri, 'admin/') && $name !== 'dashboard' && $name !== 'portal.dashboard') {
                return false;
            }

            if (in_array($name, ['admin.navigation.index'], true)) {
                return false;
            }

            return ! Str::contains($name, [
                '.store', '.update', '.destroy', '.toggle', '.reorder',
                '.create', '.edit', '.download', '.show',
            ]);
        }

        return false;
    }

    private function routeSource(Route $route, string $area): array
    {
        $name = (string) $route->getName();
        $permission = collect($route->gatherMiddleware())
            ->first(fn (string $middleware): bool => Str::startsWith($middleware, 'permission:'));

        return [
            'key' => 'route:'.$name,
            'type' => 'route',
            'label' => self::LABELS[$name] ?? $this->humanize($name),
            'url' => route($name),
            'route_name' => $name,
            'area' => $area,
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
