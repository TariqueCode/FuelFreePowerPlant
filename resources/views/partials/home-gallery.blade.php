@if($gallery->isNotEmpty())
<section class="section home-gallery-section" id="gallery">
    <div class="shell">
        <div class="section-head">
            <div><span class="eyebrow">Moments &amp; events</span><h2>Our gallery.</h2></div>
            <a class="btn secondary" href="{{ route('site.gallery') }}">View all galleries</a>
        </div>
        <div class="home-gallery-track">
            @foreach($gallery->take(8) as $item)
                <a class="home-gallery-card" href="{{ route('site.gallery') }}#gallery-{{ $item->id }}">
                    <div class="home-gallery-media">
                        @if($item->image_path)
                            <img src="{{ asset('storage/'.$item->image_path) }}" alt="{{ $item->title }}" loading="lazy">
                        @else
                            <div class="home-gallery-placeholder"><i class="fa-solid fa-images"></i></div>
                        @endif
                    </div>
                    <div class="home-gallery-body">
                        <h3>{{ $item->title }}</h3>
                        <span><i class="fa-solid fa-calendar-days"></i> {{ $item->published_at?->format('d M Y') }}</span>
                    </div>
                </a>
            @endforeach
        </div>
    </div>
</section>
<style>
.home-gallery-section{position:relative;overflow:hidden}.home-gallery-section:before{content:"";position:absolute;width:420px;height:420px;right:-220px;top:30px;border-radius:50%;background:radial-gradient(circle,rgba(67,209,240,.08),transparent 68%);pointer-events:none}.home-gallery-track{display:flex;gap:14px;overflow-x:auto;overscroll-behavior-x:contain;scroll-snap-type:x mandatory;padding:4px 2px 14px;scrollbar-width:thin;-webkit-overflow-scrolling:touch}.home-gallery-track::-webkit-scrollbar{height:5px}.home-gallery-card{flex:0 0 min(340px,82vw);scroll-snap-align:start;overflow:hidden;border:1px solid var(--line);border-radius:20px;background:linear-gradient(145deg,rgba(8,38,52,.9),rgba(3,21,30,.82));transition:transform .25s ease,border-color .25s ease;touch-action:pan-x}.home-gallery-card:hover{transform:translateY(-4px);border-color:rgba(86,210,238,.35)}.home-gallery-media{height:215px;overflow:hidden;background:#061923}.home-gallery-media img{width:100%;height:100%;object-fit:cover;display:block;transition:transform .55s ease}.home-gallery-card:hover .home-gallery-media img{transform:scale(1.04)}.home-gallery-placeholder{height:100%;display:grid;place-items:center;color:var(--cyan);font-size:45px;background:radial-gradient(circle,rgba(67,209,240,.12),transparent 65%)}.home-gallery-body{padding:15px 17px 17px}.home-gallery-body h3{margin:0 0 8px;font-size:16px;white-space:nowrap;overflow:hidden;text-overflow:ellipsis}.home-gallery-body span{color:#7ed9e9;font-size:9px}.home-gallery-body span i{color:var(--cyan);margin-right:5px}@media(max-width:520px){.home-gallery-card{flex-basis:84vw}.home-gallery-media{height:230px}}
</style>
@endif
