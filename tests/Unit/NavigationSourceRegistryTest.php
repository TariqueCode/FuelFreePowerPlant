<?php

namespace Tests\Unit;

use App\Models\NavigationMenuItem;
use App\Services\NavigationSourceRegistry;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Route;
use Tests\TestCase;

class NavigationSourceRegistryTest extends TestCase
{
    use RefreshDatabase;
    public function test_public_registry_contains_only_real_get_destinations(): void
    {
        Route::get('/__navigation-test', fn () => 'ok')->name('navigation.test');
        Route::post('/__navigation-test-action', fn () => 'ok')->name('navigation.test.store');

        $sources = app(NavigationSourceRegistry::class)->available('public', 'main');

        $this->assertTrue($sources->contains('key', 'route:navigation.test'));
        $this->assertFalse($sources->contains('key', 'route:navigation.test.store'));

        Route::get('/__protected-navigation-test', fn () => 'ok')->middleware('auth')->name('navigation.protected');
        $this->assertNull(app(NavigationSourceRegistry::class)->resolveAny('route:navigation.protected', 'public'));
        $this->assertSame('Navigation Test', $sources->firstWhere('key', 'route:navigation.test')['label']);
    }

    public function test_dashboard_registry_keeps_route_permission_metadata(): void
    {
        Route::middleware('permission:documents.view')
            ->get('/admin/__navigation-permission-test', fn () => 'ok')
            ->name('admin.documents.index');

        $source = app(NavigationSourceRegistry::class)
            ->available('dashboard', 'dashboard')
            ->firstWhere('key', 'route:admin.documents.index');

        $this->assertNotNull($source);
        $this->assertSame('documents.view', $source['permission']);
    }

    public function test_navigation_builder_routes_are_never_available(): void
    {
        Route::get('/admin/navigation/internal', fn () => 'ok')->name('admin.navigation.internal');

        $sources = app(NavigationSourceRegistry::class)->available('dashboard', 'dashboard');

        $this->assertFalse($sources->contains('key', 'route:admin.navigation.internal'));
    }
    public function test_legacy_resources_are_never_available_as_navigation_sources(): void
    {
        $user = User::factory()->create();
        $this->actingAs($user);

        $routes = app(\Illuminate\Routing\Router::class)->getRoutes();

        $resourceRoutes = collect([
            'resources.index',
            'resources.show',
            'resources.download',
        ]);

        foreach ($resourceRoutes as $name) {
            $this->assertNull(app(NavigationSourceRegistry::class)->resolveAny('route:'.$name, 'public'));
        }

        $this->assertTrue(
            app(NavigationSourceRegistry::class)->available('public', 'main')
                ->every(fn (array $source): bool =>
                    ! str_starts_with((string) ($source['url'] ?? ''), '/resources')
                    && ! str_starts_with((string) ($source['route_name'] ?? ''), 'resources.')
                )
        );
    }

}
