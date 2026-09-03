<?php

use App\Models\CmsPage;
use App\Models\NavigationMenuItem;
use App\Models\SiteContentItem;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

return new class extends Migration
{
    public function up(): void
    {
        if (Schema::hasTable('cms_pages') && Schema::hasTable('site_content_items')) {
            SiteContentItem::query()
                ->whereIn('type', ['resource', 'resources'])
                ->orderBy('id')
                ->each(function (SiteContentItem $resource): void {
                    $base = Str::slug($resource->slug ?: $resource->title);
                    if ($base === '') {
                        return;
                    }

                    $slug = $base;
                    $counter = 2;
                    while (CmsPage::query()->where('slug', $slug)->exists()) {
                        $slug = $base.'-'.$counter++;
                    }

                    CmsPage::create([
                        'title' => $resource->title,
                        'slug' => $slug,
                        'excerpt' => $resource->excerpt,
                        'content' => $resource->content,
                        'is_published' => $resource->status === 'published',
                        'meta_title' => $resource->meta_title,
                        'meta_description' => $resource->meta_description,
                        'builder_blocks' => $resource->builder_blocks,
                        'template' => $resource->template ?: 'default',
                        'use_global_framework' => $resource->use_global_framework ?? true,
                        'use_global_header' => $resource->use_global_header ?? true,
                        'use_global_footer' => $resource->use_global_footer ?? true,
                    ]);

                    foreach ([$resource->image_path, $resource->attachment_path] as $path) {
                        if ($path) {
                            Storage::disk('public')->delete($path);
                        }
                    }

                    $resource->delete();
                });
        }

        if (Schema::hasTable('navigation_menu_items')) {
            NavigationMenuItem::query()
                ->where(function ($query): void {
                    $query->whereIn('route_name', ['resources.index', 'resources.show', 'resources.download'])
                        ->orWhereIn('source_key', [
                            'route:resources.index',
                            'route:resources.show',
                            'route:resources.download',
                        ])
                        ->orWhere('url', '/resources')
                        ->orWhere('url', 'like', '/resources/%');
                })
                ->delete();
        }
    }

    public function down(): void
    {
        // Resource records and their legacy files are intentionally not recreated.
        // Content remains available through the Page Builder after this migration.
    }
};
