<?php

namespace Tests\Feature\Admin;

use App\Models\Permission;
use App\Models\Role;
use App\Models\SystemSetting;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class SettingsIntegrityTest extends TestCase
{
    use RefreshDatabase;

    private function admin(): User
    {
        $permission = Permission::firstOrCreate(['slug' => 'settings.manage'], ['name' => 'Manage Settings']);
        $role = Role::create(['name' => 'Settings QA', 'slug' => 'settings-qa', 'is_system' => false]);
        $role->permissions()->attach($permission->id);
        $user = User::factory()->create();
        $user->roles()->attach($role);

        return $user;
    }

    public function test_settings_update_persists_platform_identity_and_upload_policies(): void
    {
        $response = $this->actingAs($this->admin())->post(route('admin.settings.update'), [
            'company' => [
                'name' => 'FuelFree Power Plant Limited',
                'domain' => 'fuelfreepowerplant.com',
                'tagline' => 'Powering a cleaner future.',
                'timezone' => 'Asia/Dhaka',
            ],
            'storage' => ['quota_gib' => 80],
            'uploads' => [
                'career_max_mb' => 20,
                'documents_max_mb' => 40,
                'gallery_max_mb' => 60,
                'content_media_max_mb' => 90,
            ],
        ]);

        $response->assertRedirect();
        $this->assertDatabaseHas('system_settings', ['key' => 'company.name', 'value' => 'FuelFree Power Plant Limited']);
        $this->assertDatabaseHas('system_settings', ['key' => 'company.domain', 'value' => 'fuelfreepowerplant.com']);
        $this->assertDatabaseHas('system_settings', ['key' => 'storage.quota_gib', 'value' => '80']);
        $this->assertDatabaseHas('system_settings', ['key' => 'uploads.content_media_max_mb', 'value' => '90']);
    }

    public function test_settings_index_reads_saved_global_values(): void
    {
        SystemSetting::updateOrCreate(['key' => 'company.name'], ['value' => 'Configured Company', 'is_sensitive' => false]);

        $this->actingAs($this->admin())
            ->get(route('admin.settings'))
            ->assertOk()
            ->assertSee('Configured Company', false);
    }
}
