<?php

namespace Tests\Feature\Admin;

use App\Models\CmsPage;
use App\Models\Permission;
use App\Models\Role;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class PageBuilderEditorTest extends TestCase
{
    use RefreshDatabase;

    private function user(): User
    {
        $permissions = collect([
            ['slug' => 'cms.view', 'name' => 'View CMS'],
            ['slug' => 'cms.manage', 'name' => 'Manage CMS'],
            ['slug' => 'cms.publish', 'name' => 'Publish CMS'],
            ['slug' => 'website.manage', 'name' => 'Manage Website'],
        ])->map(fn (array $permission) => Permission::firstOrCreate(['slug' => $permission['slug']], ['name' => $permission['name']]));

        $role = Role::create(['name' => 'Page Builder QA', 'slug' => 'page-builder-qa', 'is_system' => false]);
        $role->permissions()->attach($permissions->pluck('id'));

        $user = User::factory()->create();
        $user->roles()->attach($role);
        return $user;
    }

    public function test_page_builder_exposes_global_cms_editor_tool_surface(): void
    {
        $response = $this->actingAs($this->user())->get(route('admin.page-builder.create'));

        $response->assertOk()
            ->assertSee('Global CMS Editor', false)
            ->assertSee('Home', false)
            ->assertSee('Insert', false)
            ->assertSee('View', false)
            ->assertSee('Undo', false)
            ->assertSee('Redo', false)
            ->assertSee('HTML<br>source', false)
            ->assertSee('Fullscreen', false)
            ->assertSee('YouTube', false)
            ->assertSee('Facebook', false)
            ->assertSee('Gallery', false)
            ->assertSee('Columns', false)
            ->assertSee('Table', false);
    }

    public function test_page_builder_saves_canonical_rich_text_editor_content(): void
    {
        $response = $this->actingAs($this->user())->post(route('admin.page-builder.store'), [
            'title' => 'Global Editor QA',
            'slug' => 'global-editor-qa',
            'excerpt' => 'Editor persistence check.',
            'content' => '<p><strong>Bold</strong> editor content.</p>',
            'is_published' => '0',
        ]);

        $response->assertRedirect(route('admin.page-builder.index'));
        $this->assertDatabaseHas('cms_pages', ['slug' => 'global-editor-qa']);
        $page = CmsPage::where('slug', 'global-editor-qa')->firstOrFail();
        $this->assertSame('<p><strong>Bold</strong> editor content.</p>', $page->content);
        $this->assertSame([], $page->builder_blocks);
        $this->assertTrue($page->use_global_framework);
        $this->assertTrue($page->use_global_header);
        $this->assertTrue($page->use_global_footer);
    }

    public function test_page_builder_persists_structured_sections_without_falling_back_to_legacy_cms(): void
    {
        $blocks = [
            ['type' => 'hero', 'title' => 'Our Technology', 'body' => 'Structured hero content.'],
            ['type' => 'cards', 'title' => 'Capabilities', 'items' => [['title' => 'Engineering', 'body' => 'Reliable systems.']]],
        ];

        $response = $this->actingAs($this->user())->post(route('admin.page-builder.store'), [
            'title' => 'Structured Page',
            'slug' => 'structured-page',
            'content' => '<p>Fallback content.</p>',
            'builder_blocks' => json_encode($blocks),
            'is_published' => '0',
        ]);

        $response->assertRedirect(route('admin.page-builder.index'));
        $page = CmsPage::where('slug', 'structured-page')->firstOrFail();

        // MySQL JSON normalizes object key order; compare structure, not object key insertion order.
        $this->assertSame($this->normalizeJsonValue($blocks), $this->normalizeJsonValue($page->builder_blocks));
        $this->assertSame('<p>Fallback content.</p>', $page->content);
        $this->assertTrue($page->use_global_framework);
    }

    public function test_page_builder_rejects_invalid_section_payload(): void
    {
        $response = $this->actingAs($this->user())->from(route('admin.page-builder.create'))->post(route('admin.page-builder.store'), [
            'title' => 'Invalid Sections',
            'slug' => 'invalid-sections',
            'builder_blocks' => '{not-valid-json',
            'is_published' => '0',
        ]);

        $response->assertStatus(422);
        $this->assertDatabaseMissing('cms_pages', ['slug' => 'invalid-sections']);
    }

    public function test_global_editor_media_upload_endpoint_accepts_image(): void
    {
        Storage::fake('public');

        $response = $this->actingAs($this->user())->post(route('admin.page-builder.media'), [
            'media' => UploadedFile::fake()->image('editor-image.jpg', 640, 480),
        ]);

        $response->assertOk()->assertJsonStructure(['url', 'mime', 'name']);
        $this->assertCount(1, Storage::disk('public')->allFiles('site-content/media'));
    }

    /** @return array<int|string, mixed> */
    private function normalizeJsonValue(mixed $value): mixed
    {
        if (!is_array($value)) {
            return $value;
        }

        $normalized = [];
        foreach ($value as $key => $child) {
            $normalized[$key] = $this->normalizeJsonValue($child);
        }

        if ($this->isAssociative($normalized)) {
            ksort($normalized);
        }

        return $normalized;
    }

    private function isAssociative(array $value): bool
    {
        return array_keys($value) !== range(0, count($value) - 1);
    }
}
