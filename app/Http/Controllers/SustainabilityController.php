<?php

namespace App\Http\Controllers;

use App\Models\PowerPlant;
use App\Models\SiteContentItem;
use App\Models\SystemSetting;
use Illuminate\View\View;

class SustainabilityController extends Controller
{
    public function __invoke(): View
    {
        $settings = SystemSetting::query()->pluck('value', 'key')->all();
        $brand = [
            'name' => $settings['company.name'] ?? config('fuelfree.company.name'),
            'tagline' => $settings['company.tagline'] ?? config('fuelfree.company.tagline'),
            'logo_path' => $settings['company.logo_path'] ?? null,
        ];

        $plants = PowerPlant::query()->latest()->get();
        $content = SiteContentItem::published()
            ->where('type', 'sustainability')
            ->orderBy('sort_order')
            ->latest('published_at')
            ->get();

        $sum = static function ($items, string $field): ?float {
            $values = $items->pluck($field)->filter(fn ($value) => $value !== null && $value !== '')->map(fn ($value) => (float) $value);
            return $values->isEmpty() ? null : (float) $values->sum();
        };

        $average = static function ($items, string $field): ?float {
            $values = $items->pluck($field)->filter(fn ($value) => $value !== null && $value !== '')->map(fn ($value) => (float) $value);
            return $values->isEmpty() ? null : (float) $values->avg();
        };

        $metrics = [
            'capacity_mw' => $sum($plants, 'capacity_kw'),
            'generation_mwh' => $sum($plants, 'annual_generation_mwh'),
            'co2_tonnes' => $sum($plants, 'co2_reduction_tonnes'),
            'efficiency' => $average($plants, 'efficiency_percent'),
        ];
        $metrics['capacity_mw'] = $metrics['capacity_mw'] === null ? null : $metrics['capacity_mw'] / 1000;

        return view('sustainability', compact('brand', 'plants', 'content', 'metrics'));
    }
}
