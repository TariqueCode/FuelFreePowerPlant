<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\SiteContentItem;
use App\Models\SystemSetting;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\View\View;

/**
 * Compatibility shell for legacy Site Content routes while Page Builder is
 * the canonical page-management surface. Only the media endpoint remains
 * functional because the global editor may still reference its legacy URL.
 */
class SiteContentController extends Controller
{
    public function index(Request $request): View|RedirectResponse
    {
        abort(404);
    }

    public function create(Request $request): View|RedirectResponse
    {
        abort(404);
    }

    public function store(Request $request): RedirectResponse
    {
        abort(404);
    }

    public function edit(SiteContentItem $item): View|RedirectResponse
    {
        abort(404);
    }

    public function update(Request $request, SiteContentItem $item): RedirectResponse
    {
        abort(404);
    }

    public function destroy(SiteContentItem $item): RedirectResponse
    {
        abort(404);
    }

    public function togglePage(Request $request, SiteContentItem $item): RedirectResponse
    {
        abort(404);
    }

    public function toggleNews(Request $request, SiteContentItem $item): RedirectResponse
    {
        abort(404);
    }

    public function uploadMedia(Request $request): JsonResponse
    {
        $data = $request->validate([
            'media' => ['required', 'file', 'mimes:jpg,jpeg,png,webp,gif,mp4,webm,mov', 'max:' . $this->maxUploadKb()],
        ]);

        $file = $data['media'];
        $path = $file->store('site-content/media', 'public');

        return response()->json([
            'url' => Storage::disk('public')->url($path),
            'mime' => $file->getMimeType(),
            'name' => $file->getClientOriginalName(),
        ]);
    }

    private function maxUploadKb(): int
    {
        $mb = (int) SystemSetting::query()->where('key', 'uploads.content_media_max_mb')->value('value');
        return max(1, $mb ?: (int) config('fuelfree.upload.content_media_max_mb', 100)) * 1024;
    }
}
