@extends('webmail.layout')
@section('content')
<div class="hero"><div><div class="eyebrow">Message</div><h1 class="title">{{ $message['subject'] }}</h1></div><a class="btn" href="{{ route('webmail.inbox') }}">← Inbox</a></div>
<article class="card reader">
    <div class="reader-head"><h2>{{ $message['subject'] }}</h2><div class="meta"><strong>From:</strong> {{ $message['from'] }}<br><strong>To:</strong> {{ $message['to'] }}<br><strong>Date:</strong> {{ $message['date'] }}</div></div>
    <div class="reader-body">{!! $message['body'] !!}</div>
</article>
@endsection
