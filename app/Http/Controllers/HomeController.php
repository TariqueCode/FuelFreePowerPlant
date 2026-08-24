<?php

namespace App\Http\Controllers;

use App\Models\CmsPage;
use App\Models\PowerPlant;
use Illuminate\View\View;

class HomeController extends Controller
{
    public function __invoke(): View
    {
        $plants = PowerPlant::query()
            ->orderByRaw("CASE WHEN status = 'operational' THEN 0 ELSE 1 END")
            ->latest()
            ->take(6)
            ->get();

        $homePage = CmsPage::query()
            ->where('slug', 'home')
            ->where('is_published', true)
            ->first();

        $pages = CmsPage::query()
            ->where('is_published', true)
            ->where('slug', '!=', 'home')
            ->orderBy('title')
            ->get(['title', 'slug']);

        $stats = [
            'projects' => PowerPlant::query()->count(),
            'capacity_mw' => round((float) PowerPlant::query()->sum('capacity_kw') / 1000, 2),
            'operational' => PowerPlant::query()->whereRaw('LOWER(status) = ?', ['operational'])->count(),
        ];

        return view('home', compact('plants', 'homePage', 'pages', 'stats'));
    }
}
