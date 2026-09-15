@extends('layouts.portal')
@section('title', $mode === 'create' ? 'Create News & Event' : 'Edit News & Event')
@section('content')
<div class="ne-wrap">
<header class="ne-header">
    <div><div class="ne-eyebrow">WEBSITE · NEWS & EVENT</div><h1>{{ $mode === 'create' ? 'Create News / Event' : 'Edit News / Event' }}</h1><p>Build the publication exactly around the information visitors see in the News & Event list.</p></div>
    <div class="ne-actions"><a class="ne-btn ne-btn-ghost" href="{{ route('admin.news_and_event.index') }}"><i class="fa-solid fa-arrow-left"></i> Back</a><button class="ne-btn ne-btn-primary" type="submit" form="news-event-form"><i class="fa-solid fa-floppy-disk"></i> Save</button></div>
</header>
@if($errors->any())<div class="ne-alert"><i class="fa-solid fa-circle-exclamation"></i><span>{{ $errors->first() }}</span></div>@endif
<form id="news-event-form" method="POST" enctype="multipart/form-data" action="{{ $mode === 'create' ? route('admin.news_and_event.store') : route('admin.news_and_event.update',$item) }}">@csrf @if($mode === 'edit')@method('PATCH')@endif
<div class="ne-layout">
    <main class="ne-editor-area">
        <section class="ne-card ne-editor-card">
            <div class="ne-card-head ne-editor-head"><div><span>CANONICAL EDITOR</span><h2>Global CMS Editor</h2><p>Rich text, media, embeds, tables, CTA buttons, columns, HTML source and preview.</p></div><i class="fa-solid fa-layer-group"></i></div>
            <div class="ne-editor-body"><textarea id="global-cms-content" name="content" data-global-cms-content hidden>{{ old('content',$item->content ?? '') }}</textarea></div>
        </section>
    </main>
    <section class="ne-info-grid">
        <section class="ne-card ne-identity">
            <div class="ne-card-head"><div><span>STORY DETAILS</span><h2>Publication identity</h2></div><i class="fa-regular fa-newspaper"></i></div>
            <div class="ne-form-grid">
                <label class="full">Title<input name="title" required maxlength="180" value="{{ old('title',$item->title) }}" placeholder="Publication title"></label>
                <label>Type<select name="type"><option value="news" @selected(old('type',$item->type)==='news')>News</option><option value="announcement" @selected(old('type',$item->type)==='announcement')>Event</option></select></label>
                <label>Slug<input name="slug" maxlength="180" value="{{ old('slug',$item->slug) }}" placeholder="Auto-generated if blank"><small>Leave blank to generate automatically.</small></label>
                <label class="full">Introduction<textarea name="excerpt" maxlength="1000" rows="5" placeholder="Short summary shown before readers open the story">{{ old('excerpt',$item->excerpt) }}</textarea><small>This matches the short introduction used to summarize the publication.</small></label>
            </div>
        </section>
        <section class="ne-card ne-seo">
            <div class="ne-card-head"><div><span>SEARCH</span><h2>SEO</h2></div><i class="fa-solid fa-magnifying-glass"></i></div>
            <label>Meta title<input name="meta_title" maxlength="255" value="{{ old('meta_title',$item->meta_title ?? '') }}" placeholder="Meta title for search engines"></label>
            <label>Meta description<textarea name="meta_description" maxlength="1000" rows="5" placeholder="Meta description for search engines">{{ old('meta_description',$item->meta_description ?? '') }}</textarea><small>This description helps search engines understand your content.</small></label>
        </section>
        <section class="ne-card ne-publishing">
            <div class="ne-card-head"><div><span>PUBLICATION</span><h2>Publishing options</h2></div><i class="fa-regular fa-paper-plane"></i></div>
            <label class="ne-check"><input type="checkbox" name="is_featured" value="1" @checked(old('is_featured',$item->is_featured))><span><strong>Featured item</strong><small>Use as the lead story when supported by the public view.</small></span></label>
            <label>Status<select name="status"><option value="draft" @selected(old('status',$item->status)==='draft')>Draft</option><option value="published" @selected(old('status',$item->status)==='published')>Published</option></select></label>
        </section>
    </section>
    <section class="ne-card ne-media-card">
        <div class="ne-card-head"><div><span>VISUAL</span><h2>Thumbnail &amp; media</h2></div><i class="fa-regular fa-image"></i></div>
        <div class="ne-media-layout">
            <div class="ne-thumb" id="thumbnail-preview">
                @if($item->image_path)<img src="{{ asset('storage/'.$item->image_path) }}" alt="{{ $item->cover_alt ?: $item->title }}">@else<div class="ne-thumb-empty"><i class="fa-regular fa-image"></i><strong>No thumbnail</strong><small>Use a square image for the best match with the public list.</small></div>@endif
            </div>
            <div class="ne-media-fields">
                <label class="ne-upload"><input id="cover_image" type="file" name="cover_image" accept="image/jpeg,image/png,image/webp,image/gif"><span><i class="fa-solid fa-cloud-arrow-up"></i> {{ $item->image_path ? 'Replace thumbnail' : 'Add thumbnail' }}</span><small>JPG, PNG, WebP or GIF · max {{ (int) config('fuelfree.upload.max_mb', 50) }} MB</small></label>
                @if($item->image_path)<label class="ne-remove"><input type="checkbox" name="remove_image" value="1" @checked(old('remove_image'))><span><i class="fa-regular fa-trash-can"></i> Remove current thumbnail</span></label>@endif
                <label>Thumbnail alt text<input name="cover_alt" maxlength="255" value="{{ old('cover_alt',$item->cover_alt ?? '') }}" placeholder="Describe the thumbnail"><small>Used as accessible image text when the thumbnail is displayed publicly.</small></label>
            </div>
        </div>
    </section>
</div>
</form>
</div>
@include('partials.global-cms-editor')
@include('partials.global-cms-editor-script')
@endsection
@push('head')<meta name="csrf-token" content="{{ csrf_token() }}">@endpush
@push('styles')
<style>
/* FuelFree PowerPlant — NEWS EVENT DASHBOARD LAYOUT */
.ne-wrap{max-width:1600px;margin:0 auto;color:#eaf8fb;min-width:0}
.ne-header{display:flex;justify-content:space-between;align-items:flex-end;gap:24px;padding:2px 0 20px}
.ne-eyebrow{font-size:9px;font-weight:900;letter-spacing:.18em;color:#49e8a8}
.ne-header h1{font-size:clamp(28px,3vw,42px);line-height:1.08;letter-spacing:-.04em;margin:7px 0;background:linear-gradient(90deg,#f2fbff 0%,#b8f5df 42%,#26e8a0 100%);-webkit-background-clip:text;background-clip:text;color:transparent}
.ne-header p{margin:0;color:#789aa4;font-size:11px;line-height:1.6}
.ne-actions{display:flex;gap:9px;flex-shrink:0}
.ne-btn{display:inline-flex;align-items:center;justify-content:center;gap:7px;min-height:40px;padding:0 15px;border-radius:10px;font-size:10px;font-weight:850;text-decoration:none;cursor:pointer}
.ne-btn-ghost{border:1px solid rgba(69,227,166,.22);color:#b5d8d4;background:rgba(5,25,29,.7)}
.ne-btn-primary{border:1px solid rgba(88,255,183,.5);background:linear-gradient(135deg,#10bf78,#24ed9b);color:#021810;box-shadow:0 8px 24px rgba(15,211,133,.16)}
.ne-alert{display:flex;gap:8px;align-items:center;padding:11px 13px;margin-bottom:13px;border-radius:11px;background:rgba(255,87,108,.08);border:1px solid rgba(255,87,108,.18);color:#ffacb8;font-size:10px}
.ne-layout{display:grid;grid-template-columns:minmax(0,1fr);grid-template-areas:"editor" "info" "media";gap:14px;align-items:start;min-width:0}
.ne-editor-area{grid-area:editor;min-width:0}
.ne-info-grid{grid-area:info;display:grid;grid-template-columns:repeat(3,minmax(0,1fr));gap:14px;min-width:0}
.ne-media-card{grid-area:media;min-width:0}
.ne-card{border:1px solid rgba(63,231,166,.18);border-radius:16px;background:linear-gradient(145deg,rgba(6,43,35,.96),rgba(4,24,28,.98) 58%,rgba(4,19,24,.99));padding:15px;box-shadow:0 10px 28px rgba(0,0,0,.1);min-width:0}
.ne-editor-card{padding:0;overflow:visible;background:linear-gradient(145deg,rgba(6,55,41,.98),rgba(4,27,29,.99) 65%,rgba(3,20,25,1))}
.ne-card-head{display:flex;align-items:flex-start;justify-content:space-between;gap:12px;margin-bottom:13px}
.ne-editor-head{padding:15px 16px;margin:0;border-bottom:1px solid rgba(64,230,167,.15);background:linear-gradient(120deg,rgba(10,113,76,.2),rgba(5,34,31,.35));border-radius:16px 16px 0 0}
.ne-card-head>div>span{font-size:8px;font-weight:900;letter-spacing:.15em;color:#46e9a5}
.ne-card-head h2{margin:4px 0 0;font-size:13px;color:#e8fbf4}
.ne-card-head>i{font-size:16px;color:#43e5a2}
.ne-card-head p{margin:5px 0 0;color:#6e9994;font-size:9px;line-height:1.5}
.ne-editor-body{padding:12px;overflow:visible}
.ne-layout,.ne-editor-area,.ne-editor-card,.ne-editor-body,.ff-cms-editor{overflow:visible!important}
.ne-editor-card .ff-cms-editor{width:100%;border-color:rgba(65,230,167,.22)!important;border-radius:0 0 14px 14px;box-shadow:0 8px 26px rgba(0,0,0,.12)}
.ne-editor-card .ff-cms-editor .ff-ribbon{position:sticky!important;top:0!important;z-index:1000!important;background:#061c22!important;border-bottom:1px solid rgba(62,230,166,.28)!important;box-shadow:0 6px 18px rgba(0,0,0,.2)}
.ne-editor-card .ff-cms-editor .ff-tabs{border-color:rgba(62,230,166,.16)!important}
.ne-editor-card .ff-cms-editor .ff-command:hover,.ne-editor-card .ff-cms-editor .ff-icon:hover{box-shadow:none!important}
.ne-form-grid{display:grid;grid-template-columns:minmax(0,1fr) minmax(0,1fr);gap:0 12px}
.ne-form-grid .full{grid-column:1/-1}
.ne-card label{display:block;margin-top:11px;color:#9bb7b3;font-size:9px;font-weight:750}
.ne-card input,.ne-card textarea,.ne-card select{width:100%;box-sizing:border-box;margin-top:5px;padding:10px 11px;border:1px solid rgba(69,218,175,.16);border-radius:9px;background:#04191f;color:#e9f8f3;outline:none;font-size:10px;min-height:42px}
.ne-card textarea{resize:vertical;line-height:1.6;min-height:96px}
.ne-card input:focus,.ne-card textarea:focus,.ne-card select:focus{border-color:rgba(69,235,169,.58);box-shadow:0 0 0 3px rgba(69,235,169,.06)}
.ne-card small{display:block;margin-top:4px;color:#648b88;font-size:7px;line-height:1.45}
.ne-check{display:flex!important;align-items:flex-start;gap:9px!important;padding:9px 0}
.ne-check input{width:15px!important;min-height:15px!important;margin:2px 0 0!important;accent-color:#2be9a0}
.ne-check span{color:#cce9e1;font-size:9px}.ne-check strong{display:block;font-size:9px}.ne-check small{font-weight:400}
.ne-media-layout{display:grid;grid-template-columns:330px minmax(0,1fr);gap:14px;align-items:start}
.ne-thumb{width:100%;aspect-ratio:1/1;max-width:330px;border-radius:12px;overflow:hidden;background:radial-gradient(circle at 50% 45%,rgba(43,233,160,.12),transparent 52%),#061923;border:1px dashed rgba(65,230,167,.28);display:grid;place-items:center}
.ne-thumb img{width:100%;height:100%;display:block;object-fit:cover}.ne-thumb-empty{padding:20px;text-align:center;color:#648b88}.ne-thumb-empty i{display:block;font-size:28px;color:#43e5a2;margin-bottom:8px}.ne-thumb-empty strong{display:block;color:#b9d8d1;font-size:10px}.ne-thumb-empty small{display:block;margin-top:5px;font-size:8px;line-height:1.5}
.ne-media-fields{min-width:0}.ne-upload{display:block;margin:0;border:1px dashed rgba(69,230,168,.28);border-radius:10px;padding:12px;text-align:center;cursor:pointer;background:rgba(7,38,33,.48)}
.ne-upload input{display:none!important}.ne-upload span{display:inline-flex;align-items:center;justify-content:center;gap:6px;min-height:40px;padding:0 15px;border-radius:9px;background:linear-gradient(135deg,#0ab873,#25e99c);color:#021810;font-size:10px;font-weight:850}.ne-upload small{margin-top:7px}
.ne-remove{display:flex!important;align-items:center;gap:7px!important;margin-top:9px!important;padding:8px;border-radius:8px;background:rgba(217,128,140,.05);border:1px solid rgba(217,128,140,.1)}.ne-remove input{width:14px!important;min-height:14px!important;margin:0!important;accent-color:#e98797}.ne-remove span{color:#d9919d;font-size:8px}
@media(max-width:1100px){.ne-info-grid{grid-template-columns:repeat(2,minmax(0,1fr))}.ne-publishing{grid-column:1/-1}.ne-media-layout{grid-template-columns:260px minmax(0,1fr)}}
@media(max-width:760px){.ne-header{align-items:stretch;flex-direction:column}.ne-actions{justify-content:flex-end}.ne-info-grid{grid-template-columns:1fr}.ne-publishing{grid-column:auto}.ne-form-grid{grid-template-columns:1fr}.ne-form-grid .full{grid-column:auto}.ne-media-layout{grid-template-columns:1fr}.ne-thumb{max-width:100%}.ne-header h1{font-size:30px}.ne-editor-body{padding:8px}.ne-card{padding:13px}}
@media(max-width:460px){.ne-actions{display:grid;grid-template-columns:1fr 1fr}.ne-btn{width:100%}.ne-header{padding-bottom:14px}.ne-card{border-radius:13px}.ne-editor-head{padding:13px}.ne-card input,.ne-card textarea,.ne-card select{font-size:12px}.ne-card label{font-size:10px}}
@media(prefers-reduced-motion:reduce){.ne-btn,.ne-card input,.ne-card textarea,.ne-card select{transition:none!important}}
</style>
@endpush
@push('scripts')
<script>
(function(){const input=document.getElementById('cover_image');const preview=document.getElementById('thumbnail-preview');if(!input||!preview)return;input.addEventListener('change',function(){const file=this.files&&this.files[0];if(!file)return;if(!file.type.startsWith('image/')){this.value='';return;}const url=URL.createObjectURL(file);preview.innerHTML='<img src="'+url.replace(/"/g,'&quot;')+'" alt="Selected thumbnail preview">';preview.querySelector('img').addEventListener('load',()=>URL.revokeObjectURL(url),{once:true});const remove=document.querySelector('input[name="remove_image"]');if(remove)remove.checked=false;});})();
</script>
@endpush