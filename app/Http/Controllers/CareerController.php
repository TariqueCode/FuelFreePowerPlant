<?php

namespace App\Http\Controllers;

use App\Models\CareerApplication;
use App\Models\EmailAccount;
use App\Services\WebmailService;
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

        $settings = SystemSetting::query()->pluck('value', 'key')->all();
        $configuredMailboxId = (int) ($settings['mail.career_account_id'] ?? 0);
        $careerMailbox = $configuredMailboxId
            ? EmailAccount::query()->whereKey($configuredMailboxId)->where('status','active')->first()
            : EmailAccount::query()->where('address','career@fuelfreepowerplant.com')->where('status','active')->first();
        if ($careerMailbox) {
            try {
                app(WebmailService::class)->send(
                    $careerMailbox->address,
                    $careerMailbox->password,
                    $careerMailbox->address,
                    'New career application: '.$application->name,
                    '<p><strong>New career application received.</strong></p><p>Name: '.e($application->name).'<br>Email: '.e($application->email).'<br>Phone: '.e($application->phone ?: 'Not provided').'<br>Position: '.e($application->position ?: 'General application').'</p><p>The full candidate profile is available in the admin Career section.</p>',
                    ['imap_host'=>$careerMailbox->imap_host,'imap_port'=>$careerMailbox->imap_port,'smtp_host'=>$careerMailbox->smtp_host,'smtp_port'=>$careerMailbox->smtp_port],
                    ['path'=>Storage::disk('local')->path($path),'name'=>$application->cv_original_name,'mime'=>$file->getMimeType() ?: 'application/octet-stream']
                );
            } catch (Throwable $e) {
                report($e);
            }
        }

        return back()->with('career_status', 'Your application has been received. Our career team will review your information and contact you if your profile matches an opportunity.');
    }
}