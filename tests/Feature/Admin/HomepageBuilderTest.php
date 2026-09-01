<?php

namespace Tests\Feature\Admin;

use App\Models\HomepageSection;
use App\Models\Permission;
use App\Models\Role;
use App\Models\SiteContentItem;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class HomepageBuilderTest extends TestCase
{
    use RefreshDatabase;

    public function test_invalid_homepage_order_is_rejected_without_mutating_sections(): void
    {
        $user = User::factory()->create();
        $role = Role::create(['name' => 'Test Admin', 'slug' => 'test-admin', 'is_system' => false]);
        $permission = Permission::firstOrCreate(['slug' => 'website.manage'], ['name' => 'Manage website sections']);
        $role->permissions()->attach($permission);
        $user->roles()->attach($role);
        HomepageSection::query()->delete();
        $sections = collect([
            HomepageSection::create(['key' => 'hero', 'label' => 'Hero', 'is_enabled' => true, 'sort_order' => 0]),
            HomepageSection::create(['key' => 'news', 'label' => 'News', 'is_enabled' => true, 'sort_order' => 1]),
        ]);

        $response = $this->actingAs($user)->post(route('admin.homepage-builder.update'), [
            'section_order' => ['hero', 'hero'],
            'sections' => ['hero' => '1'],
        ]);

        $response->assertRedirect();
        $response->assertSessionHasErrors(['section_order' => 'The homepage section list is invalid.']);
        $this->assertSame([0, 1], HomepageSection::ordered()->pluck('sort_order')->all());
    }
    public function test_highlight_section_can_be_disabled_without_deleting_the_highlight_module(): void
    {
        $user = User::factory()->create();
        $role = Role::create(['name' => 'Website Manager', 'slug' => 'website-manager', 'is_system' => false]);
        $permission = Permission::firstOrCreate(['slug' => 'website.manage'], ['name' => 'Manage website sections']);
        $role->permissions()->attach($permission);
        $user->roles()->attach($role);
        HomepageSection::query()->delete();

        $section = HomepageSection::create([
            'key' => 'highlight',
            'label' => 'Homepage Highlight',
            'is_enabled' => true,
            'sort_order' => 0,
        ]);

        $response = $this->actingAs($user)->post(route('admin.homepage-builder.update'), [
            'section_order' => ['highlight'],
            'sections' => [],
        ]);

        $response->assertRedirect();
        $this->assertFalse($section->fresh()->is_enabled);
        $this->assertDatabaseHas('homepage_sections', ['key' => 'highlight']);
    }


    public function test_homepage_management_limit_controls_the_number_of_profiles_rendered(): void
    {
        HomepageSection::query()->where('key', 'management')->update([
            'is_enabled' => true,
            'settings' => ['limit' => 2, 'mode' => 'latest', 'layout' => 'left'],
        ]);

        foreach (['Chairman', 'Managing Director', 'Director', 'Advisor'] as $index => $designation) {
            SiteContentItem::create([
                'type' => 'management',
                'title' => "Management {$index}",
                'slug' => "management-{$index}",
                'designation' => $designation,
                'phone' => '+8801700000000',
                'email' => "management{$index}@example.com",
                'content' => 'Leadership message.',
                'status' => 'published',
                'sort_order' => $index + 1,
                'published_at' => now()->subMinutes($index),
            ]);
        }

        $response = $this->get(route('home'));

        $debugSection = HomepageSection::query()->where('key', 'management')->first();
        $debugProfiles = SiteContentItem::published()->where('type', 'management')->orderBy('sort_order')->orderBy('title')->get(['id','title','published_at','status']);
        fwrite(STDERR, "\nDEBUG management settings=".json_encode($debugSection?->settings)." raw=".json_encode($debugSection?->getRawOriginal('settings'))." profiles=".$debugProfiles->toJson()."\n");

        $response->assertOk();
        $response->assertSee('Management 0');
        $response->assertSee('Management 1');
        $response->assertDontSee('Management 2');
        $response->assertDontSee('Management 3');
    }

    public function test_homepage_management_selected_mode_respects_selected_profiles_and_limit(): void
    {
        $first = SiteContentItem::create([
            'type' => 'management', 'title' => 'Selected First', 'slug' => 'selected-first',
            'designation' => 'Chairman', 'phone' => '+8801700000001', 'status' => 'published',
            'sort_order' => 1, 'published_at' => now(),
        ]);
        $second = SiteContentItem::create([
            'type' => 'management', 'title' => 'Selected Second', 'slug' => 'selected-second',
            'designation' => 'Director', 'phone' => '+8801700000002', 'status' => 'published',
            'sort_order' => 2, 'published_at' => now(),
        ]);
        SiteContentItem::create([
            'type' => 'management', 'title' => 'Unselected Profile', 'slug' => 'unselected-profile',
            'designation' => 'Advisor', 'phone' => '+8801700000003', 'status' => 'published',
            'sort_order' => 3, 'published_at' => now(),
        ]);

        HomepageSection::query()->where('key', 'management')->update([
            'is_enabled' => true,
            'settings' => ['limit' => 1, 'mode' => 'selected', 'ids' => [$second->id, $first->id], 'layout' => 'left'],
        ]);

        $response = $this->get(route('home'));

        $response->assertOk();
        $response->assertSee('Selected Second');
        $response->assertDontSee('Selected First');
        $response->assertDontSee('Unselected Profile');
    }

}
