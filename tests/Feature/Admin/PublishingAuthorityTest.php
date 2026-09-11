<?php

namespace Tests\Feature\Admin;

use App\Models\Permission;
use App\Models\Role;
use App\Models\User;
use App\Models\CmsPage;
use App\Models\ManagementProfileFolder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class PublishingAuthorityTest extends TestCase
{
    use RefreshDatabase;

    public function test_administrator_has_publish_authority_but_project_manager_does_not(): void
    {
        $publish = Permission::firstOrCreate(['slug' => 'website.publish'], ['name' => 'Publish website content']);
        $websiteManage = Permission::firstOrCreate(['slug' => 'website.manage'], ['name' => 'Manage website sections']);

        $administrator = Role::create(['name' => 'Administrator', 'slug' => 'administrator', 'is_system' => true]);
        $administrator->permissions()->sync([$publish->id, $websiteManage->id]);

        $projectManager = Role::create(['name' => 'Project Manager', 'slug' => 'project-manager', 'is_system' => true]);
        $projectManager->permissions()->sync([$websiteManage->id]);

        $admin = User::factory()->create();
        $admin->roles()->attach($administrator);

        $manager = User::factory()->create();
        $manager->roles()->attach($projectManager);

        $this->assertTrue($admin->hasPermission('website.publish'));
        $this->assertFalse($manager->hasPermission('website.publish'));
    }

    public function test_cms_publishing_is_a_distinct_permission(): void
    {
        $manage = Permission::firstOrCreate(['slug' => 'cms.manage'], ['name' => 'Manage CMS']);
        $publish = Permission::firstOrCreate(['slug' => 'cms.publish'], ['name' => 'Publish CMS pages']);

        $role = Role::create(['name' => 'Content Editor', 'slug' => 'content-editor', 'is_system' => false]);
        $role->permissions()->sync([$manage->id]);

        $user = User::factory()->create();
        $user->roles()->attach($role);

        $this->assertTrue($user->hasPermission('cms.manage'));
        $this->assertFalse($user->hasPermission('cms.publish'));
    }

    public function test_management_profile_publishing_requires_publish_permission(): void
    {
        $manage = Permission::firstOrCreate(['slug' => 'website.manage'], ['name' => 'Manage website']);
        $publish = Permission::firstOrCreate(['slug' => 'website.publish'], ['name' => 'Publish website']);

        $role = Role::create(['name' => 'Website Manager', 'slug' => 'website-manager-publish-test', 'is_system' => false]);
        $role->permissions()->sync([$manage->id]);
        $user = User::factory()->create();
        $user->roles()->attach($role);
        $folder = ManagementProfileFolder::create(['name' => 'QA Folder', 'slug' => 'qa-folder', 'status' => 'published', 'sort_order' => 1]);

        $this->actingAs($user)->post(route('admin.profile-builder.store'), [
            'management_profile_folder_id' => $folder->id,
            'title' => 'QA Manager', 'designation' => 'Director', 'phone' => '01700000000',
            'status' => 'published',
        ])->assertForbidden();

        $this->assertDatabaseMissing('site_content_items', ['title' => 'QA Manager']);
    }

    public function test_gallery_publishing_requires_publish_permission(): void
    {
        $manage = Permission::firstOrCreate(['slug' => 'website.manage'], ['name' => 'Manage website']);
        $role = Role::create(['name' => 'Gallery Manager', 'slug' => 'gallery-manager-publish-test', 'is_system' => false]);
        $role->permissions()->sync([$manage->id]);
        $user = User::factory()->create();
        $user->roles()->attach($role);

        $this->actingAs($user)->post(route('admin.gallery.store'), [
            'title' => 'QA Gallery', 'status' => 'published',
        ])->assertForbidden();
    }

    public function test_slider_publishing_requires_publish_permission(): void
    {
        $manage = Permission::firstOrCreate(['slug' => 'website.manage'], ['name' => 'Manage website']);
        $role = Role::create(['name' => 'Slider Manager', 'slug' => 'slider-manager-publish-test', 'is_system' => false]);
        $role->permissions()->sync([$manage->id]);
        $user = User::factory()->create();
        $user->roles()->attach($role);

        $this->actingAs($user)->post(route('admin.sliders.store'), [
            'title' => 'QA Slider', 'image' => UploadedFile::fake()->image('slider.jpg'), 'status' => 'published',
        ])->assertForbidden();
    }

    public function test_highlight_publishing_requires_publish_permission(): void
    {
        $manage = Permission::firstOrCreate(['slug' => 'website.manage'], ['name' => 'Manage website']);
        $role = Role::create(['name' => 'Homepage Manager', 'slug' => 'homepage-manager-publish-test', 'is_system' => false]);
        $role->permissions()->sync([$manage->id]);
        $user = User::factory()->create();
        $user->roles()->attach($role);

        $this->actingAs($user)->post(route('admin.homepage-builder.update'), [
            'highlight_enabled' => '1', 'highlight_status' => 'published',
        ])->assertForbidden();
    }

    public function test_cms_page_activation_and_deactivation_requires_publish_permission(): void
    {
        $manage = Permission::firstOrCreate(['slug' => 'cms.manage'], ['name' => 'Manage CMS']);
        $publish = Permission::firstOrCreate(['slug' => 'cms.publish'], ['name' => 'Publish CMS pages']);

        $managerRole = Role::create(['name' => 'CMS Manager', 'slug' => 'cms-manager-publish-test', 'is_system' => false]);
        $managerRole->permissions()->sync([$manage->id]);
        $manager = User::factory()->create();
        $manager->roles()->attach($managerRole);

        $publisherRole = Role::create(['name' => 'CMS Publisher', 'slug' => 'cms-publisher-publish-test', 'is_system' => false]);
        $publisherRole->permissions()->sync([$manage->id, $publish->id]);
        $publisher = User::factory()->create();
        $publisher->roles()->attach($publisherRole);

        $page = CmsPage::create([
            'title' => 'Publishing QA', 'slug' => 'publishing-qa', 'content' => '<p>QA</p>', 'is_published' => false,
        ]);

        $this->actingAs($manager)->patch(route('admin.cms.toggle', $page))->assertForbidden();
        $this->assertFalse($page->fresh()->is_published);

        $this->actingAs($publisher)->patch(route('admin.cms.toggle', $page))->assertRedirect();
        $this->assertTrue($page->fresh()->is_published);
    }
}
