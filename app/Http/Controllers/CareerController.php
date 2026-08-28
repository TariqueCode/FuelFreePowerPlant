<?php

namespace App\Http\Controllers;

use App\Models\CareerApplication;
use App\Models\SiteContentItem;
use App\Models\SystemSetting;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Throwable;
use Illuminate\Validation\Rules\File;
use Illuminate\View\View;

class CareerController extends Controller
{
    public function show(): View
    {
        $settings = SystemSetting::query()->pluck('value', 'key')->all();
        $brand = [
            'name' => $settings['company.name'] ?? config('fuelfree.company.name'),
            'logo_path' => $settings['company.logo_path'] ?? null,
            'tagline' => $settings['company.tagline'] ?? config('fuelfree.company.tagline'),
        ];
        $page = SiteContentItem::published()
            ->whereIn('type', ['career','careers','job'])
            ->orderBy('sort_order')->orderBy('title')->get();

        return view('career', compact('brand','page'));
    }

    public function store(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'name' => ['required','string','max:120'],
            'email' => ['required','email','max:190'],
            'phone' => ['nullable','string','max:40'],
            'position' => ['nullable','string','max:180'],
            'education' => ['nullable','string','max:255'],
            'experience' => ['nullable','string','max:180'],
            'location' => ['nullable','string','max:180'],
            'message' => ['nullable','string','max:5000'],
            'cv' => ['required', File::types(['pdf','doc','docx'])->max('8mb')],
            'consent' => ['accepted'],
            'website' => ['nullable','string','max:0'],
        ]);

        unset($data['website']);
        $file = $request->file('cv');
        $path = $file->store('career/cv', 'local');

        $application = CareerApplication::create([
            ...$data,
            'cv_path' => $path,
            'cv_original_name' => $file->getClientOriginalName(),
            'status' => 'new',
        ]);

        return back()->with('career_status', 'Your application has been received. Our career team will review your information and contact you if your profile matches an opportunity.');
    }
}