<?php

namespace App\Http\Controllers;

use App\Models\SiteContentItem;
use App\Models\SystemSetting;
use Illuminate\View\View;

class NewsController extends Controller
{
    private function brand(): array
    {
        $settings = SystemSetting::query()->pluck('value', 'key')->all();
        return [
            'name' => $settings['company.name'] ?? config('fuelfree.company.name'),
            'domain' => $settings['company.domain'] ?? config('fuelfree.company.domain'),
            'tagline' => $settings['company.tagline'] ?? config('fuelfree.company.tagline'),
            'logo_path' => $settings['company.logo_path'] ?? null,
        ];
    }

    public function index(): View
    {
        $base = SiteContentItem::published()->whereIn('type', ['news','announcement']);
        $featured = (clone $base)->where('is_featured', true)->orderByDesc('published_at')->first();
        $news = $base->orderByDesc('published_at')->orderBy('sort_order')->paginate(9);
        $brand = $this->brand();
        return view('news.index', compact('news','featured','brand'));
    }

    public function show(string $slug): View
    {
        $article = SiteContentItem::published()
            ->whereIn('type', ['news','announcement'])
            ->where('slug', $slug)
            ->firstOrFail();
        $related = SiteContentItem::published()
            ->whereIn('type', ['news','announcement'])
            ->where('id', '!=', $article->id)
            ->orderByDesc('published_at')
            ->limit(3)
            ->get();
        $brand = $this->brand();
        return view('news.show', compact('article','related','brand'));
    }
}
