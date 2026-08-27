@extends('layouts.portal')
@section('title','System Settings')
@section('content')
<section class="hero">
    <div class="eyebrow">PLATFORM CONFIGURATION</div>
    <h1>System Settings</h1>
    <p>Manage company identity, homepage, public email routing, header and footer from one organized control center.</p>
</section>

@if(session('status'))<div class="notice">{{ session('status') }}</div>@endif
@if($errors->any())<div class="errors">{{ $errors->first() }}</div>@endif

<form method="POST" enctype="multipart/form-data" action="{{ route('admin.settings.update') }}" class="settings-stack">
@csrf

<div class="form-card">
    <div class="section-heading"><div><div class="eyebrow">GENERAL</div><h2>Company &amp; system</h2><p>Core identity and platform settings used across the website.</p></div><i class="fa-solid fa-building"></i></div>
    <div class="identity">
        <div class="logo-preview">@if($settings['company.logo_path'])<img src="{{ asset('storage/'.$settings['company.logo_path']) }}" alt="Company logo">@else<i class="fa-solid fa-building"></i>@endif</div>
        <div class="identity-copy"><strong>{{ $settings['company.name'] }}</strong><span>Logo and company identity are shared with the public website.</span><label class="upload"><i class="fa-solid fa-cloud-arrow-up"></i> Change logo<input type="file" name="company[logo]" accept="image/png,image/jpeg,image/webp,image/svg+xml"></label></div>
    </div>
    <div class="fields">
        <div><label>Company name</label><input name="company[name]" value="{{ old('company.name',$settings['company.name']) }}" required maxlength="150"></div>
        <div><label>Company domain</label><input name="company[domain]" value="{{ old('company.domain',$settings['company.domain']) }}" required maxlength="255"></div>
        <div class="full"><label>Tagline</label><input name="company[tagline]" value="{{ old('company.tagline',$settings['company.tagline']) }}" maxlength="255"></div>
        <div class="full"><label>Company description</label><textarea name="company[description]" rows="3" maxlength="1000">{{ old('company.description',$settings['company.description']) }}</textarea></div>
        <div class="full"><label>Address</label><textarea name="company[address]" rows="2" maxlength="500">{{ old('company.address',$settings['company.address']) }}</textarea></div>
        <div><label>Phone</label><input name="company[phone]" value="{{ old('company.phone',$settings['company.phone']) }}" maxlength="80"></div>
        <div><label>Public email</label><input type="email" name="company[email]" value="{{ old('company.email',$settings['company.email']) }}" maxlength="255"></div>
        <div><label>Website</label><input name="company[website]" value="{{ old('company.website',$settings['company.website']) }}" maxlength="255" placeholder="www.example.com"></div>
        <div><label>Timezone</label><input name="company[timezone]" value="{{ old('company.timezone',$settings['company.timezone']) }}" required placeholder="Asia/Dhaka"></div>
        <div><label>Storage quota (GiB)</label><input name="storage[quota_gib]" type="number" min="1" step="1" value="{{ old('storage.quota_gib',$settings['storage.quota_gib']) }}" required></div>
    </div>
</div>

<div class="form-card">
    <div class="section-heading"><div><div class="eyebrow">HEADER</div><h2>Website header</h2><p>Control optional elements that appear in the public website header and mobile menu.</p></div><i class="fa-solid fa-window-maximize"></i></div>
    <div class="toggle-grid">
        @foreach([
            'header.show_social'=>['Social media icons','Show connected social profiles in the desktop header.','fa-share-nodes'],
            'header.show_webmail'=>['Webmail link','Show Webmail in the primary navigation.','fa-envelope-open-text'],
            'header.show_portal'=>['Portal / Login','Show Portal for logged-in users or Login for visitors.','fa-circle-user'],
        ] as $key=>$item)
        <label class="toggle-card">
            <span class="toggle-icon"><i class="fa-solid {{ $item[2] }}"></i></span>
            <span class="toggle-copy"><strong>{{ $item[0] }}</strong><small>{{ $item[1] }}</small></span>
            <span class="switch-wrap"><input class="switch-input" type="checkbox" name="{{ str_replace('.', '[', $key) }}]" value="1" @checked(old(str_replace('.','.', $key),$settings[$key]))><span class="switch"><i></i></span></span>
        </label>
        @endforeach
    </div>
    <div class="settings-note"><i class="fa-solid fa-circle-info"></i><span>The company logo, name and navigation pages remain managed from their dedicated sections. These controls only manage optional header elements.</span></div>
</div>

<div class="form-card">
    <div class="section-heading"><div><div class="eyebrow">FOOTER</div><h2>Website footer</h2><p>Build one consistent footer for every public page and control its visibility without editing code.</p></div><i class="fa-solid fa-rectangle-list"></i></div>
    <div class="toggle-grid">
        @foreach([
            'footer.enabled'=>['Footer enabled','Display the global footer on public pages.','fa-toggle-on'],
            'footer.show_company'=>['Company information','Show logo, company name, tagline and description.','fa-building'],
            'footer.show_contact'=>['Contact information','Show address, phone, email and website.','fa-address-card'],
            'footer.show_social'=>['Social media','Show connected social media icons in the Contact column.','fa-share-nodes'],
        ] as $key=>$item)
        <label class="toggle-card">
            <span class="toggle-icon"><i class="fa-solid {{ $item[2] }}"></i></span>
            <span class="toggle-copy"><strong>{{ $item[0] }}</strong><small>{{ $item[1] }}</small></span>
            <span class="switch-wrap"><input class="switch-input" type="checkbox" name="{{ str_replace('.', '[', $key) }}]" value="1" @checked(old($key,$settings[$key]))><span class="switch"><i></i></span></span>
        </label>
        @endforeach
    </div>
    <div class="fields footer-copy-field">
        <div class="full"><label>Copyright suffix</label><input name="footer[copyright]" value="{{ old('footer.copyright',$settings['footer.copyright']) }}" maxlength="255" placeholder="All rights reserved."></div>
    </div>
    <div class="settings-note"><i class="fa-solid fa-layer-group"></i><span>Quick Links are intentionally kept standard so the footer stays consistent. Company details, contact details, social links and copyright are all configurable here.</span></div>
</div>

<div class="form-card mail-routing-card">
    <div class="section-heading"><div><div class="eyebrow">PUBLIC EMAIL</div><h2>Website email routing</h2><p>Choose which connected company mailbox receives Contact and Career submissions.</p></div><i class="fa-solid fa-envelope-circle-check"></i></div>
    <div class="mail-routing-grid">
        <div class="mail-route"><label for="mail-contact-account">Contact page mailbox</label><select id="mail-contact-account" name="mail[contact_account_id]"><option value="">— Select a mailbox —</option>@foreach($mailboxes as $mailbox)<option value="{{ $mailbox->id }}" @selected((string) old('mail.contact_account_id',$settings['mail.contact_account_id']) === (string) $mailbox->id)>{{ $mailbox->display_name ?: $mailbox->address }} — {{ $mailbox->address }}</option>@endforeach</select><small>Contact form submissions are routed to this connected mailbox.</small></div>
        <div class="mail-route"><label for="mail-career-account">Career page mailbox</label><select id="mail-career-account" name="mail[career_account_id]"><option value="">— Select a mailbox —</option>@foreach($mailboxes as $mailbox)<option value="{{ $mailbox->id }}" @selected((string) old('mail.career_account_id',$settings['mail.career_account_id']) === (string) $mailbox->id)>{{ $mailbox->display_name ?: $mailbox->address }} — {{ $mailbox->address }}</option>@endforeach</select><small>Career applications are routed to this connected mailbox.</small></div>
    </div>
    @if($mailboxes->isEmpty())<div class="mail-routing-empty"><i class="fa-solid fa-circle-exclamation"></i><span>No active company mailboxes are available. Add and activate one from <strong>Help Desk → Mail list</strong> first.</span></div>@else<div class="mail-routing-note"><i class="fa-solid fa-shield-halved"></i><span>Routing uses the connected Help Desk mailbox. Change destinations here without editing code or <code>.env</code>.</span></div>@endif
</div>

<div class="form-card homepage-card">
    <div class="section-heading"><div><div class="eyebrow">HOMEPAGE</div><h2>Homepage content</h2><p>Turn sections on or off and control how much content each section displays.</p></div><i class="fa-solid fa-house-laptop"></i></div>
    <div class="home-options">
        @foreach([
            'slider_enabled'=>['Slider','Rotating company highlights at the top.','fa-images',null],
            'welcome_enabled'=>['Welcome message','Company introduction displayed below the slider.','fa-envelope',null],
            'news_enabled'=>['News & Notices','Latest news and notices shown on the homepage.','fa-newspaper','news_limit'],
            'gallery_enabled'=>['Gallery','Photo collections and albums shown on the homepage.','fa-photo-film','gallery_limit'],
        ] as $key=>$item)
        <div class="home-option">
            <label class="option-main"><span class="option-icon"><i class="fa-solid {{ $item[2] }}"></i></span><span class="option-copy"><strong>{{ $item[0] }}</strong><small>{{ $item[1] }}</small></span><span class="switch-wrap"><input class="switch-input" type="checkbox" name="home[{{ $key }}]" value="1" @checked(old('home.'.$key,$settings['home.'.$key]))><span class="switch"><i></i></span></span></label>
            @if($item[3])<div class="display-control"><label for="home-{{ $item[3] }}">Display count</label><div class="count-input"><input id="home-{{ $item[3] }}" name="home[{{ $item[3] }}]" type="number" min="1" max="12" step="1" value="{{ old('home.'.$item[3],$settings['home.'.$item[3]]) }}" required><span>items</span></div><small>{{ $item[3]==='news_limit' ? 'How many News & Notices appear on the homepage.' : 'How many gallery cards appear on the homepage.' }}</small></div>@endif
        </div>
        @endforeach
    </div>
    <div class="homepage-note"><i class="fa-solid fa-circle-info"></i><span><strong>Tip:</strong> Slider images are managed from the Slider menu. This page controls section visibility and display counts.</span></div>
</div>

<div class="actions"><button type="submit"><i class="fa-solid fa-floppy-disk"></i> Save all settings</button></div>
</form>
@endsection

@push('styles')
<style>
.settings-stack{max-width:1000px;display:grid;gap:18px}
.form-card{background:rgba(255,255,255,.025);border:1px solid var(--line);border-radius:20px;padding:24px}
.section-heading{display:flex;align-items:flex-start;justify-content:space-between;gap:20px;margin-bottom:20px}.section-heading h2{margin:5px 0;color:#eaf8fb;font-size:21px}.section-heading p{margin:0;color:#7899a5;font-size:10px;line-height:1.55}.section-heading>i{font-size:25px;color:#51cfe9}
.identity{display:flex;align-items:center;gap:15px;padding:16px;margin-bottom:20px;border:1px solid rgba(76,205,233,.13);border-radius:16px;background:linear-gradient(135deg,rgba(76,205,233,.06),rgba(255,255,255,.015))}
.logo-preview{width:68px;height:68px;flex:none;border-radius:17px;display:grid;place-items:center;overflow:hidden;border:1px solid var(--line);background:#061923;color:#62d4ed;font-size:25px}.logo-preview img{width:100%;height:100%;object-fit:contain}
.identity-copy strong{display:block;color:#e4f5f8;font-size:15px}.identity-copy>span{display:block;color:#7899a5;font-size:9px}.upload{display:inline-flex;align-items:center;gap:7px;margin-top:9px!important;padding:8px 10px;border:1px solid var(--line);border-radius:9px;color:#9ed3df!important;cursor:pointer!important}.upload input{display:none!important}
.fields{display:grid;grid-template-columns:1fr 1fr;gap:15px}.full{grid-column:1/-1}label{display:block;font-size:10px;color:#9eb9c4;margin:0 0 7px}input:not([type=checkbox]),textarea,select{width:100%;box-sizing:border-box;padding:12px;border-radius:11px;border:1px solid var(--line);background:#071c29;color:#e9f7fb;outline:none}textarea{resize:vertical;min-height:72px}input:focus,textarea:focus,select:focus{border-color:rgba(81,216,240,.35);box-shadow:0 0 0 3px rgba(81,216,240,.06)}
.toggle-grid{display:grid;grid-template-columns:1fr 1fr;gap:10px}.toggle-card{display:grid;grid-template-columns:42px minmax(0,1fr) 42px;align-items:center;gap:11px;padding:13px;border:1px solid rgba(76,205,233,.11);border-radius:15px;background:rgba(76,205,233,.025);cursor:pointer;margin:0}.toggle-icon{width:42px;height:42px;display:grid;place-items:center;border-radius:12px;background:rgba(72,216,241,.08);color:#5fd4ed}.toggle-copy{min-width:0}.toggle-copy strong{display:block;color:#e7f7fa;font-size:11px}.toggle-copy small{display:block;color:#718f9a;font-size:8px;line-height:1.45;margin-top:3px}.switch-wrap{justify-self:end}.switch-input{position:absolute;opacity:0;pointer-events:none}.switch{position:relative;display:block;width:42px;height:23px;border-radius:999px;background:#304952;border:1px solid rgba(255,255,255,.08)}.switch i{position:absolute;width:17px;height:17px;top:2px;left:2px;border-radius:50%;background:#a9bcc1;transition:.2s}.switch-input:checked + .switch{background:#31b985}.switch-input:checked + .switch i{left:21px;background:#fff}
.settings-note,.mail-routing-note,.mail-routing-empty,.homepage-note{display:flex;gap:9px;align-items:flex-start;margin-top:14px;padding:12px 13px;border-radius:12px;background:rgba(72,216,241,.035);border:1px solid rgba(72,216,241,.08);color:#718f9a;font-size:9px;line-height:1.55}.settings-note i,.mail-routing-note i,.homepage-note i{color:#58cfe9}.mail-routing-empty i{color:#f2b85b}.settings-note strong,.mail-routing-note strong,.mail-routing-empty strong,.homepage-note strong{color:#9bc4ce}.settings-note code,.mail-routing-note code{color:#9bc4ce}
.mail-routing-grid{display:grid;grid-template-columns:1fr 1fr;gap:14px}.mail-route{padding:15px;border:1px solid rgba(76,205,233,.11);border-radius:15px;background:rgba(76,205,233,.025)}.mail-route label{font-size:11px;color:#a9c2ca;font-weight:700}.mail-route small{display:block;color:#678692;font-size:8px;line-height:1.5;margin-top:8px}
.home-options{display:grid;gap:10px}.home-option{border:1px solid rgba(76,205,233,.11);border-radius:16px;background:rgba(76,205,233,.025);overflow:hidden}.option-main{display:grid;grid-template-columns:44px minmax(0,1fr) 46px;align-items:center;gap:12px;padding:13px 14px;margin:0;cursor:pointer}.option-icon{width:44px;height:44px;display:grid;place-items:center;border-radius:12px;background:rgba(72,216,241,.08);color:#5fd4ed;font-size:15px}.option-copy{min-width:0}.option-copy strong{display:block;color:#e7f7fa;font-size:12px}.option-copy small{display:block;color:#718f9a;font-size:9px;margin-top:4px;line-height:1.4}.display-control{display:grid;grid-template-columns:minmax(120px,1fr) 105px minmax(190px,1.8fr);align-items:center;gap:12px;padding:10px 14px 13px;margin:0 14px 12px;border-top:1px solid rgba(76,205,233,.09)}.display-control label{margin:11px 0 0;font-size:9px;color:#89a9b4}.count-input{position:relative;margin-top:7px}.count-input input{padding:9px 45px 9px 11px!important;font-size:11px}.count-input span{position:absolute;right:10px;top:50%;transform:translateY(-50%);color:#628591;font-size:8px}.display-control>small{color:#678692;font-size:8px;line-height:1.4;margin-top:8px}
.actions{display:flex;justify-content:flex-end}.actions button{border:0;border-radius:12px;padding:13px 20px;background:#31afd2;color:#fff;font-weight:700;box-shadow:0 8px 24px rgba(49,175,210,.14)}
@media(max-width:700px){.form-card{padding:18px}.fields,.toggle-grid,.mail-routing-grid{grid-template-columns:1fr}.full{grid-column:auto}.identity{align-items:flex-start}.section-heading h2{font-size:19px}.toggle-card{grid-template-columns:40px minmax(0,1fr) 42px}.toggle-icon{width:40px;height:40px}.option-main{grid-template-columns:40px minmax(0,1fr) 42px;padding:12px}.option-icon{width:40px;height:40px}.display-control{grid-template-columns:1fr 100px;margin:0 12px 11px;padding-top:9px}.display-control>small{grid-column:1/-1;margin-top:0}.actions button{width:100%}}
</style>
@endpush