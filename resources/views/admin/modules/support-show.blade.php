@extends('layouts.portal')

@section('title', $ticket->subject)
@section('content')
<section class="hero"><div class="eyebrow">TICKET #{{ $ticket->id }}</div><h1>{{ $ticket->subject }}</h1><p>{{ $ticket->user->name }} · {{ $ticket->user->email }}</p></section>

@if(session('status')) <div class="notice">{{ session('status') }}</div> @endif

<div class="ticket-head card">
    <div><span class="label">Priority</span><strong>{{ ucfirst($ticket->priority) }}</strong></div>
    <div><span class="label">Status</span><strong>{{ ucfirst(str_replace('-', ' ', $ticket->status)) }}</strong></div>
    <a href="{{ route('admin.support') }}">← All tickets</a>
</div>

<section class="messages">
    @foreach($ticket->messages as $message)
        <article class="message {{ $message->user_id === auth()->id() ? 'mine' : '' }}">
            <div class="message-top"><strong>{{ $message->user->name }}</strong><span>{{ $message->created_at->format('M d, Y H:i') }}</span></div>
            <div class="message-body">{!! nl2br(e($message->body)) !!}</div>
        </article>
    @endforeach
</section>

@if($ticket->status !== 'closed' || $isStaff)
<div class="form-card">
    <form method="POST" action="{{ route('admin.support.reply', $ticket) }}">
        @csrf
        <label>Reply</label>
        <textarea name="body" rows="6" required placeholder="Write your reply..."></textarea>
        <div class="actions"><button type="submit">Send reply</button></div>
    </form>
</div>
@endif

@if($isStaff && auth()->user()->hasPermission('support.manage'))
<div class="form-card status-card">
    <form method="POST" action="{{ route('admin.support.update', $ticket) }}">
        @csrf @method('PATCH')
        <div class="fields">
            <div><label>Status</label><select name="status"><option value="open" @selected($ticket->status==='open')>Open</option><option value="in-progress" @selected($ticket->status==='in-progress')>In progress</option><option value="closed" @selected($ticket->status==='closed')>Closed</option></select></div>
            <div><label>Priority</label><select name="priority"><option value="low" @selected($ticket->priority==='low')>Low</option><option value="normal" @selected($ticket->priority==='normal')>Normal</option><option value="high" @selected($ticket->priority==='high')>High</option></select></div>
        </div>
        <div class="actions"><button type="submit">Update ticket</button></div>
    </form>
</div>
@endif
@endsection

@push('styles')
<style>
.ticket-head{display:flex;align-items:center;gap:28px;padding:18px;margin-bottom:18px}.ticket-head>div{display:grid;gap:5px}.ticket-head .label{font-size:9px;color:var(--accent);text-transform:uppercase;letter-spacing:.12em}.ticket-head>a{margin-left:auto;color:var(--accent);text-decoration:none;font-size:12px}.messages{display:grid;gap:12px;margin-bottom:18px}.message{max-width:820px;padding:16px;border:1px solid var(--line);border-radius:15px;background:rgba(255,255,255,.025)}.message.mine{margin-left:auto;background:rgba(67,194,229,.06)}.message-top{display:flex;justify-content:space-between;gap:12px;font-size:12px}.message-top span{color:var(--muted);font-size:10px}.message-body{margin-top:10px;color:#c1d2d9;font-size:13px;line-height:1.7}.form-card{max-width:820px;background:rgba(255,255,255,.025);border:1px solid var(--line);border-radius:18px;padding:20px;margin-bottom:14px}label{display:block;font-size:12px;color:#9eb9c4;margin:0 0 7px}textarea,select{width:100%;box-sizing:border-box;padding:13px;border-radius:11px;border:1px solid var(--line);background:#071c29;color:#e9f7fb;font:inherit}.actions{display:flex;justify-content:flex-end;margin-top:14px}.actions button{border:0;border-radius:11px;padding:12px 17px;background:#31afd2;color:#fff;font-weight:700}.fields{display:grid;grid-template-columns:1fr 1fr;gap:14px}@media(max-width:620px){.ticket-head{align-items:flex-start;flex-wrap:wrap}.ticket-head>a{margin-left:0}.fields{grid-template-columns:1fr}}
</style>
@endpush
