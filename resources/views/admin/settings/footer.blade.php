@extends('layouts.admin')
@section('title','Footer Builder')
@section('content')
<x-admin.page-header title="Footer Builder" eyebrow="APPEARANCE" description="Manage the global website footer from one place." :actions="'<a class="admin-btn" href="'.route('admin.settings').'">Settings</a>'" />
@if(session('status'))<div class="admin-card" style="margin-bottom:16px"><div class="admin-card__body">{{ session('status') }}</div></div>@endif
@if($errors->any())<div class="admin-card" style="margin-bottom:16px"><div class="admin-card__body">{{ $errors->first() }}</div></div>@endif
<form method="POST" action="{{ route('admin.settings.footer.update') }}">@csrf
<x-admin.card><x-slot:header>Global footer content</x-slot:header>
<div class="footer-grid">
@foreach([
'footer.tagline'=>'Tagline','footer.technology'=>'Technology text','footer.office_heading'=>'Office heading','footer.address'=>'Office address',
'footer.contact_heading'=>'Contact heading','footer.email'=>'Contact email','footer.phone'=>'Phone','footer.website'=>'Website label',
'footer.website_url'=>'Website URL','footer.get_in_touch_label'=>'CTA label','footer.get_in_touch_url'=>'CTA URL','footer.copyright_text'=>'Copyright text'
] as $key=>$label)
<x-admin.setting-field :label="$label" :id="'setting-'.str_replace('.','-',$key)">
@if(in_array($key,['footer.address','footer.tagline','footer.technology','footer.copyright_text']))
<textarea class="admin-input" id="setting-{{ str_replace('.','-',$key) }}" name="{{ $key }}" rows="3" maxlength="500">{{ old($key,$settings[$key]??'') }}</textarea>
@else
<input class="admin-input" id="setting-{{ str_replace('.','-',$key) }}" name="{{ $key }}" value="{{ old($key,$settings[$key]??'') }}" maxlength="500" required>
@endif
</x-admin.setting-field>
@endforeach
</div>
<div style="display:flex;justify-content:flex-end;margin-top:18px"><button class="admin-btn admin-btn--primary" type="submit"><i class="fa-solid fa-floppy-disk"></i> Save Footer</button></div>
</x-admin.card></form>
@push('head')<style>
.footer-grid{display:grid;grid-template-columns:repeat(2,minmax(0,1fr));gap:14px}.admin-input{width:100%;box-sizing:border-box;min-height:38px;padding:8px 11px;border:1px solid var(--admin-border);border-radius:10px;background:rgba(255,255,255,.035);color:var(--admin-text);outline:none;font:inherit}.admin-input:focus{border-color:var(--admin-primary);box-shadow:0 0 0 3px rgba(85,204,231,.08)}
@media(max-width:700px){.footer-grid{grid-template-columns:1fr}}
</style>@endpush
@endsection