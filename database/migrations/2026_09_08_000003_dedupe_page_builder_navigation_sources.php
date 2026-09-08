<?php

use App\Models\CmsPage;
use App\Models\NavigationMenuItem;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (! Schema::hasTable('navigation_menu_items') || ! Schema::hasTable('cms_pages')) {
            return;
        }

        CmsPage::query()
            ->where('is_published', true)
            ->get(['id', 'title', 'slug'])
            ->each(function (CmsPage $page): void {
                $canonicalKey = 'cms_page:'.$page->id;
                $canonicalUrl = '/pages/'.ltrim($page->slug, '/');

                $matches = NavigationMenuItem::query()
                    ->where('menu', 'main')
                    ->where(function ($query) use ($page, $canonicalKey, $canonicalUrl): void {
                        $query
                            ->where('source_key', $canonicalKey)
                            ->orWhere('url', $canonicalUrl)
                            ->orWhere(function ($nested) use ($page): void {
                                $nested->where('route_name', 'site.about')->where('url', '/about-us');
                                if ($page->slug !== 'about-us') {
                                    $nested->whereRaw('1 = 0');
                                }
                            })
                            ->orWhere(function ($nested) use ($page): void {
                                $nested->where('route_name', 'site.technology')->where('url', '/our-technology');
                                if ($page->slug !== 'our-technology') {
                                    $nested->whereRaw('1 = 0');
                                }
                            });
                    })
                    ->orderBy('id')
                    ->get();

                $canonical = $matches->firstWhere('source_key', $canonicalKey);
                if (! $canonical) {
                    $canonical = $matches->first();
                    if ($canonical) {
                        $canonical->update([
                            'source_key' => $canonicalKey,
                            'source_type' => 'cms_page',
                            'area' => 'public',
                            'permission_key' => null,
                            'route_name' => 'cms.page',
                            'url' => $canonicalUrl,
                            'label' => $page->title,
                        ]);
                    }
                }

                if (! $canonical) {
                    return;
                }

                NavigationMenuItem::query()
                    ->where('menu', 'main')
                    ->where('id', '!=', $canonical->id)
                    ->where(function ($query) use ($page, $canonicalKey, $canonicalUrl): void {
                        $query
                            ->where('source_key', $canonicalKey)
                            ->orWhere('url', $canonicalUrl);

                        if ($page->slug === 'about-us') {
                            $query->orWhere(function ($nested): void {
                                $nested->where('route_name', 'site.about')->where('url', '/about-us');
                            });
                        }

                        if ($page->slug === 'our-technology') {
                            $query->orWhere(function ($nested): void {
                                $nested->where('route_name', 'site.technology')->where('url', '/our-technology');
                            });
                        }
                    })
                    ->delete();
            });
    }

    public function down(): void
    {
        // Navigation deduplication is intentionally irreversible.
    }
};
