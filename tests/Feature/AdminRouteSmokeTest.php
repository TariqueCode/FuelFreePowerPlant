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
    public function test_sensitive_routes_have_expected_permissions(): void
    {
        $matrix = [
            'admin.helpdesk' => 'mail.view',
            'admin.helpdesk.delete' => 'mail.manage',
            'admin.helpdesk.attachment' => 'mail.view',
            'admin.mail' => 'mail.view',
            'admin.mail.send' => 'mail.manage',
            'admin.career-applications.cv' => 'mail.view',
            'admin.documents.download' => 'documents.view',
            'admin.documents.destroy' => 'documents.manage',
            'admin.documents.share' => 'documents.manage',
            'admin.documents.unshare' => 'documents.manage',
            'admin.cms.index' => 'cms.view',
            'admin.cms.destroy' => 'cms.manage',
            'admin.social-links.destroy' => 'social-media.manage',
        ];

        foreach ($matrix as $name => $permission) {
            $route = Route::getRoutes()->getByName($name);

            $this->assertNotNull($route, "Missing route: {$name}");
            $this->assertContains('auth', $route->gatherMiddleware());
            $this->assertContains("permission:{$permission}", $route->gatherMiddleware());
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
