@extends('layouts.portal')
@section('title','Help Desk — '.$source->email)
@section('content')
<section class="hero">
    <div class="eyebrow">{{ strtoupper($label) }}</div>
    <h1>{{ $type === 'contact' ? $source->subject : 'Career application' }}</h1>
    <p>{{ $source->name }} · {{ $source->email }} · {{ $source->created_at->format('d M Y, h:i A') }}</p>
</section>

@if(session('status'))<div class="notice">{{ session('status') }}</div>@endif
@if($errors->any())<div class="notice error">{{ $errors->first() }}</div>@endif

<section class="section">
    <div class="form-card">
        <p><strong>From:</strong> {{ $source->name }}</p>
        <p><strong>Email:</strong> <a href="mailto:{{ $source->email }}">{{ $source->email }}</a></p>
        @if($source->phone)<p><strong>Phone:</strong> {{ $source->phone }}</p>@endif
        @if($type === 'career')
            <p><strong>Position:</strong> {{ $source->position ?: 'General application' }}</p>
            @if($source->education)<p><strong>Education:</strong> {{ $source->education }}</p>@endif
            @if($source->experience)<p><strong>Experience:</strong> {{ $source->experience }}</p>@endif
            @if($source->location)<p><strong>Location:</strong> {{ $source->location }}</p>@endif
            @if($source->cv_path)<p><strong>CV:</strong> <a href="{{ route('admin.career-applications.cv',$source) }}">Download CV</a></p>@endif
        @endif
        <p><strong>Message</strong></p>
        <div class="card" style="white-space:pre-wrap;line-height:1.7">{{ $source->message ?: 'No message provided.' }}</div>
    </div>
</section>

<section class="section">
    <div class="form-card">
        <h2>Conversation</h2>
        @forelse($replies->sortBy('created_at') as $reply)
            <article class="reply {{ $reply->status === 'failed' ? 'failed' : '' }}">
                <div><strong>{{ $reply->adminUser->name }}</strong><small>{{ $reply->sent_at?->format('d M Y, h:i A') ?: $reply->created_at->format('d M Y, h:i A') }} · {{ ucfirst($reply->status) }}</small></div>
                <div class="reply-body">{!! nl2br(e($reply->body)) !!}</div>
            </article>
        @empty
            <p class="muted">No replies have been sent yet.</p>
        @endforelse
    </div>
</section>

<div class="form-card">
    <h2>Reply</h2>
    <form method="POST" action="{{ route('admin.helpdesk.reply',[$type,$source->id]) }}">
        @csrf
        <p class="muted">This reply will be sent from the official {{ $type === 'career' ? 'career' : 'information' }} mailbox. It will not be saved as a copy in the hosting mailbox.</p>
        <label for="body">Message</label>
        <textarea id="body" name="body" rows="9" required placeholder="Write your reply..."></textarea>
        <div class="actions"><a href="{{ route('admin.helpdesk') }}">Back</a><button type="submit">Send reply</button></div>
    </form>
</div>
@endsection

@push('styles')
<style>
.form-card{max-width:900px;background:rgba(255,255,255,.025);border:1px solid var(--line);border-radius:18px;padding:20px;margin-bottom:14px}.form-card h2{margin:0 0 14px;font-size:17px}.muted{color:var(--muted);font-size:11px;line-height:1.6}label{display:block;font-size:12px;color:#9eb9c4;margin:0 0 7px}textarea{width:100%;box-sizing:border-box;padding:13px;border-radius:11px;border:1px solid var(--line);background:#071c29;color:#e9f7fb;font:inherit;resize:vertical}.reply{padding:14px;border:1px solid var(--line);border-radius:14px;margin-top:10px;background:rgba(67,194,229,.035)}.reply.failed{border-color:rgba(255,100,100,.25)}.reply>div:first-child{display:flex;justify-content:space-between;gap:10px}.reply small{color:var(--muted);font-size:10px}.reply-body{margin-top:10px;white-space:pre-wrap;color:#c1d2d9;line-height:1.7;font-size:13px}.actions{display:flex;justify-content:flex-end;gap:12px;align-items:center;margin-top:14px}.actions a{color:#8ca9b6;text-decoration:none;font-size:13px}.actions button{border:0;border-radius:11px;padding:12px 17px;background:#31afd2;color:#fff;font-weight:700}
</style>
@endpush