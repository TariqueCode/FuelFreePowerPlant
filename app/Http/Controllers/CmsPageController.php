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

        $brand = SystemSetting::query()
            ->whereIn('key', ['company.name', 'company.logo_path', 'company.tagline'])
            ->pluck('value', 'key');

        return view('site.company-page', ['item' => $page, 'brand' => ['name' => $brand->get('company.name') ?: config('fuelfree.company.name'), 'logo_path' => $brand->get('company.logo_path'), 'tagline' => $brand->get('company.tagline') ?: config('fuelfree.company.tagline')], 'backRoute' => route('home'), 'backLabel' => 'Back to Home']);
    }
}
