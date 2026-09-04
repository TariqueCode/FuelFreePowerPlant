<?php

namespace App\Services;

use App\Models\CmsPage;
use App\Models\NavigationMenuItem;
use App\Models\SiteContentItem;
use Illuminate\Routing\Route;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Route as RouteFacade;
use Illuminate\Support\Str;

class NavigationSourceRegistry
{
    private const EXCLUDED_ROUTE_NAMES = [
        'favicon', 'login', 'login.store', 'logout', 'profile', 'profile.update',
        'career.store', 'career.chunks', 'contact.store', 'cms.page', 'gallery.show',
        'news.show', 'projects.show', 'documents.shared-download', 'webmail.redirect',
        'resources.index', 'resources.show', 'resources.download',
        'site.plants', 'site.future-project', 'site.solutions', 'sustainability',
    ];

    private const BUILDER_ROUTE_ALIASES = [
        'admin.management.index' => ['admin.profile-builder.index', 'Profile Builder'],
        'admin.cms.index' => ['admin.page-builder.index', 'Page Builder'],
        'admin.navigation.index' => ['admin.menu-builder.index', 'Menu Builder'],
    ];

    public function available(string $area = 'public', string $menu = 'main'): Collection
    {
        abort_unless(in_array($menu, ['main', 'dashboard'], true), 404);
        $used = $this->usedSourceKeys($menu);
        $routes = collect(RouteFacade::getRoutes()->getRoutes())
            ->filter(fn (Route $route): bool => $this->eligibleRoute($route, $area))
            ->reject(fn (Route $route): bool => isset(self::BUILDER_ROUTE_ALIASES[$route->getName()]))
            ->reject(fn (Route $route): bool => $this->hasCanonicalCmsPage($route, $area))
            ->map(fn (Route $route): array => $this->routeSource($route, $area))
            ->filter(fn (array $source): bool => $source['permission'] === null || ! auth()->check() || auth()->user()->hasPermission($source['permission']));

        $cms = $area === 'public'
            ? CmsPage::query()->where('is_published', true)->orderBy('title')->get(['id', 'title', 'slug'])
                ->map(fn (CmsPage $page): array => [
                    'key' => 'cms_page:'.$page->id, 'type' => 'cms_page', 'label' => (string) $page->title,
                    'url' => route('cms.page', ['slug' => $page->slug]), 'route_name' => 'cms.page', 'area' => 'public', 'permission' => null,
                    'meta' => ['cms_page_id' => $page->id, 'slug' => $page->slug],
                ])
            : collect();

        $companyPages = $area === 'public'
            ? SiteContentItem::query()
                ->where('type', 'company')
                ->where('status', 'published')
                ->where('slug', '!=', 'about-us')
                ->orderBy('title')
                ->get(['id', 'title', 'slug'])
                ->map(fn (SiteContentItem $page): array => [
                    'key' => 'site_content:'.$page->id,
                    'type' => 'site_content',
                    'label' => (string) $page->title,
                    'url' => route('company.page', ['slug' => $page->slug]),
                    'route_name' => 'company.page',
                    'area' => 'public',
                    'permission' => null,
                    'meta' => ['site_content_id' => $page->id, 'slug' => $page->slug, 'type' => $page->type],
                ])
            : collect();

        return $routes->concat($cms)->concat($companyPages)
            ->reject(fn (array $source): bool => $used->contains($source['key']))
            ->reject(fn (array $source): bool => ! $this->isUsableNavigationLabel($source['label']))
            ->sortBy(fn (array $source): string => mb_strtolower($source['label']))->values();
    }

    public function resolve(string $key, string $area = 'public', string $menu = 'main'): ?array
    {
        return $this->available($area, $menu)->firstWhere('key', $key);
    }

    public function resolveAny(string $key, string $area = 'public'): ?array
    {
        if (! in_array($area, ['public', 'dashboard'], true)) return null;
        if (Str::startsWith($key, 'route:')) {
            $name = Str::after($key, 'route:');
            if (isset(self::BUILDER_ROUTE_ALIASES[$name])) {
                [$canonical, $label] = self::BUILDER_ROUTE_ALIASES[$name];
                $route = collect(RouteFacade::getRoutes()->getRoutes())
                    ->first(fn (Route $route): bool => $route->getName() === $canonical);
                if ($route && $this->eligibleRoute($route, $area)) {
                    $source = $this->routeSource($route, $area);
                    $source['label'] = $label;
                    return $source;
                }
                return null;
            }
            $canonical = $this->canonicalCmsPageForRoute($name, $area);
            if ($canonical) return $canonical;
            $route = collect(RouteFacade::getRoutes()->getRoutes())->first(fn (Route $route): bool => $route->getName() === $name);
            if ($route && $this->eligibleRoute($route, $area)) {
                $source = $this->routeSource($route, $area);
                if (! $this->isUsableNavigationLabel($source['label'])) return null;
                if ($source['permission'] !== null && (! auth()->check() || ! auth()->user()->hasPermission($source['permission']))) return null;
                return $source;
            }
        }

        if (Str::startsWith($key, 'cms_page:') && $area === 'public') {
            $id = (int) Str::after($key, 'cms_page:');
            $page = CmsPage::query()->whereKey($id)->where('is_published', true)->first();
            if ($page && $this->isUsableNavigationLabel((string) $page->title)) {
                return ['key' => $key, 'type' => 'cms_page', 'label' => (string) $page->title,
                    'url' => route('cms.page', ['slug' => $page->slug]), 'route_name' => 'cms.page', 'area' => $area, 'permission' => null,
                    'meta' => ['cms_page_id' => $page->id, 'slug' => $page->slug]];
            }
        }

        if (Str::startsWith($key, 'site_content:') && $area === 'public') {
            $id = (int) Str::after($key, 'site_content:');
            $page = SiteContentItem::query()
                ->whereKey($id)
                ->where('type', 'company')
                ->where('status', 'published')
                ->first();
            if ($page && $page->slug !== 'about-us' && $this->isUsableNavigationLabel((string) $page->title)) {
                return ['key' => $key, 'type' => 'site_content', 'label' => (string) $page->title,
                    'url' => route('company.page', ['slug' => $page->slug]), 'route_name' => 'company.page', 'area' => $area, 'permission' => null,
                    'meta' => ['site_content_id' => $page->id, 'slug' => $page->slug, 'type' => $page->type]];
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
        $name = $route->getName(); $uri = ltrim($route->uri(), '/');
        if (! $name || ! in_array($route->methods()[0] ?? null, ['GET', 'HEAD'], true)) return false;
        if ($this->isNavigationBuilderRoute($name)) return false;
        if (str_contains($uri, '{') || in_array($name, self::EXCLUDED_ROUTE_NAMES, true)) return false;
        if ($uri === 'resources' || Str::startsWith($uri, 'resources/')) return false;
        $middleware = collect($route->gatherMiddleware())->map(fn ($value): string => (string) $value);
        if ($area === 'public') {
            if ($route->getDomain() !== null) return false;
            return ! str_starts_with($uri, 'admin/') && ! $middleware->contains(fn (string $value): bool => $value === 'auth' || Str::startsWith($value, ['role:', 'permission:']));
        }
        if ($area === 'dashboard') return (str_starts_with($uri, 'admin/') || $name === 'dashboard') && ! $middleware->contains(fn (string $value): bool => Str::startsWith($value, 'role:'));
        return false;
    }

    private function hasCanonicalCmsPage(Route $route, string $area): bool
    {
        return $this->canonicalCmsPageForRoute((string) $route->getName(), $area) !== null;
    }

    private function canonicalCmsPageForRoute(string $name, string $area): ?array
    {
        if ($area !== 'public') return null;
        $route = collect(RouteFacade::getRoutes()->getRoutes())->first(fn (Route $route): bool => $route->getName() === $name);
        if (! $route || ! $this->eligibleRoute($route, $area)) return null;
        $slug = ltrim($route->uri(), '/');
        if ($slug === '' || str_contains($slug, '/') || str_contains($slug, '{')) return null;
        $page = CmsPage::query()->where('slug', $slug)->where('is_published', true)->first();
        if (! $page && $name === 'site.about') $page = CmsPage::query()->where('slug', 'about-us')->where('is_published', true)->first();
        if (! $page || ! $this->isUsableNavigationLabel((string) $page->title)) return null;
        return ['key' => 'cms_page:'.$page->id, 'type' => 'cms_page', 'label' => (string) $page->title,
            'url' => route('cms.page', ['slug' => $page->slug]), 'route_name' => 'cms.page', 'area' => 'public', 'permission' => null,
            'meta' => ['cms_page_id' => $page->id, 'slug' => $page->slug]];
    }

    private function isNavigationBuilderRoute(string $name): bool
    {
        return Str::startsWith($name, ['admin.navigation.', 'admin.menu-builder.', 'admin.profile-builder.', 'admin.page-builder.']);
    }

    private function routeSource(Route $route, string $area): array
    {
        $name = (string) $route->getName();
        $permission = collect($route->gatherMiddleware())->map(fn ($middleware): string => (string) $middleware)->first(fn (string $middleware): bool => Str::startsWith($middleware, 'permission:'));
        return ['key' => 'route:'.$name, 'type' => 'route', 'label' => $this->routeLabel($route, $name),
            'url' => $route->uri() === '/' ? '/' : '/'.ltrim($route->uri(), '/'), 'route_name' => $name, 'area' => $area,
            'permission' => $permission ? Str::after($permission, 'permission:') : null, 'meta' => []];
    }

    private function routeLabel(Route $route, string $name): string
    {
        $friendly = [
            'home' => 'Home',
            'site.plants' => (string) config('fuelfree.projects.label', 'Projects & Our Plans'),
            'site.future-project' => 'Future Project', 'site.solutions' => 'Solutions', 'site.gallery' => 'Gallery',
            'site.career' => 'Career', 'news.index' => 'News & Notices', 'sustainability' => 'Sustainability', 'contact' => 'Contact',
        ];
        if (array_key_exists($name, $friendly)) return $friendly[$name];
        $action = (string) ($route->getActionName() ?? ''); $controller = Str::before(Str::afterLast($action, '\\'), '@');
        if ($controller === 'PublicSiteController') {
            $section = $route->defaults['section'] ?? null;
            if (is_string($section) && trim($section) !== '') return $this->humanizeNavigationLabel($section);
            $segment = trim(ltrim($route->uri(), '/')); if ($segment !== '' && ! str_contains($segment, '/')) return $this->humanizeNavigationLabel($segment);
        }
        $label = Str::headline(Str::replace(['admin.', '.index', '.'], ['admin ', '', ' '], $name));
        if ($controller && $controller !== 'Closure') { $method = Str::headline(Str::beforeLast($controller, 'Controller')); if ($method && $method !== 'Closure') $label = $method; }
        return $label;
    }

    private function humanizeNavigationLabel(string $value): string { return Str::headline(str_replace(['-', '_'], ' ', trim($value))); }

    private function isUsableNavigationLabel(string $label): bool
    {
        $normalized = trim($label); if ($normalized === '') return false;
        return ! Str::startsWith(Str::lower($normalized), 'generated::');
    }
}
