<?php

use App\Http\Controllers\Admin\CmsController;
use App\Http\Controllers\Admin\DocumentController;
use App\Http\Controllers\Admin\InfrastructureController;
use App\Http\Controllers\Admin\SettingsController;
use App\Http\Controllers\Admin\UserController;
use App\Http\Controllers\AdminDashboardController;
use App\Http\Controllers\AdminModuleController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\ClientPortalController;
use App\Http\Controllers\CmsPageController;
use App\Http\Controllers\DashboardController;
use Illuminate\Support\Facades\Route;

Route::get('/', fn () => view('welcome'))->name('home');
Route::get('/pages/{slug}', [CmsPageController::class, 'show'])->name('cms.page');
Route::middleware('guest')->group(function () { Route::get('/login', [AuthController::class, 'showLogin'])->name('login'); Route::post('/login', [AuthController::class, 'login'])->middleware('throttle:6,1')->name('login.store'); });

Route::middleware('auth')->group(function () {
    Route::get('/dashboard', DashboardController::class)->name('dashboard')->middleware('permission:dashboard.view');

    Route::middleware('role:super-admin,administrator,project-manager,support-agent')->prefix('admin')->group(function () {
        Route::get('/', AdminDashboardController::class)->name('admin.dashboard')->middleware('permission:dashboard.view');
        Route::middleware('permission:users.view')->group(function () { Route::get('/users', [UserController::class, 'index'])->name('admin.users.index'); });
        Route::middleware('permission:users.manage')->group(function () { Route::get('/users/create', [UserController::class, 'create'])->name('admin.users.create'); Route::post('/users', [UserController::class, 'store'])->name('admin.users.store'); Route::get('/users/{user}/edit', [UserController::class, 'edit'])->name('admin.users.edit'); Route::patch('/users/{user}', [UserController::class, 'update'])->name('admin.users.update'); Route::delete('/users/{user}', [UserController::class, 'destroy'])->name('admin.users.destroy'); });
    });

    Route::prefix('admin')->middleware('permission:documents.view')->group(function () { Route::get('/documents', [DocumentController::class, 'index'])->name('admin.documents'); Route::get('/documents/{document}/download', [DocumentController::class, 'download'])->name('admin.documents.download'); });
    Route::prefix('admin')->middleware('permission:documents.manage')->group(function () { Route::post('/documents/folders', [DocumentController::class, 'storeFolder'])->name('admin.documents.folders.store'); Route::post('/documents', [DocumentController::class, 'store'])->name('admin.documents.store'); Route::post('/documents/chunks', [DocumentController::class, 'chunkUpload'])->name('admin.documents.chunks'); Route::delete('/documents/{document}', [DocumentController::class, 'destroy'])->name('admin.documents.destroy'); Route::post('/documents/folders/{folder}/rename', [DocumentController::class, 'renameFolder'])->name('admin.documents.folders.rename'); Route::post('/documents/folders/{folder}/move', [DocumentController::class, 'moveFolder'])->name('admin.documents.folders.move'); Route::post('/documents/folders/{folder}/copy', [DocumentController::class, 'copyFolder'])->name('admin.documents.folders.copy'); Route::delete('/documents/folders/{folder}', [DocumentController::class, 'destroyFolder'])->name('admin.documents.folders.destroy'); Route::post('/documents/{document}/rename', [DocumentController::class, 'rename'])->name('admin.documents.rename'); Route::post('/documents/{document}/move', [DocumentController::class, 'move'])->name('admin.documents.move'); Route::post('/documents/{document}/copy', [DocumentController::class, 'copy'])->name('admin.documents.copy'); });

    Route::prefix('admin')->middleware('permission:email.view')->group(function () { Route::get('/email', [InfrastructureController::class, 'email'])->name('admin.email'); });
    Route::prefix('admin')->middleware('permission:email.manage')->group(function () { Route::get('/email/create', [InfrastructureController::class, 'createEmail'])->name('admin.email.create'); Route::post('/email', [InfrastructureController::class, 'storeEmail'])->name('admin.email.store'); Route::delete('/email/{account}', [InfrastructureController::class, 'destroyEmail'])->name('admin.email.destroy'); });

    Route::prefix('admin')->middleware('permission:subdomains.view')->group(function () { Route::get('/subdomains', [InfrastructureController::class, 'subdomains'])->name('admin.subdomains'); });
    Route::prefix('admin')->middleware('permission:subdomains.manage')->group(function () { Route::get('/subdomains/create', [InfrastructureController::class, 'createSubdomain'])->name('admin.subdomains.create'); Route::post('/subdomains', [InfrastructureController::class, 'storeSubdomain'])->name('admin.subdomains.store'); Route::delete('/subdomains/{subdomain}', [InfrastructureController::class, 'destroySubdomain'])->name('admin.subdomains.destroy'); });

    Route::prefix('admin')->middleware('permission:support.view')->group(function () { Route::get('/support', [AdminModuleController::class, 'support'])->name('admin.support'); Route::get('/support/create', [AdminModuleController::class, 'createTicket'])->name('admin.support.create')->middleware('permission:support.create'); Route::post('/support', [AdminModuleController::class, 'storeTicket'])->name('admin.support.store')->middleware('permission:support.create'); Route::get('/support/{ticket}', [AdminModuleController::class, 'showTicket'])->name('admin.support.ticket'); Route::post('/support/{ticket}/reply', [AdminModuleController::class, 'replyTicket'])->name('admin.support.reply')->middleware('permission:support.reply'); Route::patch('/support/{ticket}', [AdminModuleController::class, 'updateTicket'])->name('admin.support.update')->middleware('permission:support.manage'); });

    Route::prefix('admin')->middleware('permission:cms.view')->group(function () { Route::get('/cms', [CmsController::class, 'index'])->name('admin.cms.index'); });
    Route::prefix('admin')->middleware('permission:cms.manage')->group(function () { Route::get('/cms/create', [CmsController::class, 'create'])->name('admin.cms.create'); Route::post('/cms', [CmsController::class, 'store'])->name('admin.cms.store'); Route::get('/cms/{page}/edit', [CmsController::class, 'edit'])->name('admin.cms.edit'); Route::patch('/cms/{page}', [CmsController::class, 'update'])->name('admin.cms.update'); Route::delete('/cms/{page}', [CmsController::class, 'destroy'])->name('admin.cms.destroy'); });

    Route::prefix('admin')->middleware('permission:settings.manage')->group(function () { Route::get('/settings', [SettingsController::class, 'index'])->name('admin.settings'); Route::post('/settings', [SettingsController::class, 'update'])->name('admin.settings.update'); });
    Route::get('/portal', ClientPortalController::class)->name('portal.dashboard')->middleware('role:client');
    Route::post('/logout', [AuthController::class, 'logout'])->name('logout');
});
