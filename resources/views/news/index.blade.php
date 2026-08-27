@extends('layouts.public')

@php($siteName = $brand['name'] ?? config('fuelfree.company.name'))
@section('title', 'News & Notices — '.$siteName)
@section('content')
<style>
.news-page{--line:var(--public-line);--text:var(--public-text);--muted:var(--public-muted);--accent:var(--public-accent);width:min(var(--public-max),calc(100% - (var(--public-gutter) * 2)));margin-inline:auto;padding:var(--public-space-7) 0 72px;color:var(--text)}
.news-page *{box-sizing:border-box}.news-page a{text-decoration:none;color:inherit}
.news-mast{border-bottom:1px solid var(--line);padding-bottom:var(--public-space-5);margin-bottom:var(--public-space-5)}
.news-kicker{display:flex;align-items:center;gap:var(--public-space-3);color:var(--accent);font-size:11px;font-weight:800;letter-spacing:.2em;text-transform:uppercase}
.news-kicker:after{content:"";height:1px;background:var(--line);flex:1}
.news-mast h1{font-size:clamp(42px,6vw,76px);line-height:.96;letter-spacing:-.055em;margin:10px 0 16px}
.news-mast p{max-width:720px;color:var(--muted);font-size:16px;line-height:1.7;margin:0}
.news-tools{display:grid;grid-template-columns:minmax(0,1fr) 140px 140px auto;gap:10px;margin:0 0 34px}
.search,.filter{height:48px;border:1px solid var(--line);border-radius:12px;background:rgba(7,27,37,.78)}
.search{display:flex;align-items:center;padding:0 15px;gap:10px}.search i{color:var(--accent)}
.search input{width:100%;min-width:0;border:0;outline:0;background:transparent;color:var(--text);font:500 14px Inter,system-ui,sans-serif}.search input::placeholder{color:#718d98}
.filter{position:relative;display:flex;align-items:center;padding:0 13px}.filter:after{content:"⌄";margin-left:auto;color:#86a7b1;pointer-events:none}
.filter select{appearance:none;border:0;outline:0;background:transparent;color:var(--text);font:500 14px Inter,system-ui,sans-serif;width:100%;cursor:pointer}.filter select option{background:#071b25;color:var(--text)}
.view-switch{display:flex;align-items:center;gap:4px;padding:4px;border:1px solid var(--line);border-radius:12px;background:rgba(7,27,37,.8)}
.view-btn{width:40px;height:38px;border:0;border-radius:8px;background:transparent;color:#75939e;cursor:pointer;display:grid;place-items:center}.view-btn.active,.view-btn:hover{background:rgba(79,210,238,.1);color:var(--accent)}
.news-head{display:flex;align-items:end;justify-content:space-between;gap:20px;border-bottom:1px solid var(--line);padding-bottom:14px;margin-bottom:16px}
.news-head h2{font-size:clamp(30px,4vw,46px);line-height:1.05;letter-spacing:-.04em;margin:6px 0 0}
.news-head small{color:var(--accent);font-size:11px;font-weight:800;letter-spacing:.16em;text-transform:uppercase}
.news-grid{display:grid;grid-template-columns:repeat(2,minmax(0,1fr));gap:16px}
.news-card{min-width:0;display:grid;grid-template-columns:220px 32px minmax(0,1fr);min-height:220px;overflow:hidden;border:1px solid var(--line);border-radius:var(--public-radius);background:linear-gradient(145deg,rgba(8,37,50,.9),rgba(3,19,27,.94));transition:transform .2s ease,border-color .2s ease,box-shadow .2s ease}
.news-card:hover{transform:translateY(-2px);border-color:rgba(72,216,241,.38);box-shadow:0 16px 36px rgba(0,0,0,.16)}
.news-media{width:220px;height:220px;background:#061923;overflow:hidden}.news-media img{width:100%;height:100%;display:block;object-fit:cover;transition:transform .35s ease}.news-card:hover .news-media img{transform:scale(1.025)}
.media-fallback{width:100%;height:100%;display:grid;place-items:center;color:var(--accent);font-size:30px}
.news-kind{display:flex;align-items:center;justify-content:center;border-inline:1px solid rgba(72,216,241,.1);color:var(--accent);font-size:8px;font-weight:800;letter-spacing:.16em;writing-mode:vertical-rl;transform:rotate(180deg);text-transform:uppercase}.news-kind.notice{color:#f0c58e}
.news-body{min-width:0;padding:20px;display:flex;flex-direction:column}
.news-title{margin:0;color:var(--text);font-size:18px;line-height:1.4;font-weight:780;display:-webkit-box;-webkit-line-clamp:3;-webkit-box-orient:vertical;overflow:hidden}
.news-excerpt{color:var(--muted);font-size:13px;line-height:1.65;margin:9px 0 0;display:-webkit-box;-webkit-line-clamp:3;-webkit-box-orient:vertical;overflow:hidden}
.news-footer{margin-top:auto;padding-top:16px;display:flex;align-items:center;justify-content:space-between;gap:10px}.news-date{color:#789aa5;font-size:11px}.read{color:var(--accent);font-size:11px;font-weight:750;white-space:nowrap}
.news-list{display:grid;gap:12px}.news-list .news-card{grid-template-columns:150px 26px minmax(0,1fr);min-height:150px}.news-list .news-media{width:150px;height:150px}.news-list .news-body{padding:17px 19px}.news-list .news-title{font-size:17px;-webkit-line-clamp:2}.news-list .news-excerpt{display:none}.news-list .news-footer{padding-top:8px}.news-list .news-kind{font-size:7px}
.pager{padding-top:28px}.empty{padding:56px 24px;border:1px dashed var(--line);border-radius:var(--public-radius);color:var(--muted);background:rgba(7,27,37,.35);text-align:center}
@media(max-width:1024px){.news-page{padding-top:40px}.news-grid{grid-template-columns:1fr}.news-tools{grid-template-columns:minmax(0,1fr) 1fr 1fr}.view-switch{display:none}.news-card{grid-template-columns:180px 28px minmax(0,1fr);min-height:180px}.news-media{width:180px;height:180px}}
@media(max-width:640px){.news-page{width:calc(100% - 24px);padding:30px 0 54px}.news-mast{padding-bottom:20px;margin-bottom:18px}.news-kicker{font-size:9px}.news-mast h1{font-size:clamp(38px,11vw,48px);margin:9px 0 13px}.news-mast p{font-size:14px;line-height:1.65}.news-tools{grid-template-columns:1fr 1fr;gap:8px;margin-bottom:25px}.search{grid-column:1/-1;height:46px}.filter{height:46px}.filter select{font-size:13px}.news-head{align-items:end;margin-bottom:13px;padding-bottom:12px}.news-head h2{font-size:28px}.news-head small{font-size:9px}.news-card,.news-list .news-card{display:flex;flex-direction:column;min-height:0;height:auto;border-radius:15px}.news-media,.news-list .news-media{width:100%;height:auto;aspect-ratio:16/9}.news-kind,.news-list .news-kind{writing-mode:horizontal-tb;transform:none;border:0;border-bottom:1px solid rgba(72,216,241,.1);justify-content:flex-start;padding:8px 13px;font-size:8px}.news-body,.news-list .news-body{height:auto;padding:14px}.news-title,.news-list .news-title{font-size:17px;line-height:1.4;-webkit-line-clamp:3}.news-excerpt{display:-webkit-box;font-size:13px;line-height:1.6;margin-top:8px;-webkit-line-clamp:3}.news-footer,.news-list .news-footer{padding-top:14px}.news-date,.read{font-size:10px}}
@media(max-width:380px){.news-page{width:calc(100% - 20px)}.news-tools{gap:6px}.news-head h2{font-size:26px}.news-head small{font-size:8px}.news-body,.news-list .news-body{padding:12px}.news-title,.news-list .news-title{font-size:16px}.news-excerpt{font-size:12px}}
</style>

<main class="news-page public-container">
    <header class="news-mast">
        <div class="news-kicker">Latest updates</div>
        <h1>News &amp; Notices</h1>
        <p>Stay informed with the latest announcements, achievements and important updates from {{ $siteName }}.</p>
    </header>

    <div class="news-tools">
        <form class="search" method="GET" action="{{ route('news.index') }}">
            <i class="fa-solid fa-magnifying-glass" aria-hidden="true"></i>
            <input type="search" name="q" value="{{ request('q') }}" placeholder="Search news..." aria-label="Search news">
        </form>
        <label class="filter"><select name="type" onchange="this.form.submit()" form="news-filter"><option value="">All</option><option value="news" @selected(request('type')==='news')>News</option><option value="announcement" @selected(request('type')==='announcement')>Notice</option></select></label>
        <label class="filter"><select name="sort" onchange="this.form.submit()" form="news-filter"><option value="newest" @selected(request('sort','newest')==='newest')>Newest</option><option value="oldest" @selected(request('sort')==='oldest')>Oldest</option></select></label>
        <div class="view-switch" aria-label="News view">
            <button class="view-btn active" type="button" data-view="grid" title="Grid view" aria-label="Grid view"><i class="fa-solid fa-grip"></i></button>
            <button class="view-btn" type="button" data-view="list" title="List view" aria-label="List view"><i class="fa-solid fa-list"></i></button>
        </div>
    </div>
    <form id="news-filter" method="GET" action="{{ route('news.index') }}"><input type="hidden" name="q" value="{{ request('q') }}"></form>

    <section>
        <div class="news-head"><div><small>From the newsroom</small><h2>Latest publications</h2></div><small>{{ $news->total() }} stories</small></div>
        @if($news->isEmpty())
            <div class="empty">No published news or notices are available yet.</div>
        @else
            <div id="newsItems" class="news-grid">
                @foreach($news as $item)
                    <a class="news-card" href="{{ route('news.show',$item->slug) }}">
                        <div class="news-media">
                            @if($item->image_path)<img src="{{ asset('storage/'.ltrim($item->image_path,'/')) }}" alt="{{ $item->cover_alt ?: $item->title }}" loading="lazy">@else<div class="media-fallback"><i class="fa-regular fa-newspaper"></i></div>@endif
                        </div>
                        <div class="news-kind {{ $item->type==='announcement'?'notice':'' }}">{{ $item->type==='announcement'?'Notice':'News' }}</div>
                        <div class="news-body">
                            <h3 class="news-title">{{ $item->title }}</h3>
                            @if($item->excerpt)<p class="news-excerpt">{{ $item->excerpt }}</p>@endif
                            <div class="news-footer"><span class="news-date">{{ $item->published_at?->format('d F Y') }}</span><span class="read">Read more →</span></div>
                        </div>
                    </a>
                @endforeach
            </div>
            <div class="pager">{{ $news->links() }}</div>
        @endif
    </section>
</main>

<script>
(() => {
    const items = document.getElementById('newsItems');
    const buttons = document.querySelectorAll('.view-btn');
    if (!items || !buttons.length) return;
    const saved = localStorage.getItem('news-view');
    const setView = view => {
        items.classList.toggle('news-list', view === 'list');
        items.classList.toggle('news-grid', view !== 'list');
        buttons.forEach(btn => btn.classList.toggle('active', btn.dataset.view === view));
        localStorage.setItem('news-view', view);
    };
    setView(saved === 'list' ? 'list' : 'grid');
    buttons.forEach(btn => btn.addEventListener('click', () => setView(btn.dataset.view)));
})();
</script>
@endsection
