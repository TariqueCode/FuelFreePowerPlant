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

    public function test_profile_folder_is_not_exposed_as_a_navigation_source(): void
    {
        $folder = $this->boardOfDirectorsFolder();

        $source = app(NavigationSourceRegistry::class)->resolveAny('management_folder:'.$folder->id, 'public');

        $this->assertNull($source);
    }

    public function test_board_of_directors_navigation_uses_the_live_management_route(): void
    {
        $source = app(NavigationSourceRegistry::class)->resolveAny('route:management', 'public');

        $this->assertSame('Board of Directors', $source['label']);
        $this->assertSame('route', $source['type']);
        $this->assertSame('/management', $source['url']);
        $this->assertSame('management', $source['route_name']);
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
