<?php

namespace App\Http\Controllers;

use App\Models\CmsPage;
use App\Models\ManagementProfileFolder;
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

        $absoluteRoute = static function (string $routeName, array $parameters = []) use ($baseUrl): string {
            $path = parse_url(route($routeName, $parameters), PHP_URL_PATH) ?: '/';

            return $baseUrl . '/' . ltrim($path, '/');
        };

        $urls = collect([
            ['loc' => $absoluteRoute('home')],
            ['loc' => $absoluteRoute('site.about')],
            ['loc' => $absoluteRoute('site.plants')],
            ['loc' => $absoluteRoute('site.future-project')],
            ['loc' => $absoluteRoute('site.solutions')],
            ['loc' => $absoluteRoute('site.gallery')],
            ['loc' => $absoluteRoute('site.career')],
            ['loc' => $absoluteRoute('news.index')],
            ['loc' => $absoluteRoute('sustainability')],
            ['loc' => $absoluteRoute('contact')],
        ]);

        foreach (ManagementProfileFolder::query()->where('status', 'published')->whereNotNull('slug')->orderBy('sort_order')->orderBy('id')->get(['slug', 'updated_at']) as $folder) {
            $urls->push([
                'loc' => $baseUrl . '/' . ltrim($folder->slug, '/'),
                'lastmod' => optional($folder->updated_at)->toAtomString(),
            ]);
        }

        foreach (CmsPage::query()->where('is_published', true)->whereNotIn('slug', ['about-us'])->get(['slug', 'updated_at']) as $page) {
            $urls->push([
                'loc' => $absoluteRoute('cms.page', ['slug' => $page->slug]),
                'lastmod' => optional($page->updated_at)->toAtomString(),
            ]);
        }

        foreach (SiteContentItem::query()->published()->whereIn('type', ['news', 'announcement'])->whereNotNull('slug')->get(['slug', 'updated_at']) as $item) {
            $urls->push([
                'loc' => $absoluteRoute('news.show', ['slug' => $item->slug]),
                'lastmod' => optional($item->updated_at)->toAtomString(),
            ]);
        }

        foreach (SiteContentItem::query()->where('type', 'gallery')->where('status', 'published')->whereNotNull('slug')->get(['slug', 'updated_at']) as $item) {
            $urls->push([
                'loc' => $absoluteRoute('gallery.show', ['item' => $item->slug]),
                'lastmod' => optional($item->updated_at)->toAtomString(),
            ]);
        }

        $xml = '<?xml version="1.0" encoding="UTF-8"?>';
        $xml .= '<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">';
        foreach ($urls->unique('loc') as $url) {
            $loc = $url['loc'];
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
        ]);
    }
}
