@extends('layouts.admin')
@section('title','Header Builder')
@section('content')
<x-admin.page-header title="Header Builder" eyebrow="APPEARANCE" description="Control the global website header labels from one place." :actions="'<a class="admin-btn" href="'.route('admin.settings').'">Settings</a>'" />
@if(session('status'))<div class="admin-card settings-alert"><div class="admin-card__body">{{ session('status') }}</div></div>@endif
@if($errors->any())<div class="admin-card settings-alert"><div class="admin-card__body">{{ $errors->first() }}</div></div>@endif
<form method="POST" action="{{ route('admin.settings.header.update') }}">
@csrf
<x-admin.card>
    <x-slot:header>Header controls</x-slot:header>
    <div class="header-builder-grid">
      <x-admin.setting-field label="Logo source" id="header-logo-source">
        <div class="admin-help">Uses the existing global branding/logo. No duplicate upload system is created here.</div>
      </x-admin.setting-field>
      <x-admin.setting-field label="Logo width (px)" id="header-logo-width"><input class="admin-input" type="number" name="header.logo_width" min="24" max="96" value="{{ old('header.logo_width',$settings['header.logo_width']??'42') }}" required></x-admin.setting-field>
      <x-admin.setting-field label="Logo gap (px)" id="header-logo-gap"><input class="admin-input" type="number" name="header.logo_gap" min="0" max="24" value="{{ old('header.logo_gap',$settings['header.logo_gap']??'9') }}" required></x-admin.setting-field>
      <x-admin.setting-field label="Social icon size (px)" id="header-social-size"><input class="admin-input" type="number" name="header.social_size" min="12" max="28" value="{{ old('header.social_size',$settings['header.social_size']??'13') }}" required></x-admin.setting-field>
      <x-admin.setting-field label="Social icon gap (px)" id="header-social-gap"><input class="admin-input" type="number" name="header.social_gap" min="0" max="16" value="{{ old('header.social_gap',$settings['header.social_gap']??'5') }}" required></x-admin.setting-field>
      <x-admin.setting-field label="Header behavior" id="header-behavior">
        <div class="admin-help">Global header settings apply consistently across supported devices.</div>
      </x-admin.setting-field>
    </div><div class="header-layout-grid">
      <x-admin.setting-field label="Header height (px)" id="header-height"><input class="admin-input" type="number" name="header.height" min="48" max="120" value="{{ old('header.height',$settings['header.height']??'64') }}" required></x-admin.setting-field>
      <x-admin.setting-field label="Container width (px)" id="header-container-width"><input class="admin-input" type="number" name="header.container_width" min="960" max="1600" value="{{ old('header.container_width',$settings['header.container_width']??'1280') }}" required></x-admin.setting-field>
      <x-admin.setting-field label="Navigation gap (px)" id="header-nav-gap"><input class="admin-input" type="number" name="header.nav_gap" min="0" max="24" value="{{ old('header.nav_gap',$settings['header.nav_gap']??'4') }}" required></x-admin.setting-field>
      <x-admin.setting-field label="Desktop alignment" id="header-alignment"><select class="admin-input" name="header.alignment"><option value="left" @selected(old('header.alignment',$settings['header.alignment']??'center')==='left')>Left</option><option value="center" @selected(old('header.alignment',$settings['header.alignment']??'center')==='center')>Center</option><option value="right" @selected(old('header.alignment',$settings['header.alignment']??'center')==='right')>Right</option></select></x-admin.setting-field>
      <label class="admin-toggle"><input type="hidden" name="header.sticky" value="0"><input type="checkbox" name="header.sticky" value="1" @checked(old('header.sticky',$settings['header.sticky']??'1')==='1')><span>Sticky header</span></label>
    </div>
<div class="header-mobile-section-title">Mobile header</div>
    <div class="header-mobile-grid">
      <x-admin.setting-field label="Mobile height (px)" id="header-mobile-height"><input class="admin-input" type="number" name="header.mobile_height" min="48" max="88" value="{{ old('header.mobile_height',$settings['header.mobile_height']??'56') }}" required></x-admin.setting-field>
      <x-admin.setting-field label="Mobile logo max width (px)" id="header-mobile-logo-width"><input class="admin-input" type="number" name="header.mobile_logo_width" min="24" max="48" value="{{ old('header.mobile_logo_width',$settings['header.mobile_logo_width']??'34') }}" required></x-admin.setting-field>
      <label class="admin-toggle"><input type="hidden" name="header.mobile_social_visible" value="0"><input type="checkbox" name="header.mobile_social_visible" value="1" @checked(old('header.mobile_social_visible',$settings['header.mobile_social_visible']??'0')==='1')><span>Show social icons on mobile</span></label>
      <label class="admin-toggle"><input type="hidden" name="header.mobile_sticky" value="0"><input type="checkbox" name="header.mobile_sticky" value="1" @checked(old('header.mobile_sticky',$settings['header.mobile_sticky']??'1')==='1')><span>Sticky mobile header</span></label>
    </div>
<div class="header-visibility-grid">
@foreach(['header.logo_visible'=>'Show logo','header.social_visible'=>'Show social icons','header.portal_visible'=>'Show portal'] as $key=>$label)
<label class="admin-toggle"><input type="checkbox" name="{{ $key }}" value="1" @checked(old($key,$settings[$key]??'1')==='1')><span>{{ $label }}</span></label>
@endforeach
</div><div class="header-section-title">Navigation labels</div>
    <div class="header-label-grid">
    @foreach([
      'header.home_label'=>'Home','header.management_label'=>'Management Team','header.gallery_label'=>'Gallery',
      'header.news_label'=>'News & Notices','header.career_label'=>'Career','header.contact_label'=>'Contact',
      'header.webmail_label'=>'Webmail','header.portal_label'=>'Portal','header.login_label'=>'Login'
    ] as $key=>$label)
      <x-admin.setting-field :label="$label" :id="'setting-'.str_replace('.','-',$key)">
        <input class="admin-input" id="setting-{{ str_replace('.','-',$key) }}" name="{{ $key }}" value="{{ old($key,$settings[$key]) }}" maxlength="60" required>
      </x-admin.setting-field>
    @endforeach
    </div>
    <div class="header-actions">
      <button class="admin-btn admin-btn--primary" type="submit"><i class="fa-solid fa-floppy-disk"></i> Save Header</button>
    </div>
</x-admin.card>
</form>
@push('head')
<style>
.header-mobile-grid{display:grid;grid-template-columns:repeat(2,minmax(0,1fr));gap:var(--admin-space-control,14px);margin-bottom:var(--admin-space-section,18px)}.header-mobile-section-title{margin:4px 0 14px;font-size:10px;font-weight:650}.header-layout-grid{display:grid;grid-template-columns:repeat(2,minmax(0,1fr));gap:var(--admin-space-control,14px);margin-bottom:var(--admin-space-section,18px)}.header-builder-grid{display:grid;grid-template-columns:repeat(2,minmax(0,1fr));gap:var(--admin-space-control,14px);margin-bottom:var(--admin-space-section,18px)}.header-visibility-grid{display:grid;grid-template-columns:repeat(3,minmax(0,1fr));gap:var(--admin-space-xs,10px);margin-bottom:var(--admin-space-section,18px)}.header-label-grid{display:grid;grid-template-columns:repeat(2,minmax(0,1fr));gap:var(--admin-space-control,14px)}.header-section-title{margin-bottom:14px;font-size:10px;font-weight:650}.header-actions{display:flex;justify-content:flex-end;margin-top:var(--admin-space-section,18px)}.admin-help{font-size:10px;line-height:1.5;color:var(--admin-muted)}.admin-toggle{display:flex;align-items:center;gap:8px;min-height:38px;padding:0 11px;border:1px solid var(--admin-border);border-radius:var(--admin-radius-sm);font-size:10px}.admin-toggle input{accent-color:var(--admin-primary)}@media(max-width:700px){.header-mobile-grid,.header-layout-grid,.header-builder-grid,.header-visibility-grid,.header-label-grid{grid-template-columns:1fr}}
</style>
@endpush
@endsection
