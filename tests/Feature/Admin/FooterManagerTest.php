<?php

namespace Tests\Feature\Admin;

use App\Models\Permission;
use App\Models\Role;
use App\Models\SystemSetting;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class FooterManagerTest extends TestCase
{
    use RefreshDatabase;

    private function admin(): User
    {
        $view = Permission::firstOrCreate(['slug' => 'website.view'], ['name' => 'View website']);
        $manage = Permission::firstOrCreate(['slug' => 'website.manage'], ['name' => 'Manage website']);
        $role = Role::create(['name' => 'Footer Manager', 'slug' => 'footer-manager', 'is_system' => false]);
        $role->permissions()->sync([$view->id, $manage->id]);

        $user = User::factory()->create();
        $user->roles()->attach($role);

        return $user;
    }

    public function test_footer_manager_renders_and_is_not_homepage_builder(): void
    {
        $response = $this->actingAs($this->admin())->get(route('admin.footer.index'));

        $response->assertOk();
        $response->assertSeeText('Footer Manager');
        $response->assertSeeText('No Homepage Builder dependency.');
        $response->assertSee('name="footer[tagline]"', false);
        $response->assertSee('name="footer[address]"', false);
    }

    public function test_footer_manager_persists_global_content_and_visibility(): void
    {
        $user = $this->admin();

        $this->actingAs($user)->post(route('admin.footer.update'), [
            'footer' => [
                'tagline' => 'A cleaner future, built responsibly.',
                'technology' => 'Advanced clean energy technology',
                'office_heading' => 'Head Office',
                'address' => "Dhaka, Bangladesh\nLevel 5",
                'contact_heading' => 'Reach us',
                'email' => 'hello@example.com',
                'phone' => '+880 1000-000000',
                'website' => 'fuelfreepowerplant.com',
                'website_url' => 'https://fuelfreepowerplant.com',
                'get_in_touch_label' => 'Talk to us',
                'get_in_touch_url' => '/contact',
                'copyright_text' => 'All rights reserved.',
                'developer_prefix' => 'Built by',
                'developer_name' => 'Example Studio',
                'developer_email' => 'dev@example.com',
            ],
            'columns_enabled' => '1',
            'contact_enabled' => '1',
            'social_enabled' => '0',
            'copyright_enabled' => '1',
        ])->assertSessionHas('status');

        $this->assertDatabaseHas('system_settings', ['key' => 'footer.tagline', 'value' => 'A cleaner future, built responsibly.']);
        $this->assertDatabaseHas('system_settings', ['key' => 'footer.address', 'value' => "Dhaka, Bangladesh\nLevel 5"]);
        $this->assertDatabaseHas('system_settings', ['key' => 'design.footer.social_enabled', 'value' => '0']);
        $this->assertDatabaseHas('system_settings', ['key' => 'footer.developer_name', 'value' => 'Example Studio']);
    }
}
