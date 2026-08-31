@extends('layouts.portal')

@section('title', 'Create User')
@section('eyebrow', 'ADMINISTRATION')
@section('heading', 'Create user')
@section('description', 'Provision a staff or client account with a system role.')

@section('content')
<div class="form-card">
    @if($errors->any())
        <div class="errors">{{ $errors->first() }}</div>
    @endif
    <form method="POST" action="{{ route('admin.users.store') }}">
        @csrf
        <div class="fields">
            <div><label>Name</label><input name="name" value="{{ old('name') }}" required maxlength="120"></div>
            <div><label>Email</label><input name="email" type="email" value="{{ old('email') }}" required maxlength="255"></div>
            <div><label>Password</label><input name="password" type="password" required minlength="12"></div>
            <div><label>Confirm password</label><input name="password_confirmation" type="password" required minlength="12"></div>
            <div class="full"><label for="role_id">Responsibility</label><select id="role_id" name="role_id" required><option value="">Choose what this person can manage</option>@foreach($roles as $role)<option value="{{ $role->id }}" @selected(old('role_id') == $role->id)>{{ $role->name }}</option>@endforeach</select><div id="capabilityHint" class="capability-hint">Choose a responsibility to see what this account can manage.</div></div>
        </div>
        <div class="actions"><a href="{{ route('admin.users.index') }}">Cancel</a><button type="submit">Create account</button></div>
    </form>
</div>
@endsection

@push('scripts')<script>const roleCapabilities=@json($roles->mapWithKeys(fn($r)=>[$r->id=>$r->permissions->pluck('name')->values()]));const roleSelect=document.getElementById('role_id'),hint=document.getElementById('capabilityHint');function showCaps(){const caps=roleCapabilities[roleSelect.value]||[];hint.innerHTML=caps.length?'<strong>This account can manage:</strong><ul>'+caps.map(c=>'<li>'+String(c).replace(/[&<>]/g,m=>({'&':'&amp;','<':'&lt;','>':'&gt;'}[m]))+'</li>').join('')+'</ul>':'Choose a responsibility to see what this account can manage.'}roleSelect.addEventListener('change',showCaps);showCaps();</script>@endpush

@push('styles')
<style>
.form-card{max-width:760px;background:rgba(255,255,255,.025);border:1px solid rgba(110,200,235,.12);border-radius:18px;padding:22px}.fields{display:grid;grid-template-columns:1fr 1fr;gap:16px}label{display:block;font-size:12px;color:#9eb9c4;margin:0 0 7px}input,select{width:100%;box-sizing:border-box;padding:13px;border-radius:11px;border:1px solid rgba(110,200,235,.15);background:#071c29;color:#e9f7fb;outline:none}.full{grid-column:1/-1}.capability-hint{margin-top:9px;padding:11px 13px;border-radius:10px;background:rgba(67,194,137,.07);border:1px solid rgba(67,194,137,.14);color:#9ecdbb;font-size:12px}.capability-hint ul{margin:7px 0 0;padding-left:18px}.capability-hint li{margin:3px 0}.errors{margin-bottom:16px;padding:11px;border-radius:10px;background:rgba(210,65,65,.12);color:#ffb0b0}.actions{display:flex;justify-content:flex-end;gap:12px;margin-top:22px;align-items:center}.actions a{color:#8ca9b6;text-decoration:none;font-size:13px}.actions button{border:0;border-radius:11px;padding:12px 17px;background:#31afd2;color:#fff;font-weight:700}@media(max-width:650px){.fields{grid-template-columns:1fr}.full{grid-column:auto}}
</style>
@endpush
