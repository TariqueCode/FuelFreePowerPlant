@extends('layouts.public')

@php($siteName = $brand['name'] ?? config('fuelfree.company.name'))
@section('title', 'News & Notices — '.$siteName)
@section('content')
<style>
.news-page{--bg:#020a10;--surface:#071b25;--line:rgba(96,216,239,.16);--text:#edfaff;--muted:#8eaab4;--cyan:#51d8f0;--green:#64e0b2;width:min(1180px,calc(100% - 32px));margin:auto;padding:52px 0 90px;color:var(--text);font-family:Inter,system-ui,-apple-system,"Segoe UI",sans-serif}
.news-page *{box-sizing:border-box}.news-page a{text-decoration:none;color:inherit}.news-mast{border-bottom:1px solid var(--line);padding-bottom:27px;margin-bottom:23px}.news-kicker{display:flex;align-items:center;gap:12px;color:var(--cyan);font-size:9px;font-weight:800;letter-spacing:.22em;text-transform:uppercase}.news-kicker:after{content:"";height:1px;background:var(--line);flex:1}.news-mast h1{font-size:clamp(45px,6vw,82px);line-height:.94;letter-spacing:-.055em;margin:10px 0 14px}.news-mast p{max-width:720px;color:var(--muted);font-size:15px;line-height:1.85;margin:0}.news-tools{display:flex;gap:10px;margin:22px 0 30px;align-items:stretch}.search{flex:1;min-width:240px;height:48px;border:1px solid var(--line);border-radius:13px;background:rgba(7,27,37,.88);display:flex;align-items:center;padding:0 15px;gap:10px}.search i{color:var(--cyan)}.search input{width:100%;min-width:0;border:0;outline:0;background:transparent;color:var(--text);font:500 10px Inter,system-ui,sans-serif}.search input::placeholder{color:#718d98}.filter{height:48px;width:125px;padding:0 13px;border:1px solid var(--line);border-radius:13px;background:rgba(7,27,37,.88);display:flex;align-items:center;position:relative}.filter:after{content:"⌄";margin-left:auto;color:#86a7b1}.filter select{appearance:none;border:0;outline:0;background:transparent;color:var(--text);font:500 10px Inter,system-ui,sans-serif;width:100%;cursor:pointer}.filter select option{background:#071b25;color:#edfaff}.view-switch{display:flex;gap:4px;padding:4px;border:1px solid var(--line);border-radius:12px;background:rgba(7,27,37,.8);flex:0 0 auto}.view-btn{width:42px;height:38px;border:0;border-radius:8px;background:transparent;color:#75939e;cursor:pointer;display:grid;place-items:center}.view-btn.active,.view-btn:hover{background:rgba(79,210,238,.1);color:var(--cyan)}
.news-head{display:flex;align-items:end;justify-content:space-between;border-bottom:1px solid var(--line);padding-bottom:13px;margin-bottom:14px}.news-head h2{font-size:clamp(29px,4vw,48px);line-height:1.1;letter-spacing:-.04em;margin:5px 0 0}.news-head small{color:var(--cyan);font-size:9px;font-weight:800;letter-spacing:.16em;text-transform:uppercase}.news-grid{display:grid;grid-template-columns:repeat(2,minmax(0,1fr));gap:14px}.news-card{display:grid;grid-template-columns:220px 32px minmax(0,1fr);height:220px;overflow:hidden;border:1px solid var(--line);border-radius:18px;background:linear-gradient(145deg,rgba(8,37,50,.9),rgba(3,19,27,.94));transition:transform .25s,border-color .25s,box-shadow .25s}.news-card:hover{transform:translateY(-3px);border-color:rgba(72,216,241,.4);box-shadow:0 18px 45px rgba(0,0,0,.18)}.news-media{width:220px;height:220px;background:#061923;overflow:hidden}.news-media img{width:100%;height:100%;display:block;object-fit:cover;transition:transform .45s}.news-card:hover .news-media img{transform:scale(1.025)}.media-fallback{width:100%;height:100%;display:grid;place-items:center;color:var(--cyan);font-size:30px}.news-kind{display:flex;align-items:center;justify-content:center;border-left:1px solid rgba(72,216,241,.1);border-right:1px solid rgba(72,216,241,.1);color:var(--cyan);font-size:8px;font-weight:800;letter-spacing:.16em;writing-mode:vertical-rl;transform:rotate(180deg);text-transform:uppercase}.news-kind.notice{color:#f0c58e}.news-body{min-width:0;height:220px;padding:19px 19px 16px;display:flex;flex-direction:column}.news-title{margin:0;color:var(--text);font-size:18px;line-height:1.38;font-weight:780;display:-webkit-box;-webkit-line-clamp:4;-webkit-box-orient:vertical;overflow:hidden}.news-excerpt{color:var(--muted);font-size:10px;line-height:1.65;margin:8px 0 0;display:-webkit-box;-webkit-line-clamp:2;-webkit-box-orient:vertical;overflow:hidden}.news-footer{margin-top:auto;padding-top:12px;display:flex;align-items:center;justify-content:space-between;gap:10px}.news-date{color:#789aa5;font-size:9px}.read{color:var(--cyan);font-size:9px;font-weight:750;white-space:nowrap}.news-list{display:grid;gap:10px}.news-list .news-card{grid-template-columns:145px 28px minmax(0,1fr);height:145px;border-radius:15px}.news-list .news-media{width:145px;height:145px}.news-list .news-body{height:145px;padding:17px 20px}.news-list .news-title{font-size:16px;-webkit-line-clamp:2}.news-list .news-excerpt{display:none}.news-list .news-footer{padding-top:7px}.news-list .news-kind{font-size:7px}.pager{padding-top:25px}.empty{padding:60px 25px;border:1px dashed var(--line);border-radius:17px;color:var(--muted);background:rgba(7,27,37,.35);text-align:center}
@media(max-width:900px){.news-page{width:min(100% - 24px,700px);padding:38px 0 70px}.news-tools{display:grid;grid-template-columns:minmax(0,1fr) 105px 105px}.view-switch{display:none}.news-grid{grid-template-columns:1fr}.news-card{grid-template-columns:170px 28px minmax(0,1fr);height:170px}.news-media{width:170px;height:170px}.news-body{height:170px}.news-list .news-card{grid-template-columns:120px 24px minmax(0,1fr);height:120px}.news-list .news-media{width:120px;height:120px}.news-list .news-body{height:120px}}
@media(max-width:560px){.news-page{width:calc(100% - 24px);padding:30px 0 60px}.news-mast{padding-bottom:20px;margin-bottom:18px}.news-mast h1{font-size:44px}.news-mast p{font-size:13px}.news-tools{grid-template-columns:minmax(0,1fr) 62px 62px;gap:6px;margin-bottom:22px}.search,.filter{height:45px;border-radius:11px}.search{padding:0 10px;gap:7px}.filter{padding:0 8px}.filter select{font-size:10px}.news-head h2{font-size:29px}.news-head small{font-size:8px}.news-card{grid-template-columns:112px 24px minmax(0,1fr);height:112px}.news-media{width:112px;height:112px}.news-body{height:112px;padding:11px 13px}.news-title{font-size:14px;-webkit-line-clamp:2}.news-excerpt{display:none}.news-footer{padding-top:5px}.news-date,.read{font-size:8px}.news-list .news-card{grid-template-columns:100px 22px minmax(0,1fr);height:100px}.news-list .news-media{width:100px;height:100px}.news-list .news-body{height:100px;padding:9px 11px}.news-list .news-title{font-size:13px}}
</style>

<main class="news-page">
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
