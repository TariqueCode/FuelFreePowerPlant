@extends('layouts.portal')
@section('title','Inquiry')
@section('content')
<div class="hero"><div class="eyebrow">INQUIRY</div><h1>{{ $inquiry->subject }}</h1><p>{{ $inquiry->name }} · {{ $inquiry->email }} · {{ $inquiry->created_at->format('d M Y, h:i A') }}</p></div>
@if(session('status'))<div class="notice">{{ session('status') }}</div>@endif
<section class="section">
<div class="form-card">
<div class="eyebrow">REPLY</div>
<h2>Reply to {{ $inquiry->email }}</h2>
<p style="color:#7898a5;font-size:10px">This reply is sent from the configured Contact mailbox. A copy is kept in the internal Help Desk history, not in the cPanel mailbox.</p>
@if($errors->has('reply'))<div class="notice">{{ $errors->first('reply') }}</div>@endif
<form method="POST" action="{{ route('admin.inquiries.reply',$inquiry) }}">
@csrf
<textarea name="body" rows="8" required placeholder="Write your reply...">{{ old('body') }}</textarea>
<button type="submit">Send reply</button>
</form>
@if($replies->isNotEmpty())
<div style="margin-top:20px"><div class="eyebrow">CONVERSATION HISTORY</div>
@foreach($replies as $reply)<article class="card" style="margin-top:8px"><strong>{{ $reply->from_address }} → {{ $reply->to_address }}</strong><small style="display:block;color:#668692;margin-top:4px">{{ optional($reply->sent_at)->format('d M Y, h:i A') }}</small><div style="white-space:pre-wrap;line-height:1.7;margin-top:10px">{{ $reply->body }}</div></article>@endforeach
</div>
@endif
</div>
</section><div class="section"><div class="form-card"><p><strong>From:</strong> {{ $inquiry->name }}</p><p><strong>Email:</strong> <a href="mailto:{{ $inquiry->email }}">{{ $inquiry->email }}</a></p><p><strong>Phone:</strong> {{ $inquiry->phone ?: 'Not provided' }}</p><p><strong>Message</strong></p><div class="card" style="white-space:pre-wrap;line-height:1.7">{{ $inquiry->message }}</div><form method="POST" action="{{ route('admin.inquiries.update',$inquiry) }}"><div class="fields">@csrf @method('PATCH')<div><label for="status">Status</label><select id="status" name="status"><option value="new" @selected($inquiry->status==='new')>New</option><option value="read" @selected($inquiry->status==='read')>Read</option><option value="in_progress" @selected($inquiry->status==='in_progress')>In progress</option><option value="resolved" @selected($inquiry->status==='resolved')>Resolved</option><option value="archived" @selected($inquiry->status==='archived')>Archived</option></select></div><div class="full"><label for="admin_note">Internal note</label><textarea id="admin_note" name="admin_note" rows="5" style="width:100%;padding:13px;border-radius:11px;border:1px solid var(--line);background:#071c29;color:#e9f7fb">{{ $inquiry->admin_note }}</textarea></div></div><div class="actions"><a href="{{ route('admin.inquiries.index') }}">Back</a><button type="submit">Save changes</button></div></form></div></div>
@endsection
