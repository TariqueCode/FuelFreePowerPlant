<?php

namespace Tests\Feature;

use App\Models\ManagementProfileFolder;
use App\Models\SiteContentItem;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ManagementProfileViewTest extends TestCase
{
    use RefreshDatabase;

    public function test_profile_folder_slug_cannot_claim_a_reserved_public_route(): void
    {
        $controller = app(\\App\\Http\\Controllers\\Admin\\ManagementController::class);
        $method = new \\ReflectionMethod($controller, 'uniqueFolderSlug');
        $method->setAccessible(true);

        $this->assertSame('about-us-2', $method->invoke($controller, 'About Us'));
        $this->assertSame('board-of-directors', $method->invoke($controller, 'Board of Directors'));
    }

    public function test_folder_profile_cards_have_a_working_profile_view_target(): void
    {
        $folder = ManagementProfileFolder::create([
            'name' => 'Test Board of Directors',
            'slug' => 'test-board-of-directors',
            'status' => 'published',
            'sort_order' => 1,
        ]);

        $member = SiteContentItem::create([
            'type' => 'management',
            'management_profile_folder_id' => $folder->id,
            'title' => 'Test Director',
            'slug' => 'test-director',
            'designation' => 'Director',
            'content' => 'Leadership profile content.',
            'status' => 'published',
            'sort_order' => 1,
            'published_at' => now(),
        ]);

        $response = $this->get('/'.$folder->slug);

        $response->assertOk();
        $response->assertSee('data-profile-target="profile-'.$member->id.'"', false);
        $response->assertSee('id="profile-'.$member->id.'"', false);
        $response->assertSee('Leadership profile content.');
    }
}
