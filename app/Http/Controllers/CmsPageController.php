<?php

namespace App\Http\Controllers;

use App\Models\CmsPage;
use App\Models\PowerPlant;
use App\Models\SiteContentItem;
use App\Models\SystemSetting;
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

        $companyItems = SiteContentItem::query()
            ->published()
            ->where('type', 'company')
            ->orderBy('sort_order')
            ->orderBy('title')
            ->get(['title', 'slug', 'excerpt', 'content', 'image_path']);

        $brand = SystemSetting::query()
            ->whereIn('key', ['company.name', 'company.logo_path', 'company.tagline'])
            ->pluck('value', 'key');

        return view('site.company-page', ['item' => $page, 'brand' => ['name' => $brand->get('company.name') ?: config('fuelfree.company.name'), 'logo_path' => $brand->get('company.logo_path'), 'tagline' => $brand->get('company.tagline') ?: config('fuelfree.company.tagline')], 'backRoute' => route('home'), 'backLabel' => 'Back to Home']);
    }
}
