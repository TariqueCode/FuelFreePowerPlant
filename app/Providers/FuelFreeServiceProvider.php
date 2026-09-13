<?php

namespace App\Providers;

use App\Http\Controllers\Admin\AuditLogController;
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

        // Register the dashboard compatibility name only after the application's
        // route files have been loaded, so the canonical admin.audit route wins
        // while the legacy admin.audit.index name remains available to views.
        $this->app->booted(function (): void {
            if (! Route::has('admin.audit.index')) {
                Route::middleware(['web', 'auth', 'permission:audit.view'])
                    ->get('/admin/audit', [AuditLogController::class, 'index'])
                    ->name('admin.audit.index');
            }
        });
    }
}
