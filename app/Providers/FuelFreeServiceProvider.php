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

        // Keep the dashboard's legacy audit route name compatible with the
        // canonical admin.audit route already used by the portal.
        if (! Route::has('admin.audit.index')) {
            Route::middleware(['web', 'auth', 'permission:audit.view'])
                ->get('/admin/audit', [AuditLogController::class, 'index'])
                ->name('admin.audit.index');
        }
    }
}
