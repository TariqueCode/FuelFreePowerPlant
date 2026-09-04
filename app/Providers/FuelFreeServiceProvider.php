<?php

namespace App\Providers;

use App\Http\Controllers\Admin\NavigationMenuController;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\ServiceProvider;

class FuelFreeServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        // Keep FuelFree application bindings here as the platform grows.
    }

    public function boot(): void
    {
        date_default_timezone_set(config('fuelfree.company.timezone', 'Asia/Dhaka'));

        // Backward-compatible route aliases for the Menu Builder UI. The canonical
        // endpoints are admin.navigation.*; these names keep older compiled views
        // functional without changing their request semantics.
        Route::middleware('auth')->prefix('admin')->group(function (): void {
            Route::middleware('permission:website.view')
                ->get('/navigation', [NavigationMenuController::class, 'index'])
                ->name('admin.menu-builder.index');

            Route::middleware('permission:website.view')
                ->get('/navigation/{item}', [NavigationMenuController::class, 'show'])
                ->name('admin.menu-builder.show');

            Route::middleware('permission:navigation.manage')->group(function (): void {
                Route::post('/navigation', [NavigationMenuController::class, 'store'])
                    ->name('admin.menu-builder.store');
                Route::patch('/navigation/{item}', [NavigationMenuController::class, 'update'])
                    ->name('admin.menu-builder.update');
                Route::delete('/navigation/{item}', [NavigationMenuController::class, 'destroy'])
                    ->name('admin.menu-builder.destroy');
                Route::post('/navigation/reorder', [NavigationMenuController::class, 'reorder'])
                    ->name('admin.menu-builder.reorder');
            });
        });
    }
}
