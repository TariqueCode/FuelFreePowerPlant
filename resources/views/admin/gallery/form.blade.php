@extends('layouts.portal')
@section('title',$gallery->exists?'Edit Gallery':'New Gallery')
@section('content')
<section class="gallery-form-head">
    <div class="gallery-form-title">
        <span class="eyebrow">ADVANCED GALLERY MANAGEMENT</span>
        <h1>{{ $gallery->exists?'Edit Gallery':'Create Gallery' }}</h1>
        <p>Build a gallery with a dedicated cover photo, short description and multiple photos or videos. URL slug is generated automatically.</p>
    </div>

    <a class="back" href="{{ route('admin.gallery.index') }}">
        <i class="fa-solid fa-arrow-left"></i>
        <span>Back</span>
    </a>
</section>
@if($errors->any())<div class="errors">{{ $errors->first() }}</div>@endif
<div class="gallery-form-card"><form id="gallery-form" method="POST" enctype="multipart/form-data" action="{{ $gallery->exists?route('admin.gallery.update',$gallery):route('admin.gallery.store') }}">@csrf @if($gallery->exists)@method('PATCH')@endif
<div class="grid"><div class="full"><label>Gallery title *</label><input name="title" value="{{ old('title',$gallery->title) }}" maxlength="255" placeholder="e.g. Summer Collection 2025" required></div><div class="full"><label>Short description</label><textarea name="excerpt" rows="3" maxlength="500" placeholder="A short description shown after opening the gallery.">{{ old('excerpt',$gallery->excerpt) }}</textarea></div><div><label>Status *</label><select name="status"><option value="draft" @selected(old('status',$gallery->status)==='draft')>Draft</option><option value="published" @selected(old('status',$gallery->status)==='published')>Published</option></select></div><div><label>Display order</label><input type="number" min="0" name="sort_order" value="{{ old('sort_order',$gallery->sort_order ?? 0) }}"></div><div><label>Publish date/time</label><input type="datetime-local" name="published_at" value="{{ old('published_at',$gallery->published_at?->format('Y-m-d\\TH:i')) }}"></div><div><label>Automatic URL</label><div class="slug-box">/gallery/{{ $gallery->slug ?: 'generated-from-title' }}</div></div><div class="full"><label>Cover photo</label><div class="cover-upload"><div class="cover-preview" id="cover-preview">@if($gallery->image_path)<img src="{{ asset('storage/'.$gallery->image_path) }}" alt="Cover">@else<i class="fa-regular fa-image"></i><span>No cover selected</span>@endif</div><div><input id="cover-input" type="file" name="cover_image" accept="image/jpeg,image/png,image/webp,image/gif"><p>The cover is used only on the Gallery listing. It will not appear inside the opened gallery.</p></div></div></div><div class="full"><label>Gallery photos &amp; videos</label><div class="media-box"><button type="button" class="upload-btn" id="media-btn"><i class="fa-solid fa-cloud-arrow-up"></i> Add Photos &amp; Videos</button><input id="media-input" type="file" hidden multiple accept="image/jpeg,image/png,image/webp,image/gif,video/mp4,video/webm,video/quicktime"><p>Upload multiple files together. Images and videos are stored separately from the gallery description.</p><div id="media-grid" class="media-grid">@foreach($gallery->galleryMedia ?? [] as $media)<div class="media-item"><div class="media-thumb">@if($media->type==='video')<video src="{{ asset('storage/'.$media->path) }}" controls preload="metadata"></video>@else<img src="{{ asset('storage/'.$media->path) }}" alt="{{ $media->original_name }}">@endif</div><div class="media-name">{{ $media->original_name ?: basename($media->path) }}</div><button type="button" class="remove-existing" data-id="{{ $media->id }}">Remove</button></div>@endforeach</div></div></div></div><div id="new-media"></div><div class="actions"><a class="back" href="{{ route('admin.gallery.index') }}">Cancel</a><button class="save" type="submit"><i class="fa-solid fa-floppy-disk"></i> {{ $gallery->exists?'Save Gallery':'Create Gallery' }}</button></div></form></div>
@endsection
@push('styles')
<style>
.gallery-form-head{
    display:flex;
    align-items:flex-end;
    justify-content:space-between;
    gap:28px;
    padding:0 0 20px;
    margin-bottom:22px;
    border-bottom:1px solid rgba(103,208,234,.14);
}

.gallery-form-title{
    min-width:0;
}

.gallery-form-title .eyebrow{
    display:block;
    color:#54cde8;
    font-size:9px;
    font-weight:800;
    letter-spacing:.16em;
    line-height:1.2;
}

.gallery-form-title h1{
    margin:7px 0 8px;
    color:#eaf8fb;
    font-size:clamp(30px,3.2vw,42px);
    font-weight:800;
    line-height:1.08;
    letter-spacing:-.035em;
}

.gallery-form-title p{
    max-width:780px;
    margin:0;
    color:#7898a5;
    font-size:11px;
    line-height:1.7;
}

.gallery-form-head .back{
    flex:0 0 auto;
    min-height:38px;
    box-sizing:border-box;
    border:1px solid var(--line);
    border-radius:10px;
    background:rgba(255,255,255,.015);
    color:#a9c1c9;
    text-decoration:none;
    padding:10px 13px;
    font-size:10px;
    font-weight:700;
    transition:border-color .18s ease,color .18s ease,background .18s ease;
}

.gallery-form-head .back:hover{
    border-color:rgba(82,216,240,.3);
    background:rgba(67,194,229,.05);
    color:#e5fbff;
}

.errors{
    margin-bottom:14px;
    padding:11px 13px;
    border:1px solid rgba(255,100,100,.18);
    border-radius:10px;
    background:rgba(210,65,65,.08);
    color:#ffb0b0;
    font-size:10px;
    line-height:1.5;
}

.gallery-form-card{
    width:100%;
    max-width:1280px;
    box-sizing:border-box;
    overflow:hidden;
    border:1px solid rgba(103,208,234,.18);
    border-radius:15px;
    background:#061923;
}

.gallery-form-card form{
    margin:0;
}

.gallery-form-card .grid{
    display:grid;
    grid-template-columns:repeat(3,minmax(0,1fr));
    gap:18px 16px;
    padding:20px;
}

.gallery-form-card .grid > .full{
    grid-column:auto;
}

.gallery-form-card .grid > .full:nth-child(1){
    grid-column:1;
}

.gallery-form-card .grid > .full:nth-child(2){
    grid-column:2;
}

.gallery-form-card .grid > .full:nth-child(7){
    grid-column:span 1;
}

.gallery-form-card .grid > .full:nth-child(8){
    grid-column:span 2;
}

.gallery-form-card label{
    display:block;
    margin:0 0 7px;
    color:#8caab4;
    font-size:10px;
    font-weight:600;
    line-height:1.3;
}

.gallery-form-card input,
.gallery-form-card select,
.gallery-form-card textarea{
    width:100%;
    min-height:46px;
    box-sizing:border-box;
    border:1px solid rgba(103,208,234,.17);
    border-radius:10px;
    outline:none;
    background:#03141c;
    color:#e4f3f7;
    padding:11px 13px;
    font:inherit;
    font-size:11px;
    transition:border-color .18s ease,background .18s ease,box-shadow .18s ease;
}

.gallery-form-card textarea{
    min-height:118px;
    resize:vertical;
    line-height:1.6;
}

.gallery-form-card input::placeholder,
.gallery-form-card textarea::placeholder{
    color:#587580;
}

.gallery-form-card input:focus,
.gallery-form-card select:focus,
.gallery-form-card textarea:focus{
    border-color:rgba(82,216,240,.42);
    background:#041922;
    box-shadow:0 0 0 3px rgba(67,194,229,.06);
}

.gallery-form-card .slug-box{
    min-height:46px;
    box-sizing:border-box;
    display:flex;
    align-items:center;
    border:1px solid rgba(103,208,234,.12);
    border-radius:10px;
    background:#041720;
    color:#658691;
    padding:11px 13px;
    font-size:10px;
    overflow:hidden;
    text-overflow:ellipsis;
    white-space:nowrap;
}

.gallery-form-card .cover-upload,
.gallery-form-card .media-box{
    min-height:190px;
    box-sizing:border-box;
    border:1px solid rgba(67,209,240,.16);
    border-radius:13px;
    background:#041a23;
}

.gallery-form-card .cover-upload{
    display:grid;
    grid-template-columns:180px minmax(0,1fr);
    gap:18px;
    align-items:center;
    padding:14px;
}

.gallery-form-card .cover-preview{
    height:158px;
    border:1px dashed rgba(103,208,234,.2);
    border-radius:11px;
    display:flex;
    flex-direction:column;
    align-items:center;
    justify-content:center;
    gap:7px;
    overflow:hidden;
    color:#668894;
    font-size:9px;
    background:rgba(0,0,0,.08);
}

.gallery-form-card .cover-preview img{
    width:100%;
    height:100%;
    object-fit:cover;
}

.gallery-form-card .cover-preview i{
    color:#48c7e5;
    font-size:30px;
}

.gallery-form-card .cover-upload input[type=file]{
    min-height:auto;
    padding:0;
    border:0;
    background:transparent;
    color:#89a7b2;
    box-shadow:none;
}

.gallery-form-card .cover-upload p,
.gallery-form-card .media-box p{
    margin:9px 0 0;
    color:#718f9a;
    font-size:9px;
    line-height:1.65;
}

.gallery-form-card .media-box{
    padding:14px;
}

.gallery-form-card .upload-btn{
    min-height:40px;
    border:1px solid rgba(82,216,240,.28);
    border-radius:10px;
    background:#0d7890;
    color:#e9fcff;
    padding:10px 13px;
    cursor:pointer;
    font-size:10px;
    font-weight:700;
    transition:background .18s ease,border-color .18s ease;
}

.gallery-form-card .upload-btn:hover{
    background:#128ca6;
    border-color:rgba(82,216,240,.42);
}

.gallery-form-card .media-grid{
    display:grid;
    grid-template-columns:repeat(4,minmax(0,1fr));
    gap:10px;
    margin-top:14px;
}

.gallery-form-card .media-item{
    overflow:hidden;
    border:1px solid rgba(103,208,234,.14);
    border-radius:11px;
    background:#03151d;
}

.gallery-form-card .media-thumb{
    height:130px;
}

.gallery-form-card .media-thumb img,
.gallery-form-card .media-thumb video{
    display:block;
    width:100%;
    height:100%;
    object-fit:cover;
}

.gallery-form-card .media-name{
    padding:8px;
    color:#89a7b2;
    font-size:8px;
    white-space:nowrap;
    overflow:hidden;
    text-overflow:ellipsis;
}

.gallery-form-card .media-item button{
    margin:0 8px 9px;
    border:1px solid rgba(255,100,100,.18);
    border-radius:7px;
    background:rgba(255,80,80,.05);
    color:#ef9d9d;
    padding:6px 8px;
    cursor:pointer;
    font-size:8px;
}

.gallery-form-card .actions{
    display:flex;
    justify-content:flex-end;
    align-items:center;
    gap:9px;
    margin:0;
    padding:15px 20px;
    border-top:1px solid rgba(103,208,234,.12);
    background:#041720;
}

.gallery-form-card .actions .back{
    min-height:40px;
    box-sizing:border-box;
    display:inline-flex;
    align-items:center;
    justify-content:center;
    padding:10px 15px;
    border:1px solid var(--line);
    border-radius:10px;
    background:transparent;
    color:#9db9c2;
    text-decoration:none;
    font-size:10px;
    font-weight:700;
}

.gallery-form-card .actions .save{
    min-height:40px;
    border:1px solid rgba(82,216,240,.28);
    border-radius:10px;
    background:#168eaa;
    color:#fff;
    padding:10px 16px;
    cursor:pointer;
    font-size:10px;
    font-weight:800;
    box-shadow:none;
}

.gallery-form-card .actions .save:hover{
    background:#199bb8;
}

@media(max-width:1050px){
    .gallery-form-card .grid{
        grid-template-columns:repeat(2,minmax(0,1fr));
    }

    .gallery-form-card .grid > .full:nth-child(1),
    .gallery-form-card .grid > .full:nth-child(2){
        grid-column:span 1;
    }

    .gallery-form-card .grid > .full:nth-child(7),
    .gallery-form-card .grid > .full:nth-child(8){
        grid-column:1/-1;
    }

    .gallery-form-card .media-grid{
        grid-template-columns:repeat(3,minmax(0,1fr));
    }
}

@media(max-width:700px){
    .gallery-form-head{
        align-items:flex-start;
        flex-direction:column;
        gap:14px;
    }

    .gallery-form-title h1{
        font-size:30px;
    }

    .gallery-form-title p{
        font-size:10px;
    }

    .gallery-form-head .back{
        align-self:flex-start;
    }

    .gallery-form-card .grid{
        grid-template-columns:1fr;
        gap:15px;
        padding:15px;
    }

    .gallery-form-card .grid > .full:nth-child(1),
    .gallery-form-card .grid > .full:nth-child(2),
    .gallery-form-card .grid > .full:nth-child(7),
    .gallery-form-card .grid > .full:nth-child(8){
        grid-column:1;
    }

    .gallery-form-card .cover-upload{
        grid-template-columns:1fr;
        gap:12px;
    }

    .gallery-form-card .cover-preview{
        height:170px;
    }

    .gallery-form-card .media-grid{
        grid-template-columns:repeat(2,minmax(0,1fr));
    }

    .gallery-form-card .actions{
        padding:13px 15px;
    }

    .gallery-form-card .actions>*{
        flex:1;
        text-align:center;
    }
}

@media(max-width:420px){
    .gallery-form-title h1{
        font-size:27px;
    }

    .gallery-form-card .media-grid{
        grid-template-columns:1fr;
    }

    .gallery-form-card .actions{
        flex-direction:column-reverse;
    }

    .gallery-form-card .actions>*{
        width:100%;
    }
}
</style>
@endpush
@push('head')<meta name="csrf-token" content="{{ csrf_token() }}">@endpush
@push('scripts')<script>const cover=document.getElementById('cover-input'),preview=document.getElementById('cover-preview'),mediaInput=document.getElementById('media-input'),mediaBtn=document.getElementById('media-btn'),grid=document.getElementById('media-grid'),newMedia=document.getElementById('new-media');cover?.addEventListener('change',()=>{const f=cover.files[0];if(!f)return;preview.innerHTML=`<img src="${URL.createObjectURL(f)}" alt="Cover preview">`});mediaBtn.addEventListener('click',()=>mediaInput.click());async function upload(f){const fd=new FormData();fd.append('media',f);const r=await fetch('{{ route('admin.gallery.media') }}',{method:'POST',headers:{'X-CSRF-TOKEN':document.querySelector('meta[name=csrf-token]').content,'Accept':'application/json'},body:fd});if(!r.ok)throw new Error('Upload failed: '+f.name);return r.json()}let mediaIndex=0;mediaInput.addEventListener('change',async()=>{for(const f of [...mediaInput.files]){try{const d=await upload(f);const div=document.createElement('div');div.className='media-item';div.innerHTML=`<div class="media-thumb">${d.type==='video'?`<video src="${d.url}" controls preload="metadata"></video>`:`<img src="${d.url}" alt="${d.name}">`}</div><div class="media-name">${d.name}</div><button type="button" class="remove-new">Remove</button>`;grid.appendChild(div);const key=mediaIndex++;const h=document.createElement('input');h.type='hidden';h.name=`media[${key}][path]`;h.value=d.path;newMedia.appendChild(h);const t=document.createElement('input');t.type='hidden';t.name=`media[${key}][type]`;t.value=d.type;newMedia.appendChild(t);const n=document.createElement('input');n.type='hidden';n.name=`media[${key}][name]`;n.value=d.name;newMedia.appendChild(n);div.querySelector('.remove-new').onclick=()=>{h.remove();t.remove();n.remove();div.remove()}}catch(e){alert(e.message)}}mediaInput.value=''});document.querySelectorAll('.remove-existing').forEach(btn=>btn.addEventListener('click',async()=>{if(!confirm('Remove this media?'))return;const r=await fetch('{{ url('/admin/galleries/media') }}/'+btn.dataset.id,{method:'DELETE',headers:{'X-CSRF-TOKEN':document.querySelector('meta[name=csrf-token]').content,'Accept':'application/json'}});if(r.ok)btn.closest('.media-item').remove()}));</script>@endpush
