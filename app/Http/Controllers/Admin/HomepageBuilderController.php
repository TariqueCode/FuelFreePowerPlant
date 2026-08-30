<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\HomepageSection;
use App\Models\PowerPlant;
use App\Models\SiteContentItem;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class HomepageBuilderController extends Controller
{
    public function index(): View
    {
        $sections = HomepageSection::query()->ordered()->get();

        $counts = [
            'projects' => PowerPlant::query()->count(),
            'management' => SiteContentItem::query()->where('type', 'management')->where('status', 'published')->count(),
            'news' => SiteContentItem::query()->whereIn('type', ['news', 'announcement'])->where('status', 'published')->count(),
            'gallery' => SiteContentItem::query()->where('type', 'gallery')->where('status', 'published')->count(),
            'sliders' => \App\Models\SiteSlider::query()->where('is_published', true)->count(),
        ];

        return view('admin.homepage-builder.index', compact('sections', 'counts'));
    }

    public function update(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'section_order' => ['required', 'array'],
            'section_order.*' => ['required', 'string', 'max:60'],
            'sections' => ['nullable', 'array'],
        ]);

        $existing = HomepageSection::query()->pluck('key')->all();
        $order = array_values($data['section_order']);

        if (count($order) !== count($existing) || count(array_unique($order)) !== count($order) || array_diff($order, $existing) || array_diff($existing, $order)) {
            return back()->withErrors(['section_order' => 'The homepage section list is invalid.']);
        }

        foreach ($order as $position => $key) {
            HomepageSection::query()->where('key', $key)->update([
                'sort_order' => $position,
                'is_enabled' => $request->boolean("sections.{$key}"),
            ]);
        }

        return back()->with('status', 'Homepage layout saved successfully.');
    }
}
