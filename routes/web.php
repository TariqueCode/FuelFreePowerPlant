<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\DashboardController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
})->name('home');

Route::middleware('guest')->group(function () {
    Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
    Route::post('/login', [AuthController::class, 'login'])
        ->middleware('throttle:6,1')
        ->name('login.store');
});

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

    Route::post('/logout', [AuthController::class, 'logout'])->name('logout');
});
