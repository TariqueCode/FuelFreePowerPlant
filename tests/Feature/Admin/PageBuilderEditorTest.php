<?php

namespace Tests\Feature\Admin;

use App\Models\Permission;
use App\Models\Role;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class PageBuilderEditorTest extends TestCase
{
    private function user(): User
    {
        $permissions = collect([
            ['slug' => 'cms.view', 'name' => 'View CMS'],
            ['slug' => 'cms.manage', 'name' => 'Manage CMS'],
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
        $response = $this->actingAs($this->user())->get(route('admin.cms.create'));

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

    public function test_page_builder_saves_rich_text_editor_content(): void
    {
        $response = $this->actingAs($this->user())->post(route('admin.cms.store'), [
            'title' => 'Global Editor QA',
            'slug' => 'global-editor-qa',
            'excerpt' => 'Editor persistence check.',
            'builder_blocks' => json_encode([
                ['type' => 'rich_text', 'title' => 'Formatted content', 'content' => '<p><strong>Bold</strong> editor content.</p>', 'tone' => 'dark', 'align' => 'left', 'visible' => true],
            ]),
            'is_published' => '0',
        ]);

        $response->assertRedirect(route('admin.cms.index'));
        $this->assertDatabaseHas('cms_pages', ['slug' => 'global-editor-qa']);
        $page = \App\Models\CmsPage::where('slug', 'global-editor-qa')->firstOrFail();
        $this->assertSame('<p><strong>Bold</strong> editor content.</p>', $page->builder_blocks[0]['content']);
        $this->assertTrue($page->use_global_framework);
        $this->assertTrue($page->use_global_header);
        $this->assertTrue($page->use_global_footer);
    }

    public function test_global_editor_media_upload_endpoint_accepts_image(): void
    {
        Storage::fake('public');

        $response = $this->actingAs($this->user())->post(route('admin.site-content.media'), [
            'media' => UploadedFile::fake()->image('editor-image.jpg', 640, 480),
        ]);

        $response->assertOk()->assertJsonStructure(['url', 'mime', 'name']);
        $this->assertCount(1, Storage::disk('public')->allFiles('site-content/media'));
    }
}
