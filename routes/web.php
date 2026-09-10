<?php

use App\Http\Controllers\Admin\AuditLogController;
use App\Http\Controllers\Admin\DocumentController;
use App\Http\Controllers\Admin\GalleryController;
use App\Http\Controllers\Admin\HealthController;
use App\Http\Controllers\Admin\InquiryController;
use App\Http\Controllers\Admin\HelpDeskController;
use App\Http\Controllers\Admin\SettingsController;
use App\Http\Controllers\Admin\SitePopupController;
use App\Http\Controllers\Admin\SocialLinkController;
use App\Http\Controllers\Admin\SiteSliderController;
use App\Http\Controllers\Admin\UserController;
use App\Http\Controllers\AdminDashboardController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\ClientPortalController;
use App\Http\Controllers\CmsPageController;
use App\Http\Controllers\ContactController;
use App\Http\Controllers\FaviconController;
use App\Http\Controllers\CareerController;
use App\Http\Controllers\Admin\CareerApplicationController;
use App\Http\Controllers\Admin\MailController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\ManagementController;
use App\Http\Controllers\NewsController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\PublicSiteController;
use App\Http\Controllers\SustainabilityController;
use App\Http\Controllers\WebmailController;
use Illuminate\Support\Facades\Route;

Route::get('/favicon.ico', FaviconController::class)->name('favicon');
Route::get('/', HomeController::class)->name('home');
Route::get('/about-us', [PublicSiteController::class, 'show'])->defaults('section', 'about-us')->name('site.about');
Route::get('/career', [CareerController::class, 'show'])->name('site.career');
Route::post('/career', [CareerController::class, 'store'])->middleware('throttle:5,10')->name('career.store');
Route::post('/career/chunks', [CareerController::class, 'chunkUpload'])->middleware(['throttle:150,10', 'throttle:30,1'])->name('career.chunks');
Route::get('/gallery', [PublicSiteController::class, 'show'])->defaults('section', 'gallery')->name('site.gallery');
Route::get('/gallery/{item}', [PublicSiteController::class, 'showGallery'])->name('gallery.show');
Route::get('/company/{slug}', [PublicSiteController::class, 'showCompanyPage'])->name('company.page');
Route::get('/sections/{section}', [PublicSiteController::class, 'show'])->name('site.section');
Route::get('/management', ManagementController::class)->name('management');
Route::get('/management/{member}/contact.vcf', [ManagementController::class, 'vcard'])->name('management.vcard');
Route::get('/news', [NewsController::class, 'index'])->name('news.index');
Route::get('/news/{slug}', [NewsController::class, 'show'])->name('news.show');
Route::get('/shared/documents/{token}', [DocumentController::class, 'sharedDownload'])
    ->where('token', '[A-Fa-f0-9]{64}')
    ->middleware('throttle:30,1')
    ->name('documents.shared-download');
Route::get('/sustainability', SustainabilityController::class)->name('sustainability');
Route::get('/contact', [ContactController::class, 'show'])->name('contact');
Route::post('/contact', [ContactController::class, 'store'])->middleware('throttle:5,1')->name('contact.store');
Route::get('/pages/{slug}', [CmsPageController::class, 'show'])->name('cms.page');

Route::get('/mail', fn () => redirect()->away(config('cpanel.webmail_url', 'https://mail.fuelfreepowerplant.com')))->name('webmail.redirect');

Route::domain('mail.fuelfreepowerplant.com')->group(function () {
    Route::get('/', [WebmailController::class, 'login'])->name('webmail.host.login');
    Route::post('/login', [WebmailController::class, 'authenticate'])->middleware('throttle:6,1')->name('webmail.host.login.store');
    Route::middleware('webmail.auth')->group(function () {
        Route::get('/inbox', [WebmailController::class, 'inbox'])->name('webmail.host.inbox');
        Route::get('/message/{uid}', [WebmailController::class, 'show'])->whereNumber('uid')->name('webmail.host.message');
        Route::get('/message/{uid}/inline/{part}', [WebmailController::class, 'inline'])->whereNumber('uid')->where('part', '[0-9.]+')->name('webmail.host.inline');
        Route::get('/message/{uid}/attachment/{part}', [WebmailController::class, 'attachment'])->whereNumber('uid')->where('part', '[0-9.]+')->name('webmail.host.attachment');
        Route::post('/message/{uid}/delete', [WebmailController::class, 'delete'])->whereNumber('uid')->name('webmail.host.delete');
        Route::post('/message/{uid}/read', [WebmailController::class, 'toggleRead'])->whereNumber('uid')->name('webmail.host.read');
        Route::get('/compose', [WebmailController::class, 'compose'])->name('webmail.host.compose');
        Route::post('/send', [WebmailController::class, 'send'])->middleware('throttle:30,1')->name('webmail.host.send');
        Route::post('/draft', [WebmailController::class, 'saveDraft'])->middleware('throttle:120,1')->name('webmail.host.draft');
        Route::post('/logout', [WebmailController::class, 'logout'])->name('webmail.host.logout');
    });
});

Route::middleware('guest')->group(function () {
    Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
    Route::post('/login', [AuthController::class, 'login'])->middleware('throttle:6,1')->name('login.store');
});

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::get('/dashboard', DashboardController::class)->name('dashboard')->middleware('permission:dashboard.view');

    Route::prefix('admin')->middleware('auth')->group(function () {
        Route::get('/', AdminDashboardController::class)->name('admin.dashboard')->middleware('permission:dashboard.view');

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

        Route::middleware('permission:mail.view')->group(function () {
            Route::get('/help-desk', [HelpDeskController::class, 'index'])->name('admin.helpdesk');
            Route::get('/help-desk/{type}/{id}', [HelpDeskController::class, 'show'])
                ->whereIn('type', ['contact', 'career', 'email'])->whereNumber('id')->name('admin.helpdesk.show');
            Route::get('/help-desk/career/{id}/cv', [HelpDeskController::class, 'downloadCareerCv'])
                ->whereNumber('id')->middleware('throttle:30,1')->name('admin.helpdesk.career.cv');
            Route::get('/help-desk/attachment/{id}', [HelpDeskController::class, 'downloadAttachment'])
                ->whereNumber('id')->name('admin.helpdesk.attachment');
        });
        Route::middleware('permission:mail.manage')->group(function () {
            Route::patch('/help-desk/{type}/{id}/status', [HelpDeskController::class, 'updateStatus'])
                ->whereIn('type', ['contact', 'career', 'email'])->whereNumber('id')->name('admin.helpdesk.status');
            Route::post('/help-desk/{type}/{id}/reply', [HelpDeskController::class, 'reply'])
                ->whereIn('type', ['contact', 'career', 'email'])->whereNumber('id')->name('admin.helpdesk.reply');
            Route::delete('/help-desk/{type}/{id}', [HelpDeskController::class, 'destroy'])
                ->whereIn('type', ['contact', 'career', 'email'])->whereNumber('id')->name('admin.helpdesk.delete');
            Route::delete('/help-desk/email/{id}', [HelpDeskController::class, 'deleteEmail'])
                ->whereNumber('id')->name('admin.helpdesk.email.delete');
        });

        Route::middleware('permission:mail.view')->group(function () {
            Route::get('/mail', [MailController::class, 'index'])->name('admin.mail');
            Route::get('/mail/{emailAccount}/message/{uid}', [MailController::class, 'show'])
                ->whereNumber('uid')->name('admin.mail.message');
            Route::get('/mail/{emailAccount}/message/{uid}/attachment/{part}', [MailController::class, 'attachment'])
                ->whereNumber('uid')->where('part', '[0-9.]+')->name('admin.mail.attachment');
            Route::get('/mail/{emailAccount}/compose', [MailController::class, 'compose'])->name('admin.mail.compose');
        });
        Route::middleware('permission:mail.manage')->group(function () {
            Route::post('/mail/accounts', [MailController::class, 'storeAccount'])->name('admin.mail.accounts.store');
            Route::patch('/mail/accounts/{emailAccount}/toggle', [MailController::class, 'toggle'])->name('admin.mail.accounts.toggle');
            Route::delete('/mail/accounts/{emailAccount}', [MailController::class, 'destroy'])->name('admin.mail.accounts.destroy');
            Route::post('/mail/{emailAccount}/message/{uid}/delete', [MailController::class, 'delete'])->whereNumber('uid')->name('admin.mail.delete');
            Route::post('/mail/{emailAccount}/message/{uid}/read', [MailController::class, 'toggleRead'])->whereNumber('uid')->name('admin.mail.read');
            Route::post('/mail/{emailAccount}/send', [MailController::class, 'send'])->name('admin.mail.send');
        });

        Route::middleware('permission:career.view')->group(function () {
            Route::get('/career-applications', [CareerApplicationController::class, 'index'])->name('admin.career-applications.index');
            Route::get('/career-applications/{application}', [CareerApplicationController::class, 'show'])->name('admin.career-applications.show');
            Route::get('/career-applications/{application}/cv', [CareerApplicationController::class, 'download'])
                ->middleware('throttle:30,1')->name('admin.career-applications.cv');
        });
        Route::middleware('permission:career.manage')->group(function () {
            Route::patch('/career-applications/{application}', [CareerApplicationController::class, 'update'])->name('admin.career-applications.update');
        });

        Route::middleware('permission:inquiries.view')->group(function () {
            Route::get('/inquiries', [InquiryController::class, 'index'])->name('admin.inquiries.index');
            Route::get('/inquiries/{inquiry}', [InquiryController::class, 'show'])->name('admin.inquiries.show');
        });
        Route::middleware('permission:inquiries.manage')->group(function () {
            Route::patch('/inquiries/{inquiry}', [InquiryController::class, 'update'])->name('admin.inquiries.update');
        });

        Route::middleware('permission:audit.view')->group(function () {
            Route::get('/audit', [AuditLogController::class, 'index'])->name('admin.audit');
        });
        Route::middleware('permission:health.view')->group(function () {
            Route::get('/health', [HealthController::class, 'index'])->name('admin.health');
        });

        Route::middleware('permission:documents.view')->group(function () {
            Route::get('/documents', [DocumentController::class, 'index'])->name('admin.documents');
            Route::get('/documents/{document}/download', [DocumentController::class, 'download'])->name('admin.documents.download');
        });
        Route::middleware('permission:documents.manage')->group(function () {
            Route::post('/documents/{document}/share', [DocumentController::class, 'share'])->name('admin.documents.share');
            Route::delete('/documents/{document}/share', [DocumentController::class, 'unshare'])->name('admin.documents.unshare');
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

        Route::middleware('permission:website.view')->group(function () {
            Route::get('/galleries', [GalleryController::class, 'index'])->name('admin.gallery.index');
            Route::get('/site-popups', [SitePopupController::class, 'index'])->name('admin.site-popups.index');
            Route::get('/sliders', [SiteSliderController::class, 'index'])->name('admin.sliders.index');
            Route::get('/homepage-builder', [\App\Http\Controllers\Admin\HomepageBuilderController::class, 'index'])->name('admin.homepage-builder.index');
            Route::get('/design-builder', [\App\Http\Controllers\Admin\DesignBuilderController::class, 'index'])->name('admin.design.index');
            Route::get('/theme-builder', [\App\Http\Controllers\Admin\ThemeBuilderController::class, 'index'])->name('admin.theme.index');
        });
        Route::middleware('permission:website.manage')->group(function () {
            Route::get('/galleries/create', [GalleryController::class, 'create'])->name('admin.gallery.create');
            Route::post('/galleries', [GalleryController::class, 'store'])->name('admin.gallery.store');
            Route::get('/galleries/{gallery}/edit', [GalleryController::class, 'edit'])->name('admin.gallery.edit');
            Route::patch('/galleries/{gallery}', [GalleryController::class, 'update'])->name('admin.gallery.update');
            Route::delete('/galleries/{gallery}', [GalleryController::class, 'destroy'])->name('admin.gallery.destroy');
            Route::post('/galleries/media', [GalleryController::class, 'uploadMedia'])->name('admin.gallery.media');
            Route::delete('/galleries/media/{media}', [GalleryController::class, 'deleteMedia'])->name('admin.gallery.media.delete');
            Route::get('/site-popups/create', [SitePopupController::class, 'create'])->name('admin.site-popups.create');
            Route::post('/site-popups', [SitePopupController::class, 'store'])->name('admin.site-popups.store');
            Route::get('/site-popups/{popup}/edit', [SitePopupController::class, 'edit'])->name('admin.site-popups.edit');
            Route::patch('/site-popups/{popup}', [SitePopupController::class, 'update'])->name('admin.site-popups.update');
            Route::delete('/site-popups/{popup}', [SitePopupController::class, 'destroy'])->name('admin.site-popups.destroy');
            Route::get('/sliders/create', [SiteSliderController::class, 'create'])->name('admin.sliders.create');
            Route::post('/sliders', [SiteSliderController::class, 'store'])->name('admin.sliders.store');
            Route::post('/sliders/reorder', [SiteSliderController::class, 'reorder'])->name('admin.sliders.reorder');
            Route::get('/sliders/{slider}/edit', [SiteSliderController::class, 'edit'])->name('admin.sliders.edit');
            Route::patch('/sliders/{slider}', [SiteSliderController::class, 'update'])->name('admin.sliders.update');
            Route::delete('/sliders/{slider}', [SiteSliderController::class, 'destroy'])->name('admin.sliders.destroy');
            Route::post('/theme-builder', [\App\Http\Controllers\Admin\ThemeBuilderController::class, 'update'])->name('admin.theme.update');
            Route::post('/design-builder', [\App\Http\Controllers\Admin\DesignBuilderController::class, 'update'])->name('admin.design.update');
            Route::post('/homepage-builder', [\App\Http\Controllers\Admin\HomepageBuilderController::class, 'update'])->name('admin.homepage-builder.update');
        });

        Route::middleware('permission:settings.manage')->group(function () {
            Route::get('/settings', [SettingsController::class, 'index'])->name('admin.settings');
            Route::post('/settings', [SettingsController::class, 'update'])->name('admin.settings.update');
        });
        Route::middleware('permission:social-media.manage')->group(function () {
            Route::get('/social-links', [SocialLinkController::class, 'index'])->name('admin.social-links.index');
            Route::post('/social-links', [SocialLinkController::class, 'store'])->name('admin.social-links.store');
            Route::patch('/social-links/{socialLink}', [SocialLinkController::class, 'update'])->name('admin.social-links.update');
            Route::post('/social-links/reorder', [SocialLinkController::class, 'reorder'])->name('admin.social-links.reorder');
            Route::delete('/social-links/{socialLink}', [SocialLinkController::class, 'destroy'])->name('admin.social-links.destroy');
        });
    });

    Route::get('/portal', ClientPortalController::class)->name('portal.dashboard')->middleware('role:client');
    Route::post('/logout', [AuthController::class, 'logout'])->name('logout');
});
