<?php

namespace App\Http\Controllers\Admin;

use App\Models\SystemSetting;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\View\View;

class SeoSettingsController
{
    private const KEYS = [
        'google_verification',
        'bing_verification',
        'meta_verification',
        'ga4_measurement_id',
        'indexnow_key',
    ];

    public function index(): View
    {
        $settings = SystemSetting::query()
            ->whereIn('key', array_map(fn ($key) => "seo.{$key}", self::KEYS))
            ->pluck('value', 'key')
            ->all();

        return view('admin.settings.seo', compact('settings'));
    }

    public function help(Request $request): View
    {
        return view('admin.settings.seo-help', ['topic' => (string) $request->query('topic', '')]);
    }

    public function update(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'seo.google_verification' => ['nullable', 'string', 'max:255'],
            'seo.bing_verification' => ['nullable', 'string', 'max:255'],
            'seo.meta_verification' => ['nullable', 'string', 'max:255'],
            'seo.ga4_measurement_id' => ['nullable', 'regex:/^G-[A-Z0-9]+$/i', 'max:40'],
            'seo.indexnow_key' => ['nullable', 'regex:/^[A-Za-z0-9_-]{8,128}$/', 'max:128'],
        ]);

        foreach (self::KEYS as $key) {
            $value = trim((string) data_get($validated, "seo.{$key}", ''));
            SystemSetting::updateOrCreate(
                ['key' => "seo.{$key}"],
                ['value' => $value, 'is_sensitive' => false]
            );
        }

        Cache::forget('fuelfree.system_settings');

        return back()->with('status', 'SEO and integration settings saved successfully.');
    }
}
