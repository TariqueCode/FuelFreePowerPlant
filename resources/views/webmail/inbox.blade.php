@extends('webmail.layout')
@section('content')
@php($hostMode=request()->getHost()==='mail.fuelfreepowerplant.com')
@php($base=$hostMode?'':'/webmail')
<div class="hero">
    <div class="hero-copy">
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
    @forelse($messages as $message)
        <a class="message-row {{ $message['seen'] ? '' : 'unread' }}" data-message-search="{{ strtolower($message['from'].' '.$message['subject'].' '.$message['date']) }}" data-message-uid="{{ $message['uid'] }}" href="{{ url($base.'/message/'.$message['uid'].'?folder='.urlencode($folder)) }}">
            <span><i class="fa-regular {{ $message['seen'] ? 'fa-envelope-open' : 'fa-envelope' }}"></i></span>
            <span class="from">{{ $message['from'] ?: 'Unknown sender' }}</span>
            <span class="subject">{{ $message['subject'] }}</span>
            <span class="date">{{ $message['date'] }}</span>
        </a>
    @empty
        <div class="empty">
            <div><i class="fa-regular fa-envelope"></i></div>
            <h2>No messages in this folder</h2>
            <p>This mailbox folder does not contain any messages.</p>
        </div>
    @endforelse
    <div class="empty" id="search-empty" hidden><i class="fa-solid fa-magnifying-glass"></i><h3>No matching messages</h3><p>Try a different sender, subject or date.</p></div>
</div>
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
 function restoreOpened(){
   rows.forEach(row=>{
     const uid=row.dataset.messageUid;
     if(!uid)return;
     try{
       if(sessionStorage.getItem('ff-webmail-opened:'+uid)==='1'){
         row.classList.remove('unread');
         const icon=row.querySelector('i.fa-envelope');
         if(icon){icon.classList.remove('fa-envelope');icon.classList.add('fa-envelope-open');}
       }
     }catch(e){}
   });
 }
 rows.forEach(row=>row.addEventListener('click',()=>{
   try{sessionStorage.setItem('ff-webmail-opened:'+row.dataset.messageUid,'1');}catch(e){}
 }));
 restoreOpened();
 input.addEventListener('input',filter);
 window.addEventListener('pageshow',event=>{if(event.persisted)window.location.reload();});
})();
</script>
@endpush
@endsection