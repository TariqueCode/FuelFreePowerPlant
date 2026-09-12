<?php

namespace Tests\Feature\Admin;

use App\Models\HomepageSection;
use App\Models\ManagementProfileFolder;
use App\Models\SiteContentItem;
use App\Services\NavigationSourceRegistry;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class BoardOfDirectorsIntegrationTest extends TestCase
{
    use RefreshDatabase;

    private function boardOfDirectorsFolder(): ManagementProfileFolder
    {
        return ManagementProfileFolder::query()->firstOrCreate(
            ['slug' => 'board-of-directors'],
            [
                'name' => 'Board of Directors',
                'status' => 'published',
                'sort_order' => 1,
            ]
        );
    }

    public function test_profile_folder_resolves_as_board_of_directors_navigation_source(): void
    {
        $folder = $this->boardOfDirectorsFolder();

        $source = app(NavigationSourceRegistry::class)->resolveAny('management_folder:'.$folder->id, 'public');

        $this->assertSame('Board of Directors', $source['label']);
        $this->assertSame('folder', $source['type']);
        $this->assertSame('/board-of-directors', $source['url']);
        $this->assertNull($source['route_name']);
    }

    public function test_homepage_management_section_uses_published_board_profiles_when_configured(): void
    {
        $folder = $this->boardOfDirectorsFolder();

        $profile = SiteContentItem::create([
            'type' => 'management',
            'management_profile_folder_id' => $folder->id,
            'title' => 'Test Director',
            'slug' => 'test-director',
            'designation' => 'Director',
            'excerpt' => 'Director',
            'phone' => '+880 1700000000',
            'status' => 'published',
            'published_at' => now(),
            'sort_order' => 1,
        ]);

        HomepageSection::query()->updateOrCreate(
            ['key' => 'management'],
            [
                'is_enabled' => true,
                'sort_order' => 3,
                'settings' => [
                    'folder_id' => $folder->id,
                    'mode' => 'selected',
                    'ids' => [$profile->id],
                    'limit' => 4,
                    'layout' => 'left',
                ],
            ]
        );

        $response = $this->get('/');

        $response->assertOk();
        $response->assertSee('Test Director');
    }
}
