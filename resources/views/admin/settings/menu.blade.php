@extends('layouts.admin')
@section('title','Menu Builder')
@section('content')
<x-admin.page-header title="Menu Builder" eyebrow="NAVIGATION" description="Control the global company navigation from one source." :actions="'<a class="admin-btn" href="'.route('admin.settings').'">Settings</a>'" />
@if(session('status'))<div class="admin-card settings-alert"><div class="admin-card__body">{{ session('status') }}</div></div>@endif
@if($errors->any())<div class="admin-card settings-alert"><div class="admin-card__body">{{ $errors->first() }}</div></div>@endif
@php
$customItems = json_decode((\App\Models\SystemSetting::where('key','navigation.custom_items')->value('value') ?? '[]'), true) ?: [];
@endphp<form method="POST" action="{{ route('admin.settings.menu.update') }}">@csrf
<x-admin.card><x-slot:header>Global company navigation</x-slot:header>
<p style="font-size:10px;color:var(--admin-muted);margin:0 0 14px">Only published company pages are listed. Changes apply to the shared public navigation; no page-specific menu is created.</p>
<div class="menu-list" id="menu-list">
@forelse($items as $i=>$item)
<div class="menu-row">
<input type="hidden" name="items[{{ $i }}][id]" value="{{ $item->id }}">
<input type="hidden" name="items[{{ $i }}][show_in_navigation]" value="0">
<label class="menu-check"><input type="checkbox" name="items[{{ $i }}][show_in_navigation]" value="1" @checked($item->show_in_navigation)><span></span></label>
<div class="menu-title"><strong>{{ $item->title }}</strong><small>/{{ $item->slug }}</small></div>
<div class="menu-order-wrap"><select class="admin-input menu-parent" name="items[{{ $i }}][navigation_parent_id]"><option value="">Top level</option>@foreach($items as $parent)<option value="{{ $parent->id }}" @selected((int)($item->navigation_parent_id ?? 0)===(int)$parent->id)>↳ {{ $parent->title }}</option>@endforeach</select><input class="admin-input menu-order" type="number" min="0" max="9999" name="items[{{ $i }}][navigation_order]" value="{{ $item->navigation_order ?? $i }}"><div class="menu-move"><button type="button" class="admin-btn" title="Move up" onclick="moveMenuRow(this,-1)">↑</button><button type="button" class="admin-btn" title="Move down" onclick="moveMenuRow(this,1)">↓</button></div></div>
</div>
@empty
<div style="font-size:10px;color:var(--admin-muted)">No published company pages are available.</div>
@endforelse
</div>
<div class="settings-subsection">
<div class="settings-subsection__title">Custom links</div>
<p class="settings-subsection__help">Optional external links. No JavaScript or drag-and-drop dependency is added.</p>
<div id="custom-menu-items"></div>
<button type="button" class="admin-btn" onclick="addCustomMenuItem()">+ Add custom link</button>
</div><div class="settings-subsection"><div class="settings-subsection__title">Menu groups</div><p class="settings-subsection__help">Create reusable group labels for the shared navigation. Groups are stored centrally.</p><div id="menu-groups">@foreach($menuGroups as $gi=>$group)<div class="menu-group-row"><input class="admin-input" name="menu_groups[{{ $gi }}][label]" maxlength="60" value="{{ $group['label'] ?? '' }}" placeholder="Group label" required><select class="admin-input" name="menu_groups[{{ $gi }}][parent_id]"><option value="">Top level</option>@foreach($items as $parent)<option value="{{ $parent->id }}" @selected((int)($group['parent_id'] ?? 0)===(int)$parent->id)>{{ $parent->title }}</option>@endforeach</select><button type="button" class="admin-btn" onclick="this.parentElement.remove()">×</button></div>@endforeach</div><button type="button" class="admin-btn" onclick="addMenuGroup()">+ Add group</button></div><div class="settings-save-row"><button class="admin-btn admin-btn--primary" type="submit"><i class="fa-solid fa-floppy-disk"></i> Save Menu</button></div>
</x-admin.card></form>
@push('head')<style>
.menu-group-row{display:grid;grid-template-columns:1fr 1fr 34px;gap:8px;margin-bottom:8px}.custom-menu-row{display:grid;grid-template-columns:1fr 1.5fr 34px;gap:8px;margin-bottom:8px}.custom-menu-row .admin-input{min-height:34px}

.menu-list{display:grid;gap:8px}.menu-row{display:grid;grid-template-columns:30px minmax(180px,1fr) minmax(130px,.7fr) 80px;align-items:center;gap:10px;padding:10px 12px;border:1px solid var(--admin-border);border-radius:10px}.menu-title{min-width:0}.menu-title strong{display:block;font-size:11px}.menu-title small{display:block;color:var(--admin-muted);font-size:9px;margin-top:2px;overflow:hidden;text-overflow:ellipsis}.menu-check input{position:absolute;opacity:0}.menu-check span{display:block;width:18px;height:18px;border:1px solid var(--admin-border);border-radius:5px;position:relative}.menu-check input:checked+span{background:var(--admin-primary);border-color:var(--admin-primary)}.menu-check input:checked+span:after{content:'✓';position:absolute;left:3px;top:0;font-size:12px;color:#041017}.menu-order-wrap{display:flex;align-items:center;gap:4px}.menu-parent{min-height:34px!important;font-size:10px}.menu-order{min-height:34px!important;text-align:center;width:58px}.menu-move{display:flex;gap:3px}.menu-move .admin-btn{min-width:24px;padding:0 5px;min-height:28px}.menu-move .admin-btn:disabled{opacity:.4;cursor:not-allowed}@media(max-width:760px){.menu-row{grid-template-columns:30px 1fr;}.menu-parent,.menu-order-wrap{grid-column:2}.menu-order-wrap{justify-content:flex-start}}
</style>
<script>
function addMenuGroup(){const wrap=document.getElementById('menu-groups'),i=wrap.children.length,row=document.createElement('div');row.className='menu-group-row';row.innerHTML='<input class="admin-input" name="menu_groups['+i+'][label]" maxlength="60" placeholder="Group label" required><select class="admin-input" name="menu_groups['+i+'][parent_id]"><option value="">Top level</option>@foreach($items as $parent)<option value="{{ $parent->id }}">{{ $parent->title }}</option>@endforeach</select><button type="button" class="admin-btn" onclick="this.parentElement.remove()">×</button>';wrap.appendChild(row)}
function syncMenuOrder(){document.querySelectorAll('#menu-list .menu-row').forEach((row,i)=>{const input=row.querySelector('.menu-order');if(input) input.value=i;});}
function refreshMenuButtons(){const rows=[...document.querySelectorAll('#menu-list .menu-row')];rows.forEach((row,i)=>row.querySelectorAll('.menu-move button').forEach((b,j)=>b.disabled=(j===0&&i===0)||(j===1&&i===rows.length-1)));}
function moveMenuRow(button,direction){const row=button.closest('.menu-row'),list=document.getElementById('menu-list');if(!row||!list)return;const rows=[...list.querySelectorAll('.menu-row')],i=rows.indexOf(row),target=rows[i+direction];if(!target)return;direction<0?list.insertBefore(row,target):list.insertBefore(target,row);syncMenuOrder();refreshMenuButtons();}
document.addEventListener('DOMContentLoaded',refreshMenuButtons);
function addCustomMenuItem(label='',url=''){const wrap=document.getElementById('custom-menu-items'),i=wrap.children.length,row=document.createElement('div');row.className='custom-menu-row';row.innerHTML='<input class="admin-input" name="custom_items['+i+'][label]" maxlength="80" placeholder="Label" value="'+label.replace(/"/g,'&quot;')+'" required><input class="admin-input" type="url" name="custom_items['+i+'][url]" maxlength="500" placeholder="https://example.com" value="'+url.replace(/"/g,'&quot;')+'" required><button type="button" class="admin-btn" onclick="this.parentElement.remove()">×</button>';wrap.appendChild(row)}
</script>@endpush
@endsection