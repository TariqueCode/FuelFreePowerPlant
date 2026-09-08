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

    private function websiteViewer(): User
    {
        $permission = Permission::firstOrCreate(['slug' => 'website.view'], ['name' => 'View website']);
        $role = Role::create(['name' => 'Website Viewer', 'slug' => 'website-viewer-retired-cms', 'is_system' => false]);
        $role->permissions()->attach($permission);

        $user = User::factory()->create();
        $user->roles()->attach($role);
        return $user;
    }

    public function test_retired_site_content_admin_page_is_not_available(): void
    {
        $this->actingAs($this->websiteViewer())
            ->get('/admin/site-content?type=news')
            ->assertNotFound();
    }

    public function test_retired_site_content_news_mutation_is_not_available(): void
    {
        $this->actingAs($this->websiteViewer())
            ->patch('/admin/site-content/news/1/toggle')
            ->assertNotFound();
    }
}
