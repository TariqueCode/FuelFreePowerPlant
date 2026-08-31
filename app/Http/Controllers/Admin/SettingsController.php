<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\EmailAccount;
use App\Models\SystemSetting;
use App\Services\WebmailService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\View\View;
use Throwable;

class SettingsController
{
    public function index(): View
    {
        $defaults=[
            'company.name'=>config('fuelfree.company.name'),'company.domain'=>config('fuelfree.company.domain'),'company.tagline'=>config('fuelfree.company.tagline'),
            'company.timezone'=>config('fuelfree.company.timezone'),'company.logo_path'=>'',
            'storage.quota_gib'=>(string)round(config('fuelfree.storage.quota_bytes',53687091200)/1073741824),'uploads.max_mb'=>(string)config('fuelfree.upload.max_mb',50),'uploads.career_max_mb'=>(string)config('fuelfree.upload.career_max_mb',50),'uploads.documents_max_mb'=>(string)config('fuelfree.upload.documents_max_mb',50),'uploads.gallery_max_mb'=>(string)config('fuelfree.upload.gallery_max_mb',50),'uploads.sliders_max_mb'=>(string)config('fuelfree.upload.sliders_max_mb',50),'uploads.popups_max_mb'=>(string)config('fuelfree.upload.popups_max_mb',50),'uploads.content_media_max_mb'=>(string)config('fuelfree.upload.content_media_max_mb',100),'home.news_limit'=>'3','home.gallery_limit'=>'4',
            'mail.contact_account_id'=>'','mail.career_account_id'=>'',
            'header.home_label'=>'Home','header.management_label'=>'Management Team','header.gallery_label'=>'Gallery','header.news_label'=>'News & Notices',
            'header.career_label'=>'Career','header.contact_label'=>'Contact','header.webmail_label'=>'Webmail','header.portal_label'=>'Portal','header.login_label'=>'Login',
            'footer.tagline'=>'Powering a cleaner, smarter future.','footer.technology'=>'Fuel-Free Flywheel-Based Clean Energy Technology',
            'footer.office_heading'=>'Office','footer.address'=>'House-141, 3rd Floor, Road-22, Mohakhali DOHS, Dhaka-1206, Bangladesh',
            'footer.contact_heading'=>'Contact','footer.email'=>'info@fuelfreepowerplant.com','footer.phone'=>'+880 1712-251892',
            'footer.website'=>'www.fuelfreepowerplant.com','footer.website_url'=>'https://www.fuelfreepowerplant.com','footer.get_in_touch_label'=>'Get in touch',
            'footer.get_in_touch_url'=>'/contact','footer.copyright_text'=>'All rights reserved.',
            'home.slider_enabled'=>'1','home.welcome_enabled'=>'1','home.news_enabled'=>'1','home.gallery_enabled'=>'1','home.hero_enabled'=>'1','home.statistics_enabled'=>'1','home.projects_enabled'=>'1','home.cta_enabled'=>'1',
        ];
        $saved=SystemSetting::query()->pluck('value','key')->all();
        $settings=array_merge($defaults,$saved);
        $contactAccount=EmailAccount::query()->where('mailbox_group','contact')->where('status','active')->where('address','like','%@fuelfreepowerplant.com')->first();
        $careerAccount=EmailAccount::query()->where('mailbox_group','career')->where('status','active')->where('address','like','%@fuelfreepowerplant.com')->first();
        return view('admin.settings.index',compact('settings','contactAccount','careerAccount'));
    }

    public function verifyMailbox(Request $request, WebmailService $webmail): RedirectResponse|JsonResponse
    {
        $group = (string) $request->input('group');
        abort_unless(in_array($group, ['contact', 'career'], true), 404);

        $label = $group === 'career' ? 'Career' : 'Contact';
        $email = strtolower(trim((string) $request->input('email')));
        $password = (string) $request->input('password');

        if (!filter_var($email, FILTER_VALIDATE_EMAIL) || !str_ends_with($email, '@fuelfreepowerplant.com')) {
            $message = $label.' mailbox address is invalid.';
            if ($request->expectsJson()) return response()->json(['message' => $message], 422);
            return back()->withErrors(['mail.'.$group.'_email' => $message])->withInput($request->except(['mail.contact_password','mail.career_password']));
        }
        if ($password === '') {
            $message = $label.' mailbox password is required for verification.';
            if ($request->expectsJson()) return response()->json(['message' => $message], 422);
            return back()->withErrors(['mail.'.$group.'_password' => $message])->withInput($request->except(['mail.contact_password','mail.career_password']));
        }

        // Use the exact same mailbox configuration used by the working Webmail login.
        // If this address already has an active EmailAccount, its stored cPanel
        // IMAP/SMTP host and ports take precedence over global defaults.
        $account = EmailAccount::query()
            ->where('address', $email)
            ->where('status', 'active')
            ->first(['imap_host', 'imap_port', 'smtp_host', 'smtp_port']);

        $config = [
            'imap_host' => $account?->imap_host ?: config('cpanel.mail_host', 'mail.fuelfreepowerplant.com'),
            'imap_port' => $account?->imap_port ?: 993,
            'smtp_host' => $account?->smtp_host ?: config('cpanel.mail_host', 'mail.fuelfreepowerplant.com'),
            'smtp_port' => $account?->smtp_port ?: 465,
        ];

        try {
            $webmail->login($email, $password, $config);

            // Keep only a non-reversible verification fingerprint in the session.
            // The password itself is never stored by the verification action.
            $fingerprint = hash_hmac('sha256', $group.'|'.$email.'|'.$password, (string) config('app.key'));
            $request->session()->put('mailbox_verification.'.$group, [
                'email' => $email,
                'fingerprint' => $fingerprint,
                'verified_at' => now()->toIso8601String(),
            ]);

            $message = $label.' mailbox login verified successfully. You can now save this mailbox.';
            if ($request->expectsJson()) {
                return response()->json(['message' => $message]);
            }
            return back()->with('mail_verify_'.$group, $message);
        } catch (Throwable $e) {
            report($e);
            $request->session()->forget('mailbox_verification.'.$group);
            $message = $label.' mailbox login failed. Check the email, password and cPanel mail server settings.';
            if ($request->expectsJson()) {
                return response()->json(['message' => $message], 422);
            }
            return back()->withErrors(['mail.'.$group.'_email' => $message])->withInput($request->except(['mail.contact_password','mail.career_password']));
        }
    }

    public function update(Request $request,WebmailService $webmail): RedirectResponse
    {
        $validated=$request->validate([
            'company.name'=>['required','string','max:150'],'company.domain'=>['required','string','max:255'],'company.tagline'=>['nullable','string','max:255'],
            'company.timezone'=>['required','timezone'],'storage.quota_gib'=>['required','numeric','min:1','max:1048576'],'uploads.max_mb'=>['required','integer','min:1','max:1048576'],'uploads.career_max_mb'=>['required','integer','min:1','max:1048576'],'uploads.documents_max_mb'=>['required','integer','min:1','max:1048576'],'uploads.gallery_max_mb'=>['required','integer','min:1','max:1048576'],'uploads.sliders_max_mb'=>['required','integer','min:1','max:1048576'],'uploads.popups_max_mb'=>['required','integer','min:1','max:1048576'],'uploads.content_media_max_mb'=>['required','integer','min:1','max:1048576'],
            'home.news_limit'=>['required','integer','min:1','max:100'],'home.gallery_limit'=>['required','integer','min:1','max:100'],
            'home.slider_enabled'=>['nullable','boolean'],'home.welcome_enabled'=>['nullable','boolean'],'home.news_enabled'=>['nullable','boolean'],'home.gallery_enabled'=>['nullable','boolean'],'home.hero_enabled'=>['nullable','boolean'],'home.statistics_enabled'=>['nullable','boolean'],'home.projects_enabled'=>['nullable','boolean'],'home.cta_enabled'=>['nullable','boolean'],
            'mail.contact_email'=>['nullable','email:rfc,dns','ends_with:@fuelfreepowerplant.com','max:255'],'mail.contact_password'=>['nullable','string','max:1000'],
            'mail.career_email'=>['nullable','email:rfc,dns','ends_with:@fuelfreepowerplant.com','max:255'],'mail.career_password'=>['nullable','string','max:1000'],
            'company.logo'=>['nullable','image','mimes:jpg,jpeg,png,webp,svg'],'header.*'=>['required','string','max:100'],
            'footer.tagline'=>['nullable','string','max:255'],'footer.technology'=>['nullable','string','max:255'],'footer.office_heading'=>['required','string','max:100'],
            'footer.address'=>['required','string','max:500'],'footer.contact_heading'=>['required','string','max:100'],'footer.email'=>['required','email','max:255'],
            'footer.phone'=>['required','string','max:50'],'footer.website'=>['required','string','max:255'],'footer.website_url'=>['required','url','max:255'],
            'footer.get_in_touch_label'=>['required','string','max:100'],'footer.get_in_touch_url'=>['required','string','max:255'],'footer.copyright_text'=>['required','string','max:150'],
        ]);

        $data=[
            'company.name'=>data_get($validated,'company.name'),'company.domain'=>data_get($validated,'company.domain'),'company.tagline'=>data_get($validated,'company.tagline'),
            'company.timezone'=>data_get($validated,'company.timezone'),'storage.quota_gib'=>data_get($validated,'storage.quota_gib'),'uploads.max_mb'=>data_get($validated,'uploads.max_mb'),'uploads.career_max_mb'=>data_get($validated,'uploads.career_max_mb'),'uploads.documents_max_mb'=>data_get($validated,'uploads.documents_max_mb'),'uploads.gallery_max_mb'=>data_get($validated,'uploads.gallery_max_mb'),'uploads.sliders_max_mb'=>data_get($validated,'uploads.sliders_max_mb'),'uploads.popups_max_mb'=>data_get($validated,'uploads.popups_max_mb'),'uploads.content_media_max_mb'=>data_get($validated,'uploads.content_media_max_mb'),
            'home.news_limit'=>data_get($validated,'home.news_limit'),'home.gallery_limit'=>data_get($validated,'home.gallery_limit'),
            'home.slider_enabled'=>$request->boolean('home.slider_enabled')?'1':'0','home.welcome_enabled'=>$request->boolean('home.welcome_enabled')?'1':'0',
            'home.news_enabled'=>$request->boolean('home.news_enabled')?'1':'0','home.gallery_enabled'=>$request->boolean('home.gallery_enabled')?'1':'0','home.hero_enabled'=>$request->boolean('home.hero_enabled')?'1':'0','home.statistics_enabled'=>$request->boolean('home.statistics_enabled')?'1':'0','home.projects_enabled'=>$request->boolean('home.projects_enabled')?'1':'0','home.cta_enabled'=>$request->boolean('home.cta_enabled')?'1':'0',
            'header.home_label'=>data_get($validated,'header.home_label'),'header.management_label'=>data_get($validated,'header.management_label'),
            'header.gallery_label'=>data_get($validated,'header.gallery_label'),'header.news_label'=>data_get($validated,'header.news_label'),
            'header.career_label'=>data_get($validated,'header.career_label'),'header.contact_label'=>data_get($validated,'header.contact_label'),
            'header.webmail_label'=>data_get($validated,'header.webmail_label'),'header.portal_label'=>data_get($validated,'header.portal_label'),'header.login_label'=>data_get($validated,'header.login_label'),
            'footer.tagline'=>data_get($validated,'footer.tagline'),'footer.technology'=>data_get($validated,'footer.technology'),'footer.office_heading'=>data_get($validated,'footer.office_heading'),
            'footer.address'=>data_get($validated,'footer.address'),'footer.contact_heading'=>data_get($validated,'footer.contact_heading'),'footer.email'=>data_get($validated,'footer.email'),
            'footer.phone'=>data_get($validated,'footer.phone'),'footer.website'=>data_get($validated,'footer.website'),'footer.website_url'=>data_get($validated,'footer.website_url'),
            'footer.get_in_touch_label'=>data_get($validated,'footer.get_in_touch_label'),'footer.get_in_touch_url'=>data_get($validated,'footer.get_in_touch_url'),'footer.copyright_text'=>data_get($validated,'footer.copyright_text'),
        ];

        foreach([
            ['group'=>'contact','email'=>$request->input('mail.contact_email'),'password'=>$request->input('mail.contact_password'),'fallback'=>'info@fuelfreepowerplant.com','label'=>'Contact'],
            ['group'=>'career','email'=>$request->input('mail.career_email'),'password'=>$request->input('mail.career_password'),'fallback'=>'career@fuelfreepowerplant.com','label'=>'Career'],
        ] as $mail){
            $existing=EmailAccount::query()->where('mailbox_group',$mail['group'])->where('status','active')->first();
            $email=strtolower(trim((string)($mail['email'] ?: ($existing?->address ?: $mail['fallback']))));
            $password=(string)$mail['password'];
            $hasMailboxInput=trim((string)$mail['email'])!=='' || $password!=='';
            if(!$hasMailboxInput) continue;
            if($password==='' && $existing && $email===$existing->address) continue;
            if($password==='') return back()->withErrors(['mail.'.$mail['group'].'_password'=>$mail['label'].' mailbox password is required when connecting a mailbox.'])->withInput($request->except(['mail.contact_password','mail.career_password']));

            $verification=$request->session()->get('mailbox_verification.'.$mail['group']);
            $fingerprint=hash_hmac('sha256',$mail['group'].'|'.$email.'|'.$password,(string) config('app.key'));
            if(!is_array($verification) || ($verification['email']??'')!==$email || !hash_equals((string)($verification['fingerprint']??''),$fingerprint)){
                return back()->withErrors(['mail.'.$mail['group'].'_email'=>'Verify the '.$mail['label'].' mailbox login successfully before saving these credentials.'])->withInput($request->except(['mail.contact_password','mail.career_password']));
            }

            $config=['imap_host'=>config('cpanel.mail_host','mail.fuelfreepowerplant.com'),'imap_port'=>993,'smtp_host'=>config('cpanel.mail_host','mail.fuelfreepowerplant.com'),'smtp_port'=>465];

            EmailAccount::query()->where('mailbox_group',$mail['group'])->where('address','!=',$email)->update(['status'=>'inactive']);
            $account=EmailAccount::updateOrCreate(['address'=>$email],[
                'user_id'=>$request->user()->id,'address'=>$email,'display_name'=>$mail['label'].' mailbox','mailbox_group'=>$mail['group'],'status'=>'active',
                'imap_host'=>$config['imap_host'],'imap_port'=>$config['imap_port'],'smtp_host'=>$config['smtp_host'],'smtp_port'=>$config['smtp_port'],
                'username'=>$email,'password'=>$password,'provisioned'=>true,'provider_message'=>'Verified from System Settings.',
            ]);
            SystemSetting::updateOrCreate(['key'=>'mail.'.$mail['group'].'_account_id'],['value'=>(string)$account->id,'is_sensitive'=>false]);
            $request->session()->forget('mailbox_verification.'.$mail['group']);
        }

        if($request->hasFile('company.logo')){
            $old=SystemSetting::query()->where('key','company.logo_path')->value('value');
            if($old) Storage::disk('public')->delete($old);
            $data['company.logo_path']=$request->file('company.logo')->store('site/branding','public');
        }
        foreach($data as $key=>$value) SystemSetting::updateOrCreate(['key'=>$key],['value'=>(string)($value??''),'is_sensitive'=>false]);
        return back()->with('status','System settings saved. Contact and Career mailboxes were verified and connected.');
    }
}
