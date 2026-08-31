@extends('layouts.portal')
@section('title','System Settings')
@section('content')
<section class="hero">
    <div class="eyebrow">PLATFORM CONFIGURATION</div>
    <h1>System Settings</h1>
    <p>Manage core platform configuration and official mailbox connections. Website content, navigation and visual design are managed in their dedicated control centers.</p>
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
        <div class="full"><label>Default upload limit (MB)</label><input name="uploads[max_mb]" type="number" min="1" max="1048576" step="1" value="{{ old('uploads.max_mb',$settings['uploads.max_mb']) }}" required><small>Fallback limit for upload modules that do not have their own policy. Enter any value you need; do not choose from a fixed list.</small></div>
        <div class="full upload-policy-panel">
            <div class="chrome-section-title"><i class="fa-solid fa-file-arrow-up"></i><span>Custom upload limits</span></div>
            <p class="upload-policy-intro">Set an independent maximum for each upload area. Values are entered directly in MB, so you can use 1, 7, 25, 50, 100, 512 or any other value allowed by your server.</p>
            <div class="upload-policy-grid">
                @foreach([
                    'career_max_mb'=>['Career applications','CV / resume uploads from the public Career page.'],
                    'documents_max_mb'=>['File Manager','Documents and media uploaded through the Admin File Manager.'],
                    'gallery_max_mb'=>['Gallery & media','Gallery cover images and gallery image/video uploads.'],'sliders_max_mb'=>['Sliders','Homepage and site slider image uploads.'],'popups_max_mb'=>['Popups','Website popup image uploads.'],'content_media_max_mb'=>['CMS media','Images and videos inserted into website content.'],
                ] as $key=>$policy)
                    <div class="upload-policy-field">
                        <label>{{ $policy[0] }} <span>(MB)</span></label>
                        <div class="mb-input"><input name="uploads[{{ $key }}]" type="number" min="1" max="1048576" step="1" value="{{ old('uploads.'.$key,$settings['uploads.'.$key]) }}" required><span>MB</span></div>
                        <small>{{ $policy[1] }}</small>
                    </div>
                @endforeach
            </div>
            <div class="chrome-note"><i class="fa-solid fa-circle-info"></i><span>These are application-level limits. Your PHP, web-server/proxy and hosting limits must be equal to or higher than the largest value you configure.</span></div>
        </div>
    </div>
</div>

<div class="form-card mail-routing-card">
    <div class="section-heading">
        <div>
            <div class="eyebrow">HELP DESK MAILBOXES</div>
            <h2>Contact &amp; Career email login</h2>
            <p>Connect the two official mailboxes directly. Use the separate Verify button for each mailbox first. Save will be accepted only after the entered email/password has passed a live IMAP login check.</p>
        </div>
        <i class="fa-solid fa-envelope-circle-check"></i>
    </div>

    <div class="mail-routing-grid">
        <div class="mail-route">
            <div class="route-title"><i class="fa-solid fa-circle-info"></i><span>Contact</span></div>
            <label for="mail-contact-email">Official email</label>
            <input id="mail-contact-email" type="email" name="mail[contact_email]" value="{{ old('mail.contact_email',$contactAccount->address ?? 'info@fuelfreepowerplant.com') }}" placeholder="info@fuelfreepowerplant.com" autocomplete="username">
            <label for="mail-contact-password">Password</label>
            <input id="mail-contact-password" type="password" name="mail[contact_password]" placeholder="{{ $contactAccount ? 'Leave blank to keep the saved password' : 'Enter mailbox password' }}" autocomplete="new-password">
            <div class="mail-verify-row">
                <button type="button" class="mail-verify-btn" data-mailbox-verify="contact"><i class="fa-solid fa-plug-circle-check"></i> Verify Contact Login</button>
                @if(session('mail_verify_contact'))<span class="mail-verify-success"><i class="fa-solid fa-circle-check"></i> {{ session('mail_verify_contact') }}</span>@endif
            </div>
            <small>Contact replies are always sent from <strong>info@fuelfreepowerplant.com</strong>. Passwords are encrypted after saving. Verify the login before saving.</small>
        </div>

        <div class="mail-route">
            <div class="route-title"><i class="fa-solid fa-briefcase"></i><span>Career</span></div>
            <label for="mail-career-email">Official email</label>
            <input id="mail-career-email" type="email" name="mail[career_email]" value="{{ old('mail.career_email',$careerAccount->address ?? 'career@fuelfreepowerplant.com') }}" placeholder="career@fuelfreepowerplant.com" autocomplete="username">
            <label for="mail-career-password">Password</label>
            <input id="mail-career-password" type="password" name="mail[career_password]" placeholder="{{ $careerAccount ? 'Leave blank to keep the saved password' : 'Enter mailbox password' }}" autocomplete="new-password">
            <div class="mail-verify-row">
                <button type="button" class="mail-verify-btn" data-mailbox-verify="career"><i class="fa-solid fa-plug-circle-check"></i> Verify Career Login</button>
                @if(session('mail_verify_career'))<span class="mail-verify-success"><i class="fa-solid fa-circle-check"></i> {{ session('mail_verify_career') }}</span>@endif
            </div>
            <small>Career replies are always sent from <strong>career@fuelfreepowerplant.com</strong>. CV attachments are stored on the Help Desk server. Verify the login before saving.</small>
        </div>
    </div>

    <div class="mail-routing-note">
        <i class="fa-solid fa-shield-halved"></i>
        <span><strong>Mailbox offloading:</strong> Help Desk imports the INBOX messages and attachments to private server storage, then permanently expunges the original message from the external mailbox. Replies do not create a Sent copy there.</span>
    </div>
</div>

<div class="form-card builder-links-card"><div class="section-heading"><div><div class="eyebrow">DESIGN &amp; LAYOUT</div><h2>Dedicated control centers</h2><p>Website composition and visual settings live in their own managers. Only controls you are authorized to open are shown here.</p></div><i class="fa-solid fa-wand-magic-sparkles"></i></div><div class="builder-links">
@if(auth()->user()->hasPermission('website.view'))<a href="{{route('admin.homepage-builder.index')}}"><i class="fa-solid fa-house"></i><span><b>Homepage Builder</b><small>Sections, visibility &amp; ordering</small></span></a><a href="{{route('admin.navigation.index')}}"><i class="fa-solid fa-bars-staggered"></i><span><b>Menu Builder</b><small>Menus, submenus &amp; drag ordering</small></span></a><a href="{{route('admin.design.index',['area'=>'header'])}}"><i class="fa-solid fa-window-maximize"></i><span><b>Header Builder</b><small>Header components &amp; visibility</small></span></a><a href="{{route('admin.design.index',['area'=>'footer'])}}"><i class="fa-solid fa-table-columns"></i><span><b>Footer Builder</b><small>Footer components &amp; visibility</small></span></a><a href="{{route('admin.theme.index')}}"><i class="fa-solid fa-palette"></i><span><b>Theme Builder</b><small>Colors, typography &amp; layout</small></span></a>@endif
@if(auth()->user()->hasPermission('cms.view'))<a href="{{route('admin.cms.index')}}"><i class="fa-solid fa-file-lines"></i><span><b>Page Builder</b><small>Pages, blocks &amp; publishing</small></span></a>@endif
</div></div>

<div class="actions"><button type="submit"><i class="fa-solid fa-floppy-disk"></i> Save all settings</button></div>
</form>

<script>
document.addEventListener('DOMContentLoaded', () => {
    document.querySelectorAll('[data-mailbox-verify]').forEach((button) => {
        button.addEventListener('click', async () => {
            const group = button.dataset.mailboxVerify;
            const email = document.getElementById('mail-' + group + '-email');
            const password = document.getElementById('mail-' + group + '-password');
            const row = button.closest('.mail-verify-row');
            let status = row?.querySelector('.mail-verify-live');

            if (!email?.value.trim() || !password?.value) {
                if (!status) {
                    status = document.createElement('span');
                    status.className = 'mail-verify-live';
                    row.appendChild(status);
                }
                status.textContent = 'Enter the email and password first.';
                status.classList.remove('mail-verify-success');
                return;
            }

            button.disabled = true;
            button.classList.add('is-loading');
            button.innerHTML = '<i class="fa-solid fa-spinner fa-spin"></i> Verifying...';
            if (!status) {
                status = document.createElement('span');
                status.className = 'mail-verify-live';
                row.appendChild(status);
            }
            status.textContent = 'Connecting to the mailbox...';
            status.classList.remove('mail-verify-success');

            try {
                const response = await fetch(@json(route('admin.settings.mail.verify')), {
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': document.querySelector('input[name="_token"]').value,
                        'Accept': 'application/json',
                    },
                    body: new URLSearchParams({
                        _token: document.querySelector('input[name="_token"]').value,
                        group,
                        email: email.value.trim(),
                        password: password.value,
                    }),
                });
                const data = await response.json();
                if (!response.ok) throw new Error(data.message || 'Mailbox login verification failed.');

                status.textContent = data.message;
                status.classList.add('mail-verify-success');
                button.innerHTML = '<i class="fa-solid fa-circle-check"></i> Login Verified';
            } catch (error) {
                status.textContent = error.message || 'Mailbox login verification failed.';
                status.classList.remove('mail-verify-success');
                button.innerHTML = group === 'career'
                    ? '<i class="fa-solid fa-plug-circle-check"></i> Verify Career Login'
                    : '<i class="fa-solid fa-plug-circle-check"></i> Verify Contact Login';
            } finally {
                button.disabled = false;
                button.classList.remove('is-loading');
            }
        });
    });
});
</script>
@endsection

@push('styles')
<style>
.settings-stack{width:100%;max-width:1120px;display:grid;gap:18px}.settings-stack .form-card{min-width:0}
.chrome-section{padding-top:18px;margin-top:18px;border-top:1px solid rgba(76,205,233,.09)}
.chrome-section:first-of-type{padding-top:0;margin-top:0;border-top:0}
.chrome-section-title{display:flex;align-items:center;gap:9px;margin-bottom:14px;color:#dff5f8;font-size:12px;font-weight:800}
.chrome-section-title i{color:#51cfe9}
.chrome-note{display:flex;gap:9px;align-items:flex-start;margin-top:14px;padding:11px 12px;border-radius:11px;background:rgba(72,216,241,.035);border:1px solid rgba(72,216,241,.08);color:#718f9a;font-size:8px;line-height:1.55}
.chrome-note i{color:#58cfe9;margin-top:1px}
.mail-routing-grid{display:grid;grid-template-columns:1fr 1fr;gap:14px}
.mail-route{padding:15px;border:1px solid rgba(76,205,233,.11);border-radius:15px;background:rgba(76,205,233,.025)}
.route-title{display:flex;align-items:center;gap:8px;margin-bottom:14px;color:#dff5f8;font-size:12px;font-weight:800}
.route-title i{color:#51cfe9}
.mail-route label{font-size:11px;color:#a9c2ca;font-weight:700;margin-top:9px}
.mail-route input{margin-bottom:3px}
.mail-route small{display:block;color:#678692;font-size:8px;line-height:1.55;margin-top:8px}
.mail-route small strong{color:#9bc4ce}
.mail-routing-note{display:flex;gap:9px;align-items:flex-start;margin-top:14px;padding:12px 13px;border-radius:12px;background:rgba(72,216,241,.035);border:1px solid rgba(72,216,241,.08);color:#718f9a;font-size:9px;line-height:1.55}
.mail-routing-note i{color:#58cfe9}
.mail-routing-note strong{color:#9bc4ce}
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
.locked-field{grid-column:1/-1}
.locked-value{min-height:46px;box-sizing:border-box;padding:10px 13px;border-radius:11px;border:1px solid rgba(76,205,233,.08);background:rgba(255,255,255,.018);display:flex;align-items:center;gap:9px;color:#7899a5}
.locked-value i{color:#587b86;font-size:12px}
.locked-value strong{color:#9bbcc5;font-weight:700}
.locked-value small{margin-left:auto;color:#526f79;font-size:8px}
.actions{display:flex;justify-content:flex-end}
.actions button{border:0;border-radius:12px;padding:13px 20px;background:#31afd2;color:#fff;font-weight:700;box-shadow:0 8px 24px rgba(49,175,210,.14)}
@media(max-width:900px){.mail-routing-grid{grid-template-columns:1fr}.upload-policy-grid{grid-template-columns:repeat(2,minmax(0,1fr))}.builder-links{grid-template-columns:1fr 1fr}}\n@media(max-width:650px){
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