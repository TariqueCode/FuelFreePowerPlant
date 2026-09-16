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
        'news.show', 'projects.show', 'documents.shared-download', 'webmail.redirect',
        'resources.index', 'resources.show', 'resources.download',
        'site.plants', 'site.future-project', 'site.solutions', 'sustainability',
    ];

    private const BUILDER_ROUTE_ALIASES = [
        'admin.profile-builder.index' => ['admin.profile-builder.index', 'Profile Builder'],
        'admin.cms.index' => ['admin.cms.index', 'Page Builder'],
        'admin.navigation.index' => ['admin.menu-builder.index', 'Menu Builder'],
        'admin.site-content.index' => ['admin.news_and_event', 'News & Event'],
    ];

    /** Request-local indexes prevent repeated full route scans during admin navigation rendering. */
    private array $routeIndex = [];
    private array $canonicalCmsCache = [];
    private ?bool $aboutPagePublished = null;

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

        return $routes->concat($cms)
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
                $route = $this->routeByName($canonical);
                if ($route && $this->eligibleRoute($route, $area)) {
                    $source = $this->routeSource($route, $area); $source['label'] = $label; return $source;
                }
                return null;
            }
            $canonical = $this->canonicalCmsPageForRoute($name, $area);
            if ($canonical) return $canonical;
            $route = $this->routeByName($name);
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
        return null;
    }

    private function usedSourceKeys(string $menu): Collection { return NavigationMenuItem::query()->where('menu', $menu)->whereNotNull('source_key')->pluck('source_key'); }

    private function routeByName(string $name): ?Route
    {
        if (! array_key_exists($name, $this->routeIndex)) {
            if ($this->routeIndex === []) {
                $this->routeIndex = collect(RouteFacade::getRoutes()->getRoutes())
                    ->filter(fn (Route $route): bool => is_string($route->getName()) && $route->getName() !== '')
                    ->keyBy(fn (Route $route): string => (string) $route->getName())
                    ->all();
            }
        }

        return $this->routeIndex[$name] ?? null;
    }

    private function eligibleRoute(Route $route, string $area): bool
    {
        $name = $route->getName(); $uri = ltrim($route->uri(), '/');
        if (! $name || ! in_array($route->methods()[0] ?? null, ['GET', 'HEAD'], true)) return false;
        if ($this->isNavigationBuilderRoute($name)) return false;
        if (str_contains($uri, '{') || in_array($name, self::EXCLUDED_ROUTE_NAMES, true)) return false;
        if ($uri === 'resources' || Str::startsWith($uri, 'resources/')) return false;
        if (Str::startsWith($uri, 'admin/site-content')) return false;
        $middleware = collect($route->gatherMiddleware())->map(fn ($value): string => (string) $value);
        if ($area === 'public') {
            if ($name === 'site.about') return $this->aboutPageIsPublished();
            if ($route->getDomain() !== null) return false;
            return ! str_starts_with($uri, 'admin/') && ! $middleware->contains(fn (string $value): bool => $value === 'auth' || Str::startsWith($value, ['role:', 'permission:']));
        }
        if ($area === 'dashboard') {
            if ($name === 'admin.dashboard') return $uri === 'admin' || str_starts_with($uri, 'admin/');
            return (str_starts_with($uri, 'admin/') || $name === 'dashboard') && ! $middleware->contains(fn (string $value): bool => Str::startsWith($value, 'role:'));
        }
        return false;
    }

    private function aboutPageIsPublished(): bool
    {
        return $this->aboutPagePublished ??= CmsPage::query()->where('slug', 'about-us')->where('is_published', true)->exists();
    }

    private function hasCanonicalCmsPage(Route $route, string $area): bool { return $this->canonicalCmsPageForRoute((string) $route->getName(), $area) !== null; }

    private function canonicalCmsPageForRoute(string $name, string $area): ?array
    {
        if ($area !== 'public') return null;
        if (array_key_exists($name, $this->canonicalCmsCache)) return $this->canonicalCmsCache[$name];

        $route = $this->routeByName($name);
        if (! $route || ! $this->eligibleRoute($route, $area)) return $this->canonicalCmsCache[$name] = null;
        $slug = ltrim($route->uri(), '/');
        if ($slug === '' || str_contains($slug, '/') || str_contains($slug, '{')) return $this->canonicalCmsCache[$name] = null;
        $page = CmsPage::query()->where('slug', $slug)->where('is_published', true)->first();
        if (! $page && $name === 'site.about') $page = CmsPage::query()->where('slug', 'about-us')->where('is_published', true)->first();
        if (! $page || ! $this->isUsableNavigationLabel((string) $page->title)) return $this->canonicalCmsCache[$name] = null;
        return $this->canonicalCmsCache[$name] = ['key' => 'cms_page:'.$page->id, 'type' => 'cms_page', 'label' => (string) $page->title,
            'url' => route('cms.page', ['slug' => $page->slug]), 'route_name' => 'cms.page', 'area' => 'public', 'permission' => null,
            'meta' => ['cms_page_id' => $page->id, 'slug' => $page->slug]];
    }

    private function isNavigationBuilderRoute(string $name): bool { return Str::startsWith($name, ['admin.navigation.', 'admin.menu-builder.', 'admin.page-builder.']); }

    private function routeSource(Route $route, string $area): array
    {
        $name = (string) $route->getName(); $middleware = collect($route->gatherMiddleware())->map(fn ($value): string => (string) $value);
        $permission = $middleware->first(fn (string $middleware): bool => Str::startsWith($middleware, 'permission:'));
        return ['key' => 'route:'.$name, 'type' => 'route', 'label' => $this->routeLabel($route, $name), 'url' => $route->uri() === '/' ? '/' : '/'.ltrim($route->uri(), '/'), 'route_name' => $name, 'area' => $area, 'permission' => $permission ? Str::after($permission, 'permission:') : null, 'meta' => []];
    }

    private function routeLabel(Route $route, string $name): string
    {
        $friendly = ['home' => 'Home', 'management' => 'Board of Directors', 'site.plants' => (string) config('fuelfree.projects.label', 'Projects & Our Plans'), 'site.future-project' => 'Future Project', 'site.solutions' => 'Solutions', 'site.gallery' => 'Gallery', 'site.career' => 'Career', 'news.index' => 'News & Event', 'sustainability' => 'Sustainability', 'contact' => 'Contact'];
        if (array_key_exists($name, $friendly)) return $friendly[$name];
        if ($name === 'admin.profile-builder.index') return 'Profile Builder';
        if ($name === 'admin.cms.index') return 'Page Builder';
        if ($name === 'admin.header-footer.index') return 'Header & Footer';
        $action = (string) ($route->getActionName() ?? ''); $controller = Str::before(Str::afterLast($action, '\\'), '@');
        if ($controller === 'PublicSiteController') { $section = $route->defaults['section'] ?? null; if (is_string($section) && trim($section) !== '') return $this->humanizeNavigationLabel($section); $segment = trim(ltrim($route->uri(), '/')); if ($segment !== '' && ! str_contains($segment, '/')) return $this->humanizeNavigationLabel($segment); }
        $label = Str::headline(Str::replace(['admin.', '.index', '.'], ['admin ', '', ' '], $name));
        if ($controller && $controller !== 'Closure') { $method = Str::headline(Str::beforeLast($controller, 'Controller')); if ($method && $method !== 'Closure') $label = $method; }
        return $label;
    }

    private function humanizeNavigationLabel(string $value): string { return Str::headline(str_replace(['-', '_'], ' ', trim($value))); }
    private function isUsableNavigationLabel(string $label): bool { $normalized = trim($label); if ($normalized === '') return false; return ! Str::startsWith(Str::lower($normalized), 'generated::'); }
}
