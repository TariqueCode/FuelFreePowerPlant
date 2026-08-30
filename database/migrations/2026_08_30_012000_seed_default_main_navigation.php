<?php

use App\Models\NavigationMenuItem;
use Illuminate\Database\Migrations\Migration;

return new class extends Migration
{
    public function up(): void
    {
        $topLevel = [
            ['label' => 'Home', 'route_name' => 'home', 'sort_order' => 0],
            ['label' => 'Company', 'group' => 'Company', 'sort_order' => 1],
            ['label' => 'Management Team', 'route_name' => 'management', 'sort_order' => 2],
            ['label' => 'Gallery', 'route_name' => 'site.gallery', 'sort_order' => 3],
            ['label' => 'News & Notices', 'route_name' => 'news.index', 'sort_order' => 4],
            ['label' => 'Career', 'route_name' => 'site.career', 'sort_order' => 5],
            ['label' => 'Contact', 'route_name' => 'contact', 'sort_order' => 6],
            ['label' => 'Webmail', 'route_name' => 'webmail.redirect', 'target' => '_blank', 'sort_order' => 7],
        ];

        foreach ($topLevel as $item) {
            NavigationMenuItem::query()->updateOrCreate(
                ['menu' => 'main', 'parent_id' => null, 'label' => $item['label']],
                $item + [
                    'menu' => 'main',
                    'target' => $item['target'] ?? '_self',
                    'is_visible' => true,
                ]
            );
        }

        $company = NavigationMenuItem::query()
            ->where('menu', 'main')
            ->whereNull('parent_id')
            ->where('label', 'Company')
            ->first();

        if (! $company) {
            return;
        }

        $children = [
            ['label' => 'About Us', 'route_name' => 'site.about', 'sort_order' => 0],
            ['label' => 'Our Plants', 'route_name' => 'site.plants', 'sort_order' => 1],
            ['label' => 'Future Project', 'route_name' => 'site.future-project', 'sort_order' => 2],
            ['label' => 'Solutions', 'route_name' => 'site.solutions', 'sort_order' => 3],
        ];

        foreach ($children as $item) {
            NavigationMenuItem::query()->updateOrCreate(
                ['menu' => 'main', 'parent_id' => $company->id, 'label' => $item['label']],
                $item + [
                    'menu' => 'main',
                    'parent_id' => $company->id,
                    'target' => '_self',
                    'is_visible' => true,
                ]
            );
        }
    }

    public function down(): void
    {
        NavigationMenuItem::query()->where('menu', 'main')->delete();
    }
};
