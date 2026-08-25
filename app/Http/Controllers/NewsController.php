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
        $news = SiteContentItem::published()
            ->where('type', 'news')
            ->orderByDesc('published_at')
            ->orderBy('sort_order')
            ->paginate(9);
        $brand = $this->brand();
        return view('news.index', compact('news', 'brand'));
    }

    public function show(string $slug): View
    {
        $article = SiteContentItem::published()
            ->where('type', 'news')
            ->where('slug', $slug)
            ->firstOrFail();
        $related = SiteContentItem::published()
            ->where('type', 'news')
            ->where('id', '!=', $article->id)
            ->orderByDesc('published_at')
            ->limit(3)
            ->get();
        $brand = $this->brand();
        return view('news.show', compact('article', 'related', 'brand'));
    }
}
