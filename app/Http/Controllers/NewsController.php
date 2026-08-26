<?php

namespace App\Http\Controllers;

use App\Models\SiteContentItem;
use App\Models\SystemSetting;
use Illuminate\Http\Request;
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

    public function index(Request $request): View
    {
        $base = SiteContentItem::published()->whereIn('type', ['news', 'announcement']);

        if ($request->filled('q')) {
            $term = trim((string) $request->input('q'));
            $base->where(function ($query) use ($term) {
                $query->where('title', 'like', "%{$term}%")
                    ->orWhere('excerpt', 'like', "%{$term}%")
                    ->orWhere('content', 'like', "%{$term}%");
            });
        }

        if (in_array($request->input('type'), ['news', 'announcement'], true)) {
            $base->where('type', $request->input('type'));
        }

        $publishedCount = (clone $base)->count();
        $featured = (clone $base)->where('is_featured', true)->orderByDesc('published_at')->first();

        $sort = $request->input('sort', 'newest') === 'oldest' ? 'asc' : 'desc';
        $newsQuery = clone $base;
        if ($featured) {
            $newsQuery->where('id', '!=', $featured->id);
        }
        $news = $newsQuery->orderBy('published_at', $sort)->orderBy('sort_order')->paginate(8)->withQueryString();

        $brand = $this->brand();

        return view('news.index', compact('news', 'featured', 'brand', 'publishedCount'));
    }

    public function show(string $slug): View
    {
        $article = SiteContentItem::published()
            ->whereIn('type', ['news', 'announcement'])
            ->where('slug', $slug)
            ->firstOrFail();
        $related = SiteContentItem::published()
            ->whereIn('type', ['news', 'announcement'])
            ->where('id', '!=', $article->id)
            ->orderByDesc('published_at')
            ->limit(3)
            ->get();
        $brand = $this->brand();
        return view('news.show', compact('article', 'related', 'brand'));
    }
}
