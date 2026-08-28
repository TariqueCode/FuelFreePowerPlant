<?php

namespace App\Providers;

use App\Models\SystemSetting;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        //
    }

    public function boot(): void
    {
        if (! Schema::hasTable('system_settings')) {
            return;
        }

        $settings = SystemSetting::query()->pluck('value', 'key');
        if ($settings->has('company.name')) config(['fuelfree.company.name' => $settings['company.name']]);
        if ($settings->has('company.domain')) config(['fuelfree.company.domain' => $settings['company.domain']]);
        if ($settings->has('company.tagline')) config(['fuelfree.company.tagline' => $settings['company.tagline']]);
        if ($settings->has('company.timezone')) config(['fuelfree.company.timezone' => $settings['company.timezone']]);
        if ($settings->has('company.logo_path')) config(['fuelfree.company.logo_path' => $settings['company.logo_path']]);
        if ($settings->has('storage.quota_gib')) config(['fuelfree.storage.quota_bytes' => (int) round((float) $settings['storage.quota_gib'] * 1073741824)]);

        foreach (['header','footer'] as $section) {
            $prefix = $section . '.';
            foreach ($settings as $key => $value) {
                if (str_starts_with($key, $prefix)) {
                    config(["fuelfree.{$section}.".substr($key, strlen($prefix)) => $value]);
                }
            }
        }
    }
}
