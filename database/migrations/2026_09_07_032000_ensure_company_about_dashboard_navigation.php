<?php

use App\Models\NavigationMenuItem;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Final idempotent repair for the dashboard Website navigation.
     *
     * Earlier repair migrations could legitimately return early when the
     * expected Website/Page Builder records were not present yet. This
     * migration deliberately does not depend on Page Builder being found in
     * order to create the missing Company & About entry. If Page Builder is
     * present, the entry is placed immediately after it; otherwise it is
     * appended safely to the Website section.
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
            ->where(function ($query): void {
                $query
                    ->where('label', 'Website')
                    ->orWhere('source_key', 'folder:website')
                    ->orWhere('source_key', 'dashboard:website');
            })
            ->orderBy('id')
            ->first();

        if (! $website) {
            return;
        }

        $item = NavigationMenuItem::query()
            ->where('menu', 'dashboard')
            ->where('area', 'dashboard')
            ->where('parent_id', $website->id)
            ->where(function ($query): void {
                $query
                    ->where('label', 'Company & About')
                    ->orWhere('label_override', 'Company & About')
                    ->orWhere('url', '/admin/site-content')
                    ->orWhere('url', '/admin/site-content?type=company')
                    ->orWhere('source_key', 'route:admin.site-content.index');
            })
            ->orderBy('id')
            ->first();

        $pageBuilder = NavigationMenuItem::query()
            ->where('menu', 'dashboard')
            ->where('area', 'dashboard')
            ->where('parent_id', $website->id)
            ->where(function ($query): void {
                $query
                    ->where('label', 'Page Builder')
                    ->orWhere('label_override', 'Page Builder')
                    ->orWhere('route_name', 'admin.page-builder.index')
                    ->orWhere('route_name', 'admin.cms.index')
                    ->orWhere('source_key', 'route:admin.page-builder.index')
                    ->orWhere('source_key', 'route:admin.cms.index');
            })
            ->orderBy('sort_order')
            ->orderBy('id')
            ->first();

        if (! $item) {
            $sortOrder = $pageBuilder
                ? $pageBuilder->sort_order + 1
                : ((int) NavigationMenuItem::query()
                    ->where('menu', 'dashboard')
                    ->where('area', 'dashboard')
                    ->where('parent_id', $website->id)
                    ->max('sort_order')) + 1;

            NavigationMenuItem::query()
                ->where('menu', 'dashboard')
                ->where('area', 'dashboard')
                ->where('parent_id', $website->id)
                ->when($pageBuilder, fn ($query) => $query->where('sort_order', '>', $pageBuilder->sort_order))
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
                'sort_order' => $sortOrder,
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
                $oldSortOrder = $item->sort_order;
                $newSortOrder = $pageBuilder->sort_order + 1;

                NavigationMenuItem::query()
                    ->where('menu', 'dashboard')
                    ->where('area', 'dashboard')
                    ->where('parent_id', $website->id)
                    ->where('id', '!=', $item->id)
                    ->where('sort_order', '>', $oldSortOrder)
                    ->where('sort_order', '<=', $newSortOrder)
                    ->decrement('sort_order');

                $item->update(['sort_order' => $newSortOrder]);
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
            ->where('label', 'Company & About')
            ->where('source_key', 'route:admin.site-content.index')
            ->delete();
    }
};
