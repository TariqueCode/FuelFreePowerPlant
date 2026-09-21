<?php

namespace App\Http\Controllers;

use App\Models\SiteContentItem;
use App\Models\SystemSetting;
use Illuminate\View\View;

class SustainabilityController
{
    public function __invoke(): View
    {
        $settings = SystemSetting::query()->pluck('value', 'key')->all();
        $brand = [
            'name' => $settings['company.name'] ?? config('fuelfree.company.name'),
            'tagline' => $settings['company.tagline'] ?? config('fuelfree.company.tagline'),
            'logo_path' => $settings['company.logo_path'] ?? null,
        ];

        // The legacy PowerPlant model/table was intentionally removed. Use the
        // current published Projects & Our Plans content source instead.
        $plants = SiteContentItem::published()
            ->where('type', 'plants')
            ->orderBy('sort_order')
            ->orderBy('title')
            ->get(['id', 'title', 'slug', 'excerpt', 'content']);

        $content = SiteContentItem::published()
            ->where('type', 'sustainability')
            ->orderBy('sort_order')
            ->latest('published_at')
            ->get();

        // Plant-level engineering metrics are no longer stored in the current
        // content model, so the sustainability page must not invent them.
        $metrics = [
            'capacity_mw' => null,
            'generation_mwh' => null,
            'co2_tonnes' => null,
            'efficiency' => null,
        ];

        return view('sustainability', compact('brand', 'plants', 'content', 'metrics'));
    }
}
