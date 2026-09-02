<?php

namespace Tests\Feature\Admin;

use App\Models\CmsPage;
use App\Models\Permission;
use App\Models\Role;
use App\Models\SiteContentItem;
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
            ['name' => $permission[1]]
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

    public function test_content_pages_uses_card_management_with_only_publish_and_delete_actions(): void
    {
        $user = $this->adminUser();

        $cms = CmsPage::create([
            'title' => 'Future Projects',
            'slug' => 'future-projects',
            'content' => '<p>Future projects content.</p>',
            'is_published' => true,
        ]);

        $company = SiteContentItem::create([
            'type' => 'company',
            'title' => 'About Us',
            'slug' => 'about-us',
            'content' => '<p>About us content.</p>',
            'status' => 'published',
        ]);

        $response = $this->actingAs($user)->get(route('admin.cms.index'));

        $response->assertOk()
            ->assertSee('Future Projects')
            ->assertSee('About Us')
            ->assertSee(route('admin.cms.edit', $cms), false)
            ->assertSee(route('admin.site-content.edit', $company), false)
            ->assertSee(route('admin.cms.toggle', $cms), false)
            ->assertSee(route('admin.site-content.page.toggle', $company), false)
            ->assertSee(route('admin.cms.destroy', $cms), false)
            ->assertSee(route('admin.site-content.destroy', $company), false)
            ->assertDontSee('Duplicate')
            ->assertDontSee('> Edit <');
    }
}
