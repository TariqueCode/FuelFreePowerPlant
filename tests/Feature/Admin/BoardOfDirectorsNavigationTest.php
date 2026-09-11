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

    public function test_board_of_directors_is_a_canonical_folder_navigation_item(): void
    {
        $folder = ManagementProfileFolder::query()->where('slug', 'board-of-directors')->firstOrFail();
        $item = NavigationMenuItem::query()->where('menu', 'main')->where('source_key', 'management_folder:'.$folder->id)->firstOrFail();

        $this->assertSame('Board of Directors', $item->displayLabel());
        $this->assertSame('folder', $item->source_type);
        $this->assertSame('/board-of-directors', $item->url);
        $this->assertNull($item->route_name);
    }

    public function test_public_navigation_accepts_management_profile_folders(): void
    {
        $folder = ManagementProfileFolder::query()->where('slug', 'board-of-directors')->firstOrFail();
        $tree = app(PublicNavigationService::class)->tree('main');
        $item = $tree->firstWhere('source_key', 'management_folder:'.$folder->id);

        $this->assertNotNull($item);
        $this->assertSame('Board of Directors', $item->displayLabel());
        $this->assertSame('/board-of-directors', $item->url);
    }

    public function test_homepage_management_section_uses_board_of_directors_folder(): void
    {
        $folder = ManagementProfileFolder::query()->where('slug', 'board-of-directors')->firstOrFail();
        $section = HomepageSection::query()->where('key', 'management')->firstOrFail();
        $settings = is_array($section->settings) ? $section->settings : [];

        $this->assertTrue((bool) $section->is_enabled);
        $this->assertSame($folder->id, (int) ($settings['folder_id'] ?? 0));
    }
}
