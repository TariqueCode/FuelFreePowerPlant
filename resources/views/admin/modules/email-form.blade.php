@extends('layouts.portal')
@section('title','Add Mailbox')
@section('content')
<section class="hero"><div class="eyebrow">MANAGED EMAIL</div><h1>Add mailbox</h1><p>Store the mailbox configuration securely. The encrypted password is never displayed back in the interface.</p></section>
@if($errors->any())<div class="errors">{{ $errors->first() }}</div>@endif
<div class="form-card"><form method="POST" action="{{ route('admin.email.store') }}">@csrf<div class="fields">
<div><label>Owner</label><select name="user_id" required>@foreach($users as $user)<option value="{{ $user->id }}">{{ $user->name }} — {{ $user->email }}</option>@endforeach</select></div>
<div><label>Mailbox address</label><input type="email" name="address" value="{{ old('address') }}" required></div>
<div><label>Display name</label><input name="display_name" value="{{ old('display_name') }}"></div><div><label>Status</label><select name="status"><option value="active">Active</option><option value="suspended">Suspended</option></select></div>
<div><label>IMAP host</label><input name="imap_host" value="{{ old('imap_host') }}" placeholder="mail.example.com"></div><div><label>IMAP port</label><input type="number" name="imap_port" value="{{ old('imap_port',993) }}" min="1" max="65535"></div>
<div><label>SMTP host</label><input name="smtp_host" value="{{ old('smtp_host') }}" placeholder="mail.example.com"></div><div><label>SMTP port</label><input type="number" name="smtp_port" value="{{ old('smtp_port',465) }}" min="1" max="65535"></div>
<div><label>Username</label><input name="username" value="{{ old('username') }}"></div><div><label>Password</label><input type="password" name="password" autocomplete="new-password"></div>
</div><div class="actions"><a href="{{ route('admin.email') }}">Cancel</a><button type="submit">Save mailbox</button></div></form></div>
@endsection
@push('styles')<style>.form-card{max-width:900px;background:rgba(255,255,255,.025);border:1px solid var(--line);border-radius:18px;padding:22px}.fields{display:grid;grid-template-columns:1fr 1fr;gap:16px}label{display:block;font-size:12px;color:#9eb9c4;margin:0 0 7px}input,select{width:100%;box-sizing:border-box;padding:13px;border-radius:11px;border:1px solid var(--line);background:#071c29;color:#e9f7fb;outline:none}.errors{margin-bottom:16px;padding:11px;border-radius:10px;background:rgba(210,65,65,.12);color:#ffb0b0}.actions{display:flex;justify-content:flex-end;gap:12px;margin-top:22px;align-items:center}.actions a{color:#8ca9b6;text-decoration:none}.actions button{border:0;border-radius:11px;padding:12px 17px;background:#31afd2;color:#fff;font-weight:700}@media(max-width:650px){.fields{grid-template-columns:1fr}}</style>@endpush
