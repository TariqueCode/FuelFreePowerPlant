@extends('layouts.portal')
@section('title','Help Desk')
@section('content')
<div class="hd-shell">
    <header class="hd-header">
        <div class="hd-title"><span class="hd-title-icon"><i class="fa-solid fa-headset"></i></span><div><h1>Help Desk</h1><p>All client communications in one place</p></div></div>
        <div class="hd-summary"><span><b>{{ number_format($items->total()) }}</b> results</span><span class="summary-dot"></span><span>{{ number_format($openCount) }} open</span></div>
    </header>

    @if(session('status'))<div class="hd-alert success"><i class="fa-solid fa-circle-check"></i><span>{{ session('status') }}</span></div>@endif
    @if($errors->any())<div class="hd-alert error"><i class="fa-solid fa-triangle-exclamation"></i><span>{{ $errors->first() }}</span></div>@endif

    <section class="hd-toolbar">
        <form method="GET" action="{{ route('admin.helpdesk') }}" class="hd-search">
            <i class="fa-solid fa-magnifying-glass"></i>
            <input name="q" value="{{ $search }}" placeholder="Search conversations…" aria-label="Search Help Desk">
            @if($channel !== 'all')<input type="hidden" name="channel" value="{{ $channel }}">@endif
            @if($status !== 'all')<input type="hidden" name="status" value="{{ $status }}">@endif
            <button type="submit">Search</button>
        </form>
        <div class="hd-filters">
            <a class="{{ $channel==='all'?'active':'' }}" href="{{ route('admin.helpdesk',array_filter(['q'=>$search,'status'=>$status!=='all'?$status:null])) }}">All <em>{{ number_format($contactCount + $careerCount + ($emailCount ?? 0)) }}</em></a>
            <a class="{{ $channel==='contact'?'active':'' }}" href="{{ route('admin.helpdesk',array_filter(['q'=>$search,'channel'=>'contact','status'=>$status!=='all'?$status:null])) }}"><i class="fa-solid fa-comments"></i> Contact <em>{{ number_format($contactCount) }}</em></a>
            <a class="{{ $channel==='career'?'active':'' }}" href="{{ route('admin.helpdesk',array_filter(['q'=>$search,'channel'=>'career','status'=>$status!=='all'?$status:null])) }}"><i class="fa-solid fa-briefcase"></i> Career <em>{{ number_format($careerCount) }}</em></a>
            <a class="{{ $status==='new'?'active':'' }}" href="{{ route('admin.helpdesk',array_filter(['q'=>$search,'channel'=>$channel!=='all'?$channel:null,'status'=>'new'])) }}"><i class="fa-solid fa-circle"></i> New <em>{{ number_format($unreadCount) }}</em></a>
            <a class="{{ $status==='replied'?'active':'' }}" href="{{ route('admin.helpdesk',array_filter(['q'=>$search,'channel'=>$channel!=='all'?$channel:null,'status'=>'replied'])) }}"><i class="fa-solid fa-reply"></i> Replied <em>{{ number_format($repliedCount) }}</em></a>
        </div>
    </section>

    <section class="hd-list-head">
        <div><h2>Inbox</h2><p>Newest conversations first</p></div>
        <span class="live-label"><span></span> Live workspace</span>
    </section>

    <section class="hd-table-card">
        <div class="hd-desktop-table">
            <table>
                <thead><tr><th>Type</th><th>Sender</th><th>Subject</th><th>Status</th><th>Received</th><th>Actions</th></tr></thead>
                <tbody>
                @forelse($items as $item)
                <tr>
                    <td><span class="channel {{ $item->channel }}"><i class="fa-solid {{ $item->channel==='career'?'fa-briefcase':'fa-comments' }}"></i>{{ ucfirst($item->channel) }}</span></td>
                    <td><div class="sender"><strong>{{ $item->name }}</strong><small>{{ $item->email }}</small></div></td>
                    <td><div class="subject"><strong>{{ $item->subject }}</strong><span>{{ \Illuminate\Support\Str::limit(trim(strip_tags($item->message ?? '')),90) }}</span></div></td>
                    <td><span class="status status-{{ $item->status }}">{{ str_replace('_',' ',ucfirst($item->status)) }}</span></td>
                    <td><time>{{ $item->received_at->format('d M Y') }}<small>{{ $item->received_at->format('h:i A') }}</small></time></td>
                    <td><div class="row-actions"><a class="open-btn" href="{{ $item->route }}">Open <i class="fa-solid fa-arrow-right"></i></a>
                    @if(auth()->user()->hasPermission('mail.manage'))
                    <form method="POST" action="{{ route('admin.helpdesk.delete',[$item->type,$item->id]) }}" onsubmit="return confirm('Permanently delete this Help Desk record?')">@csrf @method('DELETE')<button class="icon-delete" type="submit" title="Delete"><i class="fa-solid fa-trash-can"></i></button></form>
                    @endif</div></td>
                </tr>
                @empty
                <tr><td colspan="6"><div class="empty"><i class="fa-regular fa-folder-open"></i><strong>No conversations found</strong><span>Try another search or filter.</span></div></td></tr>
                @endforelse
                </tbody>
            </table>
        </div>

        <div class="hd-mobile-list">
            @forelse($items as $item)
            <article class="hd-ticket">
                <a class="ticket-main" href="{{ $item->route }}">
                    <div class="ticket-top"><span class="channel {{ $item->channel }}"><i class="fa-solid {{ $item->channel==='career'?'fa-briefcase':'fa-comments' }}"></i>{{ ucfirst($item->channel) }}</span><span class="status status-{{ $item->status }}">{{ str_replace('_',' ',ucfirst($item->status)) }}</span></div>
                    <strong>{{ $item->subject }}</strong>
                    <p>{{ \Illuminate\Support\Str::limit(trim(strip_tags($item->message ?? '')),110) }}</p>
                    <div class="ticket-meta"><span><i class="fa-regular fa-user"></i>{{ $item->name }}</span><time>{{ $item->received_at->format('d M Y, h:i A') }}</time></div>
                </a>
                @if(auth()->user()->hasPermission('mail.manage'))
                <form method="POST" action="{{ route('admin.helpdesk.delete',[$item->type,$item->id]) }}" onsubmit="return confirm('Permanently delete this Help Desk record?')">@csrf @method('DELETE')<button class="mobile-delete" type="submit"><i class="fa-solid fa-trash-can"></i><span>Delete</span></button></form>
                @endif
            </article>
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
.hd-shell{width:100%;max-width:1480px;margin:0 auto}.hd-header{display:flex;justify-content:space-between;align-items:center;gap:16px;padding:0 0 18px;border-bottom:1px solid rgba(255,255,255,.055)}.hd-title{display:flex;align-items:center;gap:12px}.hd-title-icon{width:42px;height:42px;display:grid;place-items:center;border-radius:12px;background:rgba(67,194,229,.09);border:1px solid rgba(104,204,235,.12);color:#72d8ef}.hd-title h1{margin:0;font-size:clamp(24px,3vw,34px);letter-spacing:-.035em}.hd-title p{margin:4px 0 0;color:#718f9a;font-size:10px}.hd-summary{display:flex;align-items:center;gap:9px;color:#7895a0;font-size:9px}.hd-summary b{color:#cfe1e6;font-size:11px}.summary-dot{width:4px;height:4px;border-radius:50%;background:#48d6a4}.hd-alert{display:flex;gap:9px;align-items:center;padding:10px 13px;border-radius:11px;margin-top:12px;font-size:10px}.hd-alert.success{border:1px solid rgba(72,214,164,.18);background:rgba(72,214,164,.06);color:#a9ead2}.hd-alert.error{border:1px solid rgba(255,100,100,.2);background:rgba(255,100,100,.06);color:#ffb0b0}.hd-toolbar{margin-top:14px;padding:9px;border:1px solid var(--line);border-radius:15px;background:rgba(255,255,255,.018);display:flex;gap:9px;align-items:center}.hd-search{min-width:240px;flex:1;display:flex;align-items:center;gap:8px;padding:0 5px 0 11px;background:#061721;border:1px solid rgba(104,204,235,.1);border-radius:10px;height:40px}.hd-search i{color:#648c99;font-size:11px}.hd-search input{min-width:0;flex:1;border:0;outline:0;background:transparent;color:var(--text);font-size:11px}.hd-search button{border:0;border-radius:8px;padding:7px 11px;background:#2da9ca;color:#fff;font-weight:700;font-size:10px;cursor:pointer}.hd-filters{display:flex;gap:4px;overflow:auto;scrollbar-width:none}.hd-filters::-webkit-scrollbar{display:none}.hd-filters a{display:inline-flex;align-items:center;gap:5px;white-space:nowrap;padding:8px 9px;border-radius:9px;color:#7897a3;text-decoration:none;font-size:9px;font-weight:700}.hd-filters a em{font-style:normal;color:#5e7e89;font-size:8px}.hd-filters a.active,.hd-filters a:hover{background:rgba(67,194,229,.1);color:#aee9f5}.hd-filters a.active em{color:#8edff2}.hd-list-head{display:flex;justify-content:space-between;align-items:flex-end;gap:12px;margin:18px 2px 9px}.hd-list-head h2{margin:0;font-size:15px;letter-spacing:-.02em}.hd-list-head p{margin:3px 0 0;color:#687f89;font-size:9px}.live-label{display:flex;gap:7px;align-items:center;color:#7897a3;font-size:8px}.live-label span{width:6px;height:6px;border-radius:50%;background:#48d6a4;box-shadow:0 0 0 4px rgba(72,214,164,.07)}.hd-table-card{border:1px solid var(--line);border-radius:15px;background:rgba(255,255,255,.015);overflow:hidden}.hd-desktop-table{overflow-x:auto}table{width:100%;border-collapse:collapse;min-width:900px}th,td{padding:12px 14px;text-align:left;border-bottom:1px solid rgba(255,255,255,.05)}th{font-size:8px;letter-spacing:.1em;text-transform:uppercase;color:#5e91a0;background:rgba(67,194,229,.025)}td{font-size:10px;color:#b9cdd4}.channel{display:inline-flex;align-items:center;gap:5px;padding:5px 7px;border-radius:7px;font-size:8px;font-weight:800}.channel.contact{background:rgba(67,194,229,.08);color:#8ddcf0}.channel.career{background:rgba(160,120,255,.09);color:#c4a9ff}.sender strong,.sender small,.subject strong,.subject span,time,time small{display:block}.sender strong{color:#e1eef2;font-size:10px}.sender small,.subject span,time small{color:#7895a0;font-size:8px;margin-top:3px}.subject strong{max-width:310px;white-space:nowrap;overflow:hidden;text-overflow:ellipsis;color:#cfe0e5;font-size:10px}.subject span{max-width:310px;white-space:nowrap;overflow:hidden;text-overflow:ellipsis}.status{display:inline-block;padding:5px 7px;border-radius:999px;background:rgba(255,255,255,.055);color:#a5bbc3;font-size:7px;font-weight:800;text-transform:capitalize}.status-new{background:rgba(255,186,75,.1);color:#ffd08a}.status-replied{background:rgba(72,214,164,.09);color:#8be3c3}.status-in_progress,.status-reviewing{background:rgba(67,194,229,.09);color:#8edff2}.status-closed,.status-rejected{opacity:.65}.row-actions{display:flex;align-items:center;gap:10px}.open-btn{display:inline-flex;align-items:center;gap:6px;color:#82d8ee;text-decoration:none;font-size:9px;font-weight:800}.open-btn i{font-size:8px}.icon-delete{display:grid;place-items:center;width:29px;height:29px;border:1px solid rgba(255,100,100,.12);border-radius:8px;background:rgba(255,100,100,.045);color:#d88787;cursor:pointer}.icon-delete:hover{background:rgba(255,100,100,.1);color:#ff9d9d;border-color:rgba(255,100,100,.24)}.hd-mobile-list{display:none}.empty{padding:55px 20px;text-align:center;color:#7895a0}.empty i{display:block;font-size:25px;margin-bottom:10px;color:#4f7c88}.empty strong,.empty span{display:block}.empty strong{color:#c1d3d9;font-size:12px}.empty span{font-size:9px;margin-top:4px}.hd-pagination{padding:12px 14px;border-top:1px solid rgba(255,255,255,.05)}
@media(max-width:1000px){.hd-toolbar{align-items:stretch;flex-direction:column}.hd-search{width:100%;box-sizing:border-box}.hd-filters{width:100%}}
@media(max-width:700px){.hd-header{padding-bottom:13px}.hd-title{gap:9px}.hd-title-icon{width:36px;height:36px;border-radius:10px}.hd-title h1{font-size:25px}.hd-title p{font-size:8px}.hd-summary{font-size:8px}.hd-summary b{font-size:10px}.hd-toolbar{margin-top:10px;padding:7px;border-radius:13px}.hd-search{height:38px}.hd-search button{padding:7px 9px}.hd-filters a{padding:7px 8px}.hd-list-head{margin-top:14px}.hd-list-head h2{font-size:14px}.live-label{display:none}.hd-desktop-table{display:none}.hd-mobile-list{display:block;padding:7px}.hd-ticket{display:flex;align-items:stretch;margin:5px 0;border:1px solid rgba(104,204,235,.09);border-radius:13px;background:rgba(67,194,229,.022);overflow:hidden}.ticket-main{display:block;flex:1;min-width:0;padding:12px;text-decoration:none;color:inherit}.ticket-top{display:flex;justify-content:space-between;gap:7px;align-items:center}.hd-ticket> .ticket-main>strong{display:block;margin-top:10px;color:#d9e8ec;font-size:11px;line-height:1.4}.hd-ticket p{margin:5px 0 9px;color:#7f9ba5;font-size:9px;line-height:1.5}.ticket-meta{display:flex;justify-content:space-between;gap:8px;color:#718e99;font-size:8px}.ticket-meta span{white-space:nowrap;overflow:hidden;text-overflow:ellipsis}.ticket-meta i{margin-right:4px}.hd-ticket form{display:flex;align-items:center;border-left:1px solid rgba(255,255,255,.05);padding:0 8px}.mobile-delete{border:0;background:transparent;color:#c27f7f;font-size:8px;font-weight:800;display:flex;flex-direction:column;align-items:center;gap:5px;cursor:pointer}.mobile-delete i{font-size:12px}.hd-pagination{padding:10px 8px}.hd-alert{font-size:9px}.hd-shell{max-width:none}}
</style>
@endpush