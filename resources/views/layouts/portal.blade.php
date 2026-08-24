<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width,initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Dashboard') — {{ config('fuelfree.company.name') }}</title>
    <style>
        :root{font-family:Inter,ui-sans-serif,system-ui,-apple-system,BlinkMacSystemFont,"Segoe UI",sans-serif;color-scheme:dark;--bg:#031019;--panel:#071d2a;--line:rgba(104,204,235,.13);--text:#eaf8fb;--muted:#86a5b4;--accent:#43c2e5}*{box-sizing:border-box}html,body{margin:0;min-height:100%;background:radial-gradient(circle at 75% 0,rgba(25,147,181,.14),transparent 28%),var(--bg);color:var(--text)}button,a{font:inherit}.app{min-height:100vh;display:grid;grid-template-columns:260px 1fr}.sidebar{position:sticky;top:0;height:100vh;padding:22px 16px;border-right:1px solid var(--line);background:rgba(3,16,25,.82);backdrop-filter:blur(18px)}.brand{padding:8px 10px 24px}.brand small{display:block;letter-spacing:.2em;color:#61c9e8;font-size:9px}.brand strong{display:block;margin-top:7px;font-size:16px}.nav{display:grid;gap:6px}.nav a{display:flex;align-items:center;gap:11px;padding:12px;border-radius:11px;color:var(--muted);text-decoration:none;font-size:13px}.nav a:hover,.nav a.active{background:rgba(67,194,229,.09);color:var(--text)}.sidebar-footer{position:absolute;left:16px;right:16px;bottom:20px}.logout{width:100%;padding:11px;border:1px solid var(--line);border-radius:11px;background:transparent;color:var(--muted);cursor:pointer}.main{min-width:0}.topbar{height:72px;display:flex;align-items:center;justify-content:space-between;padding:0 clamp(18px,4vw,42px);border-bottom:1px solid var(--line);background:rgba(3,16,25,.55);backdrop-filter:blur(16px)}.topbar-title{font-size:14px;color:var(--muted)}.user-chip{font-size:12px;color:#b9d2dc;white-space:nowrap;overflow:hidden;text-overflow:ellipsis;max-width:55vw}.content{padding:clamp(20px,4vw,42px);max-width:1500px}.eyebrow{font-size:9px;letter-spacing:.2em;color:#5fc7e8}.hero{margin:7px 0 28px}.hero h1{font-size:clamp(30px,4vw,52px);line-height:1;margin:8px 0}.hero p{color:var(--muted);max-width:700px}.grid{display:grid;grid-template-columns:repeat(4,minmax(0,1fr));gap:14px}.card{min-width:0;padding:20px;border:1px solid var(--line);border-radius:16px;background:linear-gradient(145deg,rgba(9,38,54,.75),rgba(5,22,32,.82));box-shadow:0 18px 45px rgba(0,0,0,.14)}.card-label{font-size:10px;letter-spacing:.08em;text-transform:uppercase;color:#7699a8}.card-value{display:block;margin-top:10px;font-size:26px;font-weight:700}.card-note{display:block;margin-top:7px;color:#7193a1;font-size:11px}.section{margin-top:18px}.section h2{font-size:16px}.section p{color:var(--muted);font-size:13px;line-height:1.7}.toolbar{display:flex;justify-content:flex-end;margin-bottom:14px}.action{display:inline-block;padding:11px 15px;border-radius:11px;background:#31afd2;color:#fff;text-decoration:none;font-weight:700;font-size:13px}.notice{padding:12px 14px;border-radius:12px;margin-bottom:14px;background:rgba(67,194,137,.1);border:1px solid rgba(67,194,137,.2);color:#a8e5ca}.table-card{background:rgba(255,255,255,.025);border:1px solid var(--line);border-radius:18px;overflow:hidden}.table-wrap{overflow-x:auto}table{width:100%;border-collapse:collapse;min-width:620px}th,td{text-align:left;padding:15px 17px;border-bottom:1px solid rgba(255,255,255,.06);font-size:13px}th{color:#74cce9;font-size:11px;letter-spacing:.08em;text-transform:uppercase}td{color:#b5cbd4}.pagination{padding:12px}.form-card{max-width:760px;background:rgba(255,255,255,.025);border:1px solid var(--line);border-radius:18px;padding:22px}.fields{display:grid;grid-template-columns:1fr 1fr;gap:16px}label{display:block;font-size:12px;color:#9eb9c4;margin:0 0 7px}input,select{width:100%;box-sizing:border-box;padding:13px;border-radius:11px;border:1px solid var(--line);background:#071c29;color:#e9f7fb;outline:none}.full{grid-column:1/-1}.errors{margin-bottom:16px;padding:11px;border-radius:10px;background:rgba(210,65,65,.12);color:#ffb0b0}.actions{display:flex;justify-content:flex-end;gap:12px;margin-top:22px;align-items:center}.actions a{color:#8ca9b6;text-decoration:none;font-size:13px}.actions button{border:0;border-radius:11px;padding:12px 17px;background:#31afd2;color:#fff;font-weight:700}
        @media(max-width:900px){.app{grid-template-columns:78px 1fr}.sidebar{padding:18px 10px}.brand strong,.brand small,.nav span,.logout{font-size:0}.brand{padding:8px}.nav a{justify-content:center}.sidebar-footer{left:10px;right:10px}.grid{grid-template-columns:repeat(2,minmax(0,1fr))}}
        @media(max-width:620px){.app{display:block}.sidebar{position:fixed;z-index:20;left:0;right:0;bottom:0;top:auto;width:100%;height:64px;padding:7px 10px;border-right:0;border-top:1px solid var(--line);display:flex;align-items:center}.brand,.sidebar-footer{display:none}.nav{width:100%;display:grid;grid-template-columns:repeat(4,1fr);gap:3px}.nav a{height:50px;padding:5px;font-size:9px;display:flex;flex-direction:column;gap:3px}.nav span{font-size:9px}.main{padding-bottom:64px}.topbar{height:62px;padding:0 17px}.content{padding:22px 16px}.grid{grid-template-columns:1fr 1fr}.card{padding:16px}.card-value{font-size:21px}.fields{grid-template-columns:1fr}.full{grid-column:auto}}
        @media(max-width:380px){.grid{grid-template-columns:1fr}}
    </style>
    @stack('head')
    @stack('styles')
</head>
<body>
<div class="app">
    <aside class="sidebar">
        <div class="brand"><small>FUELFREE</small><strong>PowerPlant</strong></div>
        <nav class="nav" aria-label="Primary navigation">
            @if(auth()->user()->hasRole(['super-admin','administrator','project-manager','support-agent']))
                <a class="{{ request()->routeIs('admin.dashboard') ? 'active' : '' }}" href="{{ route('admin.dashboard') }}"><b>◈</b><span>Overview</span></a>
            @endif
            @if(auth()->user()->hasPermission('documents.view'))
                <a class="{{ request()->routeIs('admin.documents') ? 'active' : '' }}" href="{{ route('admin.documents') }}"><b>▣</b><span>Documents</span></a>
            @endif
            @if(auth()->user()->hasPermission('email.view'))
                <a class="{{ request()->routeIs('admin.email') ? 'active' : '' }}" href="{{ route('admin.email') }}"><b>✉</b><span>Email</span></a>
            @endif
            @if(auth()->user()->hasPermission('support.view'))
                <a class="{{ request()->routeIs('admin.support') ? 'active' : '' }}" href="{{ route('admin.support') }}"><b>?</b><span>Support</span></a>
            @endif
            @if(auth()->user()->hasRole('client'))
                <a class="{{ request()->routeIs('portal.dashboard') ? 'active' : '' }}" href="{{ route('portal.dashboard') }}"><b>◈</b><span>Overview</span></a>
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
<script>
(function () {
    const uploadUrl = @json(route('admin.documents.chunks'));
    const csrf = document.querySelector('meta[name="csrf-token"]')?.content || '';
    const CHUNK_SIZE = 524288;

    function jsonError(response, payload) {
        if (payload?.message) return payload.message;
        if (payload?.errors) return Object.values(payload.errors).flat().join(' ');
        return 'Upload failed. Please try again.';
    }

    document.addEventListener('DOMContentLoaded', function () {
        const input = document.querySelector('#upload-modal input[type="file"]');
        const form = input?.closest('form');
        if (!input || !form) return;

        const submit = form.querySelector('button.primary[type="submit"]');
        const note = form.querySelector('.modal-note');
        const progress = document.createElement('div');
        progress.style.cssText = 'display:none;margin-top:4px;padding:12px;border:1px solid rgba(104,204,235,.13);border-radius:12px;background:rgba(67,194,229,.05);font-size:12px;color:#86a5b4;line-height:1.7';
        progress.innerHTML = '<div style="display:flex;justify-content:space-between;gap:12px"><span class="upload-status">Preparing upload…</span><strong class="upload-percent">0%</strong></div><div style="height:7px;margin-top:8px;border-radius:99px;background:rgba(255,255,255,.08);overflow:hidden"><span class="upload-bar" style="display:block;height:100%;width:0;border-radius:99px;background:linear-gradient(90deg,#229fd0,#54d4ee)"></span></div>';
        form.insertBefore(progress, form.querySelector('.modal-actions'));

        form.addEventListener('submit', async function (event) {
            event.preventDefault();
            const file = input.files?.[0];
            if (!file) return;
            const folder = form.querySelector('input[name="folder_id"]')?.value || '';
            const status = progress.querySelector('.upload-status');
            const percent = progress.querySelector('.upload-percent');
            const bar = progress.querySelector('.upload-bar');
            progress.style.display = 'block';
            submit.disabled = true;
            submit.style.opacity = '.65';
            input.disabled = true;
            try {
                status.textContent = 'Preparing secure upload…';
                const startBody = new URLSearchParams({ filename: file.name, size: String(file.size), mime_type: file.type || 'application/octet-stream' });
                if (folder) startBody.set('folder_id', folder);
                const startResponse = await fetch(uploadUrl, { method: 'POST', credentials: 'same-origin', headers: { 'X-CSRF-TOKEN': csrf, 'X-Requested-With': 'XMLHttpRequest', 'Accept': 'application/json' }, body: startBody });
                const startPayload = await startResponse.json().catch(() => ({}));
                if (!startResponse.ok) throw new Error(jsonError(startResponse, startPayload));
                const uploadId = startPayload.upload_id;
                const chunkSize = Number(startPayload.chunk_size) || CHUNK_SIZE;
                const totalChunks = Math.ceil(file.size / chunkSize);

                for (let index = 0; index < totalChunks; index++) {
                    const offset = index * chunkSize;
                    const blob = file.slice(offset, Math.min(offset + chunkSize, file.size));
                    let uploaded = false;
                    for (let attempt = 1; attempt <= 3 && !uploaded; attempt++) {
                        status.textContent = `Uploading chunk ${index + 1} of ${totalChunks}…`;
                        const response = await fetch(uploadUrl, { method: 'POST', credentials: 'same-origin', headers: { 'X-CSRF-TOKEN': csrf, 'X-Requested-With': 'XMLHttpRequest', 'Accept': 'application/json', 'Content-Type': 'application/octet-stream', 'X-Upload-Id': uploadId, 'X-Chunk-Index': String(index), 'X-Chunk-Offset': String(offset) }, body: blob });
                        const payload = await response.json().catch(() => ({}));
                        if (response.ok) uploaded = true;
                        else if (attempt === 3) throw new Error(jsonError(response, payload));
                        else await new Promise(resolve => setTimeout(resolve, 600 * attempt));
                    }
                    const done = Math.min(offset + blob.size, file.size);
                    const value = Math.round((done / file.size) * 100);
                    percent.textContent = value + '%';
                    bar.style.width = value + '%';
                }

                status.textContent = 'Finalizing and securing file…';
                const finalResponse = await fetch(uploadUrl + '?finalize=1', { method: 'POST', credentials: 'same-origin', headers: { 'X-CSRF-TOKEN': csrf, 'X-Requested-With': 'XMLHttpRequest', 'Accept': 'application/json', 'X-Upload-Id': uploadId } });
                const finalPayload = await finalResponse.json().catch(() => ({}));
                if (!finalResponse.ok) throw new Error(jsonError(finalResponse, finalPayload));
                status.textContent = 'Upload complete.';
                percent.textContent = '100%';
                bar.style.width = '100%';
                setTimeout(() => window.location.reload(), 500);
            } catch (error) {
                status.textContent = error?.message || 'Upload failed. Please try again.';
                status.style.color = '#ffb7b7';
                bar.style.background = '#b14a5a';
                submit.disabled = false;
                submit.style.opacity = '1';
                input.disabled = false;
            }
        });
    });
})();
</script>
@stack('scripts')
</body>
</html>
