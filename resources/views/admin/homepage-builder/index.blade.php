@extends('layouts.portal')
@section('title','Homepage')
@section('content')
@php
    $icons = [
        'hero'=>'fa-images',
        'welcome'=>'fa-building',
        'statistics'=>'fa-chart-simple',
        'projects'=>'fa-industry',
        'management'=>'fa-users',
        'news'=>'fa-newspaper',
        'gallery'=>'fa-images',
        'highlight'=>'fa-bullhorn',
        'cta'=>'fa-paper-plane',
    ];
    $titles = [
        'hero'=>'Hero Slider',
        'welcome'=>'Welcome Message',
        'statistics'=>'Power Plant Statistics',
        'projects'=>'Power Plant Projects',
        'management'=>'Management Team',
        'news'=>'News & Notices',
        'gallery'=>'Gallery',
        'highlight'=>'Homepage Highlight',
        'cta'=>'Contact & Call to Action',
    ];
    $sourceLabels = [
        'hero'=>'Slider Manager',
        'welcome'=>'Homepage content',
        'statistics'=>'Power Plant records',
        'projects'=>'Power Plant Manager',
        'management'=>'Management Team',
        'news'=>'News & Notices',
        'gallery'=>'Gallery Manager',
        'highlight'=>'Highlight Manager',
        'cta'=>'Homepage system section',
    ];
    $manageRoutes = [
        'hero'=>route('admin.sliders.index'),
        'projects'=>route('admin.plants.index'),
        'management'=>route('admin.management.index'),
        'news'=>route('admin.site-content.index',['type'=>'news']),
        'gallery'=>route('admin.gallery.index'),
        'highlight'=>route('admin.site-popups.index'),
    ];
    $controlSections = ['welcome','statistics','projects','management','news','gallery','cta'];
    $countLabels = [
        'hero'=>['key'=>'sliders','suffix'=>'published slides'],
        'projects'=>['key'=>'projects','suffix'=>'projects'],
        'management'=>['key'=>'management','suffix'=>'published profiles'],
        'news'=>['key'=>'news','suffix'=>'published items'],
        'gallery'=>['key'=>'gallery','suffix'=>'published galleries'],
    ];
    $visibleCount = $sections->where('is_enabled',true)->count();
@endphp

<section class="homepage-hero">
    <div class="hero-copy">
        <span class="eyebrow"><i class="fa-solid fa-layer-group"></i> Website / Homepage Control</span>
        <h1>Homepage</h1>
        <p>Control the public homepage from one focused workspace. Set section order, visibility and display rules here; edit source content only in its dedicated manager.</p>
    </div>
    <div class="hero-tools">
        <a class="preview-button" href="{{ route('home') }}" target="_blank" rel="noopener">
            <i class="fa-solid fa-arrow-up-right-from-square"></i><span>Preview website</span>
        </a>
        <div class="hero-stat"><strong>{{ $visibleCount }}</strong><span>of {{ $sections->count() }} visible</span></div>
    </div>
</section>

@if(session('status'))
<div class="flash flash-success"><i class="fa-solid fa-circle-check"></i><span>{{ session('status') }}</span></div>
@endif
@if($errors->any())
<div class="flash flash-error"><i class="fa-solid fa-triangle-exclamation"></i><span>{{ $errors->first() }}</span></div>
@endif

<form method="POST" action="{{ route('admin.homepage-builder.update') }}" id="home-builder">
@csrf
<div class="builder">
    <header class="builder-head">
        <div>
            <span class="eyebrow">PAGE STRUCTURE</span>
            <h2>Homepage sections</h2>
            <p>Arrange the visitor journey here. Use <b>Controls</b> only for homepage display rules and <b>Manage source</b> when the actual content belongs to another module.</p>
        </div>
        <button class="save-button" type="submit"><i class="fa-solid fa-floppy-disk"></i><span>Save changes</span></button>
    </header>

    <div class="workflow" aria-label="Homepage workflow">
        <div class="workflow-step"><b>01</b><span><strong>Arrange</strong><small>Order sections</small></span></div>
        <div class="workflow-line"></div>
        <div class="workflow-step"><b>02</b><span><strong>Control</strong><small>Visibility & display</small></span></div>
        <div class="workflow-line"></div>
        <div class="workflow-step"><b>03</b><span><strong>Source</strong><small>Edit content in its module</small></span></div>
    </div>

    <div class="section-list" id="section-list">
    @foreach($sections as $index => $section)
        @php
            $settings = is_array($section->settings) ? $section->settings : [];
            $limit = (int)($settings['limit'] ?? match($section->key){'projects'=>6,'management'=>4,'news'=>3,'gallery'=>4,default=>0});
            $mode = $settings['mode'] ?? 'latest';
            $layout = $settings['layout'] ?? 'left';
            $hasControls = in_array($section->key,$controlSections,true);
            $countMeta = $countLabels[$section->key] ?? null;
        @endphp
        <article class="section-card" draggable="true" data-key="{{ $section->key }}" data-state="{{ $section->is_enabled ? 'visible' : 'hidden' }}">
            <div class="section-row">
                <div class="order-column" aria-label="Reorder {{ $titles[$section->key] ?? $section->label }}">
                    <span class="order-number">{{ sprintf('%02d',$index + 1) }}</span>
                    <button type="button" class="move-up" title="Move up" aria-label="Move {{ $titles[$section->key] ?? $section->label }} up"><i class="fa-solid fa-chevron-up"></i></button>
                    <span class="drag-handle" title="Drag to reorder"><i class="fa-solid fa-grip-vertical"></i></span>
                    <button type="button" class="move-down" title="Move down" aria-label="Move {{ $titles[$section->key] ?? $section->label }} down"><i class="fa-solid fa-chevron-down"></i></button>
                </div>

                <span class="section-icon"><i class="fa-solid {{ $icons[$section->key] ?? 'fa-layer-group' }}"></i></span>

                <div class="section-copy">
                    <div class="section-title">
                        <h3>{{ $titles[$section->key] ?? $section->label }}</h3>
                        <span class="state-badge">{{ $section->is_enabled ? 'Visible' : 'Hidden' }}</span>
                    </div>
                    <p>{{ $section->description }}</p>
                    <div class="source-line">
                        <span><i class="fa-solid fa-database"></i> {{ $sourceLabels[$section->key] ?? 'Dedicated module' }}</span>
                        @if($countMeta && isset($counts[$countMeta['key']]))
                            <span><i class="fa-solid fa-circle"></i> {{ $counts[$countMeta['key']] }} {{ $countMeta['suffix'] }}</span>
                        @endif
                    </div>
                </div>

                <div class="row-actions">
                    @if($hasControls)
                        <button type="button" class="control-toggle" aria-expanded="false" aria-controls="controls-{{ $section->key }}">
                            <i class="fa-solid fa-sliders"></i><span>Controls</span>
                        </button>
                    @endif
                    @if(isset($manageRoutes[$section->key]))
                        <a class="source-link" href="{{ $manageRoutes[$section->key] }}" title="Open {{ $sourceLabels[$section->key] }}">
                            <i class="fa-solid fa-arrow-up-right-from-square"></i><span>Open manager</span>
                        </a>
                    @endif
                    <label class="visibility" title="{{ $section->is_enabled ? 'Hide from homepage' : 'Show on homepage' }}">
                        <span>Show</span>
                        <input type="checkbox" name="sections[{{ $section->key }}]" value="1" @checked($section->is_enabled) aria-label="Show {{ $titles[$section->key] ?? $section->label }} on homepage">
                        <i></i>
                    </label>
                </div>
            </div>

            @if($hasControls)
            <div class="section-controls" id="controls-{{ $section->key }}" hidden>
                <div class="controls-head">
                    <div><span class="eyebrow">HOMEPAGE CONTROLS</span><strong>{{ $titles[$section->key] ?? $section->label }}</strong></div>
                    <span class="control-rule">Only homepage-specific controls live here.</span>
                </div>

                @if($section->key==='welcome')
                    <div class="welcome-editor">
                        <div class="editor-intro">
                            <div class="editor-icon"><i class="fa-solid fa-pen-to-square"></i></div>
                            <div><strong>Welcome message</strong><p>Edit the homepage introduction and decide exactly how much visitors see before “Read more”.</p></div>
                        </div>
                        <div class="field-grid two">
                            <label><span>Eyebrow</span><input name="settings[welcome][eyebrow]" maxlength="120" value="{{ $settings['eyebrow'] ?? 'Welcome to '.config('fuelfree.company.name') }}"></label>
                            <label><span>Homepage title</span><input name="settings[welcome][title]" maxlength="240" value="{{ $settings['title'] ?? '' }}"></label>
                        </div>
                        <label class="full-field"><span>Complete welcome message</span><textarea name="settings[welcome][content]" rows="10" maxlength="30000" placeholder="Write the complete company introduction here...">{{ $settings['content'] ?? '' }}</textarea></label>
                        <div class="field-grid four">
                            <label><span>Preview words</span><input type="number" name="settings[welcome][preview_words]" min="20" max="500" value="{{ $settings['preview_words'] ?? 180 }}"><small>Shown first on the homepage</small></label>
                            <label><span>More words</span><input type="number" name="settings[welcome][more_words]" min="20" max="2000" value="{{ $settings['more_words'] ?? 900 }}"><small>Revealed after Read more</small></label>
                            <label><span>Text alignment</span><select name="settings[welcome][layout]"><option value="left" @selected($layout==='left')>Left</option><option value="center" @selected($layout==='center')>Center</option><option value="right" @selected($layout==='right')>Right</option></select></label>
                            <label class="check-field"><input type="checkbox" name="settings[welcome][show_full]" value="1" @checked($settings['show_full'] ?? false)><span>Show full message</span></label>
                        </div>
                    </div>
                @elseif(in_array($section->key,['projects','management','news','gallery'],true))
                    <div class="display-grid">
                        <label><span>Items on homepage</span><div class="number-field"><input type="number" name="settings[{{ $section->key }}][limit]" min="1" max="100" value="{{ $limit }}"><em>items</em></div></label>
                        <label><span>Content selection</span><select name="settings[{{ $section->key }}][mode]" class="home-mode"><option value="latest" @selected($mode==='latest')>Latest published</option><option value="selected" @selected($mode==='selected')>Choose specific items</option></select></label>
                        <label><span>Section alignment</span><select name="settings[{{ $section->key }}][layout]"><option value="left" @selected($layout==='left')>Left</option><option value="center" @selected($layout==='center')>Center</option><option value="right" @selected($layout==='right')>Right</option></select></label>
                    </div>
                    <div class="selection-panel" data-key="{{ $section->key }}" hidden>
                        <div class="picker-head"><div><strong>Choose items to feature</strong><small>These records already exist in the source module. Nothing is created or edited here.</small></div><span data-count>0 selected</span></div>
                        <input class="picker-search" type="search" placeholder="Search published items..." aria-label="Search {{ $titles[$section->key] }} items">
                        <div class="picker-list">
                        @foreach(($choices[$section->key] ?? []) as $choice)
                            @php($choiceId=(int)$choice->id)
                            <label class="picker-item" data-search="{{ strtolower($choice->title ?? $choice->name) }}"><input type="checkbox" name="settings[{{ $section->key }}][ids][]" value="{{ $choiceId }}" @checked(in_array($choiceId,$settings['ids'] ?? [],true))><span>{{ $choice->title ?? $choice->name }}</span></label>
                        @endforeach
                        </div>
                    </div>
                @elseif($section->key==='statistics')
                    <div class="simple-control">
                        <div><strong>Automatic statistics</strong><p>Figures come directly from Power Plant records. Update the source records instead of maintaining duplicate numbers here.</p></div>
                        <label><span>Section alignment</span><select name="settings[statistics][layout]"><option value="left" @selected($layout==='left')>Left</option><option value="center" @selected($layout==='center')>Center</option><option value="right" @selected($layout==='right')>Right</option></select></label>
                    </div>
                @elseif($section->key==='cta')
                    <div class="simple-control">
                        <div><strong>Call-to-action display</strong><p>This controls placement only. The CTA content is not duplicated in Homepage controls.</p></div>
                        <label><span>Section alignment</span><select name="settings[cta][layout]"><option value="left" @selected($layout==='left')>Left</option><option value="center" @selected($layout==='center')>Center</option><option value="right" @selected($layout==='right')>Right</option></select></label>
                    </div>
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
.homepage-hero{display:flex;align-items:flex-end;justify-content:space-between;gap:30px;margin-bottom:22px}.hero-copy{min-width:0}.homepage-hero h1{margin:7px 0 8px;color:#eaf8fb;font-size:clamp(34px,3.6vw,50px);line-height:1;letter-spacing:-.045em}.homepage-hero p{max-width:820px;margin:0;color:#7898a4;font-size:11px;line-height:1.7}.eyebrow{display:inline-flex;align-items:center;gap:7px;color:#5fd6ef;font-size:8px;font-weight:800;letter-spacing:.16em;text-transform:uppercase}.hero-tools{display:flex;align-items:center;gap:9px;flex:0 0 auto}.preview-button,.hero-stat{display:inline-flex;align-items:center;justify-content:center;gap:8px;min-height:40px;padding:0 13px;border:1px solid rgba(91,213,237,.14);border-radius:11px;background:rgba(72,216,241,.035);color:#a8ccd5;text-decoration:none;font-size:9px;font-weight:700}.preview-button:hover{color:#effcff;border-color:rgba(91,213,237,.3);background:rgba(72,216,241,.07)}.preview-button i{color:#60d7ef}.hero-stat{min-width:96px;gap:5px}.hero-stat strong{font-size:13px;color:#e6f8fb}.hero-stat span{color:#6f8f9b;font-size:8px}.flash{display:flex;align-items:center;gap:9px;margin:0 0 15px;padding:11px 13px;border-radius:10px;font-size:9px;line-height:1.5}.flash-success{border:1px solid rgba(74,218,178,.16);background:rgba(74,218,178,.045);color:#9ce6cf}.flash-error{border:1px solid rgba(244,122,122,.18);background:rgba(244,122,122,.045);color:#f0aaaa}.builder{border:1px solid var(--line);border-radius:20px;background:linear-gradient(145deg,rgba(8,38,52,.78),rgba(3,20,29,.93));overflow:hidden;box-shadow:0 20px 55px rgba(0,0,0,.08)}.builder-head{display:flex;align-items:center;justify-content:space-between;gap:20px;padding:20px 22px;border-bottom:1px solid rgba(91,213,237,.08)}.builder-head h2{margin:5px 0 4px;color:#e8f8fb;font-size:18px;letter-spacing:-.02em}.builder-head p{margin:0;max-width:840px;color:#6f8d98;font-size:8px;line-height:1.6}.builder-head p b{color:#a7d5de}.save-button{display:inline-flex;align-items:center;justify-content:center;gap:8px;min-height:40px;padding:0 15px;border:0;border-radius:10px;background:linear-gradient(135deg,#25abc9,#15839e);color:#fff;font-size:9px;font-weight:800;cursor:pointer;white-space:nowrap;box-shadow:0 8px 22px rgba(25,167,198,.13)}.save-button:hover{filter:brightness(1.06);transform:translateY(-1px)}.workflow{display:flex;align-items:center;gap:10px;padding:12px 22px;border-bottom:1px solid rgba(91,213,237,.07);background:rgba(72,216,241,.016)}.workflow-step{display:flex;align-items:center;gap:8px;min-width:0}.workflow-step b{width:26px;height:26px;display:grid;place-items:center;border:1px solid rgba(91,213,237,.13);border-radius:7px;background:rgba(72,216,241,.035);color:#5fd6ef;font-size:7px}.workflow-step span{display:grid;gap:1px}.workflow-step strong{color:#b9d9df;font-size:8px}.workflow-step small{color:#607f8b;font-size:6.5px}.workflow-line{height:1px;flex:0 0 42px;background:rgba(91,213,237,.1)}.section-list{padding:8px 10px 11px}.section-card{border:1px solid rgba(91,213,237,.1);border-radius:15px;background:rgba(2,15,23,.63);margin:8px 0;transition:border-color .2s,background .2s,transform .2s,box-shadow .2s}.section-card:hover{border-color:rgba(91,213,237,.2);background:rgba(5,25,36,.76)}.section-card.dragging{opacity:.45;box-shadow:0 0 0 1px rgba(91,213,237,.35)}.section-card.drop-target{outline:1px dashed rgba(91,213,237,.8);outline-offset:2px}.section-row{display:grid;grid-template-columns:46px 48px minmax(0,1fr) auto;align-items:center;gap:13px;padding:13px 14px}.order-column{display:grid;grid-template-columns:22px 22px;grid-template-rows:16px 20px 16px;align-items:center;justify-items:center;gap:1px;color:#5e7e89}.order-number{grid-column:1/3;grid-row:1;color:#4f7480;font-size:7px;font-weight:800;letter-spacing:.1em}.order-column button{width:22px;height:18px;padding:0;border:1px solid rgba(91,213,237,.1);border-radius:5px;background:#061923;color:#7c9ca6;font-size:6px;cursor:pointer}.order-column button:disabled{opacity:.28;cursor:not-allowed}.move-up{grid-column:1;grid-row:2}.move-down{grid-column:2;grid-row:2}.drag-handle{grid-column:1/3;grid-row:3;display:grid;place-items:center;width:44px;height:15px;color:#4f7180;cursor:grab}.drag-handle:hover{color:#8ddceb}.section-icon{width:48px;height:48px;display:grid;place-items:center;border-radius:13px;background:rgba(72,216,241,.07);color:#5fd5ee;font-size:16px}.section-copy{min-width:0}.section-title{display:flex;align-items:center;gap:9px;min-width:0}.section-title h3{margin:0;color:#e0f4f7;font-size:12px;line-height:1.3;letter-spacing:-.01em}.state-badge{flex:0 0 auto;padding:3px 7px;border-radius:999px;background:rgba(72,218,178,.07);color:#72dfc1;font-size:6px;font-weight:800;letter-spacing:.1em;text-transform:uppercase}.section-card[data-state=hidden] .state-badge{background:rgba(135,155,164,.08);color:#8198a0}.section-copy>p{margin:4px 0 0;color:#6e8b96;font-size:8px;line-height:1.45;white-space:nowrap;overflow:hidden;text-overflow:ellipsis}.source-line{display:flex;align-items:center;gap:11px;flex-wrap:wrap;margin-top:6px;color:#5e8793;font-size:6.5px}.source-line span{display:inline-flex;align-items:center;gap:5px}.source-line i{color:#46b8cf;font-size:5px}.row-actions{display:flex;align-items:center;justify-content:flex-end;gap:7px}.control-toggle,.source-link{display:inline-flex;align-items:center;justify-content:center;gap:6px;min-height:32px;padding:0 10px;border:1px solid rgba(91,213,237,.12);border-radius:8px;background:transparent;color:#72cfe2;text-decoration:none;font-size:8px;font-weight:700;cursor:pointer;white-space:nowrap}.control-toggle:hover,.control-toggle[aria-expanded=true],.source-link:hover{background:rgba(72,216,241,.06);border-color:rgba(91,213,237,.26);color:#e3f8fb}.source-link{color:#8caab3}.visibility{display:flex;align-items:center;gap:6px;cursor:pointer}.visibility>span{color:#6e8b96;font-size:7px}.visibility input{position:absolute;opacity:0;pointer-events:none}.visibility i{position:relative;display:block;width:42px;height:23px;border:1px solid rgba(91,213,237,.17);border-radius:999px;background:#203944;transition:.2s}.visibility i:after{content:"";position:absolute;top:3px;left:3px;width:15px;height:15px;border-radius:50%;background:#8ea7ad;transition:.2s}.visibility input:checked+i{background:rgba(45,188,151,.45);border-color:rgba(91,226,187,.45)}.visibility input:checked+i:after{left:22px;background:#dffff8}.section-controls{padding:15px 17px 17px;border-top:1px solid rgba(91,213,237,.08);background:rgba(72,216,241,.018)}.controls-head{display:flex;align-items:flex-end;justify-content:space-between;gap:15px;margin-bottom:13px}.controls-head strong{display:block;margin-top:4px;color:#def4f7;font-size:11px}.control-rule{color:#5f7e89;font-size:7px}.welcome-editor{display:grid;gap:10px}.editor-intro{display:flex;align-items:flex-start;gap:10px;padding:10px 11px;border:1px solid rgba(91,213,237,.08);border-radius:10px;background:rgba(72,216,241,.018)}.editor-icon{width:31px;height:31px;flex:0 0 31px;display:grid;place-items:center;border-radius:8px;background:rgba(72,216,241,.07);color:#5fd5ee}.editor-intro strong{display:block;color:#bfe0e6;font-size:9px}.editor-intro p{margin:3px 0 0;color:#678692;font-size:7px;line-height:1.5}.field-grid{display:grid;gap:10px}.field-grid.two{grid-template-columns:1fr 1fr}.field-grid.four{grid-template-columns:repeat(4,minmax(0,1fr))}.field-grid label,.full-field,.display-grid label,.simple-control label{display:grid;gap:6px}.field-grid label>span,.full-field>span,.display-grid label>span,.simple-control label>span{color:#8ba8b1;font-size:7.5px;font-weight:750}.field-grid label small{color:#5e7c87;font-size:6.5px;line-height:1.35}.section-controls input:not([type=checkbox]),.section-controls textarea,.section-controls select{width:100%;max-width:100%;border:1px solid rgba(91,213,237,.13);border-radius:9px;background:#071b25;color:#e4f6f8;padding:9px 10px;font:inherit;font-size:9px;outline:none}.section-controls textarea{min-height:145px;resize:vertical;line-height:1.55}.section-controls input:focus,.section-controls textarea:focus,.section-controls select:focus{border-color:rgba(81,216,240,.4);box-shadow:0 0 0 3px rgba(81,216,240,.05)}.check-field{display:flex!important;align-items:center;gap:8px;min-height:38px;padding:8px 10px;border:1px solid rgba(91,213,237,.1);border-radius:9px;background:rgba(72,216,241,.018);color:#8ba8b1}.check-field input{width:auto!important}.display-grid{display:grid;grid-template-columns:1fr 1fr 1fr;gap:10px}.number-field{position:relative}.number-field input{padding-right:43px!important}.number-field em{position:absolute;right:10px;top:50%;transform:translateY(-50%);font-style:normal;color:#5f7e89;font-size:7px}.selection-panel{margin-top:11px;padding:11px;border:1px solid rgba(91,213,237,.1);border-radius:10px;background:#061923}.picker-head{display:flex;align-items:center;justify-content:space-between;gap:12px;margin-bottom:8px}.picker-head strong{display:block;color:#bfe0e6;font-size:9px}.picker-head small{display:block;margin-top:3px;color:#607e89;font-size:7px}.picker-head>span{color:#60d1e7;font-size:7px;font-weight:800}.picker-search{margin-bottom:7px}.picker-list{display:grid;gap:3px;max-height:220px;overflow:auto}.picker-item{display:flex!important;align-items:center;gap:7px;padding:8px;border-radius:7px;background:rgba(72,216,241,.02);color:#a6c2ca!important;font-size:8px!important}.picker-item:hover{background:rgba(72,216,241,.055)}.picker-item input{width:auto!important}.simple-control{display:flex;align-items:center;justify-content:space-between;gap:20px;padding:12px;border:1px solid rgba(91,213,237,.09);border-radius:10px;background:rgba(72,216,241,.018)}.simple-control>div{min-width:0}.simple-control strong{display:block;color:#bfe0e6;font-size:9px}.simple-control p{margin:4px 0 0;color:#668590;font-size:7px;line-height:1.5}.simple-control label{width:190px;flex:0 0 190px}.simple-control select{min-height:34px}
@media(max-width:1000px){.section-row{grid-template-columns:42px 44px minmax(0,1fr) auto;gap:10px}.section-icon{width:44px;height:44px}.row-actions{gap:5px}.control-toggle,.source-link{padding:0 8px}.source-line{gap:7px}}
@media(max-width:760px){.homepage-hero{display:block;margin-bottom:17px}.homepage-hero h1{font-size:34px}.homepage-hero p{font-size:9px;line-height:1.65}.hero-tools{margin-top:11px}.preview-button{flex:1}.hero-stat{min-width:98px}.builder-head{align-items:flex-start;padding:15px}.builder-head h2{font-size:15px}.builder-head p{font-size:7.5px}.save-button{min-height:36px;padding:0 12px}.save-button span{display:none}.workflow{gap:7px;padding:9px 13px}.workflow-step b{width:23px;height:23px}.workflow-step small{display:none}.workflow-line{flex:1;min-width:10px}.section-list{padding:6px 8px}.section-row{grid-template-columns:31px 42px minmax(0,1fr);gap:9px;padding:11px}.order-column{grid-template-columns:15px 15px;grid-template-rows:14px 19px 15px}.order-column button{width:19px;height:18px}.drag-handle{width:30px}.section-icon{width:42px;height:42px;border-radius:11px;font-size:14px}.section-title{gap:6px;flex-wrap:wrap}.section-title h3{font-size:10px}.section-copy>p{font-size:7px;white-space:normal;display:-webkit-box;-webkit-line-clamp:2;-webkit-box-orient:vertical}.source-line{font-size:6px;margin-top:5px}.source-line span{max-width:100%;overflow:hidden;text-overflow:ellipsis;white-space:nowrap}.row-actions{grid-column:1/-1;display:grid;grid-template-columns:minmax(0,1fr) minmax(0,1fr) auto;gap:6px;padding-top:9px;border-top:1px solid rgba(91,213,237,.06)}.control-toggle,.source-link{min-height:34px;font-size:8px}.visibility{justify-content:flex-end;padding-inline:2px}.visibility>span{display:none}.visibility i{width:40px}.section-controls{padding:12px}.controls-head{display:block;margin-bottom:10px}.control-rule{display:block;margin-top:4px}.field-grid.two,.field-grid.four,.display-grid{grid-template-columns:1fr}.simple-control{display:grid;gap:11px}.simple-control label{width:auto;flex:auto}.selection-panel{margin-top:9px}.picker-list{max-height:190px}}
@media(max-width:430px){.homepage-hero h1{font-size:31px}.hero-tools{gap:6px}.preview-button,.hero-stat{min-height:37px;font-size:8px;padding:0 9px}.builder-head{gap:9px;padding:13px}.workflow{padding:8px 10px}.workflow-step strong{font-size:7px}.section-list{padding:5px 6px}.section-card{border-radius:13px;margin:6px 0}.section-row{grid-template-columns:29px 38px minmax(0,1fr);gap:7px;padding:9px}.section-icon{width:38px;height:38px;font-size:13px}.section-copy>p{font-size:6.5px}.source-line{font-size:5.5px}.row-actions{gap:5px}.control-toggle,.source-link{min-height:32px;padding:0 6px;font-size:7px}.visibility i{width:38px;height:22px}.visibility i:after{width:14px;height:14px}.visibility input:checked+i:after{left:20px}.section-controls{padding:10px}.editor-intro{padding:8px}.editor-intro p{font-size:6.5px}.section-controls input:not([type=checkbox]),.section-controls textarea,.section-controls select{font-size:8px;padding:8px}.section-controls textarea{min-height:125px}.workflow-step b{width:21px;height:21px;font-size:6px}}
@media(prefers-reduced-motion:reduce){.section-card,.save-button,.preview-button{transition:none}.section-card.dragging{opacity:.8}}
</style>
@endpush

@push('scripts')
<script>
(()=> {
    const form=document.getElementById('home-builder');
    const list=document.getElementById('section-list');
    const fields=document.getElementById('order-fields');
    if(!form||!list||!fields)return;

    const rows=()=>[...list.querySelectorAll('.section-card')];
    const sync=()=>{fields.innerHTML=rows().map(row=>'<input type="hidden" name="section_order[]" value="'+row.dataset.key+'">').join('');};
    const refreshMoveButtons=()=>{
        const all=rows();
        all.forEach((row,i)=>{
            const up=row.querySelector('.move-up'),down=row.querySelector('.move-down');
            if(up)up.disabled=i===0;
            if(down)down.disabled=i===all.length-1;
            const number=row.querySelector('.order-number');
            if(number)number.textContent=String(i+1).padStart(2,'0');
        });
    };

    rows().forEach(row=>{
        row.querySelector('.control-toggle')?.addEventListener('click',()=>{
            const button=row.querySelector('.control-toggle');
            const panel=row.querySelector('.section-controls');
            if(!panel)return;
            const open=button.getAttribute('aria-expanded')==='true';
            panel.hidden=open;
            button.setAttribute('aria-expanded',String(!open));
        });

        row.querySelector('.visibility input')?.addEventListener('change',e=>{
            row.dataset.state=e.target.checked?'visible':'hidden';
            const badge=row.querySelector('.state-badge');
            if(badge)badge.textContent=e.target.checked?'Visible':'Hidden';
        });

        row.querySelector('.move-up')?.addEventListener('click',()=>{
            const all=rows(),i=all.indexOf(row);
            if(i>0){list.insertBefore(row,all[i-1]);sync();refreshMoveButtons();}
        });

        row.querySelector('.move-down')?.addEventListener('click',()=>{
            const all=rows(),i=all.indexOf(row);
            if(i>=0&&i<all.length-1){list.insertBefore(all[i+1],row);sync();refreshMoveButtons();}
        });
    });

    let drag=null;
    if('ontouchstart' in window){rows().forEach(row=>row.draggable=false);}
    rows().forEach(row=>{
        row.addEventListener('dragstart',()=>{drag=row;row.classList.add('dragging');});
        row.addEventListener('dragover',e=>{if(!drag||drag===row)return;e.preventDefault();row.classList.add('drop-target');});
        row.addEventListener('dragleave',()=>row.classList.remove('drop-target'));
        row.addEventListener('drop',e=>{
            e.preventDefault();
            if(!drag||drag===row)return;
            const rect=row.getBoundingClientRect();
            list.insertBefore(drag,e.clientY<rect.top+rect.height/2?row:row.nextSibling);
            row.classList.remove('drop-target');
            sync();refreshMoveButtons();
        });
        row.addEventListener('dragend',()=>{
            row.classList.remove('dragging');
            rows().forEach(x=>x.classList.remove('drop-target'));
            drag=null;
            sync();refreshMoveButtons();
        });
    });

    document.querySelectorAll('.home-mode').forEach(select=>{
        const settings=select.closest('.section-controls');
        const panel=settings?.querySelector('.selection-panel');
        const limit=settings?.querySelector('.number-field input');
        const boxes=()=>panel?[...panel.querySelectorAll('input[type=checkbox]')]:[];
        const refresh=()=>{
            if(!panel)return;
            const selected=boxes().filter(x=>x.checked);
            panel.querySelector('[data-count]').textContent=selected.length+' selected';
            const max=Math.max(1,Math.min(100,parseInt(limit?.value||'100',10)));
            boxes().forEach(x=>x.disabled=!x.checked&&selected.length>=max);
        };
        const syncMode=()=>{if(panel)panel.hidden=select.value!=='selected';refresh();};
        select.addEventListener('change',syncMode);
        limit?.addEventListener('input',refresh);
        panel?.querySelector('.picker-search')?.addEventListener('input',e=>{
            const q=e.target.value.toLowerCase().trim();
            panel.querySelectorAll('.picker-item').forEach(item=>item.hidden=!!q&&!item.dataset.search.includes(q));
        });
        panel?.addEventListener('change',e=>{if(e.target.matches('input[type=checkbox]'))refresh();});
        syncMode();
    });

    form.addEventListener('submit',sync);
    sync();
    refreshMoveButtons();
})();
</script>
@endpush
