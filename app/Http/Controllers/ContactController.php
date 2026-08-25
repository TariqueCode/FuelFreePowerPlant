<?php

namespace App\Http\Controllers;

use App\Models\Inquiry;
use App\Models\SiteContentItem;
use App\Models\SystemSetting;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class ContactController extends Controller
{
    public function show(): View
    {
        $settings = SystemSetting::query()->pluck('value', 'key')->all();
        $brand = [
            'name' => $settings['company.name'] ?? config('fuelfree.company.name'),
            'domain' => $settings['company.domain'] ?? config('fuelfree.company.domain'),
            'tagline' => $settings['company.tagline'] ?? config('fuelfree.company.tagline'),
            'logo_path' => $settings['company.logo_path'] ?? null,
        ];
        $office = SiteContentItem::published()->where('type', 'company')->where('slug', 'contact')->first();
        return view('contact', compact('brand', 'office'));
    }

    public function store(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'name' => ['required', 'string', 'max:120'],
            'email' => ['required', 'email', 'max:190'],
            'phone' => ['nullable', 'string', 'max:40'],
            'subject' => ['required', 'string', 'max:180'],
            'message' => ['required', 'string', 'max:5000'],
            'website' => ['nullable', 'string', 'max:0'],
        ]);

        unset($data['website']);
        Inquiry::create($data);

        return back()->with('contact_status', 'Thank you. Your inquiry has been received and our team will contact you soon.');
    }
}
