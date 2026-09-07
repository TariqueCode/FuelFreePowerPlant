<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\SiteContentItem;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

/**
 * Retained only as a compatibility endpoint for routes that may still exist
 * in deployed route caches. The legacy Site Content CMS is retired; Page Builder
 * is now the canonical admin page-management surface.
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
        abort(404);
    }
}
