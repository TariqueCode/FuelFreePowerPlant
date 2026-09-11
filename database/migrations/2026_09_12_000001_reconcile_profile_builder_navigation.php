<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (! Schema::hasTable('management_profile_folders') || ! Schema::hasTable('navigation_menu_items')) {
            return;
        }

        $folders = DB::table('management_profile_folders')
            ->orderBy('sort_order')
            ->orderBy('id')
            ->get(['id', 'name', 'slug']);

        foreach ($folders as $folder) {
            $sourceKey = 'management_folder:'.$folder->id;
            $url = '/'.ltrim((string) $folder->slug, '/');

            $items = DB::table('navigation_menu_items')
                ->where('menu', 'main')
                ->where(function ($query) use ($sourceKey, $url, $folder): void {
                    $query->where('source_key', $sourceKey)
                        ->orWhere('url', $url)
                        ->orWhere(function ($legacy) use ($folder): void {
                            $legacy->whereNull('source_key')->where('label', $folder->name);
                        })
                        ->orWhere(function ($legacy): void {
                            $legacy->where('route_name', 'management')->orWhere('source_key', 'route:management');
                        });
                })
                ->orderBy('id')
                ->get(['id']);

            $item = $items->first();
            if (! $item) {
                continue;
            }

            $payload = [
                'label' => $folder->name,
                'url' => $url,
                'route_name' => null,
                'source_key' => $sourceKey,
                'source_type' => 'folder',
                'area' => 'public',
            ];
            if (Schema::hasColumn('navigation_menu_items', 'label_override')) {
                $payload['label_override'] = null;
            }
            if (Schema::hasColumn('navigation_menu_items', 'permission_key')) {
                $payload['permission_key'] = null;
            }

            DB::table('navigation_menu_items')->where('id', $item->id)->update($payload);

            if ($items->count() > 1) {
                DB::table('navigation_menu_items')->whereIn('id', $items->skip(1)->pluck('id')->all())->delete();
            }
        }

        DB::table('navigation_menu_items')
            ->where('menu', 'main')
            ->where(function ($query): void {
                $query->where('source_key', 'route:management')->orWhere('route_name', 'management');
            })
            ->delete();
    }

    public function down(): void
    {
        // Navigation metadata is intentionally not rolled back; this migration
        // repairs legacy source data and rolling it back would restore ambiguity.
    }
};
