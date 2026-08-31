<?php

namespace Tests\Feature;

use App\Models\Permission;
use App\Models\Role;
use App\Models\SiteContentItem;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ResourcePublishingTest extends TestCase
{
    use RefreshDatabase;

    public function test_resource_management_is_separate_from_public_publication_authority(): void
    {
        $manage = Permission::firstOrCreate(['slug' => 'website.manage'], ['name' => 'Manage website content']);
        $publish = Permission::firstOrCreate(['slug' => 'website.publish'], ['name' => 'Publish website content']);

        $managerRole = Role::create(['name' => 'Resource Manager', 'slug' => 'resource-manager', 'is_system' => false]);
        $managerRole->permissions()->sync([$manage->id]);

        $publisherRole = Role::create(['name' => 'Resource Publisher', 'slug' => 'resource-publisher', 'is_system' => false]);
        $publisherRole->permissions()->sync([$manage->id, $publish->id]);

        $manager = User::factory()->create();
        $manager->roles()->attach($managerRole);

        $publisher = User::factory()->create();
        $publisher->roles()->attach($publisherRole);

        $this->actingAs($manager)->post(route('admin.site-content.store'), [
            'type' => 'resource',
            'title' => 'Private QA Resource',
            'slug' => 'private-qa-resource',
            'excerpt' => 'Draft resource',
            'content' => '<p>Draft content.</p>',
            'status' => 'published',
        ])->assertForbidden();

        $this->assertDatabaseMissing('site_content_items', ['slug' => 'private-qa-resource']);

        $this->actingAs($manager)->post(route('admin.site-content.store'), [
            'type' => 'resource',
            'title' => 'Draft QA Resource',
            'slug' => 'draft-qa-resource',
            'excerpt' => 'Draft resource',
            'content' => '<p>Draft content.</p>',
            'status' => 'draft',
        ])->assertRedirect();

        $this->assertDatabaseHas('site_content_items', [
            'slug' => 'draft-qa-resource',
            'type' => 'resource',
            'status' => 'draft',
        ]);

        $this->actingAs($publisher)->post(route('admin.site-content.store'), [
            'type' => 'resource',
            'title' => 'Published QA Resource',
            'slug' => 'published-qa-resource',
            'excerpt' => 'Published resource',
            'content' => '<p>Published content.</p>',
            'status' => 'published',
        ])->assertRedirect();

        $this->get(route('resources.index'))
            ->assertOk()
            ->assertDontSee('Draft QA Resource')
            ->assertSee('Published QA Resource');

        $this->get(route('resources.show', 'draft-qa-resource'))->assertNotFound();
        $this->get(route('resources.show', 'published-qa-resource'))
            ->assertOk()
            ->assertSee('Published content.');
    }

    public function test_resource_download_route_requires_a_published_resource_with_an_attachment(): void
    {
        $draft = SiteContentItem::create([
            'type' => 'resource',
            'title' => 'Draft Download',
            'slug' => 'draft-download',
            'status' => 'draft',
            'attachment_path' => 'site-content/attachments/draft.pdf',
            'attachment_name' => 'draft.pdf',
            'attachment_mime' => 'application/pdf',
        ]);

        $this->get(route('resources.download', $draft->slug))->assertNotFound();
    }
}
