<?php

namespace App\Http\Controllers;

use App\Models\SiteContentItem;
use Illuminate\View\View;

class NewsController extends Controller
{
    public function index(): View
    {
        $news = SiteContentItem::published()
            ->where('type', 'news')
            ->orderByDesc('published_at')
            ->orderBy('sort_order')
            ->paginate(9);

        return view('news.index', compact('news'));
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

        return view('news.show', compact('article', 'related'));
    }
}
