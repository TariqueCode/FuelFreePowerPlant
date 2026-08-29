@extends('layouts.admin')
@section('title','Theme Builder')
@section('content')
<x-admin.page-header title="Theme Builder" eyebrow="APPEARANCE" description="One global visual policy for the public website." :actions="'<a class="admin-btn" href="'.route('admin.settings').'">Settings</a>'" />
@if(session('status'))<div class="admin-card" style="margin-bottom:16px"><div class="admin-card__body">{{ session('status') }}</div></div>@endif
@if($errors->any())<div class="admin-card" style="margin-bottom:16px"><div class="admin-card__body">{{ $errors->first() }}</div></div>@endif
<form method="POST" action="{{ route('admin.settings.theme.update') }}">@csrf
<x-admin.card><x-slot:header>Global color tokens</x-slot:header>
<div class="theme-grid">
@foreach(['theme.primary'=>'Primary','theme.secondary'=>'Secondary','theme.accent'=>'Accent','theme.surface'=>'Surface','theme.text'=>'Text','theme.muted'=>'Muted'] as $key=>$label)
<x-admin.setting-field :label="$label" :id="'theme-'.str_replace('.','-',$key)"><div class="color-row"><input type="color" name="{{ $key }}" value="{{ old($key,$settings[$key]) }}" aria-label="{{ $label }}"><input class="admin-input" value="{{ old($key,$settings[$key]) }}" readonly></div></x-admin.setting-field>
@endforeach
</div>
<x-admin.setting-field label="Global border radius" id="theme-radius"><input class="admin-input" type="number" min="0" max="32" name="theme.radius" value="{{ old('theme.radius',$settings['theme.radius']) }}"></x-admin.setting-field>
<div class="theme-grid">
<x-admin.setting-field label="Body font" id="theme-font-body"><input class="admin-input" name="theme.font_body" maxlength="120" value="{{ old('theme.font_body',$settings['theme.font_body']) }}"></x-admin.setting-field>
<x-admin.setting-field label="Heading font" id="theme-font-heading"><input class="admin-input" name="theme.font_heading" maxlength="120" value="{{ old('theme.font_heading',$settings['theme.font_heading']) }}"></x-admin.setting-field>
<x-admin.setting-field label="Base font size (px)" id="theme-base-size"><input class="admin-input" type="number" min="12" max="22" name="theme.base_size" value="{{ old('theme.base_size',$settings['theme.base_size']) }}"></x-admin.setting-field>
<x-admin.setting-field label="Line height" id="theme-line-height"><input class="admin-input" type="number" min="1.1" max="2.2" step="0.1" name="theme.line_height" value="{{ old('theme.line_height',$settings['theme.line_height']) }}"></x-admin.setting-field>
<x-admin.setting-field label="Section spacing (px)" id="theme-section-space"><input class="admin-input" type="number" min="0" max="160" name="theme.space_section" value="{{ old('theme.space_section',$settings['theme.space_section']) }}"></x-admin.setting-field>
<x-admin.setting-field label="Content spacing (px)" id="theme-content-space"><input class="admin-input" type="number" min="0" max="80" name="theme.space_content" value="{{ old('theme.space_content',$settings['theme.space_content']) }}"></x-admin.setting-field>
</div>
<div style="margin-top:18px;border-top:1px solid var(--admin-border);padding-top:18px"><div style="font-size:11px;font-weight:700;margin-bottom:10px">Component tokens</div><div class="theme-grid">
<x-admin.setting-field label="Card padding (px)" id="theme-card-padding"><input class="admin-input" type="number" min="8" max="64" name="theme.card_padding" value="{{ old('theme.card_padding',$settings['theme.card_padding']) }}"></x-admin.setting-field>
<x-admin.setting-field label="Button radius (px)" id="theme-button-radius"><input class="admin-input" type="number" min="0" max="32" name="theme.button_radius" value="{{ old('theme.button_radius',$settings['theme.button_radius']) }}"></x-admin.setting-field>
<x-admin.setting-field label="Button height (px)" id="theme-button-height"><input class="admin-input" type="number" min="28" max="64" name="theme.button_height" value="{{ old('theme.button_height',$settings['theme.button_height']) }}"></x-admin.setting-field>
<x-admin.setting-field label="Input radius (px)" id="theme-input-radius"><input class="admin-input" type="number" min="0" max="32" name="theme.input_radius" value="{{ old('theme.input_radius',$settings['theme.input_radius']) }}"></x-admin.setting-field>
</div></div><div style="display:flex;justify-content:flex-end;margin-top:18px"><button class="admin-btn admin-btn--primary" type="submit"><i class="fa-solid fa-floppy-disk"></i> Save Theme</button></div>
</x-admin.card></form>
@push('head')<style>.theme-grid{display:grid;grid-template-columns:repeat(3,minmax(0,1fr));gap:14px}.color-row{display:flex;gap:8px}.color-row input[type=color]{width:42px;height:38px;padding:3px;border:1px solid var(--admin-border);border-radius:9px;background:transparent}.admin-input{width:100%;box-sizing:border-box;min-height:38px;padding:8px 11px;border:1px solid var(--admin-border);border-radius:10px;background:rgba(255,255,255,.035);color:var(--admin-text);font:inherit}@media(max-width:800px){.theme-grid{grid-template-columns:repeat(2,minmax(0,1fr))}}@media(max-width:520px){.theme-grid{grid-template-columns:1fr}}</style>@endpush
@endsection