<?php

namespace Tests\Feature\Admin;

use Illuminate\Support\Facades\File;
use Tests\TestCase;

class LegacyRouteRewriteTest extends TestCase
{
    public function test_blade_precompiler_does_not_rewrite_canonical_builder_routes_to_legacy_names(): void
    {
        $provider = File::get(app_path('Providers/AppServiceProvider.php'));

        $this->assertStringNotContainsString("'admin.management.' => 'admin.profile-builder.'", $provider);
        $this->assertStringNotContainsString("'admin.page-builder.' => 'admin.cms.'", $provider);
        $this->assertStringNotContainsString('routeMap', $provider);
    }
}
