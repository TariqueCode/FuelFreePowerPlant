<?php

namespace App\Http\Controllers\Admin;

use App\Models\Document;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\View\View;

class ResilientDocumentController extends DocumentController
{
    public function index(Request $request): View
    {
        $user = $request->user();
        $folderId = $request->integer('folder');
        $search = trim((string) $request->input('q'));

        $folder = $folderId
            ? \App\Models\DocumentFolder::where('id', $folderId)->where('user_id', $user->id)->firstOrFail()
            : null;

        $folders = \App\Models\DocumentFolder::query()
            ->where('user_id', $user->id)
            ->where('parent_id', $folder?->id)
            ->withCount(['children', 'documents'])
            ->orderBy('name')
            ->get();

        $allFolders = \App\Models\DocumentFolder::query()
            ->where('user_id', $user->id)
            ->orderBy('name')
            ->get(['id', 'parent_id', 'name']);

        $documents = Document::query()
            ->where('user_id', $user->id)
            ->where('folder_id', $folder?->id)
            ->when($search !== '', fn ($query) => $query->where('original_name', 'like', "%{$search}%"))
            ->latest()
            ->paginate(20)
            ->withQueryString();

        $usedBytes = 0;
        $privateRoot = "private/{$user->id}";
        try {
            Storage::disk('local')->makeDirectory($privateRoot);
            $storage = Storage::disk('local');
            $usedBytes = collect($storage->allFiles($privateRoot))
                ->sum(fn ($file) => (int) $storage->size($file));
        } catch (\Throwable $e) {
            // The manager must remain usable even when the filesystem root is
            // temporarily unavailable. Database metadata is the safe fallback.
            $usedBytes = (int) Document::where('user_id', $user->id)->sum('size');
        }

        $quotaBytes = (int) config('fuelfree.storage.quota_bytes', 50 * 1024 * 1024 * 1024);
        $availableBytes = max(0, $quotaBytes - $usedBytes);
        $usedPercent = $quotaBytes > 0
            ? min(100, round(($usedBytes / $quotaBytes) * 100, 1))
            : 0;
        $maxUploadMb = (int) ($this->maxUploadBytes() / 1048576);

        return view('admin.documents.index', compact(
            'folder', 'folders', 'allFolders', 'documents', 'search',
            'usedBytes', 'availableBytes', 'quotaBytes', 'usedPercent', 'maxUploadMb'
        ));
    }
}
