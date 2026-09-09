<?php

namespace Tests\Feature\Admin;

use App\Models\NavigationMenuItem;
use App\Models\Permission;
use App\Models\Role;
use App\Models\User;
use App\Services\DashboardNavigationService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class DashboardNavigationServiceTest extends TestCase
{
    use RefreshDatabase;

    public function test_settings_is_available_without_a_database_navigation_record(): void
    {
        $permission = Permission::firstOrCreate(
            ['slug' => 'settings.manage'],
            ['name' => 'Manage settings'],
        );
        $role = Role::create([
            'name' => 'Settings QA',
            'slug' => 'settings-qa',
            'is_system' => false,
        ]);
        $role->permissions()->attach($permission);

        $user = User::factory()->create();
        $user->roles()->attach($role);

        $this->actingAs($user);
        $tree = app(DashboardNavigationService::class)->tree();

        $this->assertTrue($tree->contains(fn (NavigationMenuItem $item): bool => $item->route_name === 'admin.settings'));
    }

    public function test_retired_external_dashboard_destinations_are_not_rendered(): void
    {
        $permission = Permission::firstOrCreate(
            ['slug' => 'website.view'],
            ['name' => 'View website'],
        );
        $role = Role::create([
            'name' => 'Navigation QA',
            'slug' => 'navigation-qa',
            'is_system' => false,
        ]);
        $role->permissions()->attach($permission);

        $user = User::factory()->create();
        $user->roles()->attach($role);

        NavigationMenuItem::create([
            'menu' => 'dashboard',
            'area' => 'dashboard',
            'label' => 'Legacy Operations',
            'url' => '/admin/plants',
            'target' => '_self',
            'source_type' => 'external_link',
            'permission_key' => 'website.view',
            'is_visible' => true,
            'sort_order' => 1,
        ]);

        $this->actingAs($user);
        $tree = app(DashboardNavigationService::class)->tree();

        $this->assertFalse($tree->contains(fn (NavigationMenuItem $item): bool => $item->url === '/admin/plants'));
    }
}
