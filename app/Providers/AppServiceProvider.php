<?php

namespace App\Providers;

use App\Models\SystemSetting;
use Illuminate\Support\Facades\Cache;
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

        $settings = Cache::rememberForever(
            'fuelfree.system_settings',
            fn () => SystemSetting::query()->pluck('value', 'key')->all()
        );

        if (array_key_exists('company.name', $settings)) config(['fuelfree.company.name' => $settings['company.name']]);
        if (array_key_exists('company.domain', $settings)) config(['fuelfree.company.domain' => $settings['company.domain']]);
        if (array_key_exists('company.tagline', $settings)) config(['fuelfree.company.tagline' => $settings['company.tagline']]);
        if (array_key_exists('company.timezone', $settings)) config(['fuelfree.company.timezone' => $settings['company.timezone']]);
        if (array_key_exists('company.logo_path', $settings)) config(['fuelfree.company.logo_path' => $settings['company.logo_path']]);
        if (array_key_exists('storage.quota_gib', $settings)) config(['fuelfree.storage.quota_bytes' => (int) round((float) $settings['storage.quota_gib'] * 1073741824)]);

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
