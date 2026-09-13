<?php

namespace Tests\Feature\Admin;

use App\Models\HomepageSection;
use App\Models\ManagementProfileFolder;
use App\Models\NavigationMenuItem;
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

    public function test_footer_manager_is_nested_under_website_navigation(): void
    {
        $website = NavigationMenuItem::query()
            ->where('menu', 'dashboard')
            ->where('area', 'dashboard')
            ->whereNull('parent_id')
            ->where('source_type', 'folder')
            ->where(function ($query): void {
                $query->where('label', 'Website')
                    ->orWhere('source_key', 'folder:website')
                    ->orWhere('source_key', 'dashboard:website');
            })
            ->firstOrFail();

        $footer = NavigationMenuItem::query()
            ->where('menu', 'dashboard')
            ->where('area', 'dashboard')
            ->where('parent_id', $website->id)
            ->where('route_name', 'admin.footer.index')
            ->firstOrFail();

        $this->assertSame('Footer Manager', $footer->displayLabel());
        $this->assertSame('/admin/footer', $footer->url);
        $this->assertSame('route', $footer->source_type);
        $this->assertSame('route:admin.footer.index', $footer->source_key);
        $this->assertTrue((bool) $footer->is_visible);
        $this->assertSame('website.view', $footer->permission_key);
        $this->assertSame(1, NavigationMenuItem::query()
            ->where('menu', 'dashboard')
            ->where('area', 'dashboard')
            ->where('route_name', 'admin.footer.index')
            ->count());
    }
}
