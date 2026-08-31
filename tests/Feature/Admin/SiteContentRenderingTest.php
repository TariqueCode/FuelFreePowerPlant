<?php

namespace Tests\Feature\Admin;

use App\Models\Permission;
use App\Models\Role;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class SiteContentRenderingTest extends TestCase
{
    use RefreshDatabase;

    public function test_news_and_notices_admin_page_renders_without_server_error(): void
    {
        $permission = Permission::firstOrCreate(['slug' => 'website.view'], ['name' => 'View website']);
        $role = Role::create(['name' => 'Website Viewer', 'slug' => 'website-viewer', 'is_system' => false]);
        $role->permissions()->attach($permission);

        $user = User::factory()->create();
        $user->roles()->attach($role);

        $this->actingAs($user)
            ->get(route('admin.site-content.index', ['type' => 'news']))
            ->assertOk()
            ->assertSee('News &amp; Notices CMS', false)
            ->assertSee('News &amp; Notices');
    }

    public function test_portal_uses_one_news_and_notices_navigation_entry(): void
    {
        $permission = Permission::firstOrCreate(['slug' => 'website.view'], ['name' => 'View website']);
        $role = Role::create(['name' => 'Website Viewer Navigation', 'slug' => 'website-viewer-navigation', 'is_system' => false]);
        $role->permissions()->attach($permission);

        $user = User::factory()->create();
        $user->roles()->attach($role);

        $response = $this->actingAs($user)
            ->get(route('admin.site-content.index', ['type' => 'news']));

        $response->assertOk()
            ->assertSee('News &amp; Notices', false)
            ->assertDontSee('>Notices<', false)
            ->assertDontSee('>News &amp; Events<', false);
    }
}
