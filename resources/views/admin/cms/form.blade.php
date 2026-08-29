@extends('layouts.portal')
@section('title', $mode === 'create' ? 'New CMS Page' : 'Edit CMS Page')
@section('content')
<section class="hero"><div class="eyebrow">CONTENT MANAGEMENT</div><h1>{{ $mode === 'create' ? 'New page' : 'Edit page' }}</h1><p>Publish clean, responsive content from the same secure control center.</p></section>
@if($errors->any())<div class="errors">{{ $errors->first() }}</div>@endif
<div class="form-card"><form id="cms-page-form" method="POST" action="{{ $mode === 'create' ? route('admin.cms.store') : route('admin.cms.update',$page) }}">@csrf @if($mode==='edit') @method('PATCH') @endif
<div class="fields"><div><label>Title</label><input name="title" value="{{ old('title',$page->title) }}" required maxlength="180"></div><div><label>Slug</label><input name="slug" value="{{ old('slug',$page->slug) }}" placeholder="about-us" maxlength="180"></div><div class="full"><label>Excerpt</label><textarea name="excerpt" rows="3" maxlength="1000">{{ old('excerpt',$page->excerpt) }}</textarea></div><div class="full"><label for="cms-content-editor">Content</label>
@php
    $editorId = 'cms-content-editor';
    $formId = 'cms-page-form';
    $bodyName = 'content';
    $initialBody = old('content', $page->content);
    $allowAttachments = false;
@endphp
@include('partials.mail-editor', compact('editorId','formId','bodyName','initialBody','allowAttachments'))
<small class="cms-editor-note">Professional rich-text editor with Word-style formatting, selection-aware headings/fonts/sizes/colors, responsive toolbar, HTML/source view, fullscreen, links, images, tables, lists, undo/redo and clear formatting.</small></div><div class="full check"><input id="published" type="checkbox" name="is_published" value="1" @checked(old('is_published',$page->is_published))><label for="published">Publish this page</label></div></div>
<div class="actions"><a href="{{ route('admin.cms.index') }}">Cancel</a><button type="submit">{{ $mode === 'create' ? 'Create page' : 'Save changes' }}</button></div></form></div>
@endsection
@push('styles')<style>.form-card{max-width:920px;background:rgba(255,255,255,.025);border:1px solid var(--line);border-radius:18px;padding:22px}.fields{display:grid;grid-template-columns:1fr 1fr;gap:16px}.full{grid-column:1/-1}label{display:block;font-size:12px;color:#9eb9c4;margin:0 0 7px}input,textarea{width:100%;box-sizing:border-box;padding:13px;border-radius:11px;border:1px solid var(--line);background:#071c29;color:#e9f7fb;outline:none;font:inherit}textarea{resize:vertical;line-height:1.6}.full small{display:block;color:#718f9d;margin-top:7px;font-size:10px}.cms-editor-note{line-height:1.55}.full:has(.ff-mail-editor){min-width:0}.ff-mail-editor{width:100%;max-width:100%}.check{display:flex;align-items:center;gap:9px}.check input{width:auto}.check label{margin:0}.errors{margin-bottom:16px;padding:11px;border-radius:10px;background:rgba(210,65,65,.12);color:#ffb0b0}.actions{display:flex;justify-content:flex-end;gap:12px;margin-top:22px;align-items:center}.actions a{color:#8ca9b6;text-decoration:none;font-size:13px}.actions button{border:0;border-radius:11px;padding:12px 17px;background:#31afd2;color:#fff;font-weight:700}@media(max-width:650px){.fields{grid-template-columns:1fr}.full{grid-column:auto}}</style>@endpush
