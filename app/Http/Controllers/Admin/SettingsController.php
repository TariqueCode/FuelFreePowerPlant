<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\SystemSetting;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\View\View;

class SettingsController extends Controller
{
    public function index(): View
    {
        $defaults = [
            'company.name'=>config('fuelfree.company.name'),'company.domain'=>config('fuelfree.company.domain'),
            'company.tagline'=>config('fuelfree.company.tagline'),'company.timezone'=>config('fuelfree.company.timezone'),
            'company.logo_path'=>'','storage.quota_gib'=>(string) round(config('fuelfree.storage.quota_bytes',53687091200)/1073741824),
            'energy.real_data_enabled'=>'0',
        ];
        $saved=SystemSetting::query()->pluck('value','key')->all(); $settings=array_merge($defaults,$saved);
        return view('admin.settings.index',compact('settings'));
    }

    public function update(Request $request): RedirectResponse
    {
        $validated=$request->validate([
            'company.name'=>['required','string','max:150'],'company.domain'=>['required','string','max:255'],
            'company.tagline'=>['nullable','string','max:255'],'company.timezone'=>['required','timezone'],
            'storage.quota_gib'=>['required','numeric','min:1','max:1048576'],'energy.real_data_enabled'=>['nullable','boolean'],
            'company.logo'=>['nullable','image','mimes:jpg,jpeg,png,webp,svg','max:5120'],
        ]);
        $data=['company.name'=>data_get($validated,'company.name'),'company.domain'=>data_get($validated,'company.domain'),
            'company.tagline'=>data_get($validated,'company.tagline'),'company.timezone'=>data_get($validated,'company.timezone'),
            'storage.quota_gib'=>data_get($validated,'storage.quota_gib'),'energy.real_data_enabled'=>$request->boolean('energy.real_data_enabled')?'1':'0'];
        if($request->hasFile('company.logo')){
            $old=SystemSetting::query()->where('key','company.logo_path')->value('value');
            if($old) Storage::disk('public')->delete($old);
            $data['company.logo_path']=$request->file('company.logo')->store('site/branding','public');
        }
        foreach($data as $key=>$value){SystemSetting::updateOrCreate(['key'=>$key],['value'=>(string)($value??''),'is_sensitive'=>false]);}
        return back()->with('status','System settings saved. Company identity updated and real energy-data integration remains disabled unless explicitly enabled.');
    }
}
