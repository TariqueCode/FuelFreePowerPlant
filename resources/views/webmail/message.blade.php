@extends('webmail.layout')
@section('content')
<div class="hero"><div><div class="eyebrow">FuelFree PowerPlant Webmail</div><h1 class="title">Message</h1></div><a class="btn" href="{{ config('cpanel.webmail_url','https://mail.fuelfreepowerplant.com') }}/inbox?folder={{ urlencode($folder ?? 'INBOX') }}">← Inbox</a></div>
<article class="card reader">
    <div class="reader-head"><h2>{{ $message['subject'] }}</h2><div class="meta"><strong>From:</strong> {{ $message['from'] }}<br><strong>To:</strong> {{ $message['to'] }}<br><strong>Date:</strong> {{ $message['date'] }}</div></div>
    <div class="reader-actions"><a class="btn primary" href="{{ config('cpanel.webmail_url','https://mail.fuelfreepowerplant.com') }}/compose?reply={{ $message['uid'] }}&folder={{ urlencode($folder ?? 'INBOX') }}">↩ Reply</a><a class="btn" href="{{ config('cpanel.webmail_url','https://mail.fuelfreepowerplant.com') }}/compose?forward={{ $message['uid'] }}&folder={{ urlencode($folder ?? 'INBOX') }}">↗ Forward</a></div>
    <div class="reader-body">{!! $message['body'] !!}</div>
</article>
@push('styles')<style>.reader-actions{display:flex;gap:9px;flex-wrap:wrap;margin-bottom:18px}.reader-actions .btn{text-decoration:none}@media(max-width:650px){.reader-actions .btn{flex:1}}</style>@endpush
@endsection
