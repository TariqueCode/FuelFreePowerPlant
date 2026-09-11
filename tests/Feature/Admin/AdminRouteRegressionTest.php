<?php

namespace Tests\Feature\Admin;

use App\Models\Permission;
use App\Models\Role;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AdminRouteRegressionTest extends TestCase
{
    use RefreshDatabase;

    private function admin(array $permissions): User
    {
        $permissionIds = collect($permissions)->map(function (string $slug): int {
            return Permission::firstOrCreate(['slug' => $slug], ['name' => $slug])->id;
        });

        $role = Role::create([
            'name' => 'Admin Route Regression',
            'slug' => 'admin-route-regression',
            'is_system' => false,
        ]);
        $role->permissions()->sync($permissionIds);

        $user = User::factory()->create();
        $user->roles()->attach($role);
        return $user;
    }

    public function test_news_event_create_page_renders_with_canonical_index_link(): void
    {
        $user = $this->admin(['website.view', 'website.manage']);

        $response = $this->actingAs($user)->get(route('admin.news_and_event.create'));

        $response->assertOk();
        $response->assertSee('Create News / Event');
        $response->assertSee(route('admin.news_and_event.index'), false);
        $response->assertDontSee("route('admin.news_and_event')", false);
    }

    public function test_legacy_news_event_url_redirects_to_the_canonical_path(): void
    {
        $user = $this->admin(['website.view']);

        $response = $this->actingAs($user)->get('/admin/news_and_Event');

        $response->assertRedirect('/admin/news-and-Event');
        $response->assertStatus(301);
    }

    public function test_news_event_store_redirects_to_the_canonical_index_route(): void
    {
        $user = $this->admin(['website.view', 'website.manage']);

        $response = $this->actingAs($user)->post(route('admin.news_and_event.store'), [
            'type' => 'news',
            'title' => 'Route Regression News',
            'slug' => 'route-regression-news',
            'excerpt' => 'Regression test.',
            'content' => '<p>Test content.</p>',
            'status' => 'draft',
            'is_featured' => '0',
        ]);

        $response->assertRedirect(route('admin.news_and_event.index'));
        $response->assertSessionHas('status');
    }

    public function test_menu_reorder_endpoint_is_reachable_instead_of_dynamic_item_route(): void
    {
        $user = $this->admin(['website.view', 'navigation.manage']);

        $response = $this->actingAs($user)->post(route('admin.menu-builder.reorder'), [
            'menu' => 'main',
            'tree' => [],
        ]);

        $response->assertOk()->assertJson(['ok' => true]);
    }
}
