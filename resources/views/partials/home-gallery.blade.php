@if($gallery->isNotEmpty())
<section class="section home-gallery-section" id="gallery">
    <div class="shell">
        <div class="section-head">
            <div>
                <span class="eyebrow">Moments &amp; events</span>
                <h2>Our gallery.</h2>
            </div>
            <a class="btn secondary" href="{{ route('site.gallery') }}">View all photos &amp; videos</a>
        </div>
        <div class="home-gallery-grid">
            @foreach($gallery->take(6) as $item)
                <article class="home-gallery-card">
                    <div class="home-gallery-media">
                        @if($item->image_path)
                            <img src="{{ asset('storage/'.$item->image_path) }}" alt="{{ $item->title }}" loading="lazy">
                        @else
                            <div class="home-gallery-placeholder"><i class="fa-solid fa-images"></i></div>
                        @endif
                        <span class="home-gallery-badge"><i class="fa-solid fa-calendar-days"></i> {{ $item->published_at?->format('d M Y') }}</span>
                    </div>
                    <div class="home-gallery-body">
                        <h3>{{ $item->title }}</h3>
                        @if($item->excerpt)<p>{{ $item->excerpt }}</p>@endif
                        @if($item->content)<div class="home-gallery-content">{!! $item->content !!}</div>@endif
                    </div>
                </article>
            @endforeach
        </div>
    </div>
</section>
<style>
.home-gallery-section{position:relative;overflow:hidden}.home-gallery-section:before{content:"";position:absolute;width:420px;height:420px;right:-220px;top:30px;border-radius:50%;background:radial-gradient(circle,rgba(67,209,240,.08),transparent 68%);pointer-events:none}.home-gallery-grid{display:grid;grid-template-columns:repeat(3,1fr);gap:14px}.home-gallery-card{overflow:hidden;border:1px solid var(--line);border-radius:20px;background:linear-gradient(145deg,rgba(8,38,52,.9),rgba(3,21,30,.82));transition:transform .3s ease,border-color .3s ease,box-shadow .3s ease}.home-gallery-card:hover{transform:translateY(-6px);border-color:rgba(86,210,238,.32);box-shadow:0 24px 55px rgba(0,0,0,.24)}.home-gallery-media{height:220px;position:relative;overflow:hidden;background:#061923}.home-gallery-media img{width:100%;height:100%;object-fit:cover;display:block;transition:transform .7s ease}.home-gallery-card:hover .home-gallery-media img{transform:scale(1.06)}.home-gallery-placeholder{height:100%;display:grid;place-items:center;color:var(--cyan);font-size:45px;background:radial-gradient(circle,rgba(67,209,240,.12),transparent 65%)}.home-gallery-badge{position:absolute;left:12px;bottom:12px;padding:7px 9px;border-radius:999px;background:rgba(2,10,16,.76);backdrop-filter:blur(10px);color:#dffaff;font-size:9px;border:1px solid rgba(255,255,255,.1)}.home-gallery-badge i{color:var(--cyan);margin-right:4px}.home-gallery-body{padding:17px}.home-gallery-body h3{margin:0;font-size:17px}.home-gallery-body>p{color:var(--muted);font-size:10px;line-height:1.7;margin:7px 0}.home-gallery-content{color:#a9c4cc;font-size:10px;line-height:1.7}.home-gallery-content img,.home-gallery-content video{width:100%;max-height:190px;object-fit:cover;border-radius:12px;margin:8px 0}.home-gallery-content .media-gallery{display:grid;grid-template-columns:repeat(2,1fr);gap:7px}.home-gallery-content .media-gallery img{margin:0;height:100px}.home-gallery-content iframe{width:100%;min-height:180px;border:0;border-radius:12px;margin:8px 0}@media(max-width:900px){.home-gallery-grid{grid-template-columns:1fr 1fr}}@media(max-width:520px){.home-gallery-grid{grid-template-columns:1fr}.home-gallery-media{height:240px}}
</style>
@endif
