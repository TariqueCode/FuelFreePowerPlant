@extends('layouts.portal')
@section('title','Dashboard')
@section('content')
@php
    $storageGb = (float) (($storageBytes ?? 0) / 1073741824);
    $storagePct = min(100, $storageGb * 100);
    $quickActions = [];
    if (auth()->user()->hasPermission('website.view')) {
        $quickActions[] = ['icon'=>'fa-house','title'=>'Edit Homepage','text'=>'Homepage sections','url'=>route('admin.homepage-builder.index')];
        $quickActions[] = ['icon'=>'fa-images','title'=>'Manage Sliders','text'=>'Rotating banners','url'=>route('admin.sliders.index')];
        $quickActions[] = ['icon'=>'fa-newspaper','title'=>'News & Event','text'=>'Website updates','url'=>route('admin.site-content.index',['type'=>'news'])];
    }
    if (auth()->user()->hasPermission('documents.view')) $quickActions[] = ['icon'=>'fa-cloud-arrow-up','title'=>'Documents & Media','text'=>'Files and uploads','url'=>route('admin.documents')];
    if (auth()->user()->hasPermission('users.view')) $quickActions[] = ['icon'=>'fa-users','title'=>'Manage Users','text'=>'Accounts & access','url'=>route('admin.users.index')];
    if (auth()->user()->hasPermission('inquiries.view')) $quickActions[] = ['icon'=>'fa-comments','title'=>'View Inquiries','text'=>'Website messages','url'=>route('admin.inquiries.index')];
    if (auth()->user()->hasPermission('mail.view')) $quickActions[] = ['icon'=>'fa-envelope','title'=>'Webmail','text'=>'Mail workspace','url'=>route('admin.mail')];
    if (auth()->user()->hasPermission('settings.manage')) $quickActions[] = ['icon'=>'fa-gear','title'=>'Settings','text'=>'System configuration','url'=>route('admin.settings')];
@endphp

<section class="ff-control" aria-label="Administration dashboard">
    <header class="ff-control-head">
        <div class="ff-head-copy">
            <span class="ff-kicker"><i></i> ADMIN DASHBOARD</span>
            <h1>Welcome back, <strong>{{ auth()->user()->name }}!</strong></h1>
            <p>Manage your website content, communications and platform settings from one place.</p>
        </div>
        <div class="ff-head-tools">
            <div class="ff-date-card">
                <span class="ff-date-icon"><i class="fa-regular fa-calendar"></i></span>
                <div><strong>{{ now()->format('l, d M Y') }}</strong><small>{{ now()->format('h:i A') }} · {{ now()->format('T') }}</small></div>
            </div>
            <a class="ff-energy-card" href="{{ url('/') }}" target="_blank" rel="noopener noreferrer" aria-label="View public website">
                <i class="fa-solid fa-bolt"></i><div><strong>Clean Energy</strong><span>Sustainable Future</span><small>Powering a Greener Tomorrow</small></div><i class="fa-solid fa-leaf ff-leaf"></i>
            </a>
        </div>
    </header>

    <section class="ff-stat-grid" aria-label="Website statistics">
        <a class="ff-stat" href="{{ route('admin.site-content.index') }}"><span class="ff-stat-icon blue"><i class="fa-regular fa-file-lines"></i></span><span><small>Website Content</small><strong>{{ number_format($contentTotal) }}</strong><em>{{ number_format($published) }} published · {{ number_format($drafts) }} drafts</em></span><b>→</b></a>
        <a class="ff-stat" href="{{ route('admin.sliders.index') }}"><span class="ff-stat-icon green"><i class="fa-regular fa-images"></i></span><span><small>Homepage Sliders</small><strong>{{ number_format($sliders) }}</strong><em>Active homepage media</em></span><b>→</b></a>
        <a class="ff-stat" href="{{ route('admin.site-content.index',['type'=>'news']) }}"><span class="ff-stat-icon purple"><i class="fa-regular fa-newspaper"></i></span><span><small>News &amp; Event</small><strong>{{ number_format($news) }}</strong><em>Public updates</em></span><b>→</b></a>
        <a class="ff-stat" href="{{ route('admin.gallery.index') }}"><span class="ff-stat-icon amber"><i class="fa-regular fa-images"></i></span><span><small>Gallery Albums</small><strong>{{ number_format($gallery) }}</strong><em>Photo collections</em></span><b>→</b></a>
        @if(auth()->user()->hasPermission('inquiries.view'))<a class="ff-stat" href="{{ route('admin.inquiries.index') }}"><span class="ff-stat-icon cyan"><i class="fa-regular fa-comments"></i></span><span><small>Inquiries</small><strong>{{ number_format($inquiries) }}</strong><em>New website messages</em></span><b>→</b></a>@endif
    </section>

    <div class="ff-content-grid">
        <section class="ff-panel ff-quick-panel">
            <div class="ff-panel-head">
                <div class="ff-panel-title"><span class="ff-panel-icon green"><i class="fa-solid fa-bolt"></i></span><div><h2>Quick Actions</h2><p>Common tasks to manage your website quickly.</p></div></div>
                <a class="ff-view-btn" href="{{ url('/') }}" target="_blank" rel="noopener noreferrer">View Website <i class="fa-solid fa-arrow-up-right-from-square"></i></a>
            </div>
            <div class="ff-action-grid">
                @forelse($quickActions as $action)
                    <a href="{{ $action['url'] }}"><i class="fa-solid {{ $action['icon'] }}"></i><span><strong>{{ $action['title'] }}</strong><small>{{ $action['text'] }}</small></span><b>›</b></a>
                @empty
                    <div class="ff-empty">No permitted quick actions are available for this account.</div>
                @endforelse
            </div>
        </section>

        <section class="ff-panel ff-activity-panel">
            <div class="ff-panel-head"><div class="ff-panel-title"><span class="ff-panel-icon cyan"><i class="fa-regular fa-clock"></i></span><div><h2>Recent Activity</h2><p>Latest actions across the platform.</p></div></div>@if(auth()->user()->hasPermission('audit.view'))<a class="ff-view-btn" href="{{ route('admin.audit.index') }}">Audit Log <i class="fa-solid fa-arrow-right"></i></a>@endif</div>
            <div class="ff-activity-list">
                @forelse($recentActivity as $activity)
                    <div class="ff-activity">
                        <span class="ff-activity-icon"><i class="fa-solid fa-{{ $activity->module === 'auth' ? 'user' : ($activity->module === 'website' ? 'file-lines' : ($activity->module === 'settings' ? 'gear' : 'bolt')) }}"></i></span>
                        <div><strong>{{ \Illuminate\Support\Str::headline($activity->action ?: 'Platform activity') }}</strong><small>{{ $activity->user?->name ?? 'System' }}{{ $activity->module ? ' · '.\Illuminate\Support\Str::headline($activity->module) : '' }}</small></div>
                        <time>{{ optional($activity->created_at)->diffForHumans() }}</time>
                    </div>
                @empty
                    <div class="ff-activity-empty"><i class="fa-regular fa-clock"></i><span>{{ auth()->user()->hasPermission('audit.view') ? 'No activity has been recorded yet.' : 'Recent activity is available to accounts with Audit Log access.' }}</span></div>
                @endforelse
            </div>
        </section>
    </div>

    <div class="ff-bottom-grid">
        <section class="ff-panel ff-status-panel">
            <div class="ff-panel-head"><div class="ff-panel-title"><span class="ff-panel-icon cyan"><i class="fa-solid fa-wave-square"></i></span><div><h2>Platform Status</h2><p>Current application health and services.</p></div></div><span class="ff-live"><i></i> Live</span></div>
            <div class="ff-status-grid">
                <div class="ff-status"><i class="{{ $platformStatus['website'] ? 'ok' : 'bad' }}"></i><span>Website</span><strong>{{ $platformStatus['website'] ? 'Online' : 'Offline' }}</strong></div>
                <div class="ff-status"><i class="{{ $platformStatus['database'] ? 'ok' : 'bad' }}"></i><span>Database</span><strong>{{ $platformStatus['database'] ? 'Healthy' : 'Unavailable' }}</strong></div>
                <div class="ff-status"><i class="{{ $platformStatus['mail'] ? 'ok' : 'bad' }}"></i><span>Mail Service</span><strong>{{ $platformStatus['mail'] ? 'Configured' : 'Not configured' }}</strong></div>
                <div class="ff-status"><i class="{{ $platformStatus['storage'] ? 'ok' : 'bad' }}"></i><span>File Storage</span><strong>{{ $platformStatus['storage'] ? 'Ready' : 'Check access' }}</strong></div>
            </div>
        </section>
        <section class="ff-panel ff-storage-panel">
            <div class="ff-panel-head"><div class="ff-panel-title"><span class="ff-panel-icon teal"><i class="fa-solid fa-database"></i></span><div><h2>Storage Usage</h2><p>Public documents and media storage.</p></div></div><strong class="ff-storage-percent">{{ number_format($storagePct,0) }}%</strong></div>
            <div class="ff-storage-number"><strong>{{ number_format($storageGb,2) }} GB</strong><span>Current usage</span></div>
            <div class="ff-storage-track"><i style="width:{{ $storagePct }}%"></i></div>
            <div class="ff-storage-meta"><span>Used {{ number_format($storageGb,2) }} GB</span><span>Reference: 1 GB</span></div>
        </section>
    </div>

    <section class="ff-attention-panel ff-panel">
        <div class="ff-panel-head"><div class="ff-panel-title"><span class="ff-panel-icon teal"><i class="fa-solid fa-bullseye"></i></span><div><h2>Attention Overview</h2><p>Only the items that may need your next action.</p></div></div></div>
        <div class="ff-attention-grid">
            <a href="{{ route('admin.inquiries.index') }}"><span class="ff-attention-icon cyan"><i class="fa-regular fa-comments"></i></span><span><small>Website Inquiries</small><strong>{{ number_format($inquiries) }}</strong><em>{{ $inquiries > 0 ? 'Review messages' : 'No open messages' }}</em></span><b>→</b></a>
            @if(auth()->user()->hasPermission('career.view'))<a href="{{ route('admin.career-applications.index') }}"><span class="ff-attention-icon amber"><i class="fa-solid fa-briefcase"></i></span><span><small>New Career Applications</small><strong>{{ number_format($newApplications) }}</strong><em>{{ $newApplications > 0 ? 'Review applications' : 'No new applications' }}</em></span><b>→</b></a>@endif
            <a href="{{ route('admin.site-content.index') }}"><span class="ff-attention-icon blue"><i class="fa-regular fa-file-lines"></i></span><span><small>Draft Content</small><strong>{{ number_format($drafts) }}</strong><em>{{ $drafts > 0 ? 'Continue editing' : 'Everything published' }}</em></span><b>→</b></a>
            @if(auth()->user()->hasPermission('documents.view'))<a href="{{ route('admin.documents') }}"><span class="ff-attention-icon green"><i class="fa-solid fa-cloud-arrow-up"></i></span><span><small>Media Storage</small><strong>{{ number_format($storageGb,2) }} GB</strong><em>{{ $platformStatus['storage'] ? 'Storage is ready' : 'Check storage access' }}</em></span><b>→</b></a>@endif
        </div>
    </section>
</section>
@endsection

@push('styles')
<style>
.ff-control{--panel:linear-gradient(145deg,rgba(7,35,48,.86),rgba(4,22,32,.9));--line:rgba(88,213,234,.14);--text:#edf8fa;--muted:#7899a6;max-width:1480px;margin:0 auto;color:var(--text)}
.ff-control *{box-sizing:border-box}.ff-control a{text-decoration:none;color:inherit}.ff-control-head{display:flex;justify-content:space-between;align-items:flex-end;gap:20px;padding:2px 0 22px}.ff-head-copy{min-width:0}.ff-kicker{display:flex;align-items:center;gap:8px;color:#67d7ec;font-size:10px;font-weight:800;letter-spacing:.22em}.ff-kicker i,.ff-live i{display:block;width:7px;height:7px;border-radius:50%;background:#48e99a;box-shadow:0 0 12px rgba(72,233,154,.65)}.ff-control h1{margin:8px 0 7px;font-size:clamp(31px,3.4vw,46px);line-height:1.05;letter-spacing:-.04em}.ff-control h1 strong{font-weight:800;background:linear-gradient(100deg,#f1fbfb,#4de1ee);-webkit-background-clip:text;background-clip:text;color:transparent}.ff-head-copy p{margin:0;max-width:700px;color:var(--muted);font-size:13px;line-height:1.55}.ff-head-tools{display:flex;align-items:stretch;gap:9px;flex:0 0 auto}.ff-date-card,.ff-energy-card{border:1px solid var(--line);background:rgba(5,30,41,.72);border-radius:14px}.ff-date-card{display:flex;align-items:center;gap:10px;padding:10px 13px;min-width:210px}.ff-date-icon{width:38px;height:38px;display:grid;place-items:center;border-radius:10px;background:rgba(84,215,238,.08);color:#63dff1}.ff-date-card strong,.ff-date-card small{display:block}.ff-date-card strong{font-size:11px}.ff-date-card small{margin-top:4px;color:#71919d;font-size:9px}.ff-energy-card{position:relative;display:flex;align-items:center;gap:9px;min-width:230px;padding:10px 12px;overflow:hidden;background:linear-gradient(135deg,rgba(18,91,78,.8),rgba(10,47,53,.82))}.ff-energy-card:after{content:"";position:absolute;width:90px;height:90px;right:-28px;top:-35px;border-radius:50%;background:rgba(67,230,161,.14);filter:blur(3px)}.ff-energy-card>i:first-child{font-size:25px;color:#69efa5}.ff-energy-card div{position:relative;z-index:1}.ff-energy-card strong,.ff-energy-card span,.ff-energy-card small{display:block}.ff-energy-card strong{font-size:12px}.ff-energy-card span{font-size:10px;font-weight:700}.ff-energy-card small{margin-top:3px;color:#9bd6c6;font-size:8px}.ff-energy-card .ff-leaf{position:absolute;right:12px;bottom:9px;color:#73eea8;font-size:22px;opacity:.8}.ff-stat-grid{display:grid;grid-template-columns:repeat(5,minmax(0,1fr));gap:10px}.ff-stat{display:flex;align-items:center;gap:10px;min-width:0;padding:13px 14px;border:1px solid var(--line);border-radius:16px;background:var(--panel);transition:transform .18s,border-color .18s,box-shadow .18s}.ff-stat:hover{transform:translateY(-2px);border-color:rgba(88,213,234,.28);box-shadow:0 15px 35px rgba(0,0,0,.18)}.ff-stat-icon{width:41px;height:41px;display:grid;place-items:center;flex:0 0 41px;border-radius:11px;font-size:15px}.ff-stat-icon.blue,.ff-attention-icon.blue{color:#79adff;background:rgba(72,137,255,.14)}.ff-stat-icon.green,.ff-attention-icon.green{color:#6ceda5;background:rgba(51,224,135,.12)}.ff-stat-icon.purple{color:#b59cff;background:rgba(145,105,238,.14)}.ff-stat-icon.amber,.ff-attention-icon.amber{color:#f6bd62;background:rgba(237,171,50,.14)}.ff-stat-icon.cyan,.ff-attention-icon.cyan{color:#67e0f2;background:rgba(64,198,224,.14)}.ff-stat>span:nth-child(2){min-width:0;flex:1}.ff-stat small,.ff-stat em{display:block;color:#7b9ba7;font-size:8px;font-style:normal}.ff-stat strong{display:block;margin:4px 0 2px;font-size:21px}.ff-stat>b{color:#789aa7;font-size:17px;font-weight:400}.ff-content-grid,.ff-bottom-grid{display:grid;grid-template-columns:minmax(0,1.55fr) minmax(330px,.85fr);gap:11px;margin-top:11px}.ff-panel{min-width:0;border:1px solid var(--line);border-radius:18px;background:var(--panel);box-shadow:0 15px 45px rgba(0,0,0,.12);padding:18px}.ff-panel-head{display:flex;justify-content:space-between;align-items:flex-start;gap:12px}.ff-panel-title{display:flex;align-items:center;gap:10px;min-width:0}.ff-panel-icon{width:34px;height:34px;display:grid;place-items:center;flex:0 0 34px;border-radius:10px;font-size:14px}.ff-panel-icon.green{color:#6deca4;background:rgba(51,224,135,.11)}.ff-panel-icon.cyan{color:#64e1f2;background:rgba(64,198,224,.11)}.ff-panel-icon.teal{color:#60ddd1;background:rgba(61,213,195,.11)}.ff-panel-title h2{margin:0;font-size:16px;letter-spacing:-.02em}.ff-panel-title p{margin:3px 0 0;color:#72929d;font-size:9px}.ff-view-btn{display:inline-flex;align-items:center;gap:6px;white-space:nowrap;padding:7px 9px;border:1px solid rgba(91,213,236,.14);border-radius:9px;background:rgba(78,204,231,.045);color:#b8e5ec!important;font-size:8px}.ff-action-grid{display:grid;grid-template-columns:repeat(4,minmax(0,1fr));gap:8px;margin-top:14px}.ff-action-grid a,.ff-attention-grid a{display:flex;align-items:center;gap:8px;min-width:0;padding:10px;border:1px solid rgba(87,208,228,.09);border-radius:11px;background:rgba(2,18,27,.42);transition:.18s}.ff-action-grid a:hover,.ff-attention-grid a:hover{transform:translateY(-1px);border-color:rgba(87,208,228,.25);background:rgba(9,47,60,.46)}.ff-action-grid a>i{width:28px;height:28px;display:grid;place-items:center;flex:0 0 28px;border-radius:8px;background:rgba(69,201,229,.08);color:#5ed8ee;font-size:11px}.ff-action-grid span,.ff-attention-grid span:nth-child(2){min-width:0;flex:1}.ff-action-grid strong,.ff-action-grid small,.ff-attention-grid small,.ff-attention-grid strong,.ff-attention-grid em{display:block;overflow:hidden;text-overflow:ellipsis;white-space:nowrap}.ff-action-grid strong,.ff-attention-grid strong{font-size:8px}.ff-action-grid small,.ff-attention-grid small{margin-top:2px;color:#6d8c98;font-size:7px}.ff-action-grid b,.ff-attention-grid b{color:#6b929e;font-size:15px;font-weight:400}.ff-activity-list{margin-top:8px}.ff-activity{display:flex;align-items:center;gap:9px;padding:9px 0;border-bottom:1px solid rgba(255,255,255,.055)}.ff-activity:last-child{border-bottom:0}.ff-activity-icon{width:29px;height:29px;display:grid;place-items:center;flex:0 0 29px;border-radius:9px;background:rgba(63,208,224,.09);color:#63ddec;font-size:10px}.ff-activity>div{min-width:0;flex:1}.ff-activity strong,.ff-activity small{display:block;overflow:hidden;text-overflow:ellipsis;white-space:nowrap}.ff-activity strong{font-size:9px}.ff-activity small{margin-top:3px;color:#6d8c98;font-size:7px}.ff-activity time{flex:0 0 auto;color:#7795a0;font-size:7px}.ff-activity-empty{display:flex;align-items:center;justify-content:center;gap:8px;min-height:120px;color:#718e99;font-size:9px;text-align:center}.ff-live{display:inline-flex;align-items:center;gap:5px;padding:5px 8px;border:1px solid rgba(60,227,142,.15);border-radius:99px;background:rgba(60,227,142,.05);color:#69eaa2;font-size:8px}.ff-status-grid{display:grid;grid-template-columns:repeat(4,1fr);gap:8px;margin-top:14px}.ff-status{min-width:0;padding:12px;border:1px solid rgba(88,213,234,.09);border-radius:11px;background:rgba(2,18,27,.38)}.ff-status>i{display:block;width:7px;height:7px;margin-bottom:9px;border-radius:50%}.ff-status>i.ok{background:#49e794;box-shadow:0 0 10px rgba(73,231,148,.55)}.ff-status>i.bad{background:#f06b6b;box-shadow:0 0 10px rgba(240,107,107,.45)}.ff-status span,.ff-status strong{display:block}.ff-status span{color:#7a9aa5;font-size:8px}.ff-status strong{margin-top:4px;color:#69eca3;font-size:10px}.ff-status>i.bad+span+strong{color:#ef8383}.ff-storage-percent{color:#bfeef2;font-size:10px}.ff-storage-number{padding:16px 0 11px}.ff-storage-number strong{display:block;font-size:28px}.ff-storage-number span{display:block;margin-top:3px;color:#718f9a;font-size:8px}.ff-storage-track{height:7px;overflow:hidden;border-radius:99px;background:rgba(255,255,255,.06)}.ff-storage-track i{display:block;height:100%;border-radius:inherit;background:linear-gradient(90deg,#43dfae,#55d9ee)}.ff-storage-meta{display:flex;justify-content:space-between;gap:8px;margin-top:8px;color:#6f8e99;font-size:7px}.ff-attention-panel{margin-top:11px}.ff-attention-grid{display:grid;grid-template-columns:repeat(4,minmax(0,1fr));gap:8px;margin-top:14px}.ff-attention-grid a{align-items:center}.ff-attention-icon{width:29px;height:29px;display:grid;place-items:center;flex:0 0 29px;border-radius:9px;font-size:10px}.ff-attention-grid em{margin-top:3px;color:#6d8c98;font-size:7px;font-style:normal}.ff-attention-grid strong{margin-top:2px;font-size:13px}.ff-attention-grid b{margin-left:auto}
@media(max-width:1100px){.ff-control-head{align-items:flex-start;flex-direction:column}.ff-head-tools{width:100%}.ff-date-card,.ff-energy-card{flex:1}.ff-stat-grid{grid-template-columns:repeat(3,1fr)}.ff-content-grid,.ff-bottom-grid{grid-template-columns:1fr}.ff-action-grid{grid-template-columns:repeat(4,1fr)}.ff-attention-grid{grid-template-columns:repeat(2,1fr)}}
@media(max-width:800px){.ff-stat-grid{grid-template-columns:repeat(2,1fr)}.ff-action-grid{grid-template-columns:repeat(2,1fr)}}
@media(max-width:560px){.ff-head-tools{flex-direction:column}.ff-date-card,.ff-energy-card{width:100%;min-width:0}.ff-stat-grid{grid-template-columns:1fr}.ff-panel{padding:14px}.ff-action-grid,.ff-attention-grid{grid-template-columns:1fr}.ff-status-grid{grid-template-columns:1fr 1fr}.ff-stat{padding:12px}.ff-stat strong{font-size:19px}.ff-control h1{font-size:30px}}
</style>
@endpush