<?php

namespace Tests\Feature\Admin;

use App\Models\NavigationMenuItem;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Database\QueryException;
use Tests\TestCase;

class NavigationMenuIntegrityTest extends TestCase
{
    use RefreshDatabase;

    public function test_a_live_source_cannot_be_stored_twice_in_the_same_menu(): void
    {
        NavigationMenuItem::create([
            'menu' => 'main',
            'parent_id' => null,
            'label' => 'Home',
            'url' => '/',
            'route_name' => 'home',
            'target' => '_self',
            'is_visible' => true,
            'sort_order' => 0,
            'source_key' => 'route:home',
            'source_type' => 'route',
            'area' => 'public',
        ]);

        $this->expectException(QueryException::class);

        NavigationMenuItem::create([
            'menu' => 'main',
            'parent_id' => null,
            'label' => 'Home again',
            'url' => '/',
            'route_name' => 'home',
            'target' => '_self',
            'is_visible' => true,
            'sort_order' => 1,
            'source_key' => 'route:home',
            'source_type' => 'route',
            'area' => 'public',
        ]);
    }

    public function test_the_same_source_can_exist_in_main_and_dashboard_menus(): void
    {
        foreach (['main', 'dashboard'] as $position => $menu) {
            NavigationMenuItem::create([
                'menu' => $menu,
                'parent_id' => null,
                'label' => 'Home',
                'url' => '/',
                'route_name' => 'home',
                'target' => '_self',
                'is_visible' => true,
                'sort_order' => $position,
                'source_key' => 'route:home',
                'source_type' => 'route',
                'area' => $menu === 'dashboard' ? 'dashboard' : 'public',
            ]);
        }

        $this->assertDatabaseCount('navigation_menu_items', 2);
    }
}
