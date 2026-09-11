<?php

use App\Models\ManagementProfileFolder;
use App\Models\NavigationMenuItem;
use Illuminate\Database\Migrations\Migration;

return new class extends Migration
{
    public function up(): void
    {
        $folders = ManagementProfileFolder::query()
            ->orderBy('sort_order')
            ->orderBy('id')
            ->get();

        foreach ($folders as $folder) {
            $sourceKey = 'management_folder:'.$folder->id;
            $url = '/'.ltrim((string) $folder->slug, '/');

            $items = NavigationMenuItem::query()
                ->where('menu', 'main')
                ->where(function ($query) use ($sourceKey, $url, $folder): void {
                    $query->where('source_key', $sourceKey)
                        ->orWhere('url', $url)
                        ->orWhere(function ($legacy) use ($folder): void {
                            $legacy->whereNull('source_key')
                                ->where('label', $folder->name);
                        })
                        ->orWhere(function ($legacy): void {
                            $legacy->where('route_name', 'management')
                                ->orWhere('source_key', 'route:management');
                        });
                })
                ->orderBy('id')
                ->get();

            $item = $items->first();
            if (! $item) {
                continue;
            }

            $item->update([
                'label' => $folder->name,
                'label_override' => null,
                'url' => $url,
                'route_name' => null,
                'source_key' => $sourceKey,
                'source_type' => 'folder',
                'permission_key' => null,
                'area' => 'public',
            ]);

            if ($items->count() > 1) {
                NavigationMenuItem::query()
                    ->whereIn('id', $items->skip(1)->pluck('id'))
                    ->delete();
            }
        }

        NavigationMenuItem::query()
            ->where('menu', 'main')
            ->where(function ($query): void {
                $query->where('source_key', 'route:management')
                    ->orWhere('route_name', 'management');
            })
            ->delete();
    }

    public function down(): void
    {
        // Navigation data is intentionally not rolled back: this migration repairs
        // legacy source metadata and deleting that repair would reintroduce ambiguity.
    }
};
