<?php

namespace Tests\Feature\Admin;

use App\Models\HomepageSection;
use App\Models\ManagementProfileFolder;
use App\Models\Permission;
use App\Models\Role;
use App\Models\SiteContentItem;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class HomepageBuilderTest extends TestCase
{
    use RefreshDatabase;

    private function manager(string $name = 'Homepage Manager'): User
    {
        $user = User::factory()->create();
        $role = Role::create(['name' => $name, 'slug' => strtolower(str_replace(' ', '-', $name)), 'is_system' => false]);
        $permission = Permission::firstOrCreate(['slug' => 'website.manage'], ['name' => 'Manage website sections']);
        $role->permissions()->attach($permission);
        $user->roles()->attach($role);

        return $user;
    }

    private function managementConfiguration(array $titles = ['Management 0']): array
    {
        $folder = ManagementProfileFolder::create([
            'name' => 'Homepage Test Folder',
            'slug' => 'homepage-test-folder-' . uniqid(),
            'status' => 'published',
            'sort_order' => 1,
        ]);

        $ids = [];
        foreach ($titles as $index => $title) {
            $profile = SiteContentItem::create([
                'type' => 'management',
                'management_profile_folder_id' => $folder->id,
                'title' => $title,
                'slug' => strtolower(trim(preg_replace('/[^a-z0-9]+/i', '-', $title), '-')) . '-' . $index . '-' . uniqid(),
                'designation' => 'Director',
                'phone' => '+8801700000000',
                'email' => "management{$index}@example.com",
                'content' => 'Leadership message.',
                'status' => 'published',
                'sort_order' => $index + 1,
                'published_at' => now(),
            ]);
            $ids[] = $profile->id;
        }

        return ['folder_id' => $folder->id, 'ids' => $ids];
    }

    private function managementSettings(array $configuration): array
    {
        return [
            'folder_id' => $configuration['folder_id'],
            'mode' => 'selected',
            'ids' => $configuration['ids'],
            'layout' => 'left',
        ];
    }

    public function test_invalid_homepage_order_is_rejected_without_mutating_sections(): void
    {
        $user = $this->manager('Test Admin');
        HomepageSection::query()->delete();
        $sections = collect([
            HomepageSection::create(['key' => 'hero', 'label' => 'Hero', 'is_enabled' => true, 'sort_order' => 0]),
            HomepageSection::create(['key' => 'news', 'label' => 'News', 'is_enabled' => true, 'sort_order' => 1]),
        ]);
        $management = $this->managementConfiguration();

        $response = $this->actingAs($user)->post(route('admin.homepage-builder.update'), [
            'section_order' => ['hero', 'hero'],
            'sections' => ['hero' => '1'],
            'settings' => ['management' => $this->managementSettings($management)],
        ]);

        $response->assertRedirect();
        $response->assertSessionHasErrors(['section_order' => 'The homepage section list is invalid.']);
        $this->assertSame([0, 1], HomepageSection::ordered()->pluck('sort_order')->all());
    }

    public function test_highlight_section_can_be_disabled_without_deleting_the_highlight_module(): void
    {
        $user = $this->manager('Website Manager');
        HomepageSection::query()->delete();

        $section = HomepageSection::create([
            'key' => 'highlight',
            'label' => 'Homepage Highlight',
            'is_enabled' => true,
            'sort_order' => 0,
        ]);
        $management = $this->managementConfiguration();

        $response = $this->actingAs($user)->post(route('admin.homepage-builder.update'), [
            'section_order' => ['highlight'],
            'sections' => [],
            'settings' => ['management' => $this->managementSettings($management)],
        ]);

        $response->assertRedirect();
        $this->assertFalse($section->fresh()->is_enabled);
        $this->assertDatabaseHas('homepage_sections', ['key' => 'highlight']);
    }

    public function test_homepage_management_folder_selection_controls_the_profiles_rendered(): void
    {
        SiteContentItem::query()->where('type', 'management')->delete();
        $configuration = $this->managementConfiguration(['Management 0', 'Management 1', 'Management 2', 'Management 3']);

        HomepageSection::query()->where('key', 'management')->update([
            'is_enabled' => true,
            'settings' => $this->managementSettings(array_slice($configuration, 0, 0) + [
                'folder_id' => $configuration['folder_id'],
                'ids' => array_slice($configuration['ids'], 0, 2),
            ]),
        ]);

        $response = $this->get(route('home'));

        $response->assertOk();
        $response->assertSee('Management 0');
        $response->assertSee('Management 1');
        $response->assertDontSee('Management 2');
        $response->assertDontSee('Management 3');
    }

    public function test_homepage_management_selected_profiles_respect_folder_and_order(): void
    {
        $configuration = $this->managementConfiguration(['Selected First', 'Selected Second', 'Unselected Profile']);

        HomepageSection::query()->where('key', 'management')->update([
            'is_enabled' => true,
            'settings' => [
                'folder_id' => $configuration['folder_id'],
                'mode' => 'selected',
                'ids' => [$configuration['ids'][1]],
                'layout' => 'left',
            ],
        ]);

        $response = $this->get(route('home'));

        $response->assertOk();
        $response->assertSee('Selected Second');
        $response->assertDontSee('Selected First');
        $response->assertDontSee('Unselected Profile');
    }

    public function test_homepage_section_alignment_is_persisted_and_rendered(): void
    {
        $user = $this->manager('Layout Admin');
        HomepageSection::query()->delete();
        HomepageSection::create([
            'key' => 'management',
            'label' => 'Board of Directors',
            'is_enabled' => true,
            'sort_order' => 0,
            'settings' => [],
        ]);
        $management = $this->managementConfiguration(['Alignment Profile']);

        $response = $this->actingAs($user)->post(route('admin.homepage-builder.update'), [
            'section_order' => ['management'],
            'sections' => ['management' => '1'],
            'settings' => ['management' => [
                'folder_id' => $management['folder_id'],
                'mode' => 'selected',
                'ids' => $management['ids'],
                'layout' => 'left',
            ]],
        ]);

        $response->assertRedirect();
        $this->assertSame('left', HomepageSection::query()->where('key', 'management')->value('settings')['layout']);

        $home = $this->get(route('home'));
        $home->assertOk();
        $home->assertSee('home-section-management section-layout-left', false);
        $home->assertDontSee('home-section-management section-layout-right', false);
    }
}
