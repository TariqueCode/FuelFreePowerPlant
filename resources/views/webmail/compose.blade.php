@extends('webmail.layout')
@section('content')
<div class="hero"><div><div class="eyebrow">New message</div><h1 class="title">Compose</h1><p class="sub">Send an email from {{ $email }}.</p></div><a class="btn" href="{{ route('webmail.inbox') }}">← Inbox</a></div>
<form class="card compose" method="POST" action="{{ route('webmail.send') }}">
    @csrf
    <div class="field"><label for="to">To</label><input id="to" name="to" type="email" value="{{ old('to') }}" placeholder="recipient@example.com" required></div>
    <div class="field"><label for="subject">Subject</label><input id="subject" name="subject" type="text" value="{{ old('subject') }}" placeholder="Subject"></div>
    <div class="field"><label for="body">Message</label><textarea id="body" name="body" placeholder="Write your message..." required>{{ old('body') }}</textarea></div>
    <button class="btn primary" type="submit">Send message</button>
</form>
@endsection
