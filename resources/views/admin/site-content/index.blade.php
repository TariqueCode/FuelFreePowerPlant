@extends('layouts.portal')
@section('title',$title)
@section('content')
<section class="hero"><div><span class="eyebrow">CONTENT MANAGEMENT</span><h1>{{ $title }}</h1><p>{{ $type==='company' ? 'Manage company pages and corporate content in one place. Use Navigation / Menu Builder to control the public menu.' : ($type==='news' ? 'Publish news, notices and announcements with cover photos, featured placement and a professional content workflow.' : ($type==='resource' ? 'Manage public-safe resources, official documents and downloadable knowledge content.' : 'Manage structured public website content.')) }}</p></div>@if($type!=='news' && auth()->user()->hasPermission('website.manage'))<a class="primary" href="{{ route('admin.site-content.create', $type ? ['type'=>$type] : []) }}"><i class="fa-solid fa-plus"></i> {{ $type==='company'?'Add company content':'New content' }}</a>@endif</section>
@if(session('status'))<div class="notice">{{ session('status') }}</div>@endif
@if(!$type)
<div class="workspace"><a href="{{ route('admin.site-content.index',['type'=>'company']) }}"><i class="fa-solid fa-building"></i><strong>Company CMS</strong><span>About Us and corporate information</span></a><a href="{{ route('admin.site-content.index',['type'=>'news']) }}"><i class="fa-solid fa-newspaper"></i><strong>News &amp; Notices CMS</strong><span>News, notices and announcements</span></a><a href="{{ route('admin.site-content.index',['type'=>'resource']) }}"><i class="fa-solid fa-file-arrow-down"></i><strong>Resources CMS</strong><span>Public-safe documents and downloads</span></a></div>
@else
<div class="back-row"><a href="{{ route('admin.site-content.index') }}"><i class="fa-solid fa-arrow-left"></i> All content</a><span>{{ $labels[$type] ?? ucfirst($type) }}</span></div>
@endif
@if($type==='news')
<div class="news-control-row">
    @if(auth()->user()->hasPermission('website.manage'))<a class="primary compact-primary" href="{{ route('admin.site-content.create',['type'=>'news']) }}"><i class="fa-solid fa-plus"></i><span>New news / notice</span></a>@endif
    <form class="news-toolbar" method="GET" action="{{ route('admin.site-content.index') }}">
        <input type="hidden" name="type" value="news">
        <div class="search"><i class="fa-solid fa-magnifying-glass"></i><input id="news-search" name="q" value="{{ request('q') }}" placeholder="Search news &amp; notices..." autocomplete="off"></div>
        <select name="filter" title="Filter"><option value="">All</option><option value="news" @selected(request('filter')==='news')>News</option><option value="announcement" @selected(request('filter')==='announcement')>Notice</option><option value="published" @selected(request('filter')==='published')>Published</option><option value="draft" @selected(request('filter')==='draft')>Draft</option><option value="featured" @selected(request('filter')==='featured')>Featured</option></select>
        <select name="sort" title="Sort"><option value="" @selected(!request('sort'))>Newest</option><option value="oldest" @selected(request('sort')==='oldest')>Oldest</option><option value="updated" @selected(request('sort')==='updated')>Updated</option></select>
        <button type="submit" class="filter-btn" title="Apply filters" aria-label="Apply filters"><i class="fa-solid fa-sliders"></i><span>Apply</span></button>
    </form>
    <div class="publish-count"><strong>{{ $publishedCount }}</strong><span> published</span></div>
</div>
@elseif($type==='resource')
<div class="resource-control-row">
    <a class="compact-primary" href="{{ route('admin.site-content.create',['type'=>'resource']) }}"><i class="fa-solid fa-plus"></i> <span>New resource</span></a>
    <div class="publish-count"><strong>{{ $publishedCount }}</strong><span> published</span></div>
    <form class="resource-toolbar" method="GET" action="{{ route('admin.site-content.index') }}">
        <input type="hidden" name="type" value="resource">
        <label class="search"><i class="fa-solid fa-magnifying-glass"></i><input name="q" value="{{ request('q') }}" placeholder="Search resources..."></label>
        <select name="filter"><option value="">All</option><option value="published" @selected(request('filter')==='published')>Published</option><option value="draft" @selected(request('filter')==='draft')>Drafts</option><option value="featured" @selected(request('filter')==='featured')>Featured</option></select>
        <select name="sort"><option value="">Newest</option><option value="oldest" @selected(request('sort')==='oldest')>Oldest</option><option value="updated" @selected(request('sort')==='updated')>Updated</option></select>
        <button type="submit" class="filter-btn" title="Apply filters" aria-label="Apply filters"><i class="fa-solid fa-sliders"></i><span>Apply</span></button>
    </form>
</div>
<div class="resource-view-tools" role="group" aria-label="Resource view">
    <span>View</span><button type="button" class="view-toggle active" data-view="list" aria-label="List view"><i class="fa-solid fa-list"></i></button><button type="button" class="view-toggle" data-view="card" aria-label="Card view"><i class="fa-solid fa-grip"></i></button>
</div>
@elseif($type==='company')
<div class="toolbar"><div><strong>{{ $items->total() }}</strong><span> company pages</span></div><span class="hint"><i class="fa-solid fa-circle-info"></i> Public menu order is managed in Navigation Builder</span></div>
@endif
<div class="content-list {{ $type==='company' ? 'company-list' : '' }} {{ $type==='news' ? 'news-list' : ($type==='resource' ? 'resource-list' : '') }}">
@forelse($items as $item)
<article class="content-card {{ $type==='company' ? 'company-card' : '' }} {{ $type==='news' ? 'news-card' : '' }}" data-edit-url="{{ route('admin.site-content.edit',$item) }}">
    @if($type==='company')
        <div class="handle" title="Company content"><i class="fa-solid fa-building"></i></div>
        <div class="content-icon"><i class="fa-regular fa-file-lines"></i></div>
    @elseif($type==='news')
        <div class="news-cover">@if($item->image_path)<img src="{{ asset('storage/'.$item->image_path) }}" alt="{{ $item->cover_alt ?: $item->title }}" loading="lazy">@else<i class="fa-regular fa-newspaper"></i>@endif</div>
        <div class="news-kind {{ $item->type==='announcement'?'notice':'' }}">{{ $item->type==='announcement'?'Notice':($item->type==='resource'?'Resource':'News') }}</div>
    @endif
    <div class="info">
        <div class="name">{{ $item->title }}</div>
        @if($type==='news' && $item->is_featured)<div class="featured"><i class="fa-solid fa-star"></i> Featured</div>@endif
        <div class="slug">/{{ $item->slug }}</div>
        @if(in_array($type,['news','resource'],true) && $item->excerpt)<div class="excerpt">{{ \Illuminate\Support\Str::limit($item->excerpt,150) }}</div>@endif
        <div class="meta"><span class="status {{ $item->status }}">{{ ucfirst($item->status) }}</span><span class="date">{{ ($item->published_at ?? $item->updated_at)?->format('d M Y') }}</span></div>
    </div>
    @if($type==='news')
        <div class="actions news-actions">
            @if(auth()->user()->hasPermission('website.publish'))
                <form method="POST" action="{{ route('admin.site-content.news.toggle',$item) }}">
                    @csrf @method('PATCH')
                    <button type="submit" class="news-switch {{ $item->status === 'published' ? 'on' : 'off' }}" title="{{ $item->status === 'published' ? 'Deactivate' : 'Activate' }}" aria-label="{{ $item->status === 'published' ? 'Deactivate' : 'Activate' }}">
                        <span class="switch-track"><span class="switch-knob"></span></span>
                    </button>
                </form>
            @endif
            @if(auth()->user()->hasPermission('website.manage'))
                <form method="POST" action="{{ route('admin.site-content.destroy',$item) }}" onsubmit="return confirm('Delete this publication?')">
                    @csrf @method('DELETE')
                    <button type="submit" class="news-delete" title="Delete" aria-label="Delete"><i class="fa-solid fa-trash-can"></i></button>
                </form>
            @endif
        </div>
    @else
        @if($type==='resource')
            <div class="actions resource-actions">
                @if(auth()->user()->hasPermission('website.publish'))
                    <form method="POST" action="{{ route('admin.site-content.page.toggle',$item) }}">@csrf @method('PATCH')
                        <button type="submit" class="resource-switch {{ $item->status==='published'?'on':'off' }}" title="{{ $item->status==='published'?'Deactivate':'Activate' }}" aria-label="{{ $item->status==='published'?'Deactivate':'Activate' }}"><span class="switch-track"><span class="switch-knob"></span></span></button>
                    </form>
                @endif
                @if(auth()->user()->hasPermission('website.manage'))
                    <form method="POST" action="{{ route('admin.site-content.resource.duplicate',$item) }}">@csrf
                        <button type="submit" class="resource-action" title="Duplicate draft" aria-label="Duplicate draft"><i class="fa-regular fa-copy"></i></button>
                    </form>
                    <form method="POST" action="{{ route('admin.site-content.destroy',$item) }}" onsubmit="return confirm('Delete this resource?')">@csrf @method('DELETE')
                        <button type="submit" class="resource-delete" title="Delete" aria-label="Delete"><i class="fa-solid fa-trash"></i></button>
                    </form>
                @endif
            </div>
        @elseif($type!=='news')
        @if(auth()->user()->hasPermission('website.manage'))
            <div class="actions">
                <form method="POST" action="{{ route('admin.site-content.destroy',$item) }}" onsubmit="return confirm('Delete this {{ $type === 'news' ? 'publication' : 'content' }}?')">
                    @csrf @method('DELETE')
                    <button type="submit" title="Delete" aria-label="Delete"><i class="fa-solid fa-trash"></i></button>
                </form>
            </div>
        @endif
        @endif
    @endif
</article>
@empty<div class="empty"><i class="fa-regular fa-newspaper"></i><strong>No {{ $type==='news' ? 'news or notices' : ($type==='resource' ? 'resources' : 'content') }} yet</strong><span>Use the button above to create the first entry.</span></div>@endforelse
</div>
@if($items->hasPages()){{ $items->links() }}@endif
@endsection
@push('styles')<style>
.hero{display:flex;align-items:flex-end;justify-content:space-between;gap:24px;margin-bottom:22px}.eyebrow{font-size:9px;letter-spacing:.18em;color:#54cde8}.hero h1{margin:6px 0;font-size:clamp(28px,3.2vw,44px);line-height:1.08}.hero p{margin:0;color:#7898a5;font-size:12px;max-width:780px;line-height:1.65}.primary{display:inline-flex;align-items:center;justify-content:center;gap:8px;border:0;border-radius:12px;padding:11px 16px;background:linear-gradient(135deg,#25abc9,#1687a4);color:#fff;text-decoration:none;font-size:10px;font-weight:800;white-space:nowrap;box-shadow:0 8px 24px rgba(16,137,165,.18)}
.workspace{display:grid;grid-template-columns:repeat(2,minmax(0,1fr));gap:14px;margin-bottom:18px}.workspace a{padding:22px;border:1px solid var(--line);border-radius:17px;background:linear-gradient(145deg,rgba(8,38,52,.85),rgba(3,21,30,.8));display:grid;grid-template-columns:38px 1fr;column-gap:12px;align-items:center;text-decoration:none}.workspace i{grid-row:1/3;width:38px;height:38px;display:grid;place-items:center;border-radius:11px;background:rgba(67,194,229,.08);color:#61d5ed}.workspace strong{font-size:12px;color:#e4f5f8}.workspace span{font-size:9px;color:#6e8d98;margin-top:3px}.back-row{display:flex;justify-content:space-between;align-items:center;margin-bottom:11px;color:#668793;font-size:9px}.back-row a{color:#9bc0ca;text-decoration:none}
.toolbar{display:flex;justify-content:space-between;align-items:center;padding:10px 14px;margin-bottom:10px;border:1px solid var(--line);border-radius:13px;background:rgba(3,20,29,.92);backdrop-filter:blur(14px);color:#7f9ba5;font-size:10px;position:sticky;top:0;z-index:900;box-shadow:0 10px 28px rgba(0,0,0,.16)}.toolbar strong{font-size:15px;color:#eaf8fb}.hint{color:#6f8e98}.news-control-row{display:grid;grid-template-columns:minmax(0,1fr) auto;grid-template-areas:"create publish" "toolbar toolbar";align-items:center;gap:8px;margin-bottom:14px}.news-control-row .compact-primary{grid-area:create;justify-self:start}.news-control-row .news-toolbar{grid-area:toolbar}.news-control-row .publish-count{grid-area:publish;justify-self:end}.compact-primary{height:40px;padding:0 14px;border-radius:11px}.publish-count{height:40px;display:flex;align-items:center;justify-content:center;padding:0 14px;border:1px solid var(--line);border-radius:11px;background:rgba(67,194,229,.035);color:#7f9ba5;font-size:10px;white-space:nowrap}.publish-count strong{font-size:15px;color:#eaf8fb;margin-right:4px}.news-toolbar{display:grid;grid-template-columns:minmax(0,1fr) 78px 78px 58px;grid-template-areas:"search filter sort apply";align-items:center;gap:6px;min-width:0;padding:3px;border:1px solid var(--line);border-radius:11px;background:rgba(67,194,229,.025)}.news-toolbar .search{grid-area:search}.news-toolbar select[name="filter"]{grid-area:filter}.news-toolbar select[name="sort"]{grid-area:sort}.news-toolbar .filter-btn{grid-area:apply}.search{display:flex;align-items:center;gap:8px;min-width:0;width:100%;border:1px solid var(--line);border-radius:9px;background:#061923;padding:0 10px}.search i{color:#5fcbe4;font-size:11px}.search input{border:0;background:transparent;padding:9px 0;min-width:0;color:#e4f3f7;width:100%;outline:0;font-size:10px}.news-toolbar select,.filter-btn{height:34px;border:1px solid var(--line);border-radius:9px;background:#061923;color:#a9c3ca;padding:0 10px;font-size:9px}.filter-btn{background:linear-gradient(135deg,#25abc9,#1687a4);border:0;color:#fff;font-weight:800;cursor:pointer;display:inline-flex;align-items:center;justify-content:center;gap:5px;min-width:38px}
.content-list{display:grid;gap:12px}.resource-list .content-card{grid-template-columns:48px minmax(0,1fr) 42px}.resource-list .content-icon{width:48px;height:48px}.resource-list .excerpt{font-size:10px;line-height:1.5;color:#7898a5;display:-webkit-box;-webkit-line-clamp:2;-webkit-box-orient:vertical;overflow:hidden}.content-card{display:grid;grid-template-columns:54px minmax(0,1fr) auto;align-items:center;gap:12px;padding:10px 12px;border:1px solid var(--line);border-radius:15px;background:linear-gradient(90deg,rgba(8,34,46,.9),rgba(4,24,33,.92));transition:transform .18s,border-color .18s,background .18s;cursor:pointer}.content-card.company-card{grid-template-columns:26px 54px minmax(0,1fr) auto}.content-card:hover{border-color:rgba(78,205,232,.34);background:linear-gradient(90deg,rgba(9,40,53,.94),rgba(5,27,37,.96));transform:translateY(-1px)}.content-card.nav-enabled{cursor:grab}.content-card.nav-enabled:hover{transform:none}.content-card.dragging{opacity:.45;border-color:rgba(78,205,232,.5)}.handle{color:#537883;text-align:center;cursor:grab}.content-icon{width:54px;height:54px;border-radius:13px;background:#092633;display:grid;place-items:center;color:#4ec7e2;font-size:20px}.info{min-width:0}.name{font-size:13px;font-weight:800;color:#e7f6f8;white-space:nowrap;overflow:hidden;text-overflow:ellipsis}.slug{font-size:10px;color:#58c6df;margin-top:5px;white-space:nowrap;overflow:hidden;text-overflow:ellipsis}.excerpt{font-size:9px;color:#7795a0;margin-top:6px;white-space:nowrap;overflow:hidden;text-overflow:ellipsis}.featured{display:inline-flex;align-items:center;gap:4px;margin-top:7px;font-size:8px;font-weight:800;color:#f3d47f;background:rgba(243,212,127,.08);border:1px solid rgba(243,212,127,.14);border-radius:999px;padding:5px 7px}.meta{display:flex;align-items:center;gap:10px;margin-top:9px}.status{font-size:8px;padding:6px 8px;border-radius:999px}.status.published{color:#9ee7ca;background:rgba(49,191,139,.09);border:1px solid rgba(49,191,139,.15)}.status.draft{color:#f2c78d;background:rgba(220,153,68,.09);border:1px solid rgba(220,153,68,.15)}.date{color:#6f9aa5;font-size:9px}.actions{display:flex;align-items:center;justify-content:center}.actions form{margin:0}.actions button{width:34px;height:34px;border:1px solid transparent;border-radius:9px;background:transparent;color:#7797a2;display:grid;place-items:center;cursor:pointer}.actions button:hover{background:rgba(231,83,91,.1);color:#ff9da4;border-color:rgba(231,83,91,.18)}.notice{padding:11px 13px;margin-bottom:12px;border-radius:11px;background:rgba(67,194,137,.1);color:#a8e5ca;font-size:10px}.empty{text-align:center;padding:55px 20px;border:1px dashed var(--line);border-radius:18px;color:#7898a5}.empty i{font-size:34px;color:#4fc8e4}.empty strong{display:block;color:#dff4f7;margin:12px 0 5px;font-size:18px}.empty span{font-size:10px}
.news-list{grid-template-columns:1fr;gap:12px}
.resource-list .content-card{grid-template-columns:54px minmax(0,1fr) auto}
.resource-list .news-kind{color:#9ee7ca}
.resource-list .news-cover{background:linear-gradient(145deg,#082b2b,#061923)}.content-card.news-card{position:relative;display:grid;grid-template-columns:180px 38px minmax(0,1fr);width:100%;height:156px;min-height:156px;overflow:hidden;padding:0;border-radius:18px;background:linear-gradient(110deg,rgba(8,37,50,.94),rgba(3,20,29,.96));align-items:stretch}.news-card:hover{transform:translateY(-2px);border-color:rgba(72,216,241,.4)}.news-cover{width:180px;height:156px;min-width:180px;overflow:hidden;background:#061923;display:grid;place-items:center}.news-cover img{width:100%;height:100%;display:block;object-fit:cover}.news-cover i{font-size:28px;color:#48d8f1}.news-kind{height:100%;display:flex;align-items:center;justify-content:center;border-left:1px solid rgba(72,216,241,.1);border-right:1px solid rgba(72,216,241,.1);color:#48d8f1;font-size:8px;font-weight:800;letter-spacing:.16em;writing-mode:vertical-rl;transform:rotate(180deg);text-transform:uppercase}.news-kind.notice{color:#f0c58e}.news-card .info{height:156px;overflow:hidden;padding:17px 56px 14px 20px;display:flex;flex-direction:column;min-width:0}.news-card .name{font-size:18px;line-height:1.32;white-space:normal;display:-webkit-box;-webkit-line-clamp:2;-webkit-box-orient:vertical;overflow:hidden}.news-card .slug{font-size:9px;color:#58c6df}.news-card .excerpt{font-size:10px;line-height:1.55;white-space:normal;display:-webkit-box;-webkit-line-clamp:2;-webkit-box-orient:vertical;overflow:hidden}.news-card .meta{margin-top:auto;padding-top:9px}.news-card .actions{position:absolute;right:12px;top:50%;z-index:3;transform:translateY(-50%)}.news-actions{display:flex!important;flex-direction:column;align-items:center;justify-content:center;gap:7px}.news-actions form{width:32px;height:32px;display:grid;place-items:center;margin:0}.news-switch{width:32px!important;height:32px!important;padding:0;border:1px solid rgba(72,216,241,.18)!important;border-radius:9px!important;background:rgba(72,216,241,.06)!important;display:grid!important;place-items:center!important;cursor:pointer}.switch-track{position:relative;width:19px;height:10px;border-radius:999px;background:#3a5660}.switch-knob{position:absolute;top:2px;left:2px;width:6px;height:6px;border-radius:50%;background:#a4b8bd;transition:.18s}.news-switch.on{border-color:rgba(67,194,137,.28)!important;background:rgba(67,194,137,.09)!important}.news-switch.on .switch-track{background:#35b887}.news-switch.on .switch-knob{left:11px;background:#eafff7}.news-switch:hover{transform:translateY(-1px)}.news-delete{width:32px!important;height:32px!important;border:1px solid rgba(255,99,113,.12)!important;border-radius:9px!important;background:rgba(255,99,113,.04)!important;color:#ff9eaa!important}.news-delete:hover{background:rgba(255,99,113,.11)!important;border-color:rgba(255,99,113,.22)!important}.news-card .featured{align-self:flex-start}.news-card .date{font-size:9px}
@media(max-width:1100px){.content-card.news-card{grid-template-columns:150px 34px minmax(0,1fr);height:140px;min-height:140px}.news-cover{width:150px;height:140px;min-width:150px}.news-card .info{height:140px;padding:14px 50px 12px 16px}.news-card .name{font-size:16px}.news-card .excerpt{font-size:9px}}
@media(max-width:900px){.hero{flex-direction:column;gap:10px;margin-bottom:14px}.hero h1{font-size:31px}.hero p{font-size:10px;line-height:1.5}.primary{width:100%}.workspace{grid-template-columns:1fr}.content-card.news-card{grid-template-columns:128px 28px minmax(0,1fr);height:128px;min-height:128px}.news-cover{width:128px;height:128px;min-width:128px}.news-card .info{height:128px;padding:12px 42px 10px 14px}.news-card .name{font-size:15px}.news-card .excerpt{display:none}.news-kind{font-size:7px}.news-control-row{grid-template-columns:minmax(0,1fr) auto;grid-template-areas:"create publish" "toolbar toolbar";gap:6px}.news-control-row .compact-primary{width:auto}.news-control-row .publish-count{width:auto}.news-control-row .news-toolbar{width:100%}}
@media(max-width:700px){.content-card{grid-template-columns:48px minmax(0,1fr) 42px;gap:10px;padding:11px 10px}.content-card.company-card{grid-template-columns:24px 48px minmax(0,1fr) 42px}.actions{justify-content:flex-end}.meta{flex-wrap:wrap}.content-card.news-card{grid-template-columns:112px 24px minmax(0,1fr);height:112px;min-height:112px}.news-cover{width:112px;height:112px;min-width:112px}.news-kind{font-size:7px;letter-spacing:.11em}.news-card .info{height:112px;min-height:112px;padding:11px 38px 10px 13px}.news-card .name{font-size:14px;line-height:1.3}.news-card .slug,.news-card .excerpt{display:none}.news-card .meta{padding-top:5px;margin-top:auto}.news-card .date{font-size:8px}.news-card .actions{right:5px;top:50%;transform:translateY(-50%)}.news-actions{gap:6px}.news-actions form{width:28px;height:28px}.news-switch,.news-delete{width:28px!important;height:28px!important;border-radius:8px!important}.switch-track{width:17px;height:9px}.switch-knob{width:5px;height:5px;top:2px;left:2px}.news-switch.on .switch-knob{left:10px}.news-card .actions button{width:28px;height:28px}.news-card .featured{font-size:7px;padding:4px 6px;margin-top:5px}.news-control-row{grid-template-columns:minmax(0,1fr) auto;grid-template-areas:"create publish" "toolbar toolbar";gap:5px}.compact-primary{height:36px;padding:0 11px}.compact-primary span{display:inline}.publish-count{height:36px;padding:0 10px}.news-toolbar{grid-area:toolbar;grid-template-columns:minmax(0,1fr) 68px 68px 42px;grid-template-areas:"search filter sort apply";gap:4px;width:100%;padding:3px}.news-toolbar select,.filter-btn{height:30px}.search{height:30px}}
@media(max-width:500px){.hero{gap:7px;margin-bottom:7px}.eyebrow{font-size:8px}.hero h1{font-size:27px;line-height:1.1}.hero p{font-size:9px;line-height:1.35}.primary{padding:8px 12px;border-radius:10px;font-size:9px}.back-row{margin-bottom:6px;font-size:8px}.news-control-row{grid-template-columns:minmax(0,1fr) auto;grid-template-areas:"create publish" "toolbar toolbar";gap:5px;margin-bottom:8px}.compact-primary{width:auto;height:34px;padding:0 11px;border-radius:9px}.compact-primary span{display:inline;font-size:9px}.publish-count{height:34px;padding:0 7px;border-radius:9px;font-size:8px}.publish-count strong{font-size:12px}.publish-count span{font-size:8px}.news-toolbar{grid-area:toolbar;width:100%;padding:2px;gap:3px;border-radius:9px;display:grid;grid-template-columns:minmax(0,1fr) 58px 58px 34px;grid-template-areas:"search filter sort apply"}.search{height:30px;padding:0 7px;border-radius:8px;min-width:0}.search input{font-size:9px;padding:6px 0}.search i{font-size:9px}.news-toolbar select{height:30px;font-size:7px;padding:0 5px;max-width:none;min-width:0}.filter-btn{height:30px;min-width:34px;padding:0}.content-card{grid-template-columns:44px minmax(0,1fr) 38px;gap:8px;padding:9px}.content-card.company-card{grid-template-columns:22px 44px minmax(0,1fr) 38px}.content-icon{width:44px;height:44px;font-size:17px}.actions button{width:32px;height:32px}.name{font-size:12px}.slug{font-size:9px}.toolbar{padding:5px 9px;margin-bottom:6px;min-height:24px;border-radius:10px}.toolbar strong{font-size:13px}.toolbar .hint{font-size:0}.toolbar .hint i{font-size:9px}.content-card.news-card{grid-template-columns:100px 22px minmax(0,1fr);height:100px;min-height:100px}.news-cover{width:100px;height:100px;min-width:100px}.news-card .info{height:100px;min-height:100px;padding:9px 34px 8px 11px}.news-card .name{font-size:13px}.news-kind{font-size:6px}.news-card .actions{right:3px}.news-card .actions button{width:26px;height:26px}.news-card .meta{padding-top:3px}.news-card .date{font-size:8px}}

/* Responsive news controls polish */
@media(max-width:700px){
.news-control-row{grid-template-columns:minmax(0,1fr) auto;grid-template-areas:"create publish" "toolbar toolbar";gap:6px;align-items:stretch}
.news-control-row .compact-primary{min-width:0;width:auto;justify-self:stretch}
.news-control-row .publish-count{width:auto;min-width:92px;justify-self:end}
.news-control-row .news-toolbar{width:100%;min-width:0;grid-template-columns:minmax(0,1fr) 62px 62px 52px;grid-template-areas:"search filter sort apply";gap:4px;padding:3px;overflow:hidden}
.news-toolbar .search{min-width:0;width:100%}
.news-toolbar select{min-width:0;width:100%;padding-left:7px;padding-right:7px}
.news-toolbar .filter-btn{width:52px;min-width:52px;padding:0 7px;white-space:nowrap}
}
@media(max-width:420px){
.news-control-row{gap:5px}.news-control-row .publish-count{min-width:84px;padding:0 8px}
.news-toolbar{grid-template-columns:minmax(0,1fr) 56px 56px 44px !important}
.news-toolbar .filter-btn{width:44px;min-width:44px;padding:0}.news-toolbar .filter-btn span{display:none}
}

</style>@endpush

@push('styles')<style>
.resource-control-row{display:grid;grid-template-columns:auto auto minmax(0,1fr);grid-template-areas:"create publish toolbar";gap:8px;align-items:center;margin-bottom:9px}.resource-control-row .compact-primary{grid-area:create}.resource-control-row .publish-count{grid-area:publish}.resource-toolbar{grid-area:toolbar;display:grid;grid-template-columns:minmax(0,1fr) 82px 82px 58px;gap:5px;min-width:0;padding:3px;border:1px solid var(--line);border-radius:11px;background:rgba(67,194,229,.025)}.resource-toolbar .search{min-width:0}.resource-toolbar select,.resource-toolbar .filter-btn{height:34px;border:1px solid var(--line);border-radius:9px;background:#061923;color:#a9c3ca;padding:0 9px;font-size:9px}.resource-toolbar .filter-btn{border:0;background:linear-gradient(135deg,#25abc9,#1687a4);color:#fff;cursor:pointer}.resource-view-tools{display:flex;align-items:center;justify-content:flex-end;gap:5px;margin:0 0 9px;color:#678692;font-size:9px}.view-toggle{width:31px;height:30px;border:1px solid var(--line);border-radius:8px;background:#061923;color:#7695a0;cursor:pointer}.view-toggle.active{color:#dff8fc;border-color:rgba(67,194,229,.3);background:rgba(67,194,229,.08)}.resource-list.card-view{grid-template-columns:repeat(3,minmax(0,1fr));gap:12px}.resource-list.card-view .content-card{display:flex;flex-direction:column;align-items:stretch;gap:9px;padding:14px;min-height:190px}.resource-list.card-view .content-icon{width:52px;height:52px}.resource-list.card-view .info{width:100%}.resource-list.card-view .name{white-space:normal;line-height:1.35}.resource-list.card-view .slug{margin-top:5px}.resource-list.card-view .meta{margin-top:10px}.resource-list.card-view .actions{margin-top:auto;justify-content:flex-end}.resource-actions{gap:5px}.resource-actions form{margin:0}.resource-action,.resource-delete,.resource-switch{width:32px!important;height:32px!important;border:1px solid var(--line)!important;border-radius:9px!important;display:grid!important;place-items:center!important;background:rgba(67,194,229,.04)!important;color:#86a7b0!important;cursor:pointer}.resource-switch.on{background:rgba(67,194,137,.08)!important;border-color:rgba(67,194,137,.22)!important}.resource-switch .switch-track{width:19px;height:11px}.resource-switch .switch-knob{width:7px;height:7px;top:2px;left:2px}.resource-switch.on .switch-knob{left:10px}.resource-delete{color:#ff9eaa!important;background:rgba(255,99,113,.04)!important}.resource-action:hover,.resource-delete:hover,.resource-switch:hover{transform:translateY(-1px)}
@media(max-width:900px){.resource-control-row{grid-template-columns:auto auto;grid-template-areas:"create publish" "toolbar toolbar"}.resource-list.card-view{grid-template-columns:repeat(2,minmax(0,1fr))}}
@media(max-width:600px){.resource-control-row{grid-template-columns:minmax(0,1fr) auto}.resource-toolbar{grid-template-columns:minmax(0,1fr) 58px 58px 34px}.resource-toolbar select,.resource-toolbar .filter-btn{height:30px;font-size:7px;padding:0 5px}.resource-toolbar .filter-btn span{display:none}.resource-list.card-view{grid-template-columns:1fr 1fr}.resource-list.card-view .content-card{min-height:170px;padding:11px}.resource-view-tools{margin-bottom:7px}}
@media(max-width:420px){.resource-toolbar{grid-template-columns:minmax(0,1fr) 52px 52px 32px}.resource-list.card-view{grid-template-columns:1fr 1fr}}
@media(max-width:600px){
  .resource-control-row{
    grid-template-columns:minmax(0,1fr);
    grid-template-areas:"create" "publish" "toolbar";
    gap:8px;
  }
  .resource-control-row .compact-primary,
  .resource-control-row .publish-count{width:100%;box-sizing:border-box}
  .resource-control-row .compact-primary{justify-content:center}
  .resource-control-row .publish-count{justify-content:flex-start;padding-inline:12px}
  .resource-toolbar{
    grid-template-columns:minmax(0,1fr) 64px 64px 36px!important;
    width:100%;
    box-sizing:border-box;
  }
  .resource-toolbar .search{min-width:0}
  .resource-toolbar input{min-width:0;font-size:10px}
  .resource-list .content-card{
    grid-template-columns:42px minmax(0,1fr) auto;
    gap:9px;
    padding:9px;
  }
  .resource-list .content-icon{width:42px;height:42px;font-size:17px}
  .resource-list .name{font-size:12px}
  .resource-list .excerpt{font-size:9px}
  .resource-list .meta{gap:6px;flex-wrap:wrap}
  .resource-actions{gap:3px}
  .resource-action,.resource-delete,.resource-switch{width:30px!important;height:30px!important}
}
@media(max-width:420px){
  .resource-toolbar{
    grid-template-columns:minmax(0,1fr) 58px 58px 32px!important;
  }
  .resource-toolbar select{font-size:7px!important}
}
</style>
@endpush
@push('scripts')<script>
(()=>{const list=document.querySelector('.resource-list'), buttons=document.querySelectorAll('.view-toggle');if(!list||!buttons.length)return;const key='ff-resource-view';let mode=localStorage.getItem(key)||'list';const apply=()=>{list.classList.toggle('card-view',mode==='card');buttons.forEach(b=>b.classList.toggle('active',b.dataset.view===mode));};buttons.forEach(b=>b.addEventListener('click',()=>{mode=b.dataset.view;localStorage.setItem(key,mode);apply()}));apply();})();
</script>
@endpush
@push('head')<meta name="csrf-token" content="{{ csrf_token() }}">@endpush
@push('scripts')<script>
(function(){
 const cards=document.querySelectorAll('.content-card');
 cards.forEach(card=>card.addEventListener('click',e=>{
   if(e.target.closest('form,button,a')) return;
   const url=card.dataset.editUrl;
   if(url) window.location.href=url;
 }));
})();
</script>@endpush
