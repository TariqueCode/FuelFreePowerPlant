@extends('layouts.portal')
@section('title','Help Desk')
@section('content')
<div class="hd-shell">
    <section class="hd-hero">
        <div>
            <div class="hd-eyebrow"><i class="fa-solid fa-headset"></i> CLIENT COMMUNICATION CENTER</div>
            <h1>Help Desk</h1>
            <p>One centralized workspace for website inquiries, official mailbox messages and career applications. Review, reply, organize and control every conversation from one place.</p>
        </div>
        <div class="hd-hero-badge"><span class="pulse"></span><div><strong>Help Desk Online</strong><small>Central communication hub</small></div></div>
    </section>

    @if(session('status'))<div class="hd-alert success"><i class="fa-solid fa-circle-check"></i><span>{{ session('status') }}</span></div>@endif
    @if($errors->any())<div class="hd-alert error"><i class="fa-solid fa-triangle-exclamation"></i><span>{{ $errors->first() }}</span></div>@endif

    <section class="hd-stats" aria-label="Help Desk overview">
        <article class="hd-stat"><div class="stat-icon"><i class="fa-solid fa-inbox"></i></div><div><span>Total conversations</span><strong>{{ number_format($contactCount + $careerCount) }}</strong><small>Contact + Career records</small></div></article>
        <article class="hd-stat accent"><div class="stat-icon"><i class="fa-solid fa-bolt"></i></div><div><span>Needs attention</span><strong>{{ number_format($openCount) }}</strong><small>{{ number_format($unreadCount) }} new</small></div></article>
        <article class="hd-stat"><div class="stat-icon"><i class="fa-solid fa-envelope"></i></div><div><span>Contact</span><strong>{{ number_format($contactCount) }}</strong><small>Website + info mailbox</small></div></article>
        <article class="hd-stat"><div class="stat-icon"><i class="fa-solid fa-briefcase"></i></div><div><span>Career</span><strong>{{ number_format($careerCount) }}</strong><small>Applications + career mailbox</small></div></article>
    </section>

    <section class="hd-toolbar">
        <form method="GET" action="{{ route('admin.helpdesk') }}" class="hd-search">
            <i class="fa-solid fa-magnifying-glass"></i>
            <input name="q" value="{{ $search }}" placeholder="Search name, email, subject or message…" aria-label="Search Help Desk">
            @if($channel !== 'all')<input type="hidden" name="channel" value="{{ $channel }}">@endif
            @if($status !== 'all')<input type="hidden" name="status" value="{{ $status }}">@endif
            <button type="submit">Search</button>
        </form>
        <div class="hd-filters">
            <a class="{{ $channel==='all'?'active':'' }}" href="{{ route('admin.helpdesk',array_filter(['q'=>$search,'status'=>$status!=='all'?$status:null])) }}">All</a>
            <a class="{{ $channel==='contact'?'active':'' }}" href="{{ route('admin.helpdesk',array_filter(['q'=>$search,'channel'=>'contact','status'=>$status!=='all'?$status:null])) }}"><i class="fa-solid fa-comments"></i> Contact</a>
            <a class="{{ $channel==='career'?'active':'' }}" href="{{ route('admin.helpdesk',array_filter(['q'=>$search,'channel'=>'career','status'=>$status!=='all'?$status:null])) }}"><i class="fa-solid fa-briefcase"></i> Career</a>
            <a class="{{ $status==='new'?'active':'' }}" href="{{ route('admin.helpdesk',array_filter(['q'=>$search,'channel'=>$channel!=='all'?$channel:null,'status'=>'new'])) }}"><i class="fa-solid fa-circle"></i> New</a>
            <a class="{{ $status==='replied'?'active':'' }}" href="{{ route('admin.helpdesk',array_filter(['q'=>$search,'channel'=>$channel!=='all'?$channel:null,'status'=>'replied'])) }}"><i class="fa-solid fa-reply"></i> Replied</a>
        </div>
    </section>

    <section class="hd-list-head">
        <div><h2>Conversation Inbox</h2><p>{{ number_format($items->total()) }} matching records · newest first</p></div>
        <span class="live-label"><span></span> Centralized inbox</span>
    </section>

    <section class="hd-table-card">
        <div class="hd-desktop-table">
            <table>
                <thead><tr><th>Channel</th><th>Sender</th><th>Subject & preview</th><th>Status</th><th>Received</th><th></th></tr></thead>
                <tbody>
                @forelse($items as $item)
                <tr>
                    <td><span class="channel {{ $item->channel }}"><i class="fa-solid {{ $item->channel==='career'?'fa-briefcase':'fa-comments' }}"></i>{{ ucfirst($item->channel) }}</span></td>
                    <td><div class="sender"><strong>{{ $item->name }}</strong><small>{{ $item->email }}</small></div></td>
                    <td><div class="subject"><strong>{{ $item->subject }}</strong><span>{{ \Illuminate\Support\Str::limit(trim(strip_tags($item->message ?? '')),90) }}</span></div></td>
                    <td><span class="status status-{{ $item->status }}">{{ str_replace('_',' ',ucfirst($item->status)) }}</span></td>
                    <td><time>{{ $item->received_at->format('d M Y') }}<small>{{ $item->received_at->format('h:i A') }}</small></time></td>
                    <td><a class="open-btn" href="{{ $item->route }}">Open <i class="fa-solid fa-arrow-right"></i></a></td>
                </tr>
                @empty
                <tr><td colspan="6"><div class="empty"><i class="fa-regular fa-folder-open"></i><strong>No conversations found</strong><span>Try another search or filter.</span></div></td></tr>
                @endforelse
                </tbody>
            </table>
        </div>

        <div class="hd-mobile-list">
            @forelse($items as $item)
            <a class="hd-ticket" href="{{ $item->route }}">
                <div class="ticket-top"><span class="channel {{ $item->channel }}"><i class="fa-solid {{ $item->channel==='career'?'fa-briefcase':'fa-comments' }}"></i>{{ ucfirst($item->channel) }}</span><span class="status status-{{ $item->status }}">{{ str_replace('_',' ',ucfirst($item->status)) }}</span></div>
                <strong>{{ $item->subject }}</strong>
                <p>{{ \Illuminate\Support\Str::limit(trim(strip_tags($item->message ?? '')),110) }}</p>
                <div class="ticket-meta"><span><i class="fa-regular fa-user"></i>{{ $item->name }}</span><time>{{ $item->received_at->format('d M Y, h:i A') }}</time></div>
            </a>
            @empty
            <div class="empty"><i class="fa-regular fa-folder-open"></i><strong>No conversations found</strong><span>Try another search or filter.</span></div>
            @endforelse
        </div>

        @if($items->hasPages())<div class="hd-pagination">{{ $items->links() }}</div>@endif
    </section>
</div>
@endsection

@push('styles')
<style>
.hd-shell{width:100%;max-width:1480px;margin:0 auto}.hd-hero{display:flex;justify-content:space-between;gap:24px;align-items:flex-end;padding:4px 0 28px}.hd-eyebrow{font-size:10px;font-weight:800;letter-spacing:.16em;color:#5fd4f1;text-transform:uppercase}.hd-eyebrow i{margin-right:7px}.hd-hero h1{font-size:clamp(30px,4vw,48px);line-height:1;margin:10px 0 13px;letter-spacing:-.035em}.hd-hero p{max-width:780px;margin:0;color:#88a8b5;font-size:14px;line-height:1.75}.hd-hero-badge{display:flex;align-items:center;gap:10px;padding:12px 15px;border:1px solid var(--line);border-radius:15px;background:rgba(67,194,229,.045);white-space:nowrap}.hd-hero-badge strong,.hd-hero-badge small{display:block}.hd-hero-badge strong{font-size:11px}.hd-hero-badge small{margin-top:3px;color:var(--muted);font-size:9px}.pulse,.live-label span{width:7px;height:7px;border-radius:50%;display:inline-block;background:#48d6a4;box-shadow:0 0 0 5px rgba(72,214,164,.08)}.hd-alert{display:flex;gap:10px;align-items:center;padding:12px 15px;border-radius:13px;margin-bottom:14px;font-size:12px}.hd-alert.success{border:1px solid rgba(72,214,164,.18);background:rgba(72,214,164,.06);color:#a9ead2}.hd-alert.error{border:1px solid rgba(255,100,100,.2);background:rgba(255,100,100,.06);color:#ffb0b0}.hd-stats{display:grid;grid-template-columns:repeat(4,minmax(0,1fr));gap:12px}.hd-stat{min-width:0;padding:17px;border:1px solid var(--line);border-radius:17px;background:linear-gradient(145deg,rgba(255,255,255,.035),rgba(255,255,255,.012));display:flex;gap:13px;align-items:center}.hd-stat.accent{border-color:rgba(95,212,241,.22);background:linear-gradient(145deg,rgba(67,194,229,.1),rgba(255,255,255,.015))}.stat-icon{width:40px;height:40px;flex:0 0 40px;display:grid;place-items:center;border-radius:12px;background:rgba(67,194,229,.08);color:#70d7ef}.hd-stat span,.hd-stat small{display:block;color:var(--muted);font-size:9px}.hd-stat strong{display:block;margin:3px 0;font-size:23px;letter-spacing:-.03em}.hd-toolbar{margin-top:22px;padding:11px;border:1px solid var(--line);border-radius:17px;background:rgba(255,255,255,.022);display:flex;gap:10px;align-items:center}.hd-search{min-width:260px;flex:1;display:flex;align-items:center;gap:9px;padding:0 6px 0 12px;background:#061721;border:1px solid rgba(104,204,235,.12);border-radius:12px;height:42px}.hd-search i{color:#648c99;font-size:12px}.hd-search input{min-width:0;flex:1;border:0;outline:0;background:transparent;color:var(--text);font-size:12px}.hd-search button{border:0;border-radius:9px;padding:8px 12px;background:#2da9ca;color:white;font-weight:700;font-size:11px;cursor:pointer}.hd-filters{display:flex;gap:5px;overflow:auto;scrollbar-width:none}.hd-filters::-webkit-scrollbar{display:none}.hd-filters a{display:inline-flex;align-items:center;gap:6px;white-space:nowrap;padding:9px 11px;border-radius:10px;color:#7897a3;text-decoration:none;font-size:10px;font-weight:700}.hd-filters a.active,.hd-filters a:hover{background:rgba(67,194,229,.1);color:#aee9f5}.hd-list-head{display:flex;justify-content:space-between;align-items:flex-end;gap:12px;margin:28px 2px 11px}.hd-list-head h2{margin:0;font-size:18px;letter-spacing:-.02em}.hd-list-head p{margin:5px 0 0;color:var(--muted);font-size:10px}.live-label{display:flex;gap:8px;align-items:center;color:#7897a3;font-size:9px}.hd-table-card{border:1px solid var(--line);border-radius:18px;background:rgba(255,255,255,.018);overflow:hidden}.hd-desktop-table{overflow-x:auto}table{width:100%;border-collapse:collapse;min-width:900px}th,td{padding:14px 16px;text-align:left;border-bottom:1px solid rgba(255,255,255,.055)}th{font-size:9px;letter-spacing:.1em;text-transform:uppercase;color:#5e91a0;background:rgba(67,194,229,.025)}td{font-size:11px;color:#b9cdd4}.channel{display:inline-flex;align-items:center;gap:6px;padding:6px 8px;border-radius:8px;font-size:9px;font-weight:800}.channel.contact{background:rgba(67,194,229,.08);color:#8ddcf0}.channel.career{background:rgba(160,120,255,.09);color:#c4a9ff}.sender strong,.sender small,.subject strong,.subject span,time,time small{display:block}.sender strong{color:#e1eef2;font-size:11px}.sender small,.subject span,time small{color:#7895a0;font-size:9px;margin-top:4px}.subject strong{max-width:330px;white-space:nowrap;overflow:hidden;text-overflow:ellipsis;color:#cfe0e5;font-size:11px}.subject span{max-width:330px;white-space:nowrap;overflow:hidden;text-overflow:ellipsis}.status{display:inline-block;padding:5px 8px;border-radius:999px;background:rgba(255,255,255,.055);color:#a5bbc3;font-size:8px;font-weight:800;text-transform:capitalize}.status-new{background:rgba(255,186,75,.1);color:#ffd08a}.status-replied{background:rgba(72,214,164,.09);color:#8be3c3}.status-in_progress,.status-reviewing{background:rgba(67,194,229,.09);color:#8edff2}.status-closed,.status-rejected{opacity:.65}.open-btn{display:inline-flex;align-items:center;gap:7px;color:#82d8ee;text-decoration:none;font-size:10px;font-weight:800}.open-btn i{font-size:9px}.hd-mobile-list{display:none}.empty{padding:60px 20px;text-align:center;color:#7895a0}.empty i{display:block;font-size:28px;margin-bottom:12px;color:#4f7c88}.empty strong,.empty span{display:block}.empty strong{color:#c1d3d9;font-size:13px}.empty span{font-size:10px;margin-top:5px}.hd-pagination{padding:14px 16px;border-top:1px solid rgba(255,255,255,.055)}
@media(max-width:1000px){.hd-stats{grid-template-columns:repeat(2,minmax(0,1fr))}.hd-toolbar{align-items:stretch;flex-direction:column}.hd-search{width:100%}.hd-filters{width:100%}}
@media(max-width:700px){.hd-hero{display:block;padding-bottom:18px}.hd-hero h1{font-size:34px}.hd-hero p{font-size:12px;line-height:1.65}.hd-hero-badge{margin-top:15px;width:max-content}.hd-stats{grid-template-columns:1fr 1fr;gap:8px}.hd-stat{padding:12px;gap:9px;border-radius:14px}.stat-icon{width:34px;height:34px;flex-basis:34px;border-radius:10px}.hd-stat strong{font-size:19px}.hd-stat span,.hd-stat small{font-size:8px}.hd-toolbar{margin-top:15px;padding:9px;border-radius:14px}.hd-search{height:40px}.hd-search button{padding:7px 10px}.hd-filters a{padding:8px 9px}.hd-list-head{margin-top:21px}.hd-list-head h2{font-size:16px}.live-label{display:none}.hd-desktop-table{display:none}.hd-mobile-list{display:block;padding:8px}.hd-ticket{display:block;padding:14px;border:1px solid rgba(104,204,235,.1);border-radius:14px;background:rgba(67,194,229,.025);text-decoration:none;margin:6px 0;color:inherit}.ticket-top{display:flex;justify-content:space-between;gap:8px;align-items:center}.hd-ticket>strong{display:block;margin-top:12px;color:#d9e8ec;font-size:12px;line-height:1.45}.hd-ticket p{margin:6px 0 11px;color:#7f9ba5;font-size:10px;line-height:1.55}.ticket-meta{display:flex;justify-content:space-between;gap:10px;color:#718e99;font-size:9px}.ticket-meta span{white-space:nowrap;overflow:hidden;text-overflow:ellipsis}.ticket-meta i{margin-right:5px}.hd-pagination{padding:11px 9px}.hd-pagination nav{overflow:auto}.hd-alert{font-size:10px}.hd-shell{max-width:none}}
</style>
@endpush