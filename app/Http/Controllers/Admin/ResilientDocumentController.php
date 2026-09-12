<?php

namespace App\Http\Controllers\Admin;

use App\Models\Document;
use App\Models\DocumentFolder;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Illuminate\View\View;

class ResilientDocumentController extends DocumentController
{
    public function index(Request $request): View
    {
        $user = $request->user();
        $folderId = $request->integer('folder');
        $search = trim((string) $request->input('q'));
        $folder = $folderId ? DocumentFolder::where('id', $folderId)->where('user_id', $user->id)->firstOrFail() : null;
        $folders = DocumentFolder::query()->where('user_id', $user->id)->where('parent_id', $folder?->id)->withCount(['children', 'documents'])->orderBy('name')->get();
        $allFolders = DocumentFolder::query()->where('user_id', $user->id)->orderBy('name')->get(['id', 'parent_id', 'name']);
        $documents = Document::query()->where('user_id', $user->id)->where('folder_id', $folder?->id)->when($search !== '', fn ($query) => $query->where('original_name', 'like', "%{$search}%"))->latest()->paginate(20)->withQueryString();

        $usedBytes = 0;
        $privateRoot = "private/{$user->id}";
        try {
            $storage = Storage::disk('local');
            $storage->makeDirectory($privateRoot);
            $usedBytes = collect($storage->allFiles($privateRoot))->sum(fn ($file) => (int) $storage->size($file));
        } catch (\Throwable $e) {
            $usedBytes = (int) Document::where('user_id', $user->id)->sum('size');
        }

        $quotaBytes = (int) config('fuelfree.storage.quota_bytes', 50 * 1024 * 1024 * 1024);
        $availableBytes = max(0, $quotaBytes - $usedBytes);
        $usedPercent = $quotaBytes > 0 ? min(100, round(($usedBytes / $quotaBytes) * 100, 1)) : 0;

        return view('admin.documents.index', compact('folder', 'folders', 'allFolders', 'documents', 'search', 'usedBytes', 'availableBytes', 'quotaBytes', 'usedPercent'));
    }

    public function legacyFolderUrl(int $folder)
    {
        return redirect()->route('admin.documents', ['folder' => $folder]);
    }

    /**
     * Use a larger-but-safe chunk than the previous 64 KiB default. 128 KiB
     * keeps shared-hosting request bodies small while cutting request overhead.
     */
    public function chunkUpload(Request $request): mixed
    {
        if (!$request->hasHeader('X-Upload-Id')) {
            $data = $request->validate([
                'filename' => ['required', 'string', 'max:255'],
                'size' => ['required', 'integer', 'min:1'],
                'folder_id' => ['nullable', 'integer', 'exists:document_folders,id'],
            ]);
            if (!empty($data['folder_id'])) {
                DocumentFolder::whereKey($data['folder_id'])->where('user_id', $request->user()->id)->firstOrFail();
            }
            $uploadId = (string) Str::uuid();
            Storage::disk('local')->makeDirectory('.uploads');
            $chunkSize = 131072;
            Storage::disk('local')->put(
                ".uploads/{$uploadId}.json",
                json_encode([
                    'user_id' => $request->user()->id,
                    'filename' => $data['filename'],
                    'size' => (int) $data['size'],
                    'folder_id' => $data['folder_id'] ?? null,
                    'chunk_size' => $chunkSize,
                    'created_at' => now()->toIso8601String(),
                ], JSON_THROW_ON_ERROR)
            );
            return response()->json(['ok' => true, 'upload_id' => $uploadId, 'chunk_size' => $chunkSize]);
        }

        $uploadId = (string) $request->header('X-Upload-Id');
        abort_unless(preg_match('/^[0-9a-f-]{36}$/i', $uploadId), 422, 'Invalid upload session.');
        $metaPath = ".uploads/{$uploadId}.json";
        $partPath = ".uploads/{$uploadId}.part";
        $disk = Storage::disk('local');
        abort_unless($disk->exists($metaPath), 404, 'Upload session not found or expired.');
        $meta = json_decode($disk->get($metaPath), true) ?: [];
        abort_unless((int) ($meta['user_id'] ?? 0) === (int) $request->user()->id, 403);

        if ($request->boolean('finalize')) {
            $expected = (int) ($meta['size'] ?? 0);
            abort_unless($expected > 0 && $disk->exists($partPath), 422, 'Upload data is missing.');
            abort_unless((int) $disk->size($partPath) === $expected, 422, 'Uploaded data is incomplete.');
            $this->ensureQuotaAvailable($request->user()->id, $expected);
            $extension = pathinfo((string) $meta['filename'], PATHINFO_EXTENSION);
            $storedName = (string) Str::uuid().($extension !== '' ? '.'.$extension : '');
            $finalPath = "private/{$request->user()->id}/{$storedName}";
            $disk->makeDirectory("private/{$request->user()->id}");
            abort_unless($disk->move($partPath, $finalPath), 500, 'Unable to finalize uploaded file.');
            $mime = 'application/octet-stream';
            $absolute = $disk->path($finalPath);
            if (class_exists(\finfo::class)) {
                $finfo = new \finfo(FILEINFO_MIME_TYPE);
                $mime = $finfo->file($absolute) ?: $mime;
            }
            Document::create([
                'user_id' => $request->user()->id,
                'folder_id' => $meta['folder_id'] ?? null,
                'original_name' => $meta['filename'],
                'stored_name' => $storedName,
                'disk' => 'local',
                'path' => $finalPath,
                'mime_type' => $mime,
                'size' => $expected,
                'extension' => $extension !== '' ? $extension : null,
            ]);
            $disk->delete($metaPath);
            return response()->json(['ok' => true, 'message' => 'File uploaded securely.']);
        }

        $index = (int) $request->header('X-Chunk-Index', '-1');
        $offset = (int) $request->header('X-Chunk-Offset', '-1');
        $length = (int) ($request->header('X-Chunk-Length') ?: $request->header('Content-Length', '0'));
        $chunkSize = (int) ($meta['chunk_size'] ?? 131072);
        abort_if($index < 0 || $offset < 0 || $length < 1 || $length > $chunkSize, 422, 'Invalid upload chunk.');
        abort_if($offset + $length > (int) $meta['size'], 422, 'Upload chunk exceeds the declared file size.');

        $absolute = $disk->path($partPath);
        $handle = fopen($absolute, 'c+b');
        abort_unless($handle !== false, 500, 'Unable to open upload buffer.');
        if (!flock($handle, LOCK_EX)) {
            fclose($handle);
            abort(423, 'Upload is busy.');
        }
        fseek($handle, $offset);
        $input = fopen('php://input', 'rb');
        $written = $input ? stream_copy_to_stream($input, $handle) : false;
        if ($input) fclose($input);
        fflush($handle);
        flock($handle, LOCK_UN);
        fclose($handle);
        abort_unless($written === $length, 422, 'The upload chunk was incomplete.');
        return response()->json(['ok' => true, 'uploaded' => $offset + $length]);
    }

    protected function ensureQuotaAvailable(int $userId, int $additionalBytes): void
    {
        $quotaBytes = (int) config('fuelfree.storage.quota_bytes', 50 * 1024 * 1024 * 1024);
        $usedBytes = (int) Document::where('user_id', $userId)->sum('size');
        abort_if($additionalBytes < 0 || $usedBytes + $additionalBytes > $quotaBytes, 422, 'There is not enough storage space for this file.');
    }
}