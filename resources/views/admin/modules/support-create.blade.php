@extends('layouts.portal')

@section('title', 'New Support Ticket')
@section('content')
<section class="hero"><div class="eyebrow">SERVICE DESK</div><h1>New ticket</h1><p>Describe the issue clearly so the support team can respond quickly.</p></section>
<div class="form-card">
    @if($errors->any()) <div class="errors">{{ $errors->first() }}</div> @endif
    <form method="POST" action="{{ route('admin.support.store') }}">
        @csrf
        <div class="fields">
            <div class="full"><label>Subject</label><input name="subject" value="{{ old('subject') }}" maxlength="180" required></div>
            <div><label>Priority</label><select name="priority" required><option value="low">Low</option><option value="normal" selected>Normal</option><option value="high">High</option></select></div>
            <div class="full"><label>Message</label><textarea name="body" rows="8" required>{{ old('body') }}</textarea></div>
        </div>
        <div class="actions"><a href="{{ route('admin.support') }}">Cancel</a><button type="submit">Create ticket</button></div>
    </form>
</div>
@endsection

@push('styles')
<style>
.form-card{max-width:820px;background:rgba(255,255,255,.025);border:1px solid var(--line);border-radius:18px;padding:22px}.fields{display:grid;grid-template-columns:1fr 1fr;gap:16px}.full{grid-column:1/-1}label{display:block;font-size:12px;color:#9eb9c4;margin:0 0 7px}input,select,textarea{width:100%;box-sizing:border-box;padding:13px;border-radius:11px;border:1px solid var(--line);background:#071c29;color:#e9f7fb;outline:none;font:inherit}.errors{margin-bottom:16px;padding:11px;border-radius:10px;background:rgba(210,65,65,.12);color:#ffb0b0}.actions{display:flex;justify-content:flex-end;gap:12px;margin-top:22px;align-items:center}.actions a{color:#8ca9b6;text-decoration:none;font-size:13px}.actions button{border:0;border-radius:11px;padding:12px 17px;background:#31afd2;color:#fff;font-weight:700}@media(max-width:620px){.fields{grid-template-columns:1fr}.full{grid-column:auto}}
</style>
@endpush
