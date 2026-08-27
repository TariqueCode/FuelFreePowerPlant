<?php

namespace App\Http\Controllers;

use App\Models\Inquiry;
use App\Models\EmailAccount;
use App\Services\WebmailService;
use App\Models\SiteContentItem;
use App\Models\SystemSetting;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Throwable;

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

        $settings = SystemSetting::query()->pluck('value', 'key')->all();
        $configuredMailboxId = (int) ($settings['mail.contact_account_id'] ?? 0);
        $infoMailbox = $configuredMailboxId
            ? EmailAccount::query()->whereKey($configuredMailboxId)->where('status','active')->first()
            : EmailAccount::query()->where('address','info@fuelfreepowerplant.com')->where('status','active')->first();
        if ($infoMailbox) {
            try {
                app(WebmailService::class)->send(
                    $infoMailbox->address,
                    $infoMailbox->password,
                    $infoMailbox->address,
                    $data['subject'],
                    '<p><strong>New website inquiry</strong></p><p>Name: '.e($data['name']).'<br>Email: '.e($data['email']).'<br>Phone: '.e($data['phone'] ?: 'Not provided').'</p><p>'.nl2br(e($data['message'])).'</p>',
                    ['imap_host'=>$infoMailbox->imap_host,'imap_port'=>$infoMailbox->imap_port,'smtp_host'=>$infoMailbox->smtp_host,'smtp_port'=>$infoMailbox->smtp_port]
                );
            } catch (Throwable $e) {
                report($e);
            }
        }

        return back()->with('contact_status', 'Thank you. Your inquiry has been received and our team will contact you soon.');
    }
}
