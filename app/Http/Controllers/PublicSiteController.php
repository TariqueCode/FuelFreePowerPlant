<?php

namespace App\Http\Controllers;

use App\Models\CmsPage;
use App\Models\PowerPlant;
use App\Models\SiteContentItem;
use App\Models\SystemSetting;
use Illuminate\View\View;

class PublicSiteController
{
    public function show(string $section): View
    {
        $allowed = ['about-us', 'plants', 'future-project', 'career'];
        abort_unless(in_array($section, $allowed, true), 404);

        $settings = SystemSetting::query()->whereIn('key', ['company.name', 'company.logo_path', 'company.tagline'])->pluck('value', 'key');
        $brand = [
            'name' => $settings->get('company.name') ?: config('fuelfree.company.name'),
            'logo_path' => $settings->get('company.logo_path'),
            'tagline' => $settings->get('company.tagline') ?: config('fuelfree.company.tagline'),
        ];
        $pages = CmsPage::query()->where('is_published', true)->where('slug', '!=', 'home')->orderBy('title')->get(['title', 'slug']);
        $page = CmsPage::query()->where('slug', $section)->where('is_published', true)->first();
        $companyItems = collect(); $items = collect(); $projects = collect();

        if ($section === 'about-us') {
            $companyItems = SiteContentItem::published()->where('type', 'company')->orderBy('sort_order')->orderBy('title')->get();
        } elseif ($section === 'plants') {
            $projects = PowerPlant::query()->orderByRaw("CASE WHEN status='operational' THEN 0 ELSE 1 END")->orderBy('name')->get();
        } elseif ($section === 'future-project') {
            $projects = PowerPlant::query()->whereRaw("LOWER(status) != 'operational'")->orderBy('name')->get();
            $items = SiteContentItem::published()->whereIn('type', ['future-project', 'project'])->orderBy('sort_order')->orderBy('title')->get();
        } elseif ($section === 'career') {
            $items = SiteContentItem::published()->whereIn('type', ['career', 'careers', 'job'])->orderBy('sort_order')->orderBy('title')->get();
        }

        $titles = ['about-us' => 'About Us', 'plants' => 'Plants', 'future-project' => 'Future Project', 'career' => 'Career'];
        return view('site.section', compact('section', 'titles', 'page', 'pages', 'brand', 'companyItems', 'items', 'projects'));
    }
}
