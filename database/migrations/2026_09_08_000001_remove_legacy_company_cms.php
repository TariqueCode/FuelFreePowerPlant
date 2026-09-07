<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (!Schema::hasTable('site_content_items')) return;

        // Preserve legacy About Us content in the canonical Page Builder before
        // permanently removing the obsolete Company CMS records.
        if (Schema::hasTable('cms_pages')) {
            foreach (DB::table('site_content_items')->where('type', 'company')->get() as $page) {
                if (!DB::table('cms_pages')->where('slug', $page->slug)->exists()) {
                    DB::table('cms_pages')->insert([
                        'title' => $page->title,
                        'slug' => $page->slug,
                        'excerpt' => $page->excerpt,
                        'content' => $page->content,
                        'is_published' => $page->status === 'published',
                        'created_at' => $page->created_at,
                        'updated_at' => $page->updated_at,
                    ]);
                }
            }
        }

        if (Schema::hasTable('navigation_menu_items')) {
            DB::table('navigation_menu_items')->where(function ($query) {
                $query->where('route_name', 'company.page')
                    ->orWhere('source_key', 'like', 'site_content:%')
                    ->orWhere(function ($nested) {
                        $nested->where('source_type', 'site_content')
                            ->where('url', 'like', '/company/%');
                    });
            })->delete();
        }

        DB::table('site_content_items')->where('type', 'company')->delete();
    }

    public function down(): void
    {
        // Intentionally irreversible: Company CMS is a retired legacy module.
    }
};
