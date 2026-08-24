@extends('layouts.portal')

@section('title', 'Support')
@section('content')
<section class="hero">
    <div class="eyebrow">SERVICE DESK</div>
    <h1>Support</h1>
    <p>Handle client requests, operational issues and service communication from one secure ticket center.</p>
</section>

<div class="toolbar">
    @if(auth()->user()->hasPermission('support.create'))
        <a class="action" href="{{ route('admin.support.create') }}">＋ New ticket</a>
    @endif
</div>

@if(session('status')) <div class="notice">{{ session('status') }}</div> @endif

<div class="grid">
    <article class="card"><span class="card-label">Open tickets</span><strong class="card-value">{{ number_format($openCount) }}</strong><span class="card-note">Active requests</span></article>
    <article class="card"><span class="card-label">High priority</span><strong class="card-value">{{ number_format($priorityCount) }}</strong><span class="card-note">Needs attention</span></article>
    <article class="card"><span class="card-label">Visible tickets</span><strong class="card-value">{{ number_format($tickets->total()) }}</strong><span class="card-note">Based on your access</span></article>
</div>

<section class="section table-card">
    <div class="table-wrap">
        <table>
            <thead><tr><th>Subject</th><th>Requester</th><th>Priority</th><th>Status</th><th>Messages</th><th>Updated</th></tr></thead>
            <tbody>
            @forelse($tickets as $ticket)
                <tr>
                    <td><a class="ticket-link" href="{{ route('admin.support.ticket', $ticket) }}">{{ $ticket->subject }}</a></td>
                    <td>{{ $ticket->user->name }}</td>
                    <td><span class="badge {{ $ticket->priority }}">{{ ucfirst($ticket->priority) }}</span></td>
                    <td><span class="badge {{ $ticket->status }}">{{ ucfirst(str_replace('-', ' ', $ticket->status)) }}</span></td>
                    <td>{{ $ticket->messages_count }}</td>
                    <td>{{ $ticket->updated_at->format('M d, Y H:i') }}</td>
                </tr>
            @empty
                <tr><td colspan="6">No support tickets yet.</td></tr>
            @endforelse
            </tbody>
        </table>
    </div>
    <div class="pagination">{{ $tickets->links() }}</div>
</section>
@endsection

@push('styles')
<style>
.toolbar{display:flex;justify-content:flex-end;margin-bottom:14px}.action{display:inline-block;padding:11px 15px;border-radius:11px;background:#31afd2;color:#fff;text-decoration:none;font-weight:700;font-size:13px}.notice{padding:12px 14px;border-radius:12px;margin-bottom:14px;background:rgba(67,194,137,.1);border:1px solid rgba(67,194,137,.2);color:#a8e5ca}.table-card{background:rgba(255,255,255,.025);border:1px solid var(--line);border-radius:18px;overflow:hidden}.table-wrap{overflow-x:auto}table{width:100%;border-collapse:collapse;min-width:820px}th,td{text-align:left;padding:14px 16px;border-bottom:1px solid rgba(255,255,255,.06);font-size:12px}th{color:#74cce9;font-size:10px;letter-spacing:.08em;text-transform:uppercase}td{color:#b5cbd4}.ticket-link{color:var(--text);text-decoration:none;font-weight:700}.badge{display:inline-block;padding:4px 8px;border-radius:999px;font-size:10px;background:rgba(255,255,255,.06)}.badge.high{color:#ffb2bb}.badge.open{color:#8fe3c1}.badge.in-progress{color:#8edcf4}.badge.closed{color:#9aaeb8}.pagination{padding:12px}
</style>
@endpush
