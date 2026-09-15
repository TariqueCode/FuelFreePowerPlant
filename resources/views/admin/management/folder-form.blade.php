@extends('layouts.portal')
@section('title',$folder->exists?'Edit Profile Folder':'New Profile Folder')
@section('content')
<section class="hero profile-page-header">
    <div>
        <span class="eyebrow">GLOBAL · PROFILE BUILDER</span>
        <h1>{{ $folder->exists?'Edit folder':'Create folder' }}</h1>
        <p>The folder name becomes the public leadership section title and its URL slug.</p>
    </div>
    <div class="hero-actions">
        <a class="back" href="{{ route('admin.profile-builder.index') }}"><i class="fa-solid fa-arrow-left"></i> Back to Profile Builder</a>
    </div>
</section>
@if($errors->any())<div class="errors">{{ $errors->first() }}</div>@endif
<div class="card form">
    <form method="POST" action="{{ $folder->exists?route('admin.profile-builder.folders.update',$folder):route('admin.profile-builder.folders.store') }}">
        @csrf @if($folder->exists)@method('PATCH')@endif
        <label>Folder name</label>
        <input name="name" required maxlength="120" value="{{ old('name',$folder->name) }}" placeholder="Board of Directors">
        <div class="help">Example: <strong>Board of Directors</strong> → <strong>/board-of-directors</strong></div>
        <label>Status</label>
        <select name="status"><option value="published" @selected(old('status',$folder->status)==='published')>Published</option><option value="draft" @selected(old('status',$folder->status)==='draft')>Draft</option></select>
        <div class="actions"><a class="back" href="{{ route('admin.profile-builder.index') }}">Cancel</a><button class="save" type="submit"><i class="fa-solid fa-floppy-disk"></i> {{ $folder->exists?'Save folder':'Create folder' }}</button></div>
    </form>
</div>
@endsection
@push('styles')
<style>
.hero.profile-page-header{position:relative;display:flex;align-items:center;justify-content:space-between;gap:24px;margin:0 0 26px;padding:6px 0 22px;background:transparent;border:0;border-radius:0;box-shadow:none;overflow:visible}
.hero.profile-page-header:after{content:"";position:absolute;left:0;bottom:0;width:58px;height:3px;border-radius:3px;background:#35d8b0}
.hero.profile-page-header>div{background:transparent;border:0;box-shadow:none}
.eyebrow{font-size:9px;letter-spacing:.16em;color:#54cde8;font-weight:800}
.hero h1{margin:6px 0;font-size:clamp(27px,4vw,42px);letter-spacing:-.025em}
.hero p{margin:0;color:#7898a5;font-size:11px;max-width:720px;line-height:1.6}
.hero-actions{display:flex;align-items:center;justify-content:flex-end;gap:8px;flex:0 0 auto}
.back{display:inline-flex;align-items:center;justify-content:center;gap:7px;padding:10px 13px;border:1px solid var(--line);border-radius:11px;color:#9db9c2;text-decoration:none;font-size:10px;white-space:nowrap;background:rgba(255,255,255,.018)}
.card.form{max-width:780px;padding:24px;border:1px solid rgba(84,205,232,.16);border-radius:18px;background:linear-gradient(145deg,rgba(9,31,40,.92),rgba(4,20,27,.96));box-shadow:0 14px 36px rgba(0,0,0,.16)}
label{display:block;color:#8aa7b1;font-size:9px;margin:0 0 7px;font-weight:700;letter-spacing:.03em}
input,select{width:100%;border:1px solid var(--line);border-radius:10px;background:#061923;color:#e4f3f7;padding:12px;font:inherit;font-size:11px;outline:none;margin-bottom:8px}
input:focus,select:focus{border-color:rgba(84,205,232,.6);box-shadow:0 0 0 2px rgba(84,205,232,.08)}
.help{color:#718f99;font-size:9px;line-height:1.6;margin:0 0 16px}.help strong{color:#9eddec}
.actions{display:flex;justify-content:flex-end;gap:8px;margin-top:22px}.save{border:0;border-radius:11px;padding:12px 16px;background:linear-gradient(135deg,#25abc9,#1687a4);color:#fff;font-weight:800;font-size:10px;cursor:pointer}
.errors{padding:11px;border-radius:10px;margin-bottom:14px;background:rgba(210,65,65,.12);color:#ffb0b0;font-size:10px}
@media(max-width:650px){.hero.profile-page-header{align-items:flex-start;flex-direction:column;gap:14px}.hero-actions{width:100%;justify-content:flex-start}.hero-actions .back{width:100%}.card.form{max-width:none;padding:16px}.actions{flex-direction:column}.actions>*{width:100%;text-align:center}}
</style>
@endpush
