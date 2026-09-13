<?php

namespace Tests\Feature\Admin;

use App\Models\Permission;
use App\Models\Role;
use App\Models\User;
use App\Services\DashboardNavigationService;
use App\Services\NavigationSourceRegistry;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Route;
use Tests\TestCase;

class NewsAndEventNavigationTest extends TestCase
{
    use RefreshDatabase;

    private function adminWithWebsiteView(): User
    {
        $permission = Permission::query()->firstOrCreate(
            ['slug' => 'website.view'],
            ['name' => 'Website View', 'description' => 'View website administration surfaces.']
        );
        $role = Role::query()->firstOrCreate(
            ['slug' => 'news-event-navigation-test'],
            ['name' => 'News Event Navigation Test', 'description' => 'Test navigation role.', 'is_system' => false]
        );
        $role->permissions()->syncWithoutDetaching([$permission->id]);

        $user = User::factory()->create();
        $user->roles()->attach($role);
        return $user;
    }

    public function test_news_and_event_compatibility_routes_are_registered(): void
    {
        $canonical = Route::getRoutes()->getByName('admin.news_and_event');
        $legacy = Route::getRoutes()->getByName('admin.site-content.index');
        $broken = Route::getRoutes()->getByName('admin.news-and-event.compat');

        $this->assertNotNull($canonical);
        $this->assertSame('admin/news_and_Event', $canonical->uri());
        $this->assertNotNull($legacy);
        $this->assertSame('admin/site-content', $legacy->uri());
        $this->assertNotNull($broken);
        $this->assertSame('admin/news-and-Event', $broken->uri());
    }

    public function test_navigation_registry_resolves_legacy_news_source_to_canonical_entry(): void
    {
        $this->actingAs($this->adminWithWebsiteView());

        $source = app(NavigationSourceRegistry::class)->resolveAny('route:admin.site-content.index', 'dashboard');

        $this->assertNotNull($source);
        $this->assertSame('News & Event', $source['label']);
        $this->assertSame('/admin/news_and_Event', $source['url']);
        $this->assertSame('admin.news_and_event', $source['route_name']);
    }

    public function test_dashboard_navigation_exposes_news_and_event_with_canonical_url(): void
    {
        $this->actingAs($this->adminWithWebsiteView());

        $tree = app(DashboardNavigationService::class)->tree('dashboard');
        $website = $tree->firstWhere('label', 'Website');

        $this->assertNotNull($website);
        $news = $website->children->firstWhere('label', 'News & Event');
        $this->assertNotNull($news);
        $this->assertSame('/admin/news_and_Event', $news->url);
        $this->assertSame('admin.news_and_event', $news->route_name);
    }
}
