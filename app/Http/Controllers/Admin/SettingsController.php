<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\EmailAccount;
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
            'company.name'=>config('fuelfree.company.name'),'company.domain'=>config('fuelfree.company.domain'),
            'company.tagline'=>config('fuelfree.company.tagline'),'company.timezone'=>config('fuelfree.company.timezone'),
            'company.logo_path'=>'','storage.quota_gib'=>(string) round(config('fuelfree.storage.quota_bytes',53687091200)/1073741824),
            'home.news_limit'=>'3','home.gallery_limit'=>'4',
            'mail.contact_account_id'=>'','mail.career_account_id'=>'',
            'header.home_label'=>'Home','header.management_label'=>'Management Team','header.gallery_label'=>'Gallery',
            'header.news_label'=>'News & Notices','header.career_label'=>'Career','header.contact_label'=>'Contact',
            'header.webmail_label'=>'Webmail','header.portal_label'=>'Portal','header.login_label'=>'Login',
            'footer.tagline'=>'Powering a cleaner, smarter future.','footer.technology'=>'Fuel-Free Flywheel-Based Clean Energy Technology',
            'footer.office_heading'=>'Office','footer.address'=>'House-141, 3rd Floor, Road-22, Mohakhali DOHS, Dhaka-1206, Bangladesh',
            'footer.contact_heading'=>'Contact','footer.email'=>'info@fuelfreepowerplant.com','footer.phone'=>'+880 1712-251892',
            'footer.website'=>'www.fuelfreepowerplant.com','footer.website_url'=>'https://www.fuelfreepowerplant.com',
            'footer.get_in_touch_label'=>'Get in touch','footer.get_in_touch_url'=>'/contact',
            'footer.copyright_text'=>'All rights reserved.','footer.developer_prefix'=>'Developed by',
            'footer.developer_name'=>'Saif Al-Islam','footer.developer_email'=>'TariqueBN@gmail.com',
            'home.slider_enabled'=>'1','home.welcome_enabled'=>'1','home.news_enabled'=>'1','home.gallery_enabled'=>'1',
        ];
        $saved=SystemSetting::query()->pluck('value','key')->all();
        $settings=array_merge($defaults,$saved);
        $mailboxes=EmailAccount::query()
            ->where('status','active')
            ->where('address','like','%@fuelfreepowerplant.com')
            ->orderBy('address')
            ->get(['id','address','display_name','status']);
        return view('admin.settings.index',compact('settings','mailboxes'));
    }

    public function update(Request $request): RedirectResponse
    {
        $validated=$request->validate([
            'company.name'=>['required','string','max:150'],'company.domain'=>['required','string','max:255'],
            'company.tagline'=>['nullable','string','max:255'],'company.timezone'=>['required','timezone'],
            'storage.quota_gib'=>['required','numeric','min:1','max:1048576'],
            'home.news_limit'=>['required','integer','min:1','max:12'],'home.gallery_limit'=>['required','integer','min:1','max:12'],
            'home.slider_enabled'=>['nullable','boolean'],'home.welcome_enabled'=>['nullable','boolean'],
            'home.news_enabled'=>['nullable','boolean'],'home.gallery_enabled'=>['nullable','boolean'],
            'mail.contact_account_id'=>['nullable','integer','exists:email_accounts,id'],
            'mail.career_account_id'=>['nullable','integer','exists:email_accounts,id'],
            'company.logo'=>['nullable','image','mimes:jpg,jpeg,png,webp,svg'],
            'header.*'=>['required','string','max:100'],
            'footer.tagline'=>['nullable','string','max:255'],'footer.technology'=>['nullable','string','max:255'],
            'footer.office_heading'=>['required','string','max:100'],'footer.address'=>['required','string','max:500'],
            'footer.contact_heading'=>['required','string','max:100'],'footer.email'=>['required','email','max:255'],
            'footer.phone'=>['required','string','max:50'],'footer.website'=>['required','string','max:255'],
            'footer.website_url'=>['required','url','max:255'],'footer.get_in_touch_label'=>['required','string','max:100'],
            'footer.get_in_touch_url'=>['required','string','max:255'],
            'footer.copyright_text'=>['required','string','max:150'],'footer.developer_prefix'=>['nullable','string','max:50'],
            'footer.developer_name'=>['nullable','string','max:100'],'footer.developer_email'=>['nullable','email','max:255'],
        ]);

        $data=[
            'company.name'=>data_get($validated,'company.name'),'company.domain'=>data_get($validated,'company.domain'),
            'company.tagline'=>data_get($validated,'company.tagline'),'company.timezone'=>data_get($validated,'company.timezone'),
            'storage.quota_gib'=>data_get($validated,'storage.quota_gib'),
            'home.news_limit'=>data_get($validated,'home.news_limit'),'home.gallery_limit'=>data_get($validated,'home.gallery_limit'),
            'home.slider_enabled'=>$request->boolean('home.slider_enabled')?'1':'0',
            'home.welcome_enabled'=>$request->boolean('home.welcome_enabled')?'1':'0',
            'home.news_enabled'=>$request->boolean('home.news_enabled')?'1':'0',
            'home.gallery_enabled'=>$request->boolean('home.gallery_enabled')?'1':'0',
            'mail.contact_account_id'=>(string) $request->input('mail.contact_account_id',''),
            'header.home_label'=>data_get($validated,'header.home_label'),'header.management_label'=>data_get($validated,'header.management_label'),
            'header.gallery_label'=>data_get($validated,'header.gallery_label'),'header.news_label'=>data_get($validated,'header.news_label'),
            'header.career_label'=>data_get($validated,'header.career_label'),'header.contact_label'=>data_get($validated,'header.contact_label'),
            'header.webmail_label'=>data_get($validated,'header.webmail_label'),'header.portal_label'=>data_get($validated,'header.portal_label'),
            'header.login_label'=>data_get($validated,'header.login_label'),
            'footer.tagline'=>data_get($validated,'footer.tagline'),'footer.technology'=>data_get($validated,'footer.technology'),
            'footer.office_heading'=>data_get($validated,'footer.office_heading'),'footer.address'=>data_get($validated,'footer.address'),
            'footer.contact_heading'=>data_get($validated,'footer.contact_heading'),'footer.email'=>data_get($validated,'footer.email'),
            'footer.phone'=>data_get($validated,'footer.phone'),'footer.website'=>data_get($validated,'footer.website'),
            'footer.website_url'=>data_get($validated,'footer.website_url'),'footer.get_in_touch_label'=>data_get($validated,'footer.get_in_touch_label'),
            'footer.get_in_touch_url'=>data_get($validated,'footer.get_in_touch_url'),'footer.copyright_text'=>data_get($validated,'footer.copyright_text'),
            'footer.developer_prefix'=>data_get($validated,'footer.developer_prefix'),'footer.developer_name'=>data_get($validated,'footer.developer_name'),
            'footer.developer_email'=>data_get($validated,'footer.developer_email'),
            'mail.career_account_id'=>(string) $request->input('mail.career_account_id',''),
        ];

        $mailboxIds=array_filter([
            (int) $request->input('mail.contact_account_id'),
            (int) $request->input('mail.career_account_id'),
        ]);
        if ($mailboxIds) {
            $validMailboxIds=EmailAccount::query()
                ->whereIn('id',$mailboxIds)
                ->where('status','active')
                ->where('address','like','%@fuelfreepowerplant.com')
                ->pluck('id')
                ->all();
            foreach ($mailboxIds as $mailboxId) {
                if (!in_array($mailboxId,$validMailboxIds,true)) {
                    abort(422,'Selected mailbox is not active or is not a company mailbox.');
                }
            }
        }

        if($request->hasFile('company.logo')){
            $old=SystemSetting::query()->where('key','company.logo_path')->value('value');
            if($old) Storage::disk('public')->delete($old);
            $data['company.logo_path']=$request->file('company.logo')->store('site/branding','public');
        }
        foreach($data as $key=>$value){SystemSetting::updateOrCreate(['key'=>$key],['value'=>(string)($value??''),'is_sensitive'=>false]);}
        return back()->with('status','System settings saved. Homepage controls updated.');
    }
}
