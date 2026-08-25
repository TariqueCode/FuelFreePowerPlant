<!doctype html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width,initial-scale=1">
    <meta name="description" content="{{ $brand['tagline'] }}">
    <title>{{ $brand['name'] }}</title>
    <style>
        :root{font-family:Inter,ui-sans-serif,system-ui,sans-serif;color-scheme:dark;--line:rgba(110,220,240,.14);--text:#edfaff;--muted:#8caab7;--accent:#45c8e8;--accent2:#20d4aa}
        *{box-sizing:border-box}html{scroll-behavior:smooth}body{margin:0;background:radial-gradient(circle at 80% -10%,rgba(54,188,220,.18),transparent 34%),linear-gradient(180deg,#031018,#061923 48%,#031018);color:var(--text)}a{color:inherit;text-decoration:none}.shell{width:min(1180px,calc(100% - 32px));margin:auto}.top{position:sticky;top:0;z-index:20;border-bottom:1px solid var(--line);background:rgba(3,16,24,.8);backdrop-filter:blur(20px)}.bar{min-height:70px;display:flex;align-items:center;justify-content:space-between;gap:18px}.brand{display:flex;align-items:center;gap:10px;font-weight:800;min-width:0}.brand img{width:38px;height:38px;object-fit:contain;border-radius:10px}.brand-text{min-width:0}.brand small{display:block;color:var(--accent);font-size:8px;letter-spacing:.25em;text-transform:uppercase}.brand-name{display:block;white-space:nowrap;overflow:hidden;text-overflow:ellipsis;max-width:260px}.links{display:flex;gap:8px;align-items:center;overflow:auto;scrollbar-width:none}.links::-webkit-scrollbar{display:none}.links a{padding:9px 11px;border-radius:10px;color:var(--muted);font-size:12px;white-space:nowrap}.links a:hover{color:var(--text);background:rgba(69,200,232,.08)}.hero{padding:82px 0 55px;display:grid;grid-template-columns:1.25fr .75fr;gap:42px;align-items:center}.eyebrow{font-size:10px;letter-spacing:.22em;color:var(--accent);text-transform:uppercase}.hero h1{font-size:clamp(42px,7vw,78px);line-height:.96;margin:12px 0 20px;letter-spacing:-.045em}.hero p{max-width:680px;color:var(--muted);font-size:17px;line-height:1.75}.actions{display:flex;flex-wrap:wrap;gap:10px;margin-top:28px}.btn{display:inline-flex;align-items:center;gap:9px;padding:12px 16px;border-radius:12px;background:linear-gradient(135deg,#39bddd,#24a6cb);font-weight:750;font-size:13px}.btn.secondary{background:rgba(255,255,255,.035);border:1px solid var(--line);color:#b9d2db}.energy-stage{min-height:330px;border:1px solid var(--line);border-radius:30px;background:radial-gradient(circle at 50% 40%,rgba(69,200,232,.18),transparent 38%),linear-gradient(145deg,rgba(10,45,59,.9),rgba(5,24,33,.8));display:grid;place-items:center;position:relative;overflow:hidden}.energy-stage:before,.energy-stage:after{content:"";position:absolute;border:1px solid rgba(69,200,232,.16);border-radius:50%;animation:orbit 8s linear infinite}.energy-stage:before{width:220px;height:220px}.energy-stage:after{width:310px;height:310px;animation-duration:13s;animation-direction:reverse}.energy-core{position:relative;width:105px;height:105px;border-radius:50%;display:grid;place-items:center;background:radial-gradient(circle,#d9fbff 0 5%,#45c8e8 22%,rgba(69,200,232,.08) 62%,transparent 70%);box-shadow:0 0 55px rgba(69,200,232,.38);animation:core 3s ease-in-out infinite}.energy-core i{font-size:42px;color:#e9fdff;filter:drop-shadow(0 0 14px rgba(69,200,232,.8))}@keyframes core{50%{transform:scale(1.08);box-shadow:0 0 85px rgba(69,200,232,.55)}}@keyframes orbit{to{transform:rotate(360deg)}}section{padding:55px 0}.section-head{display:flex;align-items:end;justify-content:space-between;gap:20px;margin-bottom:20px}.section-head h2{font-size:28px;margin:6px 0}.section-head p{color:var(--muted);margin:0;max-width:600px;line-height:1.6}.stats{display:grid;grid-template-columns:repeat(3,1fr);gap:13px}.stat,.plant,.content-card{border:1px solid var(--line);border-radius:18px;background:linear-gradient(145deg,rgba(9,39,52,.86),rgba(5,24,33,.82))}.stat{padding:21px}.stat-label{color:#789aa8;text-transform:uppercase;letter-spacing:.1em;font-size:10px}.stat-value{font-size:32px;font-weight:800;margin-top:10px}.plant-grid,.content-grid{display:grid;grid-template-columns:repeat(3,1fr);gap:14px}.plant,.content-card{padding:20px;min-height:180px}.plant-top{display:flex;justify-content:space-between;gap:10px}.plant h3,.content-card h3{margin:8px 0;font-size:18px}.pill{padding:5px 8px;border-radius:999px;font-size:9px;text-transform:uppercase;letter-spacing:.08em;background:rgba(32,212,170,.1);border:1px solid rgba(32,212,170,.18);color:#6ee8c9;height:max-content}.plant p,.content-card p{color:var(--muted);font-size:12px;line-height:1.6}.meta{display:flex;gap:8px;flex-wrap:wrap;color:#9cc0cb;font-size:10px}.content-card .type{color:var(--accent);font-size:9px;text-transform:uppercase;letter-spacing:.12em}.content-card time{display:block;color:#678895;font-size:9px;margin-top:12px}.cms{border:1px solid var(--line);border-radius:20px;padding:28px;background:rgba(255,255,255,.025);line-height:1.8;color:#a9c1ca}.cms h1,.cms h2,.cms h3{color:var(--text)}footer{border-top:1px solid var(--line);padding:35px 0 60px;color:#6f929f;font-size:11px}.empty{padding:28px;border:1px dashed var(--line);border-radius:16px;color:var(--muted)}@media(max-width:780px){.bar{min-height:62px}.links{max-width:55vw}.hero{grid-template-columns:1fr;padding-top:55px}.energy-stage{min-height:230px}.stats{grid-template-columns:1fr 1fr}.plant-grid,.content-grid{grid-template-columns:1fr 1fr}}@media(max-width:520px){.shell{width:min(100% - 24px,1180px)}.brand-name{max-width:150px;font-size:13px}.links a{font-size:10px;padding:8px}.stats,.plant-grid,.content-grid{grid-template-columns:1fr}.stat-value{font-size:28px}.hero p{font-size:14px}.energy-stage{min-height:190px}.energy-core{width:82px;height:82px}.energy-core i{font-size:32px}section{padding:40px 0}}
    </style>
</head>
<body>
<header class="top">
    <div class="shell bar">
        <a class="brand" href="{{ route('home') }}">
            @if(!empty($brand['logo_path']))
                <img src="{{ asset('storage/'.$brand['logo_path']) }}" alt="{{ $brand['name'] }}">
            @endif
            <div class="brand-text"><small>FUELFREE</small><span class="brand-name">{{ $brand['name'] }}</span></div>
        </a>
        <nav class="links" aria-label="Website navigation">
            <a href="#about">About</a>
            <a href="#projects">Projects</a>
            @foreach($pages as $page)
                <a href="{{ route('cms.page',$page->slug) }}">{{ $page->title }}</a>
            @endforeach
            @auth
                <a href="{{ route('dashboard') }}">Dashboard</a>
            @else
                <a href="{{ route('login') }}">Portal</a>
            @endauth
        </nav>
    </div>
</header>
<main>
    <section class="shell hero">
        <div>
            <div class="eyebrow">Power infrastructure &amp; energy</div>
            <h1>{{ $homePage?->title ?? $brand['name'] }}</h1>
            <p>{{ $homePage?->excerpt ?? $brand['tagline'] }}</p>
            <div class="actions">
                <a class="btn" href="#projects"><i class="fa-solid fa-bolt"></i> Explore projects</a>
                <a class="btn secondary" href="#about"><i class="fa-solid fa-building"></i> Discover the company</a>
            </div>
        </div>
        <div class="energy-stage" aria-hidden="true"><div class="energy-core"><i class="fa-solid fa-bolt"></i></div></div>
    </section>

    <section id="about">
        <div class="shell">
            <div class="section-head"><div><div class="eyebrow">At a glance</div><h2>Built around dependable energy</h2></div><p>{{ $brand['tagline'] }}</p></div>
            <div class="stats">
                <article class="stat"><div class="stat-label">Projects</div><div class="stat-value">{{ $stats['projects'] }}</div></article>
                <article class="stat"><div class="stat-label">Installed capacity</div><div class="stat-value">{{ number_format($stats['capacity_mw'],2) }} <small>MW</small></div></article>
                <article class="stat"><div class="stat-label">Operational</div><div class="stat-value">{{ $stats['operational'] }}</div></article>
            </div>
        </div>
    </section>

    @php($companyContent = $content->get('company', collect()))
    @if($companyContent->isNotEmpty())
        <section>
            <div class="shell">
                <div class="section-head"><div><div class="eyebrow">Company</div><h2>Who we are</h2></div></div>
                <div class="content-grid">
                    @foreach($companyContent as $item)
                        <article class="content-card"><span class="type">Company</span><h3>{{ $item->title }}</h3><p>{{ $item->excerpt ?: \Illuminate\Support\Str::limit(strip_tags($item->content),180) }}</p></article>
                    @endforeach
                </div>
            </div>
        </section>
    @endif

    <section id="projects">
        <div class="shell">
            <div class="section-head"><div><div class="eyebrow">Power portfolio</div><h2>Projects &amp; power plants</h2></div><p>Project records entered through the admin control center appear here automatically.</p></div>
            @if($plants->isEmpty())
                <div class="empty">No project records have been published yet.</div>
            @else
                <div class="plant-grid">
                    @foreach($plants as $plant)
                        <article class="plant"><div class="plant-top"><div><div class="eyebrow">{{ $plant->technology ?: 'Energy project' }}</div><h3>{{ $plant->name }}</h3></div><span class="pill">{{ $plant->status ?: 'Planned' }}</span></div><p>{{ $plant->overview ?: 'Project information will be published here as the record is completed.' }}</p><div class="meta"><span>{{ $plant->location ?: 'Location pending' }}</span><span>•</span><span>{{ number_format((float)$plant->capacity_kw / 1000,2) }} MW</span></div></article>
                    @endforeach
                </div>
            @endif
        </div>
    </section>

    @php($newsContent = $content->get('news', collect()))
    @if($newsContent->isNotEmpty())
        <section>
            <div class="shell">
                <div class="section-head"><div><div class="eyebrow">Latest updates</div><h2>News &amp; announcements</h2></div><p>Published updates managed directly from the control center.</p></div>
                <div class="content-grid">
                    @foreach($newsContent->take(6) as $item)
                        <article class="content-card"><span class="type">News</span><h3>{{ $item->title }}</h3><p>{{ $item->excerpt ?: \Illuminate\Support\Str::limit(strip_tags($item->content),180) }}</p>@if($item->published_at)<time>{{ $item->published_at->format('d M Y') }}</time>@endif</article>
                    @endforeach
                </div>
            </div>
        </section>
    @endif

    @if($homePage && $homePage->content)
        <section><div class="shell"><div class="cms">{!! $homePage->content !!}</div></div></section>
    @endif
</main>
<footer><div class="shell">© {{ date('Y') }} {{ $brand['name'] }} · {{ $brand['domain'] }}</div></footer>
</body>
</html>
