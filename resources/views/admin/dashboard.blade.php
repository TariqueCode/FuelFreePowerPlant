@extends('layouts.portal')
@section('title','Admin Dashboard')
@section('content')
@php
    $totalPlants=(int)($plantStats['total']??0);
    $operationalPlants=(int)($plantStats['operational']??0);
    $plannedPlants=(int)($plantStats['planned']??0);
    $maintenancePlants=(int)($plantStats['maintenance']??0);
    $offlinePlants=(int)($plantStats['offline']??0);
    $efficiency=$performanceSummary['efficiency']??null;
    $uptime=$performanceSummary['uptime']??null;
    $output=$performanceSummary['output_kw']??null;
    $energy=$performanceSummary['energy_kwh']??null;
@endphp

<section class="ff-dashboard" aria-label="Administration dashboard">
    <header class="ff-dashboard-head">
        <div class="ff-dashboard-copy">
            <div class="ff-eyebrow"><span class="ff-eyebrow-dot"></span> ADMIN DASHBOARD</div>
            <h1>Welcome back, <span>{{ auth()->user()->name }}</span></h1>
            <p>Manage your website, content and operations from one clean workspace.</p>
        </div>
        <div class="ff-date-card">
            <span class="ff-date-icon"><i class="fa-regular fa-calendar"></i></span>
            <div><strong>{{ now()->format('l, d M Y') }}</strong><small>{{ now()->format('h:i A') }}</small></div>
        </div>
    </header>

    <section class="ff-stat-grid" aria-label="Website statistics">
        <a class="ff-stat" href="{{ route('admin.site-content.index') }}">
            <span class="ff-stat-icon ff-icon-blue"><i class="fa-regular fa-file-lines"></i></span>
            <span class="ff-stat-body"><small>Website Content</small><strong>{{ number_format($users ?? 0) }}</strong><em>Content records</em></span>
            <i class="fa-solid fa-arrow-up-right-from-square ff-stat-arrow"></i>
        </a>
        <a class="ff-stat" href="{{ route('admin.sliders.index') }}">
            <span class="ff-stat-icon ff-icon-green"><i class="fa-regular fa-images"></i></span>
            <span class="ff-stat-body"><small>Homepage Sliders</small><strong>{{ number_format(\App\Models\SiteSlider::query()->count()) }}</strong><em>Configured slides</em></span>
            <i class="fa-solid fa-arrow-up-right-from-square ff-stat-arrow"></i>
        </a>
        <a class="ff-stat" href="{{ route('admin.site-content.index',['type'=>'news']) }}">
            <span class="ff-stat-icon ff-icon-purple"><i class="fa-regular fa-newspaper"></i></span>
            <span class="ff-stat-body"><small>News &amp; Event</small><strong>{{ number_format(\App\Models\SiteContentItem::query()->where('type','news')->count()) }}</strong><em>Published &amp; draft</em></span>
            <i class="fa-solid fa-arrow-up-right-from-square ff-stat-arrow"></i>
        </a>
        <a class="ff-stat" href="{{ route('admin.gallery.index') }}">
            <span class="ff-stat-icon ff-icon-amber"><i class="fa-regular fa-images"></i></span>
            <span class="ff-stat-body"><small>Gallery Albums</small><strong>{{ number_format(\App\Models\Gallery::query()->count()) }}</strong><em>Photo collections</em></span>
            <i class="fa-solid fa-arrow-up-right-from-square ff-stat-arrow"></i>
        </a>
        @if(auth()->user()->hasPermission('inquiries.view'))
        <a class="ff-stat" href="{{ route('admin.inquiries.index') }}">
            <span class="ff-stat-icon ff-icon-cyan"><i class="fa-regular fa-comments"></i></span>
            <span class="ff-stat-body"><small>Inquiries</small><strong>{{ number_format(\App\Models\Inquiry::query()->count()) }}</strong><em>Website messages</em></span>
            <i class="fa-solid fa-arrow-up-right-from-square ff-stat-arrow"></i>
        </a>
        @endif
    </section>

    <div class="ff-main-grid">
        <section class="ff-panel ff-actions-panel">
            <div class="ff-panel-head">
                <div><span class="ff-section-kicker">QUICK ACCESS</span><h2>Common tasks</h2><p>Jump directly to the tools you use most.</p></div>
                <a class="ff-outline-btn" href="{{ url('/') }}" target="_blank" rel="noopener noreferrer">View website <i class="fa-solid fa-arrow-up-right-from-square"></i></a>
            </div>
            <div class="ff-action-grid">
                @if(auth()->user()->hasPermission('website.view'))
                <a href="{{ route('admin.homepage-builder.index') }}"><i class="fa-solid fa-house"></i><span><strong>Edit Homepage</strong><small>Manage homepage sections</small></span><b>→</b></a>
                <a href="{{ route('admin.sliders.index') }}"><i class="fa-regular fa-images"></i><span><strong>Manage Sliders</strong><small>Update rotating banners</small></span><b>→</b></a>
                <a href="{{ route('admin.site-content.index',['type'=>'news']) }}"><i class="fa-regular fa-newspaper"></i><span><strong>News &amp; Event</strong><small>Publish website updates</small></span><b>→</b></a>
                @endif
                @if(auth()->user()->hasPermission('documents.view'))
                <a href="{{ route('admin.documents') }}"><i class="fa-solid fa-cloud-arrow-up"></i><span><strong>Documents &amp; Media</strong><small>Manage secure files</small></span><b>→</b></a>
                @endif
                @if(auth()->user()->hasPermission('users.view'))
                <a href="{{ route('admin.users.index') }}"><i class="fa-solid fa-users"></i><span><strong>Manage Users</strong><small>Accounts &amp; access</small></span><b>→</b></a>
                @endif
                @if(auth()->user()->hasPermission('inquiries.view'))
                <a href="{{ route('admin.inquiries.index') }}"><i class="fa-regular fa-comments"></i><span><strong>View Inquiries</strong><small>Website messages</small></span><b>→</b></a>
                @endif
                @if(auth()->user()->hasPermission('mail.view'))
                <a href="{{ route('admin.mail') }}"><i class="fa-regular fa-envelope"></i><span><strong>Check Webmail</strong><small>Manage mail accounts</small></span><b>→</b></a>
                @endif
                @if(auth()->user()->hasPermission('settings.manage'))
                <a href="{{ route('admin.settings') }}"><i class="fa-solid fa-sliders"></i><span><strong>Open Settings</strong><small>System configuration</small></span><b>→</b></a>
                @endif
            </div>
        </section>

        <aside class="ff-panel ff-performance-panel">
            <div class="ff-panel-head"><div><span class="ff-section-kicker">POWER PLANT</span><h2>Performance</h2><p>Latest verified indicators.</p></div><span class="ff-live-pill"><i></i> Live</span></div>
            <div class="ff-performance-number"><strong>{{ $output!==null?number_format((float)$output,1).' kW':'—' }}</strong><span>Verified output</span></div>
            <div class="ff-progress-item"><div><span>Efficiency</span><strong>{{ $efficiency!==null?number_format((float)$efficiency,1).'%':'—' }}</strong></div><div class="ff-progress"><i style="width:{{ min(100,max(0,(float)($efficiency??0))) }}%"></i></div></div>
            <div class="ff-progress-item"><div><span>Uptime</span><strong>{{ $uptime!==null?number_format((float)$uptime,1).'%':'—' }}</strong></div><div class="ff-progress"><i style="width:{{ min(100,max(0,(float)($uptime??0))) }}%"></i></div></div>
            <div class="ff-energy-row"><span>Energy generated</span><strong>{{ $energy!==null?number_format((float)$energy,1).' kWh':'—' }}</strong></div>
            <div class="ff-energy-row"><span>Verified records</span><strong>{{ number_format($verifiedRecordCount??0) }}</strong></div>
        </aside>
    </div>

    <div class="ff-bottom-grid">
        <section class="ff-panel ff-plant-panel">
            <div class="ff-panel-head"><div><span class="ff-section-kicker">PLANT PORTFOLIO</span><h2>Operational overview</h2></div><a class="ff-text-link" href="{{ route('admin.plants.index') }}">Manage plants <i class="fa-solid fa-arrow-right"></i></a></div>
            <div class="ff-plant-summary"><div class="ff-plant-total"><strong>{{ number_format($totalPlants) }}</strong><span>Total plants</span></div><div class="ff-plant-list"><div><i class="ff-dot ff-dot-green"></i><span>Operational</span><b>{{ $operationalPlants }}</b></div><div><i class="ff-dot ff-dot-purple"></i><span>Planned</span><b>{{ $plannedPlants }}</b></div><div><i class="ff-dot ff-dot-amber"></i><span>Maintenance</span><b>{{ $maintenancePlants }}</b></div><div><i class="ff-dot ff-dot-muted"></i><span>Offline</span><b>{{ $offlinePlants }}</b></div></div></div>
        </section>
        <section class="ff-panel ff-storage-panel">
            <div class="ff-panel-head"><div><span class="ff-section-kicker">FILE STORAGE</span><h2>Storage usage</h2><p>Documents and media space.</p></div><i class="fa-solid fa-database ff-panel-icon"></i></div>
            @php $storageGb=(float)(($storageBytes??0)/1073741824); $storagePct=min(100,$storageGb>0?($storageGb/1)*100:0); @endphp
            <div class="ff-storage-value"><strong>{{ number_format($storageGb,2) }} GB</strong><span>Current usage</span></div>
            <div class="ff-progress"><i style="width:{{ $storagePct }}%"></i></div>
            <div class="ff-storage-meta"><span>Used {{ number_format($storageGb,2) }} GB</span><span>Reference limit 1 GB</span></div>
        </section>
    </div>
</section>

@push('styles')
<style>
.ff-dashboard{--fd-panel:linear-gradient(145deg,rgba(8,31,40,.88),rgba(3,18,25,.82));--fd-border:rgba(92,215,190,.13);--fd-muted:#7f9da7;--fd-text:#edf9f8;max-width:1480px;margin:0 auto}.ff-dashboard-head{display:flex;align-items:flex-end;justify-content:space-between;gap:22px;padding:4px 0 26px}.ff-dashboard-copy{min-width:0}.ff-eyebrow,.ff-section-kicker{display:flex;align-items:center;gap:8px;color:#62cce5;font-size:10px;font-weight:800;letter-spacing:.16em}.ff-eyebrow-dot{width:6px;height:6px;border-radius:50%;background:#42e596;box-shadow:0 0 12px rgba(66,229,150,.7)}.ff-dashboard h1{margin:9px 0 8px;font-size:clamp(30px,3.5vw,48px);line-height:1.04;letter-spacing:-.035em}.ff-dashboard h1 span{background:linear-gradient(100deg,#eefcf9,#5fe1ee);-webkit-background-clip:text;background-clip:text;color:transparent}.ff-dashboard-head p{margin:0;color:var(--fd-muted);font-size:14px;line-height:1.6}.ff-date-card{display:flex;align-items:center;gap:11px;flex:0 0 auto;padding:12px 15px;border:1px solid var(--fd-border);border-radius:15px;background:rgba(5,28,37,.62)}.ff-date-icon{width:38px;height:38px;display:grid;place-items:center;border-radius:11px;color:#63dff0;background:rgba(85,217,239,.08)}.ff-date-card strong,.ff-date-card small{display:block}.ff-date-card strong{font-size:12px}.ff-date-card small{margin-top:3px;color:#7797a1;font-size:10px}.ff-stat-grid{display:grid;grid-template-columns:repeat(5,minmax(0,1fr));gap:11px}.ff-stat{position:relative;display:flex;align-items:center;gap:11px;min-width:0;padding:14px;border:1px solid var(--fd-border);border-radius:17px;background:var(--fd-panel);color:inherit;text-decoration:none;transition:transform .2s ease,border-color .2s ease,box-shadow .2s ease}.ff-stat:hover{transform:translateY(-2px);border-color:rgba(92,215,190,.28);box-shadow:0 18px 45px rgba(0,0,0,.22)}.ff-stat-icon{width:42px;height:42px;flex:0 0 42px;display:grid;place-items:center;border-radius:12px;font-size:16px}.ff-icon-blue{background:rgba(91,156,255,.12);color:#73aaff}.ff-icon-green{background:rgba(50,229,138,.11);color:#64eea1}.ff-icon-purple{background:rgba(154,124,244,.12);color:#b298ff}.ff-icon-amber{background:rgba(240,173,92,.12);color:#f5bd70}.ff-icon-cyan{background:rgba(85,217,239,.12);color:#66e3f4}.ff-stat-body{min-width:0}.ff-stat-body small,.ff-stat-body em{display:block;color:#7896a0;font-size:9px;font-style:normal}.ff-stat-body strong{display:block;margin:4px 0 2px;font-size:21px}.ff-stat-arrow{margin-left:auto;color:#5f8793;font-size:10px}.ff-main-grid{display:grid;grid-template-columns:minmax(0,1.55fr) minmax(300px,.75fr);gap:12px;margin-top:12px}.ff-bottom-grid{display:grid;grid-template-columns:1.25fr .75fr;gap:12px;margin-top:12px}.ff-panel{min-width:0;padding:20px;border:1px solid var(--fd-border);border-radius:19px;background:var(--fd-panel);box-shadow:0 18px 55px rgba(0,0,0,.14)}.ff-panel-head{display:flex;align-items:flex-start;justify-content:space-between;gap:15px}.ff-panel-head h2{margin:4px 0 3px;font-size:18px;letter-spacing:-.02em}.ff-panel-head p{margin:0;color:#708f99;font-size:11px}.ff-outline-btn,.ff-text-link{color:#bdebf1;text-decoration:none}.ff-outline-btn{display:inline-flex;align-items:center;gap:7px;padding:8px 11px;border:1px solid rgba(85,217,239,.15);border-radius:10px;background:rgba(85,217,239,.05);font-size:10px;white-space:nowrap}.ff-text-link{font-size:10px;padding-top:4px}.ff-action-grid{display:grid;grid-template-columns:repeat(4,minmax(0,1fr));gap:9px;margin-top:17px}.ff-action-grid a{display:flex;align-items:center;gap:10px;min-width:0;padding:12px;border:1px solid rgba(92,215,190,.1);border-radius:13px;background:rgba(3,19,26,.48);color:inherit;text-decoration:none;transition:background .18s,border-color .18s,transform .18s}.ff-action-grid a:hover{background:rgba(50,229,138,.045);border-color:rgba(92,215,190,.24);transform:translateY(-1px)}.ff-action-grid a>i{width:30px;height:30px;display:grid;place-items:center;flex:0 0 30px;border-radius:9px;background:rgba(85,217,239,.07);color:#62d9ed;font-size:12px}.ff-action-grid span{min-width:0;flex:1}.ff-action-grid strong,.ff-action-grid small{display:block;overflow:hidden;text-overflow:ellipsis;white-space:nowrap}.ff-action-grid strong{font-size:10px}.ff-action-grid small{margin-top:3px;color:#6d8d97;font-size:8px}.ff-action-grid b{color:#668c97;font-size:14px;font-weight:400}.ff-live-pill{display:inline-flex;align-items:center;gap:6px;padding:5px 8px;border:1px solid rgba(50,229,138,.15);border-radius:999px;color:#69eca2;background:rgba(50,229,138,.05);font-size:9px}.ff-live-pill i{width:6px;height:6px;border-radius:50%;background:#49e793;box-shadow:0 0 9px rgba(73,231,147,.8)}.ff-performance-number{padding:22px 0 16px}.ff-performance-number strong{display:block;font-size:30px;letter-spacing:-.03em}.ff-performance-number span{color:#6f8e98;font-size:10px}.ff-progress-item{margin-top:10px}.ff-progress-item>div:first-child{display:flex;justify-content:space-between;margin-bottom:7px;color:#829fa8;font-size:10px}.ff-progress-item strong{color:#dff7f3}.ff-progress{height:7px;overflow:hidden;border-radius:99px;background:rgba(255,255,255,.055)}.ff-progress i{display:block;height:100%;border-radius:inherit;background:linear-gradient(90deg,#3bcf92,#62dff0)}.ff-energy-row{display:flex;justify-content:space-between;padding:10px 0;border-top:1px solid rgba(92,215,190,.07);margin-top:10px;color:#75939c;font-size:10px}.ff-energy-row strong{color:#dff5f2}.ff-plant-summary{display:grid;grid-template-columns:180px 1fr;gap:20px;margin-top:22px}.ff-plant-total{display:flex;flex-direction:column;justify-content:center;padding:18px;border:1px solid rgba(92,215,190,.1);border-radius:14px;background:rgba(2,17,24,.4)}.ff-plant-total strong{font-size:36px;line-height:1}.ff-plant-total span{margin-top:6px;color:#75939d;font-size:10px}.ff-plant-list{display:grid;grid-template-columns:repeat(2,minmax(0,1fr));gap:8px}.ff-plant-list div{display:flex;align-items:center;gap:8px;padding:11px;border-bottom:1px solid rgba(92,215,190,.07);color:#819da5;font-size:10px}.ff-plant-list b{margin-left:auto;color:#e3f4f1}.ff-dot{width:7px;height:7px;border-radius:50%;flex:0 0 7px}.ff-dot-green{background:#49e793}.ff-dot-purple{background:#a58aff}.ff-dot-amber{background:#f2b466}.ff-dot-muted{background:#657b84}.ff-storage-panel{display:flex;flex-direction:column}.ff-panel-icon{color:#5edcf0;font-size:17px}.ff-storage-value{margin-top:27px}.ff-storage-value strong{display:block;font-size:28px}.ff-storage-value span{display:block;margin-top:4px;color:#728f99;font-size:10px}.ff-storage-panel .ff-progress{margin-top:18px;height:9px}.ff-storage-meta{display:flex;justify-content:space-between;gap:10px;margin-top:9px;color:#6d8a94;font-size:9px}.ff-storage-meta span:last-child{text-align:right}
@media(max-width:1180px){.ff-stat-grid{grid-template-columns:repeat(3,minmax(0,1fr))}.ff-main-grid,.ff-bottom-grid{grid-template-columns:1fr}.ff-action-grid{grid-template-columns:repeat(4,minmax(0,1fr))}}
@media(max-width:760px){.ff-dashboard-head{align-items:flex-start;flex-direction:column;padding-bottom:20px}.ff-date-card{width:100%}.ff-stat-grid{grid-template-columns:repeat(2,minmax(0,1fr))}.ff-action-grid{grid-template-columns:repeat(2,minmax(0,1fr))}.ff-panel{padding:16px;border-radius:16px}.ff-panel-head{gap:10px}.ff-outline-btn{font-size:9px}.ff-plant-summary{grid-template-columns:1fr}.ff-plant-total{min-height:105px}}
@media(max-width:460px){.ff-stat-grid,.ff-action-grid{grid-template-columns:1fr}.ff-stat{padding:12px}.ff-stat-body strong{font-size:19px}.ff-dashboard h1{font-size:29px}.ff-dashboard-head p{font-size:13px}.ff-panel-head{flex-direction:column}.ff-outline-btn{align-self:flex-start}.ff-plant-list{grid-template-columns:1fr}.ff-storage-meta{flex-direction:column}.ff-storage-meta span:last-child{text-align:left}}
</style>
@endpush
@endsection
