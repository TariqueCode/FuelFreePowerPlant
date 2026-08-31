@extends('layouts.public')

@php
    $siteName = $brand['name'] ?? config('fuelfree.company.name');
@endphp

@section('title', $siteName)

@section('content')
<style>
:root{--bg:#031018;--panel:#071b26;--line:rgba(83,218,240,.16);--text:#effcff;--muted:#91aeb8;--cyan:#48d8f1;--max:1280px}
*{box-sizing:border-box}
html{overflow-x:hidden}
body{margin:0;overflow-x:hidden;background:radial-gradient(circle at 10% 0,rgba(33,187,221,.13),transparent 30%),linear-gradient(180deg,#020a10,#061721 55%,#020a10);color:var(--text);font-family:Inter,system-ui,-apple-system,"Segoe UI",sans-serif}
a{text-decoration:none;color:inherit}
img{max-width:100%}
.shell{width:min(var(--max),calc(100% - 32px));margin-inline:auto}
.eyebrow{display:block;color:var(--cyan);font-size:11px!important;line-height:1.4;letter-spacing:.2em;text-transform:uppercase}
.home-slider{position:relative;width:100%;margin:30px 0 0;overflow:visible}
.slider-track{position:relative;width:100%;aspect-ratio:2.35/1}
.slide{position:absolute;inset:0;display:block;opacity:0;visibility:hidden;transform:scale(1.008);transition:opacity .8s ease,transform 6s ease,visibility .8s}
.slide.is-active{opacity:1;visibility:visible;transform:scale(1)}
.slide-media{position:relative;width:100%;height:100%;overflow:hidden;background:#061923;border:1px solid rgba(83,218,240,.2);border-radius:24px}
.slide img{width:100%;height:100%;display:block;object-fit:cover}
.slide-caption{position:absolute;left:0;right:0;top:calc(100% + 7px);padding:0 10px;text-align:center;pointer-events:none;overflow:hidden}
.slide-caption strong{display:block;color:#8faeb8;font-size:9px;line-height:1.3;font-weight:500;white-space:nowrap;overflow:hidden;text-overflow:ellipsis}
.welcome{padding:76px 0 70px;position:relative;isolation:isolate}
.welcome:before,.welcome:after{content:"";position:absolute;border:1px solid rgba(72,216,241,.11);border-radius:50%;pointer-events:none;z-index:-1;animation:welcomeFloat 9s ease-in-out infinite}
.welcome:before{width:260px;height:260px;right:-100px;top:8%;box-shadow:0 0 80px rgba(72,216,241,.07)}
.welcome:after{width:150px;height:150px;left:-55px;bottom:5%;animation-delay:-3s}
@keyframes welcomeFloat{0%,100%{transform:translate3d(0,0,0) rotate(0);opacity:.5}50%{transform:translate3d(0,-12px,0) rotate(8deg);opacity:.85}}
.welcome-heading{padding-bottom:26px;border-bottom:1px solid var(--line)}
.welcome h1{max-width:980px;font-size:clamp(40px,6vw,72px);line-height:1.02;letter-spacing:-.055em;margin:12px 0 0}
.welcome h1 em{font-style:normal;color:#a9f5ff}
.welcome-rule{width:58px;height:2px;margin-top:18px;background:var(--cyan);opacity:.8}
.welcome-copy{max-width:960px;padding-top:28px}
.welcome-layout-center .welcome-heading,.welcome-layout-center .welcome-copy{text-align:center;margin-inline:auto}
.welcome-layout-right .welcome-heading,.welcome-layout-right .welcome-copy{margin-left:auto;text-align:right}
.welcome-preview,.welcome-more-content{color:var(--muted);font-size:15px;line-height:1.85;overflow-wrap:anywhere}
.welcome-more-content{margin-top:16px}
.welcome-more-toggle{display:inline-flex;align-items:center;gap:8px;margin-top:17px;padding:10px 14px;border:1px solid var(--line);border-radius:999px;background:rgba(72,216,241,.05);color:var(--cyan);font-size:11px;font-weight:750;cursor:pointer}
.welcome-more-toggle i{transition:transform .2s}
.welcome-more-toggle[aria-expanded=true] i{transform:rotate(180deg)}
.welcome-signoff{margin-top:23px;color:var(--text);font-weight:750;font-size:13px;line-height:1.5}
.welcome-signoff span{color:var(--muted);font-weight:500}
.section{padding:58px 0;border-top:1px solid rgba(83,218,240,.08);animation:sectionReveal .7s ease both}
@keyframes sectionReveal{from{opacity:0;transform:translateY(12px)}to{opacity:1;transform:none}}
.section-layout-center .head{text-align:center}.section-layout-center .head>div{margin-inline:auto}
.section-layout-right .head{text-align:right}.section-layout-right .head>div{margin-left:auto}
.head{display:flex;align-items:flex-end;justify-content:space-between;gap:20px;margin-bottom:24px}
.head>div{min-width:0}.head h2{font-size:clamp(27px,4vw,44px);line-height:1.05;letter-spacing:-.04em;margin:5px 0 0}
.head p{max-width:560px;margin:7px 0 0;color:var(--muted);font-size:11px!important;line-height:1.6!important}
.more{flex:0 0 auto;color:var(--cyan);font-size:11px!important;font-weight:700;white-space:nowrap}
.stats-grid{display:grid;grid-template-columns:repeat(4,minmax(0,1fr));gap:14px}
.stat-card{min-width:0;padding:22px;border:1px solid var(--line);border-radius:18px;background:linear-gradient(145deg,rgba(8,37,50,.92),rgba(3,19,27,.96));transition:transform .2s,border-color .2s}
.stat-card:hover{transform:translateY(-3px);border-color:rgba(72,216,241,.35)}
.stat-card i{color:var(--cyan);font-size:18px}.stat-card strong{display:block;margin-top:16px;font-size:28px;line-height:1.15;letter-spacing:-.03em;overflow-wrap:anywhere}.stat-card span{display:block;margin-top:7px;color:#789aa5;font-size:10px;line-height:1.4}
.project-grid{display:grid;grid-template-columns:repeat(3,minmax(0,1fr));gap:16px}
.project-card,.member-card,.folder{min-width:0;border:1px solid var(--line);border-radius:18px;overflow:hidden;background:linear-gradient(145deg,rgba(8,37,50,.92),rgba(3,19,27,.96));transition:transform .25s,border-color .25s,box-shadow .25s}
.project-card:hover,.member-card:hover,.folder:hover{transform:translateY(-4px);border-color:rgba(72,216,241,.4);box-shadow:0 16px 38px rgba(0,0,0,.15)}
.project-media{height:180px;background:#061923;display:grid;place-items:center;color:#5e8490;font-size:30px;overflow:hidden}
.project-media img,.member-photo img,.folder-media img{width:100%;height:100%;object-fit:cover;display:block}
.project-body{padding:16px}.project-top{display:flex;justify-content:space-between;gap:8px;color:#72dfbf;font-size:8px;text-transform:uppercase;letter-spacing:.12em}.project-top i{color:var(--cyan)}.project-body h3{font-size:17px;line-height:1.3;margin:10px 0 7px}.project-body p{color:var(--muted);font-size:10px;line-height:1.5;margin:0 0 13px}.project-body>strong{font-size:12px;color:#a9f5ff}
.management-grid{display:grid;grid-template-columns:repeat(4,minmax(0,1fr));gap:14px}
.member-photo{aspect-ratio:1/1;background:#061923;display:grid;place-items:center;color:#5e8490;font-size:34px;overflow:hidden}
.member-card>div:last-child{padding:14px}.member-card h3{margin:0;font-size:14px;line-height:1.3}.member-card p{margin:6px 0 0;color:var(--muted);font-size:9px;line-height:1.5}
.news-grid{display:grid;grid-template-columns:repeat(3,minmax(0,1fr));gap:18px}
.news{min-width:0;display:flex;flex-direction:column;border:1px solid var(--line);border-radius:17px;overflow:hidden;background:linear-gradient(145deg,rgba(8,37,50,.92),rgba(3,19,27,.96));transition:transform .25s,border-color .25s,box-shadow .25s}
.news:hover{transform:translateY(-4px);border-color:rgba(72,216,241,.4);box-shadow:0 16px 38px rgba(0,0,0,.15)}
.news-media{width:100%;aspect-ratio:16/9;background:#061923;overflow:hidden}.news-media img{width:100%;height:100%;object-fit:cover;display:block}.news-placeholder{height:100%;display:grid;place-items:center;color:#5e8490;font-size:26px}
.news-kind{align-self:flex-start;margin:12px 15px 0;padding:4px 7px;border-radius:999px;background:rgba(72,216,241,.06);color:var(--cyan);font-size:7px;font-weight:800;letter-spacing:.12em;text-transform:uppercase}.news-kind.notice{color:#f0c58e;background:rgba(240,197,142,.06)}
.news-body{display:flex;flex:1;min-width:0;flex-direction:column;padding:10px 15px 15px}.news h3{margin:0 0 7px;font-size:16px;line-height:1.35;display:-webkit-box;-webkit-line-clamp:2;-webkit-box-orient:vertical;overflow:hidden}.news p{color:var(--muted);font-size:10px!important;line-height:1.55!important;margin:0;display:-webkit-box;-webkit-line-clamp:3;-webkit-box-orient:vertical;overflow:hidden}.news-footer{margin-top:auto;padding-top:13px;display:flex;align-items:center;justify-content:space-between;gap:8px}.date{color:#6f9aa5;font-size:9px!important}.read{color:var(--cyan);font-size:9px!important;font-weight:700;white-space:nowrap}
.folders{display:grid;grid-template-columns:repeat(4,minmax(0,1fr));gap:18px}.folder-media{aspect-ratio:4/3;background:#061923;overflow:hidden}.folder-media img{transition:transform .5s ease}.folder:hover .folder-media img{transform:scale(1.035)}.folder-placeholder{height:100%;display:grid;place-items:center;color:#668792;font-size:34px}.folder-body{padding:14px 15px 16px}.folder-body h3{margin:0;color:var(--text);font-size:14px;line-height:1.35;display:-webkit-box;-webkit-line-clamp:2;-webkit-box-orient:vertical;overflow:hidden}.folder-meta{display:flex;align-items:center;justify-content:space-between;gap:10px;margin-top:10px;color:#789aa5;font-size:8px!important}.folder-meta span{display:inline-flex;align-items:center;gap:6px}.folder-meta i{color:var(--cyan)}.folder-count{color:#72dfbf;white-space:nowrap}.folder-date{white-space:nowrap}
.cta-card{display:flex;align-items:center;justify-content:space-between;gap:24px;padding:32px;border:1px solid var(--line);border-radius:23px;background:linear-gradient(120deg,rgba(10,54,70,.95),rgba(4,24,34,.95));box-shadow:0 20px 60px rgba(0,0,0,.16)}
.cta-card h2{margin:8px 0 7px;font-size:clamp(25px,4vw,42px);line-height:1.05;letter-spacing:-.04em}.cta-card p{margin:0;color:var(--muted);font-size:11px;line-height:1.65}.cta-card .btn{flex:0 0 auto}
.empty{padding:32px;text-align:center;color:var(--muted);border:1px dashed var(--line);border-radius:17px}
@media(max-width:1099px){
.shell{width:min(var(--max),calc(100% - 28px))}
.home-slider{margin-top:22px}.slide-media{border-radius:20px}
.welcome{padding:62px 0 64px}.welcome h1{font-size:clamp(38px,6.5vw,62px)}
.stats-grid{grid-template-columns:repeat(2,minmax(0,1fr))}.project-grid,.news-grid{grid-template-columns:repeat(2,minmax(0,1fr))}.management-grid{grid-template-columns:repeat(2,minmax(0,1fr))}.folders{grid-template-columns:repeat(3,minmax(0,1fr))}
}
@media(max-width:650px){
.shell{width:calc(100% - 22px)}
.home-slider{margin-top:7px}.slider-track{aspect-ratio:2.35/1}.slide-media{border-radius:14px}.slide-caption{top:calc(100% + 5px);padding:0 5px}.slide-caption strong{font-size:7px}
.welcome{padding:47px 0 43px}.welcome:before{width:180px;height:180px;right:-100px}.welcome:after{width:110px;height:110px;left:-65px}.welcome-heading{padding-bottom:18px}.welcome .eyebrow{font-size:8px!important;letter-spacing:.15em}.welcome h1{font-size:clamp(30px,9.3vw,44px);line-height:1.05;letter-spacing:-.045em;margin-top:9px;overflow-wrap:anywhere}.welcome-rule{width:44px;margin-top:13px}.welcome-copy{padding-top:18px}.welcome-preview,.welcome-more-content{font-size:14px;line-height:1.75}.welcome-more-toggle{font-size:9px;padding:9px 12px}.welcome-signoff{font-size:11px;margin-top:17px}
.section{padding:42px 0}.head{align-items:flex-start;gap:10px;margin-bottom:15px}.head h2{font-size:clamp(22px,7vw,31px);line-height:1.08}.head p{font-size:9px!important}.more{font-size:8px!important;padding-top:7px}
.stats-grid,.project-grid,.management-grid{grid-template-columns:repeat(2,minmax(0,1fr));gap:9px}.stat-card{padding:14px;border-radius:14px;min-height:100px}.stat-card i{font-size:14px}.stat-card strong{font-size:19px;margin-top:10px}.stat-card span{font-size:7px}.project-card,.member-card{border-radius:14px}.project-media{height:auto;aspect-ratio:4/3}.project-body{padding:10px}.project-body h3{font-size:12px;margin:7px 0 5px}.project-body p{font-size:8px;line-height:1.4;margin-bottom:8px}.project-body>strong{font-size:9px}.member-card>div:last-child{padding:10px}.member-card h3{font-size:10px}.member-card p{font-size:7px}
.news-grid{grid-template-columns:1fr;gap:9px}.news{display:grid;grid-template-columns:104px 1fr;grid-template-rows:auto 1fr;min-height:104px}.news-media{grid-row:1/3;width:104px;height:104px;aspect-ratio:1/1}.news-kind{margin:9px 10px 0;font-size:6px;padding:3px 5px}.news-body{padding:6px 10px 9px}.news h3{font-size:12px;line-height:1.3;margin:0;-webkit-line-clamp:2}.news p{display:none}.news-footer{padding-top:4px}.date,.read{font-size:7px!important}.folders{grid-template-columns:repeat(2,minmax(0,1fr));gap:9px}.folder{border-radius:14px}.folder-body{padding:9px 9px 10px}.folder-body h3{font-size:10px}.folder-meta{display:block;font-size:7px!important;margin-top:7px}.folder-count{display:block;margin-top:3px}.folder-meta span{gap:4px}.cta-card{display:block;padding:20px;border-radius:17px}.cta-card h2{font-size:24px}.cta-card p{font-size:9px}.cta-card .btn{display:inline-flex;margin-top:18px;min-height:40px}
}
@media(max-width:380px){
.shell{width:calc(100% - 18px)}.welcome{padding-top:41px}.welcome h1{font-size:29px}.welcome-preview,.welcome-more-content{font-size:13px}.stats-grid,.project-grid,.management-grid{grid-template-columns:1fr}.project-media{aspect-ratio:16/10}.news{grid-template-columns:94px 1fr;min-height:94px}.news-media{width:94px;height:94px}.folders{grid-template-columns:1fr}.head h2{font-size:23px}
}
@media(prefers-reduced-motion:reduce){.slide,.welcome:before,.welcome:after,.section,.project-card,.member-card,.folder,.news{animation:none;transition:none}.slide.is-active{transform:none}}
</style>
<main class="shell">
@foreach($home['section_order'] as $section)
@if($section==='hero' && $home['slider'] && $sliders->isNotEmpty())
<section class="home-slider" aria-label="Company highlights">
<div class="slider-track">
@foreach($sliders as $index => $slider)
@php($sliderUrl=$slider->link_url)
@if($sliderUrl)<a class="slide {{ $index===0?'is-active':'' }}" href="{{ $sliderUrl }}" @if(str_starts_with($sliderUrl,'http')) target="_blank" rel="noopener" @endif>@else<div class="slide {{ $index===0?'is-active':'' }}">@endif
<div class="slide-media"><img src="{{ asset('storage/'.ltrim($slider->image_path,'/')) }}" alt="{{ $slider->title ?: $siteName }}" @if($index>0)loading="lazy"@endif></div>
@if($slider->title)<div class="slide-caption" aria-label="{{ $slider->title }}"><strong>{{ $slider->title }}</strong></div>@endif
@if($sliderUrl)</a>@else</div>@endif
@endforeach
</div>
</section>
@endif

@if($section==='welcome' && $home['welcome'])
<section class="welcome welcome-layout-{{ $welcomeLayout }}" data-welcome>
<div class="welcome-heading"><span class="eyebrow">{{ $welcomeEyebrow ?: 'Welcome to '.$siteName }}</span><h1>{{ $welcomeTitle ?: 'Building a stronger energy future.' }}</h1><div class="welcome-rule"></div></div>
<div class="welcome-copy">
<div class="welcome-preview">{!! nl2br(e($welcomePreview)) !!}</div>
@if($welcomeHasMore)
<div class="welcome-more-content" hidden>{!! nl2br(e($welcomeRemaining)) !!}</div>
<button type="button" class="welcome-more-toggle" aria-expanded="false"><span>Read more</span><i class="fa-solid fa-arrow-down"></i></button>
@endif
@if($welcomeSignoff !== '')<div class="welcome-signoff">{{ $welcomeSignoff }}</div>@endif
</div>
</section>
@endif

@if($section==='statistics' && $home['statistics'])
<section class="section section-layout-{{ ($sectionSettings['statistics'] ?? [])['layout'] ?? 'left' }}"><div class="head"><div><span class="eyebrow">Power at a glance</span><h2>Our footprint.</h2></div><p>Key figures are calculated directly from the power plant records managed in the admin portal.</p></div>
<div class="stats-grid">
<div class="stat-card"><i class="fa-solid fa-industry"></i><strong>{{ number_format($stats['projects']) }}</strong><span>Projects</span></div>
<div class="stat-card"><i class="fa-solid fa-bolt"></i><strong>{{ number_format($stats['capacity_mw'],2) }} MW</strong><span>Total capacity</span></div>
<div class="stat-card"><i class="fa-solid fa-circle-check"></i><strong>{{ number_format($stats['operational']) }}</strong><span>Operational plants</span></div>
<div class="stat-card"><i class="fa-solid fa-leaf"></i><strong>Future-ready</strong><span>Energy development</span></div>
</div></section>
@endif

@if($section==='projects' && $home['projects'])
<section class="section section-layout-{{ ($sectionSettings['projects'] ?? [])['layout'] ?? 'left' }}"><div class="head"><div><span class="eyebrow">Our power plants</span><h2>Projects &amp; plants.</h2></div><a class="more" href="{{ route('site.plants') }}">View all →</a></div>
<div class="project-grid">
@if($plants->isNotEmpty())
@foreach($plants as $plant)
<a class="project-card" href="{{ route('projects.show',$plant->slug) }}">
<div class="project-media">@if($plant->image_path)<img src="{{ asset('storage/'.$plant->image_path) }}" alt="{{ $plant->name }}" loading="lazy">@else<i class="fa-solid fa-industry"></i>@endif</div>
<div class="project-body"><div class="project-top"><span>{{ ucfirst(str_replace('_',' ',$plant->status)) }}</span><i class="fa-solid fa-arrow-up-right-from-square"></i></div><h3>{{ $plant->name }}</h3><p>{{ $plant->location ?: $plant->technology ?: 'Power generation project' }}</p><strong>{{ number_format((float)$plant->capacity_kw/1000,2) }} MW</strong></div>
</a>
@endforeach
@else<div class="empty" style="grid-column:1/-1">No power plant projects have been published yet.</div>@endif
</div></section>
@endif

@if($section==='management' && $home['management'])

<section class="section section-layout-{{ ($sectionSettings['management'] ?? [])['layout'] ?? 'left' }}"><div class="head"><div><span class="eyebrow">Leadership</span><h2>Management team.</h2></div><a class="more" href="{{ route('management') }}">Meet the team →</a></div>
<div class="management-grid">
@if($homeManagement->isNotEmpty())
@foreach($homeManagement as $member)
<a class="member-card" href="{{ route('management') }}#member-{{ $member->id }}"><div class="member-photo">@if($member->image_path)<img src="{{ asset('storage/'.$member->image_path) }}" alt="{{ $member->title }}" loading="lazy">@else<i class="fa-solid fa-user"></i>@endif</div><div><h3>{{ $member->title }}</h3><p>{{ $member->designation ?: $member->excerpt }}</p></div></a>
@endforeach
@else<div class="empty" style="grid-column:1/-1">Management profiles will appear here when published.</div>@endif
</div></section>
@endif

@if($section==='news' && $home['news'])
<section class="section section-layout-{{ ($sectionSettings['news'] ?? [])['layout'] ?? 'left' }}"><div class="head"><div><span class="eyebrow">Latest updates</span><h2>News &amp; Notices</h2></div><a class="more" href="{{ route('news.index') }}">View all →</a></div><div class="news-grid">@if(($content['news']??collect())->isNotEmpty())@foreach(($content['news']??collect()) as $item)<a class="news" href="{{ route('news.show',$item->slug) }}"><div class="news-media">@if($item->image_path)<img src="{{ asset('storage/'.$item->image_path) }}" alt="{{ $item->cover_alt ?: $item->title }}" loading="lazy">@else<div class="news-placeholder">▣</div>@endif</div><div class="news-kind {{ $item->type==='announcement'?'notice':'' }}">{{ $item->type==='announcement'?'Notice':'News' }}</div><div class="news-body"><h3>{{ $item->title }}</h3><p>{{ $item->excerpt ?: \Illuminate\Support\Str::limit(strip_tags((string) $item->content), 180) }}</p><div class="news-footer"><span class="date">{{ $item->published_at?->format('d F Y') }}</span><span class="read">Read more →</span></div></div></a>@endforeach @else<div class="empty" style="grid-column:1/-1">No news has been published yet.</div>@endif</div></section>
@endif

@if($section==='gallery' && $home['gallery'])
<section class="section section-layout-{{ ($sectionSettings['gallery'] ?? [])['layout'] ?? 'left' }}"><div class="head"><div><span class="eyebrow">Photo collections</span><h2>Gallery</h2></div><a class="more" href="{{ route('site.gallery') }}">View all →</a></div><div class="folders">@if($gallery->isNotEmpty())@foreach($gallery as $item)<a class="folder" href="{{ route('gallery.show',['item'=>$item->slug ?: $item->id]) }}"><div class="folder-media">@if($item->image_path)<img src="{{ asset('storage/'.ltrim($item->image_path,'/')) }}" alt="{{ $item->cover_alt ?: $item->title }}" loading="lazy">@else<div class="folder-placeholder"><i class="fa-regular fa-images"></i></div>@endif</div><div class="folder-body"><h3>{{ $item->title }}</h3><div class="folder-meta"><span class="folder-date"><i class="fa-regular fa-calendar"></i>{{ $item->published_at?->format('d F Y') ?? $item->created_at?->format('d F Y') }}</span><span class="folder-count"><i class="fa-regular fa-images"></i>{{ $item->gallery_media_count }} {{ $item->gallery_media_count === 1 ? 'photo' : 'photos' }}</span></div></div></a>@endforeach @else<div class="empty" style="grid-column:1/-1">No photo galleries have been published yet.</div>@endif</div></section>
@endif

@if($section==='cta' && $home['cta'])
<section class="section section-layout-{{ ($sectionSettings['cta'] ?? [])['layout'] ?? 'left' }} cta-section"><div class="cta-card"><div><span class="eyebrow">Let's build the future</span><h2>Reliable energy. Responsible growth.</h2><p>{{ $brand['tagline'] }}</p></div><a class="btn" href="{{ route('contact') }}">Contact us <i class="fa-solid fa-arrow-right"></i></a></div></section>
@endif
@endforeach
</main>
<script>
(() => {
 document.querySelectorAll('[data-welcome]').forEach(section => {
  const button=section.querySelector('.welcome-more-toggle'), more=section.querySelector('.welcome-more-content');
  if(!button||!more)return;
  button.addEventListener('click',()=>{const open=button.getAttribute('aria-expanded')==='true';button.setAttribute('aria-expanded',String(!open));more.hidden=open;button.querySelector('span').textContent=open?'Read more':'Show less';});
 });
})();
</script>
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
