<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Document;
use App\Models\DocumentFolder;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\View\View;

class DocumentController extends Controller
{
    public function index(Request $request): View
    {
        $user = $request->user(); $folderId = $request->integer('folder'); $search = trim((string) $request->input('q'));
        $folder = $folderId ? DocumentFolder::where('id', $folderId)->where('user_id', $user->id)->firstOrFail() : null;
        $folders = DocumentFolder::query()->where('user_id', $user->id)->where('parent_id', $folder?->id)->withCount(['children', 'documents'])->orderBy('name')->get();
        $allFolders = DocumentFolder::query()->where('user_id', $user->id)->orderBy('name')->get(['id', 'parent_id', 'name']);
        $documents = Document::query()->where('user_id', $user->id)->where('folder_id', $folder?->id)->when($search !== '', fn ($query) => $query->where('original_name', 'like', "%{$search}%"))->latest()->paginate(20)->withQueryString();
        $storageFiles = Storage::disk('local')->allFiles("private/{$user->id}"); $usedBytes = collect($storageFiles)->sum(fn ($file) => (int) Storage::disk('local')->size($file));
        $quotaBytes = (int) config('fuelfree.storage.quota_bytes', 50 * 1024 * 1024 * 1024); $availableBytes = max(0, $quotaBytes - $usedBytes); $usedPercent = $quotaBytes > 0 ? min(100, round(($usedBytes / $quotaBytes) * 100, 1)) : 0;
        return view('admin.documents.index', compact('folder', 'folders', 'allFolders', 'documents', 'search', 'usedBytes', 'availableBytes', 'quotaBytes', 'usedPercent'));
    }

    public function storeFolder(Request $request): RedirectResponse
    {
        $data = $request->validate(['name' => ['required', 'string', 'max:150', 'regex:/^[^\\\\\/]+$/'], 'parent_id' => ['nullable', 'integer', 'exists:document_folders,id']]); $parentId = $data['parent_id'] ?? null;
        if ($parentId && ! DocumentFolder::whereKey($parentId)->where('user_id', $request->user()->id)->exists()) abort(403);
        DocumentFolder::create(['user_id' => $request->user()->id, 'parent_id' => $parentId, 'name' => trim($data['name'])]); return back()->with('success', 'Folder created successfully.');
    }
    public function renameFolder(Request $request, DocumentFolder $folder): RedirectResponse { $this->ownFolder($request, $folder); $data = $request->validate(['name' => ['required', 'string', 'max:150', 'regex:/^[^\\\\\/]+$/']]); $folder->update(['name' => trim($data['name'])]); return back()->with('success', 'Folder renamed successfully.'); }
    public function moveFolder(Request $request, DocumentFolder $folder): RedirectResponse
    {
        $this->ownFolder($request, $folder); $data = $request->validate(['parent_id' => ['nullable', 'integer', 'exists:document_folders,id']]); $parentId = $data['parent_id'] ?? null;
        if ($parentId) { $target = DocumentFolder::whereKey($parentId)->where('user_id', $request->user()->id)->firstOrFail(); abort_if($target->id === $folder->id || $this->isDescendant($target, $folder), 422, 'A folder cannot be moved inside itself or one of its children.'); }
        $folder->update(['parent_id' => $parentId]); return back()->with('success', 'Folder moved successfully.');
    }
    public function copyFolder(Request $request, DocumentFolder $folder): RedirectResponse
    {
        $this->ownFolder($request, $folder); $data = $request->validate(['parent_id' => ['nullable', 'integer', 'exists:document_folders,id']]); $parentId = $data['parent_id'] ?? null;
        if ($parentId) { $target = DocumentFolder::whereKey($parentId)->where('user_id', $request->user()->id)->firstOrFail(); abort_if($target->id === $folder->id || $this->isDescendant($target, $folder), 422, 'A folder cannot be copied inside itself or one of its children.'); }
        DB::transaction(fn () => $this->copyFolderTree($folder, $request->user()->id, $parentId)); return back()->with('success', 'Folder copied successfully.');
    }
    public function destroyFolder(Request $request, DocumentFolder $folder): RedirectResponse { $this->ownFolder($request, $folder); $this->deleteFolderTree($folder); return redirect()->route('admin.documents')->with('success', 'Folder and all its contents were permanently deleted.'); }
    public function store(Request $request): RedirectResponse
    {
        $data = $request->validate(['file' => ['required', 'file', 'max:51200'], 'folder_id' => ['nullable', 'integer', 'exists:document_folders,id']]); $user = $request->user();
        $allowed = ['pdf','doc','docx','xls','xlsx','csv','txt','zip','jpg','jpeg','png','webp','gif','mp4','webm','mov'];
        abort_unless(in_array(strtolower($data['file']->getClientOriginalExtension()), $allowed, true), 422, 'This file type is not supported.'); $folderId = $data['folder_id'] ?? null;
        if ($folderId && ! DocumentFolder::whereKey($folderId)->where('user_id', $user->id)->exists()) abort(403); $file = $data['file']; $storedName = $file->hashName(); $path = $file->storeAs("private/{$user->id}", $storedName, 'local');
        Document::create(['user_id' => $user->id, 'folder_id' => $folderId, 'original_name' => $file->getClientOriginalName(), 'stored_name' => $storedName, 'disk' => 'local', 'path' => $path, 'mime_type' => $file->getMimeType(), 'size' => $file->getSize(), 'extension' => strtolower($file->getClientOriginalExtension())]); return back()->with('success', 'File uploaded securely.');
    }

    public function chunkUpload(Request $request)
    {
        $user = $request->user();
        $uploadId = (string) $request->header('X-Upload-Id');
        $uploadsDir = "private/{$user->id}/.uploads";
        Storage::disk('local')->makeDirectory($uploadsDir);

        if ($uploadId === '') {
            $data = $request->validate([
                'filename' => ['required', 'string', 'max:255'],
                'size' => ['required', 'integer', 'min:1', 'max:52428800'],
                'folder_id' => ['nullable', 'integer', 'exists:document_folders,id'],
            ]);
            if (! empty($data['folder_id']) && ! DocumentFolder::whereKey($data['folder_id'])->where('user_id', $user->id)->exists()) abort(403);
            $usedBytes = collect(Storage::disk('local')->allFiles("private/{$user->id}"))->sum(fn ($file) => (int) Storage::disk('local')->size($file));
            $quotaBytes = (int) config('fuelfree.storage.quota_bytes', 50 * 1024 * 1024 * 1024);
            abort_if($usedBytes + (int) $data['size'] > $quotaBytes, 422, 'There is not enough storage space for this file.');
            $uploadId = (string) str()->uuid();
            $metaPath = "{$uploadsDir}/{$uploadId}.json";
            $partPath = "{$uploadsDir}/{$uploadId}.part";
            Storage::disk('local')->put($metaPath, json_encode([
                'user_id' => $user->id,
                'folder_id' => $data['folder_id'] ?? null,
                'filename' => basename($data['filename']),
                'size' => (int) $data['size'],
                'mime_type' => (string) $request->input('mime_type', 'application/octet-stream'),
                'created_at' => now()->toIso8601String(),
                'chunk_size' => 524288,
                'part_path' => $partPath,
            ], JSON_THROW_ON_ERROR));
            return response()->json(['upload_id' => $uploadId, 'chunk_size' => 524288]);
        }

        $metaPath = "{$uploadsDir}/{$uploadId}.json";
        $partPath = "{$uploadsDir}/{$uploadId}.part";
        abort_unless(Storage::disk('local')->exists($metaPath), 404, 'Upload session not found.');
        $meta = json_decode(Storage::disk('local')->get($metaPath), true, 512, JSON_THROW_ON_ERROR);
        abort_unless((int) ($meta['user_id'] ?? 0) === (int) $user->id, 403);
        $createdAt = (string) ($meta['created_at'] ?? '');
        if ($createdAt !== '') { try { abort_if(now()->diffInMinutes(\Carbon\Carbon::parse($createdAt)) > 120, 410, 'Upload session expired. Please start again.'); } catch (\Throwable $e) { abort(410, 'Upload session expired. Please start again.'); } }

        if ($request->boolean('finalize')) {
            $currentSize = Storage::disk('local')->exists($partPath) ? Storage::disk('local')->size($partPath) : 0;
            abort_unless($currentSize === (int) $meta['size'], 422, 'The upload is incomplete.');
            abort_if((int) $meta['size'] > 52428800, 422, 'The file exceeds the 50 MB limit.');
            $extension = strtolower(pathinfo($meta['filename'], PATHINFO_EXTENSION));
            $allowed = ['pdf','doc','docx','xls','xlsx','csv','txt','zip','jpg','jpeg','png','webp','gif','mp4','webm','mov'];
            abort_if($extension === '' || !in_array($extension, $allowed, true), 422, 'This file type is not supported.');
            $storedName = (string) str()->uuid().($extension ? '.'.$extension : '');
            $finalPath = "private/{$user->id}/{$storedName}";
            Storage::disk('local')->move($partPath, $finalPath);
            $mime = 'application/octet-stream';
            $absolute = Storage::disk('local')->path($finalPath);
            if (function_exists('finfo_open')) { $finfo = finfo_open(FILEINFO_MIME_TYPE); $detected = finfo_file($finfo, $absolute); finfo_close($finfo); if ($detected) $mime = $detected; }
            Document::create(['user_id' => $user->id, 'folder_id' => $meta['folder_id'], 'original_name' => $meta['filename'], 'stored_name' => $storedName, 'disk' => 'local', 'path' => $finalPath, 'mime_type' => $mime, 'size' => (int) $meta['size'], 'extension' => $extension]);
            Storage::disk('local')->delete($metaPath);
            return response()->json(['ok' => true, 'message' => 'File uploaded securely.']);
        }

        $index = (int) $request->header('X-Chunk-Index', '-1');
        $offset = (int) $request->header('X-Chunk-Offset', '-1');
        $length = (int) $request->header('Content-Length', '0');
        $chunkSize = (int) $meta['chunk_size'];
        abort_if($index < 0 || $offset < 0 || $length < 1 || $length > $chunkSize, 422, 'Invalid upload chunk.');
        abort_if($offset + $length > (int) $meta['size'], 422, 'Upload chunk exceeds the declared file size.');

        $absolute = Storage::disk('local')->path($partPath);
        $handle = fopen($absolute, 'c+b');
        abort_unless($handle !== false, 500, 'Unable to open upload buffer.');
        if (! flock($handle, LOCK_EX)) { fclose($handle); abort(423, 'Upload is busy.'); }
        fseek($handle, $offset);
        $input = fopen('php://input', 'rb');
        $written = $input ? stream_copy_to_stream($input, $handle) : false;
        if ($input) fclose($input);
        fflush($handle); flock($handle, LOCK_UN); fclose($handle);
        abort_unless($written === $length, 422, 'The upload chunk was incomplete.');
        return response()->json(['ok' => true, 'uploaded' => $offset + $length]);
    }

    public function rename(Request $request, Document $document): RedirectResponse
    {
        $this->ownDocument($request, $document); $data = $request->validate(['name' => ['required', 'string', 'max:255']]); $name = trim($data['name']); abort_if($name === '' || str_contains($name, '/') || str_contains($name, '\\'), 422, 'Invalid file name.');
        $extension = pathinfo($document->original_name, PATHINFO_EXTENSION); if ($extension && ! str_ends_with(strtolower($name), '.'.strtolower($extension))) $name .= '.'.$extension; $document->update(['original_name' => $name]); return back()->with('success', 'File renamed successfully.');
    }
    public function move(Request $request, Document $document): RedirectResponse
    {
        $this->ownDocument($request, $document); $data = $request->validate(['folder_id' => ['nullable', 'integer', 'exists:document_folders,id']]); $folderId = $data['folder_id'] ?? null;
        if ($folderId) DocumentFolder::whereKey($folderId)->where('user_id', $request->user()->id)->firstOrFail(); $document->update(['folder_id' => $folderId]); return back()->with('success', 'File moved successfully.');
    }
    public function copy(Request $request, Document $document): RedirectResponse
    {
        $this->ownDocument($request, $document); $data = $request->validate(['folder_id' => ['nullable', 'integer', 'exists:document_folders,id']]); $folderId = $data['folder_id'] ?? null;
        if ($folderId) DocumentFolder::whereKey($folderId)->where('user_id', $request->user()->id)->firstOrFail(); $disk = Storage::disk($document->disk); $storedName = (string) str()->uuid().($document->extension ? '.'.$document->extension : ''); $copyPath = "private/{$request->user()->id}/{$storedName}";
        abort_unless($disk->copy($document->path, $copyPath), 500, 'Unable to copy the file.'); Document::create(['user_id' => $request->user()->id, 'folder_id' => $folderId, 'original_name' => pathinfo($document->original_name, PATHINFO_FILENAME).' - Copy'.($document->extension ? '.'.$document->extension : ''), 'stored_name' => $storedName, 'disk' => $document->disk, 'path' => $copyPath, 'mime_type' => $document->mime_type, 'size' => $document->size, 'extension' => $document->extension]); return back()->with('success', 'File copied successfully.');
    }
    public function download(Request $request, Document $document): mixed { $this->ownDocument($request, $document); abort_unless(Storage::disk($document->disk)->exists($document->path), 404); return Storage::disk($document->disk)->download($document->path, $document->original_name); }

    public function share(Request $request, Document $document): RedirectResponse
    {
        $this->ownDocument($request, $document);
        if (! $document->share_token) {
            $document->share_token = bin2hex(random_bytes(32));
        }
        $document->share_enabled = true;
        $document->share_expires_at = now()->addDays(7);
        $document->save();

        return back()->with('success', 'Secure download link created.');
    }

    public function unshare(Request $request, Document $document): RedirectResponse
    {
        $this->ownDocument($request, $document);
        $document->update(['share_enabled' => false]);
        return back()->with('success', 'Download link revoked.');
    }

    public function sharedDownload(string $token): mixed
    {
        $document = Document::where('share_token', $token)->where('share_enabled', true)->where(function ($q) { $q->whereNull('share_expires_at')->orWhere('share_expires_at', '>', now()); })->firstOrFail();
        abort_unless(Storage::disk($document->disk)->exists($document->path), 404);
        return Storage::disk($document->disk)->download($document->path, $document->original_name);
    }
    public function destroy(Request $request, Document $document): RedirectResponse { $this->ownDocument($request, $document); Storage::disk($document->disk)->delete($document->path); $document->delete(); return back()->with('success', 'File deleted permanently.'); }
    private function ownFolder(Request $request, DocumentFolder $folder): void { abort_unless($folder->user_id === $request->user()->id, 403); }
    private function ownDocument(Request $request, Document $document): void { abort_unless($document->user_id === $request->user()->id, 403); }
    private function isDescendant(DocumentFolder $candidate, DocumentFolder $ancestor): bool { while ($candidate->parent_id) { if ((int) $candidate->parent_id === (int) $ancestor->id) return true; $candidate = $candidate->parent; } return false; }
    private function copyFolderTree(DocumentFolder $folder, int $userId, ?int $parentId): void
    {
        $copy = DocumentFolder::create(['user_id' => $userId, 'parent_id' => $parentId, 'name' => $folder->name.' - Copy']);
        foreach ($folder->documents()->get() as $document) { $disk = Storage::disk($document->disk); $storedName = (string) str()->uuid().($document->extension ? '.'.$document->extension : ''); $copyPath = "private/{$userId}/{$storedName}"; if ($disk->copy($document->path, $copyPath)) Document::create(['user_id' => $userId, 'folder_id' => $copy->id, 'original_name' => $document->original_name, 'stored_name' => $storedName, 'disk' => $document->disk, 'path' => $copyPath, 'mime_type' => $document->mime_type, 'size' => $document->size, 'extension' => $document->extension]); }
        foreach ($folder->children()->get() as $child) $this->copyFolderTree($child, $userId, $copy->id);
    }
    private function deleteFolderTree(DocumentFolder $folder): void { foreach ($folder->children()->get() as $child) $this->deleteFolderTree($child); foreach ($folder->documents()->get() as $document) { Storage::disk($document->disk)->delete($document->path); $document->delete(); } $folder->delete(); }
}
