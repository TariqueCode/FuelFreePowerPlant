@extends('layouts.portal')
@section('title',$folder->exists?'Edit Profile Folder':'New Profile Folder')
@section('content')
<section class="hero profile-page-header">
    <div class="profile-header-copy">
        <span class="eyebrow">GLOBAL · PROFILE BUILDER</span>
        <h1>{{ $folder->exists?'Edit folder':'Create folder' }}</h1>
        <p>The folder name becomes the public leadership section title and its URL slug.</p>
    </div>
    <a class="header-back" href="{{ route('admin.profile-builder.index') }}"><i class="fa-solid fa-arrow-left"></i><span>Back to Profile Builder</span></a>
</section>
@if($errors->any())<div class="errors" role="alert">{{ $errors->first() }}</div>@endif
<div class="profile-form-shell">
    <div class="form-heading">
        <div><span class="eyebrow">FOLDER DETAILS</span><h2>{{ $folder->exists?'Update profile folder':'Set up a new profile folder' }}</h2><p>Use a clear name for the leadership section. The URL slug is generated from the folder name.</p></div>
        <span class="form-badge"><i class="fa-solid fa-folder"></i>{{ $folder->exists?'Existing folder':'New folder' }}</span>
    </div>
    <form method="POST" action="{{ $folder->exists?route('admin.profile-builder.folders.update',$folder):route('admin.profile-builder.folders.store') }}">
        @csrf @if($folder->exists)@method('PATCH')@endif
        <div class="field">
            <label for="folder-name">Folder name <span>*</span></label>
            <input id="folder-name" name="name" required maxlength="120" value="{{ old('name',$folder->name) }}" placeholder="Board of Directors">
            <div class="help">Example: <strong>Board of Directors</strong> <i class="fa-solid fa-arrow-right"></i> <strong>/board-of-directors</strong></div>
        </div>
        <div class="field">
            <label for="folder-status">Status</label>
            <select id="folder-status" name="status"><option value="published" @selected(old('status',$folder->status)==='published')>Published</option><option value="draft" @selected(old('status',$folder->status)==='draft')>Draft</option></select>
            <div class="help">Controls whether this folder is visible on the public leadership section.</div>
        </div>
        <div class="form-actions">
            <a class="cancel" href="{{ route('admin.profile-builder.index') }}">Cancel</a>
            <button class="save" type="submit"><i class="fa-solid fa-floppy-disk"></i>{{ $folder->exists?'Save folder':'Create folder' }}</button>
        </div>
    </form>
</div>
@endsection
@push('styles')
<style>
.profile-page-header{position:relative!important;display:flex!important;align-items:flex-end!important;justify-content:space-between!important;gap:24px!important;margin:0 0 30px!important;padding:0 0 22px!important;background:transparent!important;border:0!important;border-radius:0!important;box-shadow:none!important;overflow:visible!important}
.profile-page-header:after{content:"";position:absolute;left:0;bottom:0;width:64px;height:3px;border-radius:3px;background:#35d8b0}
.profile-header-copy{background:transparent!important;border:0!important;box-shadow:none!important}
.profile-page-header .eyebrow{display:block;color:#54cde8!important;font-size:9px;font-weight:800;letter-spacing:.17em}
.profile-page-header h1{margin:7px 0 5px!important;font-size:clamp(30px,4vw,46px)!important;line-height:1.05!important}
.profile-page-header p{margin:0!important;max-width:760px!important;color:#7898a5!important;font-size:12px!important;line-height:1.6!important}
.header-back{display:inline-flex;align-items:center;gap:8px;flex:0 0 auto;padding:10px 14px;border:1px solid var(--line);border-radius:10px;background:rgba(255,255,255,.015);color:#9db9c2!important;text-decoration:none!important;font-size:10px;font-weight:700;white-space:nowrap}
.header-back:hover,.header-back:visited{color:#b9d9e1!important;text-decoration:none!important}
.profile-form-shell{max-width:920px;border:1px solid var(--line);border-radius:18px;background:linear-gradient(180deg,rgba(255,255,255,.025),rgba(255,255,255,.012));overflow:hidden}
.form-heading{display:flex;align-items:center;justify-content:space-between;gap:24px;padding:20px 22px;border-bottom:1px solid var(--line);background:rgba(255,255,255,.012)}
.form-heading .eyebrow{color:#54cde8;font-size:9px;font-weight:800;letter-spacing:.16em}.form-heading h2{margin:6px 0 4px;color:#e8f6f8;font-size:17px;line-height:1.2}.form-heading p{margin:0;color:#718f99;font-size:10px;line-height:1.55;max-width:680px}.form-badge{display:inline-flex;align-items:center;gap:7px;flex:0 0 auto;padding:7px 10px;border:1px solid rgba(84,205,232,.18);border-radius:999px;color:#8db7c2;background:rgba(84,205,232,.045);font-size:9px;font-weight:700}.form-badge i{color:#35d8b0}
.profile-form-shell form{padding:24px 22px}.field+.field{margin-top:20px}.field label{display:block;margin:0 0 7px;color:#9ab5bd;font-size:10px;font-weight:700}.field label span{color:#35d8b0}.field input,.field select{width:100%;box-sizing:border-box;margin:0;border:1px solid var(--line);border-radius:11px;background:#061923;color:#e4f3f7;padding:12px 13px;font:inherit;font-size:11px;outline:none}.field input::placeholder{color:#58727c}.field input:focus,.field select:focus{border-color:rgba(84,205,232,.55);box-shadow:0 0 0 3px rgba(84,205,232,.06)}.help{display:flex;align-items:center;gap:6px;margin-top:7px;color:#667f88;font-size:9px;line-height:1.55}.help strong{color:#9eddec}.help i{color:#54cde8;font-size:8px}
.form-actions{display:flex;justify-content:flex-end;align-items:center;gap:10px;margin-top:26px;padding-top:18px;border-top:1px solid var(--line)}.cancel,.save{display:inline-flex;align-items:center;justify-content:center;gap:7px;min-height:40px;padding:0 15px;border-radius:10px;font-size:10px;font-weight:800;text-decoration:none!important;box-sizing:border-box}.cancel{border:1px solid var(--line);background:transparent;color:#8faab3!important}.cancel:hover,.cancel:visited{color:#b9d9e1!important}.save{border:1px solid rgba(84,205,232,.25);background:linear-gradient(135deg,#25abc9,#1687a4);color:#fff;border:0;cursor:pointer}.errors{margin:0 0 18px;padding:11px 13px;border:1px solid rgba(210,65,65,.25);border-radius:10px;background:rgba(210,65,65,.10);color:#ffb0b0;font-size:10px}
@media(max-width:650px){.profile-page-header{align-items:flex-start!important;flex-direction:column!important;gap:15px!important;margin-bottom:24px!important}.header-back{width:auto}.profile-form-shell{border-radius:15px}.form-heading{align-items:flex-start;flex-direction:column;padding:17px}.profile-form-shell form{padding:18px 17px}.form-actions{justify-content:stretch}.form-actions>*{flex:1}.help{align-items:flex-start}}
</style>
@endpush
