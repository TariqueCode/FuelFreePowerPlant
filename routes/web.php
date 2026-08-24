<?php

use App\Http\Controllers\Admin\DocumentController;
use App\Http\Controllers\Admin\UserController;
use App\Http\Controllers\AdminDashboardController;
use App\Http\Controllers\AdminModuleController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\ClientPortalController;
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

    Route::middleware('role:super-admin,administrator,project-manager,support-agent')->prefix('admin')->group(function () {
        Route::get('/', AdminDashboardController::class)
            ->name('admin.dashboard')
            ->middleware('permission:dashboard.view');

        Route::middleware('permission:users.view')->group(function () {
            Route::get('/users', [UserController::class, 'index'])->name('admin.users.index');
        });

        Route::middleware('permission:users.manage')->group(function () {
            Route::get('/users/create', [UserController::class, 'create'])->name('admin.users.create');
            Route::post('/users', [UserController::class, 'store'])->name('admin.users.store');
            Route::get('/users/{user}/edit', [UserController::class, 'edit'])->name('admin.users.edit');
            Route::patch('/users/{user}', [UserController::class, 'update'])->name('admin.users.update');
            Route::delete('/users/{user}', [UserController::class, 'destroy'])->name('admin.users.destroy');
        });
    });

    // Document read access is permission-driven so clients can use their private vault too.
    Route::prefix('admin')->middleware('permission:documents.view')->group(function () {
        Route::get('/documents', [DocumentController::class, 'index'])->name('admin.documents');
        Route::get('/documents/{document}/download', [DocumentController::class, 'download'])->name('admin.documents.download');
    });

    // Document mutations require the stronger manage permission.
    Route::prefix('admin')->middleware('permission:documents.manage')->group(function () {
        Route::post('/documents/folders', [DocumentController::class, 'storeFolder'])->name('admin.documents.folders.store');
        Route::post('/documents', [DocumentController::class, 'store'])->name('admin.documents.store');
        Route::post('/documents/chunks', [DocumentController::class, 'chunkUpload'])->name('admin.documents.chunks');
        Route::delete('/documents/{document}', [DocumentController::class, 'destroy'])->name('admin.documents.destroy');
        Route::post('/documents/folders/{folder}/rename', [DocumentController::class, 'renameFolder'])->name('admin.documents.folders.rename');
        Route::post('/documents/folders/{folder}/move', [DocumentController::class, 'moveFolder'])->name('admin.documents.folders.move');
        Route::post('/documents/folders/{folder}/copy', [DocumentController::class, 'copyFolder'])->name('admin.documents.folders.copy');
        Route::delete('/documents/folders/{folder}', [DocumentController::class, 'destroyFolder'])->name('admin.documents.folders.destroy');
        Route::post('/documents/{document}/rename', [DocumentController::class, 'rename'])->name('admin.documents.rename');
        Route::post('/documents/{document}/move', [DocumentController::class, 'move'])->name('admin.documents.move');
        Route::post('/documents/{document}/copy', [DocumentController::class, 'copy'])->name('admin.documents.copy');
    });

    Route::prefix('admin')->middleware('permission:email.view')->group(function () {
        Route::get('/email', [AdminModuleController::class, 'email'])->name('admin.email');
    });

    Route::prefix('admin')->middleware('permission:support.view')->group(function () {
        Route::get('/support', [AdminModuleController::class, 'support'])->name('admin.support');
        Route::get('/support/create', [AdminModuleController::class, 'createTicket'])
            ->name('admin.support.create')
            ->middleware('permission:support.create');
        Route::post('/support', [AdminModuleController::class, 'storeTicket'])
            ->name('admin.support.store')
            ->middleware('permission:support.create');
        Route::get('/support/{ticket}', [AdminModuleController::class, 'showTicket'])
            ->name('admin.support.ticket');
        Route::post('/support/{ticket}/reply', [AdminModuleController::class, 'replyTicket'])
            ->name('admin.support.reply')
            ->middleware('permission:support.reply');
        Route::patch('/support/{ticket}', [AdminModuleController::class, 'updateTicket'])
            ->name('admin.support.update')
            ->middleware('permission:support.manage');
    });

    Route::get('/portal', ClientPortalController::class)
        ->name('portal.dashboard')
        ->middleware('role:client');

    Route::post('/logout', [AuthController::class, 'logout'])->name('logout');
});
