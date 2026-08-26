@extends('webmail.layout')
@section('content')
@php($hostMode=request()->getHost()==='mail.fuelfreepowerplant.com') @php($base=$hostMode?'':'/webmail')
<div class="hero"><div><div class="eyebrow">Mailbox</div><h1 class="title">Inbox</h1><p class="sub">Your company email, in one clean workspace.</p></div><a class="btn primary" href="{{ url($base.'/compose') }}">✎ Compose</a></div>
<div class="card">@forelse($messages as $message)<a class="message-row {{ $message['seen'] ? '' : 'unread' }}" href="{{ url($base.'/message/'.$message['uid']) }}"><span>✉</span><span class="from">{{ $message['from'] ?: 'Unknown sender' }}</span><span class="subject">{{ $message['subject'] }}</span><span class="date">{{ $message['date'] }}</span></a>@empty<div class="empty"><div style="font-size:42px;margin-bottom:12px">📭</div><h2>No messages yet</h2><p>Your inbox is empty.</p></div>@endforelse</div>
@endsection
