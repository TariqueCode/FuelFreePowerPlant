@extends('layouts.admin')
@section('title','Footer Builder')
@section('content')
<x-admin.page-header title="Footer Builder" eyebrow="APPEARANCE" description="Manage the global website footer from one place." :actions="'<a class="admin-btn" href="'.route('admin.settings').'">Settings</a>'" />
@if(session('status'))<div class="admin-card settings-alert"><div class="admin-card__body">{{ session('status') }}</div></div>@endif
@if($errors->any())<div class="admin-card settings-alert"><div class="admin-card__body">{{ $errors->first() }}</div></div>@endif
<form method="POST" action="{{ route('admin.settings.footer.update') }}">@csrf
<x-admin.card><x-slot:header>Global footer content</x-slot:header>
<div class="footer-structure-grid">
<x-admin.setting-field label="Container width (px)" id="footer-container-width"><input class="admin-input" type="number" name="footer.container_width" min="960" max="1400" value="{{ old('footer.container_width',$settings['footer.container_width']??'1120') }}" required></x-admin.setting-field>
<x-admin.setting-field label="Column gap (px)" id="footer-column-gap"><input class="admin-input" type="number" name="footer.column_gap" min="16" max="64" value="{{ old('footer.column_gap',$settings['footer.column_gap']??'46') }}" required></x-admin.setting-field>
<x-admin.setting-field label="Desktop structure" id="footer-layout"><select class="admin-input" name="footer.layout"><option value="three-column" @selected(old('footer.layout',$settings['footer.layout']??'three-column')==='three-column')>Three columns</option><option value="two-column" @selected(old('footer.layout',$settings['footer.layout']??'three-column')==='two-column')>Two columns</option><option value="stacked" @selected(old('footer.layout',$settings['footer.layout']??'three-column')==='stacked')>Stacked</option></select></x-admin.setting-field>
</div>
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
<div class="settings-save-row"><button class="admin-btn admin-btn--primary" type="submit"><i class="fa-solid fa-floppy-disk"></i> Save Footer</button></div>
</x-admin.card></form>
@push('head')<style>
.footer-structure-grid{display:grid;grid-template-columns:repeat(3,minmax(0,1fr));gap:var(--admin-space-control,14px);margin-bottom:var(--admin-space-section,18px)}.footer-grid{display:grid;grid-template-columns:repeat(2,minmax(0,1fr));gap:var(--admin-space-control,14px)}
@media(max-width:700px){.footer-structure-grid,.footer-grid{grid-template-columns:1fr}}
</style>@endpush
@endsection