<?php

namespace Tests\Feature\Admin;

use App\Models\Permission;
use App\Models\Role;
use App\Models\SystemSetting;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Cache;
use Tests\TestCase;

class SeoSettingsTest extends TestCase
{
    use RefreshDatabase;

    private function admin(): User
    {
        $permission = Permission::firstOrCreate(['slug' => 'settings.manage'], ['name' => 'Manage system settings']);
        $role = Role::create(['name' => 'SEO Manager', 'slug' => 'seo-manager', 'is_system' => false]);
        $role->permissions()->sync([$permission->id]);

        $user = User::factory()->create();
        $user->roles()->attach($role);

        return $user;
    }

    public function test_seo_settings_page_requires_settings_permission_and_renders(): void
    {
        $this->get(route('admin.settings.seo'))->assertRedirect(route('login'));

        $this->actingAs($this->admin())
            ->get(route('admin.settings.seo'))
            ->assertOk()
            ->assertSeeText('SEO & Integrations')
            ->assertSeeText('Setup Help')
            ->assertSee('Google Search Console verification');
    }

    public function test_seo_settings_are_persisted_and_invalid_identifiers_are_rejected(): void
    {
        $admin = $this->admin();

        $this->actingAs($admin)->post(route('admin.settings.seo.update'), [
            'seo' => [
                'google_verification' => 'google-token',
                'bing_verification' => 'bing-token',
                'meta_verification' => 'meta-token',
                'ga4_measurement_id' => 'G-ABC123',
                'indexnow_key' => 'Abc_1234-Key',
            ],
        ])->assertSessionHas('status');

        $this->assertDatabaseHas('system_settings', ['key' => 'seo.google_verification', 'value' => 'google-token']);
        $this->assertDatabaseHas('system_settings', ['key' => 'seo.ga4_measurement_id', 'value' => 'G-ABC123']);

        $this->actingAs($admin)->post(route('admin.settings.seo.update'), [
            'seo' => ['ga4_measurement_id' => 'not-a-ga4-id', 'indexnow_key' => 'bad key'],
        ])->assertSessionHasErrors(['seo.ga4_measurement_id', 'seo.indexnow_key']);
    }

    public function test_public_seo_baseline_is_rendered_and_robots_points_to_sitemap(): void
    {
        $this->get(route('home'))
            ->assertOk()
            ->assertSee('<meta name="description"', false)
            ->assertSee('<meta name="robots" content="index,follow', false)
            ->assertSee('<link rel="canonical" href="https://' . trim(config('fuelfree.company.domain'), '/') . '">', false)
            ->assertSee('application/ld+json', false)
            ->assertSee('"@type":"Organization"', false);

        $this->get(route('robots.txt'))
            ->assertOk()
            ->assertHeader('Content-Type', 'text/plain; charset=UTF-8')
            ->assertSeeText('User-agent: *')
            ->assertSeeText('Sitemap: https://' . trim(config('fuelfree.company.domain'), '/') . '/sitemap.xml');
    }

    public function test_sitemap_and_indexnow_endpoints_are_public_and_indexnow_key_is_exact(): void
    {
        $this->get(route('sitemap'))
            ->assertOk()
            ->assertHeader('Content-Type', 'application/xml; charset=UTF-8')
            ->assertSee('<urlset', false)
            ->assertSee('https://' . trim(config('fuelfree.company.domain'), '/') . '/', false);

        SystemSetting::updateOrCreate(['key' => 'seo.indexnow_key'], ['value' => 'Abc_1234-Key', 'is_sensitive' => false]);
        Cache::forget('fuelfree.system_settings');

        $this->get('/indexnow/Abc_1234-Key.txt')->assertOk()->assertSeeText('Abc_1234-Key');
        $this->get('/indexnow/wrong-key.txt')->assertNotFound();
    }
}
