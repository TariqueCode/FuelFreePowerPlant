<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\HomepageSection;
use App\Models\PowerPlant;
use App\Models\SiteContentItem;
use App\Models\SystemSetting;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
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
            'settings.*.limit' => ['nullable', 'integer', 'min:1', 'max:100'],
            'settings.*.mode' => ['nullable', 'in:latest,selected'],
            'settings.*.ids' => ['nullable', 'array', 'max:100'],
            'settings.*.ids.*' => ['integer', 'distinct'],
            'settings.welcome.eyebrow' => ['nullable', 'string', 'max:120'],
            'settings.welcome.signoff' => ['nullable', 'string', 'max:240'],
            'settings.welcome.title' => ['nullable', 'string', 'max:240'],
            'settings.welcome.content' => ['nullable', 'string', 'max:30000'],
            'settings.welcome.preview_words' => ['nullable', 'integer', 'min:20', 'max:500'],
            'settings.welcome.more_words' => ['nullable', 'integer', 'min:20', 'max:2000'],
            'settings.welcome.show_full' => ['nullable', 'boolean'],
            'settings.welcome.layout' => ['nullable', 'in:left,center,right'],
            'settings.welcome.management_ids' => ['nullable', 'array', 'max:2'],
            'settings.welcome.management_ids.*' => ['integer', 'distinct'],
            'settings.*.layout' => ['nullable', 'in:left,center,right'],
        ]);

        $existing = HomepageSection::query()->pluck('key')->all();
        $order = array_values($data['section_order']);

        if (count($order) !== count($existing) || count(array_unique($order)) !== count($order) || array_diff($order, $existing) || array_diff($existing, $order)) {
            return back()->withErrors(['section_order' => 'The homepage section list is invalid.']);
        }

        $selectedIds = [];
        foreach (['projects','management','news','gallery'] as $key) {
            $selectedIds[$key] = array_values(array_unique(array_map('intval', (array) $request->input("settings.{$key}.ids", []))));
        }

        $validIds = [
            'projects' => PowerPlant::query()->whereIn('id', $selectedIds['projects'])->pluck('id')->map(fn ($id) => (int) $id)->all(),
            'management' => SiteContentItem::query()->where('type', 'management')->published()->whereIn('id', $selectedIds['management'])->pluck('id')->map(fn ($id) => (int) $id)->all(),
            'news' => SiteContentItem::query()->whereIn('type', ['news','announcement'])->published()->whereIn('id', $selectedIds['news'])->pluck('id')->map(fn ($id) => (int) $id)->all(),
            'gallery' => SiteContentItem::query()->where('type', 'gallery')->published()->whereIn('id', $selectedIds['gallery'])->pluck('id')->map(fn ($id) => (int) $id)->all(),
        ];

        DB::transaction(function () use ($order, $request, $validIds) {
        foreach ($order as $position => $key) {
            $section = HomepageSection::query()->where('key', $key)->first();
            $settings = is_array($section?->settings) ? $section->settings : [];
            $settings['layout'] = in_array((string) $request->input("settings.{$key}.layout", $settings['layout'] ?? 'left'), ['left','center','right'], true) ? $request->input("settings.{$key}.layout", $settings['layout'] ?? 'left') : 'left';
            if ($key === 'welcome' && $request->has('settings.welcome')) {
                $welcome = (array) $request->input('settings.welcome', []);
                $settings['eyebrow'] = trim((string) ($welcome['eyebrow'] ?? ''));
                $settings['signoff'] = trim((string) ($welcome['signoff'] ?? ''));
                $settings['title'] = trim((string) ($welcome['title'] ?? ''));
                $settings['content'] = trim((string) ($welcome['content'] ?? ''));
                $settings['preview_words'] = max(20, min(500, (int) ($welcome['preview_words'] ?? 180)));
                $settings['more_words'] = max(20, min(2000, (int) ($welcome['more_words'] ?? 900)));
                $settings['show_full'] = $request->boolean('settings.welcome.show_full');
                $settings['layout'] = in_array(($welcome['layout'] ?? 'left'), ['left','center','right'], true) ? $welcome['layout'] : 'left';
                $requestedManagementIds = array_values(array_unique(array_map('intval', (array) ($welcome['management_ids'] ?? []))));
                $settings['management_ids'] = array_values(array_slice(
                    SiteContentItem::query()->where('type','management')->published()->whereIn('id', $requestedManagementIds)->pluck('id')->map(fn ($id) => (int) $id)->all(),
                    0,
                    2
                ));
            }
            if (in_array($key, ['projects','management','news','gallery'], true) && $request->has("settings.{$key}.limit")) {
                $settings['limit'] = max(1, min(100, (int) $request->input("settings.{$key}.limit")));
                $mode = $request->input("settings.{$key}.mode", $settings['mode'] ?? 'latest');
                $settings['mode'] = $mode;
                if ($mode === 'selected') {
                    $settings['ids'] = array_values(array_slice($validIds[$key] ?? [], 0, 100));
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
        });

        return back()->with('status', 'Homepage layout saved successfully.');
    }
}
