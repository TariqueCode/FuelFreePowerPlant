@extends('layouts.portal')
@section('title','Website Inquiries')
@section('content')
@php
    $statusLabels = [
        'new' => 'New',
        'read' => 'Read',
        'in_progress' => 'In progress',
        'replied' => 'Replied',
        'closed' => 'Closed',
    ];
@endphp

<style>
.inquiries-page{--iq-line:rgba(104,204,235,.13);--iq-soft:rgba(67,194,229,.06);--iq-panel:rgba(7,27,39,.72);width:100%;min-width:0}
.inquiries-hero{display:flex;align-items:flex-end;justify-content:space-between;gap:24px;margin-bottom:24px}
.inquiries-hero-copy{min-width:0}
.inquiries-hero .eyebrow{margin-bottom:9px}
.inquiries-hero h1{margin:0;letter-spacing:-.03em}
.inquiries-hero p{margin:10px 0 0;max-width:720px;color:#8eabb7}
.inquiries-summary{display:flex;align-items:center;gap:10px;flex:0 0 auto;padding:10px 13px;border:1px solid var(--iq-line);border-radius:13px;background:rgba(67,194,229,.045);color:#b7d8e1;white-space:nowrap}
.inquiries-summary i{color:#69d4ed}
.inquiries-summary strong{color:#f0fbfd}
.inquiries-toolbar{display:flex;align-items:center;justify-content:space-between;gap:16px;margin-bottom:16px;padding:7px;border:1px solid var(--iq-line);border-radius:15px;background:rgba(4,20,30,.62);box-shadow:0 16px 36px rgba(0,0,0,.12)}
.inquiries-filters{display:flex;align-items:center;gap:4px;min-width:0;overflow:auto;scrollbar-width:none}
.inquiries-filters::-webkit-scrollbar{display:none}
.inquiry-filter{display:inline-flex;align-items:center;justify-content:center;gap:7px;min-height:40px;padding:8px 12px;border:1px solid transparent;border-radius:10px;color:#7898a5;text-decoration:none;font-size:13px;font-weight:700;white-space:nowrap;transition:all .18s ease}
.inquiry-filter:hover{color:#dff7fb;background:var(--iq-soft);border-color:rgba(104,204,235,.08)}
.inquiry-filter.active{color:#effcff;background:rgba(67,194,229,.11);border-color:rgba(104,204,235,.16);box-shadow:inset 0 0 0 1px rgba(104,204,235,.025)}
.inquiry-filter i{font-size:12px}
.inquiries-hint{color:#6f8c98;font-size:12px;padding:0 9px;white-space:nowrap}
.inquiries-card{border:1px solid var(--iq-line);border-radius:18px;background:linear-gradient(145deg,rgba(7,27,39,.82),rgba(3,18,28,.74));box-shadow:0 22px 55px rgba(0,0,0,.16);overflow:hidden}
.inquiries-card-head{display:flex;align-items:center;justify-content:space-between;gap:14px;padding:18px 20px;border-bottom:1px solid var(--iq-line)}
.inquiries-card-head strong{font-size:16px;color:#eaf8fb}
.inquiries-card-head span{color:#72919d;font-size:12px}
.inquiries-table-wrap{overflow-x:auto}
.inquiries-table{width:100%;min-width:760px;border-collapse:collapse}
.inquiries-table th{padding:13px 20px;text-align:left;border-bottom:1px solid var(--iq-line);background:rgba(3,16,25,.3);color:#638391;font-size:11px;font-weight:800;letter-spacing:.08em;text-transform:uppercase;white-space:nowrap}
.inquiries-table td{padding:15px 20px;border-bottom:1px solid rgba(104,204,235,.08);vertical-align:middle;color:#c7dce2}
.inquiries-table tbody tr{transition:background .16s ease}
.inquiries-table tbody tr:hover{background:rgba(67,194,229,.035)}
.inquiries-table tbody tr:last-child td{border-bottom:0}
.inquiry-person{display:flex;align-items:center;gap:11px;min-width:190px}
.inquiry-avatar{width:38px;height:38px;display:grid;place-items:center;flex:0 0 38px;border:1px solid rgba(104,204,235,.13);border-radius:11px;background:rgba(67,194,229,.07);color:#73d5eb;font-size:13px}
.inquiry-person-main{min-width:0}
.inquiry-name{display:block;color:#e9f8fb;font-weight:750;white-space:nowrap;overflow:hidden;text-overflow:ellipsis;max-width:210px}
.inquiry-email{display:block;margin-top:3px;color:#72909c;font-size:12px;white-space:nowrap;overflow:hidden;text-overflow:ellipsis;max-width:210px}
.inquiry-subject{display:block;max-width:280px;color:#d7edf1;font-weight:650;white-space:nowrap;overflow:hidden;text-overflow:ellipsis}
.inquiry-phone{display:block;margin-top:4px;color:#668591;font-size:11px}
.inquiry-date{color:#9db7bf;font-size:12px;white-space:nowrap}
.status-pill{display:inline-flex;align-items:center;gap:7px;padding:6px 9px;border:1px solid rgba(104,204,235,.11);border-radius:999px;background:rgba(104,204,235,.055);color:#b9d9e1;font-size:11px;font-weight:800;white-space:nowrap}
.status-pill:before{content:"";width:6px;height:6px;border-radius:50%;background:#6c98a5;box-shadow:0 0 0 3px rgba(108,152,165,.08)}
.status-pill.status-new{color:#8ee7f8;background:rgba(73,200,230,.08);border-color:rgba(73,200,230,.16)}
.status-pill.status-new:before{background:#62d7ef;box-shadow:0 0 0 3px rgba(98,215,239,.1)}
.status-pill.status-in-progress{color:#d8d39b;background:rgba(205,194,90,.07);border-color:rgba(205,194,90,.14)}
.status-pill.status-in-progress:before{background:#d6c85d;box-shadow:0 0 0 3px rgba(214,200,93,.08)}
.status-pill.status-replied{color:#9ee5b6;background:rgba(82,196,120,.07);border-color:rgba(82,196,120,.14)}
.status-pill.status-replied:before{background:#69d28d;box-shadow:0 0 0 3px rgba(105,210,141,.08)}
.status-pill.status-closed{color:#a1b2b9;background:rgba(130,145,151,.07);border-color:rgba(130,145,151,.12)}
.inquiry-view{display:inline-flex;align-items:center;justify-content:center;gap:7px;min-height:36px;padding:7px 10px;border:1px solid rgba(104,204,235,.14);border-radius:9px;background:rgba(67,194,229,.055);color:#bfe8ef;text-decoration:none;font-size:12px;font-weight:800;transition:all .18s ease}
.inquiry-view:hover{color:#effcff;background:rgba(67,194,229,.1);border-color:rgba(104,204,235,.25);transform:translateY(-1px)}
.inquiries-empty{padding:54px 24px!important;text-align:center!important}
.inquiries-empty i{display:grid;place-items:center;width:48px;height:48px;margin:0 auto 12px;border:1px solid var(--iq-line);border-radius:14px;background:var(--iq-soft);color:#5f95a4;font-size:18px}
.inquiries-empty strong{display:block;color:#dceef2;font-size:14px}
.inquiries-empty span{display:block;margin-top:5px;color:#6f8c98;font-size:12px}
.inquiries-pagination{padding:16px 20px;border-top:1px solid var(--iq-line)}
.inquiries-pagination nav{display:flex;justify-content:center}
.inquiries-pagination svg{width:16px;height:16px}

/* Compact cards replace the wide table on phones. */
.inquiry-mobile-list{display:none}
@media(max-width:900px){
    .inquiries-hero{align-items:flex-start;flex-direction:column;gap:15px;margin-bottom:20px}
    .inquiries-summary{width:100%;justify-content:flex-start}
    .inquiries-toolbar{align-items:stretch;flex-direction:column;gap:8px;padding:6px;margin-bottom:13px}
    .inquiries-filters{width:100%}
    .inquiries-hint{display:none}
    .inquiries-card-head{padding:15px 16px}
    .inquiries-table-wrap{display:none}
    .inquiry-mobile-list{display:grid;gap:9px;padding:10px}
    .inquiry-mobile-item{display:block;padding:14px;border:1px solid rgba(104,204,235,.1);border-radius:14px;background:rgba(3,18,28,.48);text-decoration:none;color:inherit;transition:background .16s ease,border-color .16s ease,transform .16s ease}
    .inquiry-mobile-item:hover{background:rgba(67,194,229,.055);border-color:rgba(104,204,235,.2);transform:translateY(-1px)}
    .inquiry-mobile-top{display:flex;align-items:flex-start;justify-content:space-between;gap:12px}
    .inquiry-mobile-person{display:flex;gap:10px;min-width:0}
    .inquiry-mobile-person .inquiry-name{max-width:none}
    .inquiry-mobile-person .inquiry-email{max-width:calc(100vw - 190px)}
    .inquiry-mobile-subject{margin:13px 0 10px;color:#dceff3;font-size:14px;font-weight:750;line-height:1.4}
    .inquiry-mobile-bottom{display:flex;align-items:center;justify-content:space-between;gap:10px;padding-top:10px;border-top:1px solid rgba(104,204,235,.08)}
    .inquiry-mobile-date{color:#6f8c98;font-size:11px}
    .inquiry-mobile-arrow{color:#68cfe7;font-size:12px}
}
@media(max-width:520px){
    .inquiries-hero h1{font-size:30px!important}
    .inquiries-hero p{font-size:15px!important;line-height:1.5}
    .inquiry-filter{min-height:42px;padding-inline:11px;font-size:12px}
    .inquiries-card{border-radius:15px}
    .inquiries-card-head{padding:14px}
    .inquiry-mobile-list{padding:8px}
    .inquiry-mobile-item{padding:12px}
    .inquiry-mobile-person .inquiry-email{max-width:calc(100vw - 175px)}
}
</style>

<div class="inquiries-page">
    <section class="hero inquiries-hero">
        <div class="inquiries-hero-copy">
            <div class="eyebrow">CLIENT COMMUNICATION</div>
            <h1>Website Inquiries</h1>
            <p>Review, track and respond to messages submitted through the public website.</p>
        </div>
        <div class="inquiries-summary" aria-label="Inquiry count">
            <i class="fa-solid fa-inbox"></i>
            <strong>{{ $inquiries->total() }}</strong>
            <span>{{ $inquiries->total() === 1 ? 'inquiry' : 'inquiries' }}</span>
        </div>
    </section>

    <div class="inquiries-toolbar">
        <nav class="inquiries-filters" aria-label="Inquiry status filters">
            <a class="inquiry-filter {{ $status === '' ? 'active' : '' }}" href="{{ route('admin.inquiries.index') }}"><i class="fa-solid fa-layer-group"></i> All</a>
            <a class="inquiry-filter {{ $status === 'new' ? 'active' : '' }}" href="{{ route('admin.inquiries.index',['status'=>'new']) }}"><i class="fa-solid fa-circle-dot"></i> New</a>
            <a class="inquiry-filter {{ $status === 'read' ? 'active' : '' }}" href="{{ route('admin.inquiries.index',['status'=>'read']) }}"><i class="fa-regular fa-envelope-open"></i> Read</a>
            <a class="inquiry-filter {{ $status === 'in_progress' ? 'active' : '' }}" href="{{ route('admin.inquiries.index',['status'=>'in_progress']) }}"><i class="fa-solid fa-spinner"></i> In progress</a>
            <a class="inquiry-filter {{ $status === 'replied' ? 'active' : '' }}" href="{{ route('admin.inquiries.index',['status'=>'replied']) }}"><i class="fa-solid fa-reply"></i> Replied</a>
            <a class="inquiry-filter {{ $status === 'closed' ? 'active' : '' }}" href="{{ route('admin.inquiries.index',['status'=>'closed']) }}"><i class="fa-solid fa-check"></i> Closed</a>
        </nav>
        <div class="inquiries-hint"><i class="fa-regular fa-clock"></i> Latest first</div>
    </div>

    <section class="inquiries-card" aria-label="Website inquiry list">
        <div class="inquiries-card-head">
            <strong>{{ $status ? ($statusLabels[$status] ?? 'Filtered') : 'All inquiries' }}</strong>
            <span>{{ $inquiries->firstItem() ?? 0 }}–{{ $inquiries->lastItem() ?? 0 }} of {{ $inquiries->total() }}</span>
        </div>

        <div class="inquiries-table-wrap">
            <table class="inquiries-table">
                <thead>
                    <tr><th>Contact</th><th>Subject</th><th>Status</th><th>Received</th><th>Action</th></tr>
                </thead>
                <tbody>
                @forelse($inquiries as $inquiry)
                    @php
                        $label = $statusLabels[$inquiry->status] ?? str_replace('_',' ',ucfirst($inquiry->status));
                        $statusClass = 'status-'.str_replace('_','-',$inquiry->status);
                        $initial = mb_strtoupper(mb_substr(trim($inquiry->name ?: $inquiry->email), 0, 1));
                    @endphp
                    <tr>
                        <td>
                            <div class="inquiry-person">
                                <span class="inquiry-avatar" aria-hidden="true">{{ $initial }}</span>
                                <div class="inquiry-person-main">
                                    <span class="inquiry-name">{{ $inquiry->name ?: 'Unknown sender' }}</span>
                                    <span class="inquiry-email">{{ $inquiry->email }}</span>
                                </div>
                            </div>
                        </td>
                        <td>
                            <span class="inquiry-subject">{{ $inquiry->subject ?: 'No subject' }}</span>
                            @if($inquiry->phone)<span class="inquiry-phone"><i class="fa-solid fa-phone"></i> {{ $inquiry->phone }}</span>@endif
                        </td>
                        <td><span class="status-pill {{ $statusClass }}">{{ $label }}</span></td>
                        <td><span class="inquiry-date">{{ $inquiry->created_at->format('d M Y') }}<br>{{ $inquiry->created_at->format('h:i A') }}</span></td>
                        <td><a class="inquiry-view" href="{{ route('admin.inquiries.show',$inquiry) }}"><i class="fa-regular fa-eye"></i> View</a></td>
                    </tr>
                @empty
                    <tr><td colspan="5" class="inquiries-empty"><i class="fa-regular fa-envelope"></i><strong>No inquiries found</strong><span>New messages submitted through the website will appear here.</span></td></tr>
                @endforelse
                </tbody>
            </table>
        </div>

        <div class="inquiry-mobile-list">
            @forelse($inquiries as $inquiry)
                @php
                    $label = $statusLabels[$inquiry->status] ?? str_replace('_',' ',ucfirst($inquiry->status));
                    $statusClass = 'status-'.str_replace('_','-',$inquiry->status);
                    $initial = mb_strtoupper(mb_substr(trim($inquiry->name ?: $inquiry->email), 0, 1));
                @endphp
                <a class="inquiry-mobile-item" href="{{ route('admin.inquiries.show',$inquiry) }}">
                    <div class="inquiry-mobile-top">
                        <div class="inquiry-mobile-person">
                            <span class="inquiry-avatar" aria-hidden="true">{{ $initial }}</span>
                            <div class="inquiry-person-main">
                                <span class="inquiry-name">{{ $inquiry->name ?: 'Unknown sender' }}</span>
                                <span class="inquiry-email">{{ $inquiry->email }}</span>
                            </div>
                        </div>
                        <span class="status-pill {{ $statusClass }}">{{ $label }}</span>
                    </div>
                    <div class="inquiry-mobile-subject">{{ $inquiry->subject ?: 'No subject' }}</div>
                    <div class="inquiry-mobile-bottom">
                        <span class="inquiry-mobile-date"><i class="fa-regular fa-clock"></i> {{ $inquiry->created_at->format('d M Y, h:i A') }}</span>
                        <span class="inquiry-mobile-arrow"><i class="fa-solid fa-arrow-right"></i></span>
                    </div>
                </a>
            @empty
                <div class="inquiries-empty"><i class="fa-regular fa-envelope"></i><strong>No inquiries found</strong><span>New messages submitted through the website will appear here.</span></div>
            @endforelse
        </div>

        @if($inquiries->hasPages())
            <div class="inquiries-pagination">{{ $inquiries->links() }}</div>
        @endif
    </section>
</div>
@endsection
