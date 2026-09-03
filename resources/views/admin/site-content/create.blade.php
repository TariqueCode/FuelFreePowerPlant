@extends('layouts.portal')
@section('title',$item->exists?'Edit '.$labels[$item->type]:'New '.$labels[$lockedType ?? 'company'])
@section('content')
@php($contentType=$lockedType ?? $item->type)
<section class="hero"><div><span class="eyebrow">{{ strtoupper($labels[$contentType] ?? 'CONTENT') }} CMS</span><h1>{{ $item->exists?'Edit content':'Create content' }}</h1>@if($contentType==='news')<p>Create a polished news or notice with a direct cover-photo upload and visual content editor.</p>@elseif($contentType==='resource')<p>Create a public-safe resource with rich explanatory content and an optional official PDF attachment.</p>@endif</div><a class="back" href="{{ route('admin.site-content.index',['type'=>in_array($item->type,['news','announcement'],true)?'news':($item->type ?: $lockedType)]) }}"><i class="fa-solid fa-arrow-left"></i> Back</a></section>
@if($errors->any())<div class="errors">{{ $errors->first() }}</div>@endif
<div class="card"><form id="content-form" method="POST" action="{{ $item->exists?route('admin.site-content.update',$item):route('admin.site-content.store') }}">@csrf @if($item->exists)@method('PATCH')@endif
<div class="grid">
<div>@if($lockedType)
<label>Section</label>
<div class="locked-type"><i class="fa-solid {{ $contentType==='company'?'fa-building':($contentType==='gallery'?'fa-images':'fa-newspaper')) }}"></i>{{ $labels[$contentType] }}</div>
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
<div class="attachment-head"><div><label>{{ $contentType==='resource' ? 'Resource PDF attachment' : 'PDF attachment' }}</label><small>{{ $contentType==='resource' ? 'Attach the public-safe resource PDF. Only resources with Published status are listed on the public website.' : 'Attach the official notice PDF.' }} Large files are uploaded in small chunks, so normal PHP upload limits do not block the transfer. Maximum: 2 GB.</small></div>
@if($item->exists)<button type="button" class="attachment-upload" id="attachment-upload"><i class="fa-solid fa-file-arrow-up"></i> {{ $item->attachment_path ? 'Replace PDF' : 'Upload PDF' }}</button>@else<div class="attachment-save-note"><i class="fa-solid fa-circle-info"></i> Save this publication first to attach a PDF.</div>@endif</div>
@if($item->exists)<div class="attachment-current {{ $item->attachment_path ? 'has-file' : '' }}" id="attachment-current">@if($item->attachment_path)<i class="fa-solid fa-file-pdf"></i><div><strong>{{ $item->attachment_name }}</strong><span>{{ $item->attachment_size ? number_format($item->attachment_size / 1048576, 1).' MB' : 'PDF document' }}</span></div><button type="button" id="attachment-remove" title="Remove PDF" aria-label="Remove PDF"><i class="fa-solid fa-trash-can"></i></button>@else<div class="attachment-empty"><i class="fa-regular fa-file-pdf"></i><span>No PDF attached</span></div>@endif</div><div class="attachment-progress" id="attachment-progress"><div class="attachment-progress-bar" id="attachment-progress-bar"></div></div><div class="attachment-status" id="attachment-status"></div><input id="attachment-input" type="file" hidden accept="application/pdf,.pdf">@endif
</div>
@endif
@if($contentType==='gallery')<div class="full gallery-upload-box"><div class="gallery-upload-copy"><div><strong><i class="fa-solid fa-photo-film"></i> Gallery media</strong><p>Select multiple photos and videos together. They will be inserted into this event automatically.</p></div><button type="button" class="gallery-upload-btn" id="gallery-batch-upload"><i class="fa-solid fa-cloud-arrow-up"></i> Select photos &amp; videos</button></div><div id="gallery-upload-status" class="gallery-upload-status"></div></div>@endif
<div class="full"><label>Content</label><div class="editor-shell">
  <div class="word-ribbon" role="toolbar" aria-label="Microsoft Word style content editor">
    <div class="word-tabs" role="tablist">
      <button type="button" class="word-tab active" data-editor-tab="home" role="tab" aria-selected="true">Home</button>
      <button type="button" class="word-tab" data-editor-tab="insert" role="tab" aria-selected="false">Insert</button>
      <button type="button" class="word-tab" data-editor-tab="view" role="tab" aria-selected="false">View</button>
      <span class="word-ribbon-spacer"></span>
      <span id="editor-format-status" class="editor-status" aria-live="polite"><strong>Paragraph</strong><span class="status-sep">•</span><span>—</span></span>
    </div>

    <div class="word-panel active" data-editor-panel="home">
      <div class="word-group word-group-clipboard">
        <button type="button" class="word-command" data-cmd="undo" title="Undo"><i class="fa-solid fa-rotate-left"></i><span>Undo</span></button>
        <button type="button" class="word-command" data-cmd="redo" title="Redo"><i class="fa-solid fa-rotate-right"></i><span>Redo</span></button>
        <span class="word-group-label">Clipboard</span>
      </div>
      <div class="word-group">
        <div class="word-group-row">
          <select id="block-format" class="word-select format-select" title="Styles">
            <option value="p">Paragraph</option><option value="h1">Heading 1</option><option value="h2">Heading 2</option><option value="h3">Heading 3</option><option value="h4">Heading 4</option><option value="h5">Heading 5</option><option value="h6">Heading 6</option><option value="blockquote">Quote</option><option value="pre">Code</option>
          </select>
          <select id="font-name" class="word-select font-select" title="Font">
            <option value="Arial">Arial</option><option value="Georgia">Georgia</option><option value="Tahoma">Tahoma</option><option value="Times New Roman">Times New Roman</option><option value="Verdana">Verdana</option>
          </select>
          <select id="font-size" class="word-select size-select" title="Font size">
            <option value="">Size</option><option value="2">Small</option><option value="3">Normal</option><option value="4">Large</option><option value="5">X-Large</option><option value="6">Huge</option>
          </select>
        </div>
        <div class="word-group-row word-format-row">
          <button type="button" class="word-icon" data-cmd="bold" title="Bold"><b>B</b></button>
          <button type="button" class="word-icon" data-cmd="italic" title="Italic"><i>I</i></button>
          <button type="button" class="word-icon" data-cmd="underline" title="Underline"><u>U</u></button>
          <button type="button" class="word-icon" data-cmd="strikeThrough" title="Strikethrough"><s>S</s></button>
          <label class="word-color" title="Text color"><i class="fa-solid fa-font"></i><input type="color" id="text-color" value="#dff6fb" aria-label="Text color"></label>
          <label class="word-color" title="Highlight color"><i class="fa-solid fa-highlighter"></i><input type="color" id="highlight-color" value="#17323b" aria-label="Highlight color"></label>
        </div>
        <span class="word-group-label">Font</span>
      </div>
      <div class="word-group">
        <div class="word-group-row">
          <button type="button" class="word-icon" data-cmd="justifyLeft" title="Align left"><i class="fa-solid fa-align-left"></i></button>
          <button type="button" class="word-icon" data-cmd="justifyCenter" title="Center"><i class="fa-solid fa-align-center"></i></button>
          <button type="button" class="word-icon" data-cmd="justifyRight" title="Align right"><i class="fa-solid fa-align-right"></i></button>
          <button type="button" class="word-icon" data-cmd="justifyFull" title="Justify"><i class="fa-solid fa-align-justify"></i></button>
        </div>
        <div class="word-group-row">
          <button type="button" class="word-icon" data-cmd="insertUnorderedList" title="Bulleted list"><i class="fa-solid fa-list"></i></button>
          <button type="button" class="word-icon" data-cmd="insertOrderedList" title="Numbered list"><i class="fa-solid fa-list-ol"></i></button>
          <button type="button" class="word-icon" data-cmd="outdent" title="Decrease indent"><i class="fa-solid fa-outdent"></i></button>
          <button type="button" class="word-icon" data-cmd="indent" title="Increase indent"><i class="fa-solid fa-indent"></i></button>
        </div>
        <span class="word-group-label">Paragraph</span>
      </div>
      <div class="word-group">
        <button type="button" class="word-command" data-cmd="removeFormat" title="Clear formatting"><i class="fa-solid fa-eraser"></i><span>Clear<br>formatting</span></button>
        <span class="word-group-label">Editing</span>
      </div>
    </div>

    <div class="word-panel" data-editor-panel="insert">
      <div class="word-group">
        <button type="button" class="word-command" id="insert-link" title="Insert link"><i class="fa-solid fa-link"></i><span>Link</span></button>
        <button type="button" class="word-command" id="upload-image" title="Upload image"><i class="fa-regular fa-image"></i><span>Picture</span></button>
        <button type="button" class="word-command" id="insert-image-url" title="Insert image from URL"><i class="fa-solid fa-image"></i><span>Image URL</span></button>
        <button type="button" class="word-command" id="upload-video" title="Upload video"><i class="fa-solid fa-video"></i><span>Video</span></button>
        <button type="button" class="word-command" id="insert-video-url" title="Insert video from URL"><i class="fa-solid fa-link"></i><span>Video URL</span></button>
        <button type="button" class="word-command" id="insert-table" title="Insert table"><i class="fa-solid fa-table"></i><span>Table</span></button>
        <span class="word-group-label">Media</span>
      </div>
      <div class="word-group">
        <button type="button" class="word-command" id="insert-button" title="CTA button"><i class="fa-solid fa-square-up-right"></i><span>Button</span></button>
        <button type="button" class="word-command" id="insert-columns" title="Columns"><i class="fa-solid fa-table-columns"></i><span>Columns</span></button>
        <button type="button" class="word-command" id="insert-gallery" title="Image gallery"><i class="fa-solid fa-images"></i><span>Gallery</span></button>
        <button type="button" class="word-command" id="insert-youtube" title="YouTube embed"><i class="fa-brands fa-youtube"></i><span>YouTube</span></button>
        <button type="button" class="word-command" id="insert-facebook" title="Facebook video embed"><i class="fa-brands fa-facebook"></i><span>Facebook</span></button>
        <span class="word-group-label">Content</span>
      </div>
      <div class="word-group">
        <button type="button" class="word-command" data-cmd="insertHorizontalRule" title="Horizontal line"><i class="fa-solid fa-minus"></i><span>Horizontal<br>line</span></button>
        <button type="button" class="word-command" id="image-align-left" title="Image left"><i class="fa-solid fa-align-left"></i><span>Image left</span></button>
        <button type="button" class="word-command" id="image-align-center" title="Image center"><i class="fa-solid fa-align-center"></i><span>Image center</span></button>
        <button type="button" class="word-command" id="image-align-right" title="Image right"><i class="fa-solid fa-align-right"></i><span>Image right</span></button>
        <span class="word-group-label">Arrange</span>
      </div>
    </div>

    <div class="word-panel" data-editor-panel="view">
      <div class="word-group">
        <button type="button" class="word-command" id="toggle-source" title="HTML source"><i class="fa-solid fa-code"></i><span>HTML<br>source</span></button>
        <button type="button" class="word-command" id="preview-content" title="Preview"><i class="fa-solid fa-eye"></i><span>Preview</span></button>
        <button type="button" class="word-command" id="toggle-fullscreen" title="Fullscreen"><i class="fa-solid fa-expand"></i><span>Fullscreen</span></button>
        <span class="word-group-label">View</span>
      </div>
      <div class="word-group">
        <div class="word-view-note"><i class="fa-solid fa-circle-info"></i><span>Use the ribbon tabs to access formatting and insertion tools.</span></div>
        <span class="word-group-label">Editor</span>
      </div>
    </div>
  </div>
<div id="editor" class="editor" contenteditable="true">{!! old('content',$item->content) !!}</div>
<div class="editor-tools-footer"><span class="editor-counts" id="editor-counts">0 words • 0 characters</span></div>
</div><textarea id="content-source" name="content" hidden></textarea><input id="media-input" type="file" hidden accept="image/jpeg,image/png,image/webp,image/gif"><input id="gallery-input" type="file" hidden multiple accept="image/jpeg,image/png,image/webp,image/gif"><input id="video-input" type="file" hidden accept="video/mp4,video/webm">@if($contentType==='gallery')<input id="gallery-batch-input" type="file" hidden multiple accept="image/jpeg,image/png,image/webp,image/gif,video/mp4,video/webm">@endif</div>
<div><label>{{ $contentType==='gallery' ? 'Event date &amp; time' : 'Publish date/time' }}</label><input type="datetime-local" name="published_at" value="{{ old('published_at',$item->published_at?->format('Y-m-d\\TH:i')) }}"></div>
</div><div class="actions"><a class="back" href="{{ route('admin.site-content.index',['type'=>in_array($item->type,['news','announcement'],true)?'news':$item->type]) }}">Cancel</a><button class="save" type="submit"><i class="fa-solid fa-floppy-disk"></i> {{ $item->exists?'Save changes':'Create content' }}</button></div></form></div>
@endsection

@push('styles')<style>
.hero{display:flex;justify-content:space-between;align-items:flex-end;gap:18px;margin-bottom:18px}.hero h1{font-size:clamp(26px,4vw,40px);margin:6px 0}.hero p{margin:0;color:#7898a5;font-size:11px;line-height:1.6}.eyebrow{font-size:9px;letter-spacing:.14em;color:#4ec5e5}.back{display:inline-flex;align-items:center;gap:7px;padding:10px 13px;border:1px solid var(--line);border-radius:11px;color:#9db9c2;text-decoration:none;font-size:10px}.card{max-width:1100px;padding:20px;border:1px solid var(--line);border-radius:18px;background:rgba(255,255,255,.02)}.grid{display:grid;grid-template-columns:1fr 1fr;gap:15px}.full{grid-column:1/-1}label{display:block;color:#89a7b2;font-size:10px;margin-bottom:7px}input,select,textarea{width:100%;box-sizing:border-box;border:1px solid var(--line);border-radius:10px;background:#061923;color:#e4f3f7;padding:11px;font:inherit;font-size:11px;outline:none}textarea{resize:vertical;line-height:1.6}.hint{display:block;margin-top:6px;color:#678692;font-size:9px}.locked-type{height:40px;display:flex;align-items:center;gap:9px;padding:0 12px;border:1px solid var(--line);border-radius:10px;background:rgba(67,194,229,.05);color:#b9e6ef;font-size:11px}.locked-type i{color:#61d5ed}.publication-type{display:grid;grid-template-columns:1fr 1fr;gap:6px;margin-top:8px}.publication-type button{height:40px;border:1px solid var(--line);border-radius:10px;background:#061923;color:#7e9ca5;font-size:10px;font-weight:800;cursor:pointer}.publication-type button i{margin-right:6px}.publication-type button.selected{color:#e7fbff;border-color:rgba(72,216,241,.35);background:rgba(72,216,241,.09);box-shadow:inset 0 0 0 1px rgba(72,216,241,.06)}.attachment-box{padding:16px;border:1px solid rgba(67,194,229,.18);border-radius:15px;background:linear-gradient(145deg,rgba(67,194,229,.06),rgba(255,255,255,.02))}.attachment-head{display:flex;align-items:center;justify-content:space-between;gap:15px}.attachment-head label{margin-bottom:3px}.attachment-head small{color:#6f8e98;font-size:9px;line-height:1.5}.attachment-upload{border:1px solid rgba(82,216,240,.3);border-radius:10px;background:rgba(55,197,230,.12);color:#dffaff;padding:10px 13px;cursor:pointer;font-size:10px;font-weight:800;white-space:nowrap}.attachment-save-note{display:flex;align-items:center;gap:7px;color:#6f8e98;font-size:9px;padding:9px 11px;border:1px solid var(--line);border-radius:10px}.attachment-save-note i{color:#5fcfe8}.attachment-current{display:flex;align-items:center;gap:11px;margin-top:12px;min-height:58px;padding:10px 12px;border:1px solid var(--line);border-radius:11px;background:#061923}.attachment-current>i{font-size:25px;color:#ff8f9a}.attachment-current>div{min-width:0;flex:1;display:flex;flex-direction:column;gap:3px}.attachment-current strong{font-size:10px;color:#dff5f8;white-space:nowrap;overflow:hidden;text-overflow:ellipsis}.attachment-current span{font-size:8px;color:#6f8e98}.attachment-current button{width:32px;height:32px;border:1px solid rgba(255,99,113,.14);border-radius:9px;background:rgba(255,99,113,.05);color:#ff9eaa;cursor:pointer}.attachment-empty{display:flex;align-items:center;gap:9px;color:#668792;font-size:9px}.attachment-empty i{font-size:20px;color:#4fc8e4}.attachment-progress{display:none;height:6px;margin-top:10px;border-radius:999px;background:#0a2530;overflow:hidden}.attachment-progress.show{display:block}.attachment-progress-bar{height:100%;width:0;background:linear-gradient(90deg,#27b8d4,#65e3f1);transition:width .12s}.attachment-status{margin-top:7px;color:#70d9ea;font-size:9px;min-height:13px}.nav-visibility,.featured-box{padding:12px;border:1px solid rgba(67,194,229,.16);border-radius:11px;background:rgba(67,194,229,.045)}.check-label{display:flex;align-items:center;gap:8px;color:#bfe7ef;font-size:11px;margin:0;cursor:pointer}.check-label input{width:17px;height:17px;margin:0;accent-color:#29aaca}.nav-visibility small,.featured-box small{display:block;margin:6px 0 0 25px;color:#678692;font-size:9px;line-height:1.5}.news-cover-field{padding:16px;border:1px solid rgba(67,194,229,.18);border-radius:16px;background:linear-gradient(145deg,rgba(67,194,229,.07),rgba(255,255,255,.02))}.field-head{display:flex;align-items:center;justify-content:space-between;gap:15px}.field-head label{margin-bottom:3px}.field-head small{color:#6f8e98;font-size:9px}.upload-cover,.gallery-upload-btn{border:1px solid rgba(82,216,240,.3);border-radius:10px;background:rgba(55,197,230,.12);color:#dffaff;padding:10px 13px;cursor:pointer;font-size:10px;font-weight:800;white-space:nowrap}.cover-preview{margin-top:12px;width:100%;aspect-ratio:16/9;border-radius:13px;border:1px dashed rgba(100,190,210,.22);background:#061923;display:grid;place-items:center;overflow:hidden}.cover-preview img{width:100%;height:100%;object-fit:cover}.cover-preview div{text-align:center;color:#5f8793}.cover-preview i{font-size:30px;color:#4fc8e4;display:block;margin-bottom:7px}.cover-preview span{font-size:10px}.cover-actions{display:flex;align-items:center;justify-content:space-between;margin-top:8px;min-height:26px}.remove-cover{border:0;background:transparent;color:#8da8b0;font-size:9px;cursor:pointer;padding:5px}.remove-cover:not(:disabled):hover{color:#ff9da4}.remove-cover:disabled{opacity:.35;cursor:not-allowed}.cover-actions span{color:#6fd5e8;font-size:9px}.seo-box{padding:15px;border:1px solid rgba(67,194,229,.15);border-radius:14px;background:rgba(67,194,229,.03)}.seo-title{display:flex;align-items:center;gap:8px;color:#d7f4f7;font-size:11px;margin-bottom:12px}.seo-title i{color:#5ed8ee}.seo-grid{display:grid;grid-template-columns:1fr 1fr;gap:12px}.editor-shell{border:1px solid var(--line);border-radius:14px;overflow:visible;background:#061923}.editor-toolbar{display:flex;flex-wrap:wrap;align-items:center;gap:4px;padding:9px;border-bottom:1px solid var(--line);background:rgba(67,194,229,.04);position:relative;z-index:30}.editor-toolbar .tool-dropdown{position:relative;display:inline-flex}.editor-toolbar .tool-dropdown-toggle{display:inline-flex;align-items:center;justify-content:center;gap:5px;min-width:40px}.tool-caret{width:0;height:0;border-left:3px solid transparent;border-right:3px solid transparent;border-top:4px solid currentColor;margin-top:2px}.tool-dropdown.open>.tool-dropdown-toggle{background:rgba(67,194,229,.12);color:#eaf8fb}.tool-menu{display:none;position:absolute;top:calc(100% + 6px);left:0;z-index:20;min-width:190px;padding:6px;border:1px solid var(--line);border-radius:11px;background:#071b25;box-shadow:0 14px 35px rgba(0,0,0,.35)}.tool-dropdown.open>.tool-menu{display:block}.tool-menu-wide{min-width:205px}.tool-menu-insert{min-width:190px}.tool-menu-label{padding:5px 8px 7px;color:#5fcfe8;font-size:8px;letter-spacing:.12em;text-transform:uppercase}.tool-menu>button{width:100%;display:flex;align-items:center;justify-content:flex-start;gap:9px;height:32px;padding:0 9px;border:0;border-radius:7px;background:transparent;color:#a7c2cb;font-size:10px;text-align:left}.tool-menu>button:hover{background:rgba(67,194,229,.10);color:#eaf8fb}.tool-menu .tool-select{display:block;width:100%;height:32px;margin-bottom:5px}.tool-menu .tool-select:last-of-type{margin-bottom:7px}.tool-menu-row{display:flex;align-items:center;gap:9px;padding:4px 3px;color:#8eabb4;font-size:9px}.tool-menu-row input[type=color]{flex:0 0 34px}.editor-toolbar button{min-width:34px;height:32px;border:1px solid transparent;border-radius:8px;background:transparent;color:#a7c2cb;cursor:pointer}.editor-toolbar button:hover,.editor-toolbar button.active{background:rgba(67,194,229,.12);color:#eaf8fb}.editor-toolbar select{width:auto;height:32px;min-width:0;border:1px solid var(--line);border-radius:8px;background:#061923;color:#b9d7df;padding:0 7px;font-size:10px;flex:0 0 auto}.editor-toolbar .format-select{width:112px}.editor-toolbar .size-select{width:68px}.editor-toolbar .tool-select:focus{border-color:rgba(67,194,229,.38);outline:none}.editor-toolbar input[type=color]{width:34px;height:32px;padding:3px;border:1px solid var(--line);border-radius:8px;background:#061923;cursor:pointer}.tool-sep{width:1px;height:22px;background:var(--line);margin:0 3px}.editor{min-height:430px;padding:18px;color:#e6f4f7;line-height:1.75;font-size:13px;outline:none}.editor h1{font-size:30px}.editor h2{font-size:24px}.editor h3{font-size:19px}.editor h4{font-size:17px}.editor h5{font-size:15px}.editor h6{font-size:13px;text-transform:uppercase;letter-spacing:.04em}.editor blockquote{margin:14px 0;padding:10px 16px;border-left:3px solid #43c2e5;background:rgba(67,194,229,.06);color:#b9d7df}.editor table{width:100%;border-collapse:collapse;margin:14px 0}.editor td,.editor th{border:1px solid rgba(120,170,185,.35);padding:9px;text-align:left}.editor th{background:rgba(67,194,229,.08)}.editor.source-mode{font-family:ui-monospace,SFMono-Regular,Consolas,monospace;white-space:pre-wrap}.editor-shell.is-fullscreen{position:fixed;inset:0;z-index:9999;background:#04151e;border-radius:0;display:flex;flex-direction:column}.editor-shell.is-fullscreen .editor{flex:1;overflow:auto}.editor-shell.is-fullscreen .editor-toolbar{position:sticky;top:0;z-index:2}.editor img,.editor video{max-width:100%;height:auto;border-radius:12px;margin:10px 0}.editor img.align-left{display:block;margin-left:0;margin-right:auto}.editor img.align-center{display:block;margin-left:auto;margin-right:auto}.editor img.align-right{display:block;margin-left:auto;margin-right:0}.editor .content-columns{display:grid;gap:18px;margin:18px 0}.editor .content-columns.cols-2{grid-template-columns:repeat(2,minmax(0,1fr))}.editor .content-columns.cols-3{grid-template-columns:repeat(3,minmax(0,1fr))}.editor .content-column{min-width:0;padding:18px;border:1px solid rgba(67,194,229,.14);border-radius:12px;background:rgba(67,194,229,.025)}.editor .content-cta{display:inline-flex;align-items:center;justify-content:center;gap:7px;margin:10px 4px 10px 0;padding:11px 17px;border-radius:9px;text-decoration:none;font-weight:700;line-height:1.2;background:#29aaca;color:#fff}.editor .content-cta.cta-outline{background:transparent;color:#29aaca;border:1px solid #29aaca}.editor iframe{max-width:100%;width:100%;min-height:360px;border:0;border-radius:12px;margin:10px 0;background:#000}.media-gallery{display:grid;grid-template-columns:repeat(3,minmax(0,1fr));gap:10px;margin:14px 0}.media-gallery img{width:100%;aspect-ratio:16/10;object-fit:cover;border-radius:10px}.gallery-upload-box{padding:16px;border:1px solid rgba(67,209,240,.18);border-radius:15px;background:linear-gradient(135deg,rgba(67,209,240,.07),rgba(255,255,255,.02))}.gallery-upload-copy{display:flex;align-items:center;justify-content:space-between;gap:15px}.gallery-upload-copy strong{font-size:13px}.gallery-upload-copy strong i{color:#5eddf4;margin-right:6px}.gallery-upload-copy p{color:#7696a1;font-size:9px;margin:6px 0 0;line-height:1.6}.gallery-upload-status{color:#70d9ea;font-size:9px;margin-top:10px}.actions{display:flex;justify-content:flex-end;gap:9px;margin-top:20px}.save{border:0;border-radius:11px;padding:12px 16px;background:#29aaca;color:#fff;font-weight:700}.errors{padding:11px;border-radius:10px;margin-bottom:14px;background:rgba(210,65,65,.12);color:#ffb0b0}@media(max-width:650px){.hero{align-items:flex-start;gap:12px;flex-direction:column}.grid{grid-template-columns:1fr}.full{grid-column:auto}.seo-grid{grid-template-columns:1fr}.editor{min-height:360px}.editor-toolbar{position:sticky;top:0;z-index:2}.editor-toolbar .format-select{width:104px}.editor-toolbar .size-select{width:64px}.actions>*{flex:1;text-align:center}.media-gallery{grid-template-columns:repeat(2,minmax(0,1fr))}.gallery-upload-copy,.field-head{align-items:stretch;flex-direction:column}.gallery-upload-btn,.upload-cover,.attachment-upload{width:100%}.attachment-head{align-items:stretch;flex-direction:column}}

/* Word 365-inspired CMS editor UI. Dark CMS palette is intentionally preserved. */
.editor-shell{width:100%;max-width:100%;min-width:0;position:relative;overflow:visible;box-sizing:border-box;border:1px solid var(--line);border-radius:12px;background:#061923}
.word-ribbon{position:sticky;top:0;z-index:90;width:100%;max-width:100%;min-width:0;background:#071b25;border-bottom:1px solid rgba(104,204,235,.18);box-shadow:0 6px 18px rgba(0,0,0,.28);overflow:visible}
.word-tabs{display:flex;align-items:center;gap:2px;min-height:40px;padding:0 8px;border-bottom:1px solid rgba(104,204,235,.13);background:#061923;white-space:nowrap;overflow-x:auto;scrollbar-width:none}
.word-tabs::-webkit-scrollbar{display:none}
.word-tab{height:40px;padding:0 15px;border:0;border-bottom:2px solid transparent;background:transparent;color:#91aeb8;font-size:11px;font-weight:700;cursor:pointer}
.word-tab:hover{color:#dff6fb;background:rgba(67,194,229,.05)}
.word-tab.active{color:#eaf8fb;border-bottom-color:#43c2e5;background:rgba(67,194,229,.08)}
.word-ribbon-spacer{flex:1 1 auto;min-width:10px}
.editor-status{display:inline-flex;align-items:center;gap:6px;flex:0 0 auto;max-width:240px;color:#7f9ca5;font-size:9px;padding:5px 9px;border:1px solid rgba(104,204,235,.12);border-radius:7px;background:#071b25;white-space:nowrap}
.editor-status strong{color:#cfeaf0}.status-sep{color:#3d7180}
.word-panel{display:none;align-items:stretch;gap:0;min-width:max-content;min-height:76px;padding:6px 7px;overflow-x:auto;overflow-y:visible;scrollbar-width:thin}
.word-panel.active{display:flex}
.word-group{display:flex;align-items:center;gap:3px;position:relative;padding:3px 8px 17px;border-right:1px solid rgba(104,204,235,.12);min-height:58px}
.word-group:last-child{border-right:0}
.word-group-label{position:absolute;bottom:3px;left:8px;right:8px;text-align:center;color:#668791;font-size:8px;line-height:11px;white-space:nowrap;pointer-events:none}
.word-group-row{display:flex;align-items:center;gap:3px}
.word-command,.word-icon{border:1px solid transparent;background:transparent;color:#b7d0d7;border-radius:5px;cursor:pointer;transition:.15s}
.word-command{height:53px;min-width:42px;padding:5px 7px;display:flex;flex-direction:column;align-items:center;justify-content:center;gap:4px;font-size:9px;white-space:nowrap}
.word-command i{font-size:15px;color:#9bc1cb}.word-command span{line-height:11px}
.word-icon{width:31px;height:31px;display:inline-flex;align-items:center;justify-content:center;font-size:13px}
.word-command:hover,.word-icon:hover,.word-icon.active,.word-command.active{background:rgba(67,194,229,.12);border-color:rgba(67,194,229,.25);color:#effbfe}
.word-command:hover i,.word-icon:hover i{color:#dff6fb}
.word-select{height:31px;border:1px solid rgba(104,204,235,.16);border-radius:5px;background:#061923;color:#c7e0e6;padding:0 8px;font-size:10px;outline:none}
.word-select:focus{border-color:#43c2e5;box-shadow:0 0 0 2px rgba(67,194,229,.10)}
.format-select{width:125px}.font-select{width:120px}.size-select{width:72px}
.word-color{width:31px;height:31px;display:inline-flex;align-items:center;justify-content:center;border:1px solid rgba(104,204,235,.16);border-radius:5px;background:#061923;color:#b7d0d7;position:relative;cursor:pointer}
.word-color input{position:absolute;inset:0;width:100%;height:100%;opacity:0;cursor:pointer}
.word-view-note{display:flex;align-items:center;gap:9px;color:#7e9da6;font-size:10px;max-width:300px;line-height:1.5;padding:0 12px}
.word-view-note i{color:#43c2e5}
.editor{width:100%;max-width:100%;min-width:0;box-sizing:border-box;min-height:430px;padding:18px;color:#e6f4f7;line-height:1.75;font-size:13px;outline:none;overflow-wrap:anywhere;word-break:break-word}
.editor>*{max-width:100%;box-sizing:border-box}.editor h1{font-size:30px}.editor h2{font-size:24px}.editor h3{font-size:19px}.editor h4{font-size:17px}.editor h5{font-size:15px}.editor h6{font-size:13px;text-transform:uppercase;letter-spacing:.04em}.editor blockquote{margin:14px 0;padding:10px 16px;border-left:3px solid #43c2e5;background:rgba(67,194,229,.06);color:#b9d7df}.editor table{width:100%;max-width:100%;border-collapse:collapse;margin:14px 0}.editor td,.editor th{border:1px solid rgba(120,170,185,.35);padding:9px;text-align:left;overflow-wrap:anywhere;word-break:break-word}.editor th{background:rgba(67,194,229,.08)}.editor img,.editor video,.editor iframe{max-width:100%;height:auto;box-sizing:border-box}.editor iframe{width:100%;min-width:0}.editor.source-mode{font-family:ui-monospace,SFMono-Regular,Consolas,monospace;white-space:pre-wrap}
.editor-shell.is-fullscreen{position:fixed;inset:0;z-index:9999;background:#04151e;border-radius:0;display:flex;flex-direction:column}.editor-shell.is-fullscreen .word-ribbon{position:sticky;top:0;z-index:2}.editor-shell.is-fullscreen .editor{flex:1;overflow:auto}
@media(max-width:900px){.word-ribbon{top:0}.word-tabs{padding:0 5px}.word-tab{padding:0 12px}.editor-status{max-width:190px}.word-panel{min-height:72px}.word-group{padding-left:6px;padding-right:6px}.format-select{width:116px}.font-select{width:110px}}
@media(max-width:650px){.word-ribbon{top:0}.word-tabs{min-height:38px}.word-tab{height:38px;font-size:10px;padding:0 13px}.editor-status{display:none}.word-panel{min-height:74px;overflow-x:auto;padding:5px 4px}.word-group{padding-left:6px;padding-right:6px}.word-command{min-width:40px;height:51px}.word-icon{width:31px;height:31px}.word-select{height:31px;font-size:10px}.format-select{width:110px}.font-select{width:105px}.size-select{width:68px}.editor{font-size:16px;line-height:1.75;padding:14px}.editor h1{font-size:28px}.editor h2{font-size:23px}.editor h3{font-size:19px}.editor table{display:block;overflow-x:auto}.editor .content-columns{grid-template-columns:1fr!important}}
@media(max-width:420px){.word-panel{min-height:70px}.word-command{min-width:38px;height:49px;padding-left:5px;padding-right:5px}.word-command i{font-size:14px}.word-command span{font-size:8px}.word-icon{width:30px;height:30px}.word-select{font-size:9px}.format-select{width:104px}.font-select{width:98px}.size-select{width:65px}}

/* Final Word 365 ribbon behavior: one responsive, touch-scrollable ribbon. */
.word-ribbon{position:sticky;top:0;z-index:90;width:100%;max-width:100%;min-width:0;overflow:hidden;contain:layout paint}
.word-tabs{display:flex;align-items:center;gap:2px;width:100%;min-width:0;overflow-x:auto;overflow-y:hidden;-webkit-overflow-scrolling:touch;touch-action:pan-x;overscroll-behavior-x:contain;scrollbar-width:none}
.word-tabs::-webkit-scrollbar{display:none}
.word-panel{display:none;align-items:stretch;gap:0;width:100%;max-width:100%;min-width:0;overflow-x:auto;overflow-y:hidden;-webkit-overflow-scrolling:touch;touch-action:pan-x;overscroll-behavior-x:contain;scrollbar-width:thin}
.word-panel.active{display:flex}
.word-group{flex:0 0 auto}
.word-group-row{flex:0 0 auto}
.word-command,.word-icon,.word-select{flex:0 0 auto}
.editor-status{display:inline-flex;align-items:center;gap:6px;flex:0 0 auto;max-width:240px;white-space:nowrap}
.editor-shell{width:100%;max-width:100%;min-width:0;overflow:visible}
.editor{width:100%;max-width:100%;min-width:0;overflow-wrap:anywhere;word-break:break-word}
.editor pre{max-width:100%;overflow-x:auto;white-space:pre;padding:12px 14px;border:1px solid rgba(104,204,235,.16);border-radius:9px;background:#041017}
.editor table{max-width:100%}
.editor img,.editor video,.editor iframe{max-width:100%;height:auto;box-sizing:border-box}
.editor-tools-footer{display:flex;align-items:center;justify-content:space-between;gap:12px;padding:7px 12px;border-top:1px solid var(--line);background:#061923;color:#7899a5;font-size:9px}
.editor-counts{margin-left:auto;white-space:nowrap}
@media(max-width:900px){
  .word-ribbon{top:0}
  .editor-status{max-width:210px}
}
@media(max-width:650px){
  .word-ribbon{top:0}
  .word-tabs{min-height:38px}
  .word-tab{height:38px;font-size:10px;padding:0 13px}
  .editor-status{display:inline-flex;max-width:150px;font-size:8px;padding:4px 6px}
  .word-panel{min-height:74px;padding:5px 4px;overflow-x:scroll!important}
  .word-group{padding-left:6px;padding-right:6px}
  .word-command{min-width:40px;height:51px}
  .word-icon{width:31px;height:31px}
  .word-select{height:31px;font-size:10px}
  .editor{font-size:16px;line-height:1.75;padding:14px}
  .editor h1{font-size:28px}.editor h2{font-size:23px}.editor h3{font-size:19px}
  .editor table{display:block;overflow-x:auto}
  .editor .content-columns{grid-template-columns:1fr!important}
  .editor-tools-footer{font-size:8px;padding:7px 9px}
}
@media(max-width:420px){
  .editor-status{max-width:125px;overflow:hidden}
  .editor-status .status-sep{display:inline}
  .editor-status span:last-child{display:inline}
  .word-panel{min-height:70px}
  .word-command{min-width:38px;height:49px;padding-left:5px;padding-right:5px}
  .word-command i{font-size:14px}.word-command span{font-size:8px}
  .word-icon{width:30px;height:30px}.word-select{font-size:9px}
  .format-select{width:104px}.font-select{width:98px}.size-select{width:65px}
}
/* Sticky Word 365 ribbon — final fix.
   Keep the existing dark CMS palette and all editor features unchanged. */
html, body {
  overflow-x: hidden;
}
.main,
.content,
.card,
.grid,
.full,
.editor-shell {
  overflow: visible !important;
}
.word-ribbon {
  position: -webkit-sticky !important;
  position: sticky !important;
  top: 70px !important;
  z-index: 120 !important;
  width: 100% !important;
  max-width: 100% !important;
  min-width: 0 !important;
  margin: 0 !important;
  overflow: visible !important;
  contain: none !important;
  isolation: isolate;
}
.word-ribbon .word-tabs {
  position: relative;
  z-index: 2;
}
.word-ribbon .word-panel {
  position: relative;
  z-index: 1;
  overflow-x: auto !important;
  overflow-y: hidden !important;
  max-width: 100% !important;
  min-width: 0 !important;
  -webkit-overflow-scrolling: touch;
  touch-action: pan-x;
  overscroll-behavior-x: contain;
}
.editor-shell {
  position: relative !important;
  overflow: visible !important;
}
@media (max-width: 700px) {
  .word-ribbon {
    top: 70px !important;
    border-radius: 0;
  }
}
@media (min-width: 701px) {
  .word-ribbon {
    top: 70px !important;
  }
}

/* Final sticky positioning: ribbon stays directly below the dashboard topbar. */
html,body{overflow-x:hidden}
.main,.content,.card,.grid,.full,.editor-shell{overflow:visible!important}
.word-ribbon{
  position:-webkit-sticky!important;
  position:sticky!important;
  top:0!important;
  z-index:100!important;
  width:100%!important;
  max-width:100%!important;
  min-width:0!important;
  margin:0!important;
  overflow:visible!important;
  contain:none!important;
  isolation:isolate;
}
.word-ribbon .word-tabs{
  position:relative;
  z-index:2;
  min-width:0;
  overflow-x:auto;
  overflow-y:hidden;
  -webkit-overflow-scrolling:touch;
  touch-action:pan-x;
}
.word-ribbon .word-panel{
  position:relative;
  z-index:1;
  max-width:100%!important;
  min-width:0!important;
  overflow-x:auto!important;
  overflow-y:hidden!important;
  -webkit-overflow-scrolling:touch;
  touch-action:pan-x;
  overscroll-behavior-x:contain;
}
.editor-shell{position:relative!important;overflow:visible!important}
@media(max-width:700px){.word-ribbon{top:0!important}}

/* CMS editor: keep the ribbon visible while the editor is being scrolled. */
.editor-shell.ff-ribbon-active{
  --ff-ribbon-height:0px;
}
.editor-shell.ff-ribbon-active .word-ribbon{
  position:fixed !important;
  top:0 !important;
  left:var(--ff-ribbon-left,0px) !important;
  width:var(--ff-ribbon-width,100%) !important;
  max-width:none !important;
  margin:0 !important;
  z-index:9999 !important;
}
.editor-shell.ff-ribbon-active .editor{
  padding-top:calc(18px + var(--ff-ribbon-height,0px)) !important;
}
@media(max-width:700px){
  .editor-shell.ff-ribbon-active .editor{
    padding-top:calc(14px + var(--ff-ribbon-height,0px)) !important;
  }
}
/* Mobile editor ribbon: keep every control reachable without viewport clipping. */
@media (max-width:700px){
  .word-panel{
    display:flex;
    flex-wrap:wrap;
    align-content:flex-start;
    min-width:0!important;
    width:100%;
    max-width:100%;
    overflow-x:hidden!important;
    overflow-y:visible!important;
  }
  .word-group{
    max-width:100%;
    min-width:0;
    flex-wrap:wrap;
    align-content:flex-start;
  }
  .word-group-row{
    max-width:100%;
    flex-wrap:wrap;
  }
  .word-command,.word-icon,.word-select{
    flex:0 0 auto;
  }
  .word-ribbon .word-tabs{
    width:100%;
    max-width:100%;
  }
}
@media (max-width:420px){
  .word-group{padding-left:4px;padding-right:4px}
  .word-command{min-width:36px}
  .word-select.format-select{width:96px}
  .word-select.font-select{width:90px}
  .word-select.size-select{width:60px}
  .editor-status{max-width:118px}
}

/* Final mobile ribbon fix: keep tool groups in one horizontal track.
   Never stack ribbon groups vertically on narrow touch screens. */
@media (max-width:700px){
  .word-ribbon .word-panel{
    display:flex!important;
    flex-wrap:nowrap!important;
    align-items:stretch;
    justify-content:flex-start;
    overflow-x:auto!important;
    overflow-y:hidden!important;
    scrollbar-width:thin;
    min-height:68px!important;
  }
  .word-ribbon .word-group{
    flex:0 0 auto!important;
    width:max-content!important;
    max-width:none!important;
    min-width:max-content!important;
    flex-wrap:nowrap!important;
    align-content:initial!important;
  }
  .word-ribbon .word-group-row{
    flex:0 0 auto!important;
    flex-wrap:nowrap!important;
    max-width:none!important;
    width:max-content!important;
  }
  .word-ribbon .word-command,
  .word-ribbon .word-icon,
  .word-ribbon .word-select,
  .word-ribbon .word-color{
    flex:0 0 auto!important;
  }
}

/* Mobile CMS hardening: responsive form + touch-scrollable ribbon.
   Desktop rules and sticky-ribbon JavaScript remain untouched. */
@media (max-width:700px){
  .hero,.card,.grid,.full,.editor-shell,.editor-shell *{min-width:0}
  .hero{width:100%;box-sizing:border-box}
  .card{width:100%;box-sizing:border-box;padding:14px;border-radius:16px}
  .grid{display:grid;grid-template-columns:minmax(0,1fr);gap:13px}
  .grid>div{width:100%;min-width:0}
  .full{grid-column:auto}
  input,select,textarea{max-width:100%;min-width:0}
  .locked-type{max-width:100%;box-sizing:border-box}
  .publication-type{min-width:0}
  .nav-visibility,.featured-box,.seo-box,.attachment-box,.news-cover-field,.gallery-upload-box{width:100%;max-width:100%;box-sizing:border-box;min-width:0}
  .check-label{min-width:0;line-height:1.45}
  .check-label span{min-width:0;overflow-wrap:anywhere}
  .nav-visibility small,.featured-box small{margin-left:25px;overflow-wrap:anywhere}
  .field-head,.attachment-head,.gallery-upload-copy{width:100%;min-width:0}
  .field-head>div,.attachment-head>div,.gallery-upload-copy>div{min-width:0}
  .field-head small,.attachment-head small,.gallery-upload-copy p{overflow-wrap:anywhere}
  .upload-cover,.attachment-upload,.gallery-upload-btn{max-width:100%;white-space:normal}
  .cover-preview{max-width:100%}
  .seo-grid{grid-template-columns:minmax(0,1fr);min-width:0}
  .editor-shell{width:100%;max-width:100%;box-sizing:border-box;overflow:visible!important}
  .word-ribbon{width:100%!important;max-width:100%!important;min-width:0!important;overflow:hidden!important}
  .word-tabs,.word-panel{width:100%!important;max-width:100%!important;min-width:0!important;overflow-x:auto!important;overflow-y:hidden!important;-webkit-overflow-scrolling:touch;touch-action:pan-x;overscroll-behavior-x:contain}
  .word-tabs{scroll-snap-type:x proximity}
  .word-panel{scrollbar-width:thin}
  .word-group{flex:0 0 auto}
  .word-group-row{flex:0 0 auto}
  .word-command,.word-icon,.word-select{flex:0 0 auto}
  .format-select{width:112px!important}
  .font-select{width:105px!important}
  .size-select{width:68px!important}
  .editor{width:100%;max-width:100%;min-width:0;box-sizing:border-box;overflow-wrap:anywhere;word-break:break-word}
  .editor pre{max-width:100%;overflow-x:auto}
  .editor table{display:block;max-width:100%;overflow-x:auto;-webkit-overflow-scrolling:touch}
  .editor img,.editor video,.editor iframe{max-width:100%;height:auto}
  .editor iframe{width:100%;min-width:0}
  .editor .content-columns{grid-template-columns:minmax(0,1fr)!important}
  .actions{width:100%;min-width:0;flex-wrap:wrap}
  .actions>*{min-width:0}
}
@media (max-width:420px){
  .content{padding-left:10px!important;padding-right:10px!important}
  .card{padding:11px;border-radius:14px}
  .hero{gap:10px}
  .hero .back{width:auto;max-width:100%}
  .word-tab{padding-left:12px!important;padding-right:12px!important}
  .word-panel{min-height:68px}
  .word-group{padding-left:5px;padding-right:5px}
  .word-command{min-width:38px;height:48px}
  .word-icon{width:30px;height:30px}
  .format-select{width:104px!important}
  .font-select{width:98px!important}
  .size-select{width:65px!important}
  .editor{padding:12px!important;font-size:16px}
}
</style>@endpush
@push('head')<meta name="csrf-token" content="{{ csrf_token() }}">@endpush
@push('scripts')<script>
const editor=document.getElementById('editor'),source=document.getElementById('content-source'),form=document.getElementById('content-form'),mediaInput=document.getElementById('media-input'),galleryInput=document.getElementById('gallery-input'),videoInput=document.getElementById('video-input'),galleryBatchInput=document.getElementById('gallery-batch-input'),galleryStatus=document.getElementById('gallery-upload-status');


let savedEditorRange=null;
function saveEditorSelection(){const sel=window.getSelection();if(sel&&sel.rangeCount&&editor.contains(sel.anchorNode))savedEditorRange=sel.getRangeAt(0).cloneRange()}
function restoreEditorSelection(){if(!savedEditorRange)return;try{const sel=window.getSelection();sel.removeAllRanges();sel.addRange(savedEditorRange)}catch(e){}}
function sync(){if(source)source.value=editor.innerHTML;updateEditorMetrics()}
function exec(cmd,value=null){restoreEditorSelection();editor.focus();document.execCommand(cmd,false,value);sync();setTimeout(updateEditorToolbarState,0)}document.querySelectorAll('.word-ribbon [data-cmd]').forEach(b=>b.addEventListener('click',()=>{exec(b.dataset.cmd,b.dataset.value||null);b.closest('.tool-dropdown')?.classList.remove('open');b.closest('.tool-dropdown')?.querySelector('.tool-dropdown-toggle')?.setAttribute('aria-expanded','false')}));document.querySelectorAll('.tool-dropdown-toggle').forEach(btn=>btn.addEventListener('click',e=>{e.stopPropagation();const box=btn.closest('.tool-dropdown');document.querySelectorAll('.tool-dropdown.open').forEach(other=>{if(other!==box){other.classList.remove('open');other.querySelector('.tool-dropdown-toggle')?.setAttribute('aria-expanded','false')}});const open=box.classList.toggle('open');btn.setAttribute('aria-expanded',open?'true':'false')}));document.querySelectorAll('.tool-dropdown .tool-menu').forEach(menu=>menu.addEventListener('click',e=>e.stopPropagation()));document.addEventListener('click',()=>document.querySelectorAll('.tool-dropdown.open').forEach(box=>{box.classList.remove('open');box.querySelector('.tool-dropdown-toggle')?.setAttribute('aria-expanded','false')}));
document.getElementById('insert-link').onclick=()=>{const url=prompt('URL');if(url)exec('createLink',url)};document.getElementById('insert-button').onclick=()=>{const text=prompt('Button text','Learn More');if(!text)return;const url=prompt('Button URL','/');if(!url)return;const style=prompt('Button style: primary or outline','primary')==='outline'?'cta-outline':'';exec('insertHTML',`<a class="content-cta ${style}" href="${safeAttr(url)}">${safeText(text)}</a> <span>&nbsp;</span>`)};
document.getElementById('insert-columns').onclick=()=>{const count=Math.min(3,Math.max(2,parseInt(prompt('Number of columns (2 or 3)','2')||'2',10)));const cols=Array.from({length:count},(_,i)=>`<div class="content-column"><h3>Column ${i+1}</h3><p>Click here to edit this content.</p></div>`).join('');exec('insertHTML',`<div class="content-columns cols-${count}">${cols}</div><p></p>`)};
function safeText(v){return String(v).replace(/[&<>"]/g,ch=>({'&':'&amp;','<':'&lt;','>':'&gt;','"':'&quot;'}[ch]))}function safeAttr(v){return String(v).replace(/[<>"']/g,ch=>({'<':'%3C','>':'%3E','"':'%22',"'":'%27'}[ch]))}
function alignSelectedImage(cls){const sel=window.getSelection();let node=sel&&sel.anchorNode;while(node&&node!==editor&&node.nodeType===3)node=node.parentElement;while(node&&node!==editor&&node.tagName!=='IMG')node=node.parentElement;if(!node||node===editor||node.tagName!=='IMG'){alert('Select an image first.');return}node.classList.remove('align-left','align-center','align-right');node.classList.add(cls);sync()}
document.getElementById('image-align-left').onclick=()=>alignSelectedImage('align-left');document.getElementById('image-align-center').onclick=()=>alignSelectedImage('align-center');document.getElementById('image-align-right').onclick=()=>alignSelectedImage('align-right');
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
form.addEventListener('submit',sync);editor.addEventListener('input',sync);
function currentEditorBlock(){
  const sel=window.getSelection();let n=sel&&sel.rangeCount?sel.anchorNode:null;
  if(!n||!editor.contains(n))return null;if(n.nodeType===3)n=n.parentElement;
  while(n&&n!==editor){if(/^(P|DIV|H1|H2|H3|H4|H5|H6|BLOCKQUOTE|PRE|LI)$/.test(n.tagName))return n;n=n.parentElement}
  return null;
}
function updateEditorToolbarState(){
  const block=currentEditorBlock(), tag=block?.tagName?.toLowerCase()||'p';
  const format=document.getElementById('block-format');
  if(format){const allowed=['p','h1','h2','h3','h4','h5','h6','blockquote','pre'];format.value=allowed.includes(tag)?tag:'p'}
  const font=document.getElementById('font-name');
  try{
    const n=(document.queryCommandValue('fontName')||'').replaceAll('"','');
    if(font&&n){const opt=[...font.options].find(o=>n.toLowerCase().includes(o.value.toLowerCase()));if(opt)font.value=opt.value}
  }catch(e){}
  const size=document.getElementById('font-size');
  try{const v=String(document.queryCommandValue('fontSize')||'');if(size)size.value=['2','3','4','5','6'].includes(v)?v:''}catch(e){}
  const commands=['bold','italic','underline','strikeThrough','insertUnorderedList','insertOrderedList','justifyLeft','justifyCenter','justifyRight','justifyFull'];
  commands.forEach(cmd=>document.querySelectorAll('.word-ribbon [data-cmd="'+cmd+'"]').forEach(btn=>{
    let active=false;try{active=document.queryCommandState(cmd)}catch(e){}
    btn.classList.toggle('active',!!active);btn.setAttribute('aria-pressed',active?'true':'false')
  }));
  const px=block?getComputedStyle(block).fontSize:'—';
  let label=tag==='p'?'Paragraph':tag==='blockquote'?'Quote':tag==='pre'?'Code':tag.toUpperCase();
  const status=document.getElementById('editor-format-status');
  if(status)status.innerHTML='<strong>'+label+'</strong><span class="status-sep">•</span><span>'+px+'</span>';
}
function updateEditorMetrics(){
  const text=editor.innerText.replace(/\\s+/g,' ').trim();
  const words=text?text.split(/\\s+/).length:0,chars=text.length;
  const el=document.getElementById('editor-counts');if(el)el.textContent=words+' words • '+chars+' characters';
}
form.addEventListener('submit',()=>{sync()});
editor.addEventListener('input',()=>{saveEditorSelection();sync();updateEditorToolbarState()});
editor.addEventListener('keyup',()=>{saveEditorSelection();updateEditorToolbarState()});
editor.addEventListener('mouseup',()=>{saveEditorSelection();updateEditorToolbarState()});
editor.addEventListener('focus',()=>{saveEditorSelection();updateEditorToolbarState()});
document.addEventListener('selectionchange',()=>{const sel=window.getSelection();if(sel&&sel.rangeCount&&editor.contains(sel.anchorNode)){savedEditorRange=sel.getRangeAt(0).cloneRange();updateEditorToolbarState()}});
document.querySelectorAll('[data-editor-tab]').forEach(tab=>tab.addEventListener('click',()=>{
  const name=tab.dataset.editorTab;
  document.querySelectorAll('[data-editor-tab]').forEach(t=>{const active=t.dataset.editorTab===name;t.classList.toggle('active',active);t.setAttribute('aria-selected',active?'true':'false')});
  document.querySelectorAll('[data-editor-panel]').forEach(p=>p.classList.toggle('active',p.dataset.editorPanel===name));
}));
document.querySelectorAll('.word-ribbon button,.word-ribbon select,.word-ribbon input[type=color]').forEach(control=>{
  const preserve=()=>saveEditorSelection();
  control.addEventListener('pointerdown',preserve,true);
  control.addEventListener('touchstart',preserve,{capture:true,passive:true});
  control.addEventListener('mousedown',preserve,true);
  control.addEventListener('focus',preserve,true);
  if(control.tagName!=='SELECT'&&control.type!=='color')control.addEventListener('click',e=>e.preventDefault(),true);
});
function applyEditorCommand(cmd,value){
  if(!editor)return;
  restoreEditorSelection();
  editor.focus();
  try{document.execCommand('styleWithCSS',false,true)}catch(e){}
  let ok=false;
  try{ok=document.execCommand(cmd,false,value)}catch(e){}
  if(!ok&&cmd==='formatBlock'){
    try{ok=document.execCommand('formatBlock',false,'<'+String(value).replace(/[<>]/g,'')+'>')}catch(e){}
  }
  sync();
  updateEditorToolbarState();
  return ok;
}
document.getElementById('block-format').addEventListener('change',e=>{
  const value=e.target.value;
  if(!value)return;
  applyEditorCommand('formatBlock',value);
});
document.getElementById('font-name').addEventListener('change',e=>{
  if(!e.target.value)return;
  applyEditorCommand('fontName',e.target.value);
});
document.getElementById('font-size').addEventListener('change',e=>{
  if(!e.target.value)return;
  applyEditorCommand('fontSize',e.target.value);
});
document.getElementById('text-color').addEventListener('input',e=>{restoreEditorSelection();editor.focus();document.execCommand('foreColor',false,e.target.value);sync();updateEditorToolbarState()});
document.getElementById('highlight-color').addEventListener('input',e=>{restoreEditorSelection();editor.focus();document.execCommand('hiliteColor',false,e.target.value);sync();updateEditorToolbarState()});
document.getElementById('insert-image-url').onclick=()=>{const url=prompt('Image URL');if(url){restoreEditorSelection();editor.focus();document.execCommand('insertHTML',false,'<img src="'+safeAttr(url)+'" alt="Image" loading="lazy">');sync()}};
document.getElementById('insert-video-url').onclick=()=>{const url=prompt('Video URL');if(url){restoreEditorSelection();editor.focus();document.execCommand('insertHTML',false,'<video controls preload="metadata" src="'+safeAttr(url)+'"></video>');sync()}};
document.querySelectorAll('.word-ribbon [data-cmd]').forEach(b=>b.addEventListener('click',()=>{setTimeout(updateEditorToolbarState,0)}));
updateEditorMetrics();updateEditorToolbarState();




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

/* Word/Microsoft Word-style block behavior. */
(function(){
  const ed=editor;
  ed.addEventListener('keydown',function(e){
    if(e.key!=='Enter'||e.shiftKey)return;
    const block=currentEditorBlock();if(!block)return;
    const tag=block.tagName;
    if(/^H[1-6]$/.test(tag)||tag==='BLOCKQUOTE'||tag==='PRE'){
      setTimeout(function(){
        const next=currentEditorBlock();
        if(next&&next!==block&&(/^(H[1-6])$/.test(next.tagName)||next.tagName==='BLOCKQUOTE'||next.tagName==='PRE')&&!next.textContent.trim()){
          const p=document.createElement('p');p.innerHTML='<br>';next.replaceWith(p);
          const range=document.createRange();range.selectNodeContents(p);range.collapse(true);
          const sel=window.getSelection();sel.removeAllRanges();sel.addRange(range);savedEditorRange=range.cloneRange();sync();updateEditorToolbarState();
        }
      },0);
    }
  });
})();

/* Keep the CMS ribbon pinned to the viewport while its editor is in view.
   This avoids sticky being trapped by any parent scroll container in the portal layout. */
(function(){
  const shell=document.querySelector('.editor-shell');
  const ribbon=shell?.querySelector('.word-ribbon');
  if(!shell||!ribbon)return;

  let ticking=false;
  function updateRibbon(){
    ticking=false;
    const rect=shell.getBoundingClientRect();
    const height=ribbon.getBoundingClientRect().height;
    const active=rect.top<=0 && rect.bottom>0;

    if(active){
      const left=rect.left;
      const width=rect.width;
      shell.style.setProperty('--ff-ribbon-left',left+'px');
      shell.style.setProperty('--ff-ribbon-width',width+'px');
      shell.style.setProperty('--ff-ribbon-height',height+'px');
      shell.classList.add('ff-ribbon-active');
    }else{
      shell.classList.remove('ff-ribbon-active');
      shell.style.removeProperty('--ff-ribbon-left');
      shell.style.removeProperty('--ff-ribbon-width');
      shell.style.removeProperty('--ff-ribbon-height');
    }
  }

  function requestUpdate(){
    if(ticking)return;
    ticking=true;
    requestAnimationFrame(updateRibbon);
  }

  window.addEventListener('scroll',requestUpdate,{passive:true});
  window.addEventListener('resize',requestUpdate,{passive:true});
  requestUpdate();
})();
</script>@endpush


