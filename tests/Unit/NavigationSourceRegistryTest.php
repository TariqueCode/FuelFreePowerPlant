<?php

namespace Tests\Unit;

use App\Models\NavigationMenuItem;
use App\Models\User;
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


    public function test_published_about_page_builder_page_is_used_instead_of_generic_public_site_route(): void
    {
        \App\Models\CmsPage::create([
            'title' => 'About Us',
            'slug' => 'about-us',
            'content' => '<p>Builder content</p>',
            'is_published' => true,
        ]);

        $registry = app(NavigationSourceRegistry::class);

        $sources = $registry->available('public', 'main');

        $this->assertFalse($sources->contains('key', 'route:site.about'));
        $page = $sources->first(fn (array $source) => str_starts_with((string) ($source['key'] ?? ''), 'cms_page:'));
        $this->assertNotNull($page);
        $this->assertSame('About Us', $page['label']);
        $this->assertSame(route('cms.page', ['slug' => 'about-us']), $page['url']);
        $this->assertSame('cms.page', $page['route_name']);

        $resolved = $registry->resolveAny('route:site.about', 'public');
        $this->assertNotNull($resolved);
        $this->assertSame('cms_page', $resolved['type']);
        $this->assertSame('About Us', $resolved['label']);
        $this->assertSame(route('cms.page', ['slug' => 'about-us']), $resolved['url']);
    }


    public function test_any_static_public_route_matching_a_published_page_builder_slug_becomes_cms_source(): void
    {
        Route::get('/our-technology', fn () => 'legacy')->name('site.technology');

        \App\Models\CmsPage::create([
            'title' => 'Our Technology',
            'slug' => 'our-technology',
            'content' => '<p>Builder content</p>',
            'is_published' => true,
        ]);

        $registry = app(NavigationSourceRegistry::class);

        $this->assertFalse(
            $registry->available('public', 'main')->contains('key', 'route:site.technology')
        );

        $source = $registry->resolveAny('route:site.technology', 'public');
        $this->assertNotNull($source);
        $this->assertSame('cms_page', $source['type']);
        $this->assertSame('Our Technology', $source['label']);
        $this->assertSame(route('cms.page', ['slug' => 'our-technology']), $source['url']);
    }



    public function test_shared_public_site_controller_uses_destination_labels(): void
    {
        $registry = app(NavigationSourceRegistry::class);

        $solutions = $registry->resolveAny('route:site.solutions', 'public');
        $gallery = $registry->resolveAny('route:site.gallery', 'public');

        $this->assertNotNull($solutions);
        $this->assertSame('Solutions', $solutions['label']);
        $this->assertNotNull($gallery);
        $this->assertSame('Gallery', $gallery['label']);
    }

    public function test_domain_scoped_webmail_routes_are_not_public_navigation_sources(): void
    {
        $registry = app(NavigationSourceRegistry::class);

        $this->assertNull($registry->resolveAny('route:webmail.host.login', 'public'));
        $this->assertNull($registry->resolveAny('route:webmail.host.inbox', 'public'));
    }

}
