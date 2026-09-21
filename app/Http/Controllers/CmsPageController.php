<?php

namespace App\Http\Controllers;

use App\Models\CmsPage;
use App\Models\SystemSetting;
use Illuminate\View\View;

class CmsPageController extends Controller
{
    public function show(string $slug): View|RedirectResponse
    {
        // About Us has a dedicated canonical route. Prevent Page Builder
        // access from creating a duplicate indexable version of the same page.
        if ($slug === 'about-us') {
            return redirect()->route('site.about', [], 301);
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
