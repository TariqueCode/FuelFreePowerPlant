<?php

use App\Models\NavigationMenuItem;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Repair the Company & About dashboard item so the live dashboard
     * navigation service recognizes it as a permitted route source.
     *
     * The previous migration intentionally added the destination as an
     * external link, but DashboardNavigationService only renders folders
     * and resolvable source-backed items. This migration converts that one
     * item to the existing admin.site-content.index route source without
     * changing any other dashboard navigation entry.
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

        $item = NavigationMenuItem::query()
            ->where('menu', 'dashboard')
            ->where('area', 'dashboard')
            ->where('parent_id', $website->id)
            ->where(function ($query): void {
                $query
                    ->where('label', 'Company & About')
                    ->orWhere('url', '/admin/site-content?type=company')
                    ->orWhere('source_key', 'route:admin.site-content.index');
            })
            ->orderBy('id')
            ->first();

        if (! $item) {
            if (! $pageBuilder) {
                return;
            }

            NavigationMenuItem::query()
                ->where('menu', 'dashboard')
                ->where('area', 'dashboard')
                ->where('parent_id', $website->id)
                ->where('sort_order', '>', $pageBuilder->sort_order)
                ->increment('sort_order');

            $item = NavigationMenuItem::create([
                'menu' => 'dashboard',
                'parent_id' => $website->id,
                'label' => 'Company & About',
                'label_override' => 'Company & About',
                'url' => '/admin/site-content',
                'route_name' => 'admin.site-content.index',
                'target' => '_self',
                'icon' => 'fa-building',
                'is_visible' => true,
                'sort_order' => $pageBuilder->sort_order + 1,
                'source_key' => 'route:admin.site-content.index',
                'source_type' => 'route',
                'area' => 'dashboard',
                'permission_key' => 'website.view',
            ]);
        } else {
            $item->update([
                'label' => 'Company & About',
                'label_override' => 'Company & About',
                'url' => '/admin/site-content',
                'route_name' => 'admin.site-content.index',
                'target' => '_self',
                'icon' => 'fa-building',
                'is_visible' => true,
                'source_key' => 'route:admin.site-content.index',
                'source_type' => 'route',
                'permission_key' => 'website.view',
            ]);

            if ($pageBuilder && $item->sort_order <= $pageBuilder->sort_order) {
                $item->update(['sort_order' => $pageBuilder->sort_order + 1]);
                NavigationMenuItem::query()
                    ->where('menu', 'dashboard')
                    ->where('area', 'dashboard')
                    ->where('parent_id', $website->id)
                    ->where('id', '!=', $item->id)
                    ->where('sort_order', '>', $pageBuilder->sort_order)
                    ->where('sort_order', '<=', $item->sort_order)
                    ->decrement('sort_order');
            }
        }
    }

    public function down(): void
    {
        if (! Schema::hasTable('navigation_menu_items')) {
            return;
        }

        NavigationMenuItem::query()
            ->where('menu', 'dashboard')
            ->where('area', 'dashboard')
            ->where('source_key', 'route:admin.site-content.index')
            ->where('label', 'Company & About')
            ->delete();
    }
};
