@extends('layouts.portal')
@section('title','Help Desk')
@section('content')
<section class="mail-hero">
    <div>
        <span class="eyebrow">SUPPORT &amp; COMMUNICATIONS</span>
        <h1>Help Desk</h1>
        <p>One professional workspace for every connected mailbox. Switch between email addresses, read incoming messages, reply, compose and review sent mail.</p>
    </div>
    <div class="hero-actions">
        @if(auth()->user()->hasPermission('mail.manage'))
            <button class="add-btn" type="button" onclick="document.getElementById('mail-add').showModal()"><i class="fa-solid fa-plus"></i> Add email</button>
            <button class="ghost-btn" type="button" onclick="document.getElementById('mail-list').showModal()"><i class="fa-solid fa-list"></i> Mail list</button>
        @endif
    </div>
</section>

@if(session('status'))<div class="notice ok"><i class="fa-solid fa-circle-check"></i>{{ session('status') }}</div>@endif
@if($error)<div class="notice err"><i class="fa-solid fa-triangle-exclamation"></i>{{ $error }}</div>@endif

<div class="mail-layout">
    <aside class="mail-sidebar">
        <div class="side-head"><span>CONNECTED MAILBOXES</span><b>{{ $accounts->where('status','active')->count() }}</b></div>
        @forelse($accounts->where('status','active') as $account)
            <a class="mail-cat {{ $selected?->id===$account->id?'active':'' }}" href="{{ route('admin.mail',['account'=>$account->id,'folder'=>'INBOX']) }}">
                <span class="mail-icon"><i class="fa-solid fa-envelope"></i></span>
                <span class="mail-label"><strong>{{ $account->display_name ?: $account->address }}</strong><small>{{ $account->address }}</small></span>
                <i class="fa-solid fa-chevron-right chevron"></i>
            </a>
        @empty
            <div class="empty-small"><i class="fa-regular fa-envelope"></i><p>No active mailboxes.</p><button class="link-btn" type="button" onclick="document.getElementById('mail-add').showModal()">Connect an email</button></div>
        @endforelse
    </aside>

    <section class="mail-main">
        @if($selected)
            <div class="mail-toolbar">
                <div class="mail-title">
                    <span class="mail-avatar"><i class="fa-solid fa-at"></i></span>
                    <div><span class="eyebrow">CONNECTED MAILBOX</span><h2>{{ $selected->address }}</h2><small>{{ $selected->display_name ?: 'Mailbox' }}</small></div>
                </div>
                <a class="add-btn small" href="{{ route('admin.mail.compose',$selected) }}"><i class="fa-solid fa-pen"></i> Compose</a>
            </div>

            <nav class="folder-tabs" aria-label="Mail folders">
                @foreach($folders as $f)
                    <a class="{{ $folder===$f['name']?'active':'' }}" href="{{ route('admin.mail',['account'=>$selected->id,'folder'=>$f['name']]) }}">
                        <i class="fa-solid {{ $f['icon'] }}"></i>{{ $f['label'] }}
                    </a>
                @endforeach
            </nav>

            <div class="list-head"><span>{{ $folder==='INBOX' ? 'Inbox' : ($folders[array_search($folder,array_column($folders,'name'))]['label'] ?? $folder) }}</span><small>{{ count($messages) }} messages</small></div>
            <div class="message-list">
                @forelse($messages as $message)
                    <a class="mail-row {{ $message['seen']?'':'unread' }}" href="{{ route('admin.mail.message',[$selected,$message['uid'],'folder'=>$folder]) }}">
                        <span class="mail-status"></span>
                        <div class="mail-from">{{ $folder==='INBOX' ? ($message['from'] ?: 'Unknown sender') : ($message['to'] ?: 'Unknown recipient') }}</div>
                        <div class="mail-subject"><strong>{{ $message['subject'] }}</strong></div>
                        <time>{{ $message['date'] }}</time>
                    </a>
                @empty
                    <div class="empty-state"><i class="fa-regular fa-envelope-open"></i><h2>{{ $folder==='INBOX' ? 'Inbox is empty' : 'No sent messages' }}</h2><p>Messages from this folder will appear here automatically.</p></div>
                @endforelse
            </div>
        @else
            <div class="empty-state large"><i class="fa-solid fa-inbox"></i><h2>Connect a mailbox to get started</h2><p>Add an email address and verify its IMAP credentials. Every connected address will become its own mailbox tab.</p></div>
        @endif
    </section>
</div>

@if(auth()->user()->hasPermission('mail.manage'))
<dialog id="mail-add">
    <form method="POST" action="{{ route('admin.mail.accounts.store') }}">
        @csrf
        <div class="dialog-head"><div><span class="eyebrow">MAILBOX SETUP</span><h2>Connect an email</h2><p>Passwords are encrypted at rest. The mailbox is verified before it is saved.</p></div><button type="button" class="close-btn" onclick="this.closest('dialog').close()"><i class="fa-solid fa-xmark"></i></button></div>
        <div class="dialog-grid">
            <label>Email address <input id="mail-address" type="email" name="address" required placeholder="info@fuelfreepowerplant.com"></label>
            <label>Display name <input name="display_name" placeholder="Information &amp; Support"></label>
            <label class="full">Email provider
                <select id="mail-provider" name="provider" onchange="applyMailPreset()">
                    <option value="cpanel">cPanel / Custom domain</option>
                    <option value="gmail">Gmail / Google Workspace</option>
                    <option value="outlook">Outlook / Microsoft 365</option>
                    <option value="custom">Other provider</option>
                </select>
            </label>
            <label>Mailbox password / App password <input type="password" name="password" required autocomplete="new-password" placeholder="Enter the mailbox password"></label>
        </div>
        <div class="provider-help" id="provider-help"><i class="fa-solid fa-circle-info"></i><span>For cPanel email, use the mailbox password. For Gmail, use a Google App Password rather than your normal Google password.</span></div>
        <details><summary><i class="fa-solid fa-server"></i> Advanced server settings</summary>
            <div class="dialog-grid advanced">
                <label>IMAP host<input id="imap-host" name="imap_host" value="{{ config('cpanel.mail_host') }}" placeholder="imap.example.com"></label>
                <label>IMAP port<input id="imap-port" type="number" name="imap_port" value="993"></label>
                <label>SMTP host<input id="smtp-host" name="smtp_host" value="{{ config('cpanel.mail_host') }}" placeholder="smtp.example.com"></label>
                <label>SMTP port<input id="smtp-port" type="number" name="smtp_port" value="465"></label>
            </div>
        </details>
        <div class="dialog-actions"><button type="button" class="ghost-btn" onclick="this.closest('dialog').close()">Cancel</button><button class="add-btn" type="submit"><i class="fa-solid fa-plug-circle-check"></i> Verify &amp; connect</button></div>
    </form>
</dialog>

<dialog id="mail-list">
    <div class="dialog-head"><div><span class="eyebrow">MAILBOX MANAGEMENT</span><h2>Mail list</h2><p>Manage every email connected to the Help Desk.</p></div><button type="button" class="close-btn" onclick="this.closest('dialog').close()"><i class="fa-solid fa-xmark"></i></button></div>
    <div class="account-list">
        @forelse($accounts as $account)
            <div class="account-item">
                <span class="account-icon"><i class="fa-solid fa-envelope"></i></span>
                <div class="account-info"><strong>{{ $account->address }}</strong><small>{{ $account->display_name ?: 'No display name' }}</small></div>
                <span class="status-pill {{ $account->status==='active'?'on':'off' }}"><i></i>{{ ucfirst($account->status) }}</span>
                <form method="POST" action="{{ route('admin.mail.accounts.toggle',$account) }}">@csrf @method('PATCH')<button class="icon-action" title="{{ $account->status==='active'?'Deactivate':'Activate' }}"><i class="fa-solid {{ $account->status==='active'?'fa-toggle-on':'fa-toggle-off' }}"></i></button></form>
                <form method="POST" action="{{ route('admin.mail.accounts.destroy',$account) }}" onsubmit="return confirm('Remove {{ $account->address }} from Help Desk?')">@csrf @method('DELETE')<button class="icon-action danger" title="Delete"><i class="fa-solid fa-trash"></i></button></form>
            </div>
        @empty
            <div class="empty-small">No email accounts configured.</div>
        @endforelse
    </div>
</dialog>
@endif
@endsection

@push('styles')
<style>
.mail-hero{display:flex;justify-content:space-between;align-items:flex-end;gap:20px;padding:4px 0 20px}.mail-hero h1{font-size:clamp(30px,5vw,48px);margin:7px 0}.mail-hero p{color:#7898a5;font-size:11px;line-height:1.7;max-width:720px;margin:0}.hero-actions{display:flex;gap:8px;flex-wrap:wrap}.add-btn,.ghost-btn{display:inline-flex;align-items:center;justify-content:center;gap:7px;padding:10px 13px;border-radius:10px;font-size:10px;font-weight:800;cursor:pointer;text-decoration:none}.add-btn{border:0;background:linear-gradient(135deg,#37c5e6,#168faf);color:#fff}.ghost-btn{border:1px solid var(--line);background:rgba(255,255,255,.025);color:#a9c9d2}.add-btn.small{white-space:nowrap}.notice{display:flex;gap:9px;align-items:center;padding:11px 13px;border-radius:11px;margin-bottom:12px;font-size:10px}.notice.ok{border:1px solid rgba(67,224,173,.2);background:rgba(67,224,173,.07);color:#a9ead2}.notice.err{border:1px solid rgba(255,112,112,.2);background:rgba(255,112,112,.07);color:#ffc1c1}.mail-layout{display:grid;grid-template-columns:280px minmax(0,1fr);gap:13px}.mail-sidebar,.mail-main{border:1px solid var(--line);border-radius:18px;background:rgba(5,22,32,.78);min-width:0}.mail-sidebar{padding:10px}.side-head{display:flex;justify-content:space-between;align-items:center;padding:8px;color:#668692;font-size:8px;letter-spacing:.14em}.side-head b{font-size:9px;color:#9dc7d1;letter-spacing:0}.mail-cat{display:flex;align-items:center;gap:9px;padding:11px;border-radius:12px;color:#86a5b4;text-decoration:none}.mail-cat.active,.mail-cat:hover{background:rgba(67,194,229,.08);color:#eaf8fb}.mail-label{min-width:0;flex:1}.mail-cat strong,.mail-cat small{display:block;overflow:hidden;text-overflow:ellipsis;white-space:nowrap}.mail-cat strong{font-size:10px}.mail-cat small{font-size:8px;color:#668692;margin-top:3px}.mail-icon,.mail-avatar,.account-icon{display:grid;place-items:center;flex:none;border-radius:10px;background:rgba(67,194,229,.08);color:#5fd5ef}.mail-icon{width:30px;height:30px}.mail-avatar{width:42px;height:42px}.chevron{font-size:8px;color:#52717c}.mail-main{overflow:hidden}.mail-toolbar{display:flex;justify-content:space-between;align-items:center;gap:12px;padding:15px 16px;border-bottom:1px solid rgba(255,255,255,.05)}.mail-title{display:flex;align-items:center;gap:11px;min-width:0}.mail-title h2{font-size:15px;margin:4px 0 2px;overflow:hidden;text-overflow:ellipsis;white-space:nowrap}.mail-title small{color:#668692;font-size:8px}.folder-tabs{display:flex;gap:4px;padding:9px 12px;border-bottom:1px solid rgba(255,255,255,.05);overflow:auto}.folder-tabs a{display:inline-flex;align-items:center;gap:6px;padding:8px 10px;border-radius:8px;color:#71919c;text-decoration:none;font-size:9px;white-space:nowrap}.folder-tabs a.active,.folder-tabs a:hover{background:rgba(67,194,229,.09);color:#dff7fb}.list-head{display:flex;justify-content:space-between;padding:11px 15px;color:#7898a5;font-size:9px;background:rgba(255,255,255,.012)}.list-head small{color:#526f7a}.mail-row{display:grid;grid-template-columns:9px minmax(150px,.6fr) minmax(160px,1fr) 170px;gap:10px;align-items:center;padding:13px 15px;border-top:1px solid rgba(255,255,255,.045);color:#87a3ae;text-decoration:none;font-size:9px}.mail-row.unread{color:#e4f5f8;background:rgba(67,194,229,.035)}.mail-status{width:6px;height:6px;border-radius:50%;background:#4a6873}.unread .mail-status{background:#55d7ef;box-shadow:0 0 8px rgba(85,215,239,.6)}.mail-subject{overflow:hidden;text-overflow:ellipsis;white-space:nowrap}.mail-subject strong{font-weight:700}.mail-row time{font-size:8px;color:#607d88;white-space:nowrap}.empty-small,.empty-state{text-align:center;color:#7898a5;padding:30px}.empty-small i{font-size:22px;color:#5fd5ef}.empty-small p{margin:8px 0;font-size:9px}.empty-state.large{padding:70px 20px}.empty-state>i{font-size:34px;color:#5fd5ef}.empty-state h2{font-size:16px;color:#c9e1e7}.empty-state p{font-size:10px}.link-btn{border:0;background:none;color:#5fd5ef;font-size:9px;cursor:pointer}.dialog-head{display:flex;justify-content:space-between;gap:18px;align-items:flex-start}.dialog-head h2{margin:5px 0;font-size:21px}.dialog-head p{margin:0;color:#718f9c;font-size:9px;line-height:1.5}.close-btn{border:0;background:transparent;color:#77949e;font-size:15px;cursor:pointer}.dialog-grid{display:grid;grid-template-columns:1fr 1fr;gap:13px;margin-top:18px}.dialog-grid label{display:block;color:#91adb6;font-size:10px}.dialog-grid .full{grid-column:1/-1}.dialog-grid input,.dialog-grid select{width:100%;box-sizing:border-box;margin-top:6px;padding:11px;border:1px solid var(--line);border-radius:10px;background:#03131d;color:#eaf8fb;outline:none}.provider-help{display:flex;gap:8px;margin-top:12px;padding:10px 11px;border-radius:10px;background:rgba(67,194,229,.05);border:1px solid rgba(67,194,229,.08);color:#7898a5;font-size:8px;line-height:1.5}.provider-help i{color:#55cde8}.advanced{margin-top:12px}.dialog-head+details{margin-top:14px}dialog{width:min(600px,calc(100% - 24px));max-height:88vh;overflow:auto;border:1px solid var(--line);border-radius:18px;background:#061a24;color:#eaf8fb;padding:20px}dialog::backdrop{background:rgba(0,0,0,.68)}dialog summary{color:#83b3bf;font-size:9px;cursor:pointer;list-style:none}.dialog-actions{display:flex;justify-content:flex-end;gap:8px;margin-top:18px}.account-list{display:grid;gap:7px;margin-top:16px}.account-item{display:grid;grid-template-columns:34px minmax(0,1fr) auto 30px 30px;gap:8px;align-items:center;padding:10px;border:1px solid rgba(86,210,238,.1);border-radius:11px;background:rgba(67,194,229,.025)}.account-icon{width:34px;height:34px}.account-info{min-width:0}.account-info strong,.account-info small{display:block;overflow:hidden;text-overflow:ellipsis;white-space:nowrap}.account-info strong{font-size:9px}.account-info small{font-size:8px;color:#668692;margin-top:2px}.status-pill{display:inline-flex;align-items:center;gap:5px;font-size:7px;padding:5px 7px;border-radius:999px}.status-pill i{width:5px;height:5px;border-radius:50%}.status-pill.on{color:#91e4c4;background:rgba(67,224,173,.08)}.status-pill.on i{background:#45d59f}.status-pill.off{color:#a7b5b9;background:rgba(150,170,176,.08)}.status-pill.off i{background:#77888d}.icon-action{border:0;background:transparent;color:#72bdcc;cursor:pointer;font-size:11px}.icon-action.danger{color:#d77f89}.icon-action:hover{color:#eaf8fb}@media(max-width:850px){.mail-layout{grid-template-columns:1fr}.mail-sidebar{display:flex;overflow:auto;gap:5px;padding:8px}.side-head{display:none}.mail-cat{min-width:230px}.mail-row{grid-template-columns:8px minmax(120px,.7fr) 1fr}.mail-row time{display:none}}@media(max-width:600px){.mail-hero{display:block}.hero-actions{margin-top:12px}.hero-actions .add-btn,.hero-actions .ghost-btn{flex:1}.mail-toolbar{align-items:flex-start}.mail-title h2{max-width:210px}.dialog-grid{grid-template-columns:1fr}.dialog-grid .full{grid-column:auto}.account-item{grid-template-columns:32px minmax(0,1fr) auto 28px 28px}.account-item .status-pill{display:none}.mail-row{grid-template-columns:8px 1fr}.mail-subject{grid-column:2}.folder-tabs{padding-left:8px;padding-right:8px}dialog{padding:16px}}
</style>
@endpush

@push('scripts')
<script>
function applyMailPreset(){
    const provider=document.getElementById('mail-provider').value;
    const presets={
        cpanel:{imap:'{{ config('cpanel.mail_host') }}',imapPort:993,smtp:'{{ config('cpanel.mail_host') }}',smtpPort:465},
        gmail:{imap:'imap.gmail.com',imapPort:993,smtp:'smtp.gmail.com',smtpPort:465},
        outlook:{imap:'outlook.office365.com',imapPort:993,smtp:'smtp.office365.com',smtpPort:587},
        custom:{imap:'',imapPort:993,smtp:'',smtpPort:465}
    };
    const p=presets[provider];
    document.getElementById('imap-host').value=p.imap||'';
    document.getElementById('imap-port').value=p.imapPort;
    document.getElementById('smtp-host').value=p.smtp||'';
    document.getElementById('smtp-port').value=p.smtpPort;
    document.getElementById('provider-help').innerHTML=provider==='gmail'
        ? '<i class="fa-solid fa-circle-info"></i><span>Gmail / Google Workspace requires an App Password for IMAP/SMTP authentication. Your normal Google account password will not work.</span>'
        : provider==='outlook'
        ? '<i class="fa-solid fa-circle-info"></i><span>Microsoft 365 may require an app password or tenant-approved SMTP authentication, depending on your account policy.</span>'
        : '<i class="fa-solid fa-circle-info"></i><span>Verify the mailbox before it is saved. Credentials are stored encrypted.</span>';
}
</script>
@endpush