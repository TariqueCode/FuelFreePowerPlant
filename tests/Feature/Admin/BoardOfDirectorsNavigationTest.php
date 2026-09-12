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
}
