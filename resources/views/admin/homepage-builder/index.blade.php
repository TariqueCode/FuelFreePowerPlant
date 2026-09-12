@extends('layouts.portal')
@section('title','Page Builder')
@section('content')
@php
    $icons = ['hero'=>'fa-images','welcome'=>'fa-building','management'=>'fa-users','news'=>'fa-newspaper','gallery'=>'fa-images','highlight'=>'fa-bullhorn','cta'=>'fa-paper-plane'];
    $titles = ['hero'=>'Hero Slider','welcome'=>'Welcome Message','management'=>'Board of Directors','news'=>'News & Notices','gallery'=>'Gallery','highlight'=>'Homepage Highlight','cta'=>'Contact & Call to Action'];
    $sourceLabels = ['hero'=>'Slider Manager','welcome'=>'Homepage Content','management'=>'Board of Directors','news'=>'News & Notices','gallery'=>'Gallery Manager','highlight'=>'Highlight Manager','cta'=>'Homepage System'];
    $manageRoutes = ['hero'=>route('admin.sliders.index'),'management'=>route('admin.management.index'),'news'=>route('admin.site-content.index',['type'=>'news']),'gallery'=>route('admin.gallery.index'),'highlight'=>route('admin.site-popups.index')];
    $controlSections = ['hero','welcome','management','news','gallery','highlight','cta'];
    $countLabels = ['hero'=>['key'=>'sliders','suffix'=>'published slides'],'management'=>['key'=>'management','suffix'=>'published profiles'],'news'=>['key'=>'news','suffix'=>'published items'],'gallery'=>['key'=>'gallery','suffix'=>'published galleries']];
    $visibleCount = $sections->where('is_enabled',true)->count();
@endphp

<section class="pb-hero">
    <div class="pb-hero-copy">
        <span class="pb-eyebrow"><i class="fa-solid fa-layer-group"></i> Website · Page Builder</span>
        <h1>Pages</h1>
        <p>Build structured responsive pages while inheriting the live global header, theme, navigation and footer.</p>
    </div>
    <div class="pb-hero-actions">
        <a class="pb-preview" href="{{ route('home') }}" target="_blank" rel="noopener"><i class="fa-solid fa-arrow-up-right-from-square"></i><span>Preview website</span></a>
        <div class="pb-stat"><strong>{{ $visibleCount }}</strong><span>visible sections</span></div>
    </div>
</section>

@if(session('status'))<div class="pb-flash pb-flash-success"><i class="fa-solid fa-circle-check"></i><span>{{ session('status') }}</span></div>@endif
@if($errors->any())<div class="pb-flash pb-flash-error"><i class="fa-solid fa-triangle-exclamation"></i><span>{{ $errors->first() }}</span></div>@endif

<form method="POST" action="{{ route('admin.homepage-builder.update') }}" id="home-builder">
@csrf
<div class="pb-builder">
    <div class="pb-builder-head">
        <div>
            <span class="pb-eyebrow">GLOBAL FRAMEWORK</span>
            <h2>Homepage sections</h2>
            <p>Every Page Builder section uses the site's live navigation, branding, typography, theme and footer.</p>
        </div>
        <button class="pb-save" type="submit"><i class="fa-solid fa-floppy-disk"></i><span>Save changes</span></button>
    </div>

    <div class="pb-framework"><span class="pb-framework-icon"><i class="fa-solid fa-globe"></i></span><div><strong>Global framework is active</strong><small>Arrange, show/hide and configure each homepage section without duplicating source content.</small></div><b>HEADER · THEME · FOOTER</b></div>

    <div class="pb-list" id="section-list">
    @foreach($sections as $index => $section)
        @php
            $settings = is_array($section->settings) ? $section->settings : [];
            $limit = (int)($settings['limit'] ?? match($section->key){'management'=>4,'news'=>3,'gallery'=>4,default=>0});
            $mode = $settings['mode'] ?? 'latest';
            $layout = $settings['layout'] ?? 'left';
            $hasControls = in_array($section->key,$controlSections,true);
            $countMeta = $countLabels[$section->key] ?? null;
        @endphp
        <article class="pb-card" draggable="true" data-key="{{ $section->key }}" data-state="{{ $section->is_enabled ? 'visible' : 'hidden' }}">
            <div class="pb-card-main">
                <div class="pb-drag-rail">
                    <span class="pb-order">{{ sprintf('%02d',$index + 1) }}</span>
                    <span class="pb-grip" title="Drag to reorder" aria-label="Drag to reorder"><i class="fa-solid fa-grip-vertical"></i></span>
                </div>
                <div class="pb-icon"><i class="fa-solid {{ $icons[$section->key] ?? 'fa-layer-group' }}"></i></div>
                <div class="pb-copy">
                    <div class="pb-title-line"><h3>{{ $titles[$section->key] ?? $section->label }}</h3><span class="pb-live">{{ $section->is_enabled ? 'LIVE' : 'HIDDEN' }}</span></div>
                    <p>{{ $section->description }}</p>
                    <div class="pb-meta">
                        <span><i class="fa-solid fa-database"></i>{{ $sourceLabels[$section->key] ?? 'Dedicated module' }}</span>
                        @if($countMeta && isset($counts[$countMeta['key']]))<span><i class="fa-solid fa-circle"></i>{{ $counts[$countMeta['key']] }} {{ $countMeta['suffix'] }}</span>@endif
                    </div>
                </div>
                <div class="pb-card-actions">
                    @if($hasControls)<button type="button" class="pb-action pb-control" aria-expanded="false" aria-controls="controls-{{ $section->key }}"><i class="fa-solid fa-sliders"></i><span>Controls</span></button>@endif
                    @if(isset($manageRoutes[$section->key]))<a class="pb-action pb-source" href="{{ $manageRoutes[$section->key] }}" title="Open {{ $sourceLabels[$section->key] }}"><i class="fa-solid fa-arrow-up-right-from-square"></i><span>Open manager</span></a>@endif
                    <label class="pb-toggle" title="{{ $section->is_enabled ? 'Hide from homepage' : 'Show on homepage' }}"><input type="checkbox" name="sections[{{ $section->key }}]" value="1" @checked($section->is_enabled) aria-label="Show {{ $titles[$section->key] ?? $section->label }} on homepage"><i></i></label>
                </div>
            </div>

            @if($hasControls)
            <div class="pb-controls" id="controls-{{ $section->key }}" hidden>
                <div class="pb-controls-head"><div><span class="pb-eyebrow">HOMEPAGE CONTROLS</span><strong>{{ $titles[$section->key] ?? $section->label }}</strong></div><span>Only homepage-specific settings live here.</span></div>
                @if($section->key==='welcome')
                    <div class="pb-editor">
                        <div class="pb-editor-intro"><span><i class="fa-solid fa-pen-to-square"></i></span><div><strong>Welcome message</strong><p>Edit the homepage introduction and decide how much visitors see before “Read more”.</p></div></div>
                        <div class="pb-grid pb-grid-2">
                            <label><span>Eyebrow</span><input name="settings[welcome][eyebrow]" maxlength="120" value="{{ $settings['eyebrow'] ?? 'Welcome to '.config('fuelfree.company.name') }}"></label>
                            <label><span>Sign-off line</span><input name="settings[welcome][signoff]" maxlength="240" value="{{ $settings['signoff'] ?? config('fuelfree.company.name').' — Powering a cleaner, smarter future.' }}"><small>Shown below the welcome message</small></label>
                            <label><span>Homepage title</span><input name="settings[welcome][title]" maxlength="240" value="{{ $settings['title'] ?? '' }}"></label>
                        </div>
                        <label class="pb-full"><span>Complete welcome message</span><textarea name="settings[welcome][content]" rows="10" maxlength="30000" placeholder="Write the complete company introduction here...">{{ $settings['content'] ?? '' }}</textarea></label>
                        <div class="pb-grid pb-grid-4">
                            <label><span>Preview words</span><input type="number" name="settings[welcome][preview_words]" min="20" max="500" value="{{ $settings['preview_words'] ?? 180 }}"><small>Shown first on the homepage</small></label>
                            <label><span>More words</span><input type="number" name="settings[welcome][more_words]" min="20" max="2000" value="{{ $settings['more_words'] ?? 900 }}"><small>Revealed after Read more</small></label>
                            <label><span>Text alignment</span><select name="settings[welcome][layout]"><option value="left" @selected($layout==='left')>Left</option><option value="center" @selected($layout==='center')>Center</option><option value="right" @selected($layout==='right')>Right</option></select></label>
                            <label class="pb-check"><input type="checkbox" name="settings[welcome][show_full]" value="1" @checked($settings['show_full'] ?? false)><span>Show full message</span></label>
                        </div>
                    </div>
                @elseif($section->key==='management')
                    <div class="pb-management">
                        <div class="pb-grid pb-grid-2">
                            <label><span>Profile folder</span><select name="settings[management][folder_id]" class="management-folder"><option value="">Select a profile folder</option>@foreach($managementFolders as $folder)<option value="{{ $folder->id }}" @selected((int)($settings['folder_id'] ?? 0)===(int)$folder->id)>{{ $folder->name }} ({{ $folder->profiles->count() }} profiles)</option>@endforeach</select><small>Folders are managed in Profile Builder.</small></label>
                            <label><span>Section alignment</span><select name="settings[management][layout]"><option value="left" @selected($layout==='left')>Left</option><option value="center" @selected($layout==='center')>Center</option><option value="right" @selected($layout==='right')>Right</option></select></label>
                        </div>
                        <div class="pb-picker management-selection-panel" data-key="management" data-required="true">
                            <div class="pb-picker-head"><div><strong>Select profiles to show</strong><small>Choose one or more profiles from the selected folder.</small></div><span data-count>0 selected</span></div>
                            <input class="pb-search" type="search" placeholder="Search profiles..." aria-label="Search management profiles">
                            <div class="pb-picker-list">@foreach($managementFolders as $folder)@foreach($folder->profiles as $profile)@php($profileId=(int)$profile->id)<label class="pb-picker-item management-profile-choice" data-folder-id="{{ $folder->id }}" data-search="{{ strtolower($profile->title) }}"><input type="checkbox" name="settings[management][ids][]" value="{{ $profileId }}" data-folder-profile @checked(in_array($profileId,$settings['ids'] ?? [],true))><span>{{ $profile->title }}</span></label>@endforeach @endforeach</div>
                        </div>
                    </div>
                @elseif(in_array($section->key,['news','gallery'],true))
                    <div class="pb-grid pb-grid-3">
                        <label><span>Items on homepage</span><div class="pb-number"><input type="number" name="settings[{{ $section->key }}][limit]" min="1" max="100" value="{{ $limit }}"><em>items</em></div></label>
                        <label><span>Content selection</span><select name="settings[{{ $section->key }}][mode]" class="home-mode"><option value="latest" @selected($mode==='latest')>Latest published</option><option value="selected" @selected($mode==='selected')>Choose specific items</option></select></label>
                        <label><span>Section alignment</span><select name="settings[{{ $section->key }}][layout]"><option value="left" @selected($layout==='left')>Left</option><option value="center" @selected($layout==='center')>Center</option><option value="right" @selected($layout==='right')>Right</option></select></label>
                    </div>
                    <div class="pb-picker" data-key="{{ $section->key }}" hidden>
                        <div class="pb-picker-head"><div><strong>Choose items to feature</strong><small>These records already exist in the source module.</small></div><span data-count>0 selected</span></div>
                        <input class="pb-search" type="search" placeholder="Search published items..." aria-label="Search {{ $titles[$section->key] }} items">
                        <div class="pb-picker-list">@foreach(($choices[$section->key] ?? []) as $choice)@php($choiceId=(int)$choice->id)<label class="pb-picker-item" data-search="{{ strtolower($choice->title ?? $choice->name) }}"><input type="checkbox" name="settings[{{ $section->key }}][ids][]" value="{{ $choiceId }}" @checked(in_array($choiceId,$settings['ids'] ?? [],true))><span>{{ $choice->title ?? $choice->name }}</span></label>@endforeach</div>
                    </div>
                @elseif(in_array($section->key,['hero','highlight'],true))
                    <div class="pb-simple"><div><strong>Section display</strong><p>Use the shared homepage layout system. This setting controls placement without duplicating source content.</p></div><label><span>Section alignment</span><select name="settings[{{ $section->key }}][layout]"><option value="left" @selected($layout==='left')>Left</option><option value="center" @selected($layout==='center')>Center</option><option value="right" @selected($layout==='right')>Right</option></select></label></div>
                @elseif($section->key==='cta')
                    <div class="pb-simple"><div><strong>Call-to-action display</strong><p>This controls placement only. The CTA content remains managed by its source.</p></div><label><span>Section alignment</span><select name="settings[cta][layout]"><option value="left" @selected($layout==='left')>Left</option><option value="center" @selected($layout==='center')>Center</option><option value="right" @selected($layout==='right')>Right</option></select></label></div>
                @endif
            </div>
            @endif
        </article>
    @endforeach
    </div>
    <div id="order-fields"></div>
</div>
</form>
@endsection

@push('styles')
<style>
.pb-hero{display:flex;align-items:flex-end;justify-content:space-between;gap:28px;margin-bottom:20px}.pb-hero-copy{min-width:0}.pb-eyebrow{display:inline-flex;align-items:center;gap:6px;color:#58d5ee;font-size:8px;font-weight:800;letter-spacing:.16em;text-transform:uppercase}.pb-hero h1{margin:8px 0 7px;color:#e9f8fb;font-size:clamp(34px,3.5vw,49px);line-height:1;letter-spacing:-.045em}.pb-hero p{margin:0;max-width:760px;color:#7895a0;font-size:10px;line-height:1.65}.pb-hero-actions{display:flex;align-items:center;gap:8px;flex:0 0 auto}.pb-preview,.pb-stat{display:inline-flex;align-items:center;justify-content:center;gap:8px;min-height:40px;padding:0 13px;border:1px solid rgba(91,213,237,.14);border-radius:10px;background:rgba(72,216,241,.035);color:#a7cbd3;text-decoration:none;font-size:8px;font-weight:750}.pb-preview:hover{border-color:rgba(91,213,237,.32);background:rgba(72,216,241,.07);color:#effcff}.pb-preview i{color:#5dd8ef}.pb-stat{min-width:105px;gap:5px}.pb-stat strong{color:#e8f9fb;font-size:13px}.pb-stat span{color:#6f8d98;font-size:7px}.pb-flash{display:flex;align-items:center;gap:8px;margin-bottom:13px;padding:10px 12px;border-radius:9px;font-size:8px}.pb-flash-success{border:1px solid rgba(74,218,178,.17);background:rgba(74,218,178,.045);color:#a1e8d3}.pb-flash-error{border:1px solid rgba(244,122,122,.18);background:rgba(244,122,122,.045);color:#f0aaaa}.pb-builder{overflow:hidden;border:1px solid var(--line);border-radius:18px;background:linear-gradient(145deg,rgba(7,35,48,.78),rgba(2,18,27,.95));box-shadow:0 20px 55px rgba(0,0,0,.08)}.pb-builder-head{display:flex;align-items:center;justify-content:space-between;gap:20px;padding:19px 20px;border-bottom:1px solid rgba(91,213,237,.08)}.pb-builder-head h2{margin:5px 0 4px;color:#e8f8fb;font-size:17px;letter-spacing:-.02em}.pb-builder-head p{margin:0;color:#6f8e99;font-size:8px;line-height:1.55}.pb-save{display:inline-flex;align-items:center;gap:8px;min-height:39px;padding:0 14px;border:0;border-radius:9px;background:linear-gradient(135deg,#25abc9,#15839e);color:#fff;font-size:8px;font-weight:800;cursor:pointer;white-space:nowrap;box-shadow:0 8px 22px rgba(25,167,198,.13)}.pb-save:hover{filter:brightness(1.07);transform:translateY(-1px)}.pb-framework{display:flex;align-items:center;gap:10px;margin:11px 12px 7px;padding:10px 12px;border:1px solid rgba(91,213,237,.1);border-radius:11px;background:rgba(72,216,241,.025)}.pb-framework-icon{width:30px;height:30px;display:grid;place-items:center;border-radius:8px;background:rgba(72,216,241,.07);color:#5ed6ed}.pb-framework div{min-width:0;display:grid;gap:2px}.pb-framework strong{color:#b9dce3;font-size:8px}.pb-framework small{color:#5f7d88;font-size:6.5px}.pb-framework>b{margin-left:auto;color:#5fd9bd;font-size:6px;letter-spacing:.12em;white-space:nowrap}.pb-list{padding:0 10px 11px}.pb-card{margin:7px 0;border:1px solid rgba(91,213,237,.1);border-radius:13px;background:rgba(2,15,23,.67);overflow:hidden;transition:border-color .2s,background .2s,box-shadow .2s,transform .2s}.pb-card:hover{border-color:rgba(91,213,237,.22);background:rgba(5,25,36,.8);box-shadow:0 10px 28px rgba(0,0,0,.08)}.pb-card.dragging{opacity:.45;box-shadow:0 0 0 1px rgba(91,213,237,.4)}.pb-card.drop-target{outline:1px dashed rgba(91,213,237,.85);outline-offset:2px}.pb-card-main{display:grid;grid-template-columns:48px 52px minmax(0,1fr) auto;align-items:center;gap:12px;min-height:104px;padding:10px 12px}.pb-drag-rail{height:76px;display:grid;grid-template-rows:25px 1fr;place-items:center}.pb-order{color:#4e7480;font-size:7px;font-weight:850;letter-spacing:.12em}.pb-grip{display:grid;place-items:center;width:31px;height:31px;border-radius:8px;color:#527683;cursor:grab;touch-action:none;user-select:none}.pb-grip:hover{background:rgba(72,216,241,.055);color:#9be5f1}.pb-icon{width:52px;height:52px;display:grid;place-items:center;border-radius:13px;background:rgba(72,216,241,.07);color:#5ed6ee;font-size:17px}.pb-copy{min-width:0}.pb-title-line{display:flex;align-items:center;gap:8px;min-width:0}.pb-title-line h3{margin:0;color:#e2f5f8;font-size:12px;line-height:1.25;letter-spacing:-.01em;white-space:nowrap;overflow:hidden;text-overflow:ellipsis}.pb-live{flex:0 0 auto;padding:3px 7px;border-radius:999px;background:rgba(54,211,171,.09);color:#72e0c3;font-size:5.5px;font-weight:850;letter-spacing:.12em}.pb-card[data-state=hidden] .pb-live{background:rgba(130,150,160,.09);color:#8298a0}.pb-copy>p{margin:5px 0 0;color:#6d8b96;font-size:8px;line-height:1.45;white-space:nowrap;overflow:hidden;text-overflow:ellipsis}.pb-meta{display:flex;align-items:center;gap:12px;flex-wrap:wrap;margin-top:7px;color:#5f8793;font-size:6.5px}.pb-meta span{display:inline-flex;align-items:center;gap:5px}.pb-meta i{color:#49bad0;font-size:5px}.pb-card-actions{display:flex;align-items:center;gap:6px;padding-left:8px;border-left:1px solid rgba(91,213,237,.08)}.pb-action{display:inline-flex;align-items:center;justify-content:center;gap:6px;min-height:31px;padding:0 9px;border:1px solid rgba(91,213,237,.12);border-radius:8px;background:transparent;color:#78cfdf;text-decoration:none;font-size:7.5px;font-weight:750;cursor:pointer;white-space:nowrap}.pb-action:hover,.pb-control[aria-expanded=true]{border-color:rgba(91,213,237,.28);background:rgba(72,216,241,.06);color:#e5f9fc}.pb-source{color:#8caab3}.pb-toggle{display:flex;align-items:center;cursor:pointer;margin-left:2px}.pb-toggle input{position:absolute;opacity:0;pointer-events:none}.pb-toggle i{position:relative;display:block;width:43px;height:24px;border:1px solid rgba(91,213,237,.16);border-radius:999px;background:#203943;transition:.2s}.pb-toggle i:after{content:"";position:absolute;top:3px;left:3px;width:16px;height:16px;border-radius:50%;background:#8ca6ac;transition:.2s}.pb-toggle input:checked+i{background:rgba(45,188,151,.45);border-color:rgba(91,226,187,.46)}.pb-toggle input:checked+i:after{left:22px;background:#dffff8}.pb-controls{padding:15px 17px 17px;border-top:1px solid rgba(91,213,237,.08);background:rgba(72,216,241,.018)}.pb-controls-head{display:flex;align-items:flex-end;justify-content:space-between;gap:15px;margin-bottom:12px}.pb-controls-head strong{display:block;margin-top:4px;color:#def4f7;font-size:10px}.pb-controls-head>span{color:#5f7e89;font-size:6.5px}.pb-editor{display:grid;gap:10px}.pb-editor-intro{display:flex;align-items:flex-start;gap:9px;padding:9px 10px;border:1px solid rgba(91,213,237,.08);border-radius:9px;background:rgba(72,216,241,.018)}.pb-editor-intro>span{width:30px;height:30px;display:grid;place-items:center;border-radius:8px;background:rgba(72,216,241,.07);color:#5fd5ee}.pb-editor-intro strong{display:block;color:#bfe0e6;font-size:8.5px}.pb-editor-intro p{margin:3px 0 0;color:#678692;font-size:6.5px;line-height:1.5}.pb-grid{display:grid;gap:9px}.pb-grid-2{grid-template-columns:1fr 1fr}.pb-grid-3{grid-template-columns:1fr 1fr 1fr}.pb-grid-4{grid-template-columns:repeat(4,minmax(0,1fr))}.pb-grid label,.pb-full,.pb-simple label{display:grid;gap:5px}.pb-grid label>span,.pb-full>span,.pb-simple label>span{color:#8ba8b1;font-size:7px;font-weight:750}.pb-grid small{color:#5e7c87;font-size:6px;line-height:1.35}.pb-controls input:not([type=checkbox]),.pb-controls textarea,.pb-controls select{width:100%;max-width:100%;box-sizing:border-box;border:1px solid rgba(91,213,237,.13);border-radius:8px;background:#071b25;color:#e4f6f8;padding:8px 9px;font-size:8px;outline:none}.pb-controls textarea{min-height:135px;resize:vertical;line-height:1.5}.pb-controls input:focus,.pb-controls textarea:focus,.pb-controls select:focus{border-color:rgba(81,216,240,.4);box-shadow:0 0 0 3px rgba(81,216,240,.05)}.pb-check{display:flex!important;align-items:center;gap:7px;min-height:36px;padding:7px 9px;border:1px solid rgba(91,213,237,.1);border-radius:8px;background:rgba(72,216,241,.018);color:#8ba8b1}.pb-check input{width:auto!important}.pb-number{position:relative}.pb-number input{padding-right:38px!important}.pb-number em{position:absolute;right:9px;top:50%;transform:translateY(-50%);font-style:normal;color:#5f7e89;font-size:6.5px}.pb-management{display:grid;gap:10px}.pb-picker{margin-top:9px;padding:10px;border:1px solid rgba(91,213,237,.1);border-radius:9px;background:#061923}.pb-picker[data-invalid=true]{border-color:rgba(244,122,122,.35);box-shadow:0 0 0 2px rgba(244,122,122,.05)}.pb-picker-head{display:flex;align-items:center;justify-content:space-between;gap:12px;margin-bottom:7px}.pb-picker-head strong{display:block;color:#bfe0e6;font-size:8px}.pb-picker-head small{display:block;margin-top:2px;color:#607e89;font-size:6.5px}.pb-picker-head>span{color:#60d1e7;font-size:6.5px;font-weight:800}.pb-search{margin-bottom:6px}.pb-picker-list{display:grid;gap:3px;max-height:210px;overflow:auto}.pb-picker-item{display:flex!important;align-items:center;gap:7px;padding:7px;border-radius:7px;background:rgba(72,216,241,.02);color:#a6c2ca!important;font-size:7.5px!important}.pb-picker-item:hover{background:rgba(72,216,241,.055)}.pb-picker-item input{width:auto!important}.pb-picker-item[hidden],.management-profile-choice[hidden]{display:none!important}.pb-picker-item input:disabled{opacity:.35}.pb-simple{display:flex;align-items:center;justify-content:space-between;gap:20px;padding:11px;border:1px solid rgba(91,213,237,.09);border-radius:9px;background:rgba(72,216,241,.018)}.pb-simple>div{min-width:0}.pb-simple strong{display:block;color:#bfe0e6;font-size:8.5px}.pb-simple p{margin:3px 0 0;color:#668590;font-size:6.5px;line-height:1.5}.pb-simple label{width:190px;flex:0 0 190px}
@media(max-width:1050px){.pb-card-main{grid-template-columns:42px 48px minmax(0,1fr) auto;gap:9px}.pb-icon{width:48px;height:48px}.pb-action{padding:0 7px}.pb-action span{display:none}.pb-card-actions{gap:5px}}
@media(max-width:760px){.pb-hero{display:block;margin-bottom:16px}.pb-hero h1{font-size:34px}.pb-hero p{font-size:8.5px}.pb-hero-actions{margin-top:10px}.pb-preview{flex:1}.pb-stat{min-width:100px}.pb-builder-head{padding:15px}.pb-builder-head h2{font-size:15px}.pb-save{min-height:36px;padding:0 11px}.pb-save span{display:none}.pb-framework{align-items:flex-start;margin:9px 9px 6px;padding:9px}.pb-framework>b{display:none}.pb-list{padding:0 7px 9px}.pb-card{margin:6px 0;border-radius:12px}.pb-card-main{grid-template-columns:31px 43px minmax(0,1fr);gap:8px;min-height:auto;padding:10px}.pb-drag-rail{height:60px}.pb-grip{width:27px;height:27px}.pb-icon{width:43px;height:43px;border-radius:11px;font-size:14px}.pb-title-line{flex-wrap:wrap}.pb-title-line h3{font-size:10px}.pb-copy>p{white-space:normal;display:-webkit-box;-webkit-line-clamp:2;-webkit-box-orient:vertical;font-size:7px}.pb-meta{gap:7px;font-size:6px;margin-top:5px}.pb-card-actions{grid-column:1/-1;display:grid;grid-template-columns:minmax(0,1fr) minmax(0,1fr) 43px;padding:8px 0 0;border-left:0;border-top:1px solid rgba(91,213,237,.07)}.pb-action{min-height:33px;font-size:7.5px}.pb-action span{display:inline}.pb-toggle{justify-content:flex-end}.pb-toggle i{width:41px}.pb-controls{padding:12px}.pb-controls-head{display:block}.pb-controls-head>span{display:block;margin-top:4px}.pb-grid-2,.pb-grid-3,.pb-grid-4{grid-template-columns:1fr}.pb-simple{display:grid;gap:10px}.pb-simple label{width:auto;flex:auto}.pb-picker{margin-top:8px}.pb-picker-list{max-height:185px}}
@media(max-width:430px){.pb-hero h1{font-size:31px}.pb-preview,.pb-stat{min-height:36px;padding:0 9px;font-size:7px}.pb-builder-head{padding:13px}.pb-framework small{font-size:6px}.pb-card-main{grid-template-columns:28px 39px minmax(0,1fr);gap:7px;padding:9px}.pb-drag-rail{height:56px}.pb-icon{width:39px;height:39px;font-size:13px}.pb-title-line h3{font-size:9px}.pb-live{font-size:5px;padding:3px 6px}.pb-copy>p{font-size:6.5px}.pb-meta{font-size:5.5px}.pb-card-actions{grid-template-columns:minmax(0,1fr) minmax(0,1fr) 40px;gap:5px}.pb-action{min-height:31px;font-size:7px;padding:0 5px}.pb-toggle i{width:38px;height:22px}.pb-toggle i:after{width:14px;height:14px;top:3px;left:3px}.pb-toggle input:checked+i:after{left:20px}.pb-controls{padding:10px}.pb-controls input:not([type=checkbox]),.pb-controls textarea,.pb-controls select{font-size:7.5px;padding:8px}.pb-controls textarea{min-height:120px}}
@media(prefers-reduced-motion:reduce){.pb-card,.pb-save,.pb-preview{transition:none}}
</style>
@endpush

@push('scripts')
<script>
(()=>{
 const form=document.getElementById('home-builder'),list=document.getElementById('section-list'),fields=document.getElementById('order-fields');
 if(!form||!list||!fields)return;
 const rows=()=>[...list.querySelectorAll('.pb-card')];
 const sync=()=>{fields.innerHTML=rows().map(r=>'<input type="hidden" name="section_order[]" value="'+r.dataset.key+'">').join('');};
 rows().forEach(row=>{
   row.querySelector('.pb-control')?.addEventListener('click',()=>{const b=row.querySelector('.pb-control'),p=row.querySelector('.pb-controls');if(!p)return;const open=b.getAttribute('aria-expanded')==='true';p.hidden=open;b.setAttribute('aria-expanded',String(!open));});
   row.querySelector('.pb-toggle input')?.addEventListener('change',e=>{row.dataset.state=e.target.checked?'visible':'hidden';});
 });
 let drag=null;
 rows().forEach(row=>{
   const handle=row.querySelector('.pb-grip'); if(!handle)return;
   let timer=null,active=false,lastY=0;
   const clear=()=>{if(timer){clearTimeout(timer);timer=null;}if(active){active=false;row.classList.remove('dragging');rows().forEach(x=>x.classList.remove('drop-target'));sync();}};
   handle.addEventListener('touchstart',e=>{if(e.touches.length!==1)return;lastY=e.touches[0].clientY;timer=setTimeout(()=>{active=true;row.classList.add('dragging');},240);},{passive:true});
   handle.addEventListener('touchmove',e=>{if(!active){if(Math.abs(e.touches[0].clientY-lastY)>10)clear();return;}e.preventDefault();const point=e.touches[0],target=document.elementFromPoint(point.clientX,point.clientY)?.closest('.pb-card');rows().forEach(x=>x.classList.remove('drop-target'));if(!target||target===row)return;target.classList.add('drop-target');const rect=target.getBoundingClientRect();list.insertBefore(row,point.clientY<rect.top+rect.height/2?target:target.nextSibling);},{passive:false});
   handle.addEventListener('touchend',clear,{passive:true});handle.addEventListener('touchcancel',clear,{passive:true});
 });
 rows().forEach(row=>{
   row.addEventListener('dragstart',()=>{drag=row;row.classList.add('dragging');});
   row.addEventListener('dragover',e=>{if(!drag||drag===row)return;e.preventDefault();row.classList.add('drop-target');});
   row.addEventListener('dragleave',()=>row.classList.remove('drop-target'));
   row.addEventListener('drop',e=>{e.preventDefault();if(!drag||drag===row)return;const rect=row.getBoundingClientRect();list.insertBefore(drag,e.clientY<rect.top+rect.height/2?row:row.nextSibling);row.classList.remove('drop-target');sync();});
   row.addEventListener('dragend',()=>{row.classList.remove('dragging');rows().forEach(x=>x.classList.remove('drop-target'));drag=null;sync();});
 });
 document.querySelectorAll('.home-mode').forEach(select=>{
   const settings=select.closest('.pb-controls'),panel=settings?.querySelector('.pb-picker'),limit=settings?.querySelector('.pb-number input'),boxes=()=>panel?[...panel.querySelectorAll('input[type=checkbox]')]:[];
   const refresh=()=>{if(!panel)return;const selected=boxes().filter(x=>x.checked),max=Math.max(1,Math.min(100,parseInt(limit?.value||'100',10)));panel.querySelector('[data-count]').textContent=selected.length+' selected';boxes().forEach(x=>x.disabled=!x.checked&&selected.length>=max);};
   const syncMode=()=>{if(panel)panel.hidden=select.value!=='selected';refresh();};
   select.addEventListener('change',syncMode);limit?.addEventListener('input',refresh);panel?.querySelector('.pb-search')?.addEventListener('input',e=>{const q=e.target.value.toLowerCase().trim();panel.querySelectorAll('.pb-picker-item').forEach(i=>i.hidden=!!q&&!i.dataset.search.includes(q));});panel?.addEventListener('change',e=>{if(e.target.matches('input[type=checkbox]'))refresh();});syncMode();
 });
 document.querySelectorAll('.management-selection-panel').forEach(panel=>{
   const settings=panel.closest('.pb-controls'),folder=settings?.querySelector('.management-folder'),boxes=()=>[...panel.querySelectorAll('input[data-folder-profile]')];
   const refresh=()=>{const folderId=folder?.value||'',selected=boxes().filter(x=>x.checked),valid=selected.filter(x=>x.closest('.pb-picker-item')?.dataset.folderId===folderId);panel.querySelector('[data-count]').textContent=valid.length+' selected';panel.dataset.invalid=(!folderId||valid.length<1)?'true':'false';boxes().forEach(box=>{const item=box.closest('.pb-picker-item'),same=item?.dataset.folderId===folderId;if(!same)box.checked=false;box.disabled=!same;if(item)item.hidden=!same;});const q=panel.querySelector('.pb-search')?.value.toLowerCase().trim()||'';boxes().forEach(box=>{const item=box.closest('.pb-picker-item');if(item&&!item.hidden)item.hidden=!!q&&!item.dataset.search.includes(q);});};
   folder?.addEventListener('change',refresh);panel.querySelector('.pb-search')?.addEventListener('input',refresh);panel.addEventListener('change',e=>{if(e.target.matches('input[data-folder-profile]'))refresh();});refresh();
 });
 const managementPanel=document.querySelector('.management-selection-panel');
 if(managementPanel)form.addEventListener('submit',e=>{const enabled=form.querySelector('input[name="sections[management]"]')?.checked??false;if(!enabled)return;const settings=managementPanel.closest('.pb-controls'),folder=settings?.querySelector('.management-folder'),selected=[...managementPanel.querySelectorAll('input[data-folder-profile]:checked')];if(!folder?.value||selected.length<1){e.preventDefault();managementPanel.hidden=false;managementPanel.dataset.invalid='true';managementPanel.scrollIntoView({behavior:'smooth',block:'center'});folder?.focus();}});
 form.addEventListener('submit',sync);sync();
})();
</script>
@endpush
