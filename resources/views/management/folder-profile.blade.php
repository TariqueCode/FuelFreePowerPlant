@extends('layouts.public')

@php
    $brand = \App\Models\SystemSetting::query()->whereIn('key',['company.name','company.logo_path','company.tagline'])->pluck('value','key');
    $name = $brand->get('company.name') ?: config('fuelfree.company.name');
@endphp

@section('title', $folder->name.' — '.$name)

@section('content')
<style>
.management-page{
    --line:rgba(53,216,106,.16);
    --cyan:#35d86a;
    --muted:#91a99d;
    width:min(1240px,calc(100% - 48px));
    margin:auto;
    padding:68px 0 88px;
    color:#f4fff8;
}
.management-hero{margin-bottom:38px}
.management-eyebrow{
    display:inline-flex;
    align-items:center;
    gap:9px;
    font-size:10px;
    letter-spacing:.22em;
    color:#35d86a;
    font-weight:800;
    text-transform:uppercase;
}
.management-eyebrow:before{
    content:"";
    width:28px;
    height:1px;
    background:linear-gradient(90deg,#35d86a,#168dff);
}
.management-hero h1{
    font-size:clamp(38px,5vw,64px);
    line-height:1.05;
    letter-spacing:-.045em;
    margin:13px 0 12px;
    font-weight:800;
}
.management-hero p{
    margin:0;
    color:#91a99d;
    font-size:14px;
    line-height:1.75;
    max-width:720px;
}
.management-grid{
    width:min(1040px,100%);
    margin:0 auto;
    display:grid;
    grid-template-columns:repeat(2,minmax(0,1fr));
    gap:24px;
    align-items:stretch;
}
.management-card{
    min-width:0;
    min-height:320px;
    display:flex;
    flex-direction:row;
    overflow:hidden;
    border:1px solid rgba(53,216,106,.14);
    border-radius:22px;
    background:linear-gradient(145deg,rgba(8,26,18,.92),rgba(3,13,9,.98));
    box-shadow:0 18px 55px rgba(0,0,0,.28);
}
.management-card:last-child:nth-child(odd){
    grid-column:1/-1;
    width:calc((100% - 24px)/2);
    justify-self:center;
}
.management-photo{
    width:44%;
    flex:0 0 44%;
    min-height:320px;
    background:#06120d;
    overflow:hidden;
    display:grid;
    place-items:center;
}
.management-photo img{
    width:100%;
    height:100%;
    object-fit:cover;
    display:block;
}
.management-photo i{font-size:44px;color:#35d86a}
.management-body{
    width:56%;
    flex:1;
    min-width:0;
    display:flex;
    flex-direction:column;
    padding:24px;
}
.management-body h2{
    margin:0;
    color:#f4fff8;
    font-size:22px;
    line-height:1.22;
    letter-spacing:-.02em;
}
.management-role{
    margin:7px 0 0;
    color:#72e99a;
    font-size:10px;
    font-weight:800;
    letter-spacing:.06em;
    text-transform:uppercase;
}
.management-bio{
    margin:15px 0;
    color:#91a99d;
    font-size:11px;
    line-height:1.75;
    display:-webkit-box;
    -webkit-line-clamp:5;
    -webkit-box-orient:vertical;
    overflow:hidden;
}
.management-contacts{
    margin-top:auto;
    display:grid;
    gap:8px;
    padding:14px 0;
    border-top:1px solid rgba(53,216,106,.09);
}
.management-contact{
    display:flex;
    gap:8px;
    align-items:center;
    color:#a8bbb1;
    text-decoration:none;
    font-size:10px;
    min-width:0;
    overflow-wrap:anywhere;
}
.management-contact:hover{color:#effff5}
.management-contact i{
    width:25px;
    height:25px;
    flex:0 0 25px;
    display:grid;
    place-items:center;
    border-radius:7px;
    background:rgba(53,216,106,.06);
    color:#35d86a;
}
.management-actions{
    display:grid;
    grid-template-columns:1fr 1fr;
    gap:8px;
    margin-top:12px;
}
.management-action{
    display:flex;
    align-items:center;
    justify-content:center;
    gap:7px;
    padding:10px 7px;
    border:1px solid rgba(53,216,106,.16);
    border-radius:10px;
    color:#b9d8df;
    text-decoration:none;
    font-size:10px;
    font-weight:800;
    cursor:pointer;
    transition:transform .2s ease,border-color .2s ease,background .2s ease,box-shadow .2s ease;
}
.management-action:hover{
    transform:translateY(-1px);
    border-color:rgba(53,216,106,.34);
}
.management-action.primary{
    background:linear-gradient(135deg,#35d86a,#8ee9a9);
    color:#03100a;
    border-color:rgba(142,233,169,.72);
}
.management-action.primary:hover{box-shadow:0 10px 24px rgba(53,216,106,.16)}

.management-empty{
    border:1px dashed var(--line);
    border-radius:18px;
    padding:55px 20px;
    text-align:center;
    color:var(--muted);
    grid-column:1/-1;
}
.management-empty i{font-size:38px;color:#35d86a;margin-bottom:12px}
.management-empty h2{margin:0 0 6px;color:#e5fff0}
.management-empty p{margin:0;font-size:11px}

.management-modal{
    position:fixed;
    inset:0;
    z-index:9999;
    display:none;
    align-items:center;
    justify-content:center;
    padding:22px;
    background:rgba(1,10,7,.78);
    backdrop-filter:blur(10px);
}
.management-modal.is-open{display:flex}
.management-modal-card{
    position:relative;
    width:min(920px,100%);
    max-height:min(88vh,860px);
    overflow:auto;
    border:1px solid rgba(53,216,106,.20);
    border-radius:24px;
    background:linear-gradient(145deg,#092b1c,#03150e 70%);
    box-shadow:0 30px 100px rgba(0,0,0,.5);
}
.management-modal-close{
    position:absolute;
    right:14px;
    top:14px;
    width:38px;
    height:38px;
    border:1px solid rgba(53,216,106,.18);
    border-radius:50%;
    background:rgba(2,17,10,.78);
    color:#dff8e7;
    display:grid;
    place-items:center;
    cursor:pointer;
    font-size:15px;
    z-index:2;
}
.management-modal-head{
    display:grid;
    grid-template-columns:210px 1fr;
    gap:28px;
    padding:28px 28px 22px;
    border-bottom:1px solid rgba(53,216,106,.10);
}
.management-modal-photo{
    width:210px;
    aspect-ratio:4/4.6;
    border-radius:18px;
    overflow:hidden;
    background:#eef2f3;
    display:grid;
    place-items:center;
}
.management-modal-photo img{width:100%;height:100%;object-fit:cover}
.management-modal-photo i{font-size:46px;color:#35d86a}
.management-modal-title{align-self:center;padding-right:34px}
.management-modal-title h2{
    margin:0;
    color:#f4fff8;
    font-size:clamp(24px,3vw,36px);
    line-height:1.15;
    letter-spacing:-.025em;
}
.management-modal-title .management-role{font-size:12px;margin-top:9px}
.management-modal-title .management-contact{margin-top:12px}
.management-modal-content{
    padding:24px 28px 30px;
    color:#b9cdd2;
    font-size:13px;
    line-height:1.9;
    white-space:pre-line;
}

/* Desktop-only card design. Mobile/tablet below remain on the base layout. */
@media (min-width:992px){
    .management-page{
        width:min(1420px,calc(100% - 64px));
        padding:72px 0 96px;
    }
    .management-hero{
        margin-bottom:34px;
        display:grid;
        grid-template-columns:minmax(0,1fr) auto;
        align-items:end;
        gap:30px;
    }
    .management-hero h1{
        font-size:clamp(46px,4.6vw,68px);
        font-weight:850;
        letter-spacing:-.055em;
        background:linear-gradient(105deg,#f4fff8 0%,#f4fff8 48%,#35d86a 72%,#20c8ff 100%);
        -webkit-background-clip:text;
        background-clip:text;
        color:transparent;
    }
    .management-hero p{color:#a8bdb3;font-size:15px}

    .management-grid{
        width:100%;
        max-width:none;
        gap:24px;
    }
    .management-card{
        position:relative;
        display:grid;
        grid-template-columns:minmax(0,38%) minmax(0,62%);
        min-height:420px;
        border:1px solid transparent;
        border-radius:22px;
        background:
            linear-gradient(145deg,rgba(8,28,18,.98),rgba(3,13,9,.99)) padding-box,
            linear-gradient(115deg,rgba(53,216,106,.78),rgba(53,216,106,.24) 44%,rgba(22,141,255,.72)) border-box;
        box-shadow:0 18px 55px rgba(0,0,0,.32),0 0 28px rgba(53,216,106,.045);
        transition:transform .25s cubic-bezier(.2,.75,.2,1),box-shadow .25s ease;
        isolation:isolate;
    }
    .management-card:hover{
        transform:translateY(-4px);
        box-shadow:0 28px 72px rgba(0,0,0,.42),0 0 34px rgba(53,216,106,.10);
    }
    .management-card::before{
        content:"";
        position:absolute;
        inset:0;
        pointer-events:none;
        z-index:-1;
        border-radius:inherit;
        background:
            radial-gradient(circle at 100% 0%,rgba(22,141,255,.09),transparent 35%),
            radial-gradient(circle at 0% 100%,rgba(53,216,106,.08),transparent 38%);
    }
    .management-card::after{
        content:attr(data-card-number);
        position:absolute;
        top:22px;
        right:22px;
        z-index:1;
        color:rgba(53,216,106,.07);
        font-size:62px;
        line-height:1;
        font-weight:900;
        letter-spacing:-.06em;
        pointer-events:none;
    }
    .management-photo{
        position:relative;
        z-index:1;
        width:100%;
        min-height:420px;
        aspect-ratio:auto;
        background:#06120d;
        overflow:hidden;
    }
    .management-photo::after{
        content:"";
        position:absolute;
        inset:0;
        pointer-events:none;
        background:linear-gradient(90deg,transparent 68%,rgba(3,13,9,.38) 100%);
    }
    .management-photo img{
        width:100%;
        height:100%;
        object-fit:cover;
        transition:transform .55s cubic-bezier(.2,.75,.2,1);
    }
    .management-card:hover .management-photo img{transform:scale(1.025)}

    .management-body{
        position:relative;
        z-index:2;
        width:auto;
        min-width:0;
        padding:38px 34px 30px;
        display:flex;
        flex-direction:column;
        justify-content:center;
    }
    .management-body::before{
        content:"";
        position:absolute;
        left:0;
        top:13%;
        bottom:13%;
        width:1px;
        background:linear-gradient(180deg,transparent,rgba(53,216,106,.45),rgba(22,141,255,.30),transparent);
    }
    .management-body h2{
        max-width:88%;
        margin:0;
        color:transparent;
        background:linear-gradient(105deg,#35d86a 0%,#8cf1a8 40%,#33bfff 100%);
        -webkit-background-clip:text;
        background-clip:text;
        font-size:clamp(25px,2.1vw,34px);
        line-height:1.12;
        font-weight:850;
        letter-spacing:-.035em;
    }
    .management-role{
        margin-top:10px;
        color:#55e58a;
        font-size:11px;
        font-weight:850;
        letter-spacing:.19em;
    }
    .management-bio{
        margin:20px 0 0;
        max-width:560px;
        color:#a5b9ae;
        font-size:13px;
        line-height:1.78;
        display:-webkit-box;
        -webkit-box-orient:vertical;
        -webkit-line-clamp:3;
        overflow:hidden;
    }
    .management-contacts{
        margin-top:20px;
        padding:16px 0;
        border-top:1px solid rgba(53,216,106,.12);
        border-bottom:1px solid rgba(53,216,106,.08);
        display:grid;
        gap:10px;
    }
    .management-contact{
        color:#b7c9c0;
        font-size:12px;
    }
    .management-contact:hover{color:#effff5}
    .management-contact i{
        width:28px;
        height:28px;
        border-radius:8px;
        background:rgba(53,216,106,.06);
        border:1px solid rgba(53,216,106,.13);
        color:#35d86a;
    }
    .management-actions{
        grid-template-columns:minmax(0,1.45fr) minmax(100px,.72fr);
        gap:10px;
        margin-top:20px;
    }
    .management-action{
        min-height:46px;
        padding:10px 14px;
        border:1px solid rgba(53,216,106,.24);
        border-radius:12px;
        background:linear-gradient(135deg,rgba(53,216,106,.075),rgba(22,141,255,.035));
        color:#dff8e7;
        font-size:11px;
    }
    .management-action:hover{
        transform:translateY(-2px);
        border-color:rgba(53,216,106,.50);
        background:linear-gradient(135deg,rgba(53,216,106,.15),rgba(22,141,255,.065));
        box-shadow:0 10px 25px rgba(0,0,0,.24),0 0 18px rgba(53,216,106,.06);
    }
    .management-action.primary{
        background:linear-gradient(135deg,#35d86a,#8ee9a9);
        border-color:rgba(142,233,169,.78);
        color:#03100a;
    }
    .management-action.primary:hover{
        background:linear-gradient(135deg,#55e27f,#b0f3c4);
        box-shadow:0 12px 30px rgba(53,216,106,.16);
    }
}
@media (min-width:992px) and (max-width:1100px){
    .management-page{width:min(1240px,calc(100% - 40px))}
    .management-card{min-height:360px}
    .management-photo{min-height:360px}
    .management-body{padding:28px 24px}
    .management-body h2{font-size:24px}
    .management-actions{grid-template-columns:1fr}
}
@media(max-width:680px){
    .management-page{width:calc(100% - 24px);padding:40px 0 58px}
    .management-hero{margin-bottom:27px}
    .management-hero h1{font-size:clamp(34px,10vw,46px)}
    .management-hero p{font-size:12px}
    .management-grid{grid-template-columns:1fr;gap:16px}
    .management-card,.management-card:last-child:nth-child(odd){
        grid-column:auto;
        width:100%;
        min-height:0;
        display:flex;
        flex-direction:column;
        border-radius:20px;
    }
    .management-photo{width:100%;flex-basis:auto;min-height:0;aspect-ratio:4/4.7}
    .management-body{width:100%;padding:18px}
    .management-body h2{font-size:19px}
    .management-modal{padding:10px}
    .management-modal-card{max-height:94vh;border-radius:20px}
    .management-modal-head{grid-template-columns:1fr;gap:18px;padding:22px 18px 18px}
    .management-modal-photo{width:100%;max-width:250px;margin:auto}
    .management-modal-title{padding:0;text-align:center}
    .management-modal-title .management-contact{justify-content:center}
    .management-modal-content{padding:20px 18px 24px;font-size:12px;line-height:1.8}
    .management-modal-close{right:10px;top:10px}
}
@media(prefers-reduced-motion:reduce){
    .management-card,.management-action,.management-photo img{transition:none!important}
}
</style>

<div class="management-page">
    <header class="management-hero">
        <span class="management-eyebrow">LEADERSHIP</span>
        <h1>{{ $folder->name }}</h1>
        <p>Meet the people responsible for guiding {{ $name }}.</p>
    </header>

    <section class="management-grid" aria-label="{{ $folder->name }} profiles">
        @forelse($members as $member)
            <article class="management-card" data-card-number="{{ str_pad((string) $loop->iteration, 2, '0', STR_PAD_LEFT) }}">
                <div class="management-photo">
                    @if($member->image_path)
                        <img src="{{ asset('storage/'.$member->image_path) }}" alt="{{ $member->title }}">
                    @else
                        <i class="fa-solid fa-user-tie"></i>
                    @endif
                </div>
                <div class="management-body">
                    <h2>{{ $member->title }}</h2>
                    <div class="management-role">{{ $member->designation ?: $member->excerpt }}</div>
                    @if($member->content)<div class="management-bio">{{ strip_tags($member->content) }}</div>@endif
                    <div class="management-contacts">
                        @if($member->phone)<a class="management-contact" href="tel:{{ preg_replace('/\s+/', '', $member->phone) }}"><i class="fa-solid fa-phone"></i><span>{{ $member->phone }}</span></a>@endif
                        @if($member->email)<a class="management-contact" href="mailto:{{ $member->email }}"><i class="fa-solid fa-envelope"></i><span>{{ $member->email }}</span></a>@endif
                    </div>
                    <div class="management-actions">
                        <button class="management-action primary management-profile-trigger" type="button" data-profile-target="profile-{{ $member->id }}"><i class="fa-regular fa-id-card"></i> View Profile</button>
                        <a class="management-action" href="{{ route('management.vcard',$member) }}"><i class="fa-regular fa-address-card"></i> vCard</a>
                    </div>
                </div>
            </article>
        @empty
            <div class="management-empty"><i class="fa-solid fa-people-group"></i><h2>No profiles published yet</h2><p>This folder is ready for profiles from the Profile Builder.</p></div>
        @endforelse
    </section>

    @foreach($members as $member)
        <div class="management-modal" id="profile-{{ $member->id }}" role="dialog" aria-modal="true" aria-labelledby="profile-title-{{ $member->id }}" hidden>
            <div class="management-modal-card">
                <button class="management-modal-close" type="button" aria-label="Close profile"><i class="fa-solid fa-xmark"></i></button>
                <div class="management-modal-head">
                    <div class="management-modal-photo">
                        @if($member->image_path)
                            <img src="{{ asset('storage/'.$member->image_path) }}" alt="{{ $member->title }}">
                        @else
                            <i class="fa-solid fa-user-tie"></i>
                        @endif
                    </div>
                    <div class="management-modal-title">
                        <h2 id="profile-title-{{ $member->id }}">{{ $member->title }}</h2>
                        <div class="management-role">{{ $member->designation ?: $member->excerpt }}</div>
                        @if($member->phone)<a class="management-contact" href="tel:{{ preg_replace('/\s+/', '', $member->phone) }}"><i class="fa-solid fa-phone"></i><span>{{ $member->phone }}</span></a>@endif
                        @if($member->email)<a class="management-contact" href="mailto:{{ $member->email }}"><i class="fa-solid fa-envelope"></i><span>{{ $member->email }}</span></a>@endif
                    </div>
                </div>
                @if($member->content)
                    <div class="management-modal-content">{{ strip_tags($member->content) }}</div>
                @endif
            </div>
        </div>
    @endforeach
</div>

<script>
(() => {
    const modals = document.querySelectorAll('.management-modal');
    const triggers = document.querySelectorAll('.management-profile-trigger');
    let activeModal = null;
    const closeModal = () => {
        if (!activeModal) return;
        activeModal.classList.remove('is-open');
        activeModal.hidden = true;
        document.body.style.overflow = '';
        activeModal = null;
    };
    triggers.forEach((trigger) => trigger.addEventListener('click', () => {
        const modal = document.getElementById(trigger.dataset.profileTarget);
        if (!modal) return;
        closeModal();
        modal.hidden = false;
        modal.classList.add('is-open');
        activeModal = modal;
        document.body.style.overflow = 'hidden';
        modal.querySelector('.management-modal-close')?.focus();
    }));
    modals.forEach((modal) => modal.addEventListener('click', (event) => {
        if (event.target === modal || event.target.closest('.management-modal-close')) closeModal();
    }));
    document.addEventListener('keydown', (event) => {
        if (event.key === 'Escape' && activeModal) closeModal();
    });
})();
</script>
@endsection
