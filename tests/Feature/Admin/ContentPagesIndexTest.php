<?php

namespace Tests\Feature\Admin;

use App\Models\CmsPage;
use App\Models\Permission;
use App\Models\Role;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ContentPagesIndexTest extends TestCase
{
    use RefreshDatabase;

    private function adminUser(): User
    {
        $permissions = collect([
            ['cms.view', 'View CMS pages'],
            ['cms.manage', 'Manage CMS pages'],
            ['cms.publish', 'Publish CMS pages'],
            ['website.view', 'View website content'],
            ['website.manage', 'Manage website content'],
            ['website.publish', 'Publish website content'],
        ])->map(fn (array $permission) => Permission::firstOrCreate(
            ['slug' => $permission[0]],
            ['name' => $permission[1]],
        ));

        $role = Role::create([
            'name' => 'Content Pages QA',
            'slug' => 'content-pages-qa',
            'is_system' => false,
        ]);
        $role->permissions()->sync($permissions->pluck('id'));

        $user = User::factory()->create();
        $user->roles()->attach($role);

        return $user;
    }

    public function test_page_builder_is_the_sole_authoritative_content_page_surface(): void
    {
        $user = $this->adminUser();

        $futureProjects = CmsPage::query()->updateOrCreate(
            ['slug' => 'future-projects'],
            [
                'title' => 'Future Projects',
                'content' => '<p>Future projects content.</p>',
                'is_published' => true,
            ],
        );

        $aboutUs = CmsPage::query()->updateOrCreate(
            ['slug' => 'about-us'],
            [
                'title' => 'About Us',
                'content' => '<p>About us content.</p>',
                'is_published' => true,
            ],
        );

        $response = $this->actingAs($user)->get(route('admin.page-builder.index'));

        $response->assertOk()
            ->assertSee('Future Projects')
            ->assertSee('About Us')
            ->assertSee(route('admin.page-builder.edit', $futureProjects), false)
            ->assertSee(route('admin.page-builder.edit', $aboutUs), false)
            ->assertSee(route('admin.page-builder.toggle', $futureProjects), false)
            ->assertSee(route('admin.page-builder.toggle', $aboutUs), false)
            ->assertSee(route('admin.page-builder.destroy', $futureProjects), false)
            ->assertSee(route('admin.page-builder.destroy', $aboutUs), false)
            ->assertSee(route('admin.page-builder.duplicate', $futureProjects), false)
            ->assertDontSee('/admin/site-content/', false)
            ->assertDontSee('Site Content', false);
    }

    public function test_legacy_site_content_surface_is_retired(): void
    {
        $user = $this->adminUser();

        $this->actingAs($user)
            ->get('/admin/site-content')
            ->assertNotFound();

        $this->actingAs($user)
            ->get('/admin/site-content?type=resource')
            ->assertNotFound();
    }
}
