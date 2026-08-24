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
            <div class="full"><label>Role</label><select name="role_id" required><option value="">Select a role</option>@foreach($roles as $role)<option value="{{ $role->id }}" @selected(old('role_id') == $role->id)>{{ $role->name }}</option>@endforeach</select></div>
        </div>
        <div class="actions"><a href="{{ route('admin.users.index') }}">Cancel</a><button type="submit">Create account</button></div>
    </form>
</div>
@endsection

@push('styles')
<style>
.form-card{max-width:760px;background:rgba(255,255,255,.025);border:1px solid rgba(110,200,235,.12);border-radius:18px;padding:22px}.fields{display:grid;grid-template-columns:1fr 1fr;gap:16px}label{display:block;font-size:12px;color:#9eb9c4;margin:0 0 7px}input,select{width:100%;box-sizing:border-box;padding:13px;border-radius:11px;border:1px solid rgba(110,200,235,.15);background:#071c29;color:#e9f7fb;outline:none}.full{grid-column:1/-1}.errors{margin-bottom:16px;padding:11px;border-radius:10px;background:rgba(210,65,65,.12);color:#ffb0b0}.actions{display:flex;justify-content:flex-end;gap:12px;margin-top:22px;align-items:center}.actions a{color:#8ca9b6;text-decoration:none;font-size:13px}.actions button{border:0;border-radius:11px;padding:12px 17px;background:#31afd2;color:#fff;font-weight:700}@media(max-width:650px){.fields{grid-template-columns:1fr}.full{grid-column:auto}}
</style>
@endpush
