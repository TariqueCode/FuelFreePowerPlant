@extends('layouts.portal')
@section('title','Homepage')
@section('content')
<section class="builder-hero">
    <div>
        <span class="eyebrow">WEBSITE / HOMEPAGE</span>
        <h1>Homepage</h1>
        <p>Build the visitor journey without moving the underlying content. Reorder sections, control visibility and open only the settings that belong to each section.</p>
    </div>
    <div class="hero-actions">
        <a href="{{ route('home') }}" target="_blank" rel="noopener"><i class="fa-solid fa-arrow-up-right-from-square"></i><span>Preview website</span></a>
        <span class="section-count"><strong>{{ $sections->count() }}</strong><small>sections</small></span>
    </div>
</section>

@if(session('status'))<div class="notice">{{ session('status') }}</div>@endif
@if($errors->any())<div class="errors">{{ $errors->first() }}</div>@endif

<form method="POST" action="{{ route('admin.homepage-builder.update') }}" id="home-builder">
@csrf
<div class="builder-shell">
    <header class="builder-header">
        <div><span class="eyebrow">PAGE COMPOSITION</span><strong>Homepage sections</strong><small>Drag on desktop or use the arrows on touch devices. Content is edited in its own module.</small></div>
        <button class="save-button" type="submit"><i class="fa-solid fa-floppy-disk"></i><span>Save homepage</span></button>
    </header>
    <div class="builder-tip"><i class="fa-solid fa-circle-info"></i><span><strong>How this works:</strong> enable a section to publish it, arrange its position, then open <b>Settings</b> for section-specific display rules. Use the linked content manager when you need to edit the actual source content.</span></div>

    <div id="section-list">
    @php
        $icons=['hero'=>'fa-images','welcome'=>'fa-building','statistics'=>'fa-chart-simple','projects'=>'fa-industry','management'=>'fa-users','news'=>'fa-newspaper','gallery'=>'fa-images','highlight'=>'fa-bullhorn','cta'=>'fa-paper-plane'];
        $countLabels=['hero'=>'sliders','projects'=>'projects','management'=>'management profiles','news'=>'published news & notices','gallery'=>'published galleries'];
        $manageRoutes=['projects'=>route('admin.plants.index'),'management'=>route('admin.management.index'),'news'=>route('admin.site-content.index',['type'=>'news']),'gallery'=>route('admin.gallery.index'),'hero'=>route('admin.sliders.index'),'highlight'=>route('admin.site-popups.index')];
    @endphp

    @foreach($sections as $section)
    @php
        $sectionSettings=is_array($section->settings)?$section->settings:[];
        $sectionLimit=(int)($sectionSettings['limit'] ?? match($section->key){'projects'=>6,'management'=>4,'news'=>3,'gallery'=>4,default=>0});
        $sectionMode=$sectionSettings['mode'] ?? 'latest';
    @endphp
    <article class="section-card" draggable="true" data-key="{{ $section->key }}" data-state="{{ $section->is_enabled?'visible':'hidden' }}">
        <div class="section-main">
            <div class="order-tools" aria-label="Reorder {{ $section->label }}">
                <button type="button" class="move-up" title="Move up" aria-label="Move {{ $section->label }} up"><i class="fa-solid fa-chevron-up"></i></button>
                <span class="drag-handle" title="Drag to reorder"><i class="fa-solid fa-grip-vertical"></i></span>
                <button type="button" class="move-down" title="Move down" aria-label="Move {{ $section->label }} down"><i class="fa-solid fa-chevron-down"></i></button>
            </div>
            <span class="section-icon"><i class="fa-solid {{ $icons[$section->key] ?? 'fa-layer-group' }}"></i></span>
            <div class="section-copy">
                <div class="section-title-line"><strong>{{ $section->label }}</strong><span class="state-badge">{{ $section->is_enabled?'Visible':'Hidden' }}</span></div>
                <small>{{ $section->description }}</small>
                @if(($countLabels[$section->key] ?? null) && isset($counts[$countLabels[$section->key]]))
                    <span class="source-count">{{ $counts[$countLabels[$section->key]] }} {{ $countLabels[$section->key] }}</span>
                @endif
            </div>
            <div class="section-actions">
                <button type="button" class="settings-toggle" aria-expanded="false"><i class="fa-solid fa-gear"></i><span>Settings</span></button>
                @if(isset($manageRoutes[$section->key]))
                    <a class="manage-link" href="{{ $manageRoutes[$section->key] }}" title="Open {{ $section->label }}"><i class="fa-solid fa-arrow-up-right-from-square"></i><span>Edit content</span></a>
                @endif
                <label class="switch" title="{{ $section->is_enabled?'Hide from homepage':'Show on homepage' }}"><input type="checkbox" name="sections[{{ $section->key }}]" value="1" @checked($section->is_enabled) aria-label="Show {{ $section->label }} on homepage"><span></span></label>
            </div>
        </div>

        <div class="section-settings" hidden>
        @if($section->key==='welcome')
            <div class="settings-heading"><strong>Welcome message</strong><small>Edit the homepage introduction here; no separate page is required.</small></div>
            <div class="field-grid two">
                <label><span>Eyebrow</span><input name="settings[welcome][eyebrow]" maxlength="120" value="{{ $sectionSettings['eyebrow'] ?? 'Welcome to '.config('fuelfree.company.name') }}"></label>
                <label><span>Homepage title</span><input name="settings[welcome][title]" maxlength="240" value="{{ $sectionSettings['title'] ?? '' }}"></label>
            </div>
            <label class="full-field"><span>Complete welcome message</span><textarea name="settings[welcome][content]" rows="9" maxlength="30000" placeholder="Write the complete company introduction here...">{{ $sectionSettings['content'] ?? '' }}</textarea></label>
            <div class="field-grid four">
                <label><span>Preview words</span><input type="number" name="settings[welcome][preview_words]" min="20" max="500" value="{{ $sectionSettings['preview_words'] ?? 180 }}"></label>
                <label><span>More words</span><input type="number" name="settings[welcome][more_words]" min="20" max="2000" value="{{ $sectionSettings['more_words'] ?? 900 }}"></label>
                <label><span>Text alignment</span><select name="settings[welcome][layout]"><option value="left" @selected(($sectionSettings['layout'] ?? 'left')==='left')>Left</option><option value="center" @selected(($sectionSettings['layout'] ?? 'left')==='center')>Center</option><option value="right" @selected(($sectionSettings['layout'] ?? 'left')==='right')>Right</option></select></label>
                <label class="check-field"><input type="checkbox" name="settings[welcome][show_full]" value="1" @checked($sectionSettings['show_full'] ?? false)><span>Show complete message</span></label>
            </div>
            <div class="settings-note"><i class="fa-solid fa-wand-magic-sparkles"></i><span>When full message is off, visitors see the configured preview first and can reveal the configured “More words”.</span></div>
        @elseif(in_array($section->key,['projects','management','news','gallery'],true))
            <div class="settings-heading"><strong>{{ $section->label }} display</strong><small>Choose how much content appears on the homepage without editing the source records.</small></div>
            <div class="display-grid">
                <label><span>Show items</span><div class="number-field"><input type="number" name="settings[{{ $section->key }}][limit]" min="1" max="100" value="{{ $sectionLimit }}"><em>items</em></div></label>
                <label><span>Source</span><select name="settings[{{ $section->key }}][mode]" class="home-mode"><option value="latest" @selected($sectionMode==='latest')>Latest published</option><option value="selected" @selected($sectionMode==='selected')>Selected items</option></select></label>
                <label><span>Text alignment</span><select name="settings[{{ $section->key }}][layout]"><option value="left" @selected(($sectionSettings['layout'] ?? 'left')==='left')>Left</option><option value="center" @selected(($sectionSettings['layout'] ?? 'left')==='center')>Center</option><option value="right" @selected(($sectionSettings['layout'] ?? 'left')==='right')>Right</option></select></label>
            </div>
            <div class="selection-panel" data-key="{{ $section->key }}" hidden>
                <div class="picker-head"><strong>Choose {{ $section->label }}</strong><span data-count>0 selected</span></div>
                <input class="picker-search" type="search" placeholder="Search items..." aria-label="Search {{ $section->label }} items">
                <div class="picker-list">
                @foreach(($choices[$section->key] ?? []) as $choice)
                    @php($choiceId=(int)$choice->id)
                    <label class="picker-item" data-search="{{ strtolower($choice->title ?? $choice->name) }}"><input type="checkbox" name="settings[{{ $section->key }}][ids][]" value="{{ $choiceId }}" @checked(in_array($choiceId,$sectionSettings['ids'] ?? [],true))><span>{{ $choice->title ?? $choice->name }}</span></label>
                @endforeach
                </div>
                <small>Selected items are shown in the order chosen here and capped by “Show items”.</small>
            </div>
        @else
            <div class="settings-heading"><strong>Display settings</strong><small>This section is controlled by its own content module. Use the controls below only for homepage visibility and alignment.</small></div>
            <label class="alignment-field"><span>Text alignment</span><select name="settings[{{ $section->key }}][layout]"><option value="left" @selected(($sectionSettings['layout'] ?? 'left')==='left')>Left</option><option value="center" @selected(($sectionSettings['layout'] ?? 'left')==='center')>Center</option><option value="right" @selected(($sectionSettings['layout'] ?? 'left')==='right')>Right</option></select></label>
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
.builder-hero{display:flex;align-items:flex-end;justify-content:space-between;gap:24px;margin-bottom:20px}.builder-hero h1{margin:6px 0 7px;color:#eaf8fb;font-size:clamp(30px,3.2vw,44px);letter-spacing:-.04em}.builder-hero p{max-width:760px;margin:0;color:#7899a5;font-size:11px;line-height:1.65}.hero-actions{display:flex;align-items:center;gap:10px}.hero-actions>a,.section-count{display:inline-flex;align-items:center;gap:8px;min-height:38px;padding:0 12px;border:1px solid rgba(76,205,233,.12);border-radius:11px;background:rgba(72,216,241,.025);color:#9bc4ce;text-decoration:none;font-size:9px}.hero-actions>a:hover{border-color:rgba(76,205,233,.25);color:#dff5f8}.section-count{gap:5px;min-width:74px;justify-content:center}.section-count strong{font-size:12px;color:#dff5f8}.section-count small{font-size:8px;color:#6f8f99}.builder-shell{border:1px solid var(--line);border-radius:19px;background:linear-gradient(145deg,rgba(8,38,52,.76),rgba(3,20,29,.9));overflow:hidden}.builder-header{display:flex;align-items:center;justify-content:space-between;gap:16px;padding:17px 19px;border-bottom:1px solid rgba(76,205,233,.08)}.builder-header strong{display:block;margin-top:4px;color:#e6f7fa;font-size:13px}.builder-header small{display:block;margin-top:4px;color:#698893;font-size:8px;line-height:1.45}.save-button{display:inline-flex;align-items:center;gap:8px;min-height:38px;padding:0 14px;border:0;border-radius:10px;background:linear-gradient(135deg,#25abc9,#1687a4);color:#fff;font-size:9px;font-weight:800;cursor:pointer;white-space:nowrap}.builder-tip{display:flex;gap:9px;align-items:flex-start;padding:11px 19px;border-bottom:1px solid rgba(76,205,233,.07);background:rgba(72,216,241,.018);color:#718f9a;font-size:8px;line-height:1.55}.builder-tip i{color:#58cfe9;margin-top:1px}.builder-tip strong,.builder-tip b{color:#a9d7df}.section-card{margin:10px 12px;border:1px solid rgba(76,205,233,.1);border-radius:15px;background:rgba(2,15,23,.62);overflow:visible;transition:border-color .2s,transform .2s,background .2s}.section-card:hover{border-color:rgba(76,205,233,.2);background:rgba(5,25,36,.78)}.section-card.dragging{opacity:.45}.section-card.drop-target{outline:1px dashed rgba(86,210,238,.8)}.section-main{display:grid;grid-template-columns:42px 44px minmax(0,1fr) auto;align-items:center;gap:12px;padding:12px 13px}.order-tools{display:grid;grid-template-columns:1fr;justify-items:center;gap:2px}.order-tools button{display:none;width:24px;height:20px;padding:0;border:1px solid rgba(104,204,235,.12);border-radius:5px;background:#071b25;color:#7fa5af;font-size:7px;cursor:pointer}.drag-handle{display:grid;place-items:center;width:28px;height:34px;border-radius:8px;color:#598592;cursor:grab}.drag-handle:hover{background:rgba(67,194,229,.06);color:#8adbea}.section-icon{width:44px;height:44px;display:grid;place-items:center;border-radius:12px;color:#5fd4ed;background:rgba(72,216,241,.07);font-size:15px}.section-copy{min-width:0}.section-title-line{display:flex;align-items:center;gap:8px;min-width:0}.section-copy strong{color:#dff5f8;font-size:11px;line-height:1.3}.section-copy>small{display:block;max-width:720px;margin-top:4px;color:#6d8c97;font-size:8px;line-height:1.45;white-space:nowrap;overflow:hidden;text-overflow:ellipsis}.source-count{display:inline-block;margin-top:5px;color:#5ec8dc;font-size:7px}.state-badge{padding:3px 6px;border-radius:999px;background:rgba(79,210,190,.08);color:#6fe0c0;font-size:6px;font-weight:800;text-transform:uppercase;letter-spacing:.08em}.section-card[data-state="hidden"] .state-badge{background:rgba(139,161,170,.08);color:#78909a}.section-actions{display:flex;align-items:center;justify-content:flex-end;gap:7px}.settings-toggle,.manage-link{display:inline-flex;align-items:center;justify-content:center;gap:6px;min-height:31px;padding:0 9px;border:1px solid rgba(104,204,235,.11);border-radius:8px;background:transparent;color:#71cfe3;font-size:8px;text-decoration:none;cursor:pointer;white-space:nowrap}.settings-toggle:hover,.manage-link:hover,.settings-toggle[aria-expanded="true"]{background:rgba(72,216,241,.06);border-color:rgba(104,204,235,.22);color:#dff7fa}.manage-link{color:#8daeb7}.switch{position:relative;width:39px;height:22px;display:block;flex:none}.switch input{position:absolute;opacity:0;width:1px;height:1px}.switch span{position:absolute;inset:0;border:1px solid rgba(104,204,235,.15);border-radius:999px;background:#203943;cursor:pointer}.switch span:after{content:"";position:absolute;top:3px;left:3px;width:14px;height:14px;border-radius:50%;background:#8fa8ae;transition:.2s}.switch input:checked+span{background:rgba(49,185,133,.5);border-color:rgba(89,226,187,.45)}.switch input:checked+span:after{left:20px;background:#dffff7}.section-settings{padding:14px 15px 15px;border-top:1px solid rgba(76,205,233,.08);background:rgba(72,216,241,.018)}.settings-heading{margin-bottom:12px}.settings-heading strong{display:block;color:#dff5f8;font-size:10px}.settings-heading small{display:block;margin-top:3px;color:#678692;font-size:8px;line-height:1.45}.field-grid{display:grid;gap:10px}.field-grid.two{grid-template-columns:1fr 1fr}.field-grid.four{grid-template-columns:repeat(4,minmax(0,1fr));margin-top:10px}.field-grid label,.full-field,.display-grid label,.alignment-field{display:grid;gap:6px}.field-grid label>span,.full-field>span,.display-grid label>span,.alignment-field>span{font-size:8px;color:#8eaab3;font-weight:700}.section-settings input:not([type=checkbox]),.section-settings textarea,.section-settings select{width:100%;box-sizing:border-box;border:1px solid rgba(104,204,235,.13);border-radius:9px;background:#071b25;color:#e4f5f8;padding:9px;font:inherit;font-size:9px;outline:none}.section-settings textarea{min-height:135px;resize:vertical;line-height:1.55}.section-settings input:focus,.section-settings textarea:focus,.section-settings select:focus{border-color:rgba(81,216,240,.38);box-shadow:0 0 0 3px rgba(81,216,240,.05)}.check-field{display:flex!important;align-items:center;gap:8px;padding:19px 9px 0;border:1px solid rgba(104,204,235,.09);border-radius:9px;background:rgba(72,216,241,.015);color:#8eaab3}.check-field input{width:auto!important}.display-grid{display:grid;grid-template-columns:1fr 1fr 1fr;gap:10px}.number-field{position:relative}.number-field input{padding-right:42px!important}.number-field em{position:absolute;right:9px;top:50%;transform:translateY(-50%);font-style:normal;color:#648591;font-size:7px}.selection-panel{margin-top:11px;padding:11px;border:1px solid rgba(104,204,235,.11);border-radius:11px;background:#061923}.picker-head{display:flex;align-items:center;justify-content:space-between;gap:10px;margin-bottom:7px}.picker-head strong{color:#dff5f8;font-size:9px}.picker-head span{color:#5fcde3;font-size:8px}.picker-search{margin-bottom:7px}.picker-list{display:grid;gap:3px;max-height:210px;overflow:auto}.picker-item{display:flex!important;grid-template-columns:none!important;align-items:center;gap:7px;padding:7px 8px;border-radius:7px;background:rgba(72,216,241,.02);color:#a9c3cb!important;font-size:8px!important}.picker-item input{width:auto!important}.selection-panel>small,.settings-note{display:flex;gap:7px;margin-top:8px;color:#678692;font-size:7px;line-height:1.5}.settings-note{padding:9px 10px;border:1px solid rgba(72,216,241,.07);border-radius:9px;background:rgba(72,216,241,.018)}.settings-note i{color:#58cfe9}.alignment-field{max-width:240px}
@media(max-width:900px){.section-main{grid-template-columns:36px 40px minmax(0,1fr) auto}.section-icon{width:40px;height:40px}.section-copy>small{max-width:460px}.manage-link span{display:none}}
@media(max-width:650px){.builder-hero{display:block;margin-bottom:15px}.builder-hero h1{font-size:30px}.builder-hero p{font-size:9px;line-height:1.6}.hero-actions{margin-top:11px}.hero-actions>a{flex:1}.section-count{min-width:70px}.builder-header{align-items:flex-start;padding:14px}.builder-header small{max-width:220px}.save-button{min-height:36px;padding:0 11px}.save-button span{display:none}.builder-tip{padding:10px 14px;font-size:7px}.section-card{margin:8px 8px;border-radius:14px}.section-main{grid-template-columns:30px 40px minmax(0,1fr);gap:9px;padding:11px}.order-tools{grid-template-columns:1fr;gap:2px}.order-tools button{display:grid;place-items:center}.drag-handle{display:none}.section-icon{width:40px;height:40px}.section-title-line{gap:6px;flex-wrap:wrap}.section-copy strong{font-size:10px}.section-copy>small{font-size:7px;white-space:normal;display:-webkit-box;-webkit-line-clamp:2;-webkit-box-orient:vertical}.source-count{font-size:6px}.section-actions{grid-column:1/-1;display:grid;grid-template-columns:minmax(0,1fr) minmax(0,1fr) 39px;gap:6px;padding-top:8px;border-top:1px solid rgba(76,205,233,.06)}.settings-toggle,.manage-link{min-height:34px;font-size:8px}.manage-link span{display:inline}.section-settings{padding:12px}.field-grid.two,.field-grid.four,.display-grid{grid-template-columns:1fr}.check-field{padding:10px}.alignment-field{max-width:none}.selection-panel{position:static;width:auto}.picker-list{max-height:180px}}
@media(max-width:380px){.builder-hero h1{font-size:28px}.hero-actions>a{font-size:8px}.section-card{margin-inline:6px}.section-main{grid-template-columns:28px 36px minmax(0,1fr);gap:7px;padding:9px}.section-icon{width:36px;height:36px;border-radius:10px;font-size:13px}.section-copy strong{font-size:9px}.section-copy>small{font-size:6.5px}.settings-toggle,.manage-link{padding:0 6px}.section-actions{grid-template-columns:minmax(0,1fr) minmax(0,1fr) 37px}}
</style>
@endpush

@push('scripts')
<script>
(()=>{const form=document.getElementById('home-builder'),list=document.getElementById('section-list'),fields=document.getElementById('order-fields');if(!form||!list||!fields)return;
const rows=()=>[...list.querySelectorAll('.section-card')];
const sync=()=>{fields.innerHTML=rows().map(x=>'<input type="hidden" name="section_order[]" value="'+x.dataset.key+'">').join('');};
const refreshMoveButtons=()=>{const all=rows();all.forEach((row,i)=>{row.querySelector('.move-up').disabled=i===0;row.querySelector('.move-down').disabled=i===all.length-1;});};
rows().forEach(row=>{
 row.querySelector('.settings-toggle')?.addEventListener('click',()=>{const button=row.querySelector('.settings-toggle'),panel=row.querySelector('.section-settings'),open=button.getAttribute('aria-expanded')==='true';if(!panel)return;panel.hidden=open;button.setAttribute('aria-expanded',String(!open));});
 row.querySelector('.switch input')?.addEventListener('change',e=>{row.dataset.state=e.target.checked?'visible':'hidden';row.querySelector('.state-badge').textContent=e.target.checked?'Visible':'Hidden';});
 row.querySelector('.move-up')?.addEventListener('click',()=>{const all=rows(),i=all.indexOf(row);if(i>0){list.insertBefore(row,all[i-1]);sync();refreshMoveButtons();}});
 row.querySelector('.move-down')?.addEventListener('click',()=>{const all=rows(),i=all.indexOf(row);if(i>=0&&i<all.length-1){list.insertBefore(all[i+1],row);sync();refreshMoveButtons();}});
});
let drag=null;rows().forEach(row=>{row.addEventListener('dragstart',()=>{drag=row;row.classList.add('dragging')});row.addEventListener('dragover',e=>{if(!drag||drag===row)return;e.preventDefault();row.classList.add('drop-target')});row.addEventListener('dragleave',()=>row.classList.remove('drop-target'));row.addEventListener('drop',e=>{e.preventDefault();if(!drag||drag===row)return;const rect=row.getBoundingClientRect();list.insertBefore(drag,e.clientY<rect.top+rect.height/2?row:row.nextSibling);row.classList.remove('drop-target');sync();refreshMoveButtons()});row.addEventListener('dragend',()=>{row.classList.remove('dragging');rows().forEach(x=>x.classList.remove('drop-target'));drag=null;sync();refreshMoveButtons()});});
document.querySelectorAll('.home-mode').forEach(select=>{const settings=select.closest('.section-settings'),panel=settings?.querySelector('.selection-panel'),limit=settings?.querySelector('.number-field input');const boxes=()=>panel?[...panel.querySelectorAll('input[type=checkbox]')]:[];const refresh=()=>{if(!panel)return;const selected=boxes().filter(x=>x.checked);panel.querySelector('[data-count]').textContent=selected.length+' selected';const max=Math.max(1,Math.min(100,parseInt(limit?.value||'100',10)));boxes().forEach(x=>x.disabled=!x.checked&&selected.length>=max)};const syncMode=()=>{if(panel)panel.hidden=select.value!=='selected';refresh()};select.addEventListener('change',syncMode);limit?.addEventListener('input',refresh);panel?.querySelector('.picker-search')?.addEventListener('input',e=>{const q=e.target.value.toLowerCase().trim();panel.querySelectorAll('.picker-item').forEach(item=>item.hidden=!!q&&!item.dataset.search.includes(q))});panel?.addEventListener('change',e=>{if(e.target.matches('input[type=checkbox]'))refresh()});syncMode()});
form.addEventListener('submit',sync);sync();refreshMoveButtons();
})();
</script>
@endpush
