<?php

namespace App\Providers;

use App\Http\Controllers\Admin\DocumentController;
use App\Http\Controllers\Admin\ManagementController as AdminManagementController;
use App\Http\Controllers\Admin\ResilientDocumentController;
use App\Http\Controllers\ManagementController as PublicManagementController;
use App\Models\SystemSetting;
use Illuminate\Support\Facades\Blade;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->app->bind(DocumentController::class, ResilientDocumentController::class);
    }

    public function boot(): void
    {
        Blade::precompiler(function (string $template): string {
            $routeMap = ['admin.management.' => 'admin.profile-builder.', 'admin.page-builder.' => 'admin.cms.'];
            $template = str_replace(array_keys($routeMap), array_values($routeMap), $template);
            $template = preg_replace('/<option\s+value=["\']route:management["\'][^>]*>.*?<\/option>/is', '', $template) ?? $template;
            return str_replace(
                ['Advanced Menu Builder','Content Pages','Website Navigation','CONTENT MANAGEMENT','WEBSITE SECTIONS · MANAGEMENT','New CMS Page','Edit CMS Page','Add Management Member','Edit Management Profile','Add management member',"route('admin.site-content.index',['type'=>'news'])","request()->routeIs('admin.site-content.*') && request('type')==='news'",'News & Notices','News & notices','News &amp; Notices','News &amp; notices','name="settings[management][folder_id]" class="management-folder" required'],
                ['Menu Builder','Page Builder','Menu Builder','PAGE BUILDER','GLOBAL · PROFILE BUILDER','New Page','Edit Page','Add Profile','Edit Profile','Add profile',"route('admin.news_and_event.index')","request()->routeIs('admin.news_and_event*')",'News & Event','News & Event','News &amp; Event','News &amp; Event','name="settings[management][folder_id]" class="management-folder"'],
                $template
            );
        });

        $router = $this->app['router'];
        $this->app->booted(function () use ($router): void {
            // Backward-compatible URL for older Documents & Media links.
            // The current builder uses /admin/documents?folder={id}; keep the
            // legacy /admin/documents/folders/{id} URL functional instead of
            // letting it fall through to a server error or the global fallback.
            $router->get('/admin/documents/folders/{folder}', function (int $folder) {
                return redirect()->route('admin.documents', ['folder' => $folder]);
            })
                ->whereNumber('folder')
                ->middleware(['web', 'auth', 'permission:documents.view'])
                ->name('admin.documents.folders.compat');

            $router->fallback([PublicManagementController::class, 'folderFallback'])->name('management.folder');
        });

        try {
            if (!Schema::hasTable('system_settings')) return;
            $settings = Cache::rememberForever('fuelfree.system_settings', fn () => SystemSetting::query()->pluck('value','key')->all());
        } catch (\Throwable $e) { return; }
        if (array_key_exists('company.name',$settings)) config(['fuelfree.company.name'=>$settings['company.name']]);
        if (array_key_exists('company.domain',$settings)) config(['fuelfree.company.domain'=>$settings['company.domain']]);
        if (array_key_exists('company.tagline',$settings)) config(['fuelfree.company.tagline'=>$settings['company.tagline']]);
        if (array_key_exists('company.timezone',$settings)) config(['fuelfree.company.timezone'=>$settings['company.timezone']]);
        if (array_key_exists('company.logo_path',$settings)) config(['fuelfree.company.logo_path'=>$settings['company.logo_path']]);
        if (array_key_exists('storage.quota_gib',$settings)) config(['fuelfree.storage.quota_bytes'=>(int)round((float)$settings['storage.quota_gib']*1073741824)]);
        foreach (['header','footer'] as $section) { $prefix=$section.'.'; foreach ($settings as $key=>$value) if (str_starts_with($key,$prefix)) config(["fuelfree.{$section}.".substr($key,strlen($prefix))=>$value]); }
    }
}
