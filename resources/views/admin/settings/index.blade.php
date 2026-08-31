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

<div class="form-card builder-links-card"><div class="section-heading"><div><div class="eyebrow">DESIGN &amp; LAYOUT</div><h2>Visual builders</h2><p>Use dedicated builders for homepage composition, navigation and global visual styling.</p></div><i class="fa-solid fa-wand-magic-sparkles"></i></div><div class="builder-links"><a href="{{route('admin.homepage-builder.index')}}"><i class="fa-solid fa-house"></i><span><b>Homepage Builder</b><small>Sections, visibility &amp; ordering</small></span></a><a href="{{route('admin.navigation.index')}}"><i class="fa-solid fa-bars-staggered"></i><span><b>Menu Builder</b><small>Menus, submenus &amp; drag ordering</small></span></a><a href="{{route('admin.cms.index')}}"><i class="fa-solid fa-file-lines"></i><span><b>Page Builder</b><small>Pages, blocks &amp; publishing</small></span></a><a href="{{route('admin.design.index',['area'=>'header'])}}"><i class="fa-solid fa-window-maximize"></i><span><b>Header Builder</b><small>Header components &amp; visibility</small></span></a><a href="{{route('admin.design.index',['area'=>'footer'])}}"><i class="fa-solid fa-table-columns"></i><span><b>Footer Builder</b><small>Footer components &amp; visibility</small></span></a><a href="{{route('admin.theme.index')}}"><i class="fa-solid fa-palette"></i><span><b>Theme Builder</b><small>Colors, typography &amp; layout</small></span></a></div></div>