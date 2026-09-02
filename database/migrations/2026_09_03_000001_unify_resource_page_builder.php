<?php

use App\Models\CmsPage;
use App\Models\SiteContentItem;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Str;

return new class extends Migration
{
    public function up(): void
    {
        if (! Schema::hasTable('site_content_items')) {
            return;
        }

        Schema::table('site_content_items', function (Blueprint $table) {
            if (! Schema::hasColumn('site_content_items', 'builder_blocks')) {
                $table->json('builder_blocks')->nullable()->after('content');
            }
            if (! Schema::hasColumn('site_content_items', 'template')) {
                $table->string('template', 80)->default('default')->after('builder_blocks');
            }
            if (! Schema::hasColumn('site_content_items', 'use_global_framework')) {
                $table->boolean('use_global_framework')->default(true)->after('template');
            }
            if (! Schema::hasColumn('site_content_items', 'use_global_header')) {
                $table->boolean('use_global_header')->default(true)->after('use_global_framework');
            }
            if (! Schema::hasColumn('site_content_items', 'use_global_footer')) {
                $table->boolean('use_global_footer')->default(true)->after('use_global_header');
            }
        });

        // Move legacy Content Pages into the unified Resource/Page Builder store.
        // Existing Resource rows remain untouched; conflicting slugs are suffixed safely.
        if (Schema::hasTable('cms_pages')) {
            CmsPage::query()->orderBy('id')->each(function (CmsPage $page): void {
                $base = Str::slug($page->slug ?: $page->title);
                if ($base === '') {
                    return;
                }

                $slug = $base;
                $counter = 2;
                while (SiteContentItem::query()->where('type', 'resource')->where('slug', $slug)->exists()) {
                    $slug = $base.'-'.$counter++;
                }

                SiteContentItem::create([
                    'type' => 'resource',
                    'title' => $page->title,
                    'slug' => $slug,
                    'excerpt' => $page->excerpt,
                    'content' => $page->content,
                    'status' => $page->is_published ? 'published' : 'draft',
                    'published_at' => $page->is_published ? ($page->updated_at ?: now()) : null,
                    'meta_title' => $page->meta_title,
                    'meta_description' => $page->meta_description,
                    'builder_blocks' => $page->builder_blocks,
                    'template' => $page->template ?: 'default',
                    'use_global_framework' => $page->use_global_framework ?? true,
                    'use_global_header' => $page->use_global_header ?? true,
                    'use_global_footer' => $page->use_global_footer ?? true,
                    'is_featured' => false,
                    'sort_order' => $page->id,
                    'show_in_navigation' => false,
                ]);
            });
        }
    }

    public function down(): void
    {
        if (! Schema::hasTable('site_content_items')) {
            return;
        }

        Schema::table('site_content_items', function (Blueprint $table) {
            $drop = [];
            foreach (['builder_blocks', 'template', 'use_global_framework', 'use_global_header', 'use_global_footer'] as $column) {
                if (Schema::hasColumn('site_content_items', $column)) {
                    $drop[] = $column;
                }
            }
            if ($drop) {
                $table->dropColumn($drop);
            }
        });
    }
};