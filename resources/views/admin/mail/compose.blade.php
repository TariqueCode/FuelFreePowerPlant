@extends('layouts.portal')
@section('title','Compose — '.$emailAccount->address)
@section('content')
<a class="back" href="{{ route('admin.mail',['account'=>$emailAccount->id]) }}"><i class="fa-solid fa-arrow-left"></i> {{ $emailAccount->address }}</a>
<section class="compose-card"><span class="eyebrow">PROFESSIONAL MAIL EDITOR</span><h1>Compose</h1><p class="sub">Sending securely from <strong>{{ $emailAccount->address }}</strong>. Attachments are streamed from the temporary upload and are not permanently stored by the website.</p>
@if($errors->any())<div class="notice">{{ $errors->first() }}</div>@endif
<form method="POST" enctype="multipart/form-data" action="{{ route('admin.mail.send',$emailAccount) }}" id="admin-mail-compose">
 @csrf
 <div class="recipient-grid">
  <label>To<input type="text" name="to" value="{{ old('to',$initialTo) }}" placeholder="recipient@example.com, another@example.com" required></label>
  <label>Cc<input type="text" name="cc" value="{{ old('cc',$initialCc ?? '') }}" placeholder="Optional"></label>
  <label>Bcc<input type="text" name="bcc" value="{{ old('bcc','') }}" placeholder="Optional"></label>
 </div>
 <label>Subject<input name="subject" value="{{ old('subject',$initialSubject) }}" placeholder="Subject"></label>
 @include('partials.mail-editor',['editorId'=>'admin-mail-editor','formId'=>'admin-mail-compose','bodyName'=>'body','initialBody'=>old('body',$initialBody),'allowAttachments'=>true])
 <button class="add-btn send-btn" type="submit"><i class="fa-solid fa-paper-plane"></i> Send message</button>
</form></section>
@endsection
@push('styles')
<style>.back{display:inline-flex;gap:7px;color:#7898a5;text-decoration:none;font-size:10px;margin-bottom:14px}.compose-card{max-width:980px;border:1px solid var(--line);border-radius:18px;background:linear-gradient(145deg,rgba(9,38,54,.86),rgba(5,22,32,.9));padding:22px}.compose-card h1{font-size:32px;margin:7px 0}.sub{color:#7898a5;font-size:10px;line-height:1.6}.compose-card>form>label,.recipient-grid label{display:block;color:#91adb6;font-size:10px;margin:13px 0}.compose-card input{width:100%;margin-top:6px;padding:12px;border:1px solid var(--line);border-radius:10px;background:#03131d;color:#eaf8fb;box-sizing:border-box}.recipient-grid{display:grid;grid-template-columns:1fr 1fr;gap:13px}.recipient-grid label:first-child{grid-column:1/-1}.notice{padding:10px;border-radius:10px;background:rgba(255,112,112,.08);color:#ffc1c1;font-size:10px;margin:12px 0}.send-btn{margin-top:15px}@media(max-width:650px){.compose-card{padding:15px}.recipient-grid{grid-template-columns:1fr}.recipient-grid label:first-child{grid-column:auto}}</style>
@endpush