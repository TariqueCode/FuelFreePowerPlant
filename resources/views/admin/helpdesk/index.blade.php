@extends('layouts.portal')
@section('title','Help Desk')
@section('content')
<section class="hero">
    <div class="eyebrow">CLIENT COMMUNICATION</div>
    <h1>Help Desk</h1>
    <p>Contact and Career email is automatically copied to the application server and cleared from the official mailbox after a successful import. Replies are sent from the matching official address.</p>
</section>
@if(session('status'))<div class="notice">{{ session('status') }}</div>@endif
@if($errors->any())<div class="errors">{{ $errors->first() }}</div>@endif
<div class="grid">
    <article class="card"><span class="card-label">Open</span><strong class="card-value">{{ number_format($openCount) }}</strong><span class="card-note">Awaiting or active</span></article>
    <article class="card"><span class="card-label">Contact</span><strong class="card-value">{{ number_format($contactCount) }}</strong><span class="card-note">Website + mailbox</span></article>
    <article class="card"><span class="card-label">Career</span><strong class="card-value">{{ number_format($careerCount) }}</strong><span class="card-note">Applications + mailbox</span></article>
</div>
<section class="section table-card">
<div class="table-wrap"><table>
<thead><tr><th>Type</th><th>Sender</th><th>Subject</th><th>Status</th><th>Received</th><th></th></tr></thead>
<tbody>
@forelse($items as $item)
<tr>
<td><span class="badge">{{ $item->type === 'email' ? ucfirst($item->channel).' email' : ucfirst($item->type) }}</span></td>
<td>{{ $item->name }}<br><small>{{ $item->email }}</small></td>
<td>{{ $item->subject }}</td><td><span class="badge">{{ ucfirst(str_replace('_',' ',$item->status)) }}</span></td>
<td>{{ $item->received_at->format('d M Y, H:i') }}</td><td><a class="action" href="{{ $item->route }}">Open</a></td>
</tr>
@empty<tr><td colspan="6">No messages received yet.</td></tr>@endforelse
</tbody></table></div>
<div class="pagination">{{ $items->links() }}</div>
</section>
@endsection
@push('styles')
<style>
.table-card{background:rgba(255,255,255,.025);border:1px solid var(--line);border-radius:18px;overflow:hidden}.table-wrap{overflow-x:auto}table{width:100%;border-collapse:collapse;min-width:780px}th,td{text-align:left;padding:14px 16px;border-bottom:1px solid rgba(255,255,255,.06);font-size:12px}th{color:#74cce9;font-size:10px;letter-spacing:.08em;text-transform:uppercase}td{color:#b5cbd4}.action{display:inline-block;padding:8px 11px;border-radius:9px;background:rgba(67,194,229,.08);color:#8edcf4;text-decoration:none;font-weight:700;font-size:11px}.badge{display:inline-block;padding:5px 8px;border-radius:999px;font-size:9px;background:rgba(255,255,255,.06);color:#b8d5dd}.card-note{display:block;color:var(--muted);font-size:10px;margin-top:4px}
</style>
@endpush
