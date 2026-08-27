@extends('layouts.public')

@php($siteName = $brand['name'] ?? config('fuelfree.company.name'))

@section('title', 'Gallery — '.$siteName)

@section('content')
<style>
.gallery-page{--bg:#020a10;--surface:#071b25;--surface2:#092530;--line:rgba(93,211,238,.16);--text:#effcff;--muted:#91aeb8;--cyan:#4fd2ee;width:min(1180px,calc(100% - 32px));margin:auto;padding:58px 0 90px;color:var(--text);font-family:Inter,system-ui,-apple-system,"Segoe UI",sans-serif}
.gallery-page *{box-sizing:border-box}.gallery-page a{text-decoration:none;color:inherit}.gallery-hero{display:flex;align-items:end;justify-content:space-between;gap:30px;padding-bottom:28px;border-bottom:1px solid var(--line);margin-bottom:28px}.gallery-kicker{color:var(--cyan);font-size:9px;font-weight:800;letter-spacing:.22em;text-transform:uppercase}.gallery-hero h1{font-size:clamp(44px,6vw,72px);line-height:.95;letter-spacing:-.055em;margin:10px 0 12px}.gallery-hero p{max-width:590px;margin:0;color:var(--muted);font-size:14px;line-height:1.8}.view-switch{display:flex;gap:4px;padding:4px;border:1px solid var(--line);border-radius:12px;background:rgba(7,27,37,.8);flex:0 0 auto}.view-btn{width:42px;height:38px;border:0;border-radius:8px;background:transparent;color:#75939e;cursor:pointer;display:grid;place-items:center}.view-btn.active,.view-btn:hover{background:rgba(79,210,238,.1);color:var(--cyan)}.gallery-grid{display:grid;grid-template-columns:repeat(3,minmax(0,1fr));gap:20px}.gallery-card{display:block;border:1px solid var(--line);border-radius:20px;overflow:hidden;background:linear-gradient(145deg,rgba(8,37,50,.92),rgba(3,19,27,.96));transition:transform .25s,border-color .25s,box-shadow .25s}.gallery-card:hover{transform:translateY(-5px);border-color:rgba(79,210,238,.38);box-shadow:0 22px 50px rgba(0,0,0,.22)}.gallery-cover{aspect-ratio:1/1;background:#061923;overflow:hidden;position:relative}.gallery-cover img{width:100%;height:100%;display:block;object-fit:cover;transition:transform .5s ease}.gallery-card:hover .gallery-cover img{transform:scale(1.035)}.no-cover{height:100%;display:grid;place-items:center;color:#668792;font-size:34px}.gallery-info{padding:18px 19px 20px}.gallery-title{margin:0;color:var(--text);font-size:17px;font-weight:750;line-height:1.35;display:-webkit-box;-webkit-line-clamp:2;-webkit-box-orient:vertical;overflow:hidden}.gallery-meta{display:flex;align-items:center;justify-content:space-between;gap:12px;margin-top:13px;color:#789aa5;font-size:9px}.gallery-meta span{display:inline-flex;align-items:center;gap:7px}.gallery-meta i{color:var(--cyan)}.gallery-list{display:grid;gap:10px}.gallery-list .gallery-card{display:grid;grid-template-columns:150px minmax(0,1fr);min-height:150px;border-radius:16px}.gallery-list .gallery-cover{width:150px;height:150px}.gallery-list .gallery-info{display:flex;flex-direction:column;justify-content:center;padding:24px 28px}.gallery-list .gallery-title{font-size:21px}.gallery-list .gallery-meta{justify-content:flex-start;gap:26px;margin-top:14px}.gallery-list .gallery-card:hover .gallery-cover img{transform:scale(1.03)}.empty{grid-column:1/-1;padding:70px 25px;border:1px dashed var(--line);border-radius:18px;color:var(--muted);text-align:center;background:rgba(7,27,37,.35)}
@media(max-width:900px){.gallery-page{width:min(100% - 36px,700px);max-width:700px;padding:40px 0 70px;overflow:hidden}.gallery-hero{display:block}.view-switch{display:none}.gallery-grid,.gallery-list{grid-template-columns:repeat(2,minmax(0,1fr));gap:12px}.gallery-card{min-width:0;width:100%;border-radius:18px}.gallery-cover{aspect-ratio:1/1;width:100%;height:auto}.gallery-info{padding:13px;min-width:0}.gallery-title{font-size:13px;min-width:0}.gallery-meta{font-size:8px;margin-top:9px;flex-wrap:wrap;justify-content:flex-start;gap:7px}.gallery-list .gallery-card{display:block;min-height:0}.gallery-list .gallery-cover{width:100%;height:auto}.gallery-list .gallery-info{display:block;padding:13px}}
@media(max-width:520px){.gallery-page{width:calc(100% - 24px);padding:32px 0 60px}.gallery-hero{padding-bottom:20px;margin-bottom:20px}.gallery-hero h1{font-size:clamp(38px,11vw,43px)}.gallery-hero p{font-size:12px;line-height:1.75}.gallery-grid,.gallery-list{grid-template-columns:repeat(2,minmax(0,1fr));gap:8px}.gallery-card{border-radius:14px}.gallery-info{padding:10px;min-width:0}.gallery-title{font-size:12px;line-height:1.35}.gallery-meta{font-size:7px;gap:5px}.gallery-meta span{gap:4px;min-width:0}.gallery-meta span:last-child{max-width:100%;overflow:hidden;text-overflow:ellipsis;white-space:nowrap}.gallery-list .gallery-card{display:block}.gallery-list .gallery-info{padding:10px}.gallery-list .gallery-title{font-size:12px}}
</style>

<main class="gallery-page">
    <header class="gallery-hero">
        <div>
            <div class="gallery-kicker">Moments &amp; Media</div>
            <h1>Gallery</h1>
            <p>Explore our events, milestones and selected moments from {{ $siteName }}.</p>
        </div>
        <div class="view-switch" aria-label="Gallery view">
            <button class="view-btn active" type="button" data-view="grid" title="Grid view" aria-label="Grid view"><i class="fa-solid fa-grip"></i></button>
            <button class="view-btn" type="button" data-view="list" title="List view" aria-label="List view"><i class="fa-solid fa-list"></i></button>
        </div>
    </header>

    <section id="galleryItems" class="gallery-grid">
        @if($galleries->isEmpty())
            <div class="empty">No galleries have been published yet.</div>
        @else
            @foreach($galleries as $gallery)
                <a class="gallery-card" href="{{ route('gallery.show',['item'=>$gallery->slug ?: $gallery->id]) }}">
                    <div class="gallery-cover">
                        @if($gallery->image_path)
                            <img src="{{ asset('storage/'.ltrim($gallery->image_path,'/')) }}" alt="{{ $gallery->cover_alt ?: $gallery->title }}" loading="lazy">
                        @else
                            <div class="no-cover"><i class="fa-regular fa-images"></i></div>
                        @endif
                    </div>
                    <div class="gallery-info">
                        <h2 class="gallery-title">{{ $gallery->title }}</h2>
                        <div class="gallery-meta">
                            <span><i class="fa-regular fa-calendar"></i>{{ $gallery->published_at?->format('d F Y') ?? $gallery->created_at?->format('d F Y') }}</span>
                            <span><i class="fa-regular fa-images"></i>{{ $gallery->gallery_media_count }} {{ $gallery->gallery_media_count === 1 ? 'photo' : 'photos' }}</span>
                        </div>
                    </div>
                </a>
            @endforeach
        @endif
    </section>
</main>

<script>
(() => {
    const items = document.getElementById('galleryItems');
    const buttons = document.querySelectorAll('.view-btn');
    if (!items || !buttons.length) return;
    const saved = localStorage.getItem('gallery-view');
    const setView = view => {
        if (window.matchMedia('(max-width: 900px)').matches) view = 'grid';
        items.classList.toggle('gallery-list', view === 'list');
        items.classList.toggle('gallery-grid', view !== 'list');
        buttons.forEach(btn => btn.classList.toggle('active', btn.dataset.view === view));
        localStorage.setItem('gallery-view', view);
    };
    setView(saved === 'list' ? 'list' : 'grid');
    buttons.forEach(btn => btn.addEventListener('click', () => setView(btn.dataset.view)));
})();
</script>
@endsection
