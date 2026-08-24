<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width,initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Sign In — FuelFree PowerPlant</title>
    <style>
        :root{color-scheme:dark;font-family:Inter,system-ui,-apple-system,BlinkMacSystemFont,"Segoe UI",sans-serif}*{box-sizing:border-box}body{margin:0;min-height:100vh;background:radial-gradient(circle at 20% 10%,rgba(42,157,189,.18),transparent 35%),#04111a;color:#eaf8fb;display:grid;place-items:center;padding:20px}.shell{width:min(1050px,100%);display:grid;grid-template-columns:1.1fr .9fr;border:1px solid rgba(111,210,239,.16);border-radius:28px;overflow:hidden;background:rgba(7,28,41,.84);box-shadow:0 35px 100px rgba(0,0,0,.45);backdrop-filter:blur(18px)}.brand{padding:clamp(32px,6vw,70px);position:relative;overflow:hidden;background:linear-gradient(145deg,rgba(8,49,65,.75),rgba(5,23,34,.6))}.brand:after{content:"";position:absolute;width:260px;height:260px;border-radius:50%;right:-100px;bottom:-120px;background:rgba(57,196,226,.12);filter:blur(10px)}.eyebrow{font-size:10px;letter-spacing:.22em;color:#65cdea}.brand h1{font-size:clamp(38px,5vw,68px);line-height:.98;margin:20px 0 18px}.brand p{max-width:500px;color:#86a7b6;line-height:1.8}.form{padding:clamp(28px,5vw,58px);background:rgba(2,15,23,.5)}h2{margin:0 0 8px;font-size:30px}.hint{margin:0 0 28px;color:#7f9dab;font-size:14px}label{display:block;margin:17px 0 7px;font-size:12px;color:#9db7c2}input{width:100%;border:1px solid rgba(116,198,224,.16);background:rgba(255,255,255,.035);color:#fff;border-radius:12px;padding:14px 15px;outline:none}input:focus{border-color:rgba(93,206,239,.55);box-shadow:0 0 0 3px rgba(93,206,239,.08)}.remember{display:flex;align-items:center;gap:9px;margin:15px 0;color:#829ca8;font-size:12px}.remember input{width:auto}.btn{width:100%;border:0;border-radius:12px;padding:14px 16px;margin-top:10px;background:linear-gradient(135deg,#36b8dc,#1c83a5);color:#fff;font-weight:700;cursor:pointer}.errors{padding:11px 13px;border-radius:10px;background:rgba(210,65,65,.12);border:1px solid rgba(210,65,65,.22);color:#ffb0b0;font-size:12px}@media(max-width:760px){.shell{grid-template-columns:1fr;border-radius:20px}.brand{padding:30px}.brand h1{font-size:42px}.brand p{display:none}.form{padding:30px}}
    </style>
</head>
<body>
<div class="shell">
    <section class="brand">
        <div class="eyebrow">FUELFREE POWERPLANT</div>
        <h1>Powering a cleaner, smarter future.</h1>
        <p>Secure access to projects, documents, infrastructure, communications and client services.</p>
    </section>
    <section class="form">
        <h2>Sign in</h2>
        <p class="hint">Access your secure portal.</p>
        @if($errors->any())
            <div class="errors">{{ $errors->first() }}</div>
        @endif
        <form method="POST" action="{{ route('login.store') }}">
            @csrf
            <label for="email">Email address</label>
            <input id="email" name="email" type="email" value="{{ old('email') }}" autocomplete="email" required autofocus>
            <label for="password">Password</label>
            <input id="password" name="password" type="password" autocomplete="current-password" required>
            <label class="remember"><input type="checkbox" name="remember" value="1"> Remember me</label>
            <button class="btn" type="submit">Sign in securely</button>
        </form>
    </section>
</div>
</body>
</html>
