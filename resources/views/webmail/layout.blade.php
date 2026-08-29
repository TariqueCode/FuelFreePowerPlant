@php
    $publicBrand = \App\Models\SystemSetting::query()
        ->whereIn('key', ['company.name','company.logo_path','company.tagline'])
        ->pluck('value','key');
    $webmailName = $publicBrand->get('company.name') ?: config('fuelfree.company.name', 'FuelFree PowerPlant');
    $webmailLogo = $publicBrand->get('company.logo_path');
    $nameParts = preg_split('/\s+/', trim((string) $webmailName), 2);
    $nameFirst = $nameParts[0] ?? '';
    $nameRest = $nameParts[1] ?? '';
@endphp
<!doctype html>
<html lang="en">
<head>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width,initial-scale=1,viewport-fit=cover">
<meta name="csrf-token" content="{{ csrf_token() }}">
<title>{{ $title ?? 'Webmail' }} — {{ $webmailName }}</title>
@if($webmailLogo)<link rel="icon" type="image/png" href="{{ asset('storage/'.ltrim($webmailLogo,'/')) }}">@endif
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.7.2/css/all.min.css">
<style>
:root{--bg:#020a10;--panel:#071e28;--line:rgba(86,210,238,.15);--text:#edfaff;--muted:#8eaab4;--accent:#51d8f0}
*{box-sizing:border-box}
html{background:var(--bg);scroll-behavior:smooth}
body{margin:0;background:radial-gradient(circle at 12% 0,#0a2935 0,transparent 34%),linear-gradient(180deg,#020a10 0%,#061721 55%,#020a10 100%);color:var(--text);font-family:Inter,system-ui,-apple-system,BlinkMacSystemFont,"Segoe UI",sans-serif;min-height:100vh}
a{color:inherit;text-decoration:none}
button,input,textarea{font:inherit}
.wm-shell{min-height:100vh;display:flex;flex-direction:column}
.wm-top{height:76px;border-bottom:1px solid var(--line);background:rgba(2,10,16,.94);backdrop-filter:blur(16px);display:flex;align-items:center;justify-content:space-between;padding:0 clamp(16px,4vw,48px);position:sticky;top:0;z-index:50}
.brand{display:flex;align-items:center;gap:12px;min-width:0;font-weight:800}
.brand-logo{width:42px;height:42px;object-fit:contain;border-radius:9px;flex:0 0 42px}
.brand-mark{width:42px;height:42px;border-radius:11px;border:1px solid rgba(86,210,238,.22);display:grid;place-items:center;color:var(--accent);background:#061a23;flex:0 0 42px}
.brand-name{white-space:nowrap;overflow:hidden;text-overflow:ellipsis;font-size:18px;letter-spacing:-.02em}
.brand-name-first{color:var(--accent)}
.brand-name-rest{color:#effcff}
.top-actions{display:flex;gap:9px;align-items:center}
.pill{border:1px solid var(--line);border-radius:999px;padding:9px 13px;color:var(--muted);font-size:13px;max-width:280px;overflow:hidden;text-overflow:ellipsis;white-space:nowrap}
.btn{display:inline-flex;align-items:center;justify-content:center;gap:8px;border:1px solid var(--line);border-radius:11px;padding:11px 15px;background:rgba(7,30,40,.88);color:var(--text);font-weight:750;cursor:pointer;transition:.18s ease}
.btn:hover{background:#0b2d38;border-color:rgba(86,210,238,.3);transform:translateY(-1px)}
.btn.primary{background:linear-gradient(135deg,#20b7d4,#26a9cf);border-color:#2bc5df;color:#00151b}
.btn.icon{width:42px;height:42px;padding:0}
.btn.danger{color:#ffc4c8;border-color:rgba(255,100,110,.22)}
.wm-body{width:min(1180px,calc(100% - 32px));margin:28px auto;display:grid;grid-template-columns:220px minmax(0,1fr);gap:22px;flex:1}
.side{background:rgba(7,30,40,.76);border:1px solid var(--line);border-radius:20px;padding:12px;height:max-content;position:sticky;top:94px}
.side-nav{display:grid;gap:4px}
.side a{display:flex;align-items:center;gap:11px;padding:13px 14px;border-radius:12px;color:var(--muted);font-weight:700}
.side a i{width:18px;text-align:center;color:#65cfe4}
.side a.active,.side a:hover{background:#0b2d38;color:var(--text)}
.side-divider{height:1px;background:var(--line);margin:10px 5px}
.side-note{padding:11px 13px;color:#6f909b;font-size:11px;line-height:1.6}
.main{min-width:0}
.hero{display:flex;justify-content:space-between;gap:18px;align-items:flex-start;margin-bottom:18px}
.hero-copy{min-width:0}
.eyebrow{color:var(--accent);font-size:12px;letter-spacing:.18em;text-transform:uppercase;font-weight:850}
.title{font-size:clamp(28px,5vw,42px);margin:7px 0}
.sub{color:var(--muted);line-height:1.6;margin:0}
.card{background:rgba(7,30,40,.82);border:1px solid var(--line);border-radius:20px;overflow:hidden;box-shadow:0 18px 50px rgba(0,0,0,.12)}
.flash,.error{padding:13px 16px;border-radius:14px;margin-bottom:16px}
.flash{background:#08362f;color:#a8f2df;border:1px solid #145d4e}
.error{background:#391d24;color:#ffc1c5;border:1px solid #6b3039}
.mail-toolbar{display:flex;align-items:center;gap:10px;padding:13px;border-bottom:1px solid var(--line);background:rgba(5,22,30,.55)}
.search-box{position:relative;flex:1;min-width:180px}
.search-box i{position:absolute;left:13px;top:50%;transform:translateY(-50%);color:#638692}
.search-box input{width:100%;height:42px;padding:0 13px 0 38px;border:1px solid var(--line);border-radius:11px;background:#041a23;color:var(--text);outline:none}
.search-box input:focus{border-color:var(--accent)}
.mail-count{color:var(--muted);font-size:12px;white-space:nowrap}
.folder-strip{display:flex;gap:7px;flex-wrap:wrap;padding:11px 13px;border-bottom:1px solid var(--line);background:rgba(5,22,30,.32)}
.folder-chip{display:inline-flex;align-items:center;gap:7px;padding:8px 11px;border:1px solid transparent;border-radius:9px;color:var(--muted);font-size:12px;font-weight:750}
.folder-chip:hover{background:#0b2d38;color:var(--text)}
.folder-chip.active{background:rgba(67,209,240,.09);border-color:rgba(86,210,238,.18);color:#8bf3ff}
.message-row{display:grid;grid-template-columns:34px minmax(130px,190px) minmax(0,1fr) auto;gap:12px;align-items:center;padding:16px 18px;border-bottom:1px solid var(--line);transition:background .15s}
.message-row:last-child{border-bottom:0}
.message-row:hover{background:#092732}
.message-row.hidden{display:none}
.unread .subject,.unread .from{font-weight:800;color:var(--text)}
.from,.subject{overflow:hidden;text-overflow:ellipsis;white-space:nowrap}
.date{color:var(--muted);font-size:13px}
.empty{padding:70px 20px;text-align:center;color:var(--muted)}
.empty i{font-size:42px;margin-bottom:12px;color:#4f7f8c}
.compose,.reader{padding:22px}
.field{margin-bottom:16px}
.field label{display:block;margin-bottom:7px;color:#a9c2ca;font-weight:700;font-size:14px}
.field input,.field textarea{width:100%;border:1px solid var(--line);background:#041a23;color:var(--text);border-radius:13px;padding:13px 14px;outline:none}
.field input:focus,.field textarea:focus{border-color:var(--accent)}
.field textarea{min-height:280px;resize:vertical}
.reader-head{border-bottom:1px solid var(--line);padding-bottom:18px;margin-bottom:18px}
.reader-head h2{margin:6px 0 12px;font-size:25px;overflow-wrap:anywhere}
.meta{color:var(--muted);font-size:14px;line-height:1.7;overflow-wrap:anywhere}
.reader-actions{display:flex;gap:9px;flex-wrap:wrap;margin-bottom:18px}
.reader-actions form{display:contents}
.reader-body{position:relative;isolation:isolate;contain:content;line-height:1.75;overflow-x:auto;overflow-y:visible;overflow-wrap:anywhere;-webkit-overflow-scrolling:touch;background:#fff;color:#17232a;border:1px solid rgba(86,210,238,.12);border-radius:14px;padding:16px;max-width:100%}
.reader-body>*{max-width:100%}
.reader-body a{color:#087f9c;text-decoration:underline}
.reader-body img{max-width:100%!important;height:auto!important}
.reader-body video,.reader-body iframe{max-width:100%!important;height:auto}
.reader-body table{border-collapse:collapse}
.reader-body th,.reader-body td{border:1px solid #ccd8dc;padding:7px}
.reader-body pre,.reader-body code{white-space:pre-wrap;overflow-wrap:anywhere}
.attachments{padding:13px 0;border-bottom:1px solid var(--line);margin-bottom:18px}
.attachments>strong{font-size:13px}
.attachment-grid{display:grid;grid-template-columns:repeat(auto-fit,minmax(220px,1fr));gap:8px;margin-top:10px}
.attachment{display:flex;align-items:center;gap:9px;padding:10px;border:1px solid var(--line);border-radius:11px;background:#041a23}
.attachment>i:first-child{color:var(--accent);font-size:18px}
.attachment span{min-width:0;flex:1}
.attachment b,.attachment small{display:block;overflow:hidden;text-overflow:ellipsis;white-space:nowrap}
.attachment b{font-size:12px}
.attachment small{font-size:10px;color:var(--muted);margin-top:3px}
.attachment>i:last-child{color:var(--muted)}
.login-wrap{min-height:calc(100vh - 76px);display:grid;place-items:center;padding:30px 20px}
.login-card{width:min(460px,100%);background:rgba(7,30,40,.9);border:1px solid var(--line);border-radius:26px;padding:30px;box-shadow:0 24px 80px rgba(0,0,0,.3)}
.login-card h1{font-size:30px;margin:0 0 8px}
.login-card .sub{margin-bottom:24px}
.hint{font-size:13px;color:var(--muted);margin-top:14px;line-height:1.6}
.full{width:100%}
.wm-footer-wrap{width:min(1180px,calc(100% - 32px));margin:0 auto}
@media(max-width:760px){
 .wm-top{height:64px;padding:0 14px}
 .brand-logo,.brand-mark{width:36px;height:36px;flex-basis:36px}
 .brand-name{font-size:15px}
 .pill{display:none}
 .wm-body{display:block;width:calc(100% - 20px);margin:16px auto}
 .side{display:none}
 .hero{align-items:center}
 .hero .btn{padding:10px 12px}
 .message-row{grid-template-columns:30px minmax(0,1fr) auto;gap:9px}
 .message-row .from{grid-column:2}
 .message-row .subject{grid-column:2}
 .message-row .date{grid-column:3;grid-row:1 / span 2}
 .mail-toolbar{flex-wrap:wrap}
 .search-box{order:1;flex-basis:100%}
 .mail-count{margin-left:3px}
 .folder-strip{overflow:auto;flex-wrap:nowrap;scrollbar-width:none}
 .folder-strip::-webkit-scrollbar{display:none}
 .folder-chip{white-space:nowrap;flex:0 0 auto}
 .compose,.reader{padding:15px}
 .reader-actions .btn,.reader-actions form{flex:1}
 .reader-actions form .btn{width:100%}
 .reader-body{padding:12px;font-size:14px}
 .login-wrap{padding:20px}
 .login-card{padding:24px}
 .wm-footer-wrap{width:calc(100% - 20px)}
}
@media(max-width:420px){
 .message-row{padding:13px 12px}
 .message-row .date{font-size:11px}
 .title{font-size:30px}
 .hero .btn{font-size:12px}
 .brand-name{max-width:210px}
 .reader-body{font-size:13px}
}
</style>
@stack('styles')
</head>
<body>
<div class="wm-shell">
<header class="wm-top">
    <a class="brand" href="{{ config('cpanel.webmail_url','https://mail.fuelfreepowerplant.com') }}/inbox" aria-label="{{ $webmailName }}">
        @if($webmailLogo)<img class="brand-logo" src="{{ asset('storage/'.ltrim($webmailLogo,'/')) }}" alt="{{ $webmailName }}">@else<span class="brand-mark"><i class="fa-solid fa-envelope"></i></span>@endif
        <span class="brand-name"><span class="brand-name-first">{{ $nameFirst }}</span>@if($nameRest) <span class="brand-name-rest"> {{ $nameRest }}</span>@endif</span>
    </a>
    @isset($email)
    <div class="top-actions"><span class="pill"><i class="fa-solid fa-circle-user"></i> {{ $email }}</span><form method="POST" action="{{ config('cpanel.webmail_url','https://mail.fuelfreepowerplant.com') }}/logout">@csrf<button class="btn" type="submit"><i class="fa-solid fa-right-from-bracket"></i><span>Sign out</span></button></form></div>
    @endisset
</header>
@isset($email)
<div class="wm-body">
<nav class="side" aria-label="Mailbox navigation">
    <div class="side-nav">
        <a class="{{ request()->is('inbox')||request()->is('message/*')?'active':'' }}" href="{{ config('cpanel.webmail_url','https://mail.fuelfreepowerplant.com') }}/inbox"><i class="fa-solid fa-inbox"></i><span>Inbox</span></a>
        <a class="{{ request()->is('compose')?'active':'' }}" href="{{ config('cpanel.webmail_url','https://mail.fuelfreepowerplant.com') }}/compose"><i class="fa-solid fa-pen-to-square"></i><span>Compose</span></a>
    </div>
    <div class="side-divider"></div>
    <div class="side-note"><i class="fa-solid fa-shield-halved"></i> Your mailbox password is used only for the live mail connection and is never shown in the interface.</div>
</nav>
<main class="main">
@if(session('status'))<div class="flash"><i class="fa-solid fa-circle-check"></i> {{ session('status') }}</div>@endif
@if($errors->any())<div class="error"><i class="fa-solid fa-triangle-exclamation"></i> {{ $errors->first() }}</div>@endif
@yield('content')
</main>
</div>
@else
@yield('content')
@endisset
<div class="wm-footer-wrap">@include('partials.public-footer', ['brand' => $publicBrand])</div>
</div>
@stack('scripts')
</body>
</html>