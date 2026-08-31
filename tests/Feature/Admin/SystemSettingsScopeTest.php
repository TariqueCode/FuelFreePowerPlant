<?php

namespace Tests\Feature\Admin;

use App\Models\Permission;
use App\Models\Role;
use App\Models\SystemSetting;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class SystemSettingsScopeTest extends TestCase
{
    use RefreshDatabase;

    private function admin(): User
    {
        $permission = Permission::firstOrCreate(['slug' => 'settings.manage'], ['name' => 'Manage system settings']);
        $role = Role::create(['name' => 'Settings Manager', 'slug' => 'settings-manager', 'is_system' => false]);
        $role->permissions()->sync([$permission->id]);

        $user = User::factory()->create();
        $user->roles()->attach($role);

        return $user;
    }

    public function test_system_settings_does_not_expose_homepage_or_mail_configuration(): void
    {
        $response = $this->actingAs($this->admin())->get(route('admin.settings'));

        $response->assertOk();
        $response->assertDontSee('Homepage sections');
        $response->assertDontSee('Contact &amp; Career email login');
        $response->assertDontSee('Default upload limit');
        $response->assertSee('Storage &amp; upload policy');
    }

    public function test_mail_fields_cannot_create_mailbox_settings_from_system_settings(): void
    {
        $this->actingAs($this->admin())->post(route('admin.settings.update'), [
            'company' => [
                'name' => 'FuelFree PowerPlant',
                'domain' => 'fuelfreepowerplant.com',
                'tagline' => 'Powering a cleaner, smarter future.',
                'timezone' => 'Asia/Dhaka',
            ],
            'storage' => ['quota_gib' => 50],
            'uploads' => [
                'career_max_mb' => 50,
                'documents_max_mb' => 50,
                'gallery_max_mb' => 50,
                'content_media_max_mb' => 100,
            ],
            'mail' => [
                'contact_email' => 'attacker@fuelfreepowerplant.com',
                'career_email' => 'attacker2@fuelfreepowerplant.com',
            ],
        ])->assertSessionHas('status');

        $this->assertDatabaseMissing('system_settings', ['key' => 'mail.contact_account_id']);
        $this->assertDatabaseMissing('system_settings', ['key' => 'mail.career_account_id']);
    }
}
