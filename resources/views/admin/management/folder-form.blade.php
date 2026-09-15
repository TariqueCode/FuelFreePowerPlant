@extends('layouts.portal')
@section('title',$folder->exists?'Edit Profile Folder':'New Profile Folder')
@section('content')
<section class="hero profile-page-header">
    <div class="profile-header-copy">
        <span class="eyebrow">GLOBAL · PROFILE BUILDER</span>
        <h1>{{ $folder->exists?'Edit folder':'Create folder' }}</h1>
        <p>The folder name becomes the public leadership section title and its URL slug.</p>
    </div>
    <a class="back profile-header-back" href="{{ route('admin.profile-builder.index') }}"><i class="fa-solid fa-arrow-left"></i> Back to Profile Builder</a>
</section>
@if($errors->any())<div class="errors">{{ $errors->first() }}</div>@endif
<section class="folder-form-shell">
    <div class="folder-form-heading">
        <div>
            <span class="eyebrow">FOLDER DETAILS</span>
            <h2>{{ $folder->exists?'Update folder information':'Set up a new profile folder' }}</h2>
        </div>
        <span class="form-state"><i class="fa-solid fa-folder"></i> {{ $folder->exists?'Editing':'New folder' }}</span>
    </div>
    <form method="POST" action="{{ $folder->exists?route('admin.profile-builder.folders.update',$folder):route('admin.profile-builder.folders.store') }}">
        @csrf @if($folder->exists)@method('PATCH')@endif
        <div class="field">
            <label for="folder-name">Folder name</label>
            <input id="folder-name" name="name" required maxlength="120" value="{{ old('name',$folder->name) }}" placeholder="Board of Directors">
            <div class="help">Example: <strong>Board of Directors</strong> <span>→</span> <strong>/board-of-directors</strong></div>
        </div>
        <div class="field">
            <label for="folder-status">Status</label>
            <select id="folder-status" name="status">
                <option value="published" @selected(old('status',$folder->status)==='published')>Published</option>
                <option value="draft" @selected(old('status',$folder->status)==='draft')>Draft</option>
            </select>
            <div class="status-help">Controls whether this folder is visible on the public leadership section.</div>
        </div>
        <div class="actions">
            <a class="back cancel-action" href="{{ route('admin.profile-builder.index') }}">Cancel</a>
            <button class="save" type="submit"><i class="fa-solid fa-floppy-disk"></i> {{ $folder->exists?'Save folder':'Create folder' }}</button>
        </div>
    </form>
</section>
@endsection
@push('styles')
<style>
.profile-page-header{position:relative!important;display:flex!important;align-items:center!important;justify-content:space-between!important;gap:24px!important;margin:0 0 26px!important;padding:4px 0 20px!important;background:transparent!important;border:0!important;border-radius:0!important;box-shadow:none!important;overflow:visible!important}
.profile-page-header:after{content:"";position:absolute;left:0;bottom:0;width:58px;height:3px;border-radius:3px;background:#35d8b0}
.profile-header-copy{background:transparent!important;border:0!important;box-shadow:none!important}
.profile-page-header .eyebrow{color:#54cde8!important}
.profile-page-header h1{margin:6px 0!important;font-size:clamp(30px,4vw,44px)!important;line-height:1.08!important}
.profile-page-header p{margin:0!important;color:#7898a5!important;font-size:11px!important;max-width:720px!important;line-height:1.65!important}
.profile-header-back{white-space:nowrap!important;flex:0 0 auto!important}
.folder-form-shell{max-width:920px;border:1px solid var(--line);border-radius:18px;background:linear-gradient(180deg,rgba(10,34,43,.78),rgba(5,22,29,.92));box-shadow:0 14px 36px rgba(0,0,0,.16);overflow:hidden}
.folder-form-heading{display:flex;align-items:center;justify-content:space-between;gap:20px;padding:20px 22px 18px;border-bottom:1px solid var(--line);background:rgba(84,205,232,.025)}
.folder-form-heading h2{margin:6px 0 0;color:#e7f6f9;font-size:17px;line-height:1.3}
.form-state{display:inline-flex;align-items:center;gap:7px;padding:7px 10px;border:1px solid rgba(53,216,176,.18);border-radius:999px;background:rgba(53,216,176,.06);color:#7fdac4;font-size:9px;font-weight:700;white-space:nowrap}
.folder-form-shell form{padding:22px}
.field{max-width:760px;margin-bottom:18px}
.field label{display:block;color:#9ab5bd;font-size:10px;font-weight:700;margin:0 0 7px}
.field input,.field select{width:100%;box-sizing:border-box;border:1px solid var(--line);border-radius:11px;background:#061923;color:#e4f3f7;padding:12px 13px;font:inherit;font-size:11px;outline:none;margin:0;min-height:42px}
.field input:focus,.field select:focus{border-color:rgba(84,205,232,.58);box-shadow:0 0 0 3px rgba(84,205,232,.07)}
.help,.status-help{color:#718f99;font-size:9px;line-height:1.6;margin-top:7px}
.help strong{color:#9eddec}.help span{color:#35d8b0;padding:0 3px}
.status-help{margin-top:6px}
.actions{display:flex;justify-content:flex-end;align-items:center;gap:9px;margin-top:24px;padding-top:18px;border-top:1px solid var(--line)}
.cancel-action{min-width:72px}.save{border:0;border-radius:11px;padding:12px 17px;background:linear-gradient(135deg,#25abc9,#1687a4);color:#fff;font-weight:800;font-size:10px;cursor:pointer}
.errors{padding:11px 13px;border-radius:10px;margin:0 0 16px;background:rgba(210,65,65,.12);border:1px solid rgba(210,65,65,.2);color:#ffb0b0;font-size:10px}
@media(max-width:650px){.profile-page-header{align-items:flex-start!important;flex-direction:column!important;gap:14px!important}.profile-header-back{width:max-content}.folder-form-heading{align-items:flex-start;flex-direction:column;padding:17px}.folder-form-shell form{padding:17px}.actions{justify-content:stretch}.actions>*{flex:1;text-align:center}}
</style>
@endpush