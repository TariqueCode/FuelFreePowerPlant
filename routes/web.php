<?php

use App\Http\Controllers\DashboardController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
})->name('home');

Route::middleware('auth')->group(function () {
    Route::get('/dashboard', DashboardController::class)
        ->name('dashboard')
        ->middleware('permission:dashboard.view');

    Route::get('/admin', DashboardController::class)
        ->name('admin.dashboard')
        ->middleware('role:super-admin,administrator,project-manager,support-agent');

    Route::get('/portal', DashboardController::class)
        ->name('portal.dashboard')
        ->middleware('role:client');
});
