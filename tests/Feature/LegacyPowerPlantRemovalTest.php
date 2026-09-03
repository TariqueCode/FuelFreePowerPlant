<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Schema;
use Tests\TestCase;

class LegacyPowerPlantRemovalTest extends TestCase
{
    use RefreshDatabase;

    public function test_legacy_power_plant_admin_and_public_detail_routes_are_removed(): void
    {
        $this->assertFalse(Route::has('admin.plants.index'));
        $this->assertFalse(Route::has('admin.plants.create'));
        $this->assertFalse(Route::has('admin.plants.edit'));
        $this->assertFalse(Route::has('admin.plants.performance.index'));
        $this->assertFalse(Route::has('projects.show'));
        $this->assertFalse(Schema::hasTable('power_plants'));
        $this->assertFalse(Schema::hasTable('plant_performance'));
    }

    public function test_homepage_no_longer_renders_the_legacy_power_plant_sections(): void
    {
        $response = $this->get(route('home'));

        $response->assertOk();
        $response->assertDontSee('Power at a glance');
        $response->assertDontSee('Our project portfolio');
        $response->assertDontSee('Operational plants');
        $response->assertDontSee('View all →');
    }
}
