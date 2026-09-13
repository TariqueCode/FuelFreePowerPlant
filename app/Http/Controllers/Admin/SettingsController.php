<?php

namespace App\Http\Controllers\Admin;

use App\Models\SystemSetting;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\View\View;

class SettingsController
{
    public function index(): View
    {
        $defaults = [
            'company.name' => config('fuelfree.company.name'),
            'company.domain' => config('fuelfree.company.domain'),
            'company.tagline' => config('fuelfree.company.tagline'),
            'company.timezone' => config('fuelfree.company.timezone'),
            'company.logo_path' => '',
            'storage.quota_gib' => (string) round(config('fuelfree.storage.quota_bytes', 53687091200) / 1073741824),
        ];

        $saved = SystemSetting::query()->pluck('value', 'key')->all();
        $settings = array_merge($defaults, $saved);

        return view('admin.settings.index', compact('settings'));
    }

    public function update(Request $request): RedirectResponse
    {
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
            if ($old) {
                Storage::disk('public')->delete($old);
            }
            $data['company.logo_path'] = $request->file('company.logo')->store('site/branding', 'public');
        }

        foreach ($data as $key => $value) {
            SystemSetting::updateOrCreate(
                ['key' => $key],
                ['value' => (string) ($value ?? ''), 'is_sensitive' => false]
            );
        }

        return back()->with('status', 'System settings saved successfully.');
    }
}
