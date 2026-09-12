<?php

namespace Tests\Feature\Admin;

use App\Models\Document;
use App\Models\DocumentFolder;
use App\Models\Permission;
use App\Models\Role;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class DocumentsFileManagerTest extends TestCase
{
    use RefreshDatabase;

    private function admin(): User
    {
        $permissions = collect(['documents.view', 'documents.manage'])->map(fn (string $slug) => Permission::firstOrCreate(['slug' => $slug], ['name' => $slug])->id);
        $role = Role::create(['name' => 'Documents Test Admin', 'slug' => 'documents-test-admin', 'is_system' => false]);
        $role->permissions()->sync($permissions);
        $user = User::factory()->create();
        $user->roles()->attach($role);
        return $user;
    }

    public function test_file_manager_renders_and_uses_canonical_folder_query_url(): void
    {
        $user = $this->admin();
        $folder = DocumentFolder::create(['user_id' => $user->id, 'name' => 'Projects']);

        $this->actingAs($user)->get(route('admin.documents'))
            ->assertOk()
            ->assertSee('File Manager')
            ->assertSee(route('admin.documents', ['folder' => $folder->id]), false);
    }

    public function test_folder_compatibility_url_redirects_to_canonical_query_url(): void
    {
        $user = $this->admin();
        $folder = DocumentFolder::create(['user_id' => $user->id, 'name' => 'Projects']);

        $this->actingAs($user)->get('/admin/documents/folders/'.$folder->id)
            ->assertRedirect(route('admin.documents', ['folder' => $folder->id]));
    }

    public function test_document_mutations_have_post_compatibility_endpoints(): void
    {
        Storage::fake('local');
        $user = $this->admin();
        $folder = DocumentFolder::create(['user_id' => $user->id, 'name' => 'Projects']);
        $file = UploadedFile::fake()->create('report.txt', 8, 'text/plain');
        Storage::disk('local')->putFileAs('private/'.$user->id, $file, 'report.txt');
        $document = Document::create([
            'user_id' => $user->id,
            'folder_id' => $folder->id,
            'original_name' => 'report.txt',
            'stored_name' => 'report.txt',
            'disk' => 'local',
            'path' => 'private/'.$user->id.'/report.txt',
            'mime_type' => 'text/plain',
            'size' => 8192,
            'extension' => 'txt',
        ]);

        $this->actingAs($user)->post(route('admin.documents.rename', $document), ['name' => 'renamed.txt'])->assertRedirect();
        $this->assertDatabaseHas('documents', ['id' => $document->id, 'original_name' => 'renamed.txt']);

        $this->actingAs($user)->post(route('admin.documents.destroy.post', $document))->assertRedirect();
        $this->assertDatabaseMissing('documents', ['id' => $document->id]);
        Storage::disk('local')->assertMissing('private/'.$user->id.'/report.txt');
    }
}
