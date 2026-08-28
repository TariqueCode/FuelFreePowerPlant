@extends('webmail.layout')
@section('content')
<div class="hero"><div><div class="eyebrow">FuelFree PowerPlant Webmail</div><h1 class="title">{{ $mode === 'reply' ? 'Reply' : ($mode === 'forward' ? 'Forward' : 'Compose') }}</h1><p class="sub">Professional HTML email editor · sending from {{ $email }}.</p></div><a class="btn" href="{{ config('cpanel.webmail_url','https://mail.fuelfreepowerplant.com') }}/inbox?folder={{ urlencode($folder ?? 'INBOX') }}"><i class="fa-solid fa-arrow-left"></i> Inbox</a></div>
<form class="card compose" method="POST" enctype="multipart/form-data" action="{{ config('cpanel.webmail_url','https://mail.fuelfreepowerplant.com') }}/send" id="compose-form">
 @csrf
 <div class="recipient-grid">
  <div class="field"><label for="to">To</label><input id="to" name="to" value="{{ old('to',$initialTo) }}" placeholder="recipient@example.com, another@example.com" required></div>
  <div class="field"><label for="cc">Cc</label><input id="cc" name="cc" value="{{ old('cc',$initialCc ?? '') }}" placeholder="Optional"></div>
  <div class="field"><label for="bcc">Bcc</label><input id="bcc" name="bcc" value="{{ old('bcc','') }}" placeholder="Optional"></div>
 </div>
 <div class="field"><label for="subject">Subject</label><input id="subject" name="subject" value="{{ old('subject',$initialSubject) }}" placeholder="Subject"></div>
 <div class="field"><label>Message</label>@include('partials.mail-editor',['editorId'=>'webmail-editor','formId'=>'compose-form','bodyName'=>'body','initialBody'=>old('body',$initialBody),'allowAttachments'=>true])</div>
 <div class="compose-actions"><a class="btn" href="{{ config('cpanel.webmail_url','https://mail.fuelfreepowerplant.com') }}/inbox?folder={{ urlencode($folder ?? 'INBOX') }}">Cancel</a><button class="btn primary" type="submit"><i class="fa-solid fa-paper-plane"></i> Send message</button></div>
</form>
@push('styles')<style>.compose{padding:22px}.recipient-grid{display:grid;grid-template-columns:1fr 1fr;gap:14px}.recipient-grid .field:first-child{grid-column:1/-1}.field{margin-bottom:14px}.field label{display:block;margin-bottom:7px;color:#a9c2ca;font-weight:700;font-size:14px}.field input{width:100%;min-height:46px;border:1px solid var(--line);background:#041a23;color:var(--text);border-radius:13px;padding:13px 14px;outline:none}.field input:focus{border-color:var(--accent)}.compose-actions{display:flex;justify-content:flex-end;gap:10px;margin-top:18px}.compose-actions .btn{text-decoration:none}@media(max-width:650px){.compose{padding:15px}.recipient-grid{grid-template-columns:1fr}.recipient-grid .field:first-child{grid-column:auto}.compose-actions{justify-content:stretch}.compose-actions .btn{flex:1}}</style>@endpush
@endsection