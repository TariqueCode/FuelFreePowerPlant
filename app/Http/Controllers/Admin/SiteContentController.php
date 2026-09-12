<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\SiteContentItem;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\View\View;

/**
 * Compatibility shell for legacy Site Content routes while Page Builder is
 * the canonical page-management surface.
 */
class SiteContentController extends Controller
{
    public function index(Request $request): RedirectResponse { abort(404); }
    public function create(Request $request): View|RedirectResponse { abort(404); }
    public function store(Request $request): RedirectResponse { abort(404); }
    public function edit(SiteContentItem $item): View|RedirectResponse { abort(404); }
    public function update(Request $request, SiteContentItem $item): RedirectResponse { abort(404); }
    public function destroy(SiteContentItem $item): RedirectResponse { abort(404); }
    public function togglePage(Request $request, SiteContentItem $item): RedirectResponse { abort(404); }
    public function toggleNews(Request $request, SiteContentItem $item): RedirectResponse { abort(404); }

    public function uploadMedia(Request $request): JsonResponse
    {
        $data = $request->validate([
            'media' => ['required','file','mimes:jpg,jpeg,png,webp,gif,mp4,webm,mov'],
        ]);
        $file = $data['media'];
        $path = $file->store('site-content/media','public');
        return response()->json([
            'url'=>Storage::disk('public')->url($path),
            'mime'=>$file->getMimeType(),
            'name'=>$file->getClientOriginalName(),
        ]);
    }
}
