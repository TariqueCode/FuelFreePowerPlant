<?php

use App\Http\Controllers\Admin\CmsController;
use App\Http\Controllers\Admin\ManagementController;
use App\Http\Controllers\Admin\NavigationMenuController;
use App\Http\Controllers\Admin\NewsEventController;
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
            Route::middleware(['web', 'auth', 'permission:website.view'])->prefix('admin/profile-builder')->name('admin.profile-builder.')->group(function (): void {
                Route::get('/', [ManagementController::class, 'index'])->name('index');
                Route::get('/create', [ManagementController::class, 'create'])->name('create');
                Route::get('/{member}/edit', [ManagementController::class, 'edit'])->name('edit');
            });
            Route::middleware(['web', 'auth', 'permission:website.manage'])->prefix('admin/profile-builder')->name('admin.profile-builder.')->group(function (): void {
                Route::post('/', [ManagementController::class, 'store'])->name('store');
                Route::patch('/{member}', [ManagementController::class, 'update'])->name('update');
                Route::patch('/{member}/toggle', [ManagementController::class, 'toggle'])->name('toggle');
                Route::delete('/{member}', [ManagementController::class, 'destroy'])->name('destroy');
                Route::post('/reorder', [ManagementController::class, 'reorder'])->name('reorder');
                Route::get('/folders/create', [ManagementController::class, 'folderCreate'])->name('folders.create');
                Route::post('/folders', [ManagementController::class, 'folderStore'])->name('folders.store');
                Route::get('/folders/{folder}/edit', [ManagementController::class, 'folderEdit'])->name('folders.edit');
                Route::patch('/folders/{folder}', [ManagementController::class, 'folderUpdate'])->name('folders.update');
                Route::delete('/folders/{folder}', [ManagementController::class, 'folderDestroy'])->name('folders.destroy');
                Route::post('/folders/reorder', [ManagementController::class, 'folderReorder'])->name('folders.reorder');
            });
            Route::middleware(['web', 'auth', 'permission:website.view'])->prefix('admin/menu-builder')->name('admin.menu-builder.')->group(function (): void {
                Route::get('/', [NavigationMenuController::class, 'index'])->name('index');
                Route::get('/{item}', [NavigationMenuController::class, 'show'])->name('show');
            });
            Route::middleware(['web', 'auth', 'permission:navigation.manage'])->prefix('admin/menu-builder')->name('admin.menu-builder.')->group(function (): void {
                Route::post('/', [NavigationMenuController::class, 'store'])->name('store');
                Route::post('/reorder', [NavigationMenuController::class, 'reorder'])->name('reorder');
                Route::post('/{item}', [NavigationMenuController::class, 'destroy'])->name('legacy-destroy');
                Route::patch('/{item}', [NavigationMenuController::class, 'update'])->name('update');
                Route::delete('/{item}', [NavigationMenuController::class, 'destroy'])->name('destroy');
            });

            $legacyCmsNames = ['admin.cms.index','admin.cms.create','admin.cms.store','admin.cms.edit','admin.cms.update','admin.cms.duplicate','admin.cms.destroy','admin.cms.toggle'];
            $routeCollection = Route::getRoutes();
            foreach ($routeCollection->getRoutes() as $route) {
                $name = $route->getName();
                if ($name !== null && in_array($name, $legacyCmsNames, true)) {
                    $suffix = substr($name, strlen('admin.cms.'));
                    $route->name('admin.legacy-cms.'.$suffix);
                }
            }
            $routeCollection->refreshNameLookups();

            Route::middleware(['web', 'auth', 'permission:cms.view'])->prefix('admin/page-builder')->name('admin.cms.')->group(function (): void {
                Route::get('/', [CmsController::class, 'index'])->name('index');
            });
            Route::middleware(['web', 'auth', 'permission:cms.manage'])->prefix('admin/page-builder')->name('admin.cms.')->group(function (): void {
                Route::get('/create', [CmsController::class, 'create'])->name('create');
                Route::post('/', [CmsController::class, 'store'])->name('store');
                Route::get('/{page}/edit', [CmsController::class, 'edit'])->name('edit');
                Route::patch('/{page}', [CmsController::class, 'update'])->name('update');
                Route::post('/{page}/duplicate', [CmsController::class, 'duplicate'])->name('duplicate');
                Route::delete('/{page}', [CmsController::class, 'destroy'])->name('destroy');
            });
            Route::middleware(['web', 'auth', 'permission:cms.publish'])->prefix('admin/page-builder')->name('admin.cms.')->group(function (): void {
                Route::patch('/{page}/toggle', [CmsController::class, 'togglePublication'])->name('toggle');
            });

            // Legacy URL compatibility: redirect the previous News & Event path to the canonical path.
            Route::get('/admin/news_and_Event', fn () => redirect('/admin/news-and-Event', 301))->name('admin.news_and_event.legacy');

            Route::middleware(['web', 'auth', 'permission:website.view'])->prefix('admin/news-and-Event')->name('admin.news_and_event.')->group(function (): void {
                Route::get('/', [NewsEventController::class, 'index'])->name('index');
            });
            Route::middleware(['web', 'auth', 'permission:website.manage'])->prefix('admin/news-and-Event')->name('admin.news_and_event.')->group(function (): void {
                Route::get('/create', [NewsEventController::class, 'create'])->name('create');
                Route::post('/', [NewsEventController::class, 'store'])->name('store');
                Route::get('/{item}/edit', [NewsEventController::class, 'edit'])->name('edit');
                Route::patch('/{item}', [NewsEventController::class, 'update'])->name('update');
                Route::patch('/{item}/toggle', [NewsEventController::class, 'toggle'])->name('toggle');
                Route::delete('/{item}', [NewsEventController::class, 'destroy'])->name('destroy');
            });
        },
    )
    ->withMiddleware(function (Middleware $middleware): void {
        $middleware->append(SecurityHeaders::class);
        $middleware->append(HomeAnnouncementPopup::class);
        $middleware->alias(['role' => RoleMiddleware::class,'permission' => PermissionMiddleware::class,'webmail.auth' => WebmailAuth::class]);
    })
    ->withExceptions(function (Exceptions $exceptions): void {
        $exceptions->shouldRenderJsonWhen(fn (Request $request) => $request->is('api/*') || $request->expectsJson());
    })->create();
