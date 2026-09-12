<?php

namespace App\Http\Controllers\Admin;

use App\Models\SystemSetting;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\View\View;

class FooterController
{
    private const DEFAULTS = [
        'footer.tagline' => 'Powering a cleaner, smarter future.',
        'footer.technology' => 'Fuel-Free Flywheel-Based Clean Energy Technology',
        'footer.office_heading' => 'Office',
        'footer.address' => 'House-141, 3rd Floor, Road-22, Mohakhali DOHS, Dhaka-1206, Bangladesh',
        'footer.contact_heading' => 'Contact',
        'footer.email' => 'info@fuelfreepowerplant.com',
        'footer.phone' => '+880 1712-251892',
        'footer.website' => 'www.fuelfreepowerplant.com',
        'footer.website_url' => 'https://www.fuelfreepowerplant.com',
        'footer.get_in_touch_label' => 'Get in touch',
        'footer.get_in_touch_url' => '/contact',
        'footer.copyright_text' => 'All rights reserved.',
        'footer.developer_prefix' => 'Developed by',
        'footer.developer_name' => 'Saif Al-Islam',
        'footer.developer_email' => 'TariqueBN@gmail.com',
        'design.footer.columns_enabled' => '1',
        'design.footer.contact_enabled' => '1',
        'design.footer.social_enabled' => '1',
        'design.footer.copyright_enabled' => '1',
    ];

    public function index(): View
    {
        $saved = SystemSetting::query()
            ->where(function ($query): void {
                $query->where('key', 'like', 'footer.%')
                    ->orWhere('key', 'like', 'design.footer.%');
            })
            ->pluck('value', 'key')
            ->all();

        $settings = array_merge(self::DEFAULTS, $saved);
        $visibility = [
            'columns' => filter_var($settings['design.footer.columns_enabled'], FILTER_VALIDATE_BOOLEAN),
            'contact' => filter_var($settings['design.footer.contact_enabled'], FILTER_VALIDATE_BOOLEAN),
            'social' => filter_var($settings['design.footer.social_enabled'], FILTER_VALIDATE_BOOLEAN),
            'copyright' => filter_var($settings['design.footer.copyright_enabled'], FILTER_VALIDATE_BOOLEAN),
        ];

        return view('admin.footer.index', compact('settings', 'visibility'));
    }

    public function update(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'footer.tagline' => ['nullable', 'string', 'max:255'],
            'footer.technology' => ['nullable', 'string', 'max:255'],
            'footer.office_heading' => ['required', 'string', 'max:100'],
            'footer.address' => ['required', 'string', 'max:1000'],
            'footer.contact_heading' => ['required', 'string', 'max:100'],
            'footer.email' => ['nullable', 'email', 'max:255'],
            'footer.phone' => ['nullable', 'string', 'max:100'],
            'footer.website' => ['nullable', 'string', 'max:255'],
            'footer.website_url' => ['nullable', 'string', 'max:500'],
            'footer.get_in_touch_label' => ['nullable', 'string', 'max:100'],
            'footer.get_in_touch_url' => ['nullable', 'string', 'max:500'],
            'footer.copyright_text' => ['nullable', 'string', 'max:255'],
            'footer.developer_prefix' => ['nullable', 'string', 'max:100'],
            'footer.developer_name' => ['nullable', 'string', 'max:150'],
            'footer.developer_email' => ['nullable', 'email', 'max:255'],
            'columns_enabled' => ['nullable', 'boolean'],
            'contact_enabled' => ['nullable', 'boolean'],
            'social_enabled' => ['nullable', 'boolean'],
            'copyright_enabled' => ['nullable', 'boolean'],
        ]);

        $keys = array_keys(self::DEFAULTS);
        foreach ($keys as $key) {
            if (str_starts_with($key, 'footer.')) {
                SystemSetting::updateOrCreate(
                    ['key' => $key],
                    ['value' => (string) ($data[$key] ?? ''), 'is_sensitive' => false]
                );
            }
        }

        $visibility = [
            'design.footer.columns_enabled' => $request->boolean('columns_enabled') ? '1' : '0',
            'design.footer.contact_enabled' => $request->boolean('contact_enabled') ? '1' : '0',
            'design.footer.social_enabled' => $request->boolean('social_enabled') ? '1' : '0',
            'design.footer.copyright_enabled' => $request->boolean('copyright_enabled') ? '1' : '0',
        ];

        foreach ($visibility as $key => $value) {
            SystemSetting::updateOrCreate(
                ['key' => $key],
                ['value' => $value, 'is_sensitive' => false]
            );
        }

        Cache::forget('fuelfree.system_settings');
        Cache::forget('public.social-links');

        return back()->with('status', 'Footer settings saved successfully.');
    }
}
