@extends('layouts.public')

@php
    $brand=\App\Models\SystemSetting::query()->whereIn('key',['company.name','company.logo_path','company.tagline'])->pluck('value','key');
    $name=$brand->get('company.name')?:config('fuelfree.company.name');
@endphp

@section('title', 'Management — '.$name)

@section('content')
<style>
:root{
    font-family:Inter,system-ui,-apple-system,BlinkMacSystemFont,"Segoe UI",sans-serif;
    color:#eaf8fb;
    background:#020b12;
    --line:rgba(91,214,239,.14);
    --line-strong:rgba(91,214,239,.28);
    --muted:#8eaab5;
    --muted-2:#6f8d99;
    --cyan:#43d1f0;
    --cyan-soft:#73def3;
    --panel:#061b26;
    --panel-2:#082330;
}
*{box-sizing:border-box}
body{
    margin:0;
    background:
        radial-gradient(900px 420px at 50% -120px,rgba(27,155,192,.20),transparent 68%),
        radial-gradient(700px 420px at 100% 25%,rgba(14,92,119,.12),transparent 70%),
        linear-gradient(180deg,#020a11 0%,#03131d 48%,#020a11 100%);
    min-height:100vh;
}
.shell{width:min(1240px,calc(100% - 48px));margin:auto}
main{padding:72px 0 88px}
.hero{margin:0 auto 38px;max-width:920px}
.eyebrow{
    display:inline-flex;
    align-items:center;
    gap:9px;
    font-size:10px;
    letter-spacing:.22em;
    color:var(--cyan);
    text-transform:uppercase;
    font-weight:800;
}
.eyebrow:before{
    content:"";
    width:28px;
    height:1px;
    background:linear-gradient(90deg,transparent,var(--cyan));
}
.hero h1{
    font-size:clamp(38px,5vw,64px);
    line-height:1.05;
    letter-spacing:-.045em;
    margin:13px 0 14px;
    font-weight:800;
}
.hero p{
    color:var(--muted);
    max-width:700px;
    line-height:1.75;
    font-size:14px;
    margin:0;
}
.grid{
    display:grid;
    grid-template-columns:repeat(3,minmax(0,1fr));
    gap:24px;
    align-items:start;
}
.card{
    min-width:0;
    overflow:hidden;
    border:1px solid var(--line);
    border-radius:24px;
    background:
        linear-gradient(155deg,rgba(9,42,57,.94),rgba(3,21,30,.98) 62%,rgba(2,15,22,.99));
    box-shadow:0 18px 55px rgba(0,0,0,.20),inset 0 1px 0 rgba(255,255,255,.025);
    transition:transform .22s ease,border-color .22s ease,box-shadow .22s ease;
}
.card:hover{
    transform:translateY(-5px);
    border-color:var(--line-strong);
    box-shadow:0 24px 70px rgba(0,0,0,.30),0 0 0 1px rgba(67,209,240,.035);
}
.photo{
    aspect-ratio:4/4.65;
    background:#eef2f3;
    overflow:hidden;
    display:grid;
    place-items:center;
    cursor:pointer;
    position:relative;
}
.photo:after{
    content:"";
    position:absolute;
    inset:0;
    pointer-events:none;
    background:linear-gradient(180deg,rgba(255,255,255,.02),transparent 65%,rgba(0,12,18,.08));
}
.photo img{width:100%;height:100%;object-fit:cover;display:block}
.photo i{font-size:46px;color:#4fd5ef}
.body{padding:21px 21px 22px}
.body h2{
    font-size:20px;
    line-height:1.3;
    letter-spacing:-.02em;
    margin:0;
    color:#edf9fb;
    font-weight:800;
}
.role{
    margin:7px 0 0;
    color:var(--cyan-soft);
    font-size:11px;
    line-height:1.5;
    font-weight:800;
    letter-spacing:.01em;
}
.bio{
    margin:14px 0;
    color:#89a8b2;
    font-size:11px;
    line-height:1.75;
}
.bio-preview{
    display:-webkit-box;
    -webkit-box-orient:vertical;
    -webkit-line-clamp:5;
    overflow:hidden;
    max-height:97px;
}
.bio-full{
    display:none;
    margin:14px 0;
    color:#89a8b2;
    font-size:11px;
    line-height:1.75;
}
.bio-full.open{display:block}
.bio-more{
    display:inline-flex;
    align-items:center;
    gap:7px;
    margin:0 0 14px;
    padding:7px 10px;
    border:1px solid var(--line);
    border-radius:9px;
    background:rgba(67,209,240,.05);
    color:#75d9eb;
    font-size:10px;
    font-weight:800;
    cursor:pointer;
}
.bio-more:hover{background:rgba(67,209,240,.1);color:#fff}
.contact-list{
    display:grid;
    gap:8px;
    padding:14px 0;
    border-top:1px solid rgba(86,210,238,.09);
    border-bottom:1px solid rgba(86,210,238,.09);
}
.contact{
    min-width:0;
    display:flex;
    align-items:center;
    gap:9px;
    color:#9db7c0;
    text-decoration:none;
    font-size:10px;
    line-height:1.45;
}
.contact:hover{color:#eaf8fb}
.contact i{
    flex:0 0 26px;
    width:26px;
    height:26px;
    display:grid;
    place-items:center;
    border-radius:8px;
    background:rgba(67,209,240,.06);
    color:#58cee8;
}
.contact{
    overflow-wrap:anywhere;
    word-break:break-word;
}
.actions{
    display:grid;
    grid-template-columns:1fr 1fr;
    gap:8px;
    margin-top:13px;
}
.action{
    min-width:0;
    display:flex;
    align-items:center;
    justify-content:center;
    gap:7px;
    padding:11px 8px;
    border-radius:10px;
    text-decoration:none;
    font-size:10px;
    font-weight:800;
    border:1px solid var(--line);
    color:#b9d8df;
    transition:.2s ease;
}
.action:hover{
    border-color:var(--line-strong);
    background:rgba(67,209,240,.05);
    color:#fff;
}
.action.primary{
    background:linear-gradient(135deg,#2db1cf,#17758d);
    color:#fff;
    border-color:rgba(86,220,243,.25);
}
.action.primary:hover{
    background:linear-gradient(135deg,#39c2df,#1b829b);
    color:#fff;
}
.empty{
    border:1px dashed var(--line);
    border-radius:18px;
    padding:55px 20px;
    text-align:center;
    color:var(--muted);
    grid-column:1/-1;
}
.lightbox{
    position:fixed;
    inset:0;
    z-index:100;
    background:rgba(0,6,10,.94);
    backdrop-filter:blur(14px);
    display:none;
    align-items:center;
    justify-content:center;
    padding:18px;
}
.lightbox.open{display:flex}
.lightbox-content{
    max-width:96vw;
    max-height:90vh;
    overflow:hidden;
    display:flex;
    align-items:center;
    justify-content:center;
}
.lightbox img{
    max-width:90vw;
    max-height:82vh;
    width:auto;
    height:auto;
    object-fit:contain;
    border-radius:12px;
    box-shadow:0 25px 80px rgba(0,0,0,.55);
    transform:scale(1);
    transition:transform .15s ease;
    touch-action:none;
}
.zoom-controls{
    position:fixed;
    left:50%;
    bottom:22px;
    transform:translateX(-50%);
    display:flex;
    gap:6px;
    padding:6px;
    border:1px solid rgba(255,255,255,.12);
    border-radius:14px;
    background:rgba(2,11,18,.82);
    backdrop-filter:blur(12px);
}
.zoom-controls button,.lightbox-close{
    width:40px;
    height:40px;
    border:1px solid rgba(255,255,255,.14);
    border-radius:10px;
    background:rgba(255,255,255,.05);
    color:#fff;
    cursor:pointer;
}
.lightbox-close{
    position:fixed;
    top:18px;
    right:18px;
    font-size:18px;
}
.zoom-level{
    min-width:52px;
    display:grid;
    place-items:center;
    color:#9db7c0;
    font-size:9px;
}
@media(max-width:1050px){
    .shell{width:min(960px,calc(100% - 40px))}
    .grid{gap:18px}
    .body{padding:18px}
}
@media(max-width:850px){
    main{padding:55px 0 70px}
    .grid{grid-template-columns:repeat(2,minmax(0,1fr));gap:18px}
    .hero{margin-bottom:30px}
}
@media(max-width:600px){
    .shell{width:calc(100% - 24px)}
    main{padding:40px 0 58px}
    .hero h1{font-size:clamp(34px,10vw,46px)}
    .hero p{font-size:12px;line-height:1.7}
    .grid{grid-template-columns:1fr;gap:16px}
    .card{border-radius:20px}
    .photo{aspect-ratio:4/4.7}
    .body{padding:17px}
    .body h2{font-size:18px}
    .bio,.bio-full{font-size:10px}
    .contact{font-size:10px}
    .lightbox img{max-width:94vw;max-height:78vh}
    .lightbox-close{top:12px;right:12px}
}
@media(min-width:1600px){
    .shell{width:min(1320px,calc(100% - 80px))}
    .grid{gap:26px}
    .body{padding:23px}
}
</style>
<main class="shell">
    <section class="hero"><span class="eyebrow">Leadership &amp; Management</span><h1>Management Team</h1><p>Meet the people responsible for guiding {{ $name }}.</p></section>
    <section class="grid">@forelse($members as $member)<article class="card"><div class="photo" @if($member->image_path) data-image="{{ asset('storage/'.$member->image_path) }}" data-name="{{ $member->title }}" role="button" tabindex="0" @endif>@if($member->image_path)<img src="{{ asset('storage/'.$member->image_path) }}" alt="{{ $member->title }}" loading="lazy">@else<i class="fa-solid fa-user-tie"></i>@endif</div><div class="body"><h2>{{ $member->title }}</h2><div class="role">{{ $member->designation ?: $member->excerpt }}</div>@if($member->content)<div class="bio bio-preview" id="bio-preview-{{ $member->id }}">{!! nl2br(e($member->content)) !!}</div><div class="bio-full" id="bio-full-{{ $member->id }}">{!! nl2br(e($member->content)) !!}</div><button class="bio-more" type="button" data-more="{{ $member->id }}"><span>More</span><i class="fa-solid fa-chevron-down"></i></button>@endif<div class="contact-list">@if($member->phone)<a class="contact" href="tel:{{ preg_replace('/[^0-9+]/','',$member->phone) }}"><i class="fa-solid fa-phone"></i>{{ $member->phone }}</a>@endif @if($member->email)<a class="contact" href="mailto:{{ $member->email }}"><i class="fa-solid fa-envelope"></i>{{ $member->email }}</a>@endif</div><div class="actions"><a class="action primary" href="{{ route('management.vcard',$member) }}"><i class="fa-solid fa-user-plus"></i> Add to Contacts</a>@if($member->visiting_card_path)<a class="action" href="{{ asset('storage/'.$member->visiting_card_path) }}" target="_blank"><i class="fa-regular fa-address-card"></i> Visiting Card</a>@else<a class="action" href="tel:{{ preg_replace('/[^0-9+]/','',$member->phone) }}"><i class="fa-solid fa-phone"></i> Call</a>@endif</div></div></article>@empty<div class="empty"><i class="fa-solid fa-people-group"></i></div>@endforelse</section>
</main>
<div class="lightbox" id="photoLightbox"><button class="lightbox-close" id="lightboxClose" type="button"><i class="fa-solid fa-xmark"></i></button><div class="lightbox-content"><img id="lightboxImage" src="" alt=""></div><div class="zoom-controls"><button id="zoomOut" type="button"><i class="fa-solid fa-minus"></i></button><span class="zoom-level" id="zoomLevel">100%</span><button id="zoomIn" type="button"><i class="fa-solid fa-plus"></i></button><button id="zoomReset" type="button"><i class="fa-solid fa-rotate-left"></i></button></div></div>
<script>(function(){const buttons=[...document.querySelectorAll('.bio-more')];function closeOther(except){buttons.forEach(btn=>{if(btn===except)return;const id=btn.dataset.more,full=document.getElementById('bio-full-'+id),preview=document.getElementById('bio-preview-'+id);if(full&&full.classList.contains('open')){full.classList.remove('open');preview.style.display='';btn.querySelector('span').textContent='More';btn.querySelector('i').className='fa-solid fa-chevron-down'}})}buttons.forEach(btn=>btn.addEventListener('click',()=>{const id=btn.dataset.more,full=document.getElementById('bio-full-'+id),preview=document.getElementById('bio-preview-'+id),open=!full.classList.contains('open');if(open)closeOther(btn);full.classList.toggle('open',open);preview.style.display=open?'none':'';btn.querySelector('span').textContent=open?'Less':'More';btn.querySelector('i').className=open?'fa-solid fa-chevron-up':'fa-solid fa-chevron-down'}));const box=document.getElementById('photoLightbox'),img=document.getElementById('lightboxImage'),close=document.getElementById('lightboxClose'),level=document.getElementById('zoomLevel');let zoom=1;function render(){zoom=Math.min(4,Math.max(1,zoom));img.style.transform='scale('+zoom+')';level.textContent=Math.round(zoom*100)+'%'}function open(p){img.src=p.dataset.image;img.alt=p.dataset.name||'';zoom=1;render();box.classList.add('open');document.body.style.overflow='hidden'}function shut(){box.classList.remove('open');img.src='';document.body.style.overflow=''}document.querySelectorAll('.photo[data-image]').forEach(p=>{p.onclick=()=>open(p);p.onkeydown=e=>{if(e.key==='Enter'||e.key===' '){e.preventDefault();open(p)}}});document.getElementById('zoomIn').onclick=()=>{zoom+=.25;render()};document.getElementById('zoomOut').onclick=()=>{zoom-=.25;render()};document.getElementById('zoomReset').onclick=()=>{zoom=1;render()};close.onclick=shut;box.onclick=e=>{if(e.target===box)shut()};document.addEventListener('keydown',e=>{if(e.key==='Escape')shut()})})();</script>
@endsection
