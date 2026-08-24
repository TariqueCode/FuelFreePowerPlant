@extends('layouts.portal')

@section('title', 'Audit Log')
@section('content')
<section class="hero">
    <div class="eyebrow">SECURITY</div>
    <h1>Audit Log</h1>
    <p>Recent security and operational activity. Sensitive credentials are never stored in audit metadata.</p>
</section>

<div class="table-card">
    <div class="table-wrap">
        <table>
            <thead><tr><th>Time</th><th>User</th><th>Action</th><th>Module</th><th>Target</th><th>IP</th></tr></thead>
            <tbody>
            @forelse($logs as $log)
                <tr>
                    <td>{{ $log->created_at->format('Y-m-d H:i:s') }}</td>
                    <td>{{ $log->user?->email ?? 'System' }}</td>
                    <td>{{ $log->action }}</td>
                    <td>{{ $log->module ?? '—' }}</td>
                    <td>{{ $log->target_type ? class_basename($log->target_type).' #'.$log->target_id : '—' }}</td>
                    <td>{{ $log->ip_address ?? '—' }}</td>
                </tr>
            @empty
                <tr><td colspan="6">No audit events recorded yet.</td></tr>
            @endforelse
            </tbody>
        </table>
    </div>
    <div class="pagination">{{ $logs->links() }}</div>
</div>
@endsection

@push('styles')
<style>
.table-card{background:rgba(255,255,255,.025);border:1px solid var(--line);border-radius:18px;overflow:hidden}.table-wrap{overflow-x:auto}table{width:100%;border-collapse:collapse;min-width:850px}th,td{text-align:left;padding:14px 16px;border-bottom:1px solid rgba(255,255,255,.06);font-size:12px}th{color:#74cce9;font-size:10px;letter-spacing:.08em;text-transform:uppercase}td{color:#b5cbd4}.pagination{padding:12px}
</style>
@endpush
