<?php

namespace App\Http\Controllers;

use App\Models\CmsPage;
use App\Models\PowerPlant;
use Illuminate\View\View;

class CmsPageController extends Controller
{
    public function show(string $slug): View
    {
        $page = CmsPage::query()
            ->where('slug', $slug)
            ->where('is_published', true)
            ->firstOrFail();

        $pages = CmsPage::query()
            ->where('is_published', true)
            ->whereNotIn('slug', ['home', $slug])
            ->orderBy('title')
            ->get(['title', 'slug']);

        $projects = PowerPlant::query()
            ->orderByRaw("CASE WHEN status = 'operational' THEN 0 ELSE 1 END")
            ->latest()
            ->take(6)
            ->get(['name', 'slug', 'location', 'capacity_kw', 'technology', 'status', 'overview']);

        return view('cms.page', compact('page', 'pages', 'projects'));
    }
}
