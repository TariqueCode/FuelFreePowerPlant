<?php

namespace Tests\Unit;

use App\Services\NavigationSourceRegistry;
use Illuminate\Support\Facades\Route;
use Tests\TestCase;

class NavigationSourceRegistryTest extends TestCase
{
    public function test_public_registry_contains_only_real_get_destinations(): void
    {
        Route::get('/__navigation-test', fn () => 'ok')->name('navigation.test');
        Route::post('/__navigation-test-action', fn () => 'ok')->name('navigation.test.store');

        $sources = app(NavigationSourceRegistry::class)->available('public', 'test-menu');

        $this->assertTrue($sources->contains('key', 'route:navigation.test'));
        $this->assertFalse($sources->contains('key', 'route:navigation.test.store'));
        $this->assertSame('Navigation Test', $sources->firstWhere('key', 'route:navigation.test')['label']);
    }

    public function test_dashboard_registry_keeps_route_permission_metadata(): void
    {
        Route::middleware('permission:documents.view')
            ->get('/admin/__navigation-permission-test', fn () => 'ok')
            ->name('admin.documents.index');

        $source = app(NavigationSourceRegistry::class)
            ->available('dashboard', 'test-dashboard-menu')
            ->firstWhere('key', 'route:admin.documents.index');

        $this->assertNotNull($source);
        $this->assertSame('documents.view', $source['permission']);
    }
}
