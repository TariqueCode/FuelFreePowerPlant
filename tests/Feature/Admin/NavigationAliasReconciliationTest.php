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
        NavigationMenuItem::query()
            ->where('menu', 'dashboard')
            ->where(function ($query): void {
                $query->whereIn('source_key', [
                    'route:admin.management.index',
                    'route:admin.profile-builder.index',
                ])->orWhereIn('route_name', [
                    'admin.management.index',
                    'admin.profile-builder.index',
                ]);
            })
            ->delete();

        $website = NavigationMenuItem::query()
            ->where('menu', 'dashboard')
            ->where('area', 'dashboard')
            ->where('source_type', 'folder')
            ->where('label', 'Website')
            ->where('is_visible', true)
            ->first();

        if (! $website) {
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
        }

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

        $cycle = NavigationMenuItem::create([
            'menu' => 'dashboard',
            'parent_id' => null,
            'label' => 'Broken Cycle',
            'url' => '#',
            'route_name' => null,
            'target' => '_self',
            'is_visible' => true,
            'sort_order' => 90,
            'source_key' => null,
            'source_type' => 'folder',
            'area' => 'dashboard',
            'permission_key' => null,
        ]);
        \Illuminate\Support\Facades\DB::table('navigation_menu_items')
            ->where('id', $cycle->id)
            ->update(['parent_id' => $cycle->id]);

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
        $this->assertDatabaseHas('navigation_menu_items', [
            'id' => $cycle->id,
            'parent_id' => null,
        ]);
    }
}
