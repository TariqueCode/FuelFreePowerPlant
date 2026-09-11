<?php

namespace Tests\Feature\Admin;

use App\Models\NavigationMenuItem;
use App\Models\Permission;
use App\Models\Role;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class NavigationMenuReorderTest extends TestCase
{
    use RefreshDatabase;

    private function navigationAdmin(): User
    {
        $view = Permission::firstOrCreate(['slug' => 'website.view'], ['name' => 'View website']);
        $manage = Permission::firstOrCreate(['slug' => 'navigation.manage'], ['name' => 'Manage navigation']);
        $role = Role::create(['name' => 'Navigation Reorder QA', 'slug' => 'navigation-reorder-qa', 'is_system' => false]);
        $role->permissions()->sync([$view->id, $manage->id]);
        $user = User::factory()->create();
        $user->roles()->attach($role);

        return $user;
    }

    public function test_drag_drop_tree_is_persisted_with_parent_and_sort_order(): void
    {
        $user = $this->navigationAdmin();
        $folder = NavigationMenuItem::create([
            'menu' => 'main', 'parent_id' => null, 'label' => 'Company', 'url' => null,
            'route_name' => null, 'target' => '_self', 'is_visible' => true, 'sort_order' => 0,
            'source_key' => null, 'source_type' => 'folder', 'area' => 'public',
        ]);
        $first = NavigationMenuItem::create([
            'menu' => 'main', 'parent_id' => null, 'label' => 'First', 'url' => '/first',
            'route_name' => null, 'target' => '_self', 'is_visible' => true, 'sort_order' => 1,
            'source_key' => null, 'source_type' => 'external_link', 'area' => 'public',
        ]);
        $second = NavigationMenuItem::create([
            'menu' => 'main', 'parent_id' => null, 'label' => 'Second', 'url' => '/second',
            'route_name' => null, 'target' => '_self', 'is_visible' => true, 'sort_order' => 2,
            'source_key' => null, 'source_type' => 'external_link', 'area' => 'public',
        ]);

        $response = $this->actingAs($user)->postJson(route('admin.menu-builder.reorder'), [
            'menu' => 'main',
            'tree' => [
                ['id' => $second->id, 'parent_id' => $folder->id, 'sort_order' => 0],
                ['id' => $folder->id, 'parent_id' => null, 'sort_order' => 0],
                ['id' => $first->id, 'parent_id' => null, 'sort_order' => 1],
            ],
        ]);

        $response->assertOk()->assertJson(['ok' => true]);
        $this->assertDatabaseHas('navigation_menu_items', ['id' => $second->id, 'parent_id' => $folder->id, 'sort_order' => 0]);
        $this->assertDatabaseHas('navigation_menu_items', ['id' => $folder->id, 'parent_id' => null, 'sort_order' => 0]);
        $this->assertDatabaseHas('navigation_menu_items', ['id' => $first->id, 'parent_id' => null, 'sort_order' => 1]);
    }

    public function test_drag_drop_rejects_a_cycle_without_mutating_the_tree(): void
    {
        $user = $this->navigationAdmin();
        $parent = NavigationMenuItem::create([
            'menu' => 'main', 'parent_id' => null, 'label' => 'Parent', 'url' => null,
            'route_name' => null, 'target' => '_self', 'is_visible' => true, 'sort_order' => 0,
            'source_key' => null, 'source_type' => 'folder', 'area' => 'public',
        ]);
        $child = NavigationMenuItem::create([
            'menu' => 'main', 'parent_id' => $parent->id, 'label' => 'Child', 'url' => '/child',
            'route_name' => null, 'target' => '_self', 'is_visible' => true, 'sort_order' => 0,
            'source_key' => null, 'source_type' => 'external_link', 'area' => 'public',
        ]);

        $response = $this->actingAs($user)->postJson(route('admin.menu-builder.reorder'), [
            'menu' => 'main',
            'tree' => [
                ['id' => $parent->id, 'parent_id' => $child->id, 'sort_order' => 0],
                ['id' => $child->id, 'parent_id' => $parent->id, 'sort_order' => 0],
            ],
        ]);

        $response->assertStatus(422);
        $this->assertDatabaseHas('navigation_menu_items', ['id' => $parent->id, 'parent_id' => null]);
        $this->assertDatabaseHas('navigation_menu_items', ['id' => $child->id, 'parent_id' => $parent->id]);
    }
}
