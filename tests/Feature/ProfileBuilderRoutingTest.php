<?php

namespace Tests\Feature;

use App\Models\ManagementProfileFolder;
use App\Models\NavigationMenuItem;
use App\Models\Permission;
use App\Models\Role;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Route;
use Tests\TestCase;

class ProfileBuilderRoutingTest extends TestCase
{
    use RefreshDatabase;

    private function userWithPermissions(array $slugs): User
    {
        $permissions = collect($slugs)->map(fn (string $slug) => Permission::firstOrCreate(
            ['slug' => $slug],
            ['name' => $slug],
        ));

        $role = Role::create([
            'name' => 'Profile Builder QA '.uniqid(),
            'slug' => 'profile-builder-qa-'.uniqid(),
            'is_system' => false,
        ]);
        $role->permissions()->sync($permissions->pluck('id'));

        $user = User::factory()->create();
        $user->roles()->attach($role);

        return $user;
    }

    public function test_profile_builder_routes_exist_with_capability_split(): void
    {
        $this->assertTrue(Route::has('admin.profile-builder.index'));
        $this->assertTrue(Route::has('admin.profile-builder.create'));
        $this->assertTrue(Route::has('admin.profile-builder.store'));
        $this->assertTrue(Route::has('admin.profile-builder.edit'));
        $this->assertTrue(Route::has('admin.profile-builder.update'));
        $this->assertTrue(Route::has('admin.profile-builder.destroy'));
        $this->assertTrue(Route::has('admin.profile-builder.toggle'));
        $this->assertTrue(Route::has('admin.profile-builder.reorder'));
        $this->assertTrue(Route::has('admin.profile-builder.folders.create'));
        $this->assertTrue(Route::has('admin.profile-builder.folders.store'));
        $this->assertTrue(Route::has('admin.profile-builder.folders.edit'));
        $this->assertTrue(Route::has('admin.profile-builder.folders.update'));
        $this->assertTrue(Route::has('admin.profile-builder.folders.destroy'));
        $this->assertTrue(Route::has('admin.profile-builder.folders.reorder'));
        $this->assertTrue(Route::has('management.folder'));
    }

    public function test_view_only_user_can_open_builder_but_cannot_mutate_it(): void
    {
        $user = $this->userWithPermissions(['website.view']);

        $this->actingAs($user)
            ->get(route('admin.profile-builder.index'))
            ->assertOk();

        $this->actingAs($user)
            ->post(route('admin.profile-builder.store'), [
                'management_profile_folder_id' => 999,
                'title' => 'Blocked Profile',
                'designation' => 'Director',
                'phone' => '+8801700000000',
                'status' => 'draft',
            ])
            ->assertForbidden();
    }

    public function test_manager_can_create_folder_and_folder_becomes_public_page_and_navigation_item(): void
    {
        $user = $this->userWithPermissions(['website.view', 'website.manage']);

        $this->actingAs($user)
            ->post(route('admin.profile-builder.folders.store'), [
                'name' => 'Executive Leadership',
                'status' => 'published',
            ])
            ->assertRedirect(route('admin.profile-builder.index'));

        $folder = ManagementProfileFolder::query()->where('slug', 'executive-leadership')->firstOrFail();

        $this->assertDatabaseHas('navigation_menu_items', [
            'menu' => 'main',
            'source_key' => 'management_folder:'.$folder->id,
            'url' => '/executive-leadership',
            'is_visible' => 1,
        ]);

        $this->get('/executive-leadership')
            ->assertOk()
            ->assertSee('Executive Leadership');
    }

    public function test_folder_navigation_item_is_removed_when_empty_folder_is_deleted(): void
    {
        $user = $this->userWithPermissions(['website.view', 'website.manage']);
        $folder = ManagementProfileFolder::create([
            'name' => 'Delete Me',
            'slug' => 'delete-me',
            'status' => 'published',
            'sort_order' => 1,
        ]);

        NavigationMenuItem::create([
            'menu' => 'main',
            'group' => 'main',
            'parent_id' => null,
            'label' => 'Delete Me',
            'url' => '/delete-me',
            'route_name' => null,
            'source_key' => 'management_folder:'.$folder->id,
            'source_type' => 'external_link',
            'permission_key' => null,
            'target' => '_self',
            'icon' => 'fa-solid fa-users',
            'is_visible' => true,
            'sort_order' => 1,
            'area' => 'public',
        ]);

        $this->actingAs($user)
            ->delete(route('admin.profile-builder.folders.destroy', $folder))
            ->assertRedirect();

        $this->assertDatabaseMissing('management_profile_folders', ['id' => $folder->id]);
        $this->assertDatabaseMissing('navigation_menu_items', ['source_key' => 'management_folder:'.$folder->id]);
    }
}
