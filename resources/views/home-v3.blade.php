@extends('layouts.public')

@php
    $siteName = $brand['name'] ?? config('fuelfree.company.name');
@endphp

@section('title', $siteName)

@section('content')
<style>
:root{--bg:#031018;--panel:#071b26;--line:rgba(83,218,240,.16);--text:#effcff;--muted:#91aeb8;--cyan:#48d8f1;--max:1280px}
*{box-sizing:border-box}body{margin:0;background:radial-gradient(circle at 10% 0,rgba(33,187,221,.13),transparent 30%),linear-gradient(180deg,#020a10,#061721 55%,#020a10);color:var(--text);font-family:Inter,system-ui,-apple-system,"Segoe UI",sans-serif}a{text-decoration:none;color:inherit}.shell{width:min(var(--max),calc(100% - 32px));margin:auto}.welcome{padding:88px 0 72px;text-align:center}.eyebrow{color:var(--cyan);font-size:12px!important;letter-spacing:.2em;text-transform:uppercase}.welcome h1{font-size:clamp(42px,7vw,82px);line-height:.98;letter-spacing:-.06em;margin:15px auto;max-width:900px}.welcome h1 em{font-style:normal;color:#a9f5ff}.welcome p{max-width:760px;margin:0 auto;color:var(--muted);font-size:16px!important;line-height:1.9;text-align:left}.welcome-message-title{display:block;color:var(--text);font-size:clamp(22px,3vw,30px);font-weight:750;line-height:1.2;margin:0 0 14px}.welcome-message-copy{display:block}.section{padding:58px 0;border-top:1px solid rgba(83,218,240,.08)}.head{display:flex;justify-content:space-between;align-items:end;gap:24px;margin-bottom:26px}.head h2{font-size:clamp(28px,4vw,45px);letter-spacing:-.04em;margin:5px 0}.more{font-size:13px!important;color:var(--cyan);white-space:nowrap}
.news-grid{display:grid;grid-template-columns:repeat(2,minmax(0,1fr));gap:18px}.news{position:relative;display:grid;grid-template-columns:180px 34px minmax(0,1fr);min-height:180px;overflow:hidden;border:1px solid var(--line);border-radius:16px;background:linear-gradient(145deg,rgba(8,37,50,.92),rgba(3,19,27,.96));transition:transform .25s,border-color .25s,box-shadow .25s}.news:hover{transform:translateY(-3px);border-color:rgba(72,216,241,.4);box-shadow:0 14px 32px rgba(0,0,0,.16)}.news-media{width:180px;height:180px;min-height:180px;background:#061923;overflow:hidden;position:relative}.news-media img{width:100%;height:100%;display:block;object-fit:cover}.news-kind{height:100%;display:flex;align-items:center;justify-content:center;border-left:1px solid rgba(72,216,241,.1);border-right:1px solid rgba(72,216,241,.1);writing-mode:vertical-rl;transform:rotate(180deg);color:var(--cyan);font-size:8px;font-weight:800;letter-spacing:.14em;text-transform:uppercase}.news-kind.notice{color:#f0c58e}.news-body{padding:18px 18px 16px;display:flex;flex-direction:column;min-width:0}.news h3{margin:0 0 9px;font-size:18px;line-height:1.32;display:-webkit-box;-webkit-line-clamp:2;-webkit-box-orient:vertical;overflow:hidden}.news p{color:var(--muted);font-size:13px!important;line-height:1.65!important;margin:0;display:-webkit-box;-webkit-line-clamp:3;-webkit-box-orient:vertical;overflow:hidden}.news-footer{margin-top:auto;padding-top:14px;display:flex;align-items:center;justify-content:space-between;gap:8px}.date{color:#6f9aa5;font-size:11px!important}.read{color:var(--cyan);font-size:11px!important;font-weight:700;white-space:nowrap}
.folders{display:grid;grid-template-columns:repeat(3,minmax(0,1fr));gap:20px}.folder{display:block;border:1px solid var(--line);border-radius:20px;overflow:hidden;background:linear-gradient(145deg,rgba(8,37,50,.92),rgba(3,19,27,.96));transition:transform .25s,border-color .25s,box-shadow .25s}.folder:hover{transform:translateY(-5px);border-color:rgba(79,210,238,.38);box-shadow:0 22px 50px rgba(0,0,0,.22)}.folder-media{aspect-ratio:1/1;width:100%;background:#061923;overflow:hidden;position:relative}.folder-media img{width:100%;height:100%;display:block;object-fit:cover;transition:transform .5s ease}.folder:hover .folder-media img{transform:scale(1.035)}.folder-placeholder{height:100%;display:grid;place-items:center;color:#668792;font-size:34px}.folder-body{padding:18px 19px 20px}.folder-body h3{margin:0;color:var(--text);font-size:17px;font-weight:750;line-height:1.35;display:-webkit-box;-webkit-line-clamp:2;-webkit-box-orient:vertical;overflow:hidden}.folder-meta{display:flex;align-items:center;justify-content:space-between;gap:12px;margin-top:13px;color:#789aa5;font-size:9px!important}.folder-meta span{display:inline-flex;align-items:center;gap:7px}.folder-meta i{color:var(--cyan)}.folder-count{color:#72dfbf;white-space:nowrap}.folder-date{white-space:nowrap}
.empty{padding:35px;text-align:center;color:var(--muted);border:1px dashed var(--line);border-radius:18px}
@media(min-width:1100px){
.welcome{padding:78px 0 82px;text-align:left}.welcome>p{max-width:100%;display:grid;grid-template-columns:minmax(280px,.78fr) minmax(420px,1.22fr);gap:54px;align-items:start}.welcome-message-title{font-size:30px;margin:0;padding-top:4px}.welcome-message-copy{font-size:15px;line-height:1.85;background:linear-gradient(145deg,rgba(8,37,50,.72),rgba(3,19,27,.42));border:1px solid var(--line);border-radius:20px;padding:24px 26px;box-shadow:0 18px 45px rgba(0,0,0,.12)}.welcome h1{max-width:980px}
.news-grid{grid-template-columns:repeat(3,minmax(0,1fr));gap:22px}.news{display:block;min-height:0}.news-media{width:100%;height:205px;min-height:205px}.news-body{min-height:190px}.news h3{font-size:17px}.folders{grid-template-columns:repeat(4,minmax(0,1fr));gap:20px}.folder-media{aspect-ratio:4/3}.folder-body{padding:15px 16px 16px}.folder-body h3{font-size:15px}.folder-meta{margin-top:10px}
}
@media(max-width:1099px) and (min-width:651px){
.welcome{padding:66px 0 70px;text-align:left}.welcome>p{max-width:100%;display:grid;grid-template-columns:minmax(210px,.72fr) minmax(320px,1.28fr);gap:28px;align-items:start}.welcome-message-title{font-size:25px;margin:0;padding-top:2px}.welcome-message-copy{font-size:13px;line-height:1.75;background:linear-gradient(145deg,rgba(8,37,50,.7),rgba(3,19,27,.38));border:1px solid var(--line);border-radius:17px;padding:18px 19px}.welcome h1{max-width:900px}
.news-grid{grid-template-columns:repeat(2,minmax(0,1fr));gap:16px}.news{grid-template-columns:140px 27px minmax(0,1fr)}.news-media{width:140px;height:175px;min-height:175px}.news-body{min-height:175px;padding:15px}.news h3{font-size:16px}.folders{grid-template-columns:repeat(3,minmax(0,1fr));gap:14px}.folder-media{aspect-ratio:4/3}.folder-body{padding:12px 13px 14px}.folder-body h3{font-size:13px}.folder-meta{font-size:8px!important;margin-top:9px}.folder-meta span{gap:5px}
}
@media(max-width:650px){.welcome{padding:65px 0 48px}.welcome h1{font-size:45px}.welcome p{text-align:left;font-size:15px!important;line-height:1.8}.welcome-message-title{font-size:20px;margin-bottom:10px}.head{display:flex;align-items:center;justify-content:space-between;gap:12px;margin-bottom:16px}.head>div{min-width:0}.head h2{font-size:clamp(25px,7vw,34px);line-height:1.05;margin:5px 0 0;white-space:nowrap}.head .more{flex:0 0 auto;font-size:9px}.head p{margin-top:8px}.news-grid{grid-template-columns:1fr;gap:10px}.news{grid-template-columns:112px 24px minmax(0,1fr);width:100%;height:112px;min-height:112px;align-items:stretch}.news-media{width:112px;height:112px;min-width:112px;min-height:112px;aspect-ratio:1/1}.news-media img{width:112px;height:112px;object-fit:cover}.news-kind{font-size:7px;letter-spacing:.11em}.news-body{width:100%;height:112px;min-height:112px;padding:11px 13px;overflow:hidden}.news h3{font-size:14px;line-height:1.3;margin:0;-webkit-line-clamp:2}.news p{display:none}.news-footer{padding-top:5px}.date{font-size:8px}.read{font-size:8px}.folders{grid-template-columns:repeat(2,minmax(0,1fr));gap:10px}.folder-body{padding:11px 11px 12px}.folder-body h3{font-size:13px;margin-bottom:8px}.folder-meta{font-size:8px;display:block}.folder-count{display:block;margin-top:4px}.folder-date i,.folder-count i{margin-right:4px}}
@media(max-width:400px){.news{grid-template-columns:100px 22px minmax(0,1fr);height:100px;min-height:100px}.news-media,.news-media img{width:100px;height:100px;min-width:100px;min-height:100px}.news-body{height:100px;min-height:100px;padding:9px 11px}.news h3{font-size:13px}.news-kind{font-size:6px}.shell{width:calc(100% - 22px)}}

.home-slider{position:relative;width:100%;margin:28px 0 0;overflow:visible;border:0;border-radius:0;background:transparent;box-shadow:none}.home-slider.has-caption{margin-bottom:28px}
.slider-track{position:relative;width:100%;height:calc(min(100vw - 32px,1280px) / 2.35);min-height:0}
.slide{position:absolute;inset:0;display:block;opacity:0;visibility:hidden;transform:scale(1.008);transition:opacity .8s ease,transform 6s ease,visibility .8s}
.slide.is-active{opacity:1;visibility:visible;transform:scale(1)}
.slide-media{position:relative;width:100%;height:calc(min(100vw - 32px,1280px) / 2.35);min-height:0;overflow:hidden;background:#061923;border:1px solid rgba(83,218,240,.2);border-radius:26px}
.slide img{width:100%;height:100%;display:block;object-fit:cover}

.slide-caption{position:absolute;left:0;right:0;top:calc(100% + 7px);display:block;padding:0 10px;overflow:hidden;text-align:center;pointer-events:none}
.slide-caption span{display:none}
.slide-caption strong{display:block;color:#8faeb8;font-size:9px;line-height:1.25;letter-spacing:.02em;font-weight:500;white-space:nowrap;overflow:hidden;text-overflow:ellipsis;text-shadow:none}
.welcome{padding:78px 0 82px;text-align:left}
.welcome-heading{max-width:100%;padding-bottom:30px;border-bottom:1px solid var(--line)}
.welcome h1{max-width:900px;font-size:clamp(42px,6vw,72px);margin:13px 0 0}
.welcome h1 em{color:#a9f5ff;font-style:normal}
.welcome-rule{width:58px;height:2px;margin-top:20px;background:var(--cyan);opacity:.8}
.welcome-copy{max-width:900px;padding:30px 0 0}
.welcome-copy p{margin:0 0 18px;color:var(--muted);font-size:15px!important;line-height:1.85;text-align:left}
.welcome-copy strong{color:var(--text)}
.welcome-signoff{margin-top:25px;color:var(--text);font-weight:750;font-size:14px}
.welcome-signoff span{color:var(--muted);font-weight:500}

@media(min-width:1100px){
.home-slider{margin-top:32px}.home-slider.has-caption{margin-bottom:34px}
.welcome{padding:72px 0 70px}
.welcome-heading{padding-bottom:26px}
.welcome h1{max-width:980px}
.welcome-copy{max-width:960px;padding-top:30px}
.welcome-copy p{font-size:15px!important;line-height:1.85}
}

@media(max-width:1099px) and (min-width:651px){
.home-slider{margin-top:22px}.home-slider.has-caption{margin-bottom:30px}
.slider-track{height:calc(min(100vw - 32px,1280px) / 2.35);min-height:0}
.slide-media{height:calc(min(100vw - 32px,1280px) / 2.35);border-radius:20px}
.welcome{padding:62px 0 68px}
.welcome-heading{padding-bottom:24px}
.welcome h1{max-width:900px}
.welcome-copy{padding-top:24px}
.welcome-copy p{font-size:14px!important;line-height:1.75}
}

@media(max-width:650px){
.slider-arrow,.slider-dots,.slider-progress{display:none}.home-slider{margin:8px 0 0}.home-slider.has-caption{margin-bottom:24px}
.slider-track{height:calc((100vw - 22px) / 2.35)}
.slide-media{height:calc((100vw - 22px) / 2.35);border-radius:18px}
.slide-caption{top:calc(100% + 6px);padding:0 8px}
.slide-caption strong{font-size:8px}

.welcome{padding:52px 0 48px}
.welcome-heading{padding-bottom:20px}
.welcome h1{font-size:clamp(34px,10vw,48px);line-height:1.02;margin-top:10px}
.welcome-rule{margin-top:14px}
.welcome-copy{padding-top:20px}
.welcome-copy p{font-size:15px!important;line-height:1.8;text-align:left;margin-bottom:16px}
.welcome-signoff{font-size:13px;line-height:1.55;margin-top:20px}
}

@media(max-width:400px){
.slider-track{height:calc((100vw - 22px) / 2.35)}
.slide-media{height:calc((100vw - 22px) / 2.35);border-radius:16px}
.slide-caption{top:calc(100% + 5px);padding:0 6px}
.slide-caption strong{font-size:7px}
.welcome{padding-top:46px}
.welcome-copy p{font-size:14px!important;line-height:1.75}
}

</style>
<main class="shell">
    @if($home['slider'] && $sliders->isNotEmpty())
    <section class="home-slider" aria-label="Company highlights">
        <div class="slider-track">
            @foreach($sliders as $index => $slider)
                @php $sliderUrl=$slider->link_url; @endphp
                @if($sliderUrl)<a class="slide {{ $index===0?'is-active':'' }}" href="{{ $sliderUrl }}" @if(str_starts_with($sliderUrl,'http')) target="_blank" rel="noopener" @endif>@else<div class="slide {{ $index===0?'is-active':'' }}">@endif
                    <div class="slide-media">
                        <img src="{{ asset('storage/'.ltrim($slider->image_path,'/')) }}" alt="{{ $slider->title ?: $siteName }}" @if($index>0)loading="lazy"@endif>
                    </div>
                    @if($slider->title)<div class="slide-caption" aria-label="{{ $slider->title }}"><strong>{{ $slider->title }}</strong></div>@endif
                @if($sliderUrl)</a>@else</div>@endif
            @endforeach
        </div>
    </section>
    @endif

    @if($home['welcome'])
    <section class="welcome">
        <div class="welcome-heading"><span class="eyebrow">Welcome to {{ $siteName }}</span><h1>Building a <em>stronger</em> energy future.</h1><div class="welcome-rule"></div></div>
        <div class="welcome-copy">
            <p><strong>{{ $siteName }}</strong> is a forward-thinking energy company committed to contributing to Bangladesh’s sustainable energy future. Our vision is to develop efficient, reliable, and innovative power solutions that support the country’s growing energy needs and economic development.</p>
            <p>We are dedicated to building a cleaner and smarter energy future through innovation, responsible development, and world-class management practices. We aim to strengthen our capabilities, expand our projects, embrace modern technologies, and deliver dependable energy solutions while maintaining our commitment to quality, sustainability, and excellence.</p>
            <div class="welcome-signoff">{{ $siteName }} <span>— Powering a cleaner, smarter future.</span></div>
        </div>
    </section>
    @endif

    @if($home['news'])
    <section class="section"><div class="head"><div><span class="eyebrow">Latest updates</span><h2>News &amp; Notices</h2></div><a class="more" href="{{ route('news.index') }}">View all →</a></div><div class="news-grid">@if(($content['news']??collect())->isNotEmpty())@foreach(($content['news']??collect())->take(6) as $item)<a class="news" href="{{ route('news.show',$item->slug) }}"><div class="news-media">@if($item->image_path)<img src="{{ asset('storage/'.$item->image_path) }}" alt="{{ $item->cover_alt ?: $item->title }}" loading="lazy">@else<div class="news-placeholder">▣</div>@endif</div><div class="news-kind {{ $item->type==='announcement'?'notice':'' }}">{{ $item->type==='announcement'?'Notice':'News' }}</div><div class="news-body"><h3>{{ $item->title }}</h3><p>{{ $item->excerpt ?: \Illuminate\Support\Str::limit(strip_tags((string) $item->content), 180) }}</p><div class="news-footer"><span class="date">{{ $item->published_at?->format('d F Y') }}</span><span class="read">Read more →</span></div></div></a>@endforeach @else<div class="empty" style="grid-column:1/-1">No news has been published yet.</div>@endif</div></section>
    @endif

    @if($home['gallery'])
    <section class="section"><div class="head"><div><span class="eyebrow">Photo collections</span><h2>Gallery</h2></div><a class="more" href="{{ route('site.gallery') }}">View all →</a></div><div class="folders">@if($gallery->isNotEmpty())@foreach($gallery->take(8) as $item)<a class="folder" href="{{ route('gallery.show',['item'=>$item->slug ?: $item->id]) }}"><div class="folder-media">@if($item->image_path)<img src="{{ asset('storage/'.ltrim($item->image_path,'/')) }}" alt="{{ $item->cover_alt ?: $item->title }}" loading="lazy">@else<div class="folder-placeholder"><i class="fa-regular fa-images"></i></div>@endif</div><div class="folder-body"><h3>{{ $item->title }}</h3><div class="folder-meta"><span class="folder-date"><i class="fa-regular fa-calendar"></i>{{ $item->published_at?->format('d F Y') ?? $item->created_at?->format('d F Y') }}</span><span class="folder-count"><i class="fa-regular fa-images"></i>{{ $item->gallery_media_count }} {{ $item->gallery_media_count === 1 ? 'photo' : 'photos' }}</span></div></div></a>@endforeach @else<div class="empty" style="grid-column:1/-1">No photo galleries have been published yet.</div>@endif</div></section>
    @endif
</main>
@push('scripts')
<script>
(() => {
    const root = document.querySelector('.home-slider');
    if (!root) return;
    const slides = [...root.querySelectorAll('.slide')];
    if (slides.length < 2) return;

    let index = 0;
    let timer;

    const show = (nextIndex) => {
        index = (nextIndex + slides.length) % slides.length;
        slides.forEach((slide, i) => slide.classList.toggle('is-active', i === index));
    };

    const start = () => {
        clearInterval(timer);
        timer = setInterval(() => show(index + 1), 5000);
    };

    const pause = () => clearInterval(timer);

    root.addEventListener('mouseenter', pause);
    root.addEventListener('mouseleave', start);
    root.addEventListener('touchstart', pause, {passive:true});
    root.addEventListener('touchend', start, {passive:true});
    root.addEventListener('touchcancel', start, {passive:true});

    if (!window.matchMedia('(prefers-reduced-motion: reduce)').matches) start();
})();
</script>
@endpush
@endsection
