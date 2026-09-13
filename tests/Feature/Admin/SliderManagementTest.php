<?php

namespace Tests\Feature\Admin;

use App\Models\Permission;
use App\Models\Role;
use App\Models\SiteSlider;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class SliderManagementTest extends TestCase
{
    use RefreshDatabase;

    private function websiteManager(): User
    {
        $view = Permission::firstOrCreate(['slug' => 'website.view'], ['name' => 'View website sections']);
        $manage = Permission::firstOrCreate(['slug' => 'website.manage'], ['name' => 'Manage website sections']);
        $role = Role::create(['name' => 'Slider QA Manager', 'slug' => 'slider-qa-manager', 'is_system' => false]);
        $role->permissions()->sync([$view->id, $manage->id]);

        $user = User::factory()->create();
        $user->roles()->attach($role);

        return $user;
    }

    public function test_slider_index_exposes_clear_status_counts(): void
    {
        $user = $this->websiteManager();
        $now = Carbon::now();

        SiteSlider::create(['title' => 'Live', 'image_path' => 'site-sliders/live.jpg', 'sort_order' => 1, 'is_published' => true]);
        SiteSlider::create(['title' => 'Scheduled', 'image_path' => 'site-sliders/scheduled.jpg', 'sort_order' => 2, 'is_published' => true, 'starts_at' => $now->copy()->addDay()]);
        SiteSlider::create(['title' => 'Draft', 'image_path' => 'site-sliders/draft.jpg', 'sort_order' => 3, 'is_published' => false]);

        $this->actingAs($user)
            ->get(route('admin.sliders.index'))
            ->assertOk()
            ->assertSee('Homepage slides')
            ->assertSee('Live')
            ->assertSee('Scheduled')
            ->assertSee('Draft');
    }

    public function test_reorder_requires_the_complete_current_slider_set(): void
    {
        $user = $this->websiteManager();
        $first = SiteSlider::create(['title' => 'First', 'image_path' => 'first.jpg', 'sort_order' => 1, 'is_published' => true]);
        $second = SiteSlider::create(['title' => 'Second', 'image_path' => 'second.jpg', 'sort_order' => 2, 'is_published' => true]);

        $this->actingAs($user)
            ->postJson(route('admin.sliders.reorder'), ['order' => [$second->id]])
            ->assertStatus(422)
            ->assertJson(['ok' => false]);

        $this->assertSame(1, $first->fresh()->sort_order);
        $this->assertSame(2, $second->fresh()->sort_order);
    }

    public function test_reorder_updates_all_sliders_atomically(): void
    {
        $user = $this->websiteManager();
        $first = SiteSlider::create(['title' => 'First', 'image_path' => 'first.jpg', 'sort_order' => 1, 'is_published' => true]);
        $second = SiteSlider::create(['title' => 'Second', 'image_path' => 'second.jpg', 'sort_order' => 2, 'is_published' => true]);
        $third = SiteSlider::create(['title' => 'Third', 'image_path' => 'third.jpg', 'sort_order' => 3, 'is_published' => true]);

        $this->actingAs($user)
            ->postJson(route('admin.sliders.reorder'), ['order' => [$third->id, $first->id, $second->id]])
            ->assertOk()
            ->assertJson(['ok' => true]);

        $this->assertSame(1, $third->fresh()->sort_order);
        $this->assertSame(2, $first->fresh()->sort_order);
        $this->assertSame(3, $second->fresh()->sort_order);
    }
}
