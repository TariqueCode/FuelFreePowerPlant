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
<div class="ne-grid">
    <aside class="ne-sidebar">
        <section class="ne-card ne-media-card">
            <div class="ne-card-head"><div><span>VISUAL</span><h2>Thumbnail</h2></div><i class="fa-regular fa-image"></i></div>
            <div class="ne-thumb" id="thumbnail-preview">
                @if($item->image_path)<img src="{{ asset('storage/'.$item->image_path) }}" alt="{{ $item->cover_alt ?: $item->title }}">@else<div class="ne-thumb-empty"><i class="fa-regular fa-image"></i><strong>No thumbnail</strong><small>Use a square image for the best match with the public list.</small></div>@endif
            </div>
            <label class="ne-upload"><input id="cover_image" type="file" name="cover_image" accept="image/jpeg,image/png,image/webp,image/gif"><span><i class="fa-solid fa-cloud-arrow-up"></i> {{ $item->image_path ? 'Replace thumbnail' : 'Add thumbnail' }}</span><small>JPG, PNG, WebP or GIF · max {{ (int) config('fuelfree.upload.max_mb', 50) }} MB</small></label>
            @if($item->image_path)<label class="ne-remove"><input type="checkbox" name="remove_image" value="1" @checked(old('remove_image'))><span><i class="fa-regular fa-trash-can"></i> Remove current thumbnail</span></label>@endif
            <label>Thumbnail alt text<input name="cover_alt" maxlength="255" value="{{ old('cover_alt',$item->cover_alt ?? '') }}" placeholder="Describe the thumbnail"><small>Used as accessible image text when the thumbnail is displayed publicly.</small></label>
        </section>
        <section class="ne-card">
            <div class="ne-card-head"><div><span>PUBLICATION</span><h2>Publishing</h2></div><i class="fa-regular fa-paper-plane"></i></div>
            <label class="ne-check"><input type="checkbox" name="is_featured" value="1" @checked(old('is_featured',$item->is_featured))><span><strong>Featured item</strong><small>Use as the lead story when supported by the public view.</small></span></label>
            <label>Status<select name="status"><option value="draft" @selected(old('status',$item->status)==='draft')>Draft</option><option value="published" @selected(old('status',$item->status)==='published')>Published</option></select></label>
        </section>
        <section class="ne-card">
            <div class="ne-card-head"><div><span>SEARCH</span><h2>SEO</h2></div><i class="fa-solid fa-magnifying-glass"></i></div>
            <label>Meta title<input name="meta_title" maxlength="255" value="{{ old('meta_title',$item->meta_title ?? '') }}"></label>
            <label>Meta description<textarea name="meta_description" maxlength="1000" rows="4">{{ old('meta_description',$item->meta_description ?? '') }}</textarea></label>
        </section>
    </aside>
    <main class="ne-main">
        <section class="ne-card ne-identity">
            <div class="ne-card-head"><div><span>STORY DETAILS</span><h2>Publication identity</h2></div><i class="fa-regular fa-newspaper"></i></div>
            <div class="ne-form-grid">
                <label class="full">Title<input name="title" required maxlength="180" value="{{ old('title',$item->title) }}" placeholder="Publication title"></label>
                <label>Type<select name="type"><option value="news" @selected(old('type',$item->type)==='news')>News</option><option value="announcement" @selected(old('type',$item->type)==='announcement')>Event</option></select></label>
                <label>Slug<input name="slug" maxlength="180" value="{{ old('slug',$item->slug) }}" placeholder="Auto-generated if blank"><small>Leave blank to generate automatically.</small></label>
                <label class="full">Introduction<textarea name="excerpt" maxlength="1000" rows="5" placeholder="Short summary shown before readers open the story">{{ old('excerpt',$item->excerpt) }}</textarea><small>This matches the short introduction used to summarize the publication.</small></label>
            </div>
        </section>
        <section class="ne-card ne-editor-card">
            <div class="ne-card-head"><div><span>CONTENT</span><h2>Article editor</h2><p>Rich text, media, embeds, tables, CTA buttons, columns, HTML source and preview.</p></div></div>
            <div class="ne-editor-body"><textarea id="global-cms-content" name="content" data-global-cms-content hidden>{{ old('content',$item->content ?? '') }}</textarea></div>
        </section>
    </main>
</div>
</form>
</div>
@include('partials.global-cms-editor')
@include('partials.global-cms-editor-script')
@endsection
@push('head')<meta name="csrf-token" content="{{ csrf_token() }}">@endpush
@push('styles')
<style>
.ne-wrap{max-width:1480px;margin:auto;color:#eaf8fb}.ne-header{display:flex;justify-content:space-between;align-items:flex-end;gap:24px;padding:4px 0 20px}.ne-eyebrow{font-size:9px;font-weight:900;letter-spacing:.18em;color:#55d8ef}.ne-header h1{font-size:clamp(27px,3vw,40px);letter-spacing:-.035em;margin:6px 0}.ne-header p{margin:0;color:#7897a3;font-size:11px;line-height:1.6}.ne-actions{display:flex;gap:8px;flex-shrink:0}.ne-btn{display:inline-flex;align-items:center;justify-content:center;gap:7px;min-height:40px;padding:0 14px;border-radius:10px;font-size:10px;font-weight:800;text-decoration:none;cursor:pointer}.ne-btn-ghost{border:1px solid rgba(103,208,234,.14);color:#a7c1c9;background:rgba(6,24,33,.55)}.ne-btn-primary{border:0;background:linear-gradient(135deg,#28b6d6,#168da8);color:#fff}.ne-alert{display:flex;gap:8px;align-items:center;padding:11px 13px;margin-bottom:13px;border-radius:11px;background:rgba(255,87,108,.08);border:1px solid rgba(255,87,108,.18);color:#ffacb8;font-size:10px}.ne-grid{display:grid;grid-template-columns:310px minmax(0,1fr);gap:14px;align-items:start}.ne-sidebar,.ne-main{display:grid;gap:14px}.ne-card{border:1px solid rgba(103,208,234,.13);border-radius:16px;background:linear-gradient(145deg,#091f2b,#061720);padding:15px;box-shadow:0 10px 28px rgba(0,0,0,.08)}.ne-card-head{display:flex;align-items:flex-start;justify-content:space-between;gap:12px;margin-bottom:13px}.ne-card-head>div>span{font-size:8px;font-weight:900;letter-spacing:.15em;color:#58d8ed}.ne-card-head h2{margin:4px 0 0;font-size:13px;color:#e4f5f8}.ne-card-head>i{font-size:16px;color:#4ccfe7}.ne-card-head p{margin:5px 0 0;color:#668590;font-size:9px;line-height:1.5}.ne-thumb{width:100%;aspect-ratio:1/1;border-radius:12px;overflow:hidden;background:radial-gradient(circle at 50% 50%,rgba(72,216,241,.13),transparent 52%),#061923;border:1px solid rgba(103,208,234,.1);display:grid;place-items:center}.ne-thumb img{width:100%;height:100%;display:block;object-fit:cover}.ne-thumb-empty{padding:20px;text-align:center;color:#64808b}.ne-thumb-empty i{display:block;font-size:28px;color:#43cce6;margin-bottom:8px}.ne-thumb-empty strong{display:block;color:#b9d1d8;font-size:10px}.ne-thumb-empty small{display:block;margin-top:5px;font-size:8px;line-height:1.5}.ne-upload{display:block;margin-top:10px;border:1px dashed rgba(103,208,234,.2);border-radius:10px;padding:11px;text-align:center;cursor:pointer;background:rgba(7,28,38,.5)}.ne-upload input{display:none!important}.ne-upload span{display:block;color:#8fd9e8;font-size:9px;font-weight:800}.ne-upload span i{margin-right:5px}.ne-upload small{display:block;margin-top:5px;color:#607f89;font-size:7px}.ne-remove{display:flex!important;align-items:center;gap:7px!important;margin-top:9px!important;padding:8px;border-radius:8px;background:rgba(217,128,140,.05);border:1px solid rgba(217,128,140,.1)}.ne-remove input{width:14px!important;margin:0!important}.ne-remove span{color:#d9919d;font-size:8px}.ne-card label{display:block;margin-top:11px;color:#8ca8b2;font-size:9px;font-weight:700}.ne-card input,.ne-card textarea,.ne-card select{width:100%;box-sizing:border-box;margin-top:5px;padding:10px 11px;border:1px solid rgba(103,208,234,.13);border-radius:9px;background:#061823;color:#e9f8fb;outline:none;font-size:10px}.ne-card textarea{resize:vertical;line-height:1.6}.ne-card input:focus,.ne-card textarea:focus,.ne-card select:focus{border-color:rgba(76,207,231,.5);box-shadow:0 0 0 3px rgba(76,207,231,.06)}.ne-card small{display:block;margin-top:4px;color:#607f89;font-size:7px;line-height:1.45}.ne-check{display:flex!important;align-items:flex-start;gap:9px!important;padding:9px 0}.ne-check input{width:15px!important;margin:2px 0 0!important}.ne-check span{color:#cce5ea;font-size:9px}.ne-check strong{display:block;font-size:9px}.ne-check small{font-weight:400}.ne-form-grid{display:grid;grid-template-columns:minmax(0,1fr) minmax(0,1fr);gap:0 12px}.ne-form-grid .full{grid-column:1/-1}.ne-editor-card{overflow:visible;padding:0}.ne-editor-card .ne-card-head{padding:15px 16px;margin:0;border-bottom:1px solid rgba(103,208,234,.1)}.ne-editor-body{padding:12px}.ne-identity{padding-bottom:16px}@media(max-width:980px){.ne-grid{grid-template-columns:1fr}.ne-sidebar{grid-template-columns:repeat(2,minmax(0,1fr))}.ne-media-card{grid-row:span 2}}@media(max-width:650px){.ne-header{align-items:stretch;flex-direction:column}.ne-actions{justify-content:flex-end}.ne-grid{display:block}.ne-sidebar{display:grid;grid-template-columns:1fr;margin-bottom:14px}.ne-media-card{grid-row:auto}.ne-form-grid{grid-template-columns:1fr}.ne-form-grid .full{grid-column:auto}.ne-header h1{font-size:29px}}
</style>
@endpush
@push('scripts')
<script>
(function(){const input=document.getElementById('cover_image');const preview=document.getElementById('thumbnail-preview');if(!input||!preview)return;input.addEventListener('change',function(){const file=this.files&&this.files[0];if(!file)return;if(!file.type.startsWith('image/')){this.value='';return;}const url=URL.createObjectURL(file);preview.innerHTML='<img src="'+url.replace(/"/g,'&quot;')+'" alt="Selected thumbnail preview">';preview.querySelector('img').addEventListener('load',()=>URL.revokeObjectURL(url),{once:true});const remove=document.querySelector('input[name="remove_image"]');if(remove)remove.checked=false;});})();
</script>
@endpush
