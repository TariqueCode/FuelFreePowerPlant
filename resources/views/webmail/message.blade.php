@extends('webmail.layout')
@section('content')
@php($currentFolderLabel = collect($folders ?? [])->firstWhere('name',$folder ?? 'INBOX')['label'] ?? 'Inbox')
<div class="hero">
    <div class="hero-copy">
        <div class="eyebrow">Mailbox</div>
        <h1 class="title">Message</h1>
    </div>
    <a class="btn" href="{{ config('cpanel.webmail_url','https://mail.fuelfreepowerplant.com') }}/inbox?folder={{ urlencode($folder ?? 'INBOX') }}"><i class="fa-solid fa-arrow-left"></i> {{ $currentFolderLabel }}</a>
</div>
<article class="card reader">
    <div class="reader-head">
        <h2>{{ $message['subject'] }}</h2>
        <div class="meta">
            <strong>From:</strong> {{ $message['from'] }}<br>
            <strong>To:</strong> {{ $message['to'] }}
            @if(!empty($message['cc']))<br><strong>Cc:</strong> {{ $message['cc'] }}@endif
            <br><strong>Date:</strong> {{ $message['date'] }}
        </div>
    </div>

    <div class="reader-actions">
        <a class="btn primary" href="{{ config('cpanel.webmail_url','https://mail.fuelfreepowerplant.com') }}/compose?reply={{ $message['uid'] }}&folder={{ urlencode($folder ?? 'INBOX') }}"><i class="fa-solid fa-reply"></i> Reply</a>
        <a class="btn" href="{{ config('cpanel.webmail_url','https://mail.fuelfreepowerplant.com') }}/compose?forward={{ $message['uid'] }}&folder={{ urlencode($folder ?? 'INBOX') }}"><i class="fa-solid fa-share"></i> Forward</a>
        <form method="POST" action="{{ config('cpanel.webmail_url','https://mail.fuelfreepowerplant.com') }}/message/{{ $message['uid'] }}/delete">
            @csrf
            <input type="hidden" name="folder" value="{{ $folder }}">
            <button class="btn danger" type="submit"><i class="fa-solid fa-trash"></i> Delete</button>
        </form>
    </div>

    @if(!empty($message['attachments']))
    <div class="attachments">
        <strong><i class="fa-solid fa-paperclip"></i> Attachments ({{ count($message['attachments']) }})</strong>
        <div class="attachment-grid">
            @foreach($message['attachments'] as $attachment)
            <a class="attachment" href="{{ route('webmail.host.attachment',[$message['uid'],$attachment['part'],'folder'=>$folder]) }}">
                <i class="fa-solid fa-file"></i>
                <span><b>{{ $attachment['name'] }}</b><small>{{ number_format(($attachment['size']??0)/1024,1) }} KB · {{ $attachment['type'] }}</small></span>
                <i class="fa-solid fa-download"></i>
            </a>
            @endforeach
        </div>
    </div>
    @endif

    <div class="reader-body">{!! $message['body'] !!}</div>
</article>
@endsection