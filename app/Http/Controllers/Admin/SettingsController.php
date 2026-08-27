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
