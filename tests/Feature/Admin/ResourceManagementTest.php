<?php

namespace Tests\Feature\Admin;

use App\Models\Permission;
use App\Models\Role;
use App\Models\SiteContentItem;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class ResourceManagementTest extends TestCase
{
    use RefreshDatabase;

    private function publisher(): User
    {
        $manage = Permission::firstOrCreate(
            ['slug' => 'website.manage'],
            ['name' => 'Manage website sections']
        );
        $publish = Permission::firstOrCreate(
            ['slug' => 'website.publish'],
            ['name' => 'Publish website content']
        );

        $role = Role::create([
            'name' => 'Resource Publisher',
            'slug' => 'resource-publisher',
            'is_system' => false,
        ]);
        $role->permissions()->sync([$manage->id, $publish->id]);

        $user = User::factory()->create();
        $user->roles()->attach($role);

        return $user;
    }

    public function test_resource_can_be_created_from_website_content_and_published_publicly(): void
    {
        $user = $this->publisher();

        $this->actingAs($user)->post(route('admin.site-content.store'), [
            'type' => 'resource',
            'title' => 'Engineering Brief',
            'slug' => 'engineering-brief',
            'excerpt' => 'Official engineering resource.',
            'content' => '<p>Approved resource content.</p>',
            'status' => 'published',
        ])->assertRedirect(route('admin.site-content.index', ['type' => 'resource']));

        $resource = SiteContentItem::where('slug', 'engineering-brief')->firstOrFail();

        $this->get(route('resources.index'))
            ->assertOk()
            ->assertSee('Engineering Brief');

        $this->get(route('resources.show', $resource->slug))
            ->assertOk()
            ->assertSee('Approved resource content.');

        $this->get(route('resources.download', $resource->slug))
            ->assertNotFound();
    }

    public function test_resource_supports_page_builder_seo_framework_and_safe_duplicate(): void
    {
        $user = $this->publisher();

        $this->actingAs($user)->post(route('admin.site-content.store'), [
            'type' => 'resource',
            'title' => 'Unified Resource',
            'slug' => 'unified-resource',
            'excerpt' => 'Builder-enabled resource.',
            'content' => '<p>Rich editor content.</p>',
            'builder_blocks' => [
                ['type' => 'hero', 'title' => 'Hero section', 'content' => '<p>Hero body.</p>', 'visible' => true],
                ['type' => 'cta', 'title' => 'Learn more', 'url' => '/contact', 'visible' => true],
            ],
            'template' => 'article',
            'meta_title' => 'Unified Resource SEO',
            'meta_description' => 'A professional resource description.',
            'use_global_framework' => '1',
            'use_global_header' => '1',
            'use_global_footer' => '1',
            'status' => 'published',
        ])->assertRedirect(route('admin.site-content.index', ['type' => 'resource']));

        $resource = SiteContentItem::where('slug', 'unified-resource')->firstOrFail();
        $this->assertSame('article', $resource->template);
        $this->assertIsArray($resource->builder_blocks);
        $this->assertCount(2, $resource->builder_blocks);
        $this->assertTrue($resource->use_global_framework);

        $this->get(route('resources.show', $resource->slug))
            ->assertOk()
            ->assertSee('Hero section')
            ->assertSee('Hero body.')
            ->assertSee('Unified Resource SEO', false);

        $this->actingAs($user)->post(route('admin.site-content.resource.duplicate', $resource))
            ->assertRedirect();

        $this->assertDatabaseHas('site_content_items', [
            'type' => 'resource',
            'title' => 'Unified Resource Copy',
            'status' => 'draft',
        ]);
    }

    public function test_resource_download_is_available_only_for_published_resources_with_existing_attachment(): void
    {
        Storage::fake('public');
        $path = 'site-content/attachments/engineering-brief.pdf';
        Storage::disk('public')->put($path, '%PDF-1.4 QA');

        $resource = SiteContentItem::create([
            'type' => 'resource',
            'title' => 'Downloadable Brief',
            'slug' => 'downloadable-brief',
            'content' => '<p>Downloadable resource.</p>',
            'status' => 'published',
            'attachment_path' => $path,
            'attachment_name' => 'engineering-brief.pdf',
            'attachment_size' => 11,
            'attachment_mime' => 'application/pdf',
        ]);

        $this->get(route('resources.download', $resource->slug))
            ->assertOk()
            ->assertHeader('content-type', 'application/pdf');

        $resource->update(['status' => 'draft']);

        $this->get(route('resources.download', $resource->slug))
            ->assertNotFound();
    }
}
