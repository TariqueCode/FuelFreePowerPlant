<?php

use App\Models\NavigationMenuItem;
use App\Models\SiteContentItem;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (Schema::hasTable('site_content_items')) {
            SiteContentItem::query()
                ->whereIn('type', ['resource', 'resources'])
                ->delete();
        }

        if (Schema::hasTable('navigation_menu_items')) {
            NavigationMenuItem::query()
                ->where(function ($query): void {
                    $query->where('label', 'like', '%resource%')
                        ->orWhere('route_name', 'like', 'resources.%')
                        ->orWhere('source_key', 'like', 'route:resources.%')
                        ->orWhere('url', '/resources')
                        ->orWhere('url', 'like', '/resources/%');
                })
                ->delete();
        }
    }

    public function down(): void
    {
        // Legacy Resources are intentionally not restored.
    }
};
