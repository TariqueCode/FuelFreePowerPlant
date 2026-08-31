@extends('layouts.portal')
@section('title','System Settings')
@section('content')
<section class="settings-hero">
    <div>
        <span class="eyebrow">PLATFORM CONFIGURATION</span>
        <h1>System Settings</h1>
        <p>Keep only platform-wide controls here. Homepage composition, website content, navigation, mailboxes and visual design live in their dedicated managers.</p>
    </div>
    <div class="settings-scope"><i class="fa-solid fa-shield-halved"></i><span><strong>Focused control</strong><small>No homepage or mailbox configuration here.</small></span></div>
</section>

@if(session('status'))<div class="notice">{{ session('status') }}</div>@endif
@if($errors->any())<div class="errors">{{ $errors->first() }}</div>@endif

<form method="POST" enctype="multipart/form-data" action="{{ route('admin.settings.update') }}" class="settings-stack">
@csrf

<section class="settings-card">
    <header class="card-heading">
        <div><span class="eyebrow">IDENTITY</span><h2>Company identity</h2><p>Global brand information shared across the public website and platform.</p></div>
        <span class="heading-icon"><i class="fa-solid fa-building"></i></span>
    </header>

    <div class="identity-card">
        <div class="logo-preview">
            @if($settings['company.logo_path'])<img src="{{ asset('storage/'.$settings['company.logo_path']) }}" alt="Company logo">
            @else<i class="fa-solid fa-building"></i>@endif
        </div>
        <div class="identity-copy">
            <strong>{{ $settings['company.name'] }}</strong>
            <span>Use a clear square or transparent logo for the best result.</span>
            <label class="upload-button"><i class="fa-solid fa-cloud-arrow-up"></i><span>Change logo</span><input type="file" name="company[logo]" accept="image/png,image/jpeg,image/webp,image/svg+xml"></label>
        </div>
    </div>

    <div class="field-grid">
        <label><span>Company name</span><input name="company[name]" value="{{ old('company.name',$settings['company.name']) }}" required maxlength="150"></label>
        <label><span>Company domain</span><input name="company[domain]" value="{{ old('company.domain',$settings['company.domain']) }}" required maxlength="255"></label>
        <label class="full"><span>Tagline</span><input name="company[tagline]" value="{{ old('company.tagline',$settings['company.tagline']) }}" maxlength="255"></label>
        <label><span>Timezone</span><input name="company[timezone]" value="{{ old('company.timezone',$settings['company.timezone']) }}" required placeholder="Asia/Dhaka"></label>
    </div>
</section>

<section class="settings-card">
    <header class="card-heading">
        <div><span class="eyebrow">STORAGE</span><h2>Storage &amp; upload policy</h2><p>Only module-wide limits belong here. Homepage-specific upload controls are intentionally excluded.</p></div>
        <span class="heading-icon"><i class="fa-solid fa-hard-drive"></i></span>
    </header>

    <div class="quota-card">
        <div class="quota-icon"><i class="fa-solid fa-database"></i></div>
        <div><strong>Private storage quota</strong><small>Total storage allocation for admin-managed private files.</small></div>
        <label class="quota-input"><input name="storage[quota_gib]" type="number" min="1" max="1048576" step="1" value="{{ old('storage.quota_gib',$settings['storage.quota_gib']) }}" required><span>GiB</span></label>
    </div>

    <div class="policy-heading"><strong>Upload limits</strong><span>One clear limit per non-homepage module.</span></div>
    <div class="upload-grid">
        @foreach([
            'career_max_mb'=>['Career applications','CV and resume uploads.','fa-briefcase'],
            'documents_max_mb'=>['File Manager','Admin documents and media.','fa-folder-open'],
            'gallery_max_mb'=>['Gallery & media','Gallery photos and videos.','fa-images'],
            'content_media_max_mb'=>['CMS media','Images and videos used inside content.','fa-photo-film'],
        ] as $key=>$policy)
        <label class="upload-policy">
            <span class="policy-icon"><i class="fa-solid {{ $policy[2] }}"></i></span>
            <span class="policy-copy"><strong>{{ $policy[0] }}</strong><small>{{ $policy[1] }}</small></span>
            <span class="mb-field"><input name="uploads[{{ $key }}]" type="number" min="1" max="1048576" step="1" value="{{ old('uploads.'.$key,$settings['uploads.'.$key]) }}" required><em>MB</em></span>
        </label>
        @endforeach
    </div>

    <div class="info-note"><i class="fa-solid fa-circle-info"></i><span>These limits are application-level safeguards. Your PHP, web-server/proxy and hosting limits must be equal to or higher than the largest upload you allow.</span></div>
</section>

<section class="settings-card scope-card">
    <header class="card-heading">
        <div><span class="eyebrow">CONTROL CENTERS</span><h2>Where other settings belong</h2><p>A simple separation keeps System Settings clean and prevents duplicate controls.</p></div>
        <span class="heading-icon"><i class="fa-solid fa-compass"></i></span>
    </header>
    <div class="control-links">
        @if(auth()->user()->hasPermission('website.view'))
        <a href="{{route('admin.homepage-builder.index')}}"><i class="fa-solid fa-house"></i><span><strong>Homepage</strong><small>Sections, order, visibility &amp; display rules</small></span><i class="fa-solid fa-arrow-up-right-from-square"></i></a>
        <a href="{{route('admin.navigation.index')}}"><i class="fa-solid fa-bars-staggered"></i><span><strong>Navigation</strong><small>Menus, submenus and ordering</small></span><i class="fa-solid fa-arrow-up-right-from-square"></i></a>
        <a href="{{route('admin.design.index',['area'=>'header'])}}"><i class="fa-solid fa-window-maximize"></i><span><strong>Header &amp; Footer</strong><small>Website chrome and visibility</small></span><i class="fa-solid fa-arrow-up-right-from-square"></i></a>
        <a href="{{route('admin.theme.index')}}"><i class="fa-solid fa-palette"></i><span><strong>Theme</strong><small>Colors, typography and visual system</small></span><i class="fa-solid fa-arrow-up-right-from-square"></i></a>
        @endif
        @if(auth()->user()->hasPermission('cms.view'))
        <a href="{{route('admin.cms.index')}}"><i class="fa-solid fa-file-lines"></i><span><strong>Page Builder</strong><small>Pages, blocks and publishing</small></span><i class="fa-solid fa-arrow-up-right-from-square"></i></a>
        @endif
        <a href="{{route('admin.mail.index')}}"><i class="fa-solid fa-envelope"></i><span><strong>Mail &amp; Conversations</strong><small>Mailbox accounts and communication settings</small></span><i class="fa-solid fa-arrow-up-right-from-square"></i></a>
    </div>
</section>

<div class="save-bar"><div><strong>System settings</strong><span>Changes apply platform-wide. Homepage and mail configuration are managed elsewhere.</span></div><button type="submit"><i class="fa-solid fa-floppy-disk"></i> Save settings</button></div>
</form>
@endsection

@push('styles')
<style>
.settings-stack{width:100%;max-width:1120px;display:grid;gap:18px}.settings-hero{display:flex;justify-content:space-between;align-items:flex-end;gap:24px;margin-bottom:22px}.settings-hero h1{margin:6px 0 7px;font-size:clamp(30px,3.3vw,44px);letter-spacing:-.04em;color:#eaf8fb}.settings-hero p{margin:0;max-width:760px;color:#7899a5;font-size:11px;line-height:1.65}.settings-scope{display:flex;align-items:center;gap:10px;padding:11px 13px;border:1px solid rgba(76,205,233,.12);border-radius:13px;background:rgba(72,216,241,.035);min-width:230px}.settings-scope>i{color:#51cfe9;font-size:17px}.settings-scope strong,.settings-scope small{display:block}.settings-scope strong{color:#dff5f8;font-size:10px}.settings-scope small{color:#668591;font-size:8px;margin-top:3px}.settings-card{min-width:0;padding:22px;border:1px solid var(--line);border-radius:20px;background:linear-gradient(145deg,rgba(8,38,52,.76),rgba(3,19,28,.9));box-shadow:0 16px 50px rgba(0,0,0,.08)}.card-heading{display:flex;align-items:flex-start;justify-content:space-between;gap:20px;margin-bottom:20px}.card-heading h2{margin:5px 0 5px;color:#eaf8fb;font-size:21px;letter-spacing:-.025em}.card-heading p{margin:0;color:#7899a5;font-size:9px;line-height:1.55}.heading-icon{width:42px;height:42px;flex:none;display:grid;place-items:center;border-radius:12px;color:#51cfe9;background:rgba(72,216,241,.07);border:1px solid rgba(72,216,241,.09)}.identity-card{display:flex;align-items:center;gap:15px;padding:15px;margin-bottom:19px;border:1px solid rgba(76,205,233,.12);border-radius:15px;background:rgba(72,216,241,.025)}.logo-preview{width:68px;height:68px;flex:none;display:grid;place-items:center;overflow:hidden;border-radius:16px;border:1px solid var(--line);background:#061923;color:#62d4ed;font-size:24px}.logo-preview img{width:100%;height:100%;object-fit:contain}.identity-copy strong{display:block;color:#e4f5f8;font-size:14px}.identity-copy>span{display:block;color:#718f9a;font-size:8px;margin-top:4px}.upload-button{display:inline-flex!important;align-items:center!important;gap:7px!important;width:max-content;margin-top:9px!important;padding:8px 10px;border:1px solid rgba(104,204,235,.13);border-radius:9px;color:#9ed3df!important;background:rgba(72,216,241,.035);cursor:pointer!important;font-size:9px!important}.upload-button input{display:none!important}.field-grid{display:grid;grid-template-columns:1fr 1fr;gap:14px}.field-grid label,.upload-policy{display:grid;gap:7px}.field-grid label>span{font-size:9px;color:#9eb9c4;font-weight:700}.field-grid .full{grid-column:1/-1}.field-grid input,.quota-input input,.mb-field input{width:100%;box-sizing:border-box;padding:11px 12px;border-radius:10px;border:1px solid rgba(104,204,235,.13);background:#071b27;color:#e9f7fb;outline:none;font:inherit;font-size:10px}.field-grid input:focus,.quota-input input:focus,.mb-field input:focus{border-color:rgba(81,216,240,.4);box-shadow:0 0 0 3px rgba(81,216,240,.06)}.quota-card{display:grid;grid-template-columns:42px minmax(0,1fr) 135px;align-items:center;gap:12px;padding:13px 14px;border:1px solid rgba(76,205,233,.11);border-radius:14px;background:rgba(72,216,241,.025)}.quota-icon{width:42px;height:42px;display:grid;place-items:center;border-radius:12px;background:rgba(72,216,241,.08);color:#58d5ed}.quota-card strong,.quota-card small{display:block}.quota-card strong{font-size:11px;color:#e4f5f8}.quota-card small{font-size:8px;color:#6f8e98;margin-top:3px}.quota-input{position:relative;display:block}.quota-input input{padding-right:42px}.quota-input span{position:absolute;right:10px;top:50%;transform:translateY(-50%);color:#6b8d98;font-size:8px}.policy-heading{display:flex;align-items:end;justify-content:space-between;gap:12px;margin:20px 0 10px}.policy-heading strong{font-size:11px;color:#dff5f8}.policy-heading span{font-size:8px;color:#678692}.upload-grid{display:grid;grid-template-columns:1fr 1fr;gap:10px}.upload-policy{grid-template-columns:36px minmax(0,1fr) 100px;align-items:center;gap:10px;padding:12px;border:1px solid rgba(76,205,233,.1);border-radius:13px;background:rgba(72,216,241,.02)}.policy-icon{width:36px;height:36px;display:grid;place-items:center;border-radius:10px;background:rgba(72,216,241,.07);color:#56d1eb}.policy-copy{min-width:0}.policy-copy strong,.policy-copy small{display:block}.policy-copy strong{color:#dff5f8;font-size:10px}.policy-copy small{color:#678692;font-size:8px;line-height:1.45;margin-top:3px}.mb-field{position:relative}.mb-field input{padding-right:34px;text-align:right}.mb-field em{position:absolute;right:9px;top:50%;transform:translateY(-50%);font-style:normal;color:#638490;font-size:7px}.info-note{display:flex;gap:9px;align-items:flex-start;margin-top:12px;padding:11px 12px;border-radius:11px;border:1px solid rgba(72,216,241,.08);background:rgba(72,216,241,.025);color:#6e8d98;font-size:8px;line-height:1.55}.info-note i{color:#58cfe9;margin-top:1px}.control-links{display:grid;grid-template-columns:repeat(2,minmax(0,1fr));gap:9px}.control-links a{display:grid;grid-template-columns:36px minmax(0,1fr) 16px;align-items:center;gap:10px;padding:12px;border:1px solid rgba(76,205,233,.1);border-radius:12px;color:#6f909b;text-decoration:none;background:rgba(72,216,241,.02);transition:.2s}.control-links a:hover{transform:translateY(-1px);border-color:rgba(76,205,233,.22);background:rgba(72,216,241,.045)}.control-links a>i:first-child{width:36px;height:36px;display:grid;place-items:center;border-radius:10px;color:#57d3ec;background:rgba(72,216,241,.07)}.control-links strong,.control-links small{display:block}.control-links strong{font-size:10px;color:#dff5f8}.control-links small{font-size:8px;margin-top:3px}.control-links a>i:last-child{font-size:8px;color:#4f7380}.save-bar{position:sticky;bottom:10px;z-index:10;display:flex;align-items:center;justify-content:space-between;gap:16px;padding:12px 14px;border:1px solid rgba(76,205,233,.16);border-radius:14px;background:rgba(3,20,29,.94);backdrop-filter:blur(12px);box-shadow:0 14px 35px rgba(0,0,0,.22)}.save-bar strong,.save-bar span{display:block}.save-bar strong{font-size:10px;color:#dff5f8}.save-bar span{font-size:8px;color:#668591;margin-top:3px}.save-bar button{display:inline-flex;align-items:center;justify-content:center;gap:8px;min-height:38px;padding:0 15px;border:0;border-radius:10px;background:linear-gradient(135deg,#25abc9,#1687a4);color:#fff;font-size:9px;font-weight:800;cursor:pointer;white-space:nowrap}
@media(max-width:900px){.settings-hero{align-items:flex-start}.upload-grid{grid-template-columns:1fr}.control-links{grid-template-columns:1fr 1fr}}
@media(max-width:650px){.settings-stack{gap:13px}.settings-hero{display:block;margin-bottom:15px}.settings-hero h1{font-size:30px}.settings-hero p{font-size:9px;line-height:1.6}.settings-scope{margin-top:12px;min-width:0;width:100%}.settings-card{padding:15px;border-radius:16px}.card-heading{gap:12px;margin-bottom:15px}.card-heading h2{font-size:18px}.card-heading p{font-size:8px;line-height:1.5}.heading-icon{width:36px;height:36px;border-radius:10px;font-size:12px}.identity-card{align-items:flex-start;padding:12px}.logo-preview{width:58px;height:58px}.field-grid{grid-template-columns:1fr;gap:11px}.field-grid .full{grid-column:auto}.quota-card{grid-template-columns:38px minmax(0,1fr);gap:9px}.quota-input{grid-column:1/-1}.upload-policy{grid-template-columns:34px minmax(0,1fr) 88px;padding:10px}.policy-icon{width:34px;height:34px}.policy-copy strong{font-size:9px}.policy-copy small{font-size:7px}.control-links{grid-template-columns:1fr}.save-bar{position:static;display:block;padding:11px}.save-bar button{width:100%;margin-top:9px}.save-bar span{font-size:7px}}
@media(max-width:380px){.settings-card{padding:13px}.identity-card{gap:10px}.identity-copy>span{font-size:7px}.upload-policy{grid-template-columns:32px minmax(0,1fr) 82px;gap:8px}.policy-icon{width:32px;height:32px}.mb-field input{padding:10px 30px 10px 7px}.mb-field em{right:7px}}
</style>
@endpush
