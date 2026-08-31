<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\HomepageSection;
use App\Models\PowerPlant;
use App\Models\SiteContentItem;
use App\Models\SystemSetting;
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

        $choices = [
            'projects' => PowerPlant::query()->orderBy('name')->get(['id','name']),
            'management' => SiteContentItem::query()->where('type','management')->published()->orderBy('sort_order')->orderBy('title')->get(['id','title']),
            'news' => SiteContentItem::query()->whereIn('type',['news','announcement'])->published()->orderBy('sort_order')->latest('published_at')->get(['id','title','type']),
            'gallery' => SiteContentItem::query()->where('type','gallery')->published()->orderBy('sort_order')->latest('published_at')->get(['id','title']),
        ];

        return view('admin.homepage-builder.index', compact('sections', 'counts', 'choices'));
    }

    public function update(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'section_order' => ['required', 'array'],
            'section_order.*' => ['required', 'string', 'max:60'],
            'sections' => ['nullable', 'array'],
            'settings' => ['nullable', 'array'],
            'settings.*.limit' => ['nullable', 'integer', 'min:1', 'max:12'],
            'settings.*.mode' => ['nullable', 'in:latest,selected'],
            'settings.*.ids' => ['nullable', 'array', 'max:12'],
            'settings.*.ids.*' => ['integer', 'distinct'],
        ]);

        $existing = HomepageSection::query()->pluck('key')->all();
        $order = array_values($data['section_order']);

        if (count($order) !== count($existing) || count(array_unique($order)) !== count($order) || array_diff($order, $existing) || array_diff($existing, $order)) {
            return back()->withErrors(['section_order' => 'The homepage section list is invalid.']);
        }

        foreach ($order as $position => $key) {
            $section = HomepageSection::query()->where('key', $key)->first();
            $settings = is_array($section?->settings) ? $section->settings : [];
            if (in_array($key, ['projects','management','news','gallery'], true) && $request->has("settings.{$key}.limit")) {
                $settings['limit'] = max(1, min(12, (int) $request->input("settings.{$key}.limit")));
                $mode = $request->input("settings.{$key}.mode", $settings['mode'] ?? 'latest');
                $settings['mode'] = $mode;
                if ($mode === 'selected') {
                    $settings['ids'] = array_values(array_unique(array_slice(array_map('intval', (array) $request->input("settings.{$key}.ids", [])), 0, 12)));
                } else {
                    unset($settings['ids']);
                }
            }
            HomepageSection::query()->where('key', $key)->update([
                'sort_order' => $position,
                'is_enabled' => $request->boolean("sections.{$key}"),
                'settings' => $settings,
            ]);
        }

        return back()->with('status', 'Homepage layout saved successfully.');
    }
}
