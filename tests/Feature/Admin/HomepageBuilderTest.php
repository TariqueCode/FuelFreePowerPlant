<?php

namespace Tests\Feature\Admin;

use App\Models\HomepageSection;
use App\Models\Permission;
use App\Models\Role;
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
        $permission = Permission::create(['name' => 'Manage website sections', 'slug' => 'website.manage']);
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
        $this->assertSame('The homepage section list is invalid.', session('errors')?->first('section_order'));
        $this->assertSame([0, 1], HomepageSection::ordered()->pluck('sort_order')->all());
    }
}
