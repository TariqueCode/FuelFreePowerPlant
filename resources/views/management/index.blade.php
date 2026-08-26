@php
    $brand = \App\Models\SystemSetting::query()
        ->whereIn('key', ['company.name', 'company.logo_path'])
        ->pluck('value', 'key');

    $name = $brand->get('company.name') ?: config('fuelfree.company.name');
    $logo = $brand->get('company.logo_path');
@endphp
<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width,initial-scale=1">
    <title>Management — {{ $name }}</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.7.2/css/all.min.css">
    <style>
        :root{font-family:Inter,system-ui,sans-serif;color:#eaf8fb;background:#020b12;--line:rgba(86,210,238,.15);--muted:#8eabb7;--cyan:#43d1f0}
        *{box-sizing:border-box}
        body{margin:0;background:radial-gradient(circle at 70% 0,rgba(18,145,184,.18),transparent 32%),linear-gradient(180deg,#020a11,#03131d 60%,#020a11);min-height:100vh}
        .shell{width:min(1180px,calc(100% - 32px));margin:auto}
        .header{position:sticky;top:0;z-index:20;background:rgba(2,11,18,.88);backdrop-filter:blur(18px);border-bottom:1px solid var(--line)}
        .nav{min-height:72px;display:flex;align-items:center;justify-content:space-between;gap:18px}
        .brand{display:flex;align-items:center;gap:10px;color:#fff;text-decoration:none;min-width:0}
        .brand img{width:40px;height:40px;object-fit:contain;border-radius:10px}
        .brand strong{white-space:nowrap;overflow:hidden;text-overflow:ellipsis}
        .back{color:#9db7c0;text-decoration:none;font-size:11px;padding:10px 13px;border:1px solid var(--line);border-radius:11px}
        .back:hover{color:#fff;background:rgba(67,209,240,.08)}
        main{padding:60px 0}
        .hero{margin-bottom:32px}
        .eyebrow{font-size:10px;letter-spacing:.2em;color:var(--cyan);text-transform:uppercase}
        .hero h1{font-size:clamp(34px,6vw,60px);letter-spacing:-.04em;margin:12px 0}
        .hero p{color:var(--muted);max-width:650px;line-height:1.7;font-size:13px}
        .grid{display:grid;grid-template-columns:repeat(3,minmax(0,1fr));gap:18px}
        .card{overflow:hidden;border:1px solid var(--line);border-radius:20px;background:linear-gradient(145deg,rgba(8,38,52,.88),rgba(3,21,30,.88));box-shadow:0 20px 55px rgba(0,0,0,.2)}
        .photo{aspect-ratio:4/3;background:linear-gradient(135deg,#0a2c3b,#061720);overflow:hidden;display:grid;place-items:center}
        .photo img{width:100%;height:100%;object-fit:cover}
        .photo i{font-size:42px;color:#4fd5ef}
        .body{padding:19px}.body h2{font-size:19px;margin:0}.role{margin:7px 0 0;color:#5dc7df;font-size:11px;font-weight:700}.bio{margin-top:14px;color:#89a8b2;font-size:11px;line-height:1.7}.bio p{margin:0 0 8px}
        .empty{border:1px dashed var(--line);border-radius:18px;padding:55px 20px;text-align:center;color:var(--muted);grid-column:1/-1}.empty i{font-size:35px;color:#4aa9bd;margin-bottom:12px}
        .footer{border-top:1px solid var(--line);padding:28px 0 45px;color:#66838e;font-size:10px}
        @media(max-width:850px){.grid{grid-template-columns:repeat(2,minmax(0,1fr))}}
        @media(max-width:560px){.shell{width:calc(100% - 24px)}main{padding:40px 0}.grid{grid-template-columns:1fr}.back{font-size:10px}.brand strong{font-size:13px}}
    </style>
</head>
<body>
    <header class="header">
        <div class="shell nav">
            <a class="brand" href="{{ route('home') }}">
                @if($logo)
                    <img src="{{ asset('storage/'.$logo) }}" alt="{{ $name }}">
                @endif
                <strong>{{ $name }}</strong>
            </a>
            <a class="back" href="{{ route('home') }}"><i class="fa-solid fa-arrow-left"></i> Back to website</a>
        </div>
    </header>

    <main class="shell">
        <section class="hero">
            <span class="eyebrow">Leadership &amp; Management</span>
            <h1>Management Team</h1>
            <p>Meet the people responsible for guiding {{ $name }}.</p>
        </section>

        <section class="grid">
            @forelse($members as $member)
                <article class="card">
                    <div class="photo">
                        @if($member->image_path)
                            <img src="{{ asset('storage/'.$member->image_path) }}" alt="{{ $member->title }}" loading="lazy">
                        @else
                            <i class="fa-solid fa-user-tie" aria-hidden="true"></i>
                        @endif
                    </div>
                    <div class="body">
                        <h2>{{ $member->title }}</h2>
                        @if($member->excerpt)
                            <div class="role">{{ $member->excerpt }}</div>
                        @endif
                        @if($member->content)
                            <div class="bio">{!! $member->content !!}</div>
                        @endif
                    </div>
                </article>
            @empty
                <div class="empty">
                    <i class="fa-solid fa-people-group" aria-hidden="true"></i>
                    <div>No management profiles have been published yet.</div>
                </div>
            @endforelse
        </section>
    </main>

    <footer class="footer">
        <div class="shell">{{ $name }} · Management</div>
    </footer>
</body>
</html>
