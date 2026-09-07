<?php

use App\Models\NavigationMenuItem;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Add only the existing Company & About destination to the dashboard
     * Website section, immediately after Page Builder. Existing navigation
     * items and their destinations are left untouched.
     */
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

        $pageBuilder = NavigationMenuItem::query()
            ->where('menu', 'dashboard')
            ->where('area', 'dashboard')
            ->where('parent_id', $website->id)
            ->where(function ($query): void {
                $query
                    ->where('label', 'Page Builder')
                    ->orWhere('route_name', 'admin.page-builder.index')
                    ->orWhere('route_name', 'admin.cms.index')
                    ->orWhere('source_key', 'route:admin.page-builder.index')
                    ->orWhere('source_key', 'route:admin.cms.index');
            })
            ->orderBy('sort_order')
            ->orderBy('id')
            ->first();

        if (! $pageBuilder) {
            return;
        }

        $existing = NavigationMenuItem::query()
            ->where('menu', 'dashboard')
            ->where('area', 'dashboard')
            ->where('parent_id', $website->id)
            ->where(function ($query): void {
                $query
                    ->where('label', 'Company & About')
                    ->orWhere('url', '/admin/site-content?type=company');
            })
            ->first();

        if ($existing) {
            return;
        }

        NavigationMenuItem::query()
            ->where('menu', 'dashboard')
            ->where('area', 'dashboard')
            ->where('parent_id', $website->id)
            ->where('sort_order', '>', $pageBuilder->sort_order)
            ->increment('sort_order');

        NavigationMenuItem::create([
            'menu' => 'dashboard',
            'parent_id' => $website->id,
            'label' => 'Company & About',
            'url' => '/admin/site-content?type=company',
            'route_name' => null,
            'target' => '_self',
            'icon' => 'fa-building',
            'is_visible' => true,
            'sort_order' => $pageBuilder->sort_order + 1,
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
            ->where('label', 'Company & About')
            ->where('url', '/admin/site-content?type=company')
            ->delete();
    }
};
