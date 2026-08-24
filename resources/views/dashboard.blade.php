<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Dashboard — {{ $platform }}</title>
    <style>
        :root{color-scheme:dark;font-family:Inter,system-ui,sans-serif}body{margin:0;background:#06131d;color:#e7f5fa}main{min-height:100vh;display:grid;place-items:center;padding:24px;box-sizing:border-box}.panel{width:min(900px,100%);border:1px solid rgba(100,200,240,.15);border-radius:24px;padding:clamp(24px,5vw,48px);background:linear-gradient(145deg,rgba(11,37,53,.92),rgba(4,18,28,.96));box-shadow:0 30px 90px rgba(0,0,0,.35)}.eyebrow{font-size:10px;letter-spacing:.2em;color:#67c9ea}.user{margin-top:8px;color:#83a9b9;font-size:14px}h1{font-size:clamp(32px,7vw,64px);line-height:1;margin:14px 0}p{color:#8da9b7;line-height:1.7}.grid{display:grid;grid-template-columns:repeat(3,1fr);gap:12px;margin-top:28px}.card{padding:18px;border:1px solid rgba(100,200,240,.1);border-radius:15px;background:rgba(255,255,255,.025)}.card b{display:block;font-size:12px}.card span{font-size:10px;color:#66899a}@media(max-width:650px){.grid{grid-template-columns:1fr}.panel{border-radius:18px}}
    </style>
</head>
<body>
<main><section class="panel">
    <div class="eyebrow">FUELFREE POWERPLANT / SECURE PORTAL</div>
    <h1>Welcome, {{ $user->name }}.</h1>
    <div class="user">{{ $user->email }}</div>
    <p>The authenticated dashboard foundation is active. Feature modules will be connected here progressively.</p>
    <div class="grid">
        <div class="card"><b>Documents</b><span>Secure vault</span></div>
        <div class="card"><b>Email</b><span>Mailbox management</span></div>
        <div class="card"><b>Support</b><span>Client communication</span></div>
    </div>
</section></main>
</body>
</html>
