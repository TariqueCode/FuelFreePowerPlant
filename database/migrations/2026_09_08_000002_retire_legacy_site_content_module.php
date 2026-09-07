<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // The Page Builder is now the single source for editable website pages.
        // Remove the retired Site Content admin module and its legacy page types
        // without touching independent Gallery records.
        if (Schema::hasTable('navigation_menu_items')) {
            DB::table('navigation_menu_items')
                ->where('area', 'dashboard')
                ->where(function ($query) {
                    $query->where('route_name', 'like', 'admin.site-content%')
                        ->orWhere('url', 'like', '/admin/site-content%')
                        ->orWhere('source_key', 'like', 'site_content:%');
                })
                ->delete();
        }

        if (Schema::hasTable('site_content_items')) {
            DB::table('site_content_items')
                ->whereIn('type', ['plants', 'future-project', 'solution'])
                ->delete();
        }
    }

    public function down(): void
    {
        // Intentionally irreversible: retired legacy Site Content records are not restored.
    }
};
