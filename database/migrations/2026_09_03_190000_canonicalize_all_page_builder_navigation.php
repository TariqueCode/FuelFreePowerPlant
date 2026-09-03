<?php

use App\Models\CmsPage;
use App\Models\NavigationMenuItem;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (! Schema::hasTable('navigation_menu_items') || ! Schema::hasTable('cms_pages')) {
            return;
        }

        $pages = CmsPage::query()->where('is_published', true)->get(['id', 'title', 'slug']);

        NavigationMenuItem::query()
            ->where('menu', 'main')
            ->where(function ($query): void {
                $query->whereNull('source_key')->orWhere('source_key', '');
            })
            ->get()
            ->each(function (NavigationMenuItem $item) use ($pages): void {
                $route = $item->route_name
                    ? collect(Route::getRoutes()->getRoutes())->first(fn ($route) => $route->getName() === $item->route_name)
                    : null;

                $routeSlug = $route ? ltrim($route->uri(), '/') : null;
                $page = $routeSlug && ! str_contains($routeSlug, '/')
                    ? $pages->firstWhere('slug', $routeSlug)
                    : null;

                if (! $page && $item->route_name === 'site.about') {
                    $page = $pages->firstWhere('slug', 'about-us');
                }

                if (! $page) {
                    return;
                }

                $item->update([
                    'source_key' => 'cms_page:'.$page->id,
                    'source_type' => 'cms_page',
                    'area' => 'public',
                    'permission_key' => null,
                    'route_name' => 'cms.page',
                    'url' => route('cms.page', ['slug' => $page->slug]),
                    'label' => $page->title,
                ]);
            });
    }

    public function down(): void
    {
        // Canonical CMS navigation remains authoritative after deployment.
    }
};
