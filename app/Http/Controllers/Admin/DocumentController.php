<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Document;
use App\Models\DocumentFolder;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\View\View;

class DocumentController extends Controller
{
    public function index(Request $request): View
    {
        $user = $request->user();
        $folderId = $request->integer('folder');
        $search = trim((string) $request->input('q'));

        $folder = $folderId
            ? DocumentFolder::where('id', $folderId)->where('user_id', $user->id)->firstOrFail()
            : null;

        $folders = DocumentFolder::query()
            ->where('user_id', $user->id)
            ->where('parent_id', $folder?->id)
            ->orderBy('name')
            ->get();

        $documents = Document::query()
            ->where('user_id', $user->id)
            ->where('folder_id', $folder?->id)
            ->when($search !== '', fn ($query) => $query->where('original_name', 'like', "%{$search}%"))
            ->latest()
            ->paginate(20)
            ->withQueryString();

        $storageFiles = Storage::disk('local')->allFiles("private/{$user->id}");
        $usedBytes = 0;
        foreach ($storageFiles as $storageFile) {
            $usedBytes += (int) Storage::disk('local')->size($storageFile);
        }

        $quotaBytes = (int) config('fuelfree.storage.quota_bytes', 0);
        $usedPercent = $quotaBytes > 0 ? min(100, round(($usedBytes / $quotaBytes) * 100, 1)) : 0;
        $availableBytes = $quotaBytes > $usedBytes ? $quotaBytes - $usedBytes : 0;

        return view('admin.documents.index', compact(
            'folder', 'folders', 'documents', 'search',
            'usedBytes', 'availableBytes', 'quotaBytes', 'usedPercent'
        ));
    }

    public function storeFolder(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'name' => ['required', 'string', 'max:150', 'regex:/^[^\\\\\/]+$/'],
            'parent_id' => ['nullable', 'integer', 'exists:document_folders,id'],
        ]);

        $parentId = $data['parent_id'] ?? null;
        if ($parentId && ! DocumentFolder::whereKey($parentId)->where('user_id', $request->user()->id)->exists()) {
            abort(403);
        }

        DocumentFolder::create([
            'user_id' => $request->user()->id,
            'parent_id' => $parentId,
            'name' => trim($data['name']),
        ]);

        return back()->with('success', 'Folder created successfully.');
    }

    public function store(Request $request): RedirectResponse
    {
        $data = $request->validate([
            // No application-level file-size or MIME whitelist: the hosting/PHP limits remain the hard boundary.
            'file' => ['required', 'file'],
            'folder_id' => ['nullable', 'integer', 'exists:document_folders,id'],
        ]);

        $user = $request->user();
        $folderId = $data['folder_id'] ?? null;
        if ($folderId && ! DocumentFolder::whereKey($folderId)->where('user_id', $user->id)->exists()) {
            abort(403);
        }

        $file = $data['file'];
        $storedName = $file->hashName();
        $path = $file->storeAs("private/{$user->id}", $storedName, 'local');

        Document::create([
            'user_id' => $user->id,
            'folder_id' => $folderId,
            'original_name' => $file->getClientOriginalName(),
            'stored_name' => $storedName,
            'disk' => 'local',
            'path' => $path,
            'mime_type' => $file->getMimeType(),
            'size' => $file->getSize(),
            'extension' => strtolower($file->getClientOriginalExtension()),
        ]);

        return back()->with('success', 'File uploaded securely.');
    }

    public function download(Request $request, Document $document): mixed
    {
        abort_unless($document->user_id === $request->user()->id, 403);
        abort_unless(Storage::disk($document->disk)->exists($document->path), 404);

        return Storage::disk($document->disk)->download($document->path, $document->original_name);
    }

    public function destroy(Request $request, Document $document): RedirectResponse
    {
        abort_unless($document->user_id === $request->user()->id, 403);
        Storage::disk($document->disk)->delete($document->path);
        $document->delete();

        return back()->with('success', 'File deleted.');
    }
}
