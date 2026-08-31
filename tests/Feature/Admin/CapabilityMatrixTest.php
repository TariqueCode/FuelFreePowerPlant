<?php

namespace Tests\Feature\Admin;

use App\Models\NavigationMenuItem;
use App\Models\Permission;
use App\Models\Role;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class CapabilityMatrixTest extends TestCase
{
    use RefreshDatabase;

    private function userWithPermissions(array $slugs): User
    {
        $permissions = collect($slugs)->mapWithKeys(fn ($slug) => [
            $slug => Permission::firstOrCreate(['slug' => $slug], ['name' => $slug]),
        ]);

        $role = Role::create([
            'name' => 'Test Role',
            'slug' => 'test-role-'.uniqid(),
            'is_system' => false,
        ]);
        $role->permissions()->sync($permissions->pluck('id'));

        $user = User::factory()->create();
        $user->roles()->attach($role);

        return $user;
    }

    public function test_navigation_view_does_not_grant_navigation_management(): void
    {
        $user = $this->userWithPermissions(['website.view']);
        $item = NavigationMenuItem::create([
            'menu' => 'main',
            'label' => 'Test',
            'url' => '/test',
            'target' => '_self',
            'sort_order' => 0,
            'is_visible' => true,
        ]);

        $this->actingAs($user)
            ->patch(route('admin.navigation.update', $item), [
                'kind' => 'link',
                'label' => 'Changed',
                'url' => '/changed',
                'target' => '_self',
                'is_visible' => true,
            ])
            ->assertForbidden();

        $this->assertSame('Test', $item->fresh()->label);
    }

    public function test_navigation_manage_can_update_navigation(): void
    {
        $user = $this->userWithPermissions(['website.view', 'navigation.manage']);
        $item = NavigationMenuItem::create([
            'menu' => 'main',
            'label' => 'Test',
            'url' => '/test',
            'target' => '_self',
            'sort_order' => 0,
            'is_visible' => true,
        ]);

        $this->actingAs($user)
            ->patch(route('admin.navigation.update', $item), [
                'kind' => 'link',
                'label' => 'Changed',
                'url' => '/changed',
                'target' => '_self',
                'is_visible' => true,
            ])
            ->assertRedirect();

        $this->assertSame('Changed', $item->fresh()->label);
    }
}
