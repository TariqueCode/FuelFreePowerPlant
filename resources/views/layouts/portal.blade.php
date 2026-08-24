<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width,initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Dashboard') — {{ config('fuelfree.company.name') }}</title>
    <style>
        :root{font-family:Inter,ui-sans-serif,system-ui,-apple-system,BlinkMacSystemFont,"Segoe UI",sans-serif;color-scheme:dark;--bg:#031019;--panel:#071d2a;--line:rgba(104,204,235,.13);--text:#eaf8fb;--muted:#86a5b4;--accent:#43c2e5}*{box-sizing:border-box}html,body{margin:0;min-height:100%;background:radial-gradient(circle at 75% 0,rgba(25,147,181,.14),transparent 28%),var(--bg);color:var(--text)}button,a{font:inherit}.app{min-height:100vh;display:grid;grid-template-columns:260px 1fr}.sidebar{position:sticky;top:0;height:100vh;padding:22px 16px;border-right:1px solid var(--line);background:rgba(3,16,25,.82);backdrop-filter:blur(18px)}.brand{padding:8px 10px 24px}.brand small{display:block;letter-spacing:.2em;color:#61c9e8;font-size:9px}.brand strong{display:block;margin-top:7px;font-size:16px}.nav{display:grid;gap:6px}.nav a{display:flex;align-items:center;gap:11px;padding:12px;border-radius:11px;color:var(--muted);text-decoration:none;font-size:13px}.nav a:hover,.nav a.active{background:rgba(67,194,229,.09);color:var(--text)}.sidebar-footer{position:absolute;left:16px;right:16px;bottom:20px}.logout{width:100%;padding:11px;border:1px solid var(--line);border-radius:11px;background:transparent;color:var(--muted);cursor:pointer}.main{min-width:0}.topbar{height:72px;display:flex;align-items:center;justify-content:space-between;padding:0 clamp(18px,4vw,42px);border-bottom:1px solid var(--line);background:rgba(3,16,25,.55);backdrop-filter:blur(16px)}.topbar-title{font-size:14px;color:var(--muted)}.user-chip{font-size:12px;color:#b9d2dc}.content{padding:clamp(20px,4vw,42px);max-width:1500px}.eyebrow{font-size:9px;letter-spacing:.2em;color:#5fc7e8}.hero{margin:7px 0 28px}.hero h1{font-size:clamp(30px,4vw,52px);line-height:1;margin:8px 0}.hero p{color:var(--muted);max-width:700px}.grid{display:grid;grid-template-columns:repeat(4,minmax(0,1fr));gap:14px}.card{min-width:0;padding:20px;border:1px solid var(--line);border-radius:16px;background:linear-gradient(145deg,rgba(9,38,54,.75),rgba(5,22,32,.82));box-shadow:0 18px 45px rgba(0,0,0,.14)}.card-label{font-size:10px;letter-spacing:.08em;text-transform:uppercase;color:#7699a8}.card-value{display:block;margin-top:10px;font-size:26px;font-weight:700}.card-note{display:block;margin-top:7px;color:#7193a1;font-size:11px}.section{margin-top:18px}.section h2{font-size:16px}.section p{color:var(--muted);font-size:13px;line-height:1.7}.mobile-menu{display:none}@media(max-width:900px){.app{grid-template-columns:78px 1fr}.sidebar{padding:18px 10px}.brand strong,.brand small,.nav span,.logout{font-size:0}.brand{padding:8px}.nav a{justify-content:center}.sidebar-footer{left:10px;right:10px}.grid{grid-template-columns:repeat(2,minmax(0,1fr))}}@media(max-width:620px){.app{display:block}.sidebar{position:fixed;z-index:20;left:0;right:0;bottom:0;top:auto;width:100%;height:64px;padding:7px 10px;border-right:0;border-top:1px solid var(--line);display:flex;align-items:center}.brand,.sidebar-footer{display:none}.nav{width:100%;display:grid;grid-template-columns:repeat(4,1fr);gap:3px}.nav a{height:50px;padding:5px;font-size:9px;display:flex;flex-direction:column;gap:3px}.nav span{font-size:9px}.main{padding-bottom:64px}.topbar{height:62px;padding:0 17px}.content{padding:22px 16px}.grid{grid-template-columns:1fr 1fr}.card{padding:16px}.card-value{font-size:21px}}@media(max-width:380px){.grid{grid-template-columns:1fr}}
    </style>
    @stack('head')
</head>
<body>
<div class="app">
    <aside class="sidebar">
        <div class="brand"><small>FUELFREE</small><strong>PowerPlant</strong></div>
        <nav class="nav" aria-label="Primary navigation">
            @if(auth()->user()->hasRole(['super-admin','administrator','project-manager','support-agent']))
                <a class="active" href="{{ route('admin.dashboard') }}"><b>◈</b><span>Overview</span></a>
            @endif
            @if(auth()->user()->hasPermission('documents.view'))
                <a href="#documents"><b>▣</b><span>Documents</span></a>
            @endif
            @if(auth()->user()->hasPermission('email.view'))
                <a href="#email"><b>✉</b><span>Email</span></a>
            @endif
            @if(auth()->user()->hasPermission('support.view'))
                <a href="#support"><b>?</b><span>Support</span></a>
            @endif
            @if(auth()->user()->hasRole('client'))
                <a class="active" href="{{ route('portal.dashboard') }}"><b>◈</b><span>Overview</span></a>
            @endif
        </nav>
        <div class="sidebar-footer">
            <form method="POST" action="{{ route('logout') }}">@csrf<button class="logout" type="submit">Sign out</button></form>
        </div>
    </aside>
    <main class="main">
        <header class="topbar"><div class="topbar-title">{{ config('fuelfree.company.name') }}</div><div class="user-chip">{{ auth()->user()->name }} · {{ auth()->user()->email }}</div></header>
        <div class="content">@yield('content')</div>
    </main>
</div>
@stack('scripts')
</body>
</html>
