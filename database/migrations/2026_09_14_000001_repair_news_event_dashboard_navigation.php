<?php

use App\Models\NavigationMenuItem;
use Illuminate\Database\Migrations\Migration;

return new class extends Migration
{
    public function up(): void
    {
        NavigationMenuItem::query()
            ->where('menu', 'dashboard')
            ->where(function ($query): void {
                $query->where('route_name', 'admin.site-content.index')
                    ->orWhere('url', '/admin/site-content?type=news')
                    ->orWhere(function ($nested): void {
                        $nested->where('label', 'News & Event')
                            ->whereIn('route_name', ['admin.news_and_event', 'admin.news_and_event.index']);
                    });
            })
            ->update([
                'label' => 'News & Event',
                'url' => '/admin/site-content?type=news',
                'route_name' => 'admin.site-content.index',
                'source_key' => 'route:admin.site-content.index',
                'source_type' => 'route',
            ]);
    }

    public function down(): void
    {
        // Keep the canonical live route; there is no safe legacy route to restore.
    }
};
