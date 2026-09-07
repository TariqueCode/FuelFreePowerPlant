@extends('layouts.portal')

@section('title', $mode === 'create' ? 'Create Page' : 'Edit Page')

@section('content')
@php
    $initialBlocks = old('builder_blocks', $page->builder_blocks ?? []);
    if (is_string($initialBlocks)) {
        $initialBlocks = json_decode($initialBlocks, true) ?: [];
    }
@endphp

<div class="pb-page">
    <header class="pb-header">
        <div>
            <div class="pb-eyebrow"><i class="fa-solid fa-layer-group"></i> PAGE BUILDER</div>
            <h1>{{ $mode === 'create' ? 'Create a new page' : 'Edit page' }}</h1>
            <p>Build responsive pages from structured sections. Every published page automatically uses the live global framework, header and footer.</p>
        </div>
        <div class="pb-header-actions">
            <a class="pb-ghost" href="{{ route('admin.cms.index') }}"><i class="fa-solid fa-arrow-left"></i><span>All Pages</span></a>
            <button class="pb-primary" type="submit" form="page-builder-form"><i class="fa-solid fa-floppy-disk"></i><span>{{ $mode === 'create' ? 'Create Page' : 'Save Changes' }}</span></button>
        </div>
    </header>

    @if($errors->any())
        <div class="pb-alert"><i class="fa-solid fa-circle-exclamation"></i><div><strong>Could not save the page.</strong><span>{{ $errors->first() }}</span></div></div>
    @endif
    @if(session('status'))
        <div class="pb-success"><i class="fa-solid fa-circle-check"></i>{{ session('status') }}</div>
    @endif

    <form id="page-builder-form" method="POST" action="{{ $mode === 'create' ? route('admin.cms.store') : route('admin.cms.update', $page) }}">
        @csrf
        @if($mode === 'edit') @method('PATCH') @endif
        <input type="hidden" name="use_global_framework" value="1">
        <input type="hidden" name="use_global_header" value="1">
        <input type="hidden" name="use_global_footer" value="1">
        <input type="hidden" name="builder_blocks" id="builder-blocks" value="">

        <div class="pb-workspace">
            <aside class="pb-sidebar" aria-label="Page settings">
                <section class="pb-card">
                    <div class="pb-card-title"><i class="fa-regular fa-file-lines"></i><span>Page identity</span></div>
                    <label>Page title<input name="title" id="page-title" value="{{ old('title', $page->title) }}" maxlength="180" required placeholder="e.g. Our Technology"></label>
                    <label>URL slug<div class="pb-slug"><span>/pages/</span><input name="slug" id="page-slug" value="{{ old('slug', $page->slug) }}" maxlength="180" placeholder="our-technology"></div></label>
                    <label>Short introduction<textarea name="excerpt" id="page-excerpt" rows="4" maxlength="1000" placeholder="A concise introduction shown below the page title.">{{ old('excerpt', $page->excerpt) }}</textarea></label>
                </section>

                <section class="pb-card">
                    <div class="pb-card-title"><i class="fa-solid fa-magnifying-glass"></i><span>SEO</span></div>
                    <label>SEO title<input name="meta_title" value="{{ old('meta_title', $page->meta_title ?? '') }}" maxlength="255" placeholder="Optional search title"></label>
                    <label>SEO description<textarea name="meta_description" rows="4" maxlength="1000" placeholder="Optional search description">{{ old('meta_description', $page->meta_description ?? '') }}</textarea></label>
                </section>

                <section class="pb-card pb-framework-card">
                    <div class="pb-card-title"><i class="fa-solid fa-globe"></i><span>Global framework</span><span class="pb-live">LIVE</span></div>
                    <p>These pages are locked to the site's global design system so navigation, branding and footer updates stay synchronized everywhere.</p>
                    <div class="pb-framework-row"><i class="fa-solid fa-check"></i><span>Global Header</span><b>ON</b></div>
                    <div class="pb-framework-row"><i class="fa-solid fa-check"></i><span>Global Footer</span><b>ON</b></div>
                    <div class="pb-framework-row"><i class="fa-solid fa-check"></i><span>Global Theme / Framework</span><b>ON</b></div>
                </section>

                <section class="pb-card">
                    <div class="pb-card-title"><i class="fa-solid fa-bullhorn"></i><span>Publishing</span></div>
                    <label class="pb-publish"><input type="checkbox" name="is_published" value="1" @checked(old('is_published', $page->is_published))><span><strong>Publish page</strong><small>Only published pages are visible at their public URL.</small></span></label>
                </section>
            </aside>

            <main class="pb-main">
                <section class="pb-builder-card">
                    <div class="pb-builder-toolbar">
                        <div>
                            <div class="pb-card-title"><i class="fa-solid fa-wand-magic-sparkles"></i><span>Visual sections</span></div>
                            <small>Drag to reorder. Add only the sections you need.</small>
                        </div>
                        <button type="button" class="pb-add" id="add-section"><i class="fa-solid fa-plus"></i> Add Section</button>
                    </div>
                    <div id="sections" class="pb-sections" aria-live="polite"></div>
                    <div id="empty-state" class="pb-empty">
                        <div class="pb-empty-icon"><i class="fa-solid fa-cubes"></i></div>
                        <strong>Start with your first section</strong>
                        <span>Choose a ready-made section below. Your page will stay responsive on desktop, tablet and mobile.</span>
                        <button type="button" class="pb-empty-action" id="empty-add"><i class="fa-solid fa-plus"></i> Add First Section</button>
                    </div>
                </section>

                <section class="pb-preview-card">
                    <div class="pb-preview-head"><div><div class="pb-card-title"><i class="fa-solid fa-display"></i><span>Live preview</span></div><small>Preview of the page content area. Global header/footer are shown as framework boundaries.</small></div><span class="pb-preview-badge"><i class="fa-solid fa-circle"></i> Live</span></div>
                    <div class="pb-preview-frame">
                        <div class="pb-preview-global pb-preview-header"><span><i class="fa-solid fa-globe"></i> Global Header</span><small>Managed centrally</small></div>
                        <div class="pb-preview-content" id="preview"></div>
                        <div class="pb-preview-global pb-preview-footer"><span><i class="fa-solid fa-globe"></i> Global Footer</span><small>Managed centrally</small></div>
                    </div>
                </section>
            </main>
        </div>
    </form>
</div>
@endsection

@push('styles')
<style>
:root{--pb-bg:#06131c;--pb-surface:#091c28;--pb-surface-2:#0b2230;--pb-line:rgba(103,208,234,.14);--pb-line-strong:rgba(103,208,234,.28);--pb-text:#ecf9fb;--pb-muted:#7897a3;--pb-accent:#49d5ef;--pb-success:#42c99b;--pb-danger:#ff8797}
.pb-page{width:min(1440px,100%);margin:0 auto;padding:10px 0 48px;color:var(--pb-text)}
.pb-header{display:flex;justify-content:space-between;align-items:flex-end;gap:24px;padding:8px 0 22px}.pb-header h1{margin:5px 0 7px;font-size:clamp(25px,3vw,38px);letter-spacing:-.6px;line-height:1.1}.pb-header p{max-width:760px;margin:0;color:var(--pb-muted);font-size:12px;line-height:1.7}.pb-eyebrow{color:var(--pb-accent);font-size:9px;font-weight:900;letter-spacing:.16em;text-transform:uppercase}.pb-header-actions{display:flex;align-items:center;gap:8px;flex:0 0 auto}.pb-ghost,.pb-primary,.pb-add,.pb-empty-action{display:inline-flex;align-items:center;justify-content:center;gap:8px;border-radius:11px;min-height:42px;padding:0 14px;font-size:11px;font-weight:800;text-decoration:none;cursor:pointer}.pb-ghost{border:1px solid var(--pb-line);background:rgba(255,255,255,.025);color:#b9d1d8}.pb-primary{border:1px solid rgba(73,213,239,.3);background:linear-gradient(135deg,#28b6d6,#168da8);color:#fff;box-shadow:0 8px 22px rgba(17,151,183,.18)}.pb-alert,.pb-success{display:flex;align-items:flex-start;gap:10px;border-radius:12px;padding:12px 14px;margin-bottom:14px;font-size:11px}.pb-alert{background:rgba(255,87,108,.08);border:1px solid rgba(255,87,108,.18);color:#ffc0c9}.pb-alert strong,.pb-alert span{display:block}.pb-alert span{margin-top:2px;color:#ff9eaa}.pb-success{background:rgba(66,201,155,.08);border:1px solid rgba(66,201,155,.18);color:#9ce5c9}
.pb-workspace{display:grid;grid-template-columns:minmax(250px,300px) minmax(0,1fr);gap:16px;align-items:start}.pb-sidebar{display:grid;gap:12px;position:sticky;top:76px;min-width:0}.pb-card,.pb-builder-card,.pb-preview-card{border:1px solid var(--pb-line);background:linear-gradient(145deg,rgba(9,31,43,.96),rgba(5,19,28,.96));border-radius:16px;box-shadow:0 14px 35px rgba(0,0,0,.12)}.pb-card{padding:15px}.pb-card-title{display:flex;align-items:center;gap:8px;color:#e5f6f9;font-size:12px;font-weight:850}.pb-card-title i{color:var(--pb-accent);font-size:11px}.pb-card>label{display:block;margin-top:13px;color:#8eabb5;font-size:10px;font-weight:700}.pb-card input,.pb-card textarea{width:100%;box-sizing:border-box;margin-top:6px;border:1px solid var(--pb-line);background:#061824;color:#eaf8fb;border-radius:9px;padding:10px 11px;outline:none;font-size:12px;line-height:1.5}.pb-card input:focus,.pb-card textarea:focus{border-color:rgba(73,213,239,.5);box-shadow:0 0 0 3px rgba(73,213,239,.06)}.pb-card textarea{resize:vertical}.pb-slug{display:flex;align-items:center;margin-top:6px;border:1px solid var(--pb-line);background:#061824;border-radius:9px;overflow:hidden}.pb-slug span{padding-left:10px;color:#527481;font-size:10px;white-space:nowrap}.pb-slug input{border:0;margin:0;background:transparent;border-radius:0;padding-left:4px}.pb-framework-card{background:linear-gradient(145deg,rgba(23,58,65,.32),rgba(5,23,30,.95))}.pb-framework-card p{margin:9px 0 12px;color:#718f99;font-size:9px;line-height:1.6}.pb-live{margin-left:auto;padding:3px 6px;border-radius:999px;background:rgba(66,201,155,.1);border:1px solid rgba(66,201,155,.18);color:#74ddb8;font-size:7px;letter-spacing:.08em}.pb-framework-row{display:flex;align-items:center;gap:8px;padding:8px 0;border-top:1px solid rgba(103,208,234,.08);font-size:10px;color:#b3cbd2}.pb-framework-row i{color:var(--pb-success);font-size:9px}.pb-framework-row b{margin-left:auto;color:#74ddb8;font-size:8px}.pb-publish{display:flex!important;align-items:flex-start;gap:10px;padding:10px;border:1px solid var(--pb-line);border-radius:10px;background:rgba(255,255,255,.02);cursor:pointer}.pb-publish input{width:16px!important;height:16px!important;margin:1px 0 0!important;accent-color:var(--pb-accent)}.pb-publish span{margin:0}.pb-publish strong{display:block;color:#dceff3;font-size:11px}.pb-publish small{display:block;margin-top:3px;color:#6f8c97;font-size:8px;line-height:1.4}
.pb-main{display:grid;gap:16px;min-width:0}.pb-builder-card{overflow:hidden}.pb-builder-toolbar,.pb-preview-head{display:flex;align-items:center;justify-content:space-between;gap:14px;padding:15px 17px;border-bottom:1px solid var(--pb-line)}.pb-builder-toolbar small,.pb-preview-head small{display:block;margin-top:4px;color:#698894;font-size:9px;line-height:1.45}.pb-add{border:1px solid rgba(73,213,239,.25);background:rgba(73,213,239,.08);color:#a9ebf7}.pb-sections{display:grid;gap:10px;padding:12px}.pb-empty{display:grid;justify-items:center;text-align:center;padding:48px 22px;color:#76939e}.pb-empty-icon{width:52px;height:52px;display:grid;place-items:center;border-radius:15px;background:rgba(73,213,239,.07);border:1px solid rgba(73,213,239,.15);color:var(--pb-accent);font-size:20px;margin-bottom:12px}.pb-empty strong{color:#dceff3;font-size:14px}.pb-empty span{max-width:460px;margin:6px 0 15px;font-size:9px;line-height:1.6}.pb-empty-action{min-height:36px;border:1px solid rgba(73,213,239,.25);background:rgba(73,213,239,.08);color:#a9ebf7;font-size:9px}
.pb-section{border:1px solid rgba(103,208,234,.13);border-radius:13px;background:#071b27;overflow:hidden}.pb-section.dragging{opacity:.5}.pb-section-head{display:grid;grid-template-columns:auto minmax(0,1fr) auto;align-items:center;gap:9px;padding:10px 12px;background:rgba(255,255,255,.018);border-bottom:1px solid rgba(103,208,234,.09)}.pb-drag{color:#56d4ed;cursor:grab;font-size:12px;padding:5px}.pb-section-name{min-width:0}.pb-section-name strong{display:block;font-size:11px;color:#dceff3}.pb-section-name small{display:block;color:#64818c;font-size:8px;margin-top:2px}.pb-section-tools{display:flex;align-items:center;gap:4px}.pb-icon-btn{width:30px;height:30px;display:grid;place-items:center;border:1px solid transparent;border-radius:8px;background:transparent;color:#7595a0;cursor:pointer}.pb-icon-btn:hover{background:rgba(73,213,239,.06);border-color:var(--pb-line);color:#c7e6ec}.pb-icon-btn.danger:hover{color:#ff9aaa;background:rgba(255,87,108,.06);border-color:rgba(255,87,108,.14)}.pb-section-body{display:none;padding:12px}.pb-section.open .pb-section-body{display:block}.pb-grid{display:grid;grid-template-columns:repeat(2,minmax(0,1fr));gap:10px}.pb-grid.full{grid-template-columns:1fr}.pb-field{min-width:0}.pb-field label{display:block;color:#8ba8b1;font-size:9px;font-weight:700;margin-bottom:5px}.pb-field input,.pb-field textarea,.pb-field select{width:100%;box-sizing:border-box;border:1px solid var(--pb-line);background:#061824;color:#eaf8fb;border-radius:8px;padding:9px 10px;outline:none;font-size:11px}.pb-field textarea{resize:vertical;min-height:80px;line-height:1.55}.pb-field input:focus,.pb-field textarea:focus,.pb-field select:focus{border-color:rgba(73,213,239,.45)}.pb-field small{display:block;color:#5f7e89;font-size:8px;line-height:1.45;margin-top:4px}.pb-check{display:flex!important;align-items:center;gap:7px;color:#b7d1d8!important;cursor:pointer}.pb-check input{width:15px!important;height:15px!important;accent-color:var(--pb-accent)}.pb-style-row{display:flex;gap:6px;flex-wrap:wrap}.pb-style-row button{border:1px solid var(--pb-line);background:#061824;color:#7897a3;border-radius:8px;padding:7px 9px;font-size:8px;cursor:pointer}.pb-style-row button.active{border-color:rgba(73,213,239,.4);background:rgba(73,213,239,.1);color:#d9f6fb}.pb-items{display:grid;gap:8px;margin-top:8px}.pb-item{display:grid;grid-template-columns:minmax(0,1fr) auto;gap:8px;padding:9px;border:1px solid rgba(103,208,234,.09);border-radius:9px;background:rgba(255,255,255,.018)}.pb-item-fields{display:grid;grid-template-columns:repeat(2,minmax(0,1fr));gap:7px}.pb-item-remove{border:0;background:transparent;color:#d77f8c;cursor:pointer;font-size:9px}.pb-add-item{margin-top:8px;border:1px dashed rgba(73,213,239,.22);background:transparent;color:#7fcfe0;border-radius:8px;padding:7px 9px;font-size:8px;cursor:pointer}
.pb-preview-card{overflow:hidden}.pb-preview-badge{display:inline-flex;align-items:center;gap:5px;color:#73ddb8;font-size:8px;font-weight:800;text-transform:uppercase;letter-spacing:.08em}.pb-preview-badge i{font-size:5px}.pb-preview-frame{margin:12px;border:1px solid rgba(103,208,234,.12);border-radius:12px;overflow:hidden;background:#04121b}.pb-preview-global{display:flex;justify-content:space-between;align-items:center;gap:10px;padding:8px 11px;background:rgba(73,213,239,.045);color:#75c6d5;font-size:8px}.pb-preview-global span{font-weight:800}.pb-preview-global small{color:#527783;font-size:7px}.pb-preview-content{display:grid;gap:10px;padding:12px}.pb-preview-section{min-width:0;border:1px solid rgba(103,208,234,.13);border-radius:12px;background:linear-gradient(145deg,rgba(8,38,52,.85),rgba(3,20,29,.92));overflow:hidden}.pb-preview-inner{padding:clamp(18px,3vw,30px)}.pb-preview-kicker{font-size:7px;color:#63d9ec;font-weight:900;letter-spacing:.16em;text-transform:uppercase}.pb-preview-title{margin:5px 0 7px;color:#effcff;font-size:clamp(19px,3vw,31px);line-height:1.1}.pb-preview-copy{color:#9dbac3;font-size:10px;line-height:1.65}.pb-preview-hero{display:grid;grid-template-columns:minmax(0,1.1fr) minmax(150px,.9fr);align-items:stretch}.pb-preview-media{min-height:150px;background:linear-gradient(135deg,rgba(73,213,239,.1),rgba(114,223,191,.04));display:grid;place-items:center;color:#5d8792}.pb-preview-media img{width:100%;height:100%;min-height:150px;object-fit:cover;display:block}.pb-preview-split{display:grid;grid-template-columns:1fr 1fr;align-items:center}.pb-preview-image img{display:block;width:100%;max-height:320px;object-fit:cover}.pb-preview-cards{display:grid;grid-template-columns:repeat(3,minmax(0,1fr));gap:8px;padding:10px}.pb-preview-card-item{padding:11px;border:1px solid rgba(103,208,234,.11);border-radius:9px;background:rgba(73,213,239,.025)}.pb-preview-card-item strong{display:block;color:#e6f7fa;font-size:10px}.pb-preview-card-item span{display:block;color:#7897a3;font-size:8px;line-height:1.5;margin-top:4px}.pb-preview-stats{display:grid;grid-template-columns:repeat(4,minmax(0,1fr));gap:8px;padding:12px}.pb-preview-stat{text-align:center;padding:10px;border-radius:9px;background:rgba(73,213,239,.035);border:1px solid rgba(103,208,234,.1)}.pb-preview-stat strong{display:block;color:#effcff;font-size:17px}.pb-preview-stat span{display:block;color:#6f919d;font-size:7px;margin-top:3px}.pb-preview-cta{text-align:center;padding:22px}.pb-preview-button{display:inline-flex;padding:8px 13px;border-radius:8px;background:#2caeca;color:#fff;font-size:8px;font-weight:800}.pb-preview-video{min-height:150px;display:grid;place-items:center;color:#6e9aa6;background:repeating-linear-gradient(135deg,rgba(73,213,239,.035),rgba(73,213,239,.035) 8px,rgba(73,213,239,.02) 8px,rgba(73,213,239,.02) 16px)}.pb-preview-divider{height:1px;margin:10px 20px;background:linear-gradient(90deg,transparent,rgba(73,213,239,.35),transparent)}.pb-preview-rich{padding:18px;color:#a3c0c8;font-size:10px;line-height:1.75}.pb-preview-rich h2{color:#eaf8fb;font-size:17px}.pb-preview-rich ul{padding-left:18px}.pb-preview-rich a{color:#64dff1}.pb-tone-dark{background:linear-gradient(145deg,rgba(5,24,34,.96),rgba(2,13,20,.98))}.pb-tone-accent{background:linear-gradient(145deg,rgba(20,76,87,.6),rgba(5,25,32,.96));border-color:rgba(73,213,239,.25)}.pb-tone-light{background:linear-gradient(145deg,rgba(28,51,58,.9),rgba(12,29,36,.98))}.pb-align-center{text-align:center}.pb-align-center .pb-preview-inner{margin-inline:auto}.pb-align-right{text-align:right}
@media(max-width:1100px){.pb-workspace{grid-template-columns:250px minmax(0,1fr)}.pb-preview-cards{grid-template-columns:repeat(2,minmax(0,1fr))}}
@media(max-width:820px){.pb-header{align-items:flex-start;flex-direction:column}.pb-header-actions{width:100%}.pb-header-actions>*{flex:1}.pb-workspace{grid-template-columns:1fr}.pb-sidebar{position:static;grid-template-columns:repeat(2,minmax(0,1fr))}.pb-sidebar .pb-framework-card,.pb-sidebar .pb-publish{grid-column:span 1}.pb-grid{grid-template-columns:1fr}.pb-item-fields{grid-template-columns:1fr}}
@media(max-width:600px){.pb-page{padding-top:0}.pb-header{padding-bottom:15px}.pb-header h1{font-size:26px}.pb-header p{font-size:11px}.pb-header-actions{display:grid;grid-template-columns:1fr 1fr}.pb-ghost,.pb-primary{min-height:40px}.pb-sidebar{grid-template-columns:1fr}.pb-builder-toolbar,.pb-preview-head{align-items:flex-start;flex-direction:column}.pb-add{width:100%}.pb-section-head{grid-template-columns:auto minmax(0,1fr) auto}.pb-section-tools .pb-icon-btn{width:28px;height:28px}.pb-preview-hero,.pb-preview-split{grid-template-columns:1fr}.pb-preview-cards{grid-template-columns:1fr}.pb-preview-stats{grid-template-columns:repeat(2,minmax(0,1fr))}.pb-preview-inner{padding:17px}.pb-preview-frame{margin:8px}.pb-preview-global{align-items:flex-start;flex-direction:column;gap:2px}}
@media(max-width:380px){.pb-header-actions{grid-template-columns:1fr}.pb-item{grid-template-columns:1fr}.pb-page{padding-inline:0}}
</style>
@endpush

@push('scripts')
<script>
(() => {
    const initial = @json($initialBlocks);
    const sections = document.getElementById('sections');
    const empty = document.getElementById('empty-state');
    const addButton = document.getElementById('add-section');
    const emptyAdd = document.getElementById('empty-add');
    const hidden = document.getElementById('builder-blocks');
    const preview = document.getElementById('preview');
    const titleInput = document.getElementById('page-title');
    const excerptInput = document.getElementById('page-excerpt');

    const catalog = {
        hero: {label:'Hero', icon:'fa-solid fa-rocket', help:'High-impact opening section', defaults:{eyebrow:'FEATURED',title:'',content:'',image:'',image_alt:'',button_text:'',button_url:'',tone:'dark',align:'left',visible:true}},
        rich_text: {label:'Rich Text', icon:'fa-solid fa-align-left', help:'Editorial content and formatted HTML', defaults:{eyebrow:'',title:'',content:'',tone:'dark',align:'left',visible:true}},
        image: {label:'Image', icon:'fa-regular fa-image', help:'Full-width visual with caption', defaults:{title:'',content:'',image:'',image_alt:'',tone:'dark',align:'left',visible:true}},
        split: {label:'Image + Content', icon:'fa-solid fa-table-columns', help:'Two-column story section', defaults:{eyebrow:'',title:'',content:'',image:'',image_alt:'',button_text:'',button_url:'',layout:'image-left',tone:'dark',align:'left',visible:true}},
        cards: {label:'Cards', icon:'fa-solid fa-grip', help:'Repeatable feature cards', defaults:{eyebrow:'',title:'',content:'',columns:3,tone:'dark',items:[{title:'Feature title',content:'Feature description.'},{title:'Another feature',content:'Supporting description.'},{title:'Third feature',content:'Supporting description.'}],visible:true}},
        stats: {label:'Stats', icon:'fa-solid fa-chart-simple', help:'Numbers, metrics or milestones', defaults:{title:'',content:'',tone:'accent',items:[{value:'100%',label:'Clean output'},{value:'24/7',label:'Availability'},{value:'0',label:'Fuel required'},{value:'∞',label:'Scalable'}],visible:true}},
        cta: {label:'Call to Action', icon:'fa-solid fa-bullhorn', help:'Focused conversion section', defaults:{eyebrow:'NEXT STEP',title:'Ready to explore?',content:'Connect with our team to learn more.',button_text:'Get in touch',button_url:'/contact',tone:'accent',align:'center',visible:true}},
        video: {label:'Video', icon:'fa-solid fa-circle-play', help:'Responsive embedded video', defaults:{title:'',content:'',url:'',tone:'dark',visible:true}},
        divider: {label:'Divider', icon:'fa-solid fa-minus', help:'Visual spacing between sections', defaults:{tone:'dark',visible:true}}
    };

    let blocks = Array.isArray(initial) ? initial.map(normalize) : [];
    let draggedIndex = null;

    function normalize(block){
        const type = catalog[block?.type] ? block.type : 'rich_text';
        const base = JSON.parse(JSON.stringify(catalog[type].defaults));
        const merged = Object.assign(base, block || {}, {type});
        if (Array.isArray(base.items)) merged.items = Array.isArray(block?.items) && block.items.length ? block.items.map(x => Object.assign({}, base.items[0] || {}, x || {})) : base.items;
        return merged;
    }

    function esc(value){
        return String(value ?? '').replace(/[&<>"']/g, c => ({'&':'&amp;','<':'&lt;','>':'&gt;','"':'&quot;',"'":'&#039;'}[c]));
    }
    function safeUrl(value){
        const raw = String(value ?? '').trim();
        if (!raw) return '#';
        if (/^(javascript|data|vbscript):/i.test(raw)) return '#';
        return raw;
    }
    function htmlPreview(value){
        const raw = String(value ?? '').trim();
        if (!raw) return '';
        return raw.replace(/<script[\s\S]*?<\/script>/gi,'').slice(0,9000);
    }
    function setField(block, key, value){ block[key] = value; sync(false); }

    function field(label, key, value, type='text', placeholder=''){
        const control = type === 'textarea'
            ? `<textarea data-field="${key}" rows="4" placeholder="${esc(placeholder)}">${esc(value)}</textarea>`
            : `<input data-field="${key}" type="${type}" value="${esc(value)}" placeholder="${esc(placeholder)}">`;
        return `<div class="pb-field"><label>${esc(label)}</label>${control}</div>`;
    }
    function selectField(label,key,value,options){
        return `<div class="pb-field"><label>${esc(label)}</label><select data-field="${key}">${options.map(([v,l])=>`<option value="${esc(v)}" ${value===v?'selected':''}>${esc(l)}</option>`).join('')}</select></div>`;
    }
    function visibleField(block){
        return `<label class="pb-field pb-check"><input data-field="visible" type="checkbox" ${block.visible !== false ? 'checked' : ''}><span>Show this section</span></label>`;
    }
    function commonFields(block, includeTitle=true){
        let out = '<div class="pb-grid">';
        if (includeTitle) out += field('Eyebrow','eyebrow',block.eyebrow || '','text','Optional small label');
        out += field('Section title','title',block.title || '','text','Section heading');
        out += selectField('Tone','tone',block.tone || 'dark',[['dark','Dark'],['accent','Accent'],['light','Soft']]);
        out += selectField('Alignment','align',block.align || 'left',[['left','Left'],['center','Center'],['right','Right']]);
        out += '</div>';
        return out;
    }
    function renderItems(block){
        const items = Array.isArray(block.items) ? block.items : [];
        return `<div class="pb-field pb-field-items"><label>Items</label><div class="pb-items">${items.map((item,j)=>`<div class="pb-item" data-item="${j}"><div class="pb-item-fields">${field('Title','title',item.title || '','text','Card title').replace('data-field="title"','data-item-field="title"')}${field('Description','content',item.content || '','textarea','Card description').replace('data-field="content"','data-item-field="content"')}${block.type==='stats'?field('Value','value',item.value || '','text','100%').replace('data-field="value"','data-item-field="value"'):''}</div><button type="button" class="pb-item-remove" data-remove-item="${j}" aria-label="Remove item"><i class="fa-solid fa-xmark"></i></button></div>`).join('')}</div><button type="button" class="pb-add-item" data-add-item><i class="fa-solid fa-plus"></i> Add item</button></div>`;
    }

    function sectionBody(block){
        if(block.type==='divider') return `<div class="pb-grid full">${visibleField(block)}</div>`;
        if(block.type==='video') return `${commonFields(block,false)}<div class="pb-grid full">${field('Video embed URL','url',block.url || '','url','https://www.youtube.com/embed/...')}${field('Caption / context','content',block.content || '','textarea','Optional supporting text')}${visibleField(block)}</div>`;
        if(block.type==='image') return `${commonFields(block)}<div class="pb-grid">${field('Image URL','image',block.image || '','url','https://...')}${field('Alt text','image_alt',block.image_alt || block.title || '','text','Accessible image description')}</div><div class="pb-grid full">${field('Caption / content','content',block.content || '','textarea','Optional caption or supporting copy')}${visibleField(block)}</div>`;
        if(block.type==='split') return `${commonFields(block)}<div class="pb-grid">${field('Image URL','image',block.image || '','url','https://...')}${field('Alt text','image_alt',block.image_alt || block.title || '','text','Accessible image description')}${selectField('Image position','layout',block.layout || 'image-left',[['image-left','Image left'],['image-right','Image right']])}${field('Button text','button_text',block.button_text || '','text','Optional CTA')}</div><div class="pb-grid full">${field('Content','content',block.content || '','textarea','Write the section copy. Basic HTML is supported.')}${field('Button URL','button_url',block.button_url || '','url','/contact')}${visibleField(block)}</div>`;
        if(block.type==='cards') return `${commonFields(block)}<div class="pb-grid">${selectField('Columns','columns',String(block.columns || 3),[['2','2 columns'],['3','3 columns'],['4','4 columns']])}</div>${renderItems(block)}${visibleField(block)}`;
        if(block.type==='stats') return `${commonFields(block)}${renderItems(block)}${visibleField(block)}`;
        if(block.type==='cta') return `${commonFields(block)}<div class="pb-grid">${field('Button text','button_text',block.button_text || '','text','Get in touch')}${field('Button URL','button_url',block.button_url || '','url','/contact')}</div><div class="pb-grid full">${field('Content','content',block.content || '','textarea','Short supporting message')}${visibleField(block)}</div>`;
        return `${commonFields(block)}<div class="pb-grid full">${field('Content','content',block.content || '','textarea','Write content. Basic HTML is supported.')}${visibleField(block)}</div>`;
    }

    function renderEditor(){
        sections.innerHTML='';
        empty.style.display = blocks.length ? 'none' : 'grid';
        blocks.forEach((block,index)=>{
            const meta=catalog[block.type];
            const el=document.createElement('article');
            el.className='pb-section open';
            el.draggable=true;
            el.dataset.index=index;
            el.innerHTML=`<div class="pb-section-head"><span class="pb-drag" title="Drag to reorder"><i class="fa-solid fa-grip-vertical"></i></span><div class="pb-section-name"><strong>${esc(meta.label)}</strong><small>${esc(meta.help)}</small></div><div class="pb-section-tools"><button type="button" class="pb-icon-btn" data-duplicate title="Duplicate"><i class="fa-regular fa-copy"></i></button><button type="button" class="pb-icon-btn" data-toggle title="Collapse"><i class="fa-solid fa-chevron-up"></i></button><button type="button" class="pb-icon-btn danger" data-remove title="Remove"><i class="fa-regular fa-trash-can"></i></button></div></div><div class="pb-section-body">${sectionBody(block)}</div>`;
            el.querySelector('[data-toggle]').onclick=()=>{el.classList.toggle('open');el.querySelector('[data-toggle] i').className=el.classList.contains('open')?'fa-solid fa-chevron-up':'fa-solid fa-chevron-down';};
            el.querySelector('[data-remove]').onclick=()=>{blocks.splice(index,1);sync();};
            el.querySelector('[data-duplicate]').onclick=()=>{blocks.splice(index+1,0,JSON.parse(JSON.stringify(block)));sync();};
            el.querySelectorAll('[data-field]').forEach(control=>{
                const key=control.dataset.field;
                const handler=()=>{block[key]=control.type==='checkbox'?control.checked:control.value; sync(false);};
                control.addEventListener(control.type==='checkbox'?'change':'input',handler);
                if(control.tagName==='SELECT') control.addEventListener('change',handler);
            });
            el.querySelectorAll('[data-item-field]').forEach(control=>{
                const item=block.items?.[Number(control.closest('[data-item]').dataset.item)];
                const key=control.dataset.itemField;
                if(!item)return;
                const handler=()=>{item[key]=control.value;sync(false);};
                control.addEventListener('input',handler);
            });
            el.querySelector('[data-add-item]')?.addEventListener('click',()=>{if(!Array.isArray(block.items))block.items=[];block.items.push(block.type==='stats'?{value:'',label:'Metric'}:{title:'New card',content:'Description'});sync();});
            el.querySelectorAll('[data-remove-item]').forEach(btn=>btn.addEventListener('click',()=>{block.items.splice(Number(btn.dataset.removeItem),1);sync();}));
            el.addEventListener('dragstart',()=>{draggedIndex=index;el.classList.add('dragging');});
            el.addEventListener('dragend',()=>{draggedIndex=null;el.classList.remove('dragging');});
            el.addEventListener('dragover',e=>{e.preventDefault();});
            el.addEventListener('drop',e=>{e.preventDefault();const target=index;if(draggedIndex===null||draggedIndex===target)return;const moved=blocks.splice(draggedIndex,1)[0];blocks.splice(target,0,moved);sync();});
            sections.appendChild(el);
        });
    }

    function previewBlock(block){
        if(block.visible===false)return '';
        const tone=`pb-tone-${esc(block.tone || 'dark')}`;
        const align=`pb-align-${esc(block.align || 'left')}`;
        const kicker=block.eyebrow?`<div class="pb-preview-kicker">${esc(block.eyebrow)}</div>`:'';
        const title=block.title?`<h2 class="pb-preview-title">${esc(block.title)}</h2>`:'';
        const content=block.content?`<div class="pb-preview-copy">${htmlPreview(block.content)}</div>`:'';
        if(block.type==='divider') return '<div class="pb-preview-divider"></div>';
        if(block.type==='hero') return `<section class="pb-preview-section ${tone} ${align}"><div class="pb-preview-hero"><div class="pb-preview-inner">${kicker}${title}${content}${block.button_text?`<a class="pb-preview-button" href="${esc(safeUrl(block.button_url))}">${esc(block.button_text)}</a>`:''}</div><div class="pb-preview-media">${block.image?`<img src="${esc(safeUrl(block.image))}" alt="${esc(block.image_alt || block.title || '')}">`:'<i class="fa-regular fa-image fa-2x"></i>'}</div></div></section>`;
        if(block.type==='rich_text') return `<section class="pb-preview-section ${tone}"><div class="pb-preview-rich ${align}">${kicker}${title}${htmlPreview(block.content) || '<span>Add rich content to preview it here.</span>'}</div></section>`;
        if(block.type==='image') return `<section class="pb-preview-section ${tone}"><div class="pb-preview-image">${block.image?`<img src="${esc(safeUrl(block.image))}" alt="${esc(block.image_alt || block.title || '')}">`: '<div class="pb-preview-media"><i class="fa-regular fa-image fa-2x"></i></div>'}</div>${title||content?`<div class="pb-preview-inner ${align}">${title}${content}</div>`:''}</section>`;
        if(block.type==='split') {const media=`<div class="pb-preview-media">${block.image?`<img src="${esc(safeUrl(block.image))}" alt="${esc(block.image_alt || block.title || '')}">`:'<i class="fa-regular fa-image fa-2x"></i>'}</div>`;const copy=`<div class="pb-preview-inner ${align}">${kicker}${title}${content}${block.button_text?`<a class="pb-preview-button" href="${esc(safeUrl(block.button_url))}">${esc(block.button_text)}</a>`:''}</div>`;return `<section class="pb-preview-section ${tone}"><div class="pb-preview-split">${block.layout==='image-right'?copy+media:media+copy}</div></section>`;}
        if(block.type==='cards'){const items=Array.isArray(block.items)?block.items:[];return `<section class="pb-preview-section ${tone}"><div class="pb-preview-inner ${align}">${kicker}${title}${content}</div><div class="pb-preview-cards" style="grid-template-columns:repeat(${Math.min(4,Math.max(2,Number(block.columns)||3))},minmax(0,1fr))">${items.map(i=>`<div class="pb-preview-card-item"><strong>${esc(i.title || 'Card')}</strong><span>${esc(i.content || '')}</span></div>`).join('')}</div></section>`;}
        if(block.type==='stats'){const items=Array.isArray(block.items)?block.items:[];return `<section class="pb-preview-section ${tone}"><div class="pb-preview-inner ${align}">${kicker}${title}${content}</div><div class="pb-preview-stats">${items.map(i=>`<div class="pb-preview-stat"><strong>${esc(i.value || '—')}</strong><span>${esc(i.label || '')}</span></div>`).join('')}</div></section>`;}
        if(block.type==='cta') return `<section class="pb-preview-section ${tone}"><div class="pb-preview-cta ${align}">${kicker}${title}${content}<div><a class="pb-preview-button" href="${esc(safeUrl(block.button_url))}">${esc(block.button_text || 'Learn more')}</a></div></div></section>`;
        if(block.type==='video') return `<section class="pb-preview-section ${tone}"><div class="pb-preview-video"><i class="fa-solid fa-circle-play"></i>${block.url?`<span>${esc(block.url)}</span>`:'<span>Add a video embed URL</span>'}</div>${title||content?`<div class="pb-preview-inner ${align}">${title}${content}</div>`:''}</section>`;
        return '';
    }

    function renderPreview(){
        const pageTitle=titleInput?.value?.trim() || 'Untitled page';
        const excerpt=excerptInput?.value?.trim();
        preview.innerHTML=`<div class="pb-preview-section pb-tone-dark"><div class="pb-preview-inner"><div class="pb-preview-kicker">PAGE</div><h2 class="pb-preview-title">${esc(pageTitle)}</h2>${excerpt?`<div class="pb-preview-copy">${esc(excerpt)}</div>`:''}</div></div>${blocks.map(previewBlock).join('') || '<div class="pb-preview-section"><div class="pb-preview-inner"><div class="pb-preview-copy">Your sections will appear here as you build the page.</div></div></div>'}`;
    }

    function sync(full=true){
        hidden.value=JSON.stringify(blocks);
        if(full) renderEditor();
        renderPreview();
    }

    function add(type='hero'){
        blocks.push(normalize({type}));
        sync();
        requestAnimationFrame(()=>sections.lastElementChild?.scrollIntoView({behavior:'smooth',block:'center'}));
    }
    addButton?.addEventListener('click',()=>add('hero'));
    emptyAdd?.addEventListener('click',()=>add('hero'));
    titleInput?.addEventListener('input',renderPreview);
    excerptInput?.addEventListener('input',renderPreview);

    addButton?.addEventListener('contextmenu',e=>e.preventDefault());
    const builderToolbar = document.querySelector('.pb-builder-toolbar');
    if(builderToolbar){
        builderToolbar.addEventListener('click',e=>{
            if(e.target.closest('#add-section'))return;
        });
    }

    // Replace the single add action with a compact section chooser.
    addButton?.addEventListener('click', function(e){
        e.stopImmediatePropagation();
        const existing=document.querySelector('.pb-chooser');
        if(existing){existing.remove();return;}
        const chooser=document.createElement('div');
        chooser.className='pb-chooser';
        chooser.innerHTML=Object.entries(catalog).map(([key,item])=>`<button type="button" data-add-type="${key}"><i class="${item.icon}"></i><span><strong>${esc(item.label)}</strong><small>${esc(item.help)}</small></span></button>`).join('');
        document.querySelector('.pb-builder-toolbar').appendChild(chooser);
        chooser.querySelectorAll('[data-add-type]').forEach(btn=>btn.addEventListener('click',()=>{add(btn.dataset.addType);chooser.remove();}));
    }, true);

    sync();
})();
</script>
<style>
.pb-builder-toolbar{position:relative}.pb-chooser{position:absolute;right:16px;top:68px;width:min(390px,calc(100% - 32px));display:grid;grid-template-columns:1fr 1fr;gap:6px;padding:8px;border:1px solid rgba(103,208,234,.2);border-radius:12px;background:#061720;box-shadow:0 18px 45px rgba(0,0,0,.4);z-index:30}.pb-chooser button{display:flex;align-items:flex-start;gap:8px;text-align:left;border:1px solid rgba(103,208,234,.09);background:rgba(255,255,255,.02);border-radius:9px;padding:9px;color:#c8e3e8;cursor:pointer}.pb-chooser button:hover{border-color:rgba(73,213,239,.3);background:rgba(73,213,239,.06)}.pb-chooser button>i{width:24px;height:24px;display:grid;place-items:center;color:#55d9ef;background:rgba(73,213,239,.08);border-radius:7px;font-size:10px}.pb-chooser strong,.pb-chooser small{display:block}.pb-chooser strong{font-size:9px}.pb-chooser small{color:#64828d;font-size:7px;line-height:1.35;margin-top:2px}@media(max-width:520px){.pb-chooser{grid-template-columns:1fr;right:8px;top:108px;width:calc(100% - 16px)}}
</style>
@endpush
