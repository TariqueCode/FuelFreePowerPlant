<?php

use App\Models\NavigationMenuItem;
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

        DB::transaction(function (): void {
            $website = NavigationMenuItem::query()
                ->where('menu', 'dashboard')
                ->where('area', 'dashboard')
                ->whereNull('parent_id')
                ->where(function ($query): void {
                    $query
                        ->where(function ($nested): void {
                            $nested->where('source_type', 'folder')
                                ->where(function ($source): void {
                                    $source->where('source_key', 'folder:website')
                                        ->orWhere('source_key', 'dashboard:website');
                                });
                        })
                        ->orWhere(function ($nested): void {
                            $nested->where('label', 'Website')
                                ->orWhere('label_override', 'Website');
                        });
                })
                ->orderBy('id')
                ->first();

            if (! $website) {
                $lastOrder = (int) (NavigationMenuItem::query()
                    ->where('menu', 'dashboard')
                    ->where('area', 'dashboard')
                    ->whereNull('parent_id')
                    ->max('sort_order') ?? -1);

                $website = NavigationMenuItem::create([
                    'menu' => 'dashboard',
                    'parent_id' => null,
                    'label' => 'Website',
                    'label_override' => null,
                    'url' => null,
                    'route_name' => null,
                    'target' => '_self',
                    'icon' => 'fa-globe',
                    'is_visible' => true,
                    'sort_order' => $lastOrder + 1,
                    'source_key' => 'folder:website',
                    'source_type' => 'folder',
                    'area' => 'dashboard',
                    'permission_key' => null,
                ]);
            }

            $footerItems = NavigationMenuItem::query()
                ->where('menu', 'dashboard')
                ->where('area', 'dashboard')
                ->where(function ($query): void {
                    $query
                        ->where('label', 'Footer Manager')
                        ->orWhere('label_override', 'Footer Manager')
                        ->orWhere('url', '/admin/footer')
                        ->orWhere('route_name', 'admin.footer.index')
                        ->orWhere('source_key', 'route:admin.footer.index');
                })
                ->orderBy('id')
                ->get();

            $footer = $footerItems->first(fn (NavigationMenuItem $item): bool => (int) $item->parent_id === (int) $website->id)
                ?? $footerItems->first();

            if (! $footer) {
                $lastChildOrder = (int) (NavigationMenuItem::query()
                    ->where('menu', 'dashboard')
                    ->where('area', 'dashboard')
                    ->where('parent_id', $website->id)
                    ->max('sort_order') ?? -1);

                $footer = NavigationMenuItem::create([
                    'menu' => 'dashboard',
                    'parent_id' => $website->id,
                    'label' => 'Footer Manager',
                    'label_override' => null,
                    'url' => '/admin/footer',
                    'route_name' => 'admin.footer.index',
                    'target' => '_self',
                    'icon' => 'fa-window-maximize',
                    'is_visible' => true,
                    'sort_order' => $lastChildOrder + 1,
                    'source_key' => 'route:admin.footer.index',
                    'source_type' => 'route',
                    'area' => 'dashboard',
                    'permission_key' => 'website.view',
                ]);
            } else {
                $footer->update([
                    'parent_id' => $website->id,
                    'label' => 'Footer Manager',
                    'label_override' => null,
                    'url' => '/admin/footer',
                    'route_name' => 'admin.footer.index',
                    'target' => '_self',
                    'icon' => 'fa-window-maximize',
                    'is_visible' => true,
                    'source_key' => 'route:admin.footer.index',
                    'source_type' => 'route',
                    'area' => 'dashboard',
                    'permission_key' => 'website.view',
                ]);
            }

            $duplicateIds = $footerItems
                ->filter(fn (NavigationMenuItem $item): bool => (int) $item->id !== (int) $footer->id)
                ->pluck('id');

            if ($duplicateIds->isNotEmpty()) {
                NavigationMenuItem::query()->whereIn('id', $duplicateIds)->delete();
            }
        });
    }

    public function down(): void
    {
        // Intentionally left empty: this is a production data-repair migration.
    }
};
