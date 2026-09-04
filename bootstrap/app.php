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
            Route::middleware(['auth', 'permission:website.view'])
                ->prefix('admin/profile-builder')->name('admin.profile-builder.')
                ->group(function (): void {
                    Route::get('/', [ManagementController::class, 'index'])->name('index');
                    Route::get('/create', [ManagementController::class, 'create'])->name('create');
                    Route::get('/{member}/edit', [ManagementController::class, 'edit'])->name('edit');
                    Route::post('/', [ManagementController::class, 'store'])->name('store');
                    Route::patch('/{member}', [ManagementController::class, 'update'])->name('update');
                    Route::patch('/{member}/toggle', [ManagementController::class, 'toggle'])->name('toggle');
                    Route::delete('/{member}', [ManagementController::class, 'destroy'])->name('destroy');
                    Route::post('/reorder', [ManagementController::class, 'reorder'])->name('reorder');
                });

            Route::middleware(['auth', 'permission:cms.view'])
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

            Route::middleware(['auth', 'permission:cms.publish'])
                ->prefix('admin/page-builder')->name('admin.page-builder.')
                ->group(function (): void {
                    Route::patch('/{page}/toggle', [CmsController::class, 'togglePublication'])->name('toggle');
                });

            Route::middleware(['auth', 'permission:website.view'])
                ->prefix('admin/menu-builder')->name('admin.menu-builder.')
                ->group(function (): void {
                    Route::get('/', [NavigationMenuController::class, 'index'])->name('index');
                    // Keep the item endpoint read-only while accepting legacy POST navigations
                    // that can still exist in cached/older admin markup during deployment.
                    Route::match(['get', 'post'], '/{item}', [NavigationMenuController::class, 'show'])->name('show');
                });

            Route::middleware(['auth', 'permission:navigation.manage'])
                ->prefix('admin/menu-builder')->name('admin.menu-builder.')
                ->group(function (): void {
                    Route::post('/', [NavigationMenuController::class, 'store'])->name('store');
                    Route::patch('/{item}', [NavigationMenuController::class, 'update'])->name('update');
                    Route::delete('/{item}', [NavigationMenuController::class, 'destroy'])->name('destroy');
                    Route::post('/reorder', [NavigationMenuController::class, 'reorder'])->name('reorder');
                });
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
