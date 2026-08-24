@extends('layouts.portal')

@section('title', 'Secure Document Vault')

@section('content')
<div class="hero">
    <div class="eyebrow">SECURE DOCUMENT VAULT</div>
    <h1>Documents</h1>
    <p>Keep operational and business documents in a private, permission-controlled workspace.</p>
</div>

@if(session('success'))
    <div class="notice success">{{ session('success') }}</div>
@endif
@if($errors->any())
    <div class="notice error">{{ $errors->first() }}</div>
@endif

<div class="toolbar">
    <form method="GET" action="{{ route('admin.documents') }}" class="search">
        <input name="q" value="{{ $search }}" placeholder="Search documents..." aria-label="Search documents">
        @if($folder)<input type="hidden" name="folder" value="{{ $folder->id }}">@endif
        <button type="submit">Search</button>
    </form>
    <div class="actions">
        <button type="button" onclick="document.getElementById('folder-modal').showModal()">+ New folder</button>
        <button type="button" onclick="document.getElementById('upload-modal').showModal()">Upload document</button>
    </div>
</div>

@if($folder)
    <div class="crumb"><a href="{{ route('admin.documents') }}">Vault</a><span>/</span><strong>{{ $folder->name }}</strong></div>
@endif

<div class="folder-grid">
    @forelse($folders as $item)
        <a class="folder" href="{{ route('admin.documents', ['folder' => $item->id]) }}"><span>▰</span><div><b>{{ $item->name }}</b><small>Folder</small></div></a>
    @empty
        <div class="empty-mini">No folders here yet.</div>
    @endforelse
</div>

<div class="table-card">
    <div class="table-head"><h2>Files</h2><span>{{ $documents->total() }} document(s)</span></div>
    <div class="files">
        @forelse($documents as $document)
            <div class="file-row">
                <div class="file-icon">{{ strtoupper($document->extension ?: 'FILE') }}</div>
                <div class="file-meta"><b>{{ $document->original_name }}</b><small>{{ $document->size_label }} · {{ $document->mime_type ?: 'Unknown type' }} · {{ $document->created_at->format('M d, Y H:i') }}</small></div>
                <div class="file-actions">
                    <a href="{{ route('admin.documents.download', $document) }}">Download</a>
                    <form method="POST" action="{{ route('admin.documents.destroy', $document) }}" onsubmit="return confirm('Delete this document permanently?')">@csrf @method('DELETE')<button type="submit">Delete</button></form>
                </div>
            </div>
        @empty
            <div class="empty">No documents in this folder. Upload your first secure file.</div>
        @endforelse
    </div>
    @if($documents->hasPages())<div class="pagination">{{ $documents->links() }}</div>@endif
</div>

<dialog id="folder-modal"><form method="POST" action="{{ route('admin.documents.folders.store') }}">@csrf<h2>Create folder</h2><input name="name" placeholder="Folder name" maxlength="150" required>@if($folder)<input type="hidden" name="parent_id" value="{{ $folder->id }}">@endif<div class="modal-actions"><button type="button" onclick="this.closest('dialog').close()">Cancel</button><button type="submit">Create folder</button></div></form></dialog>
<dialog id="upload-modal"><form method="POST" action="{{ route('admin.documents.store') }}" enctype="multipart/form-data">@csrf<h2>Upload document</h2><input type="file" name="file" accept=".pdf,.jpg,.jpeg,.png,.txt,.doc,.docx,.xls,.xlsx,.zip" required>@if($folder)<input type="hidden" name="folder_id" value="{{ $folder->id }}">@endif<p class="modal-note">Maximum file size: 50 MB. Files are stored outside the public web directory.</p><div class="modal-actions"><button type="button" onclick="this.closest('dialog').close()">Cancel</button><button type="submit">Upload securely</button></div></form></dialog>
@endsection

@push('head')
<style>
.notice{padding:13px 16px;border-radius:12px;margin-bottom:16px;font-size:13px}.success{background:rgba(55,190,137,.1);border:1px solid rgba(55,190,137,.2);color:#9ce6c9}.error{background:rgba(220,70,70,.1);border:1px solid rgba(220,70,70,.2);color:#ffb7b7}.toolbar{display:flex;gap:12px;justify-content:space-between;align-items:center;margin-bottom:18px}.search{display:flex;gap:8px;flex:1;max-width:620px}.search input,.search button,.actions button,dialog input{border:1px solid var(--line);background:rgba(255,255,255,.035);color:var(--text);border-radius:11px;padding:12px 14px}.search input{min-width:0;flex:1}.actions{display:flex;gap:8px}.actions button,.search button,.modal-actions button:last-child{background:rgba(67,194,229,.12);cursor:pointer}.crumb{display:flex;gap:9px;align-items:center;margin:8px 0 16px;font-size:12px;color:var(--muted)}.crumb a{color:var(--accent);text-decoration:none}.folder-grid{display:grid;grid-template-columns:repeat(auto-fill,minmax(190px,1fr));gap:10px;margin-bottom:18px}.folder{display:flex;gap:12px;align-items:center;padding:15px;border:1px solid var(--line);border-radius:14px;background:rgba(255,255,255,.025);color:var(--text);text-decoration:none}.folder span{color:var(--accent);font-size:22px}.folder small,.file-meta small{display:block;color:var(--muted);margin-top:4px;font-size:11px}.table-card{border:1px solid var(--line);border-radius:17px;overflow:hidden;background:rgba(255,255,255,.018)}.table-head{display:flex;justify-content:space-between;align-items:center;padding:18px 20px;border-bottom:1px solid var(--line)}.table-head h2{margin:0;font-size:16px}.table-head span{color:var(--muted);font-size:11px}.file-row{display:grid;grid-template-columns:48px minmax(0,1fr) auto;gap:13px;align-items:center;padding:14px 20px;border-bottom:1px solid rgba(104,204,235,.07)}.file-icon{width:42px;height:42px;border-radius:11px;display:grid;place-items:center;background:rgba(67,194,229,.08);color:var(--accent);font-size:8px;font-weight:700;overflow:hidden}.file-meta{min-width:0}.file-meta b{display:block;white-space:nowrap;overflow:hidden;text-overflow:ellipsis}.file-actions{display:flex;gap:10px;align-items:center}.file-actions a,.file-actions button{color:var(--accent);background:none;border:0;text-decoration:none;font-size:11px;cursor:pointer}.empty,.empty-mini{padding:28px;text-align:center;color:var(--muted);font-size:13px}.pagination{padding:14px 20px}dialog{border:1px solid var(--line);border-radius:18px;background:#071d2a;color:var(--text);padding:24px;min-width:min(420px,calc(100vw - 32px));box-shadow:0 30px 90px #000}dialog::backdrop{background:rgba(0,0,0,.65)}dialog form{display:grid;gap:12px}dialog h2{margin:0 0 6px}.modal-actions{display:flex;justify-content:flex-end;gap:8px}.modal-actions button{padding:10px 13px;border:1px solid var(--line);border-radius:10px;background:transparent;color:var(--text)}.modal-note{color:var(--muted);font-size:11px;line-height:1.6}@media(max-width:700px){.toolbar{display:grid}.search{max-width:none}.actions{display:grid;grid-template-columns:1fr 1fr}.file-row{grid-template-columns:42px minmax(0,1fr)}.file-actions{grid-column:2}.folder-grid{grid-template-columns:1fr 1fr}}@media(max-width:420px){.folder-grid{grid-template-columns:1fr}.actions{grid-template-columns:1fr}}
</style>
@endpush
