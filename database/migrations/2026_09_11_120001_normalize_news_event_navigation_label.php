<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (! Schema::hasTable('navigation_menu_items')) {
            return;
        }

        DB::table('navigation_menu_items')
            ->where(function ($query): void {
                $query->where('route_name', 'admin.news_and_event.index')
                    ->orWhere('route_name', 'admin.news_and_event')
                    ->orWhere('source_key', 'route:admin.site-content.index')
                    ->orWhere(function ($urlQuery): void {
                        $urlQuery->where('url', 'like', '%/admin/news_and_Event')
                            ->orWhere('url', 'like', '%/admin/news_and_Event/%');
                    });
            })
            ->update([
                'label' => 'News & Event',
                'label_override' => null,
            ]);
    }

    public function down(): void
    {
        // One-way terminology correction; rollback intentionally keeps the current label.
    }
};
