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

class HomepageWelcomeSettingsTest extends TestCase
{
    use RefreshDatabase;

    private function manager(): User
    {
        $permission = Permission::firstOrCreate(['slug' => 'website.manage'], ['name' => 'Manage website']);
        $role = Role::create(['name' => 'Homepage Manager', 'slug' => 'homepage-manager', 'is_system' => false]);
        $role->permissions()->sync([$permission->id]);
        $user = User::factory()->create();
        $user->roles()->attach($role);

        HomepageSection::firstOrCreate(['key' => 'welcome'], [
            'key' => 'welcome',
            'label' => 'Company Introduction',
            'description' => 'Welcome content',
            'source_type' => 'cms_page',
            'is_enabled' => true,
            'sort_order' => 1,
            'settings' => [],
        ]);

        return $user;
    }

    private function managementConfiguration(): array
    {
        $folder = ManagementProfileFolder::create([
            'name' => 'Welcome Test Folder',
            'slug' => 'welcome-test-folder-' . uniqid(),
            'status' => 'published',
            'sort_order' => 1,
        ]);

        $profile = SiteContentItem::create([
            'type' => 'management',
            'management_profile_folder_id' => $folder->id,
            'title' => 'Welcome Management Profile',
            'slug' => 'welcome-management-profile-' . uniqid(),
            'designation' => 'Director',
            'phone' => '+8801700000000',
            'email' => 'welcome@example.com',
            'content' => 'Leadership message.',
            'status' => 'published',
            'sort_order' => 1,
            'published_at' => now(),
        ]);

        return ['folder_id' => $folder->id, 'ids' => [$profile->id]];
    }

    public function test_welcome_message_can_be_fully_configured(): void
    {
        $user = $this->manager();
        $management = $this->managementConfiguration();
        $order = HomepageSection::query()->ordered()->pluck('key')->all();
        $sections = array_fill_keys($order, '1');

        $this->actingAs($user)->post(route('admin.homepage-builder.update'), [
            'section_order' => $order,
            'sections' => $sections,
            'settings' => [
                'management' => [
                    'folder_id' => $management['folder_id'],
                    'mode' => 'selected',
                    'ids' => $management['ids'],
                    'layout' => 'left',
                ],
                'welcome' => [
                    'eyebrow' => 'FUEL FREE POWER PLANT LIMITED',
                    'signoff' => 'FUEL FREE POWER PLANT LIMITED — Powering a cleaner, smarter future.',
                    'title' => 'Rethinking the Future of Electricity',
                    'content' => 'Energy Without the Conventional Fuel Dependency. The world is entering a new era of energy.',
                    'preview_words' => 30,
                    'more_words' => 120,
                    'show_full' => '1',
                    'layout' => 'center',
                ],
            ],
        ])->assertSessionHas('status');

        $settings = HomepageSection::where('key', 'welcome')->value('settings');
        $this->assertSame('FUEL FREE POWER PLANT LIMITED', $settings['eyebrow']);
        $this->assertSame('FUEL FREE POWER PLANT LIMITED — Powering a cleaner, smarter future.', $settings['signoff']);
        $this->assertSame('Rethinking the Future of Electricity', $settings['title']);
        $this->assertSame(30, $settings['preview_words']);
        $this->assertSame(120, $settings['more_words']);
        $this->assertTrue($settings['show_full']);
        $this->assertSame('center', $settings['layout']);
    }
}
