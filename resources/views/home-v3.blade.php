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
.welcome-copy{max-width:900px;padding:30px 0 0;position:relative}.welcome-layout-center .welcome-heading,.welcome-layout-center .welcome-copy{text-align:center;margin-inline:auto}.welcome-layout-right .welcome-heading,.welcome-layout-right .welcome-copy{margin-left:auto;text-align:right}.welcome-more-content{margin-top:16px;color:var(--muted);line-height:1.85}.welcome-more-toggle{display:inline-flex;align-items:center;gap:8px;margin-top:18px;padding:10px 14px;border:1px solid var(--line);border-radius:999px;background:rgba(72,216,241,.05);color:var(--cyan);font-size:11px;font-weight:700;cursor:pointer}.welcome-more-toggle i{transition:transform .2s}.welcome-more-toggle[aria-expanded=true] i{transform:rotate(180deg)}.welcome-preview{color:var(--muted);font-size:15px;line-height:1.85;white-space:normal}
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


.stats-grid{display:grid;grid-template-columns:repeat(4,minmax(0,1fr));gap:14px}.stat-card{padding:24px;border:1px solid var(--line);border-radius:18px;background:linear-gradient(145deg,rgba(8,37,50,.92),rgba(3,19,27,.96));min-width:0}.stat-card i{color:var(--cyan);font-size:18px}.stat-card strong{display:block;font-size:30px;line-height:1.15;margin-top:18px;letter-spacing:-.03em}.stat-card span{display:block;color:#789aa5;font-size:10px;margin-top:7px}.project-grid{display:grid;grid-template-columns:repeat(3,minmax(0,1fr));gap:16px}.project-card{border:1px solid var(--line);border-radius:18px;overflow:hidden;background:linear-gradient(145deg,rgba(8,37,50,.92),rgba(3,19,27,.96));transition:.25s}.project-card:hover{transform:translateY(-4px);border-color:rgba(72,216,241,.4)}.project-media{height:180px;background:#061923;display:grid;place-items:center;color:#5e8490;font-size:30px;overflow:hidden}.project-media img{width:100%;height:100%;object-fit:cover}.project-body{padding:17px}.project-top{display:flex;justify-content:space-between;gap:8px;color:#72dfbf;font-size:8px;text-transform:uppercase;letter-spacing:.12em}.project-top i{color:var(--cyan)}.project-body h3{font-size:17px;margin:11px 0 7px}.project-body p{color:var(--muted);font-size:10px;margin:0 0 14px}.project-body>strong{font-size:12px;color:#a9f5ff}.management-grid{display:grid;grid-template-columns:repeat(4,minmax(0,1fr));gap:14px}.member-card{border:1px solid var(--line);border-radius:18px;overflow:hidden;background:linear-gradient(145deg,rgba(8,37,50,.92),rgba(3,19,27,.96));transition:.25s}.member-card:hover{transform:translateY(-4px);border-color:rgba(72,216,241,.4)}.member-photo{aspect-ratio:1/1;background:#061923;display:grid;place-items:center;color:#5e8490;font-size:34px;overflow:hidden}.member-photo img{width:100%;height:100%;object-fit:cover}.member-card>div:last-child{padding:15px}.member-card h3{margin:0;font-size:14px}.member-card p{margin:7px 0 0;color:var(--muted);font-size:9px;line-height:1.5}.cta-card{display:flex;align-items:center;justify-content:space-between;gap:24px;padding:34px;border:1px solid var(--line);border-radius:24px;background:linear-gradient(120deg,rgba(10,54,70,.95),rgba(4,24,34,.95));box-shadow:0 20px 60px rgba(0,0,0,.16)}.cta-card h2{margin:9px 0 8px;font-size:clamp(25px,4vw,42px);letter-spacing:-.04em}.cta-card p{margin:0;color:var(--muted);font-size:12px;line-height:1.7}.cta-card .btn{flex:0 0 auto}
@media(max-width:900px){.stats-grid{grid-template-columns:repeat(2,1fr)}.project-grid{grid-template-columns:repeat(2,1fr)}.management-grid{grid-template-columns:repeat(2,1fr)}}
@media(max-width:600px){.stats-grid,.project-grid,.management-grid{grid-template-columns:1fr 1fr;gap:10px}.stat-card{padding:17px}.stat-card strong{font-size:23px;margin-top:13px}.project-media{height:135px}.project-body{padding:13px}.project-body h3{font-size:14px}.cta-card{display:block;padding:25px}.cta-card .btn{display:inline-flex;margin-top:20px}}
@media(max-width:380px){.stats-grid,.project-grid,.management-grid{grid-template-columns:1fr}.project-media{height:180px}}
<style>
/* Final responsive polish: consistent typography, spacing and touch targets */
@media(max-width:650px){
  .shell{width:calc(100% - 24px)}
  .welcome{padding:44px 0 42px}
  .welcome-heading{padding-bottom:18px}
  .welcome .eyebrow{font-size:9px!important;letter-spacing:.15em;line-height:1.4}
  .welcome h1{font-size:clamp(31px,9.2vw,44px);line-height:1.06;letter-spacing:-.045em;margin:9px 0 0;max-width:100%;overflow-wrap:anywhere}
  .welcome-rule{width:44px;height:2px;margin-top:13px}
  .welcome-copy{padding-top:18px}
  .welcome-copy p{font-size:14px!important;line-height:1.75;text-align:left;margin:0 0 13px}
  .welcome-signoff{font-size:12px;line-height:1.5;margin-top:17px}
  .section{padding:42px 0}
  .head{align-items:flex-start;margin-bottom:15px;gap:10px}
  .head>div{min-width:0}
  .head h2{font-size:clamp(23px,7vw,31px);line-height:1.08;letter-spacing:-.035em;margin:5px 0 0}
  .head p{font-size:10px!important;line-height:1.65!important;margin:8px 0 0}
  .more{font-size:9px!important;padding-top:7px}
  .home-slider{margin-top:5px}
  .slide-media{border-radius:14px}
  .slide-caption{padding:0 6px}
  .slide-caption strong{font-size:8px;line-height:1.3}
  .stats-grid{grid-template-columns:repeat(2,minmax(0,1fr));gap:9px}
  .stat-card{padding:14px;border-radius:14px;min-height:104px}
  .stat-card i{font-size:15px}
  .stat-card strong{font-size:20px;line-height:1.12;margin-top:10px;overflow-wrap:anywhere}
  .stat-card span{font-size:8px;line-height:1.35;margin-top:6px}
  .project-grid,.management-grid{grid-template-columns:repeat(2,minmax(0,1fr));gap:9px}
  .project-card,.member-card{border-radius:14px}
  .project-media{height:125px}
  .project-body{padding:11px}
  .project-body h3{font-size:13px;line-height:1.3;margin:8px 0 5px}
  .project-body p{font-size:8px;line-height:1.45;margin-bottom:9px}
  .project-body>strong{font-size:10px}
  .member-card>div:last-child{padding:11px}
  .member-card h3{font-size:12px;line-height:1.3}
  .member-card p{font-size:8px;line-height:1.45}
  .cta-card{padding:21px;border-radius:17px}
  .cta-card h2{font-size:25px;line-height:1.1}
  .cta-card p{font-size:10px;line-height:1.65}
  .cta-card .btn{min-height:42px}
}
@media(max-width:380px){
  .shell{width:calc(100% - 20px)}
  .welcome{padding-top:38px}
  .welcome h1{font-size:30px}
  .welcome-copy p{font-size:13px!important}
  .stats-grid,.project-grid,.management-grid{grid-template-columns:1fr}
  .project-media{height:170px}
  .head h2{font-size:24px}
}
@media(min-width:651px) and (max-width:1099px){
  .welcome h1{font-size:clamp(40px,6vw,64px);line-height:1.02}
  .head h2{line-height:1.08}
  .project-grid{gap:13px}
  .management-grid{gap:13px}
}
</style>
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
<div class="welcome-signoff">{{ $siteName }} <span>— Powering a cleaner, smarter future.</span></div>
</div>
</section>
@endif

@if($section==='statistics' && $home['statistics'])
<section class="section"><div class="head"><div><span class="eyebrow">Power at a glance</span><h2>Our footprint.</h2></div><p>Key figures are calculated directly from the power plant records managed in the admin portal.</p></div>
<div class="stats-grid">
<div class="stat-card"><i class="fa-solid fa-industry"></i><strong>{{ number_format($stats['projects']) }}</strong><span>Projects</span></div>
<div class="stat-card"><i class="fa-solid fa-bolt"></i><strong>{{ number_format($stats['capacity_mw'],2) }} MW</strong><span>Total capacity</span></div>
<div class="stat-card"><i class="fa-solid fa-circle-check"></i><strong>{{ number_format($stats['operational']) }}</strong><span>Operational plants</span></div>
<div class="stat-card"><i class="fa-solid fa-leaf"></i><strong>Future-ready</strong><span>Energy development</span></div>
</div></section>
@endif

@if($section==='projects' && $home['projects'])
<section class="section"><div class="head"><div><span class="eyebrow">Our power plants</span><h2>Projects &amp; plants.</h2></div><a class="more" href="{{ route('site.plants') }}">View all →</a></div>
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

<section class="section"><div class="head"><div><span class="eyebrow">Leadership</span><h2>Management team.</h2></div><a class="more" href="{{ route('management') }}">Meet the team →</a></div>
<div class="management-grid">
@if($homeManagement->isNotEmpty())
@foreach($homeManagement as $member)
<a class="member-card" href="{{ route('management') }}#member-{{ $member->id }}"><div class="member-photo">@if($member->image_path)<img src="{{ asset('storage/'.$member->image_path) }}" alt="{{ $member->title }}" loading="lazy">@else<i class="fa-solid fa-user"></i>@endif</div><div><h3>{{ $member->title }}</h3><p>{{ $member->designation ?: $member->excerpt }}</p></div></a>
@endforeach
@else<div class="empty" style="grid-column:1/-1">Management profiles will appear here when published.</div>@endif
</div></section>
@endif

@if($section==='news' && $home['news'])
<section class="section"><div class="head"><div><span class="eyebrow">Latest updates</span><h2>News &amp; Notices</h2></div><a class="more" href="{{ route('news.index') }}">View all →</a></div><div class="news-grid">@if(($content['news']??collect())->isNotEmpty())@foreach(($content['news']??collect()) as $item)<a class="news" href="{{ route('news.show',$item->slug) }}"><div class="news-media">@if($item->image_path)<img src="{{ asset('storage/'.$item->image_path) }}" alt="{{ $item->cover_alt ?: $item->title }}" loading="lazy">@else<div class="news-placeholder">▣</div>@endif</div><div class="news-kind {{ $item->type==='announcement'?'notice':'' }}">{{ $item->type==='announcement'?'Notice':'News' }}</div><div class="news-body"><h3>{{ $item->title }}</h3><p>{{ $item->excerpt ?: \Illuminate\Support\Str::limit(strip_tags((string) $item->content), 180) }}</p><div class="news-footer"><span class="date">{{ $item->published_at?->format('d F Y') }}</span><span class="read">Read more →</span></div></div></a>@endforeach @else<div class="empty" style="grid-column:1/-1">No news has been published yet.</div>@endif</div></section>
@endif

@if($section==='gallery' && $home['gallery'])
<section class="section"><div class="head"><div><span class="eyebrow">Photo collections</span><h2>Gallery</h2></div><a class="more" href="{{ route('site.gallery') }}">View all →</a></div><div class="folders">@if($gallery->isNotEmpty())@foreach($gallery as $item)<a class="folder" href="{{ route('gallery.show',['item'=>$item->slug ?: $item->id]) }}"><div class="folder-media">@if($item->image_path)<img src="{{ asset('storage/'.ltrim($item->image_path,'/')) }}" alt="{{ $item->cover_alt ?: $item->title }}" loading="lazy">@else<div class="folder-placeholder"><i class="fa-regular fa-images"></i></div>@endif</div><div class="folder-body"><h3>{{ $item->title }}</h3><div class="folder-meta"><span class="folder-date"><i class="fa-regular fa-calendar"></i>{{ $item->published_at?->format('d F Y') ?? $item->created_at?->format('d F Y') }}</span><span class="folder-count"><i class="fa-regular fa-images"></i>{{ $item->gallery_media_count }} {{ $item->gallery_media_count === 1 ? 'photo' : 'photos' }}</span></div></div></a>@endforeach @else<div class="empty" style="grid-column:1/-1">No photo galleries have been published yet.</div>@endif</div></section>
@endif

@if($section==='cta' && $home['cta'])
<section class="section cta-section"><div class="cta-card"><div><span class="eyebrow">Let's build the future</span><h2>Reliable energy. Responsible growth.</h2><p>{{ $brand['tagline'] }}</p></div><a class="btn" href="{{ route('contact') }}">Contact us <i class="fa-solid fa-arrow-right"></i></a></div></section>
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
