<?php

namespace Tests\Feature\Admin;

use App\Models\Permission;
use App\Models\Role;
use App\Models\SitePopup;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class SitePopupTest extends TestCase
{
    use RefreshDatabase;

    private function manager(string $name = 'Highlight Manager'): User
    {
        $user = User::factory()->create();
        $role = Role::create([
            'name' => $name,
            'slug' => strtolower(str_replace(' ', '-', $name)),
            'is_system' => false,
        ]);
        $permission = Permission::firstOrCreate(['slug' => 'website.manage'], ['name' => 'Manage website']);
        $role->permissions()->attach($permission);
        $user->roles()->attach($role);

        return $user;
    }

    public function test_banner_can_be_saved_without_fileinfo_mime_guessing(): void
    {
        Storage::fake('public');
        $user = $this->manager();

        $image = UploadedFile::fake()->image('announcement.jpg', 1200, 600);

        $response = $this->actingAs($user)->post(route('admin.site-popups.store'), [
            'image' => $image,
            'title' => 'Important announcement',
            'display_seconds' => 8,
            'is_published' => false,
        ]);

        $response->assertRedirect(route('admin.site-popups.index'));
        $popup = SitePopup::query()->latest('id')->first();
        $this->assertNotNull($popup);
        $this->assertNotEmpty($popup->image_path);
        Storage::disk('public')->assertExists($popup->image_path);
    }

    public function test_new_banner_requires_an_image(): void
    {
        $user = $this->manager('Highlight Image Required');

        $response = $this->actingAs($user)->post(route('admin.site-popups.store'), [
            'title' => 'Without image',
            'is_published' => false,
        ]);

        $response->assertSessionHasErrors('image');
        $this->assertDatabaseCount('site_popups', 0);
    }
}
