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

    public function available(string $area = 'public', string $menu = 'main'): Collection
    {
        abort_unless(in_array($menu, ['main', 'dashboard'], true), 404);
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
        if (! in_array($area, ['public', 'dashboard'], true)) return null;
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

        $middleware = collect($route->gatherMiddleware())->map(fn ($value): string => (string) $value);

        if ($area === 'public') {
            return ! str_starts_with($uri, 'admin/')
                && ! $middleware->contains(fn (string $value): bool =>
                    $value === 'auth' || Str::startsWith($value, ['role:', 'permission:']));
        }

        if ($area === 'dashboard') {
            return (str_starts_with($uri, 'admin/') || $name === 'dashboard')
                && ! $middleware->contains(fn (string $value): bool => Str::startsWith($value, 'role:'));
        }

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
            ->map(fn ($middleware): string => (string) $middleware)
            ->first(fn (string $middleware): bool => Str::startsWith($middleware, 'permission:'));

        return [
            'key' => 'route:'.$name, 'type' => 'route',
            'label' => $this->routeLabel($route, $name),
            'url' => route($name), 'route_name' => $name, 'area' => $area,
            'permission' => $permission ? Str::after($permission, 'permission:') : null,
            'meta' => [],
        ];
    }

    private function routeLabel(Route $route, string $name): string
    {
        $action = (string) ($route->getActionName() ?? '');
        $controller = Str::afterLast($action, '\\');
        $controller = Str::before($controller, '@');

        $label = Str::headline(Str::replace(['admin.', '.index', '.'], ['admin ', '', ' '], $name));
        if ($controller && $controller !== 'Closure') {
            $method = Str::beforeLast($controller, 'Controller');
            $method = Str::headline($method);
            if ($method && $method !== 'Closure') $label = $method;
        }

        return $label;
    }
}
