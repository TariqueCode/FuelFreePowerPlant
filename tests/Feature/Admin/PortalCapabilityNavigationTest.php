<?php

namespace Tests\Feature\Admin;

use App\Models\Permission;
use App\Models\Role;
use App\Models\SystemSetting;
use App\Models\User;
use App\Services\DashboardNavigationService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class PortalCapabilityNavigationTest extends TestCase
{
    use RefreshDatabase;

    public function test_user_management_navigation_is_capability_aware(): void
    {
        $view = Permission::firstOrCreate(['slug'=>'users.view'], ['name'=>'View users']);
        $manage = Permission::firstOrCreate(['slug'=>'users.manage'], ['name'=>'Manage users']);
        $role = Role::create(['name'=>'User Viewer','slug'=>'user-viewer','is_system'=>false]);
        $role->permissions()->sync([$view->id]);
        $user = User::factory()->create();
        $user->roles()->attach($role);

        $this->actingAs($user)->get(route('admin.users.index'))->assertOk()->assertSee('Users');
    }

    public function test_user_viewer_cannot_access_user_mutation(): void
    {
        $view = Permission::firstOrCreate(['slug'=>'users.view'], ['name'=>'View users']);
        $manage = Permission::firstOrCreate(['slug'=>'users.manage'], ['name'=>'Manage users']);
        $role = Role::create(['name'=>'User Viewer Mutation','slug'=>'user-viewer-mutation','is_system'=>false]);
        $role->permissions()->sync([$view->id]);
        $user = User::factory()->create();
        $user->roles()->attach($role);
        $target = User::factory()->create();

        $this->actingAs($user)->patch(route('admin.users.update', $target), [
            'name' => 'Should Not Change',
            'email' => $target->email,
            'password' => '',
            'password_confirmation' => '',
        ])->assertForbidden();

        $this->assertSame($target->name, $target->fresh()->name);
    }

    public function test_user_manager_sees_add_account_navigation(): void
    {
        $view = Permission::firstOrCreate(['slug'=>'users.view'], ['name'=>'View users']);
        $manage = Permission::firstOrCreate(['slug'=>'users.manage'], ['name'=>'Manage users']);
        $role = Role::create(['name'=>'User Manager','slug'=>'user-manager','is_system'=>false]);
        $role->permissions()->sync([$view->id, $manage->id]);
        $user = User::factory()->create();
        $user->roles()->attach($role);

        $this->actingAs($user)->get(route('admin.users.index'))->assertOk()->assertSee('Users');
    }

    public function test_dashboard_navigation_restores_missing_builtin_capabilities(): void
    {
        $slugs = [
            'dashboard.view', 'website.view', 'cms.view', 'social-media.manage',
            'documents.view', 'users.view', 'audit.view', 'health.view',
            'mail.view', 'career.view', 'inquiries.view', 'settings.manage',
        ];
        $permissions = collect($slugs)->map(fn (string $slug) => Permission::firstOrCreate(
            ['slug' => $slug],
            ['name' => ucwords(str_replace(['.', '-'], ' ', $slug))]
        ));
        $role = Role::create(['name'=>'Full Portal Viewer','slug'=>'full-portal-viewer','is_system'=>false]);
        $role->permissions()->sync($permissions->pluck('id')->all());
        $user = User::factory()->create();
        $user->roles()->attach($role);

        $this->actingAs($user);
        $tree = $this->app->make(DashboardNavigationService::class)->tree();
        $topLevel = $tree->pluck('label')->values()->all();
        $nested = $tree->filter(fn ($item) => $item->source_type === 'folder')
            ->flatMap(fn ($item) => $item->children->pluck('label'))
            ->values()->all();
        $labels = collect($topLevel)->merge($nested)->values()->all();

        foreach (['Dashboard', 'Website', 'Homepage', 'Slider', 'Highlight Banner', 'Profile Builder', 'News & Notices', 'Gallery', 'Page Builder', 'Social Media', 'Menu Builder', 'Documents & Media', 'Users & Access', 'Users', 'Audit Log', 'System Health', 'Communications', 'Help Desk', 'Mail', 'Career Applications', 'Website Inquiries', 'Settings'] as $label) {
            $this->assertContains($label, $labels, "Missing dashboard navigation item: {$label}");
        }
    }

    public function test_system_settings_save_does_not_require_or_overwrite_website_owned_settings(): void
    {
        $settings = Permission::firstOrCreate(['slug'=>'settings.manage'], ['name'=>'Manage settings']);
        $role = Role::create(['name'=>'Settings Manager','slug'=>'settings-manager','is_system'=>false]);
        $role->permissions()->sync([$settings->id]);
        $user = User::factory()->create();
        $user->roles()->attach($role);

        SystemSetting::create(['key'=>'header.home_label','value'=>'Custom Home','is_sensitive'=>false]);
        SystemSetting::create(['key'=>'footer.email','value'=>'footer@example.test','is_sensitive'=>false]);

        $response = $this->actingAs($user)->post(route('admin.settings.update'), [
            'company' => [
                'name' => 'FuelFree PowerPlant',
                'domain' => 'fuelfreepowerplant.com',
                'tagline' => 'Cleaner energy',
                'timezone' => 'Asia/Dhaka',
            ],
            'storage' => ['quota_gib' => 50],
            'uploads' => [
                'max_mb'=>50,'career_max_mb'=>50,'documents_max_mb'=>50,'gallery_max_mb'=>50,
                'sliders_max_mb'=>50,'popups_max_mb'=>50,'content_media_max_mb'=>100,
            ],
        ]);

        $response->assertSessionHas('status');
        $this->assertSame('Cleaner energy', SystemSetting::where('key','company.tagline')->value('value'));
        $this->assertSame('Custom Home', SystemSetting::where('key','header.home_label')->value('value'));
        $this->assertSame('footer@example.test', SystemSetting::where('key','footer.email')->value('value'));
    }

}
