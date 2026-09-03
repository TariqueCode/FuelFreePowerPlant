<?php

use App\Models\CmsPage;
use App\Models\NavigationMenuItem;
use Illuminate\Database\Migrations\Migration;

return new class extends Migration
{
    public function up(): void
    {
        $page = CmsPage::query()
            ->where('slug', 'about-us')
            ->where('is_published', true)
            ->first();

        if (! $page) {
            return;
        }

        NavigationMenuItem::query()
            ->whereIn('menu', ['main'])
            ->where('source_key', 'route:site.about')
            ->update([
                'source_key' => 'cms_page:'.$page->id,
                'source_type' => 'cms_page',
                'area' => 'public',
                'permission_key' => null,
                'route_name' => 'cms.page',
                'url' => route('cms.page', ['slug' => $page->slug]),
                'label' => $page->title,
            ]);
    }

    public function down(): void
    {
        // The canonical CMS-page source should remain authoritative after deployment.
    }
};
