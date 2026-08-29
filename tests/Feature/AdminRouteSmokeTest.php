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
    public function test_application_debug_defaults_to_false_in_configuration(): void
    {
        $this->assertFalse((bool) config('app.debug'));
    }

    /** @return void */
    public function test_sensitive_routes_use_explicit_bindings_and_constraints(): void
    {
        foreach ([
            'admin.helpdesk.show',
            'admin.helpdesk.status',
            'admin.helpdesk.reply',
            'admin.helpdesk.delete',
            'admin.helpdesk.attachment',
            'admin.mail.message',
            'admin.mail.attachment',
            'admin.career-applications.cv',
            'admin.documents.download',
            'admin.documents.share',
            'admin.documents.unshare',
            'admin.documents.destroy',
            'admin.cms.edit',
            'admin.cms.update',
            'admin.cms.destroy',
        ] as $name) {
            $route = Route::getRoutes()->getByName($name);
            $this->assertNotNull($route, "Missing route: {$name}");
            $this->assertNotSame([], $route->wheres(), "Missing route constraints: {$name}");
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
            'admin.documents.folders.store' => 'documents.manage',
            'admin.documents.folders.rename' => 'documents.manage',
            'admin.documents.folders.move' => 'documents.manage',
            'admin.documents.folders.copy' => 'documents.manage',
            'admin.documents.folders.destroy' => 'documents.manage',
            'admin.documents.store' => 'documents.manage',
            'admin.documents.rename' => 'documents.manage',
            'admin.documents.move' => 'documents.manage',
            'admin.documents.copy' => 'documents.manage',
            'admin.documents.share' => 'documents.manage',
            'admin.documents.unshare' => 'documents.manage',
            'admin.cms.index' => 'cms.view',
            'admin.cms.destroy' => 'cms.manage',
            'admin.cms.store' => 'cms.manage',
            'admin.cms.update' => 'cms.manage',
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
