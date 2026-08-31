<?php

namespace Tests\Feature\Admin;

use App\Models\HomepageSection;
use App\Models\Permission;
use App\Models\Role;
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

    public function test_welcome_message_can_be_fully_configured(): void
    {
        $user = $this->manager();

        $this->actingAs($user)->post(route('admin.homepage-builder.update'), [
            'section_order' => ['welcome'],
            'sections' => ['welcome' => '1'],
            'settings' => [
                'welcome' => [
                    'eyebrow' => 'FUEL FREE POWER PLANT LIMITED',
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
        $this->assertSame('Rethinking the Future of Electricity', $settings['title']);
        $this->assertSame(30, $settings['preview_words']);
        $this->assertSame(120, $settings['more_words']);
        $this->assertTrue($settings['show_full']);
        $this->assertSame('center', $settings['layout']);
    }
}
