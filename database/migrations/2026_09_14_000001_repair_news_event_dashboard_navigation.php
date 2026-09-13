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
                'url' => '/admin/news-and-Event',
                'route_name' => 'admin.news_and_event',
                'source_key' => 'route:admin.site-content.index',
                'source_type' => 'route',
            ]);
    }

    public function down(): void
    {
        NavigationMenuItem::query()
            ->where('menu', 'dashboard')
            ->where('route_name', 'admin.news_and_event')
            ->where('label', 'News & Event')
            ->update([
                'url' => '/admin/site-content?type=news',
                'route_name' => 'admin.site-content.index',
                'source_key' => 'route:admin.site-content.index',
            ]);
    }
};
