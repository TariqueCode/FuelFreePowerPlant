@extends('layouts.portal')
@section('title',$item->exists?'Edit '.$labels[$item->type]:'New '.$labels[$lockedType ?? 'company'])
@section('content')
@php($contentType=$lockedType ?? $item->type)
<section class="hero"><div><span class="eyebrow">{{ strtoupper($labels[$contentType] ?? 'CONTENT') }} CMS</span><h1>{{ $item->exists?'Edit content':'Create content' }}</h1>@if($contentType==='news')<p>Create a polished news or notice with a direct cover-photo upload and visual content editor.</p>@endif</div><a class="back" href="{{ route('admin.site-content.index',['type'=>in_array($item->type,['news','announcement'],true)?'news':($item->type ?: $lockedType)]) }}"><i class="fa-solid fa-arrow-left"></i> Back</a></section>
@if($errors->any())<div class="errors">{{ $errors->first() }}</div>@endif
<div class="card"><form id="content-form" method="POST" action="{{ $item->exists?route('admin.site-content.update',$item):route('admin.site-content.store') }}">@csrf @if($item->exists)@method('PATCH')@endif
<div class="grid">
<div>@if($lockedType)
<label>Section</label>
<div class="locked-type"><i class="fa-solid {{ $contentType==='company'?'fa-building':($contentType==='gallery'?'fa-images':'fa-newspaper') }}"></i>{{ $labels[$contentType] }}</div>
<input type="hidden" name="type" value="{{ $item->exists?$item->type:$lockedType }}">
@if($contentType==='news')
<div class="publication-type"><button type="button" class="{{ old('publication_type',$item->type)==='news'?'selected':'' }}" data-publication-type="news"><i class="fa-regular fa-newspaper"></i> News</button><button type="button" class="{{ old('publication_type',$item->type)==='announcement'?'selected':'' }}" data-publication-type="announcement"><i class="fa-solid fa-bullhorn"></i> Notice</button></div>
<input type="hidden" id="publication-type" name="publication_type" value="{{ old('publication_type',$item->type ?: 'news') }}">
<small class="hint">Choose whether this publication is a News item or an Official Notice.</small>
@endif
@else
<label>Content type</label><select name="type" required>@foreach($types as $type)<option value="{{ $type }}" @selected(old('type',$item->type)===$type)>{{ $labels[$type] }}</option>@endforeach</select>
@endif</div>
<div><label>Status</label><select name="status" required><option value="draft" @selected(old('status',$item->status ?: 'draft')==='draft')>Draft</option><option value="published" @selected(old('status',$item->status)==='published')>Published</option></select></div>
@if($contentType==='company')<div class="nav-visibility"><label class="check-label"><input type="checkbox" name="show_in_navigation" value="1" @checked(old('show_in_navigation',$item->show_in_navigation))> <span>Show this page in navigation</span></label><small>Enable this to add the page to the public website navigation. Its position can be arranged from the Company CMS list.</small></div>@endif
@if($contentType==='news')
<div class="news-cover-field full"><div class="field-head"><div><label>Cover photo</label><small>Use a clear 16:9 image. It will be used in the CMS list, news page and sharing preview.</small></div><button type="button" class="upload-cover" id="cover-upload"><i class="fa-solid fa-cloud-arrow-up"></i> {{ $item->image_path?'Replace cover':'Upload cover photo' }}</button></div><div class="cover-preview {{ $item->image_path?'has-image':'' }}" id="cover-preview">@if($item->image_path)<img src="{{ asset('storage/'.$item->image_path) }}" alt="{{ $item->cover_alt ?: $item->title }}">@else<div><i class="fa-regular fa-image"></i><span>No cover photo selected</span></div>@endif</div><div class="cover-actions"><button type="button" id="remove-cover" class="remove-cover" @disabled(!$item->image_path)><i class="fa-solid fa-trash-can"></i> Remove</button><span id="cover-status"></span></div><input type="file" id="cover-input" hidden accept="image/jpeg,image/png,image/webp"></div>
@endif
<div class="full"><label>{{ $contentType==='gallery' ? 'Event / gallery title' : 'Title' }}</label><input name="title" value="{{ old('title',$item->title) }}" required maxlength="255"></div>
<div class="full"><label>Slug</label><input name="slug" value="{{ old('slug',$item->slug) }}" maxlength="255"><small class="hint">Leave blank to generate automatically from the title.</small></div>
<div class="full"><label>Short description / excerpt</label><textarea name="excerpt" rows="3" maxlength="1000">{{ old('excerpt',$item->excerpt) }}</textarea></div>
@if($contentType==='news')
<div><label>Publication date &amp; time</label><input type="datetime-local" name="published_at" value="{{ old('published_at',$item->published_at?->format('Y-m-d\TH:i')) }}"></div>
<div><label>Cover photo alt text</label><input name="cover_alt" value="{{ old('cover_alt',$item->cover_alt) }}" maxlength="255" placeholder="Describe the cover image"></div>
<div class="featured-box"><label class="check-label"><input type="checkbox" name="is_featured" value="1" @checked(old('is_featured',$item->is_featured))> <span>Feature this publication</span></label><small>Featured publications can be highlighted on public news areas.</small></div>
<div class="seo-box full"><div class="seo-title"><i class="fa-solid fa-magnifying-glass-chart"></i><strong>SEO settings</strong></div><div class="seo-grid"><div><label>Meta title</label><input name="meta_title" value="{{ old('meta_title',$item->meta_title) }}" maxlength="255" placeholder="Optional SEO title"></div><div><label>Meta description</label><textarea name="meta_description" rows="2" maxlength="1000" placeholder="Optional search description">{{ old('meta_description',$item->meta_description) }}</textarea></div></div></div><div class="attachment-box full">
<div class="attachment-head"><div><label>PDF attachment</label><small>Attach the official notice PDF. Large files are uploaded in small chunks, so normal PHP upload limits do not block the transfer. Maximum: 2 GB.</small></div>
@if($item->exists)<button type="button" class="attachment-upload" id="attachment-upload"><i class="fa-solid fa-file-arrow-up"></i> {{ $item->attachment_path ? 'Replace PDF' : 'Upload PDF' }}</button>@else<div class="attachment-save-note"><i class="fa-solid fa-circle-info"></i> Save this publication first to attach a PDF.</div>@endif</div>
@if($item->exists)<div class="attachment-current {{ $item->attachment_path ? 'has-file' : '' }}" id="attachment-current">@if($item->attachment_path)<i class="fa-solid fa-file-pdf"></i><div><strong>{{ $item->attachment_name }}</strong><span>{{ $item->attachment_size ? number_format($item->attachment_size / 1048576, 1).' MB' : 'PDF document' }}</span></div><button type="button" id="attachment-remove" title="Remove PDF" aria-label="Remove PDF"><i class="fa-solid fa-trash-can"></i></button>@else<div class="attachment-empty"><i class="fa-regular fa-file-pdf"></i><span>No PDF attached</span></div>@endif</div><div class="attachment-progress" id="attachment-progress"><div class="attachment-progress-bar" id="attachment-progress-bar"></div></div><div class="attachment-status" id="attachment-status"></div><input id="attachment-input" type="file" hidden accept="application/pdf,.pdf">@endif
</div>
@endif
@if($contentType==='gallery')<div class="full gallery-upload-box"><div class="gallery-upload-copy"><div><strong><i class="fa-solid fa-photo-film"></i> Gallery media</strong><p>Select multiple photos and videos together. They will be inserted into this event automatically.</p></div><button type="button" class="gallery-upload-btn" id="gallery-batch-upload"><i class="fa-solid fa-cloud-arrow-up"></i> Select photos &amp; videos</button></div><div id="gallery-upload-status" class="gallery-upload-status"></div></div>@endif
<div class="full"><label>Content</label><div class="editor-shell"><div class="editor-toolbar">
<button type="button" data-cmd="undo" title="Undo"><i class="fa-solid fa-rotate-left"></i></button>
<button type="button" data-cmd="redo" title="Redo"><i class="fa-solid fa-rotate-right"></i></button>
<span class="tool-sep"></span>
<button type="button" data-cmd="bold" title="Bold"><i class="fa-solid fa-bold"></i></button>
<button type="button" data-cmd="italic" title="Italic"><i class="fa-solid fa-italic"></i></button>
<button type="button" data-cmd="underline" title="Underline"><i class="fa-solid fa-underline"></i></button>
<button type="button" data-cmd="strikeThrough" title="Strike"><i class="fa-solid fa-strikethrough"></i></button>
<button type="button" data-cmd="removeFormat" title="Clear formatting"><i class="fa-solid fa-eraser"></i></button>

<div class="tool-dropdown">
<button type="button" class="tool-dropdown-toggle" aria-expanded="false" title="Text formatting"><i class="fa-solid fa-font"></i><span class="tool-caret"></span></button>
<div class="tool-menu">
<div class="tool-menu-label">Text</div>
<select id="block-format" class="tool-select format-select" title="Text style"><option value="p">Paragraph</option><option value="h1">Heading 1</option><option value="h2">Heading 2</option><option value="h3">Heading 3</option><option value="h4">Heading 4</option><option value="h5">Heading 5</option><option value="h6">Heading 6</option><option value="blockquote">Quote</option></select>
<select id="font-size" class="tool-select size-select" title="Font size"><option value="">Size</option><option value="2">Small</option><option value="3">Normal</option><option value="4">Large</option><option value="5">XL</option><option value="6">XXL</option></select>
<div class="tool-menu-row"><input type="color" id="text-color" value="#dff6fb" title="Text color"><span>Text color</span></div>
<div class="tool-menu-row"><input type="color" id="highlight-color" value="#17323b" title="Highlight color"><span>Highlight</span></div>
</div></div>

<div class="tool-dropdown">
<button type="button" class="tool-dropdown-toggle" aria-expanded="false" title="Lists"><i class="fa-solid fa-list"></i><span class="tool-caret"></span></button>
<div class="tool-menu tool-menu-wide">
<div class="tool-menu-label">Lists &amp; Indent</div>
<button type="button" data-cmd="insertUnorderedList"><i class="fa-solid fa-list-ul"></i> Bullet list</button>
<button type="button" data-cmd="insertOrderedList"><i class="fa-solid fa-list-ol"></i> Numbered list</button>
<button type="button" data-cmd="outdent"><i class="fa-solid fa-outdent"></i> Decrease indent</button>
<button type="button" data-cmd="indent"><i class="fa-solid fa-indent"></i> Increase indent</button>
</div></div>

<div class="tool-dropdown">
<button type="button" class="tool-dropdown-toggle" aria-expanded="false" title="Alignment"><i class="fa-solid fa-align-left"></i><span class="tool-caret"></span></button>
<div class="tool-menu tool-menu-wide">
<div class="tool-menu-label">Alignment</div>
<button type="button" data-cmd="justifyLeft"><i class="fa-solid fa-align-left"></i> Align left</button>
<button type="button" data-cmd="justifyCenter"><i class="fa-solid fa-align-center"></i> Center</button>
<button type="button" data-cmd="justifyRight"><i class="fa-solid fa-align-right"></i> Align right</button>
<button type="button" data-cmd="justifyFull"><i class="fa-solid fa-align-justify"></i> Justify</button>
</div></div>

<div class="tool-dropdown">
<button type="button" class="tool-dropdown-toggle" aria-expanded="false" title="Insert content"><i class="fa-solid fa-plus"></i><span class="tool-caret"></span></button>
<div class="tool-menu tool-menu-insert">
<div class="tool-menu-label">Insert</div>
<button type="button" id="insert-link"><i class="fa-solid fa-link"></i> Link</button>
<button type="button" id="insert-button"><i class="fa-solid fa-square-up-right"></i> CTA button</button>
<button type="button" id="insert-columns"><i class="fa-solid fa-table-columns"></i> Columns</button>
<button type="button" id="insert-table"><i class="fa-solid fa-table"></i> Table</button>
<button type="button" data-cmd="insertHorizontalRule"><i class="fa-solid fa-minus"></i> Horizontal line</button>
</div></div>

<div class="tool-dropdown">
<button type="button" class="tool-dropdown-toggle" aria-expanded="false" title="Media"><i class="fa-solid fa-photo-film"></i><span class="tool-caret"></span></button>
<div class="tool-menu tool-menu-wide">
<div class="tool-menu-label">Media</div>
<button type="button" id="upload-image"><i class="fa-solid fa-image"></i> Upload image</button>
<button type="button" id="upload-video"><i class="fa-solid fa-video"></i> Upload video</button>
<button type="button" id="insert-gallery"><i class="fa-solid fa-images"></i> Image gallery</button>
<button type="button" id="insert-youtube"><i class="fa-brands fa-youtube"></i> YouTube</button>
<button type="button" id="insert-facebook"><i class="fa-brands fa-facebook"></i> Facebook video</button>
</div></div>

<div class="tool-dropdown">
<button type="button" class="tool-dropdown-toggle" aria-expanded="false" title="Image alignment"><i class="fa-solid fa-image"></i><span class="tool-caret"></span></button>
<div class="tool-menu tool-menu-wide">
<div class="tool-menu-label">Selected image</div>
<button type="button" id="image-align-left"><i class="fa-solid fa-align-left"></i> Align left</button>
<button type="button" id="image-align-center"><i class="fa-solid fa-align-center"></i> Center image</button>
<button type="button" id="image-align-right"><i class="fa-solid fa-align-right"></i> Align right</button>
</div></div>

<span class="tool-sep"></span>
<button type="button" id="toggle-source" title="HTML source"><i class="fa-solid fa-code"></i></button>
<button type="button" id="preview-content" title="Preview"><i class="fa-solid fa-eye"></i></button>
<button type="button" id="toggle-fullscreen" title="Fullscreen"><i class="fa-solid fa-expand"></i></button>
</div></div><div id="editor" class="editor" contenteditable="true">{!! old('content',$item->content) !!}</div></div><textarea id="content-source" name="content" hidden></textarea><input id="media-input" type="file" hidden accept="image/jpeg,image/png,image/webp,image/gif"><input id="gallery-input" type="file" hidden multiple accept="image/jpeg,image/png,image/webp,image/gif"><input id="video-input" type="file" hidden accept="video/mp4,video/webm">@if($contentType==='gallery')<input id="gallery-batch-input" type="file" hidden multiple accept="image/jpeg,image/png,image/webp,image/gif,video/mp4,video/webm">@endif</div>
<div><label>{{ $contentType==='gallery' ? 'Event date &amp; time' : 'Publish date/time' }}</label><input type="datetime-local" name="published_at" value="{{ old('published_at',$item->published_at?->format('Y-m-d\\TH:i')) }}"></div>
</div><div class="actions"><a class="back" href="{{ route('admin.site-content.index',['type'=>in_array($item->type,['news','announcement'],true)?'news':$item->type]) }}">Cancel</a><button class="save" type="submit"><i class="fa-solid fa-floppy-disk"></i> {{ $item->exists?'Save changes':'Create content' }}</button></div></form></div>
@endsection
@push('styles')<style>
.hero{display:flex;justify-content:space-between;align-items:flex-end;gap:18px;margin-bottom:18px}.hero h1{font-size:clamp(26px,4vw,40px);margin:6px 0}.hero p{margin:0;color:#7898a5;font-size:11px;line-height:1.6}.eyebrow{font-size:9px;letter-spacing:.14em;color:#4ec5e5}.back{display:inline-flex;align-items:center;gap:7px;padding:10px 13px;border:1px solid var(--line);border-radius:11px;color:#9db9c2;text-decoration:none;font-size:10px}.card{max-width:1100px;padding:20px;border:1px solid var(--line);border-radius:18px;background:rgba(255,255,255,.02)}.grid{display:grid;grid-template-columns:1fr 1fr;gap:15px}.full{grid-column:1/-1}label{display:block;color:#89a7b2;font-size:10px;margin-bottom:7px}input,select,textarea{width:100%;box-sizing:border-box;border:1px solid var(--line);border-radius:10px;background:#061923;color:#e4f3f7;padding:11px;font:inherit;font-size:11px;outline:none}textarea{resize:vertical;line-height:1.6}.hint{display:block;margin-top:6px;color:#678692;font-size:9px}.locked-type{height:40px;display:flex;align-items:center;gap:9px;padding:0 12px;border:1px solid var(--line);border-radius:10px;background:rgba(67,194,229,.05);color:#b9e6ef;font-size:11px}.locked-type i{color:#61d5ed}.publication-type{display:grid;grid-template-columns:1fr 1fr;gap:6px;margin-top:8px}.publication-type button{height:40px;border:1px solid var(--line);border-radius:10px;background:#061923;color:#7e9ca5;font-size:10px;font-weight:800;cursor:pointer}.publication-type button i{margin-right:6px}.publication-type button.selected{color:#e7fbff;border-color:rgba(72,216,241,.35);background:rgba(72,216,241,.09);box-shadow:inset 0 0 0 1px rgba(72,216,241,.06)}.attachment-box{padding:16px;border:1px solid rgba(67,194,229,.18);border-radius:15px;background:linear-gradient(145deg,rgba(67,194,229,.06),rgba(255,255,255,.02))}.attachment-head{display:flex;align-items:center;justify-content:space-between;gap:15px}.attachment-head label{margin-bottom:3px}.attachment-head small{color:#6f8e98;font-size:9px;line-height:1.5}.attachment-upload{border:1px solid rgba(82,216,240,.3);border-radius:10px;background:rgba(55,197,230,.12);color:#dffaff;padding:10px 13px;cursor:pointer;font-size:10px;font-weight:800;white-space:nowrap}.attachment-save-note{display:flex;align-items:center;gap:7px;color:#6f8e98;font-size:9px;padding:9px 11px;border:1px solid var(--line);border-radius:10px}.attachment-save-note i{color:#5fcfe8}.attachment-current{display:flex;align-items:center;gap:11px;margin-top:12px;min-height:58px;padding:10px 12px;border:1px solid var(--line);border-radius:11px;background:#061923}.attachment-current>i{font-size:25px;color:#ff8f9a}.attachment-current>div{min-width:0;flex:1;display:flex;flex-direction:column;gap:3px}.attachment-current strong{font-size:10px;color:#dff5f8;white-space:nowrap;overflow:hidden;text-overflow:ellipsis}.attachment-current span{font-size:8px;color:#6f8e98}.attachment-current button{width:32px;height:32px;border:1px solid rgba(255,99,113,.14);border-radius:9px;background:rgba(255,99,113,.05);color:#ff9eaa;cursor:pointer}.attachment-empty{display:flex;align-items:center;gap:9px;color:#668792;font-size:9px}.attachment-empty i{font-size:20px;color:#4fc8e4}.attachment-progress{display:none;height:6px;margin-top:10px;border-radius:999px;background:#0a2530;overflow:hidden}.attachment-progress.show{display:block}.attachment-progress-bar{height:100%;width:0;background:linear-gradient(90deg,#27b8d4,#65e3f1);transition:width .12s}.attachment-status{margin-top:7px;color:#70d9ea;font-size:9px;min-height:13px}.nav-visibility,.featured-box{padding:12px;border:1px solid rgba(67,194,229,.16);border-radius:11px;background:rgba(67,194,229,.045)}.check-label{display:flex;align-items:center;gap:8px;color:#bfe7ef;font-size:11px;margin:0;cursor:pointer}.check-label input{width:17px;height:17px;margin:0;accent-color:#29aaca}.nav-visibility small,.featured-box small{display:block;margin:6px 0 0 25px;color:#678692;font-size:9px;line-height:1.5}.news-cover-field{padding:16px;border:1px solid rgba(67,194,229,.18);border-radius:16px;background:linear-gradient(145deg,rgba(67,194,229,.07),rgba(255,255,255,.02))}.field-head{display:flex;align-items:center;justify-content:space-between;gap:15px}.field-head label{margin-bottom:3px}.field-head small{color:#6f8e98;font-size:9px}.upload-cover,.gallery-upload-btn{border:1px solid rgba(82,216,240,.3);border-radius:10px;background:rgba(55,197,230,.12);color:#dffaff;padding:10px 13px;cursor:pointer;font-size:10px;font-weight:800;white-space:nowrap}.cover-preview{margin-top:12px;width:100%;aspect-ratio:16/9;border-radius:13px;border:1px dashed rgba(100,190,210,.22);background:#061923;display:grid;place-items:center;overflow:hidden}.cover-preview img{width:100%;height:100%;object-fit:cover}.cover-preview div{text-align:center;color:#5f8793}.cover-preview i{font-size:30px;color:#4fc8e4;display:block;margin-bottom:7px}.cover-preview span{font-size:10px}.cover-actions{display:flex;align-items:center;justify-content:space-between;margin-top:8px;min-height:26px}.remove-cover{border:0;background:transparent;color:#8da8b0;font-size:9px;cursor:pointer;padding:5px}.remove-cover:not(:disabled):hover{color:#ff9da4}.remove-cover:disabled{opacity:.35;cursor:not-allowed}.cover-actions span{color:#6fd5e8;font-size:9px}.seo-box{padding:15px;border:1px solid rgba(67,194,229,.15);border-radius:14px;background:rgba(67,194,229,.03)}.seo-title{display:flex;align-items:center;gap:8px;color:#d7f4f7;font-size:11px;margin-bottom:12px}.seo-title i{color:#5ed8ee}.seo-grid{display:grid;grid-template-columns:1fr 1fr;gap:12px}.editor-shell{border:1px solid var(--line);border-radius:14px;overflow:visible;background:#061923}.editor-toolbar{display:flex;flex-wrap:wrap;align-items:center;gap:4px;padding:9px;border-bottom:1px solid var(--line);background:rgba(67,194,229,.04);position:relative;z-index:30}.editor-toolbar .tool-dropdown{position:relative;display:inline-flex}.editor-toolbar .tool-dropdown-toggle{display:inline-flex;align-items:center;justify-content:center;gap:5px;min-width:40px}.tool-caret{width:0;height:0;border-left:3px solid transparent;border-right:3px solid transparent;border-top:4px solid currentColor;margin-top:2px}.tool-dropdown.open>.tool-dropdown-toggle{background:rgba(67,194,229,.12);color:#eaf8fb}.tool-menu{display:none;position:absolute;top:calc(100% + 6px);left:0;z-index:20;min-width:190px;padding:6px;border:1px solid var(--line);border-radius:11px;background:#071b25;box-shadow:0 14px 35px rgba(0,0,0,.35)}.tool-dropdown.open>.tool-menu{display:block}.tool-menu-wide{min-width:205px}.tool-menu-insert{min-width:190px}.tool-menu-label{padding:5px 8px 7px;color:#5fcfe8;font-size:8px;letter-spacing:.12em;text-transform:uppercase}.tool-menu>button{width:100%;display:flex;align-items:center;justify-content:flex-start;gap:9px;height:32px;padding:0 9px;border:0;border-radius:7px;background:transparent;color:#a7c2cb;font-size:10px;text-align:left}.tool-menu>button:hover{background:rgba(67,194,229,.10);color:#eaf8fb}.tool-menu .tool-select{display:block;width:100%;height:32px;margin-bottom:5px}.tool-menu .tool-select:last-of-type{margin-bottom:7px}.tool-menu-row{display:flex;align-items:center;gap:9px;padding:4px 3px;color:#8eabb4;font-size:9px}.tool-menu-row input[type=color]{flex:0 0 34px}.editor-toolbar button{min-width:34px;height:32px;border:1px solid transparent;border-radius:8px;background:transparent;color:#a7c2cb;cursor:pointer}.editor-toolbar button:hover,.editor-toolbar button.active{background:rgba(67,194,229,.12);color:#eaf8fb}.editor-toolbar select{width:auto;height:32px;min-width:0;border:1px solid var(--line);border-radius:8px;background:#061923;color:#b9d7df;padding:0 7px;font-size:10px;flex:0 0 auto}.editor-toolbar .format-select{width:112px}.editor-toolbar .size-select{width:68px}.editor-toolbar .tool-select:focus{border-color:rgba(67,194,229,.38);outline:none}.editor-toolbar input[type=color]{width:34px;height:32px;padding:3px;border:1px solid var(--line);border-radius:8px;background:#061923;cursor:pointer}.tool-sep{width:1px;height:22px;background:var(--line);margin:0 3px}.editor{min-height:430px;padding:18px;color:#e6f4f7;line-height:1.75;font-size:13px;outline:none}.editor h1{font-size:30px}.editor h2{font-size:24px}.editor h3{font-size:19px}.editor h4{font-size:17px}.editor h5{font-size:15px}.editor h6{font-size:13px;text-transform:uppercase;letter-spacing:.04em}.editor blockquote{margin:14px 0;padding:10px 16px;border-left:3px solid #43c2e5;background:rgba(67,194,229,.06);color:#b9d7df}.editor table{width:100%;border-collapse:collapse;margin:14px 0}.editor td,.editor th{border:1px solid rgba(120,170,185,.35);padding:9px;text-align:left}.editor th{background:rgba(67,194,229,.08)}.editor.source-mode{font-family:ui-monospace,SFMono-Regular,Consolas,monospace;white-space:pre-wrap}.editor-shell.is-fullscreen{position:fixed;inset:0;z-index:9999;background:#04151e;border-radius:0;display:flex;flex-direction:column}.editor-shell.is-fullscreen .editor{flex:1;overflow:auto}.editor-shell.is-fullscreen .editor-toolbar{position:sticky;top:0;z-index:2}.editor img,.editor video{max-width:100%;height:auto;border-radius:12px;margin:10px 0}.editor img.align-left{display:block;margin-left:0;margin-right:auto}.editor img.align-center{display:block;margin-left:auto;margin-right:auto}.editor img.align-right{display:block;margin-left:auto;margin-right:0}.editor .content-columns{display:grid;gap:18px;margin:18px 0}.editor .content-columns.cols-2{grid-template-columns:repeat(2,minmax(0,1fr))}.editor .content-columns.cols-3{grid-template-columns:repeat(3,minmax(0,1fr))}.editor .content-column{min-width:0;padding:18px;border:1px solid rgba(67,194,229,.14);border-radius:12px;background:rgba(67,194,229,.025)}.editor .content-cta{display:inline-flex;align-items:center;justify-content:center;gap:7px;margin:10px 4px 10px 0;padding:11px 17px;border-radius:9px;text-decoration:none;font-weight:700;line-height:1.2;background:#29aaca;color:#fff}.editor .content-cta.cta-outline{background:transparent;color:#29aaca;border:1px solid #29aaca}.editor iframe{max-width:100%;width:100%;min-height:360px;border:0;border-radius:12px;margin:10px 0;background:#000}.media-gallery{display:grid;grid-template-columns:repeat(3,minmax(0,1fr));gap:10px;margin:14px 0}.media-gallery img{width:100%;aspect-ratio:16/10;object-fit:cover;border-radius:10px}.gallery-upload-box{padding:16px;border:1px solid rgba(67,209,240,.18);border-radius:15px;background:linear-gradient(135deg,rgba(67,209,240,.07),rgba(255,255,255,.02))}.gallery-upload-copy{display:flex;align-items:center;justify-content:space-between;gap:15px}.gallery-upload-copy strong{font-size:13px}.gallery-upload-copy strong i{color:#5eddf4;margin-right:6px}.gallery-upload-copy p{color:#7696a1;font-size:9px;margin:6px 0 0;line-height:1.6}.gallery-upload-status{color:#70d9ea;font-size:9px;margin-top:10px}.actions{display:flex;justify-content:flex-end;gap:9px;margin-top:20px}.save{border:0;border-radius:11px;padding:12px 16px;background:#29aaca;color:#fff;font-weight:700}.errors{padding:11px;border-radius:10px;margin-bottom:14px;background:rgba(210,65,65,.12);color:#ffb0b0}@media(max-width:650px){.hero{align-items:flex-start;gap:12px;flex-direction:column}.grid{grid-template-columns:1fr}.full{grid-column:auto}.seo-grid{grid-template-columns:1fr}.editor{min-height:360px}.editor-toolbar{position:sticky;top:0;z-index:2}.editor-toolbar .format-select{width:104px}.editor-toolbar .size-select{width:64px}.actions>*{flex:1;text-align:center}.media-gallery{grid-template-columns:repeat(2,minmax(0,1fr))}.gallery-upload-copy,.field-head{align-items:stretch;flex-direction:column}.gallery-upload-btn,.upload-cover,.attachment-upload{width:100%}.attachment-head{align-items:stretch;flex-direction:column}}

/* Word-like editor UX: sticky, responsive, selection-aware. Existing palette intentionally preserved. */
.editor-shell{width:100%;max-width:100%;min-width:0;box-sizing:border-box;overflow:visible;position:relative}
.editor-toolbar{position:sticky!important;top:var(--admin-editor-sticky-top,64px);z-index:80;display:flex;align-items:center;flex-wrap:nowrap!important;max-width:100%;min-width:0;overflow-x:auto;overflow-y:visible;scrollbar-width:thin;scroll-behavior:smooth;-webkit-overflow-scrolling:touch}
.editor-toolbar::-webkit-scrollbar{height:5px}.editor-toolbar::-webkit-scrollbar-thumb{background:rgba(67,194,229,.28);border-radius:99px}
.editor-toolbar>*{flex:0 0 auto}
.editor-status{display:inline-flex;align-items:center;gap:6px;flex:0 0 auto;min-height:30px;margin-left:3px;padding:0 9px;border:1px solid var(--line);border-radius:8px;background:rgba(67,194,229,.045);color:#8eabb4;font-size:9px;white-space:nowrap}
.editor-status strong{color:#dff6fb;font-size:9px}
.editor-status .status-sep{opacity:.35}
.editor{width:100%;max-width:100%;min-width:0;box-sizing:border-box;overflow-wrap:anywhere;word-break:break-word}
.editor>*{max-width:100%;box-sizing:border-box}
.editor table{max-width:100%}
.editor img,.editor video,.editor iframe{max-width:100%;height:auto;box-sizing:border-box}
.editor iframe{min-width:0}
.editor .content-columns{max-width:100%;min-width:0}
.editor .content-column{min-width:0;overflow-wrap:anywhere}
@media(max-width:900px){
  .editor-toolbar{top:58px;padding:7px 8px;gap:2px;overflow-x:auto;overflow-y:visible;white-space:nowrap}
  .editor-toolbar button{min-width:40px;height:38px}
  .editor-toolbar select{height:38px}
  .editor-toolbar .format-select{width:112px}.editor-toolbar .size-select{width:72px}
  .editor-status{height:38px;min-height:38px}
}
@media(max-width:650px){
  .editor-shell{width:100%;max-width:100%}
  .editor-toolbar{top:58px;border-radius:11px;scroll-snap-type:x proximity}
  .editor-toolbar .tool-dropdown{flex:0 0 auto}
  .editor-toolbar .tool-dropdown-toggle{min-width:40px}
  .editor-toolbar .tool-menu{position:fixed;left:8px;right:8px;top:104px;max-height:min(52vh,420px);overflow:auto;z-index:10000}
  .editor-toolbar .tool-menu>button{min-height:42px;height:auto}
  .editor{font-size:16px;line-height:1.75;padding:14px}
  .editor h1{font-size:28px}.editor h2{font-size:23px}.editor h3{font-size:19px}
  .editor table{display:block;width:100%;overflow-x:auto}
  .editor-status{max-width:none}
}
@media(max-width:420px){
  .editor-toolbar{padding-left:6px;padding-right:6px}
  .editor-toolbar button{min-width:40px}
  .editor-toolbar .format-select{width:104px}.editor-toolbar .size-select{width:66px}
  .editor-status{font-size:8px;padding:0 7px}
}
</style>@endpush
@push('head')<meta name="csrf-token" content="{{ csrf_token() }}">@endpush
@push('scripts')<script>
const editor=document.getElementById('editor'),source=document.getElementById('content-source'),form=document.getElementById('content-form'),mediaInput=document.getElementById('media-input'),galleryInput=document.getElementById('gallery-input'),videoInput=document.getElementById('video-input'),galleryBatchInput=document.getElementById('gallery-batch-input'),galleryStatus=document.getElementById('gallery-upload-status');
function sync(){if(source)source.value=editor.innerHTML}function exec(cmd,value=null){editor.focus();document.execCommand(cmd,false,value);sync()}document.querySelectorAll('.editor-toolbar [data-cmd]').forEach(b=>b.addEventListener('click',()=>{exec(b.dataset.cmd,b.dataset.value||null);b.closest('.tool-dropdown')?.classList.remove('open');b.closest('.tool-dropdown')?.querySelector('.tool-dropdown-toggle')?.setAttribute('aria-expanded','false')}));document.querySelectorAll('.tool-dropdown-toggle').forEach(btn=>btn.addEventListener('click',e=>{e.stopPropagation();const box=btn.closest('.tool-dropdown');document.querySelectorAll('.tool-dropdown.open').forEach(other=>{if(other!==box){other.classList.remove('open');other.querySelector('.tool-dropdown-toggle')?.setAttribute('aria-expanded','false')}});const open=box.classList.toggle('open');btn.setAttribute('aria-expanded',open?'true':'false')}));document.querySelectorAll('.tool-dropdown .tool-menu').forEach(menu=>menu.addEventListener('click',e=>e.stopPropagation()));document.addEventListener('click',()=>document.querySelectorAll('.tool-dropdown.open').forEach(box=>{box.classList.remove('open');box.querySelector('.tool-dropdown-toggle')?.setAttribute('aria-expanded','false')}));
document.getElementById('insert-link').onclick=()=>{const url=prompt('URL');if(url)exec('createLink',url)};document.getElementById('insert-button').onclick=()=>{const text=prompt('Button text','Learn More');if(!text)return;const url=prompt('Button URL','/');if(!url)return;const style=prompt('Button style: primary or outline','primary')==='outline'?'cta-outline':'';exec('insertHTML',`<a class="content-cta ${style}" href="${safeAttr(url)}">${safeText(text)}</a> <span>&nbsp;</span>`)};
document.getElementById('insert-columns').onclick=()=>{const count=Math.min(3,Math.max(2,parseInt(prompt('Number of columns (2 or 3)','2')||'2',10)));const cols=Array.from({length:count},(_,i)=>`<div class="content-column"><h3>Column ${i+1}</h3><p>Click here to edit this content.</p></div>`).join('');exec('insertHTML',`<div class="content-columns cols-${count}">${cols}</div><p></p>`)};
function safeText(v){return String(v).replace(/[&<>"]/g,ch=>({'&':'&amp;','<':'&lt;','>':'&gt;','"':'&quot;'}[ch]))}function safeAttr(v){return String(v).replace(/[<>"']/g,ch=>({'<':'%3C','>':'%3E','"':'%22',"'":'%27'}[ch]))}
function alignSelectedImage(cls){const sel=window.getSelection();let node=sel&&sel.anchorNode;while(node&&node!==editor&&node.nodeType===3)node=node.parentElement;while(node&&node!==editor&&node.tagName!=='IMG')node=node.parentElement;if(!node||node===editor||node.tagName!=='IMG'){alert('Select an image first.');return}node.classList.remove('align-left','align-center','align-right');node.classList.add(cls);sync()}
document.getElementById('image-align-left').onclick=()=>alignSelectedImage('align-left');document.getElementById('image-align-center').onclick=()=>alignSelectedImage('align-center');document.getElementById('image-align-right').onclick=()=>alignSelectedImage('align-right');
document.getElementById('block-format').onchange=e=>{if(e.target.value)exec('formatBlock',e.target.value)};document.getElementById('font-size').onchange=e=>{if(e.target.value)exec('fontSize',e.target.value);e.target.value=''};document.getElementById('text-color').oninput=e=>exec('foreColor',e.target.value);document.getElementById('highlight-color').oninput=e=>exec('hiliteColor',e.target.value);
document.getElementById('insert-table').onclick=()=>{const rows=Math.min(20,Math.max(2,parseInt(prompt('Number of rows','3')||'3',10)));const cols=Math.min(10,Math.max(1,parseInt(prompt('Number of columns','3')||'3',10)));let h='<table><thead><tr>'+Array.from({length:cols},(_,i)=>'<th>Header '+(i+1)+'</th>').join('')+'</tr></thead><tbody>'+Array.from({length:rows-1},()=>'<tr>'+Array.from({length:cols},()=>'<td>Cell</td>').join('')+'</tr>').join('')+'</tbody></table><p></p>';exec('insertHTML',h)};
let sourceMode=false;document.getElementById('toggle-source').onclick=()=>{if(!sourceMode){sourceMode=true;source.value=editor.innerHTML;editor.textContent=source.value;editor.classList.add('source-mode');document.getElementById('toggle-source').classList.add('active')}else{sourceMode=false;editor.innerHTML=editor.textContent;editor.classList.remove('source-mode');document.getElementById('toggle-source').classList.remove('active');sync()}};
document.getElementById('preview-content').onclick=()=>{if(sourceMode)document.getElementById('toggle-source').click();const w=window.open('','_blank','width=1100,height=800');if(!w)return;w.document.write('<!doctype html><html><head><title>Content Preview</title><style>body{font-family:Arial,sans-serif;max-width:900px;margin:40px auto;padding:0 20px;line-height:1.75;color:#17252d}img,video,iframe{max-width:100%}table{width:100%;border-collapse:collapse}td,th{border:1px solid #ccc;padding:8px}blockquote{border-left:4px solid #28aaca;padding-left:15px}</style></head><body>'+editor.innerHTML+'</body></html>');w.document.close()};
document.getElementById('toggle-fullscreen').onclick=()=>{document.querySelector('.editor-shell').classList.toggle('is-fullscreen');document.querySelector('.editor-shell').classList.contains('is-fullscreen')?document.getElementById('toggle-fullscreen').innerHTML='<i class="fa-solid fa-compress"></i>':document.getElementById('toggle-fullscreen').innerHTML='<i class="fa-solid fa-expand"></i>'};
async function upload(file){const fd=new FormData();fd.append('media',file);const res=await fetch('{{ route('admin.site-content.media') }}',{method:'POST',headers:{'X-CSRF-TOKEN':document.querySelector('meta[name=csrf-token]').content,'Accept':'application/json'},body:fd});if(!res.ok)throw new Error('Upload failed for '+file.name);return res.json()}
const safeName=n=>n.replaceAll('"','');
document.getElementById('upload-image').onclick=()=>mediaInput.click();mediaInput.onchange=async()=>{if(!mediaInput.files[0])return;try{const d=await upload(mediaInput.files[0]);exec('insertHTML',`<img src="${d.url}" alt="${safeName(d.name)}" loading="lazy">`)}catch(e){alert(e.message)}mediaInput.value=''};
document.getElementById('upload-video').onclick=()=>videoInput.click();videoInput.onchange=async()=>{if(!videoInput.files[0])return;try{const d=await upload(videoInput.files[0]);exec('insertHTML',`<video controls preload="metadata" src="${d.url}"></video>`)}catch(e){alert(e.message)}videoInput.value=''};
document.getElementById('insert-youtube').onclick=()=>{const url=prompt('YouTube URL');if(!url)return;const m=url.match(/(?:youtu\.be\/|youtube\.com\/(?:watch\?v=|shorts\/|embed\/))([^?&/]+)/);if(m)exec('insertHTML',`<iframe src="https://www.youtube.com/embed/${m[1]}" title="YouTube" allowfullscreen loading="lazy"></iframe>`)};
document.getElementById('insert-facebook').onclick=()=>{const url=prompt('Facebook video URL');if(url)exec('insertHTML',`<iframe src="https://www.facebook.com/plugins/video.php?href=${encodeURIComponent(url)}&show_text=false" title="Facebook" allowfullscreen loading="lazy"></iframe>`)};
document.getElementById('insert-gallery').onclick=()=>galleryInput.click();galleryInput.onchange=async()=>{const files=[...galleryInput.files];if(!files.length)return;try{const uploads=await Promise.all(files.map(upload));exec('insertHTML','<div class="media-gallery">'+uploads.map(d=>`<img src="${d.url}" alt="${safeName(d.name)}" loading="lazy">`).join('')+'</div>')}catch(e){alert(e.message)}galleryInput.value=''};
if(galleryBatchInput){document.getElementById('gallery-batch-upload').onclick=()=>galleryBatchInput.click();galleryBatchInput.onchange=async()=>{const files=[...galleryBatchInput.files];if(!files.length)return;galleryStatus.textContent=`Uploading ${files.length} media files...`;try{const uploads=await Promise.all(files.map(upload));const html=uploads.map(d=>d.mime.startsWith('video/')?`<video controls preload="metadata" src="${d.url}"></video>`:`<img src="${d.url}" alt="${safeName(d.name)}" loading="lazy">`).join('');exec('insertHTML',`<div class="media-gallery">${html}</div>`);galleryStatus.textContent=`${uploads.length} files added successfully.`}catch(e){galleryStatus.textContent=e.message;alert(e.message)}galleryBatchInput.value=''}}
form.addEventListener('submit',sync);editor.addEventListener('input',sync);sync();

const publicationType=document.getElementById('publication-type');
document.querySelectorAll('[data-publication-type]').forEach(btn=>btn.addEventListener('click',()=>{
    if(!publicationType)return;
    publicationType.value=btn.dataset.publicationType;
    document.querySelectorAll('[data-publication-type]').forEach(b=>b.classList.toggle('selected',b===btn));
}));

const attachmentInput=document.getElementById('attachment-input'),attachmentStatus=document.getElementById('attachment-status'),attachmentProgress=document.getElementById('attachment-progress'),attachmentBar=document.getElementById('attachment-progress-bar'),attachmentCurrent=document.getElementById('attachment-current');
const csrf=document.querySelector('meta[name=csrf-token]')?.content;
const formatSize=b=>{if(!b)return '0 B';const u=['B','KB','MB','GB'];const i=Math.min(Math.floor(Math.log(b)/Math.log(1024)),3);return (b/Math.pow(1024,i)).toFixed(i?1:0)+' '+u[i]};
async function uploadAttachment(file){
    if(!file||file.type!=='application/pdf')throw new Error('Please select a valid PDF file.');
    const max=2*1024*1024*1024;if(file.size>max)throw new Error('The PDF must be 2 GB or smaller.');
    attachmentStatus.textContent='Preparing secure upload...';attachmentProgress.classList.add('show');attachmentBar.style.width='0%';
    const init=await fetch('{{ $item->exists ? route('admin.site-content.attachment.chunks',$item) : '#' }}',{method:'POST',headers:{'X-CSRF-TOKEN':csrf,'Accept':'application/json','Content-Type':'application/json'},body:JSON.stringify({filename:file.name,size:file.size})});
    if(!init.ok)throw new Error((await init.json().catch(()=>({}))).message||'Unable to start upload.');
    const meta=await init.json(),id=meta.upload_id,size=meta.chunk_size||524288;let uploaded=0;
    for(let offset=0;offset<file.size;offset+=size){
        const chunk=file.slice(offset,Math.min(offset+size,file.size));
        const res=await fetch('{{ $item->exists ? route('admin.site-content.attachment.chunks',$item) : '#' }}',{method:'POST',headers:{'X-CSRF-TOKEN':csrf,'Accept':'application/json','X-Upload-Id':id,'X-Chunk-Index':String(Math.floor(offset/size)),'X-Chunk-Offset':String(offset),'Content-Type':'application/octet-stream'},body:chunk});
        if(!res.ok)throw new Error((await res.json().catch(()=>({}))).message||'A file chunk failed to upload.');
        uploaded+=chunk.size;attachmentBar.style.width=Math.round(uploaded/file.size*100)+'%';attachmentStatus.textContent='Uploading PDF — '+formatSize(uploaded)+' / '+formatSize(file.size);
    }
    attachmentStatus.textContent='Finalizing PDF...';
    const done=await fetch('{{ $item->exists ? route('admin.site-content.attachment.chunks',$item) : '#' }}',{method:'POST',headers:{'X-CSRF-TOKEN':csrf,'Accept':'application/json','X-Upload-Id':id,'Content-Type':'application/json'},body:JSON.stringify({finalize:true})});
    if(!done.ok)throw new Error((await done.json().catch(()=>({}))).message||'Unable to finalize the PDF.');
    const data=await done.json();
    attachmentStatus.textContent='PDF uploaded successfully.';
    attachmentCurrent.classList.add('has-file');
    attachmentCurrent.innerHTML='<i class="fa-solid fa-file-pdf"></i><div><strong>'+data.name.replaceAll('<','&lt;').replaceAll('>','&gt;')+'</strong><span>'+formatSize(data.size)+'</span></div><button type="button" id="attachment-remove" title="Remove PDF" aria-label="Remove PDF"><i class="fa-solid fa-trash-can"></i></button>';
    bindAttachmentRemove();
}
function bindAttachmentRemove(){
    const btn=document.getElementById('attachment-remove');if(!btn)return;
    btn.onclick=async()=>{if(!confirm('Remove this PDF attachment?'))return;const res=await fetch('{{ $item->exists ? route('admin.site-content.attachment.destroy',$item) : '#' }}',{method:'DELETE',headers:{'X-CSRF-TOKEN':csrf,'Accept':'application/json'}});if(!res.ok){attachmentStatus.textContent='Unable to remove the PDF.';return}attachmentCurrent.innerHTML='<div class="attachment-empty"><i class="fa-regular fa-file-pdf"></i><span>No PDF attached</span></div>';attachmentCurrent.classList.remove('has-file');attachmentStatus.textContent='PDF attachment removed.'};
}
if(attachmentInput){document.getElementById('attachment-upload').onclick=()=>attachmentInput.click();attachmentInput.onchange=async()=>{const file=attachmentInput.files[0];if(!file)return;try{await uploadAttachment(file)}catch(e){attachmentStatus.textContent=e.message;attachmentProgress.classList.remove('show');alert(e.message)}attachmentInput.value=''};bindAttachmentRemove();}

/* Word-like editor behavior layer */
(function(){
  const ed=document.getElementById('editor');
  const bar=document.querySelector('.editor-toolbar');
  if(!ed||!bar)return;
  let savedRange=null;

  function saveSelection(){
    const sel=window.getSelection();
    if(!sel||!sel.rangeCount)return;
    const r=sel.getRangeAt(0);
    if(ed.contains(r.commonAncestorContainer))savedRange=r.cloneRange();
  }
  function restoreSelection(){
    if(!savedRange)return;
    try{
      const sel=window.getSelection();sel.removeAllRanges();sel.addRange(savedRange);
    }catch(e){}
  }
  function currentBlock(){
    const sel=window.getSelection();
    let n=sel&&sel.rangeCount?sel.anchorNode:null;
    if(!n)return null;
    if(n.nodeType===3)n=n.parentElement;
    while(n&&n!==ed){
      if(/^(P|DIV|H1|H2|H3|H4|H5|H6|BLOCKQUOTE|LI)$/.test(n.tagName))return n;
      n=n.parentElement;
    }
    return null;
  }
  function pxFromSize(v){
    const map={1:'10px',2:'13px',3:'16px',4:'18px',5:'24px',6:'32px',7:'48px'};
    return map[String(v)]||'—';
  }
  function updateState(){
    const block=currentBlock();
    const tag=block?.tagName?.toLowerCase()||'p';
    const format=document.getElementById('block-format');
    if(format){
      const allowed=['p','h1','h2','h3','h4','h5','h6','blockquote'];
      format.value=allowed.includes(tag)?tag:'p';
    }
    const size=document.getElementById('font-size');
    let fs='';
    try{fs=document.queryCommandValue('fontSize')||''}catch(e){}
    if(size && ['2','3','4','5','6'].includes(String(fs)))size.value=String(fs); else if(size)size.value='';
    const b=bar.querySelector('[data-cmd="bold"]'),i=bar.querySelector('[data-cmd="italic"]'),u=bar.querySelector('[data-cmd="underline"]');
    [['bold',b],['italic',i],['underline',u]].forEach(([cmd,btn])=>{
      if(!btn)return;let active=false;try{active=document.queryCommandState(cmd)}catch(e){}
      btn.classList.toggle('active',!!active);btn.setAttribute('aria-pressed',active?'true':'false');
    });
    let px='—';
    if(block){const computed=getComputedStyle(block);px=computed.fontSize||'—'}
    let label=tag==='p'?'Paragraph':tag.toUpperCase();
    if(tag==='blockquote')label='Quote';
    const status=document.getElementById('editor-format-status');
    if(status)status.innerHTML='<strong>'+label+'</strong><span class="status-sep">•</span><span>'+px+'</span>';
  }

  // Preserve the selection before toolbar controls take focus.
  bar.addEventListener('mousedown',function(e){
    if(e.target.closest('button,select,input'))saveSelection();
  },true);

  // Patch command execution so formatting applies to the saved selection.
  bar.querySelectorAll('[data-cmd]').forEach(btn=>{
    btn.addEventListener('click',function(){
      restoreSelection();
      ed.focus();
      setTimeout(updateState,0);
    },true);
  });
  ['block-format','font-size','text-color','highlight-color'].forEach(id=>{
    const el=document.getElementById(id);
    if(el)el.addEventListener('change',()=>{restoreSelection();ed.focus();setTimeout(updateState,0)},true);
  });

  // After a heading/list/quote, Enter creates a fresh paragraph instead of inheriting the block.
  ed.addEventListener('keydown',function(e){
    if(e.key!=='Enter'||e.shiftKey)return;
    const block=currentBlock();
    if(!block)return;
    const tag=block.tagName;
    if(/^H[1-6]$/.test(tag)||tag==='BLOCKQUOTE'){
      setTimeout(function(){
        const next=currentBlock();
        if(next && next!==block && (/^H[1-6]$/.test(next.tagName)||next.tagName==='BLOCKQUOTE') && !next.textContent.trim()){
          const p=document.createElement('p');p.innerHTML='<br>';
          next.replaceWith(p);
          const range=document.createRange();range.selectNodeContents(p);range.collapse(true);
          const sel=window.getSelection();sel.removeAllRanges();sel.addRange(range);savedRange=range.cloneRange();sync();updateState();
        }
      },0);
    }
  });

  // Ensure an explicit Paragraph selection is always a real paragraph.
  const format=document.getElementById('block-format');
  if(format)format.addEventListener('change',function(){
    if(this.value!=='p')return;
    restoreSelection();
    const block=currentBlock();
    if(block && block.tagName!=='P'){
      document.execCommand('formatBlock',false,'p');
      sync();updateState();
    }
  },true);

  // Dynamic status indicator: format + current rendered font size.
  if(!document.getElementById('editor-format-status')){
    const status=document.createElement('span');
    status.id='editor-format-status';
    status.className='editor-status';
    status.setAttribute('aria-live','polite');
    status.innerHTML='<strong>Paragraph</strong><span class="status-sep">•</span><span>—</span>';
    bar.appendChild(status);
  }

  document.addEventListener('selectionchange',function(){
    const sel=window.getSelection();
    if(sel&&sel.rangeCount&&ed.contains(sel.anchorNode)){savedRange=sel.getRangeAt(0).cloneRange();updateState();}
  });
  ['keyup','mouseup','input','focus'].forEach(ev=>ed.addEventListener(ev,function(){saveSelection();updateState()}));
  window.addEventListener('resize',updateState);
  setTimeout(updateState,50);
})();
</script>@endpush
