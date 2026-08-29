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
use Illuminate\Support\Facades\Cache;
use Illuminate\View\View;
use Throwable;

class SettingsController
{
    public function index(): View
    {
        $defaults=[
            'company.name'=>config('fuelfree.company.name'),'company.domain'=>config('fuelfree.company.domain'),'company.tagline'=>config('fuelfree.company.tagline'),
            'company.timezone'=>config('fuelfree.company.timezone'),'company.logo_path'=>'',
            'storage.quota_gib'=>(string)round(config('fuelfree.storage.quota_bytes',53687091200)/1073741824),'home.news_limit'=>'3','home.gallery_limit'=>'4',
            'mail.contact_account_id'=>'','mail.career_account_id'=>'',
            'header.home_label'=>'Home','header.management_label'=>'Management Team','header.gallery_label'=>'Gallery','header.news_label'=>'News & Notices',
            'header.career_label'=>'Career','header.contact_label'=>'Contact','header.webmail_label'=>'Webmail','header.portal_label'=>'Portal','header.login_label'=>'Login',
            'footer.tagline'=>'Powering a cleaner, smarter future.','footer.technology'=>'Fuel-Free Flywheel-Based Clean Energy Technology',
            'footer.office_heading'=>'Office','footer.address'=>'House-141, 3rd Floor, Road-22, Mohakhali DOHS, Dhaka-1206, Bangladesh',
            'footer.contact_heading'=>'Contact','footer.email'=>'info@fuelfreepowerplant.com','footer.phone'=>'+880 1712-251892',
            'footer.website'=>'www.fuelfreepowerplant.com','footer.website_url'=>'https://www.fuelfreepowerplant.com','footer.get_in_touch_label'=>'Get in touch',
            'footer.get_in_touch_url'=>'/contact','footer.copyright_text'=>'All rights reserved.',
            'home.slider_enabled'=>'1','home.welcome_enabled'=>'1','home.news_enabled'=>'1','home.gallery_enabled'=>'1',
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

    public function header(): View
    {
        $defaults = [
            'header.home_label'=>'Home','header.management_label'=>'Management Team','header.gallery_label'=>'Gallery',
            'header.news_label'=>'News & Notices','header.career_label'=>'Career','header.contact_label'=>'Contact',
            'header.webmail_label'=>'Webmail','header.portal_label'=>'Portal','header.login_label'=>'Login',
        ];
        $settings = array_merge($defaults, SystemSetting::query()->whereIn('key', array_keys($defaults))->pluck('value','key')->all());
        return view('admin.settings.header', compact('settings'));
    }

    public function theme(): View
    {
        $keys = ['theme.primary','theme.secondary','theme.accent','theme.surface','theme.text','theme.muted','theme.radius','theme.font_body','theme.font_heading','theme.base_size','theme.line_height','theme.space_section','theme.space_content'];
        $defaults = ['theme.primary'=>'#55cce7','theme.secondary'=>'#0f2430','theme.accent'=>'#9de8f7','theme.surface'=>'#07131a','theme.text'=>'#eaf7fb','theme.muted'=>'#8ea8b2','theme.radius'=>'12','theme.font_body'=>'Inter, sans-serif','theme.font_heading'=>'Inter, sans-serif','theme.base_size'=>'16','theme.line_height'=>'1.6','theme.space_section'=>'64','theme.space_content'=>'24'];
        $settings = array_merge($defaults, SystemSetting::query()->whereIn('key',$keys)->pluck('value','key')->all());
        return view('admin.settings.theme', compact('settings'));
    }

    public function updateTheme(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'theme.primary'=>['required','regex:/^#[0-9A-Fa-f]{6}$/'],
            'theme.secondary'=>['required','regex:/^#[0-9A-Fa-f]{6}$/'],
            'theme.accent'=>['required','regex:/^#[0-9A-Fa-f]{6}$/'],
            'theme.surface'=>['required','regex:/^#[0-9A-Fa-f]{6}$/'],
            'theme.text'=>['required','regex:/^#[0-9A-Fa-f]{6}$/'],
            'theme.muted'=>['required','regex:/^#[0-9A-Fa-f]{6}$/'],
            'theme.radius'=>['required','integer','min:0','max:32'],
            'theme.font_body'=>['required','string','max:120'],'theme.font_heading'=>['required','string','max:120'],
            'theme.base_size'=>['required','integer','min:12','max:22'],'theme.line_height'=>['required','numeric','min:1.1','max:2.2'],
            'theme.space_section'=>['required','integer','min:0','max:160'],'theme.space_content'=>['required','integer','min:0','max:80'],
        ]);
        foreach($data as $key=>$value) SystemSetting::updateOrCreate(['key'=>$key],['value'=>$value,'is_sensitive'=>false]);
        Cache::forget('fuelfree.system_settings');
        return back()->with('status','Global theme saved successfully.');
    }

    public function menu(): View
    {
        $items = \App\Models\SiteContentItem::query()
            ->where('type','company')->where('status','published')
            ->orderByRaw('CASE WHEN navigation_order IS NULL THEN 1 ELSE 0 END')
            ->orderBy('navigation_order')->orderByDesc('created_at')
            ->get(['id','title','slug','show_in_navigation','navigation_order']);
        return view('admin.settings.menu', compact('items'));
    }

    public function updateMenu(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'items'=>['nullable','array','max:100'],
            'items.*.id'=>['required','integer'],
            'items.*.show_in_navigation'=>['nullable','boolean'],
            'items.*.navigation_order'=>['nullable','integer','min:0','max:9999'],
            'custom_items'=>['nullable','array','max:30'],
            'custom_items.*.label'=>['required','string','max:80'],
            'custom_items.*.url'=>['required','url','max:500'],
        ]);
        $ids = collect($data['items'] ?? [])->pluck('id')->all();
        \App\Models\SiteContentItem::query()->whereIn('id',$ids)->where('type','company')->get()->each(function ($item) use ($data) {
            $row = collect($data['items'])->firstWhere('id',$item->id);
            $item->show_in_navigation = (bool)($row['show_in_navigation'] ?? false);
            $item->navigation_order = $row['navigation_order'] ?? null;
            $item->save();
        });
        Cache::forget('public.company-navigation');
        if ($request->filled('custom_items')) { SystemSetting::updateOrCreate(['key'=>'navigation.custom_items'],['value'=>json_encode(array_values($data['custom_items'] ?? []), JSON_UNESCAPED_SLASHES),'is_sensitive'=>false]); } else { SystemSetting::updateOrCreate(['key'=>'navigation.custom_items'],['value'=>'[]','is_sensitive'=>false]); }
        Cache::forget('fuelfree.system_settings');
        return back()->with('status','Global navigation saved successfully.');
    }

    public function footer(): View
    {
        $keys = ['footer.tagline','footer.technology','footer.office_heading','footer.address','footer.contact_heading','footer.email','footer.phone','footer.website','footer.website_url','footer.get_in_touch_label','footer.get_in_touch_url','footer.copyright_text'];
        $defaults = [
            'footer.tagline'=>'Powering a cleaner, smarter future.','footer.technology'=>'Fuel-Free Flywheel-Based Clean Energy Technology',
            'footer.office_heading'=>'Office','footer.address'=>'','footer.contact_heading'=>'Contact','footer.email'=>'info@fuelfreepowerplant.com',
            'footer.phone'=>'','footer.website'=>'www.fuelfreepowerplant.com','footer.website_url'=>'/','footer.get_in_touch_label'=>'Get in touch',
            'footer.get_in_touch_url'=>'/contact','footer.copyright_text'=>'All rights reserved.',
        ];
        $settings = array_merge($defaults, SystemSetting::query()->whereIn('key',$keys)->pluck('value','key')->all());
        return view('admin.settings.footer', compact('settings'));
    }

    public function updateFooter(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'footer.tagline'=>['nullable','string','max:255'],'footer.technology'=>['nullable','string','max:255'],
            'footer.office_heading'=>['required','string','max:100'],'footer.address'=>['required','string','max:500'],
            'footer.contact_heading'=>['required','string','max:100'],'footer.email'=>['required','email','max:255'],
            'footer.phone'=>['required','string','max:50'],'footer.website'=>['required','string','max:255'],
            'footer.website_url'=>['required','url','max:500'],'footer.get_in_touch_label'=>['required','string','max:100'],
            'footer.get_in_touch_url'=>['required','string','max:500'],'footer.copyright_text'=>['required','string','max:255'],
        ]);
        foreach($data as $key=>$value){ SystemSetting::updateOrCreate(['key'=>$key],['value'=>$value,'is_sensitive'=>false]); }
        Cache::forget('fuelfree.system_settings');
        return back()->with('status','Footer settings saved successfully.');
    }

    public function updateHeader(Request $request): RedirectResponse
    {

        $data = $request->validate([
            'header.home_label'=>['required','string','max:40'],
            'header.management_label'=>['required','string','max:60'],
            'header.gallery_label'=>['required','string','max:40'],
            'header.news_label'=>['required','string','max:60'],
            'header.career_label'=>['required','string','max:40'],
            'header.contact_label'=>['required','string','max:40'],
            'header.webmail_label'=>['required','string','max:40'],
            'header.portal_label'=>['required','string','max:40'],
            'header.login_label'=>['required','string','max:40'],
            'header.logo_visible'=>['nullable','boolean'],'header.social_visible'=>['nullable','boolean'],'header.portal_visible'=>['nullable','boolean'],
        ]);
        foreach ($data as $key => $value) {
            SystemSetting::updateOrCreate(['key'=>$key], ['value'=>$value, 'is_sensitive'=>false]);
        }
        Cache::forget('fuelfree.system_settings');
        return back()->with('status','Header settings saved successfully.');
    }

    public function update(Request $request,WebmailService $webmail): RedirectResponse
    {
        $validated=$request->validate([
            'company.name'=>['required','string','max:150'],'company.domain'=>['required','string','max:255'],'company.tagline'=>['nullable','string','max:255'],
            'company.timezone'=>['required','timezone'],'storage.quota_gib'=>['required','numeric','min:1','max:1048576'],
            'home.news_limit'=>['required','integer','min:1','max:12'],'home.gallery_limit'=>['required','integer','min:1','max:12'],
            'home.slider_enabled'=>['nullable','boolean'],'home.welcome_enabled'=>['nullable','boolean'],'home.news_enabled'=>['nullable','boolean'],'home.gallery_enabled'=>['nullable','boolean'],
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
            'company.timezone'=>data_get($validated,'company.timezone'),'storage.quota_gib'=>data_get($validated,'storage.quota_gib'),
            'home.news_limit'=>data_get($validated,'home.news_limit'),'home.gallery_limit'=>data_get($validated,'home.gallery_limit'),
            'home.slider_enabled'=>$request->boolean('home.slider_enabled')?'1':'0','home.welcome_enabled'=>$request->boolean('home.welcome_enabled')?'1':'0',
            'home.news_enabled'=>$request->boolean('home.news_enabled')?'1':'0','home.gallery_enabled'=>$request->boolean('home.gallery_enabled')?'1':'0',
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
