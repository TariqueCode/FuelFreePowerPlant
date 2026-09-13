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
            ->where('menu', 'dashboard')
            ->where('area', 'dashboard')
            ->whereNull('parent_id')
            ->orderBy('sort_order')
            ->get();

        $this->assertSame(['Dashboard', 'Website', 'Users & Access', 'Communications', 'Settings'], $roots->pluck('label')->all());
        $this->assertSame(['fa-house', 'fa-globe', 'fa-users-gear', 'fa-comments', 'fa-sliders'], $roots->pluck('icon')->all());

        $website = $roots->firstWhere('label', 'Website');
        $children = NavigationMenuItem::query()
            ->where('menu', 'dashboard')
            ->where('area', 'dashboard')
            ->where('parent_id', $website->id)
            ->orderBy('sort_order')
            ->get();

        $this->assertSame(
            ['Homepage', 'Slider', 'Highlight Banner', 'Profile Builder', 'News & Event', 'Gallery', 'Page Builder', 'Social Media', 'Menu Builder', 'Documents & Media', 'Footer Manager'],
            $children->pluck('label')->all()
        );
        $this->assertSame(
            ['fa-house-chimney', 'fa-images', 'fa-rectangle-ad', 'fa-user-tie', 'fa-newspaper', 'fa-images', 'fa-file-lines', 'fa-share-nodes', 'fa-sitemap', 'fa-folder-open', 'fa-window-maximize'],
            $children->pluck('icon')->all()
        );

        $this->assertDatabaseHas('navigation_menu_items', [
            'menu' => 'dashboard',
            'area' => 'dashboard',
            'label' => 'Footer Manager',
            'parent_id' => $website->id,
            'route_name' => 'admin.footer.index',
            'source_key' => 'route:admin.footer.index',
            'icon' => 'fa-window-maximize',
        ]);
    }
}
