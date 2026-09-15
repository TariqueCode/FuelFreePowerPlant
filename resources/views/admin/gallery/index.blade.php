@extends('layouts.portal')
@section('title','Gallery Management')
@section('content')
<section class="gallery-admin-head">
    <div class="gallery-admin-title">
        <span class="eyebrow">MEDIA MANAGEMENT</span>
        <h1>Gallery Management</h1>
        <p>Manage photo and video galleries with dedicated covers, descriptions and automatic URLs.</p>
    </div>

    <div class="gallery-admin-actions">
        <div class="gallery-count">
            <strong>{{ $publishedGalleries }}</strong>
            <span>published</span>
        </div>
        <a class="primary" href="{{ route('admin.gallery.create') }}">
            <i class="fa-solid fa-plus"></i>
            <span>New Gallery</span>
        </a>
    </div>
</section>
@if(session('status'))<div class="notice">{{ session('status') }}</div>@endif
<div class="grid">@forelse($galleries as $gallery)<article class="gallery-card"><a href="{{ route('admin.gallery.edit',$gallery) }}" class="cover">@if($gallery->image_path)<img src="{{ asset('storage/'.$gallery->image_path) }}" alt="{{ $gallery->title }}">@else<div class="no-cover"><i class="fa-regular fa-images"></i><span>No cover photo</span></div>@endif<div class="cover-overlay"><span><i class="fa-solid fa-pen"></i> Manage gallery</span><span class="media-chip"><i class="fa-regular fa-images"></i> {{ $gallery->gallery_media_count }}</span></div></a><div class="body"><div class="meta"><span class="status {{ $gallery->status }}"><span class="status-dot"></span>{{ $gallery->status }}</span><span class="media-count"><i class="fa-regular fa-images"></i>{{ $gallery->gallery_media_count }} media</span></div><h2>{{ $gallery->title }}</h2>@if($gallery->excerpt)<p>{{ $gallery->excerpt }}</p>@else<p class="muted">No description added yet.</p>@endif<div class="card-footer"><div class="slug"><i class="fa-solid fa-link"></i><span>/gallery/{{ $gallery->slug }}</span></div><div class="actions"><a class="edit" href="{{ route('admin.gallery.edit',$gallery) }}" aria-label="Edit {{ $gallery->title }}"><i class="fa-solid fa-pen"></i><span>Edit</span></a><form method="POST" action="{{ route('admin.gallery.destroy',$gallery) }}" onsubmit="return confirm('Delete this gallery and all its media?')">@csrf @method('DELETE')<button class="delete" type="submit" aria-label="Delete {{ $gallery->title }}"><i class="fa-solid fa-trash"></i><span>Delete</span></button></form></div></div></div></article>@empty<div class="empty"><div class="empty-icon"><i class="fa-regular fa-images"></i></div><strong>No galleries yet.</strong><span>Create a gallery to start managing your photos and videos.</span><a href="{{ route('admin.gallery.create') }}">Create your first gallery</a></div>@endforelse</div>{{ $galleries->links() }}
@endsection
@push('styles')
<style>
.gallery-admin-head{
    display:flex;
    align-items:flex-end;
    justify-content:space-between;
    gap:28px;
    padding:0 0 20px;
    margin-bottom:22px;
    border-bottom:1px solid rgba(103,208,234,.14);
}

.gallery-admin-title{min-width:0}

.gallery-admin-title .eyebrow{
    display:inline-block;
    color:#54cde8;
    font-size:9px;
    font-weight:800;
    letter-spacing:.18em;
}

.gallery-admin-title h1{
    margin:7px 0;
    color:#eaf8fb;
    font-size:clamp(30px,3.2vw,42px);
    font-weight:800;
    line-height:1.08;
    letter-spacing:-.035em;
}

.gallery-admin-title p{
    max-width:720px;
    margin:0;
    color:#7898a5;
    font-size:11px;
    line-height:1.7;
}

.gallery-admin-actions{
    display:flex;
    align-items:center;
    justify-content:flex-end;
    gap:9px;
    flex:0 0 auto;
}

.gallery-count{
    display:inline-flex;
    align-items:center;
    justify-content:center;
    gap:6px;
    min-height:38px;
    padding:0 13px;
    border:1px solid var(--line);
    border-radius:10px;
    background:rgba(67,194,229,.025);
    color:#7898a5;
    font-size:9px;
    white-space:nowrap;
}

.gallery-count strong{
    color:#eaf8fb;
    font-size:15px;
    font-weight:800;
}

.primary{
    display:inline-flex;
    align-items:center;
    justify-content:center;
    gap:8px;
    min-height:38px;
    padding:0 15px;
    border:1px solid rgba(82,216,240,.28);
    border-radius:10px;
    background:#168eaa;
    color:#fff;
    text-decoration:none;
    font-size:9px;
    font-weight:800;
    white-space:nowrap;
    box-shadow:none;
}

.primary:hover{
    background:#1aa1bd;
    border-color:rgba(82,216,240,.42);
}

.grid{
    display:grid;
    grid-template-columns:repeat(4,minmax(0,1fr));
    gap:16px;
}

.gallery-card{
    position:relative;
    overflow:hidden;
    border:1px solid var(--line);
    border-radius:15px;
    background:#061923;
    transition:border-color .18s ease,box-shadow .18s ease;
}

.gallery-card:hover{
    transform:none;
    border-color:rgba(78,205,232,.28);
    box-shadow:0 12px 30px rgba(0,0,0,.18);
}

.cover{
    position:relative;
    display:block;
    aspect-ratio:16/10;
    overflow:hidden;
    background:#061923;
}

.cover:after{
    content:"";
    position:absolute;
    inset:0;
    background:linear-gradient(180deg,transparent 42%,rgba(2,15,23,.72) 100%);
    pointer-events:none;
}

.cover img{
    width:100%;
    height:100%;
    display:block;
    object-fit:cover;
    transition:transform .25s ease;
}

.gallery-card:hover .cover img{
    transform:scale(1.025);
    filter:none;
}

.no-cover{
    height:100%;
    display:grid;
    place-items:center;
    align-content:center;
    gap:7px;
    color:#6f909d;
    font-size:9px;
}

.no-cover i{
    font-size:29px;
    color:#4fc8e5;
}

.cover-overlay{
    position:absolute;
    z-index:2;
    left:11px;
    right:11px;
    bottom:10px;
    display:flex;
    align-items:center;
    justify-content:space-between;
    gap:8px;
    color:#fff;
    font-size:8px;
    font-weight:700;
}

.cover-overlay>span:first-child{
    display:inline-flex;
    align-items:center;
    gap:6px;
}

.media-chip{
    display:inline-flex;
    align-items:center;
    gap:5px;
    padding:4px 7px;
    border:1px solid rgba(255,255,255,.13);
    border-radius:8px;
    background:rgba(4,20,29,.72);
    white-space:nowrap;
}

.body{padding:14px}

.meta{
    display:flex;
    align-items:center;
    justify-content:space-between;
    gap:8px;
    color:#6f9aa5;
    font-size:8px;
}

.status{
    display:inline-flex;
    align-items:center;
    gap:6px;
    padding:4px 8px;
    border-radius:7px;
    text-transform:uppercase;
    font-size:7px;
    font-weight:800;
    letter-spacing:.05em;
}

.status-dot{
    width:5px;
    height:5px;
    border-radius:50%;
    background:currentColor;
}

.published{
    background:rgba(32,212,170,.08);
    color:#72e5c9;
}

.draft{
    background:rgba(255,190,70,.08);
    color:#e6c37c;
}

.media-count{
    display:inline-flex;
    align-items:center;
    gap:5px;
    color:#8caab3;
}

.body h2{
    margin:10px 0 5px;
    color:#e7f6f8;
    font-size:15px;
    font-weight:750;
    line-height:1.35;
}

.body p{
    min-height:32px;
    margin:0;
    color:#7898a5;
    font-size:9px;
    line-height:1.65;
    display:-webkit-box;
    -webkit-line-clamp:2;
    -webkit-box-orient:vertical;
    overflow:hidden;
}

.body p.muted{color:#617f89}

.card-footer{
    display:flex;
    align-items:center;
    justify-content:space-between;
    gap:9px;
    margin-top:13px;
    padding-top:11px;
    border-top:1px solid rgba(120,164,175,.1);
}

.slug{
    min-width:0;
    display:flex;
    align-items:center;
    gap:6px;
    color:#617f89;
    font-size:7px;
}

.slug i{
    flex:0 0 auto;
    color:#4fc8e5;
}

.slug span{
    overflow:hidden;
    text-overflow:ellipsis;
    white-space:nowrap;
}

.actions{
    display:flex;
    gap:6px;
    flex:0 0 auto;
}

.actions a,
.actions button{
    display:inline-flex;
    align-items:center;
    justify-content:center;
    gap:5px;
    min-height:30px;
    padding:0 9px;
    border:1px solid var(--line);
    border-radius:8px;
    background:transparent;
    color:#9bbac3;
    font-size:8px;
    font-weight:700;
    text-decoration:none;
    cursor:pointer;
}

.actions a:hover{
    border-color:rgba(78,205,232,.32);
    color:#dff5f8;
    background:rgba(67,194,229,.05);
}

.actions .delete:hover{
    border-color:rgba(255,99,113,.28);
    color:#ff9eaa;
    background:rgba(255,99,113,.05);
}

.actions form{margin:0}

.empty{
    grid-column:1/-1;
    min-height:260px;
    display:flex;
    flex-direction:column;
    align-items:center;
    justify-content:center;
    gap:8px;
    padding:30px;
    border:1px dashed rgba(103,208,234,.18);
    border-radius:15px;
    background:rgba(7,27,37,.22);
    color:#6f909d;
    text-align:center;
}

.empty-icon{
    width:54px;
    height:54px;
    display:grid;
    place-items:center;
    margin-bottom:3px;
    border:1px solid rgba(78,205,232,.18);
    border-radius:13px;
    background:rgba(67,194,229,.035);
    color:#52cee9;
    font-size:22px;
}

.empty strong{
    color:#d9f0f4;
    font-size:15px;
    font-weight:750;
}

.empty span{
    max-width:330px;
    color:#6f909d;
    font-size:9px;
    line-height:1.6;
}

.empty a{
    margin-top:2px;
    color:#5fd4ed;
    font-size:9px;
    font-weight:800;
    text-decoration:none;
}

@media(max-width:1250px){
    .grid{grid-template-columns:repeat(3,minmax(0,1fr))}
}

@media(max-width:900px){
    .gallery-admin-head{
        align-items:flex-start;
        flex-direction:column;
        gap:13px;
    }

    .gallery-admin-actions{
        width:100%;
        justify-content:flex-start;
    }

    .grid{
        grid-template-columns:repeat(2,minmax(0,1fr));
        gap:13px;
    }
}

@media(max-width:600px){
    .gallery-admin-title h1{font-size:30px}

    .gallery-admin-title p{font-size:10px}

    .gallery-admin-actions{
        display:grid;
        grid-template-columns:auto minmax(0,1fr);
    }

    .gallery-admin-actions .primary{
        width:100%;
    }

    .grid{gap:10px}

    .body{padding:11px}

    .body h2{font-size:13px}

    .card-footer{
        align-items:flex-end;
        flex-direction:column;
    }

    .slug{width:100%}

    .actions{width:100%}

    .actions a,
    .actions button{flex:1}
}

@media(max-width:430px){
    .gallery-admin-head{
        margin-bottom:15px;
        padding-bottom:15px;
    }

    .gallery-admin-title h1{font-size:27px}

    .gallery-admin-actions{
        grid-template-columns:1fr;
    }

    .gallery-count{width:100%}

    .grid{grid-template-columns:1fr}

    .cover{aspect-ratio:16/9}

    .card-footer{
        flex-direction:row;
        align-items:center;
    }

    .slug{
        width:auto;
        flex:1;
    }

    .actions{width:auto}
}
</style>
@endpush
