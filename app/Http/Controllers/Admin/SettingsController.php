<?php

namespace App\Http\Controllers\Admin;

use App\Models\SystemSetting;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\View\View;

class SettingsController
{
    public function index(Request $request): View
    {
        $defaults = [
            'company.name' => config('fuelfree.company.name'),
            'company.domain' => config('fuelfree.company.domain'),
            'company.tagline' => config('fuelfree.company.tagline'),
            'company.timezone' => config('fuelfree.company.timezone'),
            'company.logo_path' => '',
            'storage.quota_gib' => (string) round(config('fuelfree.storage.quota_bytes', 53687091200) / 1073741824),
        ];
        $footerDefaults = [
            'footer.tagline' => config('fuelfree.footer.tagline', ''),
            'footer.technology' => config('fuelfree.footer.technology', 'Fuel-Free Flywheel-Based Clean Energy Technology'),
            'footer.office_heading' => config('fuelfree.footer.office_heading', 'Office'),
            'footer.address' => config('fuelfree.footer.address', ''),
            'footer.contact_heading' => config('fuelfree.footer.contact_heading', 'Contact'),
            'footer.email' => config('fuelfree.footer.email', 'info@fuelfreepowerplant.com'),
            'footer.phone' => config('fuelfree.footer.phone', '+880 1712-251892'),
            'footer.website' => config('fuelfree.footer.website', 'www.fuelfreepowerplant.com'),
            'footer.website_url' => config('fuelfree.footer.website_url', 'https://www.fuelfreepowerplant.com'),
            'footer.get_in_touch_label' => config('fuelfree.footer.get_in_touch_label', 'Get in touch'),
            'footer.get_in_touch_url' => config('fuelfree.footer.get_in_touch_url', route('contact')),
            'footer.copyright_text' => config('fuelfree.footer.copyright_text', 'All rights reserved.'),
            'design.footer.columns_enabled' => '1',
            'design.footer.links_enabled' => '1',
            'design.footer.social_enabled' => '1',
            'design.footer.contact_enabled' => '1',
            'design.footer.copyright_enabled' => '1',
        ];
        $saved = SystemSetting::query()->pluck('value', 'key')->all();
        $settings = array_merge($defaults, $saved);
        if ($request->query('section') === 'footer') {
            $settings = array_merge($footerDefaults, $saved);
            return view('admin.settings.footer', compact('settings'));
        }
        return view('admin.settings.index', compact('settings'));
    }

    public function update(Request $request): RedirectResponse
    {
        if ($request->query('section') === 'footer') {
            return $this->updateFooter($request);
        }
        $validated = $request->validate([
            'company.name' => ['required', 'string', 'max:150'],
            'company.domain' => ['required', 'string', 'max:255'],
            'company.tagline' => ['nullable', 'string', 'max:255'],
            'company.timezone' => ['required', 'timezone'],
            'company.logo' => ['nullable', 'image', 'mimes:jpg,jpeg,png,webp,svg'],
        ]);
        $data = [
            'company.name' => data_get($validated, 'company.name'),
            'company.domain' => data_get($validated, 'company.domain'),
            'company.tagline' => data_get($validated, 'company.tagline'),
            'company.timezone' => data_get($validated, 'company.timezone'),
        ];
        if ($request->hasFile('company.logo')) {
            $old = SystemSetting::query()->where('key', 'company.logo_path')->value('value');
            if ($old) Storage::disk('public')->delete($old);
            $data['company.logo_path'] = $request->file('company.logo')->store('site/branding', 'public');
        }
        foreach ($data as $key => $value) {
            SystemSetting::updateOrCreate(['key' => $key], ['value' => (string) ($value ?? ''), 'is_sensitive' => false]);
        }
        return back()->with('status', 'System settings saved successfully.');
    }

    public function updateFooter(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'footer.tagline' => ['nullable', 'string', 'max:255'],
            'footer.technology' => ['nullable', 'string', 'max:255'],
            'footer.office_heading' => ['required', 'string', 'max:100'],
            'footer.address' => ['required', 'string', 'max:1000'],
            'footer.contact_heading' => ['required', 'string', 'max:100'],
            'footer.email' => ['nullable', 'email', 'max:255'],
            'footer.phone' => ['nullable', 'string', 'max:80'],
            'footer.website' => ['nullable', 'string', 'max:255'],
            'footer.website_url' => ['nullable', 'url', 'max:500'],
            'footer.get_in_touch_label' => ['nullable', 'string', 'max:100'],
            'footer.get_in_touch_url' => ['nullable', 'url', 'max:500'],
            'footer.copyright_text' => ['nullable', 'string', 'max:255'],
            'design.footer.columns_enabled' => ['nullable', 'boolean'],
            'design.footer.links_enabled' => ['nullable', 'boolean'],
            'design.footer.social_enabled' => ['nullable', 'boolean'],
            'design.footer.contact_enabled' => ['nullable', 'boolean'],
            'design.footer.copyright_enabled' => ['nullable', 'boolean'],
        ]);
        foreach (\Illuminate\Support\Arr::dot($validated) as $key => $value) {
            SystemSetting::updateOrCreate(
                ['key' => $key],
                [
                    'value' => is_bool($value) ? ($value ? '1' : '0') : (string) ($value ?? ''),
                    'is_sensitive' => false,
                ]
            );
        }
        foreach (['columns', 'links', 'social', 'contact', 'copyright'] as $key) {
            $setting = 'design.footer.'.$key.'_enabled';
            if (! \Illuminate\Support\Arr::has($validated, $setting)) {
                SystemSetting::updateOrCreate(['key' => $setting], ['value' => '0', 'is_sensitive' => false]);
            }
        }
        return back()->with('status', 'Footer settings saved successfully.');
    }
}
