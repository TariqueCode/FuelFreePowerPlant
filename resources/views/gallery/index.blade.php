@php($siteName=$brand['name'])
<!doctype html>
<html lang="en">
<head>
<meta charset="utf-8"><meta name="viewport" content="width=device-width,initial-scale=1,viewport-fit=cover">
<title>Gallery — {{ $siteName }}</title>
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.7.2/css/all.min.css">
<style>
:root{--bg:#03090e;--panel:#071720;--text:#effcff;--muted:#91aeb8;--line:rgba(93,211,238,.16);--cyan:#4fd2ee;--cyan2:#a8f5ff}*{box-sizing:border-box}html{scroll-behavior:smooth}body{margin:0;background:radial-gradient(circle at 80% 0,rgba(23,142,177,.16),transparent 30%),linear-gradient(180deg,#020a10,#061721 55%,#020a10);color:var(--text);font-family:Inter,system-ui,sans-serif;overflow-x:hidden}a{text-decoration:none;color:inherit}.shell{width:min(1180px,calc(100% - 32px));margin:auto}.header{position:sticky;top:0;z-index:30;background:rgba(2,10,16,.82);backdrop-filter:blur(18px);border-bottom:1px solid var(--line)}.nav{height:70px;display:flex;align-items:center;justify-content:space-between;gap:15px}.brand{display:flex;align-items:center;gap:10px;font-weight:850;min-width:0}.brand img{width:40px;height:40px;object-fit:contain;border-radius:10px}.brand i{color:var(--cyan)}.brand span{white-space:nowrap;overflow:hidden;text-overflow:ellipsis}.back{font-size:10px;color:#b4ced5;padding:10px 13px;border:1px solid var(--line);border-radius:10px;transition:.2s}.back:hover{background:rgba(79,210,238,.08);color:#fff}.hero{padding:65px 0 34px}.eyebrow{display:inline-flex;align-items:center;gap:9px;font-size:9px;letter-spacing:.2em;color:var(--cyan);text-transform:uppercase}.eyebrow:before{content:"";width:28px;height:1px;background:var(--cyan)}.hero h1{font-size:clamp(43px,6vw,72px);letter-spacing:-.055em;margin:12px 0}.hero p{max-width:760px;color:var(--muted);line-height:1.85;font-size:12px}.gallery-list{display:grid;gap:24px;padding-bottom:70px}.gallery-item{scroll-margin-top:90px;border:1px solid var(--line);border-radius:24px;overflow:hidden;background:linear-gradient(145deg,rgba(8,38,52,.9),rgba(3,21,30,.88));box-shadow:0 20px 50px rgba(0,0,0,.12)}.gallery-head{padding:21px 23px 15px;display:flex;justify-content:space-between;align-items:end;gap:15px}.gallery-head h2{margin:0;font-size:22px}.date{color:#7ed9e9;font-size:9px;white-space:nowrap}.date i{margin-right:5px;color:var(--cyan)}.gallery-desc{padding:0 23px 18px;color:var(--muted);font-size:10px;line-height:1.8}.media-grid{display:grid;grid-template-columns:repeat(3,minmax(0,1fr));gap:8px;padding:0 10px 10px}.media-grid img,.media-grid video,.media-grid iframe{width:100%;height:230px;display:block;object-fit:cover;border:0;border-radius:14px;background:#061923}.media-grid img{cursor:zoom-in;transition:transform .3s,filter .3s}.media-grid img:hover{transform:scale(1.015);filter:brightness(1.08)}.media-grid video{object-fit:cover}.media-grid iframe{min-height:230px}.media-grid>*:only-child{grid-column:1/-1;height:auto;max-height:650px;object-fit:contain}.gallery-rich-content{grid-column:1/-1;display:contents}.gallery-rich-content img{height:230px}.gallery-rich-content figure{margin:0}.gallery-rich-content a{color:var(--cyan2)}.gallery-rich-content p{color:var(--muted);font-size:10px;line-height:1.7;padding:0 10px}.empty{padding:60px 20px;text-align:center;color:var(--muted);border:1px dashed var(--line);border-radius:20px}.empty i{font-size:34px;color:var(--cyan)}.lightbox{position:fixed;inset:0;z-index:100;display:none;align-items:center;justify-content:center;padding:18px;background:rgba(0,6,10,.95);backdrop-filter:blur(14px)}.lightbox.open{display:flex}.lightbox img{max-width:94vw;max-height:90vh;width:auto;height:auto;object-fit:contain;border-radius:13px;box-shadow:0 20px 80px rgba(0,0,0,.5)}.close{position:fixed;right:18px;top:18px;width:44px;height:44px;border:1px solid rgba(255,255,255,.15);border-radius:12px;background:rgba(255,255,255,.07);color:#fff;cursor:pointer}.close:hover{background:rgba(255,255,255,.12)}@media(max-width:800px){.media-grid{grid-template-columns:repeat(2,minmax(0,1fr))}.media-grid img,.media-grid video,.media-grid iframe,.gallery-rich-content img{height:190px}}@media(max-width:520px){.shell{width:calc(100% - 24px)}.nav{height:66px}.hero{padding:46px 0 26px}.gallery-head{display:block;padding:18px}.date{display:block;margin-top:8px}.gallery-desc{padding:0 18px 15px}.media-grid{grid-template-columns:1fr 1fr;padding:0 7px 7px;gap:6px}.media-grid img,.media-grid video,.media-grid iframe,.gallery-rich-content img{height:150px}.back{font-size:9px;padding:9px 10px}.brand span{font-size:12px}}
</style>
</head>
<body>
<header class="header"><div class="shell nav"><a class="brand" href="{{ route('home') }}">@if($brand['logo_path'])<img src="{{ asset('storage/'.$brand['logo_path']) }}" alt="{{ $siteName }}">@else<i class="fa-solid fa-images"></i>@endif<span>{{ $siteName }}</span></a><a class="back" href="{{ route('home') }}"><i class="fa-solid fa-arrow-left"></i> Back to website</a></div></header>
<main class="shell">
<section class="hero"><span class="eyebrow">Moments &amp; milestones</span><h1>Gallery</h1><p>Explore events, activities, milestones and selected moments from {{ $siteName }}.</p></section>
<section class="gallery-list">
@forelse($items as $item)
<article class="gallery-item" id="gallery-{{ $item->id }}">
<div class="gallery-head"><h2>{{ $item->title }}</h2><span class="date"><i class="fa-solid fa-calendar-days"></i>{{ $item->published_at?->format('d F Y') }}</span></div>
@if($item->excerpt)<div class="gallery-desc">{{ $item->excerpt }}</div>@endif
<div class="media-grid">
@if($item->image_path)<img src="{{ asset('storage/'.$item->image_path) }}" alt="{{ $item->title }}" loading="lazy">@endif
@if($item->safe_content)<div class="gallery-rich-content">{!! $item->safe_content !!}</div>@endif
@if(!$item->image_path && !$item->safe_content)<div class="empty" style="grid-column:1/-1"><i class="fa-solid fa-image"></i><br><br>No media has been added to this gallery item yet.</div>@endif
</div>
</article>
@empty
<div class="empty"><i class="fa-solid fa-images"></i><br><br>No gallery moments have been published yet.</div>
@endforelse
</section>
</main>
<div class="lightbox" id="lightbox" aria-hidden="true"><button class="close" type="button" aria-label="Close"><i class="fa-solid fa-xmark"></i></button><img id="lightboxImage" alt=""></div>
<script>
const box=document.getElementById('lightbox'),lightImg=document.getElementById('lightboxImage');document.querySelectorAll('.media-grid img').forEach(img=>img.addEventListener('click',()=>{lightImg.src=img.currentSrc||img.src;lightImg.alt=img.alt||'';box.classList.add('open');box.setAttribute('aria-hidden','false')}));function closeLightbox(){box.classList.remove('open');box.setAttribute('aria-hidden','true');lightImg.removeAttribute('src')}document.querySelector('.close').addEventListener('click',closeLightbox);box.addEventListener('click',e=>{if(e.target===box)closeLightbox()});document.addEventListener('keydown',e=>{if(e.key==='Escape')closeLightbox()});
</script>
</body></html>
