<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (!Schema::hasTable('navigation_menu_items')) {
            return;
        }

        $items = DB::table('navigation_menu_items')
            ->where('menu', 'main')
            ->where('source_type', 'folder')
            ->where('source_key', 'like', 'management_folder:%')
            ->get(['id', 'parent_id']);

        foreach ($items as $item) {
            DB::table('navigation_menu_items')
                ->where('menu', 'main')
                ->where('parent_id', $item->id)
                ->update(['parent_id' => $item->parent_id]);

            DB::table('navigation_menu_items')
                ->where('id', $item->id)
                ->update([
                    'source_type' => 'route',
                    'source_key' => 'route:management',
                    'route_name' => 'management',
                    'url' => '/management',
                    'label' => 'Board of Directors',
                    'label_override' => null,
                    'area' => 'public',
                    'permission_key' => null,
                ]);
        }
    }

    public function down(): void
    {
        if (!Schema::hasTable('navigation_menu_items')) {
            return;
        }

        DB::table('navigation_menu_items')
            ->where('menu', 'main')
            ->where('source_key', 'route:management')
            ->where('route_name', 'management')
            ->update([
                'source_type' => 'folder',
                'source_key' => null,
                'route_name' => null,
                'label' => 'Board of Directors',
            ]);
    }
};
