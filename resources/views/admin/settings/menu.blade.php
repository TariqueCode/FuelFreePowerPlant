@extends('layouts.admin')
@section('title','Menu Builder')
@section('content')
<x-admin.page-header title="Menu Builder" eyebrow="NAVIGATION" description="Control the global company navigation from one source." :actions="'<a class="admin-btn" href="'.route('admin.settings').'">Settings</a>'" />
@if(session('status'))<div class="admin-card" style="margin-bottom:16px"><div class="admin-card__body">{{ session('status') }}</div></div>@endif
@if($errors->any())<div class="admin-card" style="margin-bottom:16px"><div class="admin-card__body">{{ $errors->first() }}</div></div>@endif
@php
$customItems = json_decode((\App\Models\SystemSetting::where('key','navigation.custom_items')->value('value') ?? '[]'), true) ?: [];
@endphp<form method="POST" action="{{ route('admin.settings.menu.update') }}">@csrf
<x-admin.card><x-slot:header>Global company navigation</x-slot:header>
<p style="font-size:10px;color:var(--admin-muted);margin:0 0 14px">Only published company pages are listed. Changes apply to the shared public navigation; no page-specific menu is created.</p>
<div class="menu-list">
@forelse($items as $i=>$item)
<div class="menu-row">
<input type="hidden" name="items[{{ $i }}][id]" value="{{ $item->id }}">
<input type="hidden" name="items[{{ $i }}][show_in_navigation]" value="0">
<label class="menu-check"><input type="checkbox" name="items[{{ $i }}][show_in_navigation]" value="1" @checked($item->show_in_navigation)><span></span></label>
<div class="menu-title"><strong>{{ $item->title }}</strong><small>/{{ $item->slug }}</small></div>
<input class="admin-input menu-order" type="number" min="0" max="9999" name="items[{{ $i }}][navigation_order]" value="{{ $item->navigation_order ?? $i }}">
</div>
@empty
<div style="font-size:10px;color:var(--admin-muted)">No published company pages are available.</div>
@endforelse
</div>
<div style="margin-top:22px;border-top:1px solid var(--admin-border);padding-top:18px">
<div style="font-size:11px;font-weight:700;margin-bottom:6px">Custom links</div>
<p style="font-size:10px;color:var(--admin-muted);margin:0 0 12px">Optional external links. No JavaScript or drag-and-drop dependency is added.</p>
<div id="custom-menu-items"></div>
<button type="button" class="admin-btn" onclick="addCustomMenuItem()">+ Add custom link</button>
</div><div style="display:flex;justify-content:flex-end;margin-top:18px"><button class="admin-btn admin-btn--primary" type="submit"><i class="fa-solid fa-floppy-disk"></i> Save Menu</button></div>
</x-admin.card></form>
@push('head')<style>
.custom-menu-row{display:grid;grid-template-columns:1fr 1.5fr 34px;gap:8px;margin-bottom:8px}.custom-menu-row .admin-input{min-height:34px}

.menu-list{display:grid;gap:8px}.menu-row{display:grid;grid-template-columns:30px 1fr 80px;align-items:center;gap:10px;padding:10px 12px;border:1px solid var(--admin-border);border-radius:10px}.menu-title{min-width:0}.menu-title strong{display:block;font-size:11px}.menu-title small{display:block;color:var(--admin-muted);font-size:9px;margin-top:2px;overflow:hidden;text-overflow:ellipsis}.menu-check input{position:absolute;opacity:0}.menu-check span{display:block;width:18px;height:18px;border:1px solid var(--admin-border);border-radius:5px;position:relative}.menu-check input:checked+span{background:var(--admin-primary);border-color:var(--admin-primary)}.menu-check input:checked+span:after{content:'✓';position:absolute;left:3px;top:0;font-size:12px;color:#041017}.menu-order{min-height:34px!important;text-align:center}@media(max-width:600px){.menu-row{grid-template-columns:30px 1fr 65px}}
</style>
<script>
function addCustomMenuItem(label='',url=''){const wrap=document.getElementById('custom-menu-items'),i=wrap.children.length,row=document.createElement('div');row.className='custom-menu-row';row.innerHTML='<input class="admin-input" name="custom_items['+i+'][label]" maxlength="80" placeholder="Label" value="'+label.replace(/"/g,'&quot;')+'" required><input class="admin-input" type="url" name="custom_items['+i+'][url]" maxlength="500" placeholder="https://example.com" value="'+url.replace(/"/g,'&quot;')+'" required><button type="button" class="admin-btn" onclick="this.parentElement.remove()">×</button>';wrap.appendChild(row)}
</script>@endpush
@endsection