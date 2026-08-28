@extends('layouts.portal')
@section('title','System Settings')
@section('content')
<section class="hero">
    <div class="eyebrow">PLATFORM CONFIGURATION</div>
    <h1>System Settings</h1>
    <p>Manage company identity, storage and homepage content from one organized control center.</p>
</section>

@if(session('status'))<div class="notice">{{ session('status') }}</div>@endif
@if($errors->any())<div class="errors">{{ $errors->first() }}</div>@endif

<form method="POST" enctype="multipart/form-data" action="{{ route('admin.settings.update') }}" class="settings-stack">
@csrf

<div class="form-card">
    <div class="section-heading">
        <div><div class="eyebrow">GENERAL</div><h2>Company &amp; system</h2><p>Core identity and platform settings used across the website.</p></div>
        <i class="fa-solid fa-sliders"></i>
    </div>

    <div class="identity">
        <div class="logo-preview">
            @if($settings['company.logo_path'])<img src="{{ asset('storage/'.$settings['company.logo_path']) }}" alt="Company logo">
            @else<i class="fa-solid fa-building"></i>@endif
        </div>
        <div class="identity-copy">
            <strong>{{ $settings['company.name'] }}</strong>
            <span>Logo and company identity are shared with the public website.</span>
            <label class="upload"><i class="fa-solid fa-cloud-arrow-up"></i> Change logo<input type="file" name="company[logo]" accept="image/png,image/jpeg,image/webp,image/svg+xml"></label>
        </div>
    </div>

    <div class="fields">
        <div><label>Company name</label><input name="company[name]" value="{{ old('company.name',$settings['company.name']) }}" required maxlength="150"></div>
        <div><label>Company domain</label><input name="company[domain]" value="{{ old('company.domain',$settings['company.domain']) }}" required maxlength="255"></div>
        <div class="full"><label>Tagline</label><input name="company[tagline]" value="{{ old('company.tagline',$settings['company.tagline']) }}" maxlength="255"></div>
        <div><label>Timezone</label><input name="company[timezone]" value="{{ old('company.timezone',$settings['company.timezone']) }}" required placeholder="Asia/Dhaka"></div>
        <div><label>Storage quota (GiB)</label><input name="storage[quota_gib]" type="number" min="1" step="1" value="{{ old('storage.quota_gib',$settings['storage.quota_gib']) }}" required></div>
    </div>
</div>

<div class="form-card mail-routing-card">
    <div class="section-heading">
        <div>
            <div class="eyebrow">PUBLIC EMAIL</div>
            <h2>Website email routing</h2>
            <p>Choose which connected company mailbox receives Contact and Career submissions.</p>
        </div>
        <i class="fa-solid fa-envelope-circle-check"></i>
    </div>

    <div class="mail-routing-grid">
        <div class="mail-route">
            <label for="mail-contact-account">Contact page mailbox</label>
            <select id="mail-contact-account" name="mail[contact_account_id]">
                <option value="">— Select a mailbox —</option>
                @foreach($mailboxes as $mailbox)
                    <option value="{{ $mailbox->id }}" @selected((string) old('mail.contact_account_id',$settings['mail.contact_account_id']) === (string) $mailbox->id)>
                        {{ $mailbox->display_name ?: $mailbox->address }} — {{ $mailbox->address }}
                    </option>
                @endforeach
            </select>
            <small>New messages submitted from the public Contact page will be forwarded here.</small>
        </div>

        <div class="mail-route">
            <label for="mail-career-account">Career page mailbox</label>
            <select id="mail-career-account" name="mail[career_account_id]">
                <option value="">— Select a mailbox —</option>
                @foreach($mailboxes as $mailbox)
                    <option value="{{ $mailbox->id }}" @selected((string) old('mail.career_account_id',$settings['mail.career_account_id']) === (string) $mailbox->id)>
                        {{ $mailbox->display_name ?: $mailbox->address }} — {{ $mailbox->address }}
                    </option>
                @endforeach
            </select>
            <small>New CV applications submitted from the public Career page will be forwarded here.</small>
        </div>
    </div>

    @if($mailboxes->isEmpty())
        <div class="mail-routing-empty">
            <i class="fa-solid fa-circle-exclamation"></i>
            <span>No active company mailboxes are available. Add and activate a mailbox from <strong>Help Desk → Mail list</strong> first.</span>
        </div>
    @else
        <div class="mail-routing-note">
            <i class="fa-solid fa-shield-halved"></i>
            <span>Routing uses the connected mailbox account stored in the Help Desk. You can change the destination anytime without editing code or <code>.env</code>.</span>
        </div>
    @endif
</div>

<div class="form-card homepage-card">
    <div class="section-heading">
        <div>
            <div class="eyebrow">HOMEPAGE</div>
            <h2>Homepage content</h2>
            <p>Turn sections on or off and control how much content each section displays.</p>
        </div>
        <i class="fa-solid fa-house-laptop"></i>
    </div>

    <div class="home-options">
        @foreach([
            'slider_enabled'=>['Slider','Rotating company highlights at the top.','fa-images',null],
            'welcome_enabled'=>['Welcome message','Company introduction displayed below the slider.','fa-envelope',null],
            'news_enabled'=>['News & Notices','Latest news and notices shown on the homepage.','fa-newspaper','news_limit'],
            'gallery_enabled'=>['Gallery','Photo collections and albums shown on the homepage.','fa-photo-film','gallery_limit'],
        ] as $key=>$item)
        <div class="home-option">
            <label class="option-main">
                <span class="option-icon"><i class="fa-solid {{ $item[2] }}"></i></span>
                <span class="option-copy"><strong>{{ $item[0] }}</strong><small>{{ $item[1] }}</small></span>
                <span class="switch-wrap">
                    <input class="switch-input" type="checkbox" name="home[{{ $key }}]" value="1" @checked(old('home.'.$key,$settings['home.'.$key]))>
                    <span class="switch"><i></i></span>
                </span>
            </label>

            @if($item[3])
            <div class="display-control">
                <label for="home-{{ $item[3] }}">Display count</label>
                <div class="count-input">
                    <input id="home-{{ $item[3] }}" name="home[{{ $item[3] }}]" type="number" min="1" max="12" step="1" value="{{ old('home.'.$item[3],$settings['home.'.$item[3]]) }}" required>
                    <span>items</span>
                </div>
                <small>{{ $item[3]==='news_limit' ? 'How many News & Notices appear on the homepage.' : 'How many gallery cards appear on the homepage.' }}</small>
            </div>
            @endif
        </div>
        @endforeach
    </div>

    <div class="homepage-note">
        <i class="fa-solid fa-circle-info"></i>
        <span><strong>Tip:</strong> The Slider is managed separately from the Slider menu, where images can be reordered with drag &amp; drop. This page only controls whether each homepage section is visible and, where applicable, its display count.</span>
    </div>
</div>

<div class="form-card chrome-card">
    <div class="section-heading">
        <div><div class="eyebrow">SITE CHROME</div><h2>Header &amp; Footer</h2><p>Edit the visible Header and Footer text, contact details and links from one place. Company name and logo are controlled above, while social links remain managed from Social Media.</p></div>
        <i class="fa-solid fa-window-maximize"></i>
    </div>
    <div class="chrome-section">
        <div class="chrome-section-title"><i class="fa-solid fa-bars"></i><span>Header navigation</span></div>
        <div class="fields">
            <div><label>Home label</label><input name="header[home_label]" value="{{ old('header.home_label',$settings['header.home_label']) }}" required maxlength="100"></div>
            <div><label>Management label</label><input name="header[management_label]" value="{{ old('header.management_label',$settings['header.management_label']) }}" required maxlength="100"></div>
            <div><label>Gallery label</label><input name="header[gallery_label]" value="{{ old('header.gallery_label',$settings['header.gallery_label']) }}" required maxlength="100"></div>
            <div><label>News &amp; Notices label</label><input name="header[news_label]" value="{{ old('header.news_label',$settings['header.news_label']) }}" required maxlength="100"></div>
            <div><label>Career label</label><input name="header[career_label]" value="{{ old('header.career_label',$settings['header.career_label']) }}" required maxlength="100"></div>
            <div><label>Contact label</label><input name="header[contact_label]" value="{{ old('header.contact_label',$settings['header.contact_label']) }}" required maxlength="100"></div>
            <div><label>Webmail label</label><input name="header[webmail_label]" value="{{ old('header.webmail_label',$settings['header.webmail_label']) }}" required maxlength="100"></div>
            <div><label>Portal label</label><input name="header[portal_label]" value="{{ old('header.portal_label',$settings['header.portal_label']) }}" required maxlength="100"></div>
            <div><label>Login label</label><input name="header[login_label]" value="{{ old('header.login_label',$settings['header.login_label']) }}" required maxlength="100"></div>
        </div>
        <div class="chrome-note"><i class="fa-solid fa-circle-info"></i><span>Company/CMS page names remain editable from their existing management screens, keeping the Header connected to your content.</span></div>
    </div>
    <div class="chrome-section">
        <div class="chrome-section-title"><i class="fa-solid fa-layer-group"></i><span>Footer content</span></div>
        <div class="fields">
            <div class="full"><label>Tagline</label><input name="footer[tagline]" value="{{ old('footer.tagline',$settings['footer.tagline']) }}" maxlength="255"></div>
            <div class="full"><label>Technology line</label><input name="footer[technology]" value="{{ old('footer.technology',$settings['footer.technology']) }}" maxlength="255"></div>
            <div><label>Office heading</label><input name="footer[office_heading]" value="{{ old('footer.office_heading',$settings['footer.office_heading']) }}" required maxlength="100"></div>
            <div><label>Contact heading</label><input name="footer[contact_heading]" value="{{ old('footer.contact_heading',$settings['footer.contact_heading']) }}" required maxlength="100"></div>
            <div class="full"><label>Office address</label><textarea name="footer[address]" rows="3" maxlength="500" required>{{ old('footer.address',$settings['footer.address']) }}</textarea></div>
            <div><label>Email</label><input type="email" name="footer[email]" value="{{ old('footer.email',$settings['footer.email']) }}" required maxlength="255"></div>
            <div><label>Phone</label><input name="footer[phone]" value="{{ old('footer.phone',$settings['footer.phone']) }}" required maxlength="50"></div>
            <div><label>Website text</label><input name="footer[website]" value="{{ old('footer.website',$settings['footer.website']) }}" required maxlength="255"></div>
            <div><label>Website URL</label><input type="url" name="footer[website_url]" value="{{ old('footer.website_url',$settings['footer.website_url']) }}" required maxlength="255"></div>
            <div><label>Get in touch label</label><input name="footer[get_in_touch_label]" value="{{ old('footer.get_in_touch_label',$settings['footer.get_in_touch_label']) }}" required maxlength="100"></div>
            <div><label>Get in touch URL</label><input name="footer[get_in_touch_url]" value="{{ old('footer.get_in_touch_url',$settings['footer.get_in_touch_url']) }}" required maxlength="255"></div>
            <div><label>Copyright text</label><input name="footer[copyright_text]" value="{{ old('footer.copyright_text',$settings['footer.copyright_text']) }}" required maxlength="150"></div>
            <div><label>Developer prefix</label><input name="footer[developer_prefix]" value="{{ old('footer.developer_prefix',$settings['footer.developer_prefix']) }}" maxlength="50"></div>
            <div><label>Developer name</label><input name="footer[developer_name]" value="{{ old('footer.developer_name',$settings['footer.developer_name']) }}" maxlength="100"></div>
            <div><label>Developer email</label><input type="email" name="footer[developer_email]" value="{{ old('footer.developer_email',$settings['footer.developer_email']) }}" maxlength="255"></div>
        </div>
        <div class="chrome-note"><i class="fa-solid fa-share-nodes"></i><span>Social icons and destinations continue to use the Social Media manager as the single source of truth.</span></div>
    </div>
</div>

<div class="actions"><button type="submit"><i class="fa-solid fa-floppy-disk"></i> Save all settings</button></div>
</form>
@endsection

@push('styles')
<style>
.settings-stack{max-width:980px;display:grid;gap:18px}
.chrome-section{padding-top:18px;margin-top:18px;border-top:1px solid rgba(76,205,233,.09)}
.chrome-section:first-of-type{padding-top:0;margin-top:0;border-top:0}
.chrome-section-title{display:flex;align-items:center;gap:9px;margin-bottom:14px;color:#dff5f8;font-size:12px;font-weight:800}
.chrome-section-title i{color:#51cfe9}
.chrome-note{display:flex;gap:9px;align-items:flex-start;margin-top:14px;padding:11px 12px;border-radius:11px;background:rgba(72,216,241,.035);border:1px solid rgba(72,216,241,.08);color:#718f9a;font-size:8px;line-height:1.55}
.chrome-note i{color:#58cfe9;margin-top:1px}
.mail-routing-grid{display:grid;grid-template-columns:1fr 1fr;gap:14px}
.mail-route{padding:15px;border:1px solid rgba(76,205,233,.11);border-radius:15px;background:rgba(76,205,233,.025)}
.mail-route label{font-size:11px;color:#a9c2ca;font-weight:700}
.mail-route select{width:100%;box-sizing:border-box;padding:12px;border-radius:11px;border:1px solid var(--line);background:#071c29;color:#e9f7fb;outline:none}
.mail-route select:focus{border-color:rgba(81,216,240,.35);box-shadow:0 0 0 3px rgba(81,216,240,.06)}
.mail-route small{display:block;color:#678692;font-size:8px;line-height:1.5;margin-top:8px}
.mail-routing-note,.mail-routing-empty{display:flex;gap:9px;align-items:flex-start;margin-top:14px;padding:12px 13px;border-radius:12px;background:rgba(72,216,241,.035);border:1px solid rgba(72,216,241,.08);color:#718f9a;font-size:9px;line-height:1.55}
.mail-routing-note i{color:#58cfe9}.mail-routing-empty i{color:#f2b85b}
.mail-routing-note strong,.mail-routing-empty strong{color:#9bc4ce}
.mail-routing-note code{color:#9bc4ce}
.form-card{background:rgba(255,255,255,.025);border:1px solid var(--line);border-radius:20px;padding:24px}
.section-heading{display:flex;align-items:flex-start;justify-content:space-between;gap:20px;margin-bottom:20px}
.section-heading h2{margin:5px 0 5px;color:#eaf8fb;font-size:21px}
.section-heading p{margin:0;color:#7899a5;font-size:10px;line-height:1.55}
.section-heading>i{font-size:26px;color:#51cfe9}
.identity{display:flex;align-items:center;gap:15px;padding:16px;margin-bottom:20px;border:1px solid rgba(76,205,233,.13);border-radius:16px;background:linear-gradient(135deg,rgba(76,205,233,.06),rgba(255,255,255,.015))}
.logo-preview{width:68px;height:68px;flex:none;border-radius:17px;display:grid;place-items:center;overflow:hidden;border:1px solid var(--line);background:#061923;color:#62d4ed;font-size:25px}
.logo-preview img{width:100%;height:100%;object-fit:contain}
.identity-copy strong{display:block;color:#e4f5f8;font-size:15px;margin:2px 0 4px}
.identity-copy>span{display:block;color:#7899a5;font-size:9px}
.upload{display:inline-flex;align-items:center;gap:7px;margin-top:9px!important;padding:8px 10px;border:1px solid var(--line);border-radius:9px;color:#9ed3df!important;cursor:pointer!important}
.upload input{display:none!important}
.fields{display:grid;grid-template-columns:1fr 1fr;gap:16px}
.full{grid-column:1/-1}
label{display:block;font-size:10px;color:#9eb9c4;margin:0 0 7px}
input:not([type=checkbox]),textarea{width:100%;box-sizing:border-box;padding:13px;border-radius:11px;border:1px solid var(--line);background:#071c29;color:#e9f7fb;outline:none;font:inherit}
textarea{resize:vertical;min-height:84px}
input:not([type=checkbox]):focus,textarea:focus{border-color:rgba(81,216,240,.35);box-shadow:0 0 0 3px rgba(81,216,240,.06)}
.home-options{display:grid;gap:10px}
.home-option{border:1px solid rgba(76,205,233,.11);border-radius:16px;background:rgba(76,205,233,.025);overflow:hidden}
.option-main{display:grid;grid-template-columns:44px minmax(0,1fr) 46px;align-items:center;gap:12px;padding:13px 14px;margin:0;cursor:pointer}
.option-icon{width:44px;height:44px;display:grid;place-items:center;border-radius:12px;background:rgba(72,216,241,.08);color:#5fd4ed;font-size:15px}
.option-copy{min-width:0}
.option-copy strong{display:block;color:#e7f7fa;font-size:12px}
.option-copy small{display:block;color:#718f9a;font-size:9px;margin-top:4px;line-height:1.4}
.switch-wrap{justify-self:end;display:block}
.switch-input{position:absolute;opacity:0;pointer-events:none}
.switch{position:relative;display:block;width:42px;height:23px;border-radius:999px;background:#304952;border:1px solid rgba(255,255,255,.08)}
.switch i{position:absolute;width:17px;height:17px;top:2px;left:2px;border-radius:50%;background:#a9bcc1;transition:.2s}
.switch-input:checked + .switch{background:#31b985}
.switch-input:checked + .switch i{left:21px;background:#fff}
.display-control{display:grid;grid-template-columns:minmax(120px,1fr) 105px minmax(190px,1.8fr);align-items:center;gap:12px;padding:10px 14px 13px;margin:0 14px 12px;border-top:1px solid rgba(76,205,233,.09)}
.display-control label{margin:11px 0 0;font-size:9px;color:#89a9b4}
.count-input{position:relative;margin-top:7px}
.count-input input{padding:9px 45px 9px 11px!important;font-size:11px}
.count-input span{position:absolute;right:10px;top:50%;transform:translateY(-50%);color:#628591;font-size:8px}
.display-control>small{color:#678692;font-size:8px;line-height:1.4;margin-top:8px}
.homepage-note{display:flex;gap:9px;align-items:flex-start;margin-top:14px;padding:12px 13px;border-radius:12px;background:rgba(72,216,241,.035);border:1px solid rgba(72,216,241,.08);color:#718f9a;font-size:8px;line-height:1.55}
.homepage-note i{color:#58cfe9;margin-top:1px}
.homepage-note strong{color:#9bc4ce}
.actions{display:flex;justify-content:flex-end}
.actions button{border:0;border-radius:12px;padding:13px 20px;background:#31afd2;color:#fff;font-weight:700;box-shadow:0 8px 24px rgba(49,175,210,.14)}
@media(max-width:650px){
.mail-routing-grid{grid-template-columns:1fr}

.form-card{padding:18px}
.section-heading{margin-bottom:16px}
.section-heading h2{font-size:19px}
.identity{align-items:flex-start}
.fields{grid-template-columns:1fr}
.full{grid-column:auto}
.option-main{grid-template-columns:40px minmax(0,1fr) 42px;padding:12px}
.option-icon{width:40px;height:40px}
.display-control{grid-template-columns:1fr 100px;margin:0 12px 11px;padding-top:9px}
.display-control>small{grid-column:1/-1;margin-top:0}
.actions button{width:100%}
}
</style>
@endpush