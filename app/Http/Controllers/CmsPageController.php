<?php

namespace App\Http\Controllers;

use App\Models\CmsPage;
use Illuminate\View\View;

class CmsPageController extends Controller
{
    public function show(string $slug): View
    {
        $page = CmsPage::where('slug', $slug)->where('is_published', true)->firstOrFail();
        return view('cms.page', compact('page'));
    }
}
