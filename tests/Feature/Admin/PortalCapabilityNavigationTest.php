<?php

namespace Tests\Feature\Admin;

use App\Models\Permission;
use App\Models\Role;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class PortalCapabilityNavigationTest extends TestCase
{
    use RefreshDatabase;

    public function test_user_management_navigation_is_capability_aware(): void
    {
        $view = Permission::firstOrCreate(['slug'=>'users.view'], ['name'=>'View users']);
        $manage = Permission::firstOrCreate(['slug'=>'users.manage'], ['name'=>'Manage users']);
        $role = Role::create(['name'=>'User Viewer','slug'=>'user-viewer','is_system'=>false]);
        $role->permissions()->sync([$view->id]);
        $user = User::factory()->create();
        $user->roles()->attach($role);

        $this->actingAs($user)->get(route('admin.users.index'))->assertOk()->assertSee('Users')->assertDontSee('Add account');
    }

    public function test_user_manager_sees_add_account_navigation(): void
    {
        $view = Permission::firstOrCreate(['slug'=>'users.view'], ['name'=>'View users']);
        $manage = Permission::firstOrCreate(['slug'=>'users.manage'], ['name'=>'Manage users']);
        $role = Role::create(['name'=>'User Manager','slug'=>'user-manager','is_system'=>false]);
        $role->permissions()->sync([$view->id, $manage->id]);
        $user = User::factory()->create();
        $user->roles()->attach($role);

        $this->actingAs($user)->get(route('admin.users.index'))->assertOk()->assertSee('Add account');
    }
}
