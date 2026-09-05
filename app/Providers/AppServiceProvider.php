<?php

namespace App\Providers;

use App\Http\Controllers\Admin\ManagementController as AdminManagementController;
use App\Http\Controllers\ManagementController as PublicManagementController;
use App\Models\SystemSetting;
use Illuminate\Routing\Events\RouteMatched;
use Illuminate\Support\Facades\Blade;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        //
    }

    public function boot(): void
    {
        // Keep legacy builder route names source-compatible without changing
        // public-facing content labels. Profile Builder is an admin module;
        // it must never replace the public folder title such as Board of Directors.
        Blade::precompiler(function (string $template): string {
            $routeMap = [
                "admin.management." => "admin.profile-builder.",
                "admin.cms." => "admin.page-builder.",
                "admin.navigation." => "admin.menu-builder.",
            ];

            $template = str_replace(array_keys($routeMap), array_values($routeMap), $template);

            return str_replace([
                'Advanced Menu Builder',
                'Content Pages',
                'Website Navigation',
                'CONTENT MANAGEMENT',
                'WEBSITE SECTIONS · MANAGEMENT',
                'New CMS Page',
                'Edit CMS Page',
                'Add Management Member',
                'Edit Management Profile',
                'Add management member',
            ], [
                'Menu Builder',
                'Page Builder',
                'Menu Builder',
                'PAGE BUILDER',
                'GLOBAL · PROFILE BUILDER',
                'New Page',
                'Edit Page',
                'Add Profile',
                'Edit Profile',
                'Add profile',
            ], $template);
        });

        // The old builder namespaces remain blocked; the canonical builder
        // aliases below are the supported admin endpoints.
        $this->app['router']->matched(function (RouteMatched $event): void {
            $routeName = $event->route->getName();

            if (in_array($routeName, [
                'admin.navigation.index', 'admin.navigation.store', 'admin.navigation.reorder',
                'admin.navigation.show', 'admin.navigation.update', 'admin.navigation.destroy',
                'admin.management.index', 'admin.management.store', 'admin.management.create',
                'admin.management.reorder', 'admin.management.update', 'admin.management.destroy',
                'admin.management.edit', 'admin.management.toggle',
                'admin.cms.index', 'admin.cms.store', 'admin.cms.create', 'admin.cms.update',
                'admin.cms.destroy', 'admin.cms.duplicate', 'admin.cms.edit', 'admin.cms.toggle',
            ], true)) {
                abort(404);
            }
        });

        $router = $this->app['router'];

        // Canonical Profile Builder admin endpoints.
        $router->get('/admin/profile-builder', [AdminManagementController::class, 'index'])
            ->middleware(['auth', 'permission:website.view'])->name('admin.profile-builder.index');
        $router->get('/admin/profile-builder/folders/create', [AdminManagementController::class, 'folderCreate'])
            ->middleware(['auth', 'permission:website.manage'])->name('admin.profile-builder.folders.create');
        $router->post('/admin/profile-builder/folders', [AdminManagementController::class, 'folderStore'])
            ->middleware(['auth', 'permission:website.manage'])->name('admin.profile-builder.folders.store');
        $router->get('/admin/profile-builder/folders/{folder}/edit', [AdminManagementController::class, 'folderEdit'])
            ->middleware(['auth', 'permission:website.manage'])->name('admin.profile-builder.folders.edit');
        $router->patch('/admin/profile-builder/folders/{folder}', [AdminManagementController::class, 'folderUpdate'])
            ->middleware(['auth', 'permission:website.manage'])->name('admin.profile-builder.folders.update');
        $router->delete('/admin/profile-builder/folders/{folder}', [AdminManagementController::class, 'folderDestroy'])
            ->middleware(['auth', 'permission:website.manage'])->name('admin.profile-builder.folders.destroy');
        $router->post('/admin/profile-builder/folders/reorder', [AdminManagementController::class, 'folderReorder'])
            ->middleware(['auth', 'permission:website.manage'])->name('admin.profile-builder.folders.reorder');

        $router->get('/admin/profile-builder/create', [AdminManagementController::class, 'create'])
            ->middleware(['auth', 'permission:website.manage'])->name('admin.profile-builder.create');
        $router->post('/admin/profile-builder', [AdminManagementController::class, 'store'])
            ->middleware(['auth', 'permission:website.manage'])->name('admin.profile-builder.store');
        $router->get('/admin/profile-builder/{member}/edit', [AdminManagementController::class, 'edit'])
            ->middleware(['auth', 'permission:website.manage'])->name('admin.profile-builder.edit');
        $router->patch('/admin/profile-builder/{member}', [AdminManagementController::class, 'update'])
            ->middleware(['auth', 'permission:website.manage'])->name('admin.profile-builder.update');
        $router->delete('/admin/profile-builder/{member}', [AdminManagementController::class, 'destroy'])
            ->middleware(['auth', 'permission:website.manage'])->name('admin.profile-builder.destroy');
        $router->patch('/admin/profile-builder/{member}/toggle', [AdminManagementController::class, 'toggle'])
            ->middleware(['auth', 'permission:website.manage'])->name('admin.profile-builder.toggle');
        $router->post('/admin/profile-builder/reorder', [AdminManagementController::class, 'reorder'])
            ->middleware(['auth', 'permission:website.manage'])->name('admin.profile-builder.reorder');

        // Pretty public folder URLs. This is deliberately registered after the
        // explicit application routes so /about-us, /career, /contact, etc.
        // keep their existing routes. Only a real published profile folder is
        // served; unknown one-segment URLs return 404.
        $router->get('/{folderSlug}', [PublicManagementController::class, 'folder'])
            ->where('folderSlug', '[A-Za-z0-9][A-Za-z0-9\-]*')
            ->name('management.folder');

        if (! Schema::hasTable('system_settings')) {
            return;
        }

        $settings = Cache::rememberForever(
            'fuelfree.system_settings',
            fn () => SystemSetting::query()->pluck('value', 'key')->all()
        );

        if (array_key_exists('company.name', $settings)) config(['fuelfree.company.name' => $settings['company.name']]);
        if (array_key_exists('company.domain', $settings)) config(['fuelfree.company.domain' => $settings['company.domain']]);
        if (array_key_exists('company.tagline', $settings)) config(['fuelfree.company.tagline' => $settings['company.tagline']]);
        if (array_key_exists('company.timezone', $settings)) config(['fuelfree.company.timezone' => $settings['company.timezone']]);
        if (array_key_exists('company.logo_path', $settings)) config(['fuelfree.company.logo_path' => $settings['company.logo_path']]);
        if (array_key_exists('storage.quota_gib', $settings)) config(['fuelfree.storage.quota_bytes' => (int) round((float) $settings['storage.quota_gib'] * 1073741824)]);

        foreach (['header','footer'] as $section) {
            $prefix = $section . '.';
            foreach ($settings as $key => $value) {
                if (str_starts_with($key, $prefix)) {
                    config(["fuelfree.{$section}.".substr($key, strlen($prefix)) => $value]);
                }
            }
        }
    }
}
