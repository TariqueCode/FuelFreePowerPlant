<?php

namespace App\Http\Controllers;

use App\Models\CmsPage;
use App\Models\SiteContentItem;
use Illuminate\Http\Response;

class SitemapController extends Controller
{
    public function __invoke(): Response
    {
        $configuredDomain = trim((string) config('fuelfree.company.domain', request()->getHost()));
        $baseUrl = rtrim(
            preg_match('/^https?:\/\//i', $configuredDomain) ? $configuredDomain : 'https://' . $configuredDomain,
            '/'
        );

        $urls = collect([
            ['loc' => route('home')],
            ['loc' => route('site.about')],
            ['loc' => route('site.plants')],
            ['loc' => route('site.future-project')],
            ['loc' => route('site.solutions')],
            ['loc' => route('site.gallery')],
            ['loc' => route('site.career')],
            ['loc' => route('management')],
            ['loc' => route('news.index')],
            ['loc' => route('sustainability')],
            ['loc' => route('contact')],
        ]);

        foreach (CmsPage::query()->where('is_published', true)->whereNotIn('slug', ['about-us'])->get(['slug', 'updated_at']) as $page) {
            $urls->push([
                'loc' => route('cms.page', ['slug' => $page->slug]),
                'lastmod' => optional($page->updated_at)->toAtomString(),
            ]);
        }

        foreach (SiteContentItem::query()->published()->whereIn('type', ['news', 'announcement'])->whereNotNull('slug')->get(['slug', 'updated_at']) as $item) {
            $urls->push([
                'loc' => route('news.show', ['slug' => $item->slug]),
                'lastmod' => optional($item->updated_at)->toAtomString(),
            ]);
        }

        foreach (SiteContentItem::query()->where('type', 'gallery')->where('status', 'published')->whereNotNull('slug')->get(['slug', 'updated_at']) as $item) {
            $urls->push([
                'loc' => route('gallery.show', ['item' => $item->slug]),
                'lastmod' => optional($item->updated_at)->toAtomString(),
            ]);
        }

        $xml = '<?xml version="1.0" encoding="UTF-8"?>';
        $xml .= '<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">';
        foreach ($urls->unique('loc') as $url) {
            $loc = str_replace(request()->getSchemeAndHttpHost(), $baseUrl, $url['loc']);
            $xml .= '<url><loc>' . htmlspecialchars($loc, ENT_XML1 | ENT_QUOTES, 'UTF-8') . '</loc>';
            if (!empty($url['lastmod'])) {
                $xml .= '<lastmod>' . htmlspecialchars($url['lastmod'], ENT_XML1 | ENT_QUOTES, 'UTF-8') . '</lastmod>';
            }
            $xml .= '</url>';
        }
        $xml .= '</urlset>';

        return response($xml, 200, [
            'Content-Type' => 'application/xml; charset=UTF-8',
            'Cache-Control' => 'public, max-age=3600',
            'X-Robots-Tag' => 'noindex',
        ]);
    }
}
