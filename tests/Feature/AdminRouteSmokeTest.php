<?php

namespace Tests\Feature;

use Illuminate\Support\Facades\Route;
use Tests\TestCase;

class AdminRouteSmokeTest extends TestCase
{
    /** @return void */
    public function test_critical_admin_routes_are_registered(): void
    {
        foreach ([
            'admin.dashboard',
            'admin.helpdesk',
            'admin.settings',
            'admin.settings.theme',
            'admin.settings.header',
            'admin.settings.footer',
            'admin.settings.menu',
            'admin.cms.index',
        ] as $name) {
            $this->assertTrue(Route::has($name), "Missing route: {$name}");
        }
    }

    /** @return void */
    public function test_settings_routes_require_authentication_and_permission(): void
    {
        foreach ([
            'admin.settings',
            'admin.settings.theme',
            'admin.settings.header',
            'admin.settings.footer',
            'admin.settings.menu',
        ] as $name) {
            $route = Route::getRoutes()->getByName($name);

            $this->assertNotNull($route, "Missing route: {$name}");
            $middleware = $route->gatherMiddleware();

            $this->assertContains('auth', $middleware);
            $this->assertContains('permission:settings.manage', $middleware);
        }
    }
}
