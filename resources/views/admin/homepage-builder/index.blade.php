@extends('layouts.portal')
@section('title','Homepage')
@section('content')
@php
    $titles = ['hero'=>'Hero Slider','welcome'=>'Welcome Message','management'=>'Board of Directors','news'=>'News & Notices','gallery'=>'Gallery','highlight'=>'Homepage Highlight','cta'=>'Contact & Call to Action'];
    $icons = ['hero'=>'fa-images','welcome'=>'fa-building','management'=>'fa-users','news'=>'fa-newspaper','gallery'=>'fa-images','highlight'=>'fa-bullhorn','cta'=>'fa-paper-plane'];
    $sourceLabels = ['hero'=>'Slider Manager','welcome'=>'Homepage content','management'=>'Profile Builder','news'=>'News & Notices','gallery'=>'Gallery Manager','highlight'=>'Homepage Highlight','cta'=>'Homepage system section'];
    // Canonical Homepage Builder links only. Retired admin.management and admin.site-content surfaces are intentionally absent.
    $manageRoutes = ['hero'=>route('admin.sliders.index'),'management'=>route('admin.profile-builder.index'),'gallery'=>route('admin.gallery.index'),'highlight'=>route('admin.site-popups.index')];
    $controlSections = array_keys($titles);
    $visibleCount = $sections->where('is_enabled', true)->count();
@endphp

<section class="homepage-hero">
    <div>
        <span class="eyebrow"><i class="fa-solid fa-layer-group"></i> Website / Homepage Control</span>
        <h1>Homepage</h1>
        <p>Arrange homepage sections, control visibility, and use each module as the single source for its content.</p>
    </div>
    <div class="hero-tools">
        <a class="preview-button" href="{{ route('home') }}" target="_blank" rel="noopener"><i class="fa-solid fa-arrow-up-right-from-square"></i> Preview website</a>
        <div class="hero-stat"><strong>{{ $visibleCount }}</strong><span>of {{ $sections->count() }} visible</span></div>
    </div>
</section>

@if(session('status'))<div class="flash flash-success"><i class="fa-solid fa-circle-check"></i>{{ session('status') }}</div>@endif
@if($errors->any())<div class="flash flash-error"><i class="fa-solid fa-triangle-exclamation"></i>{{ $errors->first() }}</div>@endif

<form method="POST" action="{{ route('admin.homepage-builder.update') }}" id="home-builder">
@csrf
<div class="builder">
    <header class="builder-head">
        <div><span class="eyebrow">PAGE STRUCTURE</span><h2>Homepage sections</h2><p>Drag to reorder. Use Controls for homepage display settings; edit source content in its canonical module.</p></div>
        <button class="save-button" type="submit"><i class="fa-solid fa-floppy-disk"></i> Save changes</button>
    </header>

    <div class="section-list" id="section-list">
    @foreach($sections as $index => $section)
        @php
            $settings = is_array($section->settings) ? $section->settings : [];
            $layout = $settings['layout'] ?? 'left';
            $limit = (int)($settings['limit'] ?? match($section->key){'management'=>4,'news'=>3,'gallery'=>4,default=>1});
            $mode = $settings['mode'] ?? 'latest';
        @endphp
        <article class="section-card" draggable="true" data-key="{{ $section->key }}">
            <div class="section-row">
                <div class="order-column"><span class="order-number">{{ sprintf('%02d',$index + 1) }}</span><span class="drag-handle" title="Drag to reorder" aria-label="Drag to reorder"><i class="fa-solid fa-grip-vertical"></i></span></div>
                <span class="section-icon"><i class="fa-solid {{ $icons[$section->key] ?? 'fa-layer-group' }}"></i></span>
                <div class="section-copy">
                    <div class="section-title"><h3>{{ $titles[$section->key] ?? $section->label }}</h3><span class="state-badge">{{ $section->is_enabled ? 'Visible' : 'Hidden' }}</span></div>
                    <p>{{ $section->description }}</p>
                    <div class="source-line"><span><i class="fa-solid fa-database"></i> {{ $sourceLabels[$section->key] ?? 'Dedicated module' }}</span></div>
                </div>
                <div class="row-actions">
                    <button type="button" class="control-toggle" aria-expanded="false" aria-controls="controls-{{ $section->key }}"><i class="fa-solid fa-sliders"></i><span>Controls</span></button>
                    @if(isset($manageRoutes[$section->key]))<a class="source-link" href="{{ $manageRoutes[$section->key] }}"><i class="fa-solid fa-arrow-up-right-from-square"></i><span>Open manager</span></a>@endif
                    <label class="visibility"><span>Show</span><input type="checkbox" name="sections[{{ $section->key }}]" value="1" @checked($section->is_enabled) aria-label="Show {{ $titles[$section->key] ?? $section->label }} on homepage"><i></i></label>
                </div>
            </div>

            <div class="section-controls" id="controls-{{ $section->key }}" hidden>
                <div class="controls-head"><div><span class="eyebrow">HOMEPAGE CONTROLS</span><strong>{{ $titles[$section->key] ?? $section->label }}</strong></div><span class="control-rule">Homepage display rules only.</span></div>

                @if($section->key === 'welcome')
                    <div class="field-grid two">
                        <label><span>Eyebrow</span><input name="settings[welcome][eyebrow]" maxlength="120" value="{{ $settings['eyebrow'] ?? 'Welcome to '.config('fuelfree.company.name') }}"></label>
                        <label><span>Sign-off line</span><input name="settings[welcome][signoff]" maxlength="240" value="{{ $settings['signoff'] ?? config('fuelfree.company.name').' — Powering a cleaner, smarter future.' }}"></label>
                        <label><span>Homepage title</span><input name="settings[welcome][title]" maxlength="240" value="{{ $settings['title'] ?? '' }}"></label>
                        <label><span>Text alignment</span><select name="settings[welcome][layout]"><option value="left" @selected($layout==='left')>Left</option><option value="center" @selected($layout==='center')>Center</option><option value="right" @selected($layout==='right')>Right</option></select></label>
                    </div>
                    <label class="full-field"><span>Complete welcome message</span><textarea name="settings[welcome][content]" rows="9" maxlength="30000">{{ $settings['content'] ?? '' }}</textarea></label>
                    <div class="field-grid three">
                        <label><span>Preview words</span><input type="number" name="settings[welcome][preview_words]" min="20" max="500" value="{{ $settings['preview_words'] ?? 180 }}"></label>
                        <label><span>More words</span><input type="number" name="settings[welcome][more_words]" min="20" max="2000" value="{{ $settings['more_words'] ?? 900 }}"></label>
                        <label class="check-field"><input type="checkbox" name="settings[welcome][show_full]" value="1" @checked($settings['show_full'] ?? false)><span>Show full message</span></label>
                    </div>
                @elseif($section->key === 'management')
                    <div class="field-grid two">
                        <label><span>Profile folder</span><select name="settings[management][folder_id]" class="management-folder" required><option value="">Select a published folder</option>@foreach($managementFolders as $folder)<option value="{{ $folder->id }}" @selected((int)($settings['folder_id'] ?? 0)===(int)$folder->id)>{{ $folder->name }} ({{ $folder->profiles->count() }} profiles)</option>@endforeach</select><small>Profiles are managed only in Profile Builder.</small></label>
                        <label><span>Section alignment</span><select name="settings[management][layout]"><option value="left" @selected($layout==='left')>Left</option><option value="center" @selected($layout==='center')>Center</option><option value="right" @selected($layout==='right')>Right</option></select></label>
                    </div>
                    <div class="selection-panel management-selection-panel" data-key="management" data-required="true">
                        <div class="picker-head"><strong>Select profiles to show</strong><span data-count>0 selected</span></div>
                        <input class="picker-search" type="search" placeholder="Search profiles..." aria-label="Search management profiles">
                        <div class="picker-list">@foreach($managementFolders as $folder)@foreach($folder->profiles as $profile)<label class="picker-item management-profile-choice" data-folder-id="{{ $folder->id }}" data-search="{{ strtolower($profile->title) }}"><input type="checkbox" name="settings[management][ids][]" value="{{ $profile->id }}" data-folder-profile @checked(in_array((int)$profile->id,array_map('intval',$settings['ids'] ?? []),true))><span>{{ $profile->title }}</span></label>@endforeach @endforeach</div>
                    </div>
                @elseif(in_array($section->key,['news','gallery'],true))
                    <div class="field-grid three">
                        <label><span>Items on homepage</span><input type="number" name="settings[{{ $section->key }}][limit]" min="1" max="100" value="{{ $limit }}"></label>
                        <label><span>Content selection</span><select name="settings[{{ $section->key }}][mode]" class="home-mode"><option value="latest" @selected($mode==='latest')>Latest published</option><option value="selected" @selected($mode==='selected')>Choose specific items</option></select></label>
                        <label><span>Section alignment</span><select name="settings[{{ $section->key }}][layout]"><option value="left" @selected($layout==='left')>Left</option><option value="center" @selected($layout==='center')>Center</option><option value="right" @selected($layout==='right')>Right</option></select></label>
                    </div>
                    <div class="selection-panel" data-key="{{ $section->key }}" hidden><div class="picker-head"><strong>Choose published items</strong><span data-count>0 selected</span></div><input class="picker-search" type="search" placeholder="Search published items..."><div class="picker-list">@foreach(($choices[$section->key] ?? []) as $choice)<label class="picker-item" data-search="{{ strtolower($choice->title ?? $choice->name) }}"><input type="checkbox" name="settings[{{ $section->key }}][ids][]" value="{{ $choice->id }}" @checked(in_array((int)$choice->id,array_map('intval',$settings['ids'] ?? []),true))><span>{{ $choice->title ?? $choice->name }}</span></label>@endforeach</div></div>
                @else
                    <div class="field-grid one"><label><span>Section alignment</span><select name="settings[{{ $section->key }}][layout]"><option value="left" @selected($layout==='left')>Left</option><option value="center" @selected($layout==='center')>Center</option><option value="right" @selected($layout==='right')>Right</option></select></label></div>
                @endif
            </div>
        </article>
    @endforeach
    </div>
    <div id="order-fields"></div>
</div>
</form>
@endsection

@push('styles')
<style>
.homepage-hero{display:flex;align-items:flex-end;justify-content:space-between;gap:24px;margin-bottom:22px}.homepage-hero h1{margin:8px 0;color:#eaf8fb;font-size:clamp(34px,4vw,50px)}.homepage-hero p{max-width:760px;color:#9eb6bc;margin:0;line-height:1.6}.hero-tools{display:flex;align-items:center;gap:12px;flex-wrap:wrap}.preview-button,.save-button,.source-link,.control-toggle{border:1px solid rgba(255,255,255,.12);border-radius:12px;padding:11px 14px;text-decoration:none;display:inline-flex;align-items:center;gap:8px}.preview-button,.save-button{background:#eaf8fb;color:#10262b;font-weight:700}.hero-stat{padding:10px 14px;border:1px solid rgba(255,255,255,.1);border-radius:12px;display:flex;gap:7px;align-items:baseline}.hero-stat strong{font-size:20px;color:#fff}.hero-stat span{color:#91a9af;font-size:12px}.flash{margin:0 0 18px;padding:13px 15px;border-radius:12px;display:flex;gap:10px}.flash-success{background:rgba(35,160,105,.12);color:#a8e4c9}.flash-error{background:rgba(220,80,80,.12);color:#ffc0c0}.builder{border:1px solid rgba(255,255,255,.09);border-radius:18px;background:rgba(7,20,24,.72);overflow:hidden}.builder-head{display:flex;justify-content:space-between;align-items:center;gap:20px;padding:24px;border-bottom:1px solid rgba(255,255,255,.08)}.builder-head h2{margin:7px 0;color:#fff}.builder-head p{margin:0;color:#8ea6ad}.section-list{padding:14px}.section-card{border:1px solid rgba(255,255,255,.08);border-radius:15px;margin-bottom:12px;background:rgba(255,255,255,.025)}.section-card.dragging{opacity:.55}.section-row{display:grid;grid-template-columns:58px 46px minmax(0,1fr) auto;gap:14px;align-items:center;padding:18px}.order-column{display:flex;align-items:center;gap:9px;color:#759098}.order-number{font-weight:800;color:#cce2e6}.drag-handle{cursor:grab}.section-icon{width:40px;height:40px;border-radius:12px;display:grid;place-items:center;background:rgba(102,210,221,.1);color:#9de9ef}.section-title{display:flex;gap:10px;align-items:center}.section-title h3{margin:0;color:#f3fbfc;font-size:16px}.section-copy p{margin:5px 0;color:#8fa8ae;font-size:13px}.state-badge{font-size:11px;padding:4px 8px;border-radius:999px;background:rgba(255,255,255,.07);color:#a7bdc2}.source-line{font-size:11px;color:#789197}.row-actions{display:flex;align-items:center;justify-content:flex-end;gap:8px;flex-wrap:wrap}.source-link,.control-toggle{background:transparent;color:#a9c1c6;cursor:pointer}.visibility{display:flex;align-items:center;gap:8px;color:#91a9af;font-size:12px}.visibility input{position:absolute;opacity:0}.visibility i{width:38px;height:22px;border-radius:20px;background:#31454a;position:relative;cursor:pointer}.visibility i:after{content:'';position:absolute;width:16px;height:16px;left:3px;top:3px;border-radius:50%;background:#d7e7e9;transition:.18s}.visibility input:checked+i{background:#5fbec8}.visibility input:checked+i:after{left:19px}.section-controls{padding:20px;border-top:1px solid rgba(255,255,255,.08);background:rgba(0,0,0,.12)}.controls-head{display:flex;justify-content:space-between;gap:15px;align-items:center;margin-bottom:18px}.controls-head strong{display:block;color:#fff;margin-top:4px}.control-rule{font-size:11px;color:#71898f}.field-grid{display:grid;gap:14px}.field-grid.two{grid-template-columns:repeat(2,minmax(0,1fr))}.field-grid.three{grid-template-columns:repeat(3,minmax(0,1fr))}.field-grid.one{grid-template-columns:minmax(0,360px)}label>span{display:block;font-size:12px;color:#9db3b8;margin-bottom:6px}input,select,textarea{width:100%;box-sizing:border-box;border:1px solid rgba(255,255,255,.1);border-radius:10px;background:#0b1b20;color:#eaf5f7;padding:10px 11px}textarea{resize:vertical}.full-field{display:block;margin-top:14px}.full-field textarea{min-height:160px}.check-field{display:flex;align-items:center;gap:9px;padding-top:24px;color:#a9bdc2}.check-field input{width:auto}.selection-panel{margin-top:14px;padding:15px;border:1px solid rgba(255,255,255,.08);border-radius:12px}.picker-head{display:flex;justify-content:space-between;gap:10px;align-items:center;margin-bottom:10px;color:#d7e7e9}.picker-head span{font-size:11px;color:#8da6ac}.picker-list{display:grid;grid-template-columns:repeat(2,minmax(0,1fr));gap:8px;margin-top:10px;max-height:240px;overflow:auto}.picker-item{display:flex;gap:8px;align-items:center;padding:9px;border-radius:9px;background:rgba(255,255,255,.035);color:#b7c9cd;font-size:12px}.picker-item input{width:auto}.picker-search{margin-top:4px}@media(max-width:900px){.homepage-hero,.builder-head{align-items:flex-start;flex-direction:column}.section-row{grid-template-columns:48px 40px minmax(0,1fr)}.row-actions{grid-column:3}.field-grid.two,.field-grid.three,.picker-list{grid-template-columns:1fr}}@media(max-width:600px){.section-row{grid-template-columns:42px 1fr}.section-icon{display:none}.row-actions{grid-column:1/-1;justify-content:flex-start}.section-copy{grid-column:2}.order-column{grid-row:1 / span 2}.builder-head{padding:18px}.section-list{padding:8px}.hero-tools{width:100%}.preview-button{flex:1;justify-content:center}}
</style>
@endpush

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded',()=>{
 const form=document.getElementById('home-builder'), list=document.getElementById('section-list'), order=document.getElementById('order-fields');
 const refresh=()=>{[...list.querySelectorAll('.section-card')].forEach((c,i)=>{const n=c.querySelector('.order-number');if(n)n.textContent=String(i+1).padStart(2,'0');});order.innerHTML='';[...list.querySelectorAll('.section-card')].forEach(c=>{const i=document.createElement('input');i.type='hidden';i.name='section_order[]';i.value=c.dataset.key;order.appendChild(i);});};
 let dragged=null;
 list.querySelectorAll('.section-card').forEach(card=>{card.addEventListener('dragstart',()=>{dragged=card;card.classList.add('dragging')});card.addEventListener('dragend',()=>{dragged=null;card.classList.remove('dragging');refresh()});card.addEventListener('dragover',e=>{e.preventDefault();if(dragged&&dragged!==card){const r=card.getBoundingClientRect();list.insertBefore(dragged,e.clientY<r.top+r.height/2?card:card.nextSibling)}})});
 document.querySelectorAll('.control-toggle').forEach(btn=>btn.addEventListener('click',()=>{const panel=document.getElementById(btn.getAttribute('aria-controls'));const open=btn.getAttribute('aria-expanded')==='true';btn.setAttribute('aria-expanded',String(!open));panel.hidden=open;}));
 document.querySelectorAll('.home-mode').forEach(select=>{const sync=()=>{const panel=select.closest('.section-controls')?.querySelector('.selection-panel');if(panel)panel.hidden=select.value!=='selected'};select.addEventListener('change',sync);sync()});
 const syncPickers=()=>document.querySelectorAll('.selection-panel').forEach(panel=>{const count=panel.querySelectorAll('input[type=checkbox]:checked').length;const out=panel.querySelector('[data-count]');if(out)out.textContent=count+' selected';});
 document.querySelectorAll('.picker-search').forEach(input=>input.addEventListener('input',()=>{const q=input.value.toLowerCase();input.closest('.selection-panel').querySelectorAll('.picker-item').forEach(item=>item.hidden=q&&!item.dataset.search.includes(q));}));
 document.querySelectorAll('.picker-list input').forEach(i=>i.addEventListener('change',syncPickers));
 const folder=document.querySelector('.management-folder');if(folder){const filter=()=>{const id=folder.value;document.querySelectorAll('.management-profile-choice').forEach(item=>{item.hidden=!!id&&item.dataset.folderId!==id;if(item.hidden){const box=item.querySelector('input');if(box)box.checked=false;}});syncPickers()};folder.addEventListener('change',filter);filter();}
 form.addEventListener('submit',()=>refresh());refresh();syncPickers();
});
</script>
@endpush
