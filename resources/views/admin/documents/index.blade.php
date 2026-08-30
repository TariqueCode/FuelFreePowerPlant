@extends('layouts.portal')

@section('title', 'File Manager')

@section('content')
<div class="hero file-hero">
    <div class="eyebrow">SECURE FILE MANAGER</div>
    <h1>My Files</h1>
    <p>Organize your private workspace like a modern desktop file manager. Create, move, copy, rename and delete folders and files.</p>
</div>

@if(session('success')) <div class="notice success">{{ session('success') }}</div> @endif
@if($errors->any()) <div class="notice error">{{ $errors->first() }}</div> @endif

<div class="storage-card">
    <div class="storage-top">
        <div><span>STORAGE</span><strong>{{ number_format($usedBytes / 1073741824, 2) }} GB used</strong></div>
        <div class="storage-right"><strong>{{ number_format($availableBytes / 1073741824, 2) }} GB free</strong><small>of {{ number_format($quotaBytes / 1073741824, 0) }} GB</small></div>
    </div>
    <div class="storage-track"><span style="width: {{ $usedPercent }}%"></span></div>
    <div class="storage-bottom"><span>{{ $usedPercent }}% used</span><span>{{ $documents->total() }} file(s) in this folder</span></div>
</div>

<div class="manager-toolbar">
    <form method="GET" action="{{ route('admin.documents') }}" class="search">
        <span>⌕</span><input name="q" value="{{ $search }}" placeholder="Search files and folders..." aria-label="Search files and folders">@if($folder)<input type="hidden" name="folder" value="{{ $folder->id }}">@endif
    </form>
    <div class="manager-actions">
        <button class="tool-btn" type="button" onclick="document.getElementById('folder-modal').showModal()">＋ Folder</button>
        <button class="tool-btn primary" type="button" onclick="document.getElementById('upload-modal').showModal()">↑ Upload</button>
        <button class="view-btn" type="button" onclick="toggleView()" aria-label="Toggle view">▦</button>
    </div>
</div>

@if($folder)
<div class="breadcrumb"><a href="{{ route('admin.documents') }}">My Files</a><span>›</span><strong>{{ $folder->name }}</strong></div>
@endif

<section class="file-section">
    <div class="section-head"><div><span class="section-kicker">FOLDERS</span><h2>This folder</h2></div><span>{{ $folders->count() }} folder(s)</span></div>
    <div class="folder-grid" id="folder-grid">
        @forelse($folders as $item)
            <div class="folder-card">
                <a class="folder-main" href="{{ route('admin.documents', ['folder' => $item->id]) }}"><div class="folder-icon">▰</div><div class="folder-info"><b>{{ $item->name }}</b><small>{{ $item->children_count }} folders · {{ $item->documents_count }} files</small></div></a>
                <details class="menu"><summary aria-label="Folder actions">⋮</summary><div class="menu-pop">
                    <button type="button" onclick="openDialog('rename-folder-{{ $item->id }}')">Rename</button>
                    <button type="button" onclick="openDialog('move-folder-{{ $item->id }}')">Move</button>
                    <button type="button" onclick="openDialog('copy-folder-{{ $item->id }}')">Copy</button>
                    <button class="danger" type="button" onclick="openDialog('delete-folder-{{ $item->id }}')">Delete</button>
                </div></details>
            </div>
        @empty <div class="empty">No folders here yet.</div> @endforelse
    </div>
</section>

<section class="file-section">
    <div class="section-head"><div><span class="section-kicker">FILES</span><h2>Documents & files</h2></div><span>{{ $documents->total() }} item(s)</span></div>
    <div class="file-list" id="file-list">
        @forelse($documents as $document)
            <article class="file-row">
                <div class="file-type">{{ strtoupper($document->extension ?: 'FILE') }}</div>
                <div class="file-meta"><b title="{{ $document->original_name }}">{{ $document->original_name }}</b><small>{{ $document->size_label }} · {{ $document->mime_type ?: 'Unknown type' }} · {{ $document->created_at->format('M d, Y H:i') }}</small></div>
                <div class="file-actions"><a class="download-link" href="{{ route('admin.documents.download', $document) }}"><i class="fa-solid fa-download"></i> Download</a>@if($document->share_enabled && $document->share_token)<button class="share-link active" type="button" onclick="openDialog('share-file-{{ $document->id }}')"><i class="fa-solid fa-link"></i> Shared</button>@else<button class="share-link" type="button" onclick="openDialog('share-file-{{ $document->id }}')"><i class="fa-solid fa-share-nodes"></i> Share</button>@endif<details class="menu"><summary>⋮</summary><div class="menu-pop right"><button type="button" onclick="openDialog('rename-file-{{ $document->id }}')">Rename</button><button type="button" onclick="openDialog('move-file-{{ $document->id }}')">Move</button><button type="button" onclick="openDialog('copy-file-{{ $document->id }}')">Copy</button><button class="danger" type="button" onclick="openDialog('delete-file-{{ $document->id }}')">Delete</button></div></details></div>
            </article>
        @empty <div class="empty">No files in this folder. Upload your first file.</div> @endforelse
    </div>
    @if($documents->hasPages())<div class="pagination">{{ $documents->links() }}</div>@endif
</section>

<dialog id="folder-modal"><form method="POST" action="{{ route('admin.documents.folders.store') }}">@csrf<h2>New folder</h2><p>Create a folder inside the current location.</p><input name="name" placeholder="Folder name" maxlength="150" required>@if($folder)<input type="hidden" name="parent_id" value="{{ $folder->id }}">@endif<div class="modal-actions"><button type="button" onclick="this.closest('dialog').close()">Cancel</button><button class="primary" type="submit">Create folder</button></div></form></dialog>
<dialog id="upload-modal"><form method="POST" action="{{ route('admin.documents.store') }}" enctype="multipart/form-data">@csrf<h2>Upload files</h2><p>Select a file up to 50 MB.</p><input id="file-upload-input" type="file" name="file" accept=".pdf,.doc,.docx,.xls,.xlsx,.csv,.txt,.zip,.jpg,.jpeg,.png,.webp,.gif,.mp4,.webm,.mov" required>@if($folder)<input type="hidden" name="folder_id" value="{{ $folder->id }}">@endif<div class="modal-note">Maximum file size: 50 MB. Upload sessions expire after 2 hours. Available storage and server limits still apply.</div><div class="modal-actions"><button type="button" onclick="this.closest('dialog').close()">Cancel</button><button class="primary" type="submit">Upload securely</button></div></form></dialog>

@foreach($folders as $item)
<dialog id="rename-folder-{{ $item->id }}"><form method="POST" action="{{ route('admin.documents.folders.rename', $item) }}">@csrf @method('PATCH')<h2>Rename folder</h2><input name="name" value="{{ $item->name }}" maxlength="150" required><div class="modal-actions"><button type="button" onclick="this.closest('dialog').close()">Cancel</button><button class="primary">Save</button></div></form></dialog>
<dialog id="move-folder-{{ $item->id }}"><form method="POST" action="{{ route('admin.documents.folders.move', $item) }}">@csrf<h2>Move folder</h2><select name="parent_id"><option value="">My Files (root)</option>@foreach($allFolders as $target) @if($target->id !== $item->id)<option value="{{ $target->id }}">{{ $target->name }}</option>@endif @endforeach</select><div class="modal-note">Invalid destinations such as this folder or its descendants are rejected automatically.</div><div class="modal-actions"><button type="button" onclick="this.closest('dialog').close()">Cancel</button><button class="primary">Move</button></div></form></dialog>
<dialog id="copy-folder-{{ $item->id }}"><form method="POST" action="{{ route('admin.documents.folders.copy', $item) }}">@csrf<h2>Copy folder</h2><select name="parent_id"><option value="">My Files (root)</option>@foreach($allFolders as $target)<option value="{{ $target->id }}">{{ $target->name }}</option>@endforeach</select><div class="modal-note">The complete folder tree and its files will be duplicated.</div><div class="modal-actions"><button type="button" onclick="this.closest('dialog').close()">Cancel</button><button class="primary">Copy folder</button></div></form></dialog>
<dialog id="delete-folder-{{ $item->id }}"><form method="POST" action="{{ route('admin.documents.folders.destroy', $item) }}">@csrf @method('DELETE')<h2>Delete folder?</h2><p class="warning">This permanently deletes the folder, all nested folders and all files inside it.</p><div class="modal-actions"><button type="button" onclick="this.closest('dialog').close()">Cancel</button><button class="danger-btn">Delete permanently</button></div></form></dialog>
@endforeach

@foreach($documents as $document)
<dialog id="rename-file-{{ $document->id }}"><form method="POST" action="{{ route('admin.documents.rename', $document) }}">@csrf @method('PATCH')<h2>Rename file</h2><input name="name" value="{{ $document->original_name }}" maxlength="255" required><div class="modal-actions"><button type="button" onclick="this.closest('dialog').close()">Cancel</button><button class="primary">Save</button></div></form></dialog>
<dialog id="move-file-{{ $document->id }}"><form method="POST" action="{{ route('admin.documents.move', $document) }}">@csrf<h2>Move file</h2><select name="folder_id"><option value="">My Files (root)</option>@foreach($allFolders as $target)<option value="{{ $target->id }}" @selected($target->id === $document->folder_id)>{{ $target->name }}</option>@endforeach</select><div class="modal-actions"><button type="button" onclick="this.closest('dialog').close()">Cancel</button><button class="primary">Move</button></div></form></dialog>
<dialog id="copy-file-{{ $document->id }}"><form method="POST" action="{{ route('admin.documents.copy', $document) }}">@csrf<h2>Copy file</h2><select name="folder_id"><option value="">My Files (root)</option>@foreach($allFolders as $target)<option value="{{ $target->id }}">{{ $target->name }}</option>@endforeach</select><div class="modal-actions"><button type="button" onclick="this.closest('dialog').close()">Cancel</button><button class="primary">Make a copy</button></div></form></dialog>
<dialog id="delete-file-{{ $document->id }}"><form method="POST" action="{{ route('admin.documents.destroy', $document) }}">@csrf @method('DELETE')<h2>Delete file?</h2><p class="warning">This permanently removes the stored file.</p><div class="modal-actions"><button type="button" onclick="this.closest('dialog').close()">Cancel</button><button class="danger-btn">Delete permanently</button></div></form></dialog><dialog id="share-file-{{ $document->id }}"><div class="share-dialog"><div class="share-icon"><i class="fa-solid fa-link"></i></div><div><h2>Share document</h2><p>Anyone with this link can download this file. You can revoke the link anytime.</p></div>@if($document->share_enabled && $document->share_token)<label class="share-label">Download link</label><div class="share-url"><input id="share-url-{{ $document->id }}" readonly value="{{ $document->share_url }}"><button type="button" onclick="copyShareLink('share-url-{{ $document->id }}',this)" aria-label="Copy link"><i class="fa-solid fa-copy"></i></button></div><div class="share-status"><i class="fa-solid fa-circle-check"></i> Link is active</div><div class="modal-actions"><button type="button" onclick="this.closest('dialog').close()">Done</button><form method="POST" action="{{ route('admin.documents.unshare', $document) }}">@csrf @method('DELETE')<button class="danger-btn" type="submit">Revoke link</button></form></div>@else<form method="POST" action="{{ route('admin.documents.share', $document) }}">@csrf<div class="modal-note">Create a private-looking, non-guessable download URL for sharing this document.</div><div class="modal-actions"><button type="button" onclick="this.closest('dialog').close()">Cancel</button><button class="primary" type="submit"><i class="fa-solid fa-link"></i> Create link</button></div></form>@endif</div></dialog>
@endforeach
@endsection

@push('scripts')
<script>
document.getElementById('file-upload-input')?.addEventListener('change', function () {
    const max = 50 * 1024 * 1024;
    const file = this.files?.[0];
    if (file && file.size > max) {
        this.value = '';
        alert('The selected file is larger than the 50 MB limit.');
    }
});
</script>
@endpush

@push('head')
<style>
.notice{padding:13px 16px;border-radius:12px;margin-bottom:16px;font-size:13px}.success{background:rgba(55,190,137,.1);border:1px solid rgba(55,190,137,.2);color:#9ce6c9}.error{background:rgba(220,70,70,.1);border:1px solid rgba(220,70,70,.2);color:#ffb7b7}.storage-card{border:1px solid var(--line);border-radius:17px;padding:18px 20px;margin-bottom:18px;background:linear-gradient(135deg,rgba(67,194,229,.06),rgba(255,255,255,.018))}.storage-top,.storage-bottom{display:flex;justify-content:space-between;gap:12px;align-items:flex-end}.storage-top span,.section-kicker{display:block;color:var(--accent);font-size:9px;letter-spacing:.18em}.storage-top strong{display:block;font-size:18px}.storage-right{text-align:right}.storage-right small{display:block;color:var(--muted);font-size:10px;margin-top:3px}.storage-track{height:9px;margin:15px 0 8px;border-radius:99px;overflow:hidden;background:rgba(255,255,255,.08)}.storage-track span{display:block;height:100%;border-radius:99px;background:linear-gradient(90deg,#229fd0,#54d4ee)}.storage-bottom{color:var(--muted);font-size:10px}.manager-toolbar{display:flex;gap:12px;justify-content:space-between;align-items:center;margin-bottom:18px}.search{display:flex;align-items:center;gap:8px;flex:1;max-width:680px;padding:0 13px;border:1px solid var(--line);border-radius:12px;background:rgba(255,255,255,.035)}.search span{font-size:20px;color:var(--muted)}.search input{width:100%;border:0!important;background:transparent!important;padding:13px 2px!important;outline:none}.manager-actions{display:flex;gap:8px}.tool-btn,.view-btn{border:1px solid var(--line);border-radius:11px;padding:11px 14px;background:rgba(255,255,255,.035);color:var(--text);cursor:pointer}.tool-btn.primary,.primary{background:#31afd2!important;color:#fff!important;border-color:transparent!important}.view-btn{font-size:18px;padding:7px 12px}.breadcrumb{display:flex;gap:9px;align-items:center;margin:0 0 18px;font-size:12px;color:var(--muted)}.breadcrumb a{color:var(--accent);text-decoration:none}.file-section{margin-top:20px}.section-head{display:flex;align-items:end;justify-content:space-between;margin-bottom:10px}.section-head h2{margin:4px 0 0;font-size:17px}.section-head>span{color:var(--muted);font-size:11px}.folder-grid{display:grid;grid-template-columns:repeat(auto-fill,minmax(210px,1fr));gap:10px}.folder-card{position:relative;display:flex;align-items:center;min-width:0;padding:13px;border:1px solid var(--line);border-radius:15px;background:rgba(255,255,255,.025)}.folder-main{display:flex;align-items:center;gap:12px;min-width:0;flex:1;color:var(--text);text-decoration:none}.folder-icon{width:42px;height:42px;display:grid;place-items:center;border-radius:12px;background:rgba(67,194,229,.08);color:var(--accent);font-size:22px}.folder-info{min-width:0}.folder-info b{display:block;white-space:nowrap;overflow:hidden;text-overflow:ellipsis}.folder-info small{display:block;margin-top:4px;color:var(--muted);font-size:10px}.file-list{border:1px solid var(--line);border-radius:17px;overflow:visible;background:rgba(255,255,255,.018)}.file-row{display:grid;grid-template-columns:48px minmax(0,1fr) auto;gap:13px;align-items:center;padding:13px 17px;border-bottom:1px solid rgba(104,204,235,.07)}.file-row:last-child{border-bottom:0}.file-type{width:42px;height:42px;display:grid;place-items:center;border-radius:11px;background:rgba(67,194,229,.08);color:var(--accent);font-size:8px;font-weight:800}.file-meta{min-width:0}.file-meta b{display:block;white-space:nowrap;overflow:hidden;text-overflow:ellipsis}.file-meta small{display:block;color:var(--muted);margin-top:4px;font-size:10px;white-space:nowrap;overflow:hidden;text-overflow:ellipsis}.file-actions{display:flex;align-items:center;gap:8px}.file-actions>a,.share-link{color:var(--accent);text-decoration:none;font-size:11px}.share-link{border:1px solid rgba(67,194,229,.16);background:rgba(67,194,229,.05);border-radius:9px;padding:7px 9px;cursor:pointer}.share-link.active{color:#8ee6c5;border-color:rgba(55,190,137,.18);background:rgba(55,190,137,.06)}.share-dialog{display:grid;gap:12px}.share-dialog>div:first-child{display:flex;gap:12px;align-items:flex-start}.share-icon{width:42px;height:42px;display:grid;place-items:center;border-radius:12px;background:rgba(67,194,229,.1);color:var(--accent);font-size:17px}.share-label{font-size:10px;color:var(--muted);letter-spacing:.08em;text-transform:uppercase}.share-url{display:flex;gap:7px}.share-url input{min-width:0;flex:1}.share-url button{width:44px;border:1px solid var(--line);border-radius:11px;background:rgba(67,194,229,.08);color:var(--accent);cursor:pointer}.share-status{color:#8ee6c5;font-size:11px}.share-status i{margin-right:5px}.share-dialog .modal-actions form{display:contents}.menu{position:relative}.menu summary{list-style:none;cursor:pointer;width:28px;height:28px;display:grid;place-items:center;border-radius:8px;color:var(--muted);font-size:18px}.menu summary::-webkit-details-marker{display:none}.menu[open] summary{background:rgba(67,194,229,.1);color:var(--text)}.menu-pop{position:absolute;z-index:30;top:32px;right:0;width:155px;padding:6px;border:1px solid var(--line);border-radius:12px;background:#08202e;box-shadow:0 20px 50px rgba(0,0,0,.4)}.menu-pop button{display:block;width:100%;padding:9px 10px;border:0;border-radius:8px;text-align:left;background:transparent;color:var(--text);font-size:11px;cursor:pointer}.menu-pop button:hover{background:rgba(67,194,229,.08)}.menu-pop .danger{color:#ff9e9e}.empty{padding:30px;text-align:center;color:var(--muted);font-size:12px}.pagination{padding:14px}.pagination nav{display:flex;justify-content:center}dialog{border:1px solid var(--line);border-radius:18px;background:#071d2a;color:var(--text);padding:24px;min-width:min(430px,calc(100vw - 30px));box-shadow:0 30px 90px #000}dialog::backdrop{background:rgba(0,0,0,.7);backdrop-filter:blur(3px)}dialog form{display:grid;gap:12px}dialog h2{margin:0;font-size:20px}dialog p{margin:0;color:var(--muted);font-size:12px;line-height:1.6}dialog input,dialog select{width:100%;box-sizing:border-box;padding:13px;border-radius:11px;border:1px solid var(--line);background:#071c29;color:#e9f7fb;outline:none}.modal-note{color:var(--muted);font-size:11px;line-height:1.6}.warning{color:#ffb0b0!important}.modal-actions{display:flex;justify-content:flex-end;gap:8px;margin-top:6px}.modal-actions button{padding:10px 14px;border:1px solid var(--line);border-radius:10px;background:transparent;color:var(--text);cursor:pointer}.danger-btn{background:#a63c4a!important;border-color:transparent!important;color:#fff!important}
@media(max-width:700px){.manager-toolbar{display:grid}.search{max-width:none}.manager-actions{display:grid;grid-template-columns:1fr 1fr 48px}.tool-btn{padding:10px 8px}.file-row{grid-template-columns:42px minmax(0,1fr);padding:12px}.file-actions{grid-column:2;justify-content:flex-end}.folder-grid{grid-template-columns:1fr 1fr}}
@media(max-width:440px){.file-actions{flex-wrap:wrap}.share-link,.download-link{font-size:10px}.folder-grid{grid-template-columns:1fr}.storage-top{align-items:flex-start;flex-direction:column}.storage-right{text-align:left}.manager-actions{grid-template-columns:1fr 1fr}.view-btn{display:none}.file-actions{justify-content:flex-start}.file-actions>a{font-size:10px}}
</style>
@endpush

@push('scripts')
<script>
function openDialog(id){const d=document.getElementById(id);if(d)d.showModal();}
function toggleView(){document.getElementById('folder-grid').classList.toggle('compact');document.getElementById('file-list').classList.toggle('compact');}
function copyShareLink(id,button){const input=document.getElementById(id);navigator.clipboard.writeText(input.value).then(()=>{const old=button.innerHTML;button.innerHTML='<i class="fa-solid fa-check"></i>';setTimeout(()=>button.innerHTML=old,1400);}).catch(()=>{input.select();document.execCommand('copy');});}
document.addEventListener('click',e=>{document.querySelectorAll('details.menu[open]').forEach(d=>{if(!d.contains(e.target))d.removeAttribute('open')})});
</script>
@endpush
