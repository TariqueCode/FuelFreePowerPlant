@extends('layouts.portal')

@section('title', 'Admin Dashboard')

@section('content')
<section class="app-hero">
    <div>
        <div class="eyebrow">OPERATIONS CONTROL CENTER</div>
        <h1>Good to see you, <span>{{ auth()->user()->name }}</span>.</h1>
        <p>Everything important about the FuelFree PowerPlant platform, in one secure workspace.</p>
    </div>
    <div class="hero-orbit" aria-hidden="true"><i></i><b>⚡</b></div>
</section>

<section class="quick-grid" aria-label="Platform overview">
    @if(!is_null($users))
        <a class="quick-card users" href="{{ route('admin.users.index') }}"><span class="quick-icon">♙</span><span class="quick-copy"><small>Users</small><strong>{{ number_format($users) }}</strong><em>Manage accounts</em></span><span class="quick-arrow">›</span></a>
    @endif
    @if(auth()->user()->hasPermission('documents.view'))
        <a class="quick-card files" href="{{ route('admin.documents') }}"><span class="quick-icon">▣</span><span class="quick-copy"><small>Documents</small><strong>{{ number_format($documents) }}</strong><em>Secure file vault</em></span><span class="quick-arrow">›</span></a>
    @endif
    <article class="quick-card folders"><span class="quick-icon">▱</span><span class="quick-copy"><small>Folders</small><strong>{{ number_format($folders) }}</strong><em>Private folders</em></span></article>
    <article class="quick-card storage"><span class="quick-icon">◉</span><span class="quick-copy"><small>Storage</small><strong>{{ number_format($storageBytes / 1073741824, 2) }} GB</strong><em>Current usage</em></span></article>
</section>

<section class="mobile-actions" aria-label="Quick actions">
    @if(auth()->user()->hasPermission('documents.view'))<a href="{{ route('admin.documents') }}"><span>▣</span>Files</a>@endif
    @if(auth()->user()->hasPermission('email.view'))<a href="{{ route('admin.email') }}"><span>✉</span>Email</a>@endif
    @if(auth()->user()->hasPermission('users.view'))<a href="{{ route('admin.users.index') }}"><span>♙</span>Users</a>@endif
    @if(auth()->user()->hasPermission('support.view'))<a href="{{ route('admin.support') }}"><span>?</span>Support</a>@endif
</section>

<section class="status-card">
    <div class="status-head"><div><span class="status-dot"></span><span class="eyebrow">SYSTEM STATUS</span></div><span class="live-pill">LIVE</span></div>
    <h2>Your control center is ready.</h2>
    <p>Authentication, role permissions, secure documents and the responsive admin workspace are active. Email, support and other operational modules connect through the same permission-aware foundation.</p>
    <div class="status-line"><span><i></i> Core platform</span><span><i></i> Secure session</span><span><i></i> Permission aware</span></div>
</section>

@push('styles')
<style>
.app-hero{position:relative;min-height:220px;display:flex;align-items:center;justify-content:space-between;gap:24px;padding:28px 0 20px;overflow:hidden}.app-hero h1{margin:9px 0 10px;font-size:clamp(30px,4vw,50px);line-height:1.02;letter-spacing:-.035em}.app-hero h1 span{background:linear-gradient(100deg,#eefbff,#63d8f3);-webkit-background-clip:text;background-clip:text;color:transparent}.app-hero p{margin:0;max-width:660px;color:#86a5b4;line-height:1.65;font-size:14px}.hero-orbit{width:145px;height:145px;flex:0 0 145px;border:1px solid rgba(67,194,229,.16);border-radius:50%;display:grid;place-items:center;position:relative;box-shadow:0 0 70px rgba(27,177,215,.08),inset 0 0 40px rgba(27,177,215,.06);animation:floatOrb 5s ease-in-out infinite}.hero-orbit:before,.hero-orbit:after{content:"";position:absolute;border:1px solid rgba(67,194,229,.13);border-radius:50%}.hero-orbit:before{inset:15px}.hero-orbit:after{inset:32px}.hero-orbit i{position:absolute;width:8px;height:8px;border-radius:50%;background:#5bd7ef;top:10px;left:50%;box-shadow:0 0 15px #5bd7ef;animation:spinOrb 5s linear infinite}.hero-orbit b{font-size:35px;font-weight:500;color:#70ddf5;text-shadow:0 0 24px rgba(67,194,229,.55)}.quick-grid{display:grid;grid-template-columns:repeat(4,minmax(0,1fr));gap:13px}.quick-card{position:relative;min-width:0;min-height:142px;padding:18px;display:flex;align-items:flex-start;gap:13px;border:1px solid var(--line);border-radius:18px;background:linear-gradient(145deg,rgba(9,39,55,.86),rgba(5,22,32,.9));color:var(--text);text-decoration:none;overflow:hidden;box-shadow:0 14px 35px rgba(0,0,0,.12);animation:cardIn .5s ease both}.quick-card:before{content:"";position:absolute;inset:auto -20% -70% 20%;height:110px;background:radial-gradient(circle,rgba(67,194,229,.13),transparent 65%);pointer-events:none}.quick-card:hover{transform:translateY(-4px);border-color:rgba(104,204,235,.28);box-shadow:0 22px 45px rgba(0,0,0,.2)}.quick-icon{width:44px;height:44px;flex:0 0 44px;display:grid;place-items:center;border-radius:14px;background:rgba(67,194,229,.09);border:1px solid rgba(104,204,235,.12);color:#6ad8f0;font-size:20px;box-shadow:inset 0 0 20px rgba(67,194,229,.05)}.quick-copy{min-width:0;display:flex;flex-direction:column}.quick-copy small{text-transform:uppercase;letter-spacing:.09em;color:#7193a1;font-size:9px}.quick-copy strong{font-size:25px;line-height:1.2;margin:7px 0 4px}.quick-copy em{font-style:normal;color:#6f919f;font-size:10px}.quick-arrow{margin-left:auto;color:#5f8290;font-size:23px;line-height:1}.status-card{position:relative;margin-top:16px;padding:24px;border:1px solid var(--line);border-radius:20px;background:linear-gradient(145deg,rgba(8,34,48,.8),rgba(4,20,29,.88));overflow:hidden}.status-card:after{content:"";position:absolute;width:240px;height:240px;right:-110px;bottom:-150px;background:radial-gradient(circle,rgba(44,192,230,.13),transparent 68%);pointer-events:none}.status-head{display:flex;align-items:center;justify-content:space-between}.status-head>div{display:flex;align-items:center;gap:8px}.status-dot{width:7px;height:7px;border-radius:50%;background:#55dfaa;box-shadow:0 0 12px rgba(85,223,170,.75);animation:pulseDot 2s ease-in-out infinite}.live-pill{font-size:9px;letter-spacing:.12em;color:#8ce7c1;padding:5px 8px;border:1px solid rgba(85,223,170,.2);border-radius:999px;background:rgba(85,223,170,.06)}.status-card h2{margin:14px 0 8px;font-size:20px}.status-card p{margin:0;max-width:800px;color:#829faa;font-size:12px;line-height:1.75}.status-line{display:flex;gap:22px;flex-wrap:wrap;margin-top:19px;color:#7898a5;font-size:10px}.status-line i{display:inline-block;width:5px;height:5px;border-radius:50%;background:#4fc5e5;margin-right:6px}.mobile-actions{display:none}@keyframes cardIn{from{opacity:0;transform:translateY(10px)}to{opacity:1;transform:none}}@keyframes floatOrb{0%,100%{transform:translateY(0)}50%{transform:translateY(-7px)}}@keyframes spinOrb{to{transform:rotate(360deg) translateX(60px) rotate(-360deg)}}@keyframes pulseDot{50%{transform:scale(1.5);opacity:.65}}
@media(max-width:900px){.quick-grid{grid-template-columns:repeat(2,minmax(0,1fr))}.hero-orbit{width:115px;height:115px;flex-basis:115px}}
@media(max-width:620px){.app-hero{min-height:0;padding:7px 0 20px;align-items:flex-start}.app-hero h1{font-size:30px;line-height:1.08;max-width:340px}.app-hero p{font-size:12px;max-width:330px}.hero-orbit{width:82px;height:82px;flex-basis:82px;margin-top:8px}.hero-orbit b{font-size:22px}.hero-orbit i{width:5px;height:5px;top:7px}.quick-grid{grid-template-columns:1fr 1fr;gap:10px}.quick-card{min-height:116px;padding:14px;border-radius:16px;gap:10px}.quick-icon{width:38px;height:38px;flex-basis:38px;border-radius:12px;font-size:17px}.quick-copy strong{font-size:20px}.quick-copy em{font-size:9px}.quick-arrow{display:none}.mobile-actions{display:grid;grid-template-columns:repeat(4,1fr);gap:8px;margin-top:12px}.mobile-actions a{min-width:0;min-height:68px;display:flex;flex-direction:column;align-items:center;justify-content:center;gap:6px;border:1px solid var(--line);border-radius:14px;background:rgba(8,33,46,.7);color:#8caab6;text-decoration:none;font-size:9px}.mobile-actions a span{font-size:17px;color:#63d5ef}.mobile-actions a:active{transform:scale(.97);background:rgba(67,194,229,.1)}.status-card{margin-top:12px;padding:18px;border-radius:17px}.status-card h2{font-size:17px}.status-card p{font-size:11px;line-height:1.7}.status-line{gap:10px 15px}.status-line span{font-size:9px}}
@media(prefers-reduced-motion:reduce){.hero-orbit,.hero-orbit i,.status-dot,.quick-card{animation:none!important}}
</style>
@endpush
@endsection
