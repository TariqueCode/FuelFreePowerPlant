<?php

namespace App\Http\Controllers\Admin;

use App\Models\Document;
use App\Models\DocumentFolder;
use App\Models\SystemSetting;
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
            ? DocumentFolder::where('id', $folderId)->where('user_id', $user->id)->firstOrFail()
            : null;

        $folders = DocumentFolder::query()
            ->where('user_id', $user->id)
            ->where('parent_id', $folder?->id)
            ->withCount(['children', 'documents'])
            ->orderBy('name')
            ->get();

        $allFolders = DocumentFolder::query()
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
            $storage = Storage::disk('local');
            $storage->makeDirectory($privateRoot);
            $usedBytes = collect($storage->allFiles($privateRoot))
                ->sum(fn ($file) => (int) $storage->size($file));
        } catch (\Throwable $e) {
            // Keep the manager usable when the filesystem root is missing or
            // temporarily unavailable. Database metadata is the safe fallback.
            $usedBytes = (int) Document::where('user_id', $user->id)->sum('size');
        }

        $quotaBytes = (int) config('fuelfree.storage.quota_bytes', 50 * 1024 * 1024 * 1024);
        $availableBytes = max(0, $quotaBytes - $usedBytes);
        $usedPercent = $quotaBytes > 0
            ? min(100, round(($usedBytes / $quotaBytes) * 100, 1))
            : 0;

        $configuredMb = (int) SystemSetting::query()
            ->where('key', 'uploads.documents_max_mb')
            ->value('value');
        $maxUploadMb = max(1, $configuredMb ?: (int) config('fuelfree.upload.documents_max_mb', 50));

        return view('admin.documents.index', compact(
            'folder', 'folders', 'allFolders', 'documents', 'search',
            'usedBytes', 'availableBytes', 'quotaBytes', 'usedPercent', 'maxUploadMb'
        ));
    }
}
