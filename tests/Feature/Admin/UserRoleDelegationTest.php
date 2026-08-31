<?php

namespace Tests\Feature\Admin;

use App\Models\Permission;
use App\Models\Role;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class UserRoleDelegationTest extends TestCase
{
    use RefreshDatabase;

    private function role(string $slug, array $permissions): Role
    {
        $role = Role::create(['name' => ucfirst($slug), 'slug' => $slug, 'is_system' => true]);
        $role->permissions()->sync(collect($permissions)->map(fn ($slug) => Permission::create(['name' => $slug, 'slug' => $slug]))->all());
        return $role;
    }

    public function test_user_cannot_assign_a_role_with_permissions_they_do_not_have(): void
    {
        $administrator = $this->role('administrator', ['users.manage']);
        $super = $this->role('super-admin', ['users.manage', 'settings.manage']);

        $actor = User::factory()->create();
        $actor->roles()->attach($administrator);

        $target = User::factory()->create();

        $response = $this->actingAs($actor)->patch(route('admin.users.update', $target), [
            'name' => $target->name,
            'email' => $target->email,
            'password' => '',
            'password_confirmation' => '',
            'role_id' => $super->id,
        ]);

        $response->assertForbidden();
        $this->assertFalse($target->fresh()->hasRole('super-admin'));
    }

    public function test_super_admin_can_assign_any_role(): void
    {
        $super = $this->role('super-admin', ['users.manage']);
        $restricted = $this->role('restricted', ['settings.manage']);

        $actor = User::factory()->create();
        $actor->roles()->attach($super);
        $target = User::factory()->create();

        $response = $this->actingAs($actor)->patch(route('admin.users.update', $target), [
            'name' => $target->name,
            'email' => $target->email,
            'password' => '',
            'password_confirmation' => '',
            'role_id' => $restricted->id,
        ]);

        $response->assertRedirect(route('admin.users.index'));
        $this->assertTrue($target->fresh()->hasRole('restricted'));
    }
}
