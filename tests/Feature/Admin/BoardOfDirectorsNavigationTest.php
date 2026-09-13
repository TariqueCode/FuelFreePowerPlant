<?php

namespace Tests\Feature\Admin;

use App\Models\HomepageSection;
use App\Models\ManagementProfileFolder;
use App\Models\NavigationMenuItem;
use App\Models\Role;
use App\Models\User;
use App\Services\DashboardNavigationService;
use App\Services\PublicNavigationService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class BoardOfDirectorsNavigationTest extends TestCase
{
    use RefreshDatabase;

    public function test_board_of_directors_is_a_live_route_navigation_item(): void
    {
        $item = NavigationMenuItem::query()->where('menu', 'main')->where('source_key', 'route:management')->firstOrFail();

        $this->assertSame('Board of Directors', $item->displayLabel());
        $this->assertSame('route', $item->source_type);
        $this->assertSame('/management', $item->url);
        $this->assertSame('management', $item->route_name);
        $this->assertSame(0, $item->children()->count());
        $this->assertSame(0, NavigationMenuItem::query()->where('menu', 'main')->where('source_type', 'folder')->where('source_key', 'like', 'management_folder:%')->count());
    }

    public function test_public_navigation_exposes_management_as_board_of_directors(): void
    {
        $tree = app(PublicNavigationService::class)->tree('main');
        $item = $tree->firstWhere('source_key', 'route:management');

        $this->assertNotNull($item);
        $this->assertSame('Board of Directors', $item->displayLabel());
        $this->assertSame('/management', $item->url);
        $this->assertSame('route', $item->source_type);
    }

    public function test_homepage_management_section_still_uses_board_of_directors_profiles(): void
    {
        $folder = ManagementProfileFolder::query()->where('slug', 'board-of-directors')->firstOrFail();
        $section = HomepageSection::query()->where('key', 'management')->firstOrFail();
        $settings = is_array($section->settings) ? $section->settings : [];

        $this->assertTrue((bool) $section->is_enabled);
        $this->assertSame($folder->id, (int) ($settings['folder_id'] ?? 0));
    }

    public function test_dashboard_navigation_resolves_real_builder_routes_and_keeps_required_shell_items(): void
    {
        $admin = User::factory()->create();
        $admin->roles()->attach(Role::query()->where('slug', 'super-admin')->firstOrFail());
        $this->actingAs($admin);

        $tree = app(DashboardNavigationService::class)->tree('dashboard');
        $labels = $tree->pluck('label')->map(fn ($label) => trim((string) $label))->all();

        $this->assertContains('Dashboard', $labels);
        $website = $tree->firstWhere('label', 'Website');
        $this->assertNotNull($website);

        $childLabels = $website->children->pluck('label')->map(fn ($label) => trim((string) $label))->all();
        $this->assertContains('Profile Builder', $childLabels);
        $this->assertContains('Page Builder', $childLabels);

        $profile = $website->children->firstWhere('source_key', 'route:admin.management.index');
        $page = $website->children->firstWhere('source_key', 'route:admin.cms.index');
        $this->assertNotNull($profile);
        $this->assertNotNull($page);
        $this->assertSame('/admin/management', $profile->url);
        $this->assertSame('/admin/cms', $page->url);
    }
}
