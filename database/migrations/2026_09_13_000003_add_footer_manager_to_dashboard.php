<?php

use App\Models\NavigationMenuItem;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (! Schema::hasTable('navigation_menu_items')) {
            return;
        }

        $website = NavigationMenuItem::query()
            ->where('menu', 'dashboard')
            ->where('area', 'dashboard')
            ->whereNull('parent_id')
            ->where('source_type', 'folder')
            ->where('label', 'Website')
            ->first();

        if (! $website) {
            return;
        }

        $existing = NavigationMenuItem::query()
            ->where('menu', 'dashboard')
            ->where('area', 'dashboard')
            ->where('parent_id', $website->id)
            ->where(function ($query): void {
                $query->where('label', 'Footer Manager')
                    ->orWhere('url', '/admin/footer');
            })
            ->first();

        if ($existing) {
            return;
        }

        $lastOrder = (int) (NavigationMenuItem::query()
            ->where('menu', 'dashboard')
            ->where('area', 'dashboard')
            ->where('parent_id', $website->id)
            ->max('sort_order') ?? -1);

        NavigationMenuItem::create([
            'menu' => 'dashboard',
            'parent_id' => $website->id,
            'label' => 'Footer Manager',
            'url' => '/admin/footer',
            'route_name' => 'admin.footer.index',
            'target' => '_self',
            'icon' => 'fa-window-maximize',
            'is_visible' => true,
            'sort_order' => $lastOrder + 1,
            'source_key' => null,
            'source_type' => 'external_link',
            'area' => 'dashboard',
            'permission_key' => 'website.view',
        ]);
    }

    public function down(): void
    {
        if (! Schema::hasTable('navigation_menu_items')) {
            return;
        }

        NavigationMenuItem::query()
            ->where('menu', 'dashboard')
            ->where('area', 'dashboard')
            ->where('url', '/admin/footer')
            ->where('label', 'Footer Manager')
            ->delete();
    }
};
