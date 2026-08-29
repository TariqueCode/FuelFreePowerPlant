@extends('layouts.portal')
@section('title','Help Desk — '.($type === 'email' ? $source->sender_email : $source->email))
@section('content')
<section class="hero">
<div class="eyebrow">{{ strtoupper($label) }}</div>
<h1>{{ $type === 'email' ? ($source->subject ?: '(No subject)') : ($type === 'contact' ? $source->subject : 'Career application') }}</h1>
<p>{{ $type === 'email' ? ($source->sender_name ?: $source->sender_email) : $source->name }} · {{ $type === 'email' ? $source->sender_email : $source->email }} · {{ ($type === 'email' ? ($source->received_at ?: $source->created_at) : $source->created_at)->format('d M Y, h:i A') }}</p>
</section>
@if(session('status'))<div class="notice">{{ session('status') }}</div>@endif
@if($errors->any())<div class="notice error">{{ $errors->first() }}</div>@endif
<section class="section"><div class="form-card">
@if($type === 'email')
<p><strong>From:</strong> {{ $source->sender_name ?: 'Unknown sender' }}</p>
<p><strong>Email:</strong> <a href="mailto:{{ $source->sender_email }}">{{ $source->sender_email }}</a></p>
@if($source->to_email)<p><strong>To:</strong> {{ $source->to_email }}</p>@endif
@if($source->cc_email)<p><strong>CC:</strong> {{ $source->cc_email }}</p>@endif
<p><strong>Mailbox:</strong> {{ $source->mailbox_group === 'career' ? 'career@fuelfreepowerplant.com' : 'info@fuelfreepowerplant.com' }}</p>
<p><strong>Message</strong></p><div class="email-body card">{!! $source->body_html ?: nl2br(e($source->body_text ?: 'No message provided.')) !!}</div>
@if($source->attachments->isNotEmpty())<div class="attachments"><strong>Attachments</strong><div class="attachment-list">
@foreach($source->attachments as $attachment)<a href="{{ route('admin.helpdesk.attachment',$attachment->id) }}"><i class="fa-solid fa-paperclip"></i>{{ $attachment->filename }} <small>({{ number_format($attachment->size / 1024,1) }} KB)</small></a>@endforeach
</div></div>@endif
@else
<p><strong>From:</strong> {{ $source->name }}</p><p><strong>Email:</strong> <a href="mailto:{{ $source->email }}">{{ $source->email }}</a></p>
@if($source->phone)<p><strong>Phone:</strong> {{ $source->phone }}</p>@endif
@if($type === 'career')
<p><strong>Position:</strong> {{ $source->position ?: 'General application' }}</p>
@if($source->education)<p><strong>Education:</strong> {{ $source->education }}</p>@endif
@if($source->experience)<p><strong>Experience:</strong> {{ $source->experience }}</p>@endif
@if($source->location)<p><strong>Location:</strong> {{ $source->location }}</p>@endif
@if($source->cv_path)<p><strong>CV:</strong> <a href="{{ route('admin.career-applications.cv',$source) }}">Download CV</a></p>@endif
@endif
<p><strong>Message</strong></p><div class="card" style="white-space:pre-wrap;line-height:1.7">{{ $source->message ?: 'No message provided.' }}</div>
@endif
</div></section>
<section class="section"><div class="form-card"><h2>Conversation</h2>
@forelse($replies->sortBy('created_at') as $reply)
<article class="reply {{ $reply->status === 'failed' ? 'failed' : '' }}"><div><strong>{{ $reply->adminUser->name }}</strong><small>{{ $reply->sent_at?->format('d M Y, h:i A') ?: $reply->created_at->format('d M Y, h:i A') }} · {{ ucfirst($reply->status) }}</small></div><div class="reply-body">{!! nl2br(e($reply->body)) !!}</div></article>
@empty<p class="muted">No replies have been sent yet.</p>@endforelse
</div></section>
<div class="form-card"><h2>Reply</h2>
<form method="POST" action="{{ route('admin.helpdesk.reply',[$type,$source->id]) }}">
@csrf
<p class="muted">This reply is sent from <strong>{{ ($type === 'career' || ($type === 'email' && $source->mailbox_group === 'career')) ? 'career@fuelfreepowerplant.com' : 'info@fuelfreepowerplant.com' }}</strong>. It is not copied into the hosting mailbox Sent folder.</p>
<label for="body">Message</label><textarea id="body" name="body" rows="9" required placeholder="Write your reply..."></textarea>
<div class="actions"><a href="{{ route('admin.helpdesk') }}">Back</a>@if($type === 'email')<button class="danger" type="submit" form="delete-email" onclick="return confirm('Permanently delete this Help Desk email and its attachments?')">Delete email</button>@endif<button type="submit">Send reply</button></div>
</form>
@if($type === 'email')<form id="delete-email" method="POST" action="{{ route('admin.helpdesk.email.delete',$source->id) }}">@csrf @method('DELETE')</form>@endif
</div>
@endsection
@push('styles')
<style>
.form-card{max-width:900px;background:rgba(255,255,255,.025);border:1px solid var(--line);border-radius:18px;padding:20px;margin-bottom:14px}.form-card h2{margin:0 0 14px;font-size:17px}.muted{color:var(--muted);font-size:11px;line-height:1.6}label{display:block;font-size:12px;color:#9eb9c4;margin:0 0 7px}textarea{width:100%;box-sizing:border-box;padding:13px;border-radius:11px;border:1px solid var(--line);background:#071c29;color:#e9f7fb;font:inherit;resize:vertical}.email-body{white-space:normal;overflow-wrap:anywhere;line-height:1.7}.email-body img{max-width:100%;height:auto}.email-body table{max-width:100%;overflow:auto;display:block}.attachments{margin-top:16px}.attachment-list{display:flex;flex-wrap:wrap;gap:8px;margin-top:9px}.attachment-list a{display:inline-flex;align-items:center;gap:7px;padding:9px 11px;border:1px solid var(--line);border-radius:10px;color:#9edff0;text-decoration:none;font-size:11px}.attachment-list small{color:var(--muted)}.reply{padding:14px;border:1px solid var(--line);border-radius:14px;margin-top:10px;background:rgba(67,194,229,.035)}.reply.failed{border-color:rgba(255,100,100,.25)}.reply>div:first-child{display:flex;justify-content:space-between;gap:10px}.reply small{color:var(--muted);font-size:10px}.reply-body{margin-top:10px;white-space:pre-wrap;color:#c1d2d9;line-height:1.7;font-size:13px}.actions{display:flex;justify-content:flex-end;gap:12px;align-items:center;margin-top:14px}.actions a{color:#8ca9b6;text-decoration:none;font-size:13px}.actions button{border:0;border-radius:11px;padding:12px 17px;background:#31afd2;color:#fff;font-weight:700}.actions .danger{background:rgba(210,75,75,.18);color:#ff9d9d;border:1px solid rgba(210,75,75,.3)}
</style>
@endpush
