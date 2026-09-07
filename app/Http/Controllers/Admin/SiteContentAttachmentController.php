<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\SiteContentItem;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

/**
 * Compatibility shell for route caches from the retired Site Content CMS.
 */
class SiteContentAttachmentController extends Controller
{
    public function chunk(Request $request, SiteContentItem $item): JsonResponse
    {
        abort(404);
    }

    public function destroy(SiteContentItem $item): JsonResponse
    {
        abort(404);
    }
}
