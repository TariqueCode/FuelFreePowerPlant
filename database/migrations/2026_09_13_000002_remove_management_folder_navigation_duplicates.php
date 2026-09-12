<?php

use App\Models\NavigationMenuItem;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        DB::transaction(function (): void {
            NavigationMenuItem::query()
                ->where('menu', 'main')
                ->where('source_key', 'like', 'management_folder:%')
                ->orderBy('id')
                ->get()
                ->each(function (NavigationMenuItem $item): void {
                    NavigationMenuItem::query()
                        ->where('menu', 'main')
                        ->where('parent_id', $item->id)
                        ->update(['parent_id' => $item->parent_id]);

                    $item->delete();
                });

            $managementItems = NavigationMenuItem::query()
                ->where('menu', 'main')
                ->where(function ($query): void {
                    $query->where('source_key', 'route:management')
                        ->orWhere('route_name', 'management');
                })
                ->orderBy('id')
                ->get();

            if ($managementItems->isNotEmpty()) {
                $primary = $managementItems->first();
                $primary->update([
                    'source_key' => 'route:management',
                    'source_type' => 'route',
                    'route_name' => 'management',
                    'url' => '/management',
                    'label' => 'Board of Directors',
                    'label_override' => null,
                    'permission_key' => null,
                    'area' => 'public',
                ]);

                $managementItems->skip(1)->each(function (NavigationMenuItem $duplicate): void {
                    NavigationMenuItem::query()
                        ->where('menu', 'main')
                        ->where('parent_id', $duplicate->id)
                        ->update(['parent_id' => $duplicate->parent_id]);
                    $duplicate->delete();
                });
            }
        });
    }

    public function down(): void
    {
        // The old management profile folder navigation entries are intentionally
        // not recreated: profile folders are data containers, not menu items.
    }
};
