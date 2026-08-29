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
<div style="display:flex;justify-content:flex-end;margin-top:18px"><button class="admin-btn admin-btn--primary" type="submit"><i class="fa-solid fa-floppy-disk"></i> Save Theme</button></div>
</x-admin.card></form>
@push('head')<style>.theme-grid{display:grid;grid-template-columns:repeat(3,minmax(0,1fr));gap:14px}.color-row{display:flex;gap:8px}.color-row input[type=color]{width:42px;height:38px;padding:3px;border:1px solid var(--admin-border);border-radius:9px;background:transparent}.admin-input{width:100%;box-sizing:border-box;min-height:38px;padding:8px 11px;border:1px solid var(--admin-border);border-radius:10px;background:rgba(255,255,255,.035);color:var(--admin-text);font:inherit}@media(max-width:800px){.theme-grid{grid-template-columns:repeat(2,minmax(0,1fr))}}@media(max-width:520px){.theme-grid{grid-template-columns:1fr}}</style>@endpush
@endsection