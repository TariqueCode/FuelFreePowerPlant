@extends('layouts.portal')
@section('title','Help Desk — '.($type === 'email' ? $source->sender_email : $source->email))
@section('content')
<div class="hd-detail">
    <div class="detail-topbar">
        <a class="back-link" href="{{ route('admin.helpdesk') }}"><i class="fa-solid fa-arrow-left"></i><span>Help Desk</span></a>
        <div class="detail-actions">
            <span class="channel {{ $type==='career' || ($type==='email' && $source->mailbox_group==='career') ? 'career':'contact' }}"><i class="fa-solid {{ $type==='career' || ($type==='email' && $source->mailbox_group==='career') ? 'fa-briefcase':'fa-comments' }}"></i>{{ $label }}</span>
        </div>
    </div>

    @if(session('status'))<div class="hd-alert success"><i class="fa-solid fa-circle-check"></i><span>{{ session('status') }}</span></div>@endif
    @if($errors->any())<div class="hd-alert error"><i class="fa-solid fa-triangle-exclamation"></i><span>{{ $errors->first() }}</span></div>@endif

    <div class="detail-layout">
        <main>
            <section class="message-card">
                <div class="message-head">
                    <div>
                        <div class="hd-eyebrow">INCOMING MESSAGE</div>
                        <h1>{{ $type === 'email' ? ($source->subject ?: '(No subject)') : ($type === 'contact' ? ($source->subject ?: '(No subject)') : 'Career application') }}</h1>
                    </div>
                    <span class="status status-{{ $source->status }}">{{ str_replace('_',' ',ucfirst($source->status)) }}</span>
                </div>
                <div class="sender-card">
                    <div class="avatar">{{ strtoupper(mb_substr($type === 'email' ? ($source->sender_name ?: $source->sender_email) : $source->name,0,1)) }}</div>
                    <div class="sender-info">
                        <strong>{{ $type === 'email' ? ($source->sender_name ?: 'Unknown sender') : $source->name }}</strong>
                        <a href="mailto:{{ $type === 'email' ? $source->sender_email : $source->email }}">{{ $type === 'email' ? $source->sender_email : $source->email }}</a>
                    </div>
                    <time>{{ ($type === 'email' ? ($source->received_at ?: $source->created_at) : $source->created_at)->format('d M Y, h:i A') }}</time>
                </div>

                @if($type === 'email')
                    <div class="meta-grid">
                        @if($source->to_email)<div><span>To</span><strong>{{ $source->to_email }}</strong></div>@endif
                        @if($source->cc_email)<div><span>CC</span><strong>{{ $source->cc_email }}</strong></div>@endif
                        <div><span>Mailbox</span><strong>{{ $source->mailbox_group === 'career' ? 'career@fuelfreepowerplant.com' : 'info@fuelfreepowerplant.com' }}</strong></div>
                    </div>
                    <div class="message-label">Message</div>
                    <div class="email-body">{!! $source->body_html ?: nl2br(e($source->body_text ?: 'No message provided.')) !!}</div>
                    @if($source->attachments->isNotEmpty())
                    <div class="attachments"><div class="message-label">Attachments <span>{{ $source->attachments->count() }}</span></div><div class="attachment-list">
                        @foreach($source->attachments as $attachment)<a href="{{ route('admin.helpdesk.attachment',$attachment->id) }}"><i class="fa-solid fa-paperclip"></i><span>{{ $attachment->filename }}<small>{{ number_format($attachment->size / 1024,1) }} KB</small></span><i class="fa-solid fa-download"></i></a>@endforeach
                    </div></div>
                    @endif
                @else
                    <div class="meta-grid">
                        @if($source->phone)<div><span>Phone</span><strong>{{ $source->phone }}</strong></div>@endif
                        @if($type === 'career')<div><span>Position</span><strong>{{ $source->position ?: 'General application' }}</strong></div>@endif
                        @if($type === 'career' && $source->education)<div><span>Education</span><strong>{{ $source->education }}</strong></div>@endif
                        @if($type === 'career' && $source->experience)<div><span>Experience</span><strong>{{ $source->experience }}</strong></div>@endif
                    </div>
                    @if($type === 'career' && $source->cv_path)<div class="cv-box"><div><i class="fa-solid fa-file-pdf"></i><span><strong>{{ $source->cv_original_name ?: 'Curriculum Vitae' }}</strong><small>Candidate attachment</small></span></div><a href="{{ route('admin.career-applications.cv',$source) }}">Download CV</a></div>@endif
                    <div class="message-label">Message</div>
                    <div class="plain-body">{{ $source->message ?: 'No message provided.' }}</div>
                @endif
            </section>

            <section class="conversation-card">
                <div class="section-title"><div><h2>Conversation</h2><p>Complete reply history for this request</p></div><span>{{ $replies->count() }} replies</span></div>
                @forelse($replies->sortBy('created_at') as $reply)
                <article class="reply {{ $reply->status === 'failed' ? 'failed' : '' }}">
                    <div class="reply-avatar">{{ strtoupper(mb_substr($reply->adminUser->name,0,1)) }}</div>
                    <div class="reply-main"><div class="reply-head"><strong>{{ $reply->adminUser->name }}</strong><small>{{ $reply->sent_at?->format('d M Y, h:i A') ?: $reply->created_at->format('d M Y, h:i A') }} · {{ ucfirst($reply->status) }}</small></div><div class="reply-body">{{ $reply->body }}</div></div>
                </article>
                @empty<div class="conversation-empty"><i class="fa-regular fa-comments"></i><strong>No replies yet</strong><span>This conversation is waiting for its first response.</span></div>@endforelse
            </section>

            @if(auth()->user()->hasPermission('mail.manage'))
            <section class="reply-card" id="reply">
                <div class="section-title"><div><h2>Reply to sender</h2><p>Your response will be sent through the official mailbox</p></div><i class="fa-solid fa-paper-plane"></i></div>
                <div class="route-note"><i class="fa-solid fa-shield-halved"></i><span>Sending from <strong>{{ ($type === 'career' || ($type === 'email' && $source->mailbox_group === 'career')) ? 'career@fuelfreepowerplant.com' : 'info@fuelfreepowerplant.com' }}</strong></span></div>
                <form method="POST" action="{{ route('admin.helpdesk.reply',[$type,$source->id]) }}">
                    @csrf
                    <textarea name="body" rows="8" required placeholder="Write a professional response…"></textarea>
                    <div class="reply-footer"><span>Private Help Desk reply · not copied to hosting mailbox Sent folder</span><button type="submit"><i class="fa-solid fa-paper-plane"></i> Send reply</button></div>
                </form>
            </section>
            @endif
        </main>

        <aside class="detail-sidebar">
            @if(auth()->user()->hasPermission('mail.manage'))
            <section class="side-card">
                <div class="side-title"><i class="fa-solid fa-sliders"></i><div><strong>Conversation control</strong><small>Manage this record</small></div></div>
                <form method="POST" action="{{ route('admin.helpdesk.status',[$type,$source->id]) }}">
                    @csrf @method('PATCH')
                    <label for="status">Status</label>
                    <select id="status" name="status">
                        @foreach(($type==='career'?['new','reviewing','shortlisted','rejected','hired']:['new','read','in_progress','replied','closed']) as $option)
                            <option value="{{ $option }}" @selected($source->status===$option)>{{ str_replace('_',' ',ucfirst($option)) }}</option>
                        @endforeach
                    </select>
                    <button class="save-status" type="submit">Update status</button>
                </form>
            </section>
            @endif

            <section class="side-card">
                <div class="side-title"><i class="fa-solid fa-circle-info"></i><div><strong>Record details</strong><small>Help Desk metadata</small></div></div>
                <div class="side-details">
                    <div><span>Channel</span><strong>{{ $label }}</strong></div>
                    <div><span>Received</span><strong>{{ ($type === 'email' ? ($source->received_at ?: $source->created_at) : $source->created_at)->format('d M Y, h:i A') }}</strong></div>
                    @if($type==='email')<div><span>Mailbox</span><strong>{{ $source->mailbox_group==='career'?'Career':'Contact' }}</strong></div>@endif
                    <div><span>Replies</span><strong>{{ $replies->count() }}</strong></div>
                </div>
            </section>

            @if($type==='email' && auth()->user()->hasPermission('mail.manage'))
            <section class="side-card danger-card">
                <div class="side-title"><i class="fa-solid fa-trash-can"></i><div><strong>Danger zone</strong><small>Permanent action</small></div></div>
                <p>Delete this imported email and its stored attachments from the Help Desk server.</p>
                <form method="POST" action="{{ route('admin.helpdesk.email.delete',$source->id) }}" onsubmit="return confirm('Permanently delete this Help Desk email and its attachments?')">@csrf @method('DELETE')<button class="delete-btn" type="submit">Delete email</button></form>
            </section>
            @endif
        </aside>
    </div>
</div>
@endsection

@push('styles')
<style>
.hd-detail{max-width:1480px;margin:0 auto}.detail-topbar{display:flex;justify-content:space-between;align-items:center;gap:12px;margin-bottom:18px}.back-link{display:inline-flex;align-items:center;gap:8px;color:#8eabb5;text-decoration:none;font-size:11px;font-weight:700}.back-link:hover{color:#cbeaf1}.detail-layout{display:grid;grid-template-columns:minmax(0,1fr) 300px;gap:16px}.message-card,.conversation-card,.reply-card,.side-card{border:1px solid var(--line);border-radius:18px;background:rgba(255,255,255,.018)}.message-card{padding:22px}.message-head{display:flex;justify-content:space-between;align-items:flex-start;gap:16px}.message-head h1{font-size:clamp(22px,3vw,34px);line-height:1.18;letter-spacing:-.03em;margin:8px 0 20px;overflow-wrap:anywhere}.hd-eyebrow{font-size:9px;font-weight:800;letter-spacing:.15em;color:#5fd4f1}.channel{display:inline-flex;align-items:center;gap:6px;padding:6px 9px;border-radius:9px;font-size:9px;font-weight:800}.channel.contact{background:rgba(67,194,229,.08);color:#8ddcf0}.channel.career{background:rgba(160,120,255,.09);color:#c4a9ff}.status{display:inline-block;padding:6px 9px;border-radius:999px;background:rgba(255,255,255,.055);color:#a5bbc3;font-size:8px;font-weight:800;text-transform:capitalize;white-space:nowrap}.status-new{background:rgba(255,186,75,.1);color:#ffd08a}.status-replied{background:rgba(72,214,164,.09);color:#8be3c3}.status-in_progress,.status-reviewing{background:rgba(67,194,229,.09);color:#8edff2}.status-closed,.status-rejected{opacity:.65}.sender-card{display:flex;align-items:center;gap:11px;padding:12px;border-radius:14px;background:rgba(67,194,229,.035);border:1px solid rgba(104,204,235,.08)}.avatar,.reply-avatar{display:grid;place-items:center;border-radius:12px;background:rgba(67,194,229,.12);color:#85def2;font-weight:800}.avatar{width:40px;height:40px;flex:0 0 40px}.sender-info{min-width:0;flex:1}.sender-info strong,.sender-info a{display:block;overflow:hidden;text-overflow:ellipsis;white-space:nowrap}.sender-info strong{font-size:11px;color:#dbe9ed}.sender-info a{margin-top:3px;color:#75cbe0;font-size:10px;text-decoration:none}.sender-card time{color:#7895a0;font-size:9px;white-space:nowrap}.meta-grid{display:grid;grid-template-columns:repeat(3,minmax(0,1fr));gap:8px;margin:14px 0}.meta-grid>div{padding:10px 11px;border:1px solid rgba(104,204,235,.08);border-radius:11px}.meta-grid span,.side-details span{display:block;color:#658590;font-size:8px;text-transform:uppercase;letter-spacing:.08em}.meta-grid strong,.side-details strong{display:block;margin-top:4px;color:#bcd1d8;font-size:9px;overflow-wrap:anywhere}.message-label{margin:18px 0 9px;color:#83a5b0;font-size:9px;font-weight:800;text-transform:uppercase;letter-spacing:.1em}.email-body,.plain-body{color:#c4d5da;font-size:13px;line-height:1.85;overflow-wrap:anywhere}.email-body img{max-width:100%;height:auto}.email-body table{max-width:100%;display:block;overflow:auto}.plain-body{white-space:pre-wrap}.attachments{margin-top:18px}.message-label span{display:inline-grid;place-items:center;min-width:18px;height:18px;padding:0 5px;border-radius:9px;background:rgba(67,194,229,.1);color:#79d7ed;margin-left:4px}.attachment-list{display:grid;grid-template-columns:repeat(2,minmax(0,1fr));gap:8px}.attachment-list a{display:flex;align-items:center;gap:9px;padding:10px 11px;border:1px solid rgba(104,204,235,.1);border-radius:11px;color:#86d9eb;text-decoration:none;font-size:10px}.attachment-list a>i:last-child{margin-left:auto;font-size:9px}.attachment-list span{min-width:0;overflow:hidden}.attachment-list span strong,.attachment-list span small{display:block;overflow:hidden;text-overflow:ellipsis;white-space:nowrap}.attachment-list small{color:#6f8d97;margin-top:3px}.cv-box{display:flex;justify-content:space-between;align-items:center;gap:12px;padding:13px;border:1px solid rgba(104,204,235,.1);border-radius:13px;margin-top:16px}.cv-box>div{display:flex;align-items:center;gap:9px;min-width:0}.cv-box i{font-size:20px;color:#e77b7b}.cv-box strong,.cv-box small{display:block;overflow:hidden;text-overflow:ellipsis;white-space:nowrap}.cv-box strong{font-size:10px}.cv-box small{color:#6f8d97;font-size:8px;margin-top:3px}.cv-box a{padding:8px 10px;border-radius:9px;background:rgba(67,194,229,.09);color:#8edff2;text-decoration:none;font-size:9px;font-weight:800;white-space:nowrap}.conversation-card,.reply-card{margin-top:16px;padding:19px}.section-title{display:flex;justify-content:space-between;align-items:center;gap:12px}.section-title h2{margin:0;font-size:16px}.section-title p{margin:4px 0 0;color:#6f8e99;font-size:9px}.section-title>span{color:#7fa0ab;font-size:9px}.reply{display:flex;gap:10px;margin-top:12px;padding:12px;border:1px solid rgba(104,204,235,.08);border-radius:13px;background:rgba(67,194,229,.025)}.reply.failed{border-color:rgba(255,100,100,.22)}.reply-avatar{width:34px;height:34px;flex:0 0 34px;border-radius:10px;font-size:10px}.reply-main{min-width:0;flex:1}.reply-head{display:flex;justify-content:space-between;gap:10px}.reply-head strong{font-size:10px}.reply-head small{color:#6f8d97;font-size:8px}.reply-body{margin-top:9px;color:#b8cbd1;font-size:11px;line-height:1.7;white-space:pre-wrap;overflow-wrap:anywhere}.conversation-empty{text-align:center;padding:30px 10px;color:#6f8d97}.conversation-empty i{display:block;font-size:22px;margin-bottom:8px}.conversation-empty strong,.conversation-empty span{display:block}.conversation-empty strong{font-size:11px;color:#b9ccd2}.conversation-empty span{font-size:9px;margin-top:4px}.reply-card .section-title>i{color:#6fd5ed}.route-note{display:flex;align-items:center;gap:8px;padding:10px 11px;margin:13px 0;border:1px solid rgba(72,214,164,.12);border-radius:10px;background:rgba(72,214,164,.035);color:#80b3a3;font-size:9px}.route-note i{color:#58d0a3}.route-note strong{color:#a4e8d1}.reply-card textarea{width:100%;box-sizing:border-box;border:1px solid rgba(104,204,235,.12);border-radius:12px;background:#061721;color:#e8f6f8;padding:13px;resize:vertical;outline:0;font:inherit;font-size:12px;line-height:1.7}.reply-card textarea:focus{border-color:rgba(104,204,235,.3);box-shadow:0 0 0 3px rgba(67,194,229,.04)}.reply-footer{display:flex;justify-content:space-between;align-items:center;gap:12px;margin-top:10px}.reply-footer span{color:#627f8a;font-size:8px}.reply-footer button,.save-status{border:0;border-radius:10px;padding:10px 14px;background:#2da9ca;color:#fff;font-size:10px;font-weight:800;cursor:pointer}.detail-sidebar{display:flex;flex-direction:column;gap:12px}.side-card{padding:15px}.side-title{display:flex;align-items:center;gap:9px;padding-bottom:12px;border-bottom:1px solid rgba(255,255,255,.055)}.side-title>i{width:30px;height:30px;display:grid;place-items:center;border-radius:9px;background:rgba(67,194,229,.08);color:#73d4eb;font-size:11px}.side-title strong,.side-title small{display:block}.side-title strong{font-size:11px}.side-title small{margin-top:3px;color:#688692;font-size:8px}.side-card label{display:block;margin:14px 0 7px;color:#7898a3;font-size:9px}.side-card select{width:100%;border:1px solid rgba(104,204,235,.12);border-radius:10px;background:#071b25;color:#d9ebef;padding:10px;font-size:10px;outline:0}.save-status{width:100%;margin-top:9px}.side-details{display:grid;gap:11px;padding-top:13px}.side-details strong{font-size:10px}.danger-card{border-color:rgba(255,100,100,.14)}.danger-card .side-title>i{background:rgba(255,100,100,.07);color:#ff9292}.danger-card p{color:#708b95;font-size:9px;line-height:1.6}.delete-btn{width:100%;border:1px solid rgba(255,100,100,.2);border-radius:10px;background:rgba(255,100,100,.07);color:#ff9f9f;padding:9px;font-size:9px;font-weight:800;cursor:pointer}.hd-alert{display:flex;gap:10px;align-items:center;padding:12px 15px;border-radius:13px;margin-bottom:14px;font-size:11px}.hd-alert.success{border:1px solid rgba(72,214,164,.18);background:rgba(72,214,164,.06);color:#a9ead2}.hd-alert.error{border:1px solid rgba(255,100,100,.2);background:rgba(255,100,100,.06);color:#ffb0b0}
@media(max-width:1000px){.detail-layout{grid-template-columns:1fr}.detail-sidebar{display:grid;grid-template-columns:repeat(2,minmax(0,1fr));align-items:start}.detail-sidebar .danger-card{grid-column:1/-1}}
@media(max-width:700px){.detail-topbar{margin-bottom:12px}.detail-layout{display:block}.message-card,.conversation-card,.reply-card,.side-card{border-radius:15px}.message-card{padding:15px}.message-head{display:block}.message-head h1{font-size:22px;margin-bottom:13px}.message-head>.status{display:inline-block}.sender-card{align-items:flex-start}.sender-card time{margin-left:auto}.meta-grid{grid-template-columns:1fr 1fr}.meta-grid>div:last-child:nth-child(odd){grid-column:1/-1}.email-body,.plain-body{font-size:12px;line-height:1.75}.attachment-list{grid-template-columns:1fr}.conversation-card,.reply-card{padding:14px;margin-top:10px}.section-title h2{font-size:14px}.reply-head{display:block}.reply-head small{display:block;margin-top:3px}.reply-footer{display:block}.reply-footer span{display:block;margin-bottom:9px;line-height:1.5}.reply-footer button{width:100%;padding:12px}.detail-sidebar{display:block;margin-top:10px}.side-card{margin-top:10px}.cv-box{align-items:flex-start;flex-direction:column}.cv-box a{width:100%;text-align:center}.back-link{font-size:10px}}
</style>
@endpush