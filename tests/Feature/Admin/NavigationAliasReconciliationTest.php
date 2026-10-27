<?php

namespace Tests\Feature\Admin;

use App\Models\NavigationMenuItem;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class NavigationAliasReconciliationTest extends TestCase
{
    use RefreshDatabase;

    public function test_migration_canonicalizes_builder_aliases_and_reparents_duplicate_children(): void
    {
        $website = NavigationMenuItem::create([
            'menu' => 'dashboard',
            'parent_id' => null,
            'label' => 'Website',
            'url' => '#',
            'route_name' => null,
            'target' => '_self',
            'is_visible' => true,
            'sort_order' => 10,
            'source_key' => null,
            'source_type' => 'folder',
            'area' => 'dashboard',
            'permission_key' => null,
        ]);

        $legacy = NavigationMenuItem::create([
            'menu' => 'dashboard',
            'parent_id' => $website->id,
            'label' => 'Profile Builder',
            'url' => '/admin/management',
            'route_name' => 'admin.management.index',
            'target' => '_self',
            'is_visible' => true,
            'sort_order' => 20,
            'source_key' => 'route:admin.management.index',
            'source_type' => 'route',
            'area' => 'dashboard',
            'permission_key' => 'website.view',
        ]);

        $canonical = NavigationMenuItem::create([
            'menu' => 'dashboard',
            'parent_id' => $website->id,
            'label' => 'Profiles',
            'url' => '/admin/profile-builder',
            'route_name' => 'admin.profile-builder.index',
            'target' => '_self',
            'is_visible' => true,
            'sort_order' => 21,
            'source_key' => 'route:admin.profile-builder.index',
            'source_type' => 'route',
            'area' => 'dashboard',
            'permission_key' => 'website.view',
        ]);

        $child = NavigationMenuItem::create([
            'menu' => 'dashboard',
            'parent_id' => $canonical->id,
            'label' => 'Legacy child',
            'url' => '/legacy-child',
            'route_name' => null,
            'target' => '_self',
            'is_visible' => true,
            'sort_order' => 1,
            'source_key' => null,
            'source_type' => 'external_link',
            'area' => 'dashboard',
            'permission_key' => null,
        ]);

        $migration = require base_path('database/migrations/2026_10_27_000001_canonicalize_dashboard_builder_navigation.php');
        $migration->up();

        $this->assertSame(
            1,
            NavigationMenuItem::query()
                ->where('menu', 'dashboard')
                ->where('source_key', 'route:admin.profile-builder.index')
                ->count()
        );
        $this->assertDatabaseMissing('navigation_menu_items', ['id' => $canonical->id]);
        $this->assertDatabaseHas('navigation_menu_items', [
            'id' => $legacy->id,
            'source_key' => 'route:admin.profile-builder.index',
            'route_name' => 'admin.profile-builder.index',
            'url' => '/admin/profile-builder',
            'parent_id' => $website->id,
        ]);
        $this->assertDatabaseHas('navigation_menu_items', [
            'id' => $child->id,
            'parent_id' => $website->id,
        ]);
    }
}
