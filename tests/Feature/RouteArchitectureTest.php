<?php

namespace Tests\Feature;

use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Route;
use Tests\TestCase;

class RouteArchitectureTest extends TestCase
{
    public function test_canonical_builder_routes_are_exposed_and_legacy_builder_routes_are_not(): void
    {
        $this->assertNotNull(Route::getRoutes()->getByName('admin.page-builder.index'));
        $this->assertNotNull(Route::getRoutes()->getByName('admin.profile-builder.index'));
        $this->assertNotNull(Route::getRoutes()->getByName('admin.menu-builder.index'));

        $this->assertNull(Route::getRoutes()->getByName('admin.cms.index'));
        $this->assertNull(Route::getRoutes()->getByName('admin.management.index'));
        $this->assertNull(Route::getRoutes()->getByName('admin.navigation.index'));
    }

    public function test_canonical_menu_builder_does_not_expose_legacy_destroy_route(): void
    {
        $this->assertNotNull(Route::getRoutes()->getByName('admin.menu-builder.destroy'));
        $this->assertNull(Route::getRoutes()->getByName('admin.menu-builder.legacy-destroy'));
    }

    public function test_retired_public_routes_are_not_registered(): void
    {
        $this->assertNull(Route::getRoutes()->getByName('site.plants'));
        $this->assertNull(Route::getRoutes()->getByName('site.future-project'));
        $this->assertNull(Route::getRoutes()->getByName('site.solutions'));
    }

    public function test_retired_admin_uris_are_not_registered(): void
    {
        $retiredPrefixes = ['admin/site-content', 'admin/plants'];
        foreach (Route::getRoutes()->getRoutes() as $route) {
            $uri = trim($route->uri(), '/');
            foreach ($retiredPrefixes as $prefix) {
                $this->assertFalse(
                    $uri === $prefix || str_starts_with($uri, $prefix.'/'),
                    "Retired admin URI [$uri] remains registered for route [{$route->getName()}].",
                );
            }
        }
    }

    public function test_retired_route_names_are_not_referenced_by_admin_views(): void
    {
        $forbidden = [
            "route('admin.cms.",
            'route("admin.cms.',
            "route('admin.management.",
            'route("admin.management.',
            "route('admin.navigation.",
            'route("admin.navigation.',
            "route('admin.site-content.",
            'route("admin.site-content.',
            "route('admin.menu-builder.legacy-destroy'",
        ];

        foreach (File::allFiles(resource_path('views/admin')) as $file) {
            $contents = $file->getContents();
            foreach ($forbidden as $needle) {
                $this->assertStringNotContainsString(
                    $needle,
                    $contents,
                    "Retired route reference [$needle] remains in {$file->getRelativePathname()}.",
                );
            }
        }
    }

    public function test_canonical_builder_route_names_are_unique(): void
    {
        $names = collect(Route::getRoutes()->getRoutes())
            ->map(fn ($route) => $route->getName())
            ->filter()
            ->values();

        $this->assertCount($names->unique()->count(), $names, 'Duplicate named routes detected.');
    }
}
