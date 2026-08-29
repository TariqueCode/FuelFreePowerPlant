@extends('layouts.admin')
@section('title','Header Builder')
@section('content')
<x-admin.page-header title="Header Builder" eyebrow="APPEARANCE" description="Control the global website header labels from one place." :actions="'<a class="admin-btn" href="'.route('admin.settings').'">Settings</a>'" />
@if(session('status'))<div class="admin-card" style="margin-bottom:16px"><div class="admin-card__body">{{ session('status') }}</div></div>@endif
@if($errors->any())<div class="admin-card" style="margin-bottom:16px"><div class="admin-card__body">{{ $errors->first() }}</div></div>@endif
<form method="POST" action="{{ route('admin.settings.header.update') }}">
@csrf
<x-admin.card>
    <x-slot:header>Header controls</x-slot:header><div style="display:grid;grid-template-columns:repeat(3,minmax(0,1fr));gap:10px;margin-bottom:18px">
@foreach(['header.logo_visible'=>'Show logo','header.social_visible'=>'Show social icons','header.portal_visible'=>'Show portal'] as $key=>$label)
<label class="admin-toggle"><input type="checkbox" name="{{ $key }}" value="1" @checked(old($key,$settings[$key]??'1')==='1')><span>{{ $label }}</span></label>
@endforeach
</div><div style="margin-bottom:14px;font-size:10px;font-weight:650">Navigation labels</div>
    <div style="display:grid;grid-template-columns:repeat(2,minmax(0,1fr));gap:14px">
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
    <div style="display:flex;justify-content:flex-end;margin-top:18px">
      <button class="admin-btn admin-btn--primary" type="submit"><i class="fa-solid fa-floppy-disk"></i> Save Header</button>
    </div>
</x-admin.card>
</form>
@push('head')
<style>
.admin-toggle{display:flex;align-items:center;gap:8px;min-height:38px;padding:0 11px;border:1px solid var(--admin-border);border-radius:10px;font-size:10px}.admin-toggle input{accent-color:var(--admin-primary)}.admin-input{width:100%;box-sizing:border-box;min-height:38px;padding:8px 11px;border:1px solid var(--admin-border);border-radius:10px;background:rgba(255,255,255,.035);color:var(--admin-text);outline:none}.admin-input:focus{border-color:var(--admin-primary);box-shadow:0 0 0 3px rgba(85,204,231,.08)}
@media(max-width:700px){form .admin-card [style*="grid-template-columns"]{grid-template-columns:1fr!important}}
</style>
@endpush
@endsection
