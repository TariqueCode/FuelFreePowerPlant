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
    grid-template-columns:repeat(4,minmax(0,1fr));
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
/* Desktop presentation only. Everything at 850px and below keeps the existing mobile/tablet design. */
@media(min-width:851px){
    main{padding-top:68px}
    .hero{
        max-width:none;
        margin-bottom:42px;
        padding:0 4px;
    }
    .hero h1{
        max-width:850px;
        font-size:clamp(44px,4.5vw,62px);
    }
    .hero p{
        max-width:760px;
        font-size:14px;
    }
    .grid{
        gap:22px;
        align-items:stretch;
    }
    .card{
        height:100%;
        display:flex;
        flex-direction:column;
        border-radius:22px;
    }
    .photo{
        aspect-ratio:4/4.45;
    }
    .body{
        flex:1;
        display:flex;
        flex-direction:column;
        padding:20px 20px 21px;
    }
    .bio{
        font-size:11px;
        line-height:1.8;
    }
    .bio-preview{
        -webkit-line-clamp:6;
        max-height:119px;
    }
    .bio-full.open{
        display:block;
        padding:14px 15px;
        margin:15px 0 10px;
        max-height:none;
        border:1px solid rgba(67,209,240,.12);
        border-radius:12px;
        background:rgba(2,14,21,.48);
        box-shadow:inset 0 1px 0 rgba(255,255,255,.018);
        color:#9bb5be;
    }
    .bio-more{
        align-self:flex-start;
        margin-bottom:16px;
        padding:8px 12px;
    }
    .contact-list{
        margin-top:auto;
        padding:15px 0;
    }
    .contact{
        font-size:10px;
    }
    .actions{
        margin-top:14px;
    }
    .action{
        min-height:40px;
    }
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


/* Desktop-only management profile experience.
   Mobile/tablet card layout and interactions are intentionally left untouched. */
@media(min-width:851px){
    .bio-modal{
        position:fixed;inset:0;z-index:120;display:none;align-items:center;justify-content:center;
        padding:30px;background:rgba(0,5,9,.78);backdrop-filter:blur(18px) saturate(120%);
    }
    .bio-modal.open{display:flex}
    .bio-modal-panel{
        position:relative;width:min(1080px,94vw);height:min(760px,86vh);min-height:560px;
        display:grid;grid-template-columns:minmax(340px,42%) minmax(0,1fr);overflow:hidden;
        border:1px solid rgba(67,209,240,.24);border-radius:28px;
        background:radial-gradient(500px 320px at 0 0,rgba(67,209,240,.10),transparent 70%),linear-gradient(145deg,#092936 0%,#041923 58%,#021119 100%);
        box-shadow:0 40px 120px rgba(0,0,0,.68),0 0 0 1px rgba(255,255,255,.025) inset,0 0 70px rgba(23,137,164,.08);
        animation:profileIn .22s ease-out;
    }
    @keyframes profileIn{from{opacity:0;transform:translateY(12px) scale(.985)}to{opacity:1;transform:none}}

    /* Full portrait visibility: no cropping inside the desktop profile modal. */
    .bio-modal-photo{
        position:relative;min-width:0;min-height:0;height:100%;display:flex;align-items:center;justify-content:center;
        overflow:hidden;padding:24px;
        background:radial-gradient(500px 500px at 50% 35%,rgba(67,209,240,.08),transparent 72%),#e9eef0;
        border-right:1px solid rgba(91,214,239,.12);
    }
    .bio-modal-photo:before{content:"";position:absolute;inset:18px;border:1px solid rgba(67,209,240,.13);border-radius:20px;pointer-events:none}
    .bio-modal-photo:after{content:"";position:absolute;inset:24px;border-radius:17px;background:linear-gradient(180deg,transparent 68%,rgba(0,10,15,.10));pointer-events:none}
    .bio-modal-photo img{
        position:relative;z-index:1;display:block;width:auto;height:auto;max-width:100%;max-height:100%;
        object-fit:contain;object-position:center;border-radius:15px;
    }
    .bio-modal-photo-fallback{position:relative;z-index:1;width:100%;height:100%;display:grid;place-items:center;color:#48cde9;font-size:68px}

    .bio-modal-info{min-width:0;min-height:0;height:100%;display:flex;flex-direction:column;overflow:hidden}
    .bio-modal-head{flex:0 0 auto;display:flex;align-items:flex-start;justify-content:space-between;gap:18px;padding:32px 34px 22px}
    .bio-modal-kicker{margin:0 0 8px;color:var(--cyan);font-size:9px;font-weight:800;letter-spacing:.18em;text-transform:uppercase}
    .bio-modal-title{margin:0;color:#effbfc;font-size:30px;line-height:1.18;letter-spacing:-.035em;font-weight:800;overflow-wrap:anywhere}
    .bio-modal-role{margin:8px 0 0;color:var(--cyan-soft);font-size:11px;line-height:1.55;font-weight:800}
    .bio-modal-close{
        flex:0 0 40px;width:40px;height:40px;display:grid;place-items:center;border:1px solid rgba(91,214,239,.16);
        border-radius:11px;background:rgba(67,209,240,.05);color:#b9d8df;cursor:pointer;
    }
    .bio-modal-close:hover{background:rgba(67,209,240,.12);color:#fff;border-color:rgba(91,214,239,.30)}
    .bio-modal-divider{flex:0 0 auto;height:1px;margin:0 34px;background:rgba(91,214,239,.11)}

    /* Only the biography scrolls; contact details and actions stay visible. */
    .bio-modal-scroll{
        min-height:0;flex:1 1 auto;overflow-y:auto;overflow-x:hidden;padding:24px 34px 18px;
        scrollbar-width:thin;scrollbar-color:rgba(67,209,240,.28) transparent;
    }
    .bio-modal-section-title{margin:0 0 11px;color:#dff6f9;font-size:10px;font-weight:800;letter-spacing:.12em;text-transform:uppercase}
    .bio-modal-body{color:#a5bdc5;font-size:13px;line-height:1.9;overflow-wrap:anywhere;white-space:normal}
    .bio-modal-body p{margin:0 0 14px}.bio-modal-body p:last-child{margin-bottom:0}

    .bio-modal-footer{
        flex:0 0 auto;padding:16px 34px 24px;border-top:1px solid rgba(91,214,239,.10);
        background:linear-gradient(180deg,rgba(2,14,21,.15),rgba(2,14,21,.48));
    }
    .bio-modal-footer .bio-modal-section-title{margin-bottom:9px}
    .bio-modal-contacts{display:grid;grid-template-columns:repeat(2,minmax(0,1fr));gap:8px}
    .bio-modal-contact{
        min-width:0;display:flex;align-items:center;gap:9px;padding:9px 10px;border:1px solid rgba(91,214,239,.09);
        border-radius:10px;background:rgba(2,14,21,.34);color:#a8c1c9;text-decoration:none;font-size:10px;line-height:1.35;overflow-wrap:anywhere;
    }
    .bio-modal-contact:hover{border-color:rgba(91,214,239,.20);color:#fff;background:rgba(67,209,240,.05)}
    .bio-modal-contact i{width:27px;height:27px;flex:0 0 27px;display:grid;place-items:center;border-radius:8px;background:rgba(67,209,240,.07);color:#58cee8}
    .bio-modal-actions{display:grid;grid-template-columns:repeat(2,minmax(0,1fr));gap:8px;margin-top:9px}
    .bio-modal-action{
        min-width:0;min-height:40px;display:flex;align-items:center;justify-content:center;gap:7px;padding:9px 10px;
        border-radius:10px;border:1px solid rgba(91,214,239,.13);color:#c2dde3;text-decoration:none;font-size:10px;font-weight:800;background:rgba(67,209,240,.04);
    }
    .bio-modal-action:hover{background:rgba(67,209,240,.09);color:#fff}
    .bio-modal-action.primary{background:linear-gradient(135deg,#2db1cf,#17758d);border-color:rgba(86,220,243,.25);color:#fff}
}

/* Desktop modal tuning for smaller laptops. */
@media(min-width:851px) and (max-width:1180px) and (hover:hover) and (pointer:fine){
    .bio-modal{padding:18px}
    .bio-modal-panel{width:min(1000px,96vw);height:min(700px,90vh);min-height:500px;grid-template-columns:minmax(300px,40%) minmax(0,1fr);border-radius:22px}
    .bio-modal-photo{padding:18px}.bio-modal-photo:before{inset:13px;border-radius:16px}.bio-modal-photo:after{inset:18px;border-radius:13px}
    .bio-modal-head{padding:25px 27px 18px}.bio-modal-divider{margin:0 27px}.bio-modal-scroll{padding:20px 27px 14px}.bio-modal-footer{padding:13px 27px 18px}
    .bio-modal-title{font-size:26px}.bio-modal-body{font-size:12px;line-height:1.8}
}
/* Desktop: use a consistent executive-card layout. Biography never changes card height. */
.desktop-profile-button{display:none}
@media(min-width:851px){
    .desktop-profile-button{
        display:inline-flex;
        align-items:center;
        justify-content:center;
        gap:7px;
        align-self:flex-start;
        min-height:36px;
        margin:13px 0 15px;
        padding:8px 13px;
        border:1px solid rgba(67,209,240,.15);
        border-radius:10px;
        background:rgba(67,209,240,.045);
        color:#8bddec;
        font-size:10px;
        font-weight:800;
        cursor:pointer;
        transition:.2s ease;
    }
    .desktop-profile-button:hover{
        background:rgba(67,209,240,.10);
        border-color:rgba(67,209,240,.30);
        color:#fff;
    }
    .body .bio-preview,
    .body .bio-more{display:none}
    .body .bio-full{display:none!important}
    .card{height:auto;align-self:stretch}
    .body{min-height:255px}
    .contact-list{margin-top:auto}
}
</style>
<main class="shell">
    <section class="hero"><span class="eyebrow">Leadership &amp; Management</span><h1>Board of Directors</h1><p>Meet the people responsible for guiding {{ $name }}.</p></section>
    <section class="grid">@forelse($members as $member)<article class="card"><div class="photo" @if($member->image_path) data-image="{{ asset('storage/'.$member->image_path) }}" data-name="{{ $member->title }}" role="button" tabindex="0" @endif>@if($member->image_path)<img src="{{ asset('storage/'.$member->image_path) }}" alt="{{ $member->title }}" loading="lazy">@else<i class="fa-solid fa-user-tie"></i>@endif</div><div class="body"><h2>{{ $member->title }}</h2><div class="role">{{ $member->designation ?: $member->excerpt }}</div><button class="desktop-profile-button" type="button" data-profile="{{ $member->id }}"><i class="fa-regular fa-id-card"></i> View Profile</button>@if($member->content)<div class="bio bio-preview" id="bio-preview-{{ $member->id }}">{!! nl2br(e($member->content)) !!}</div><div class="bio-full" id="bio-full-{{ $member->id }}">{!! nl2br(e($member->content)) !!}</div><button class="bio-more" type="button" data-more="{{ $member->id }}"><span>More</span><i class="fa-solid fa-chevron-down"></i></button>@endif<div class="contact-list">@if($member->phone)<a class="contact" href="tel:{{ preg_replace('/[^0-9+]/','',$member->phone) }}"><i class="fa-solid fa-phone"></i>{{ $member->phone }}</a>@endif @if($member->email)<a class="contact" href="mailto:{{ $member->email }}"><i class="fa-solid fa-envelope"></i>{{ $member->email }}</a>@endif</div><div class="actions"><a class="action primary" href="{{ route('management.vcard',$member) }}"><i class="fa-solid fa-user-plus"></i> Add to Contacts</a>@if($member->visiting_card_path)<a class="action" href="{{ asset('storage/'.$member->visiting_card_path) }}" target="_blank"><i class="fa-regular fa-address-card"></i> Visiting Card</a>@else<a class="action" href="tel:{{ preg_replace('/[^0-9+]/','',$member->phone) }}"><i class="fa-solid fa-phone"></i> Call</a>@endif</div></div></article>@empty<div class="empty"><i class="fa-solid fa-people-group"></i></div>@endforelse</section>
</main>

<div class="bio-modal" id="bioModal" aria-hidden="true">
    <div class="bio-modal-panel" role="dialog" aria-modal="true" aria-labelledby="bioModalTitle">
        <div class="bio-modal-photo" id="bioModalPhoto"></div>
        <div class="bio-modal-info">
            <div class="bio-modal-head">
                <div>
                    <div class="bio-modal-kicker">Management Profile</div>
                    <h2 class="bio-modal-title" id="bioModalTitle"></h2>
                    <div class="bio-modal-role" id="bioModalRole"></div>
                </div>
                <button class="bio-modal-close" id="bioModalClose" type="button" aria-label="Close profile"><i class="fa-solid fa-xmark"></i></button>
            </div>
            <div class="bio-modal-divider"></div>
            <div class="bio-modal-scroll">
                <div class="bio-modal-section-title">Profile</div>
                <div class="bio-modal-body" id="bioModalBody"></div>
            </div>
            <div class="bio-modal-footer">
                <div class="bio-modal-section-title">Contact &amp; Actions</div>
                <div class="bio-modal-contacts" id="bioModalContacts"></div>
                <div class="bio-modal-actions" id="bioModalActions"></div>
            </div>
        </div>
    </div>
</div>
<div class="lightbox" id="photoLightbox"><button class="lightbox-close" id="lightboxClose" type="button"><i class="fa-solid fa-xmark"></i></button><div class="lightbox-content"><img id="lightboxImage" src="" alt=""></div><div class="zoom-controls"><button id="zoomOut" type="button"><i class="fa-solid fa-minus"></i></button><span class="zoom-level" id="zoomLevel">100%</span><button id="zoomIn" type="button"><i class="fa-solid fa-plus"></i></button><button id="zoomReset" type="button"><i class="fa-solid fa-rotate-left"></i></button></div></div>
<script>(function(){const buttons=[...document.querySelectorAll('.bio-more')];function closeOther(except){buttons.forEach(btn=>{if(btn===except)return;const id=btn.dataset.more,full=document.getElementById('bio-full-'+id),preview=document.getElementById('bio-preview-'+id);if(full&&full.classList.contains('open')){full.classList.remove('open');preview.style.display='';btn.querySelector('span').textContent='More';btn.querySelector('i').className='fa-solid fa-chevron-down'}})}const bioModal=document.getElementById('bioModal'),bioModalTitle=document.getElementById('bioModalTitle'),bioModalRole=document.getElementById('bioModalRole'),bioModalBody=document.getElementById('bioModalBody'),bioModalPhoto=document.getElementById('bioModalPhoto'),bioModalContacts=document.getElementById('bioModalContacts'),bioModalActions=document.getElementById('bioModalActions'),bioModalClose=document.getElementById('bioModalClose');const desktopProfiles=[...document.querySelectorAll('.desktop-profile-button')];function shutBioModal(){if(!bioModal)return;bioModal.classList.remove('open');bioModal.setAttribute('aria-hidden','true');document.body.style.overflow=''}function openBioModal(btn){const card=btn.closest('.card'),body=card.querySelector('.body'),title=body.querySelector('h2'),role=body.querySelector('.role'),full=body.querySelector('.bio-full'),photo=card.querySelector('.photo img'),contacts=[...body.querySelectorAll('.contact')],actions=[...body.querySelectorAll('.actions .action')];bioModalTitle.textContent=title?title.textContent:'';bioModalRole.textContent=role?role.textContent:'';bioModalBody.innerHTML=full?full.innerHTML:'<p>No additional profile information is available.</p>';bioModalPhoto.innerHTML=photo?'<img src="'+photo.src.replace(/"/g,'&quot;')+'" alt="'+(title?title.textContent.replace(/"/g,'&quot;'):'')+'">':'<div class="bio-modal-photo-fallback"><i class="fa-solid fa-user-tie"></i></div>';bioModalContacts.innerHTML=contacts.map(a=>'<a class="bio-modal-contact" href="'+a.getAttribute('href')+'">'+a.innerHTML+'</a>').join('');bioModalActions.innerHTML=actions.map(a=>'<a class="bio-modal-action '+(a.classList.contains('primary')?'primary':'')+'" href="'+a.getAttribute('href')+'" '+(a.target?'target="'+a.target+'"':'')+'>'+a.innerHTML+'</a>').join('');bioModal.classList.add('open');bioModal.setAttribute('aria-hidden','false');document.body.style.overflow='hidden';bioModalClose.focus()}desktopProfiles.forEach(btn=>btn.addEventListener('click',()=>openBioModal(btn)));buttons.forEach(btn=>btn.addEventListener('click',()=>{if(window.matchMedia('(min-width:851px)').matches){openBioModal(btn);return}const id=btn.dataset.more,full=document.getElementById('bio-full-'+id),preview=document.getElementById('bio-preview-'+id),open=!full.classList.contains('open');if(open)closeOther(btn);full.classList.toggle('open',open);preview.style.display=open?'none':'';btn.querySelector('span').textContent=open?'Less':'More';btn.querySelector('i').className=open?'fa-solid fa-chevron-up':'fa-solid fa-chevron-down'}));if(bioModal){bioModalClose.addEventListener('click',shutBioModal);bioModal.addEventListener('click',e=>{if(e.target===bioModal)shutBioModal})}const box=document.getElementById('photoLightbox'),img=document.getElementById('lightboxImage'),close=document.getElementById('lightboxClose'),level=document.getElementById('zoomLevel');let zoom=1;function render(){zoom=Math.min(4,Math.max(1,zoom));img.style.transform='scale('+zoom+')';level.textContent=Math.round(zoom*100)+'%'}function open(p){img.src=p.dataset.image;img.alt=p.dataset.name||'';zoom=1;render();box.classList.add('open');document.body.style.overflow='hidden'}function shut(){box.classList.remove('open');img.src='';document.body.style.overflow=''}document.querySelectorAll('.photo[data-image]').forEach(p=>{p.onclick=()=>{if(window.matchMedia('(min-width:851px)').matches){const profile=p.closest('.card')?.querySelector('.desktop-profile-button');if(profile){openBioModal(profile);return}}open(p)};p.onkeydown=e=>{if(e.key==='Enter'||e.key===' '){e.preventDefault();if(window.matchMedia('(min-width:851px)').matches){const profile=p.closest('.card')?.querySelector('.desktop-profile-button');if(profile){openBioModal(profile);return}}open(p)}}});document.getElementById('zoomIn').onclick=()=>{zoom+=.25;render()};document.getElementById('zoomOut').onclick=()=>{zoom-=.25;render()};document.getElementById('zoomReset').onclick=()=>{zoom=1;render()};close.onclick=shut;box.onclick=e=>{if(e.target===box)shut()};document.addEventListener('keydown',e=>{if(e.key==='Escape'){shut();shutBioModal()}})})();</script>


<!-- Final desktop-only executive profile refinement.
     Mobile/tablet (850px and below) intentionally untouched. -->
<style>
@media (min-width:851px){
    /* Keep the team grid clean and uniform on desktop. */
    .grid{
        align-items:stretch;
    }
    .card{
        height:100%;
    }
    .card .body{
        min-height:250px;
    }
    .desktop-profile-button{
        margin-top:15px;
    }

    /* Executive profile modal */
    .bio-modal{
        padding:24px;
        background:
            radial-gradient(900px 600px at 50% 45%,rgba(28,139,170,.10),transparent 65%),
            rgba(0,5,9,.84);
        backdrop-filter:blur(22px) saturate(125%);
    }
    .bio-modal-panel{
        width:min(1180px,94vw);
        height:min(790px,88vh);
        min-height:560px;
        grid-template-columns:minmax(380px,43%) minmax(0,57%);
        border-radius:26px;
        border:1px solid rgba(91,214,239,.24);
        background:
            radial-gradient(650px 500px at 0 0,rgba(67,209,240,.105),transparent 68%),
            linear-gradient(145deg,#082633 0%,#041923 52%,#021119 100%);
        box-shadow:
            0 45px 130px rgba(0,0,0,.72),
            0 0 0 1px rgba(255,255,255,.025) inset,
            0 0 90px rgba(23,137,164,.09);
    }

    /* Full portrait area — never crop the original management photo. */
    .bio-modal-photo{
        padding:28px;
        background:
            radial-gradient(520px 520px at 50% 38%,rgba(67,209,240,.09),transparent 70%),
            #e9eef0;
        border-right:1px solid rgba(91,214,239,.13);
    }
    .bio-modal-photo:before{
        inset:20px;
        border-radius:19px;
        border-color:rgba(67,209,240,.16);
    }
    .bio-modal-photo:after{
        inset:28px;
        border-radius:16px;
        background:linear-gradient(180deg,transparent 72%,rgba(0,10,15,.10));
    }
    .bio-modal-photo img{
        width:auto;
        height:auto;
        max-width:100%;
        max-height:100%;
        object-fit:contain;
        border-radius:14px;
    }

    .bio-modal-info{
        background:linear-gradient(180deg,rgba(255,255,255,.008),transparent 35%);
    }
    .bio-modal-head{
        padding:30px 32px 22px;
    }
    .bio-modal-kicker{
        margin-bottom:7px;
        font-size:9px;
        letter-spacing:.20em;
    }
    .bio-modal-title{
        font-size:31px;
        line-height:1.15;
    }
    .bio-modal-role{
        margin-top:9px;
        font-size:11px;
    }
    .bio-modal-close{
        width:42px;
        height:42px;
        flex-basis:42px;
        border-radius:12px;
    }
    .bio-modal-divider{
        margin:0 32px;
    }

    /* Only this region scrolls. Header/footer remain visible at all times. */
    .bio-modal-scroll{
        padding:25px 32px 20px;
        overscroll-behavior:contain;
        scrollbar-width:thin;
    }
    .bio-modal-scroll::-webkit-scrollbar{
        width:6px;
    }
    .bio-modal-scroll::-webkit-scrollbar-thumb{
        background:rgba(67,209,240,.24);
        border-radius:99px;
    }
    .bio-modal-section-title{
        margin-bottom:12px;
        color:#72dced;
        font-size:9px;
        letter-spacing:.18em;
    }
    .bio-modal-body{
        max-width:760px;
        color:#a9c1c9;
        font-size:13px;
        line-height:1.95;
    }
    .bio-modal-body p{
        margin:0 0 16px;
    }
    .bio-modal-body p:last-child{
        margin-bottom:0;
    }

    /* Fixed contact/action area — no information disappears when the bio is long. */
    .bio-modal-footer{
        padding:17px 32px 24px;
        background:
            linear-gradient(180deg,rgba(2,14,21,.18),rgba(2,14,21,.62));
        box-shadow:0 -12px 30px rgba(0,0,0,.10);
    }
    .bio-modal-footer .bio-modal-section-title{
        margin-bottom:10px;
    }
    .bio-modal-contacts{
        grid-template-columns:repeat(2,minmax(0,1fr));
        gap:9px;
    }
    .bio-modal-contact{
        min-height:44px;
        padding:8px 10px;
        font-size:10px;
        background:rgba(1,12,18,.40);
    }
    .bio-modal-actions{
        grid-template-columns:repeat(2,minmax(0,1fr));
        gap:9px;
        margin-top:10px;
    }
    .bio-modal-action{
        min-height:42px;
        font-size:10px;
        border-radius:11px;
    }
}

@media (min-width:851px) and (max-width:1100px) and (hover:hover) and (pointer:fine){
    .bio-modal-panel{
        width:min(1000px,96vw);
        height:min(720px,90vh);
        min-height:520px;
        grid-template-columns:minmax(315px,40%) minmax(0,60%);
    }
    .bio-modal-photo{
        padding:20px;
    }
    .bio-modal-photo:before{inset:14px}
    .bio-modal-photo:after{inset:20px}
    .bio-modal-head{
        padding:23px 25px 18px;
    }
    .bio-modal-title{
        font-size:26px;
    }
    .bio-modal-divider{
        margin:0 25px;
    }
    .bio-modal-scroll{
        padding:19px 25px 15px;
    }
    .bio-modal-body{
        font-size:12px;
        line-height:1.85;
    }
    .bio-modal-footer{
        padding:13px 25px 18px;
    }
}
/* Desktop modal visual polish — mobile/tablet remains unchanged. */
@media (min-width:851px){
    .bio-modal-photo{
        background:
            radial-gradient(520px 520px at 50% 38%,rgba(67,209,240,.12),transparent 70%),
            linear-gradient(145deg,#061923 0%,#04131d 58%,#020d14 100%);
        border-right:1px solid rgba(67,209,240,.16);
    }
    .bio-modal-photo:before{
        inset:20px;
        border:1px solid rgba(67,209,240,.12);
        border-radius:19px;
        box-shadow:inset 0 0 30px rgba(67,209,240,.025);
    }
    .bio-modal-photo:after{
        inset:28px;
        border-radius:16px;
        background:linear-gradient(180deg,transparent 72%,rgba(0,10,15,.16));
    }
    .bio-modal-photo img{
        border:1px solid rgba(67,209,240,.58);
        box-shadow:
            0 0 0 3px rgba(67,209,240,.045),
            0 18px 45px rgba(0,0,0,.35),
            0 0 24px rgba(67,209,240,.08);
    }
    .bio-modal-photo-fallback{
        color:#48cde9;
    }
}
@media (min-width:851px) and (max-width:1100px){
    .bio-modal-photo{
        background:
            radial-gradient(420px 420px at 50% 38%,rgba(67,209,240,.11),transparent 70%),
            linear-gradient(145deg,#061923 0%,#04131d 58%,#020d14 100%);
    }
    .bio-modal-photo img{
        border-color:rgba(67,209,240,.58);
    }
}
</style>

@endsection
