<?php

use App\Http\Controllers\Admin\UserController;
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

    Route::middleware('role:super-admin,administrator')->prefix('admin')->group(function () {
        Route::get('/', DashboardController::class)
            ->name('admin.dashboard');

        Route::middleware('permission:users.view')->group(function () {
            Route::get('/users', [UserController::class, 'index'])->name('admin.users.index');
        });

        Route::middleware('permission:users.manage')->group(function () {
            Route::get('/users/create', [UserController::class, 'create'])->name('admin.users.create');
            Route::post('/users', [UserController::class, 'store'])->name('admin.users.store');
        });
    });

    Route::get('/portal', DashboardController::class)
        ->name('portal.dashboard')
        ->middleware('role:client');

    Route::post('/logout', [AuthController::class, 'logout'])->name('logout');
});
