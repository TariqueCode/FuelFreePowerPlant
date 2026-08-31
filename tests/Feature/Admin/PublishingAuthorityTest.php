<?php

namespace Tests\Feature\Admin;

use App\Models\Permission;
use App\Models\Role;
use App\Models\User;
use App\Models\SiteContentItem;
use App\Models\HomepageSection;
use App\Models\PowerPlant;
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
    public function test_news_publication_toggle_requires_publish_permission_and_works_when_granted(): void
    {
        $manage = Permission::firstOrCreate(['slug' => 'website.manage'], ['name' => 'Manage website sections']);
        $publish = Permission::firstOrCreate(['slug' => 'website.publish'], ['name' => 'Publish website content']);

        $managerRole = Role::create(['name' => 'Content Manager', 'slug' => 'content-manager-toggle', 'is_system' => false]);
        $managerRole->permissions()->sync([$manage->id]);
        $manager = User::factory()->create();
        $manager->roles()->attach($managerRole);

        $publisherRole = Role::create(['name' => 'Publisher', 'slug' => 'publisher-toggle', 'is_system' => false]);
        $publisherRole->permissions()->sync([$manage->id, $publish->id]);
        $publisher = User::factory()->create();
        $publisher->roles()->attach($publisherRole);

        $item = SiteContentItem::create([
            'type' => 'news', 'title' => 'QA News', 'slug' => 'qa-news-toggle',
            'status' => 'draft', 'content' => 'QA',
        ]);

        $this->actingAs($manager)->patch(route('admin.site-content.news.toggle', $item))->assertForbidden();
        $this->assertSame('draft', $item->fresh()->status);

        $this->actingAs($publisher)->patch(route('admin.site-content.news.toggle', $item))->assertRedirect();
        $this->assertSame('published', $item->fresh()->status);
    }

    public function test_management_profile_publishing_requires_publish_permission(): void
    {
        $manage = Permission::firstOrCreate(['slug' => 'website.manage'], ['name' => 'Manage website']);
        $publish = Permission::firstOrCreate(['slug' => 'website.publish'], ['name' => 'Publish website']);

        $role = Role::create(['name' => 'Website Manager', 'slug' => 'website-manager-publish-test', 'is_system' => false]);
        $role->permissions()->sync([$manage->id]);
        $user = User::factory()->create();
        $user->roles()->attach($role);

        $this->actingAs($user)->post(route('admin.management.store'), [
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

        $this->assertDatabaseMissing('site_content_items', ['title' => 'QA Gallery']);
    }

    public function test_slider_publishing_denial_happens_before_file_storage(): void
    {
        Storage::fake('public');

        $manage = Permission::firstOrCreate(['slug' => 'website.manage'], ['name' => 'Manage website']);
        $role = Role::create(['name' => 'Slider Manager', 'slug' => 'slider-manager-publish-test', 'is_system' => false]);
        $role->permissions()->sync([$manage->id]);
        $user = User::factory()->create();
        $user->roles()->attach($role);

        $this->actingAs($user)->post(route('admin.sliders.store'), [
            'title' => 'QA Slider',
            'image' => UploadedFile::fake()->image('qa-slider.jpg'),
            'is_published' => '1',
        ])->assertForbidden();

        $this->assertDatabaseMissing('site_sliders', ['title' => 'QA Slider']);
        Storage::disk('public')->assertDirectoryEmpty('site-sliders');
    }

    public function test_highlight_publishing_denial_happens_before_file_storage(): void
    {
        Storage::fake('public');

        $manage = Permission::firstOrCreate(['slug' => 'website.manage'], ['name' => 'Manage website']);
        $role = Role::create(['name' => 'Highlight Manager', 'slug' => 'highlight-manager-publish-test', 'is_system' => false]);
        $role->permissions()->sync([$manage->id]);
        $user = User::factory()->create();
        $user->roles()->attach($role);

        $this->actingAs($user)->post(route('admin.site-popups.store'), [
            'title' => 'QA Highlight',
            'image' => UploadedFile::fake()->image('qa-highlight.jpg'),
            'is_published' => '1',
        ])->assertForbidden();

        $this->assertDatabaseMissing('site_popups', ['title' => 'QA Highlight']);
        Storage::disk('public')->assertDirectoryEmpty('site-popups');
    }

    public function test_homepage_selected_content_uses_admin_selected_order(): void
    {
        $first = PowerPlant::create(['name' => 'First QA Plant', 'slug' => 'first-qa-plant', 'status' => 'operational', 'capacity_kw' => 100]);
        $second = PowerPlant::create(['name' => 'Second QA Plant', 'slug' => 'second-qa-plant', 'status' => 'operational', 'capacity_kw' => 200]);
        HomepageSection::updateOrCreate(['key' => 'projects'], [
            'sort_order' => 3, 'is_enabled' => true, 'settings' => [
                'limit' => 2, 'mode' => 'selected', 'ids' => [$second->id, $first->id],
            ],
        ]);

        $response = $this->get(route('home'));
        $response->assertOk();
        $response->assertSeeInOrder([$second->name, $first->name]);
    }

}
