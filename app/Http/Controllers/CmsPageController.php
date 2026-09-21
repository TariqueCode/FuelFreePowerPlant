<?php

namespace App\Http\Controllers;

use App\Models\CmsPage;
use App\Models\SystemSetting;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class CmsPageController extends Controller
{
    public function show(string $slug): View|RedirectResponse
    {
        // Dedicated public pages own these slugs. Keep Page Builder from
        // exposing duplicate indexable URLs for the same canonical content.
        $canonicalRoutes = [
            'about-us' => 'site.about',
            'plants' => 'site.plants',
            'future-project' => 'site.future-project',
            'career' => 'site.career',
            'solutions' => 'site.solutions',
            'gallery' => 'site.gallery',
            'news-and-event' => 'news.index',
            'sustainability' => 'sustainability',
            'contact' => 'contact',
            'management' => 'management',
        ];

        if (isset($canonicalRoutes[$slug])) {
            return redirect()->route($canonicalRoutes[$slug], [], 301);
        }

        $page = CmsPage::query()
            ->where('slug', $slug)
            ->where('is_published', true)
            ->firstOrFail();

        $brand = SystemSetting::query()
            ->whereIn('key', ['company.name', 'company.logo_path', 'company.tagline'])
            ->pluck('value', 'key');

        return view('site.company-page', [
            'item' => $page,
            // Page Builder pages always inherit the live global shell.
            'useGlobalFramework' => true,
            'useGlobalHeader' => true,
            'useGlobalFooter' => true,
            'brand' => [
                'name' => $brand->get('company.name') ?: config('fuelfree.company.name'),
                'logo_path' => $brand->get('company.logo_path'),
                'tagline' => $brand->get('company.tagline') ?: config('fuelfree.company.tagline'),
            ],
            'backRoute' => route('home'),
            'backLabel' => 'Back to Home',
        ]);
    }
}
