<?php

namespace Tests\Feature\Admin;

use App\Models\NavigationMenuItem;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AdminDashboardNavigationTest extends TestCase
{
    use RefreshDatabase;

    public function test_professional_dashboard_navigation_shell_and_icons_are_restored(): void
    {
        $roots = NavigationMenuItem::query()
            ->where('menu', 'dashboard')->where('area', 'dashboard')->whereNull('parent_id')
            ->orderBy('sort_order')->get();

        foreach ([
            'Dashboard' => 'fa-house',
            'Website' => 'fa-globe',
            'Users & Access' => 'fa-users-gear',
            'Communications' => 'fa-comments',
            'Settings' => 'fa-sliders',
        ] as $label => $icon) {
            $item = $roots->firstWhere('label', $label);
            $this->assertNotNull($item, "Missing dashboard root item: {$label}");
            $this->assertSame($icon, $item->icon, "Wrong icon for dashboard root item: {$label}");
        }

        $website = $roots->firstWhere('label', 'Website');
        $this->assertNotNull($website);

        $children = NavigationMenuItem::query()
            ->where('menu', 'dashboard')->where('area', 'dashboard')->where('parent_id', $website->id)
            ->get()->keyBy('label');

        foreach ([
            'Homepage' => 'fa-house-chimney',
            'Slider' => 'fa-images',
            'Highlight Banner' => 'fa-rectangle-ad',
            'Profile Builder' => 'fa-user-tie',
            'News & Event' => 'fa-newspaper',
            'Gallery' => 'fa-images',
            'Page Builder' => 'fa-file-lines',
            'Social Media' => 'fa-share-nodes',
            'Menu Builder' => 'fa-sitemap',
            'Documents & Media' => 'fa-folder-open',
        ] as $label => $icon) {
            $item = $children->get($label);
            $this->assertNotNull($item, "Missing Website navigation item: {$label}");
            $this->assertSame($icon, $item->icon, "Wrong icon for Website navigation item: {$label}");
        }

        $this->assertDatabaseHas('navigation_menu_items', [
            'menu' => 'dashboard', 'area' => 'dashboard', 'label' => 'Footer Manager',
            'parent_id' => $website->id, 'route_name' => 'admin.footer.index',
            'source_key' => 'route:admin.footer.index', 'icon' => 'fa-window-maximize',
        ]);
    }
}
