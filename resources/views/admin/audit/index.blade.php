@extends('layouts.portal')

@section('title', 'Audit Log')
@section('content')
<section class="audit-admin-head">
    <div class="audit-admin-title">
        <span class="eyebrow">SECURITY · ACTIVITY</span>
        <h1>Audit Log</h1>
        <p>Review recent security and operational activity. Sensitive credentials are never stored in audit metadata.</p>
    </div>

    <div class="audit-admin-actions">
        <div class="audit-count">
            <strong>{{ $logs->total() }}</strong>
            <span>events</span>
        </div>
    </div>
</section>

<section class="audit-table-card">
    <div class="audit-table-head">
        <div>
            <span class="eyebrow">ACTIVITY STREAM</span>
            <h2>Recent events</h2>
        </div>
        <span class="audit-table-note"><i class="fa-solid fa-shield-halved"></i> Protected audit metadata</span>
    </div>
    <div class="table-card">
    <div class="table-wrap">
        <table>
            <thead><tr><th>Time</th><th>User</th><th>Action</th><th>Module</th><th>Target</th><th>IP</th></tr></thead>
            <tbody>
            @forelse($logs as $log)
                <tr>
                    <td>{{ $log->created_at->format('Y-m-d H:i:s') }}</td>
                    <td><span class="audit-user">{{ $log->user?->email ?? 'System' }}</span></td>
                    <td><span class="audit-action">{{ $log->action }}</span></td>
                    <td><span class="audit-module">{{ $log->module ?? '—' }}</span></td>
                    <td>{{ $log->target_type ? class_basename($log->target_type).' #'.$log->target_id : '—' }}</td>
                    <td><span class="audit-ip">{{ $log->ip_address ?? '—' }}</span></td>
                </tr>
            @empty
                <tr><td colspan="6">No audit events recorded yet.</td></tr>
            @endforelse
            </tbody>
        </table>
    </div>
    <div class="pagination">{{ $logs->links() }}</div>
</section>
@endsection

@push('styles')
<style>
.audit-admin-head{
    display:flex;
    align-items:flex-end;
    justify-content:space-between;
    gap:28px;
    padding:0 0 20px;
    margin-bottom:20px;
    border-bottom:1px solid rgba(103,208,234,.14);
}

.audit-admin-title{min-width:0}

.audit-admin-title .eyebrow,
.audit-table-head .eyebrow{
    display:inline-block;
    color:#54cde8;
    font-size:9px;
    font-weight:800;
    letter-spacing:.18em;
}

.audit-admin-title h1{
    margin:7px 0;
    color:#eaf8fb;
    font-size:clamp(30px,3.2vw,42px);
    font-weight:800;
    line-height:1.08;
    letter-spacing:-.035em;
}

.audit-admin-title p{
    max-width:760px;
    margin:0;
    color:#7898a5;
    font-size:11px;
    line-height:1.7;
}

.audit-admin-actions{
    display:flex;
    align-items:center;
    justify-content:flex-end;
    flex:0 0 auto;
}

.audit-count{
    display:inline-flex;
    align-items:center;
    justify-content:center;
    gap:6px;
    min-height:38px;
    padding:0 13px;
    border:1px solid var(--line);
    border-radius:10px;
    background:rgba(67,194,229,.025);
    color:#7898a5;
    font-size:9px;
    white-space:nowrap;
}

.audit-count strong{
    color:#eaf8fb;
    font-size:15px;
    font-weight:800;
}

.audit-table-card{
    overflow:hidden;
    border:1px solid var(--line);
    border-radius:15px;
    background:#061923;
    box-shadow:0 12px 30px rgba(0,0,0,.12);
}

.audit-table-head{
    display:flex;
    align-items:center;
    justify-content:space-between;
    gap:18px;
    padding:14px 16px;
    border-bottom:1px solid rgba(103,208,234,.11);
    background:rgba(67,194,229,.025);
}

.audit-table-head h2{
    margin:5px 0 0;
    color:#eaf8fb;
    font-size:14px;
    font-weight:800;
    letter-spacing:-.01em;
}

.audit-table-note{
    display:inline-flex;
    align-items:center;
    gap:7px;
    color:#6f98a4;
    font-size:9px;
    white-space:nowrap;
}

.audit-table-note i{color:#54cde8}

.table-card{
    overflow:hidden;
    border:0;
    border-radius:0;
    background:transparent;
}

.table-wrap{overflow-x:auto}

table{
    width:100%;
    min-width:900px;
    border-collapse:collapse;
}

th,
td{
    text-align:left;
    padding:13px 16px;
    border-bottom:1px solid rgba(255,255,255,.055);
    font-size:11px;
    vertical-align:middle;
}

th{
    background:rgba(67,194,229,.035);
    color:#74cce9;
    font-size:9px;
    font-weight:800;
    letter-spacing:.1em;
    text-transform:uppercase;
    white-space:nowrap;
}

td{color:#b5cbd4}

tbody tr{
    transition:background .16s ease;
}

tbody tr:hover{
    background:rgba(82,216,240,.035);
}

tbody tr:last-child td{border-bottom:0}

.audit-user{
    color:#d8edf2;
    font-weight:650;
}

.audit-action,
.audit-module{
    display:inline-flex;
    align-items:center;
    min-height:24px;
    padding:0 8px;
    border:1px solid rgba(82,216,240,.14);
    border-radius:7px;
    background:rgba(82,216,240,.045);
    color:#9fd8e6;
    font-size:9px;
    font-weight:750;
}

.audit-module{
    color:#86b7c3;
    border-color:rgba(255,255,255,.08);
    background:rgba(255,255,255,.025);
}

.audit-ip{
    color:#83a8b3;
    font-variant-numeric:tabular-nums;
}

.pagination{
    display:flex;
    align-items:center;
    justify-content:center;
    min-height:54px;
    padding:10px 14px;
    border-top:1px solid rgba(255,255,255,.055);
    background:rgba(67,194,229,.018);
}

.pagination nav{width:100%}

.pagination svg{
    width:14px;
    height:14px;
}

@media(max-width:900px){
    .audit-admin-head{
        align-items:flex-start;
        flex-direction:column;
        gap:14px;
    }

    .audit-admin-actions{justify-content:flex-start}

    .audit-table-head{
        align-items:flex-start;
        flex-direction:column;
        gap:8px;
    }
}

@media(max-width:520px){
    .audit-admin-head{
        padding-bottom:16px;
        margin-bottom:16px;
    }

    .audit-admin-title h1{
        font-size:30px;
    }

    .audit-admin-title p{
        font-size:10px;
    }

    .audit-table-head{
        padding:12px;
    }

    th,
    td{
        padding:11px 12px;
    }

    .audit-table-note{
        font-size:8px;
    }
}
</style>
@endpush
