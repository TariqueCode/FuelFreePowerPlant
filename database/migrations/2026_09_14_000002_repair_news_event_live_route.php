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
                $query->where('label', 'News & Event')
                    ->orWhere('route_name', 'admin.news_and_event')
                    ->orWhere('route_name', 'admin.news_and_event.index')
                    ->orWhere('url', '/admin/news-and-Event');
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
        NavigationMenuItem::query()
            ->where('menu', 'dashboard')
            ->where('label', 'News & Event')
            ->where('route_name', 'admin.site-content.index')
            ->update([
                'url' => '/admin/site-content?type=news',
                'route_name' => 'admin.site-content.index',
                'source_key' => 'route:admin.site-content.index',
                'source_type' => 'route',
            ]);
    }
};
