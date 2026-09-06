<?php

use App\Http\Controllers\Admin\CmsController;
use App\Http\Controllers\Admin\ManagementController;
use App\Http\Controllers\Admin\NavigationMenuController;
use App\Http\Middleware\HomeAnnouncementPopup;
use App\Http\Middleware\PermissionMiddleware;
use App\Http\Middleware\RoleMiddleware;
use App\Http\Middleware\SecurityHeaders;
use App\Http\Middleware\WebmailAuth;
use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
        then: function (): void {
            Route::middleware(['web', 'auth', 'permission:website.view'])
                ->prefix('admin/profile-builder')->name('admin.profile-builder.')
                ->group(function (): void {
                    Route::get('/', [ManagementController::class, 'index'])->name('index');
                });

            Route::middleware(['web', 'auth', 'permission:website.manage'])
                ->prefix('admin/profile-builder')->name('admin.profile-builder.')
                ->group(function (): void {
                    Route::get('/folders/create', [ManagementController::class, 'folderCreate'])->name('folders.create');
                    Route::post('/folders', [ManagementController::class, 'folderStore'])->name('folders.store');
                    Route::get('/folders/{folder}/edit', [ManagementController::class, 'folderEdit'])->name('folders.edit');
                    Route::patch('/folders/{folder}', [ManagementController::class, 'folderUpdate'])->name('folders.update');
                    Route::delete('/folders/{folder}', [ManagementController::class, 'folderDestroy'])->name('folders.destroy');
                    Route::post('/folders/reorder', [ManagementController::class, 'folderReorder'])->name('folders.reorder');

                    Route::get('/create', [ManagementController::class, 'create'])->name('create');
                    Route::post('/', [ManagementController::class, 'store'])->name('store');
                    Route::get('/{member}/edit', [ManagementController::class, 'edit'])->name('edit');
                    Route::patch('/{member}', [ManagementController::class, 'update'])->name('update');
                    Route::delete('/{member}', [ManagementController::class, 'destroy'])->name('destroy');
                    Route::post('/reorder', [ManagementController::class, 'reorder'])->name('reorder');
                });

            Route::middleware(['web', 'auth', 'permission:website.publish'])
                ->prefix('admin/profile-builder')->name('admin.profile-builder.')
                ->group(function (): void {
                    Route::patch('/{member}/toggle', [ManagementController::class, 'toggle'])->name('toggle');
                });

            Route::middleware(['web', 'auth', 'permission:cms.view'])
                ->prefix('admin/page-builder')->name('admin.page-builder.')
                ->group(function (): void {
                    Route::get('/', [CmsController::class, 'index'])->name('index');
                    Route::get('/create', [CmsController::class, 'create'])->name('create');
                    Route::get('/{page}/edit', [CmsController::class, 'edit'])->name('edit');
                    Route::post('/', [CmsController::class, 'store'])->name('store');
                    Route::patch('/{page}', [CmsController::class, 'update'])->name('update');
                    Route::delete('/{page}', [CmsController::class, 'destroy'])->name('destroy');
                    Route::post('/{page}/duplicate', [CmsController::class, 'duplicate'])->name('duplicate');
                });

            Route::middleware(['web', 'auth', 'permission:cms.publish'])
                ->prefix('admin/page-builder')->name('admin.page-builder.')
                ->group(function (): void {
                    Route::patch('/{page}/toggle', [CmsController::class, 'togglePublication'])->name('toggle');
                });

            Route::middleware(['web', 'auth', 'permission:website.view'])
                ->prefix('admin/menu-builder')->name('admin.menu-builder.')
                ->group(function (): void {
                    Route::get('/', [NavigationMenuController::class, 'index'])->name('index');
                    Route::get('/{item}', [NavigationMenuController::class, 'show'])->name('show');
                });

            Route::middleware(['web', 'auth', 'permission:navigation.manage'])
                ->prefix('admin/menu-builder')->name('admin.menu-builder.')
                ->group(function (): void {
                    Route::post('/', [NavigationMenuController::class, 'store'])->name('store');
                    // Legacy admin markup submits deletion as POST to /{item}.
                    // Keep that compatibility path permission-protected and map it to destroy.
                    Route::post('/{item}', [NavigationMenuController::class, 'destroy'])->name('legacy-destroy');
                    Route::patch('/{item}', [NavigationMenuController::class, 'update'])->name('update');
                    Route::delete('/{item}', [NavigationMenuController::class, 'destroy'])->name('destroy');
                    Route::post('/reorder', [NavigationMenuController::class, 'reorder'])->name('reorder');
                });

            // Published profile folders use their configured slug as a first-class public page.
            // This catch-all is intentionally registered after the application routes so it
            // cannot shadow existing static, authentication, admin, or management routes.
            Route::get('/{folderSlug}', [\App\Http\Controllers\ManagementController::class, 'folder'])
                ->where('folderSlug', '[A-Za-z0-9]+(?:-[A-Za-z0-9]+)*')
                ->name('management.folder');
        },
    )
    ->withMiddleware(function (Middleware $middleware): void {
        $middleware->append(SecurityHeaders::class);
        $middleware->append(HomeAnnouncementPopup::class);
        $middleware->alias([
            'role' => RoleMiddleware::class,
            'permission' => PermissionMiddleware::class,
            'webmail.auth' => WebmailAuth::class,
        ]);
    })
    ->withExceptions(function (Exceptions $exceptions): void {
        $exceptions->shouldRenderJsonWhen(
            fn (Request $request) => $request->is('api/*') || $request->expectsJson(),
        );
    })->create();
