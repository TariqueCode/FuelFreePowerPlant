@extends('webmail.layout')
@section('content')
@php($hostMode=request()->getHost()==='mail.fuelfreepowerplant.com')
@php($base=$hostMode?'':'/webmail')
<div class="hero">
    <div>
        <div class="eyebrow"><i class="fa-solid fa-envelope-open-text"></i> Mailbox</div>
        <h1 class="title">{{ collect($folders)->firstWhere('name',$folder)['label'] ?? 'Inbox' }}</h1>
        <p class="sub">{{ $email }} · {{ count($messages) }} messages loaded</p>
    </div>
    <a class="btn primary" href="{{ url($base.'/compose?folder='.urlencode($folder)) }}"><i class="fa-solid fa-pen-to-square"></i> Compose</a>
</div>
<div class="card">
    <div class="mail-toolbar">
        <div class="search-box"><i class="fa-solid fa-magnifying-glass" aria-hidden="true"></i><input id="mail-search" type="search" placeholder="Search loaded messages…" autocomplete="off" aria-label="Search messages"></div>
        <span class="mail-count" id="mail-count">{{ count($messages) }} messages</span>
        <a class="btn icon" href="{{ url($base.'/inbox?folder='.urlencode($folder)) }}" title="Refresh" aria-label="Refresh"><i class="fa-solid fa-rotate"></i></a>
    </div>
    <div class="folder-strip" aria-label="Mailbox folders">
        @foreach($folders as $mailFolder)
            <a class="folder-chip {{ $mailFolder['name'] === $folder ? 'active' : '' }}" href="{{ url($base.'/inbox?folder='.urlencode($mailFolder['name'])) }}">
                <i class="fa-solid {{ $mailFolder['icon'] ?? 'fa-folder' }}"></i><span>{{ $mailFolder['label'] }}</span>
            </a>
        @endforeach
    </div>
    @forelse($messages as $message)
        <a class="message-row {{ $message['seen'] ? '' : 'unread' }}" data-message-search="{{ strtolower($message['from'].' '.$message['subject'].' '.$message['date']) }}" href="{{ url($base.'/message/'.$message['uid'].'?folder='.urlencode($folder)) }}">
            <span><i class="fa-regular {{ $message['seen'] ? 'fa-envelope-open' : 'fa-envelope' }}"></i></span>
            <span class="from">{{ $message['from'] ?: 'Unknown sender' }}</span>
            <span class="subject">{{ $message['subject'] }}</span>
            <span class="date">{{ $message['date'] }}</span>
        </a>
    @empty
        <div class="empty"><div><i class="fa-regular fa-envelope"></i></div><h2>No messages in this folder</h2><p>This mailbox folder does not contain any messages.</p><a class="btn primary" href="{{ url($base.'/compose?folder='.urlencode($folder)) }}"><i class="fa-solid fa-pen-to-square"></i> Compose</a></div>
    @endforelse
    <div class="empty" id="search-empty" hidden><i class="fa-solid fa-magnifying-glass"></i><h3>No matching messages</h3><p>Try a different sender, subject or date.</p></div>
</div>
@push('styles')
<style>
.folder-strip{display:flex;gap:7px;flex-wrap:wrap;padding:11px 13px;border-bottom:1px solid var(--line);background:rgba(5,22,30,.32)}
.folder-chip{display:inline-flex;align-items:center;gap:7px;padding:8px 11px;border:1px solid transparent;border-radius:9px;color:var(--muted);font-size:12px;font-weight:750}
.folder-chip:hover{background:#0b2d38;color:var(--text)}.folder-chip.active{background:rgba(67,209,240,.09);border-color:rgba(86,210,238,.18);color:#8bf3ff}
@media(max-width:760px){.folder-strip{overflow:auto;flex-wrap:nowrap;scrollbar-width:none}.folder-chip{white-space:nowrap;flex:0 0 auto}}
</style>
@endpush
@push('scripts')
<script>
(function(){
 const input=document.getElementById('mail-search'), rows=[...document.querySelectorAll('[data-message-search]')], empty=document.getElementById('search-empty'), count=document.getElementById('mail-count');
 if(!input)return;
 function filter(){
   const q=input.value.trim().toLowerCase(); let visible=0;
   rows.forEach(row=>{const show=!q||row.dataset.messageSearch.includes(q);row.classList.toggle('hidden',!show);if(show)visible++;});
   if(count)count.textContent=visible+' '+(visible===1?'message':'messages');
   if(empty)empty.hidden=visible!==0||rows.length===0;
 }
 input.addEventListener('input',filter);
})();
</script>
@endpush
@endsection