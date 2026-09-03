<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (! Schema::hasTable('navigation_menu_items')) {
            return;
        }

        // Keep the oldest record for each live source/menu pair. Reparent its
        // children before removing later duplicates so the saved tree remains usable.
        if (Schema::hasColumn('navigation_menu_items', 'source_key')) {
            DB::table('navigation_menu_items')
                ->select('menu', 'source_key')
                ->whereNotNull('source_key')
                ->where('source_key', '!=', '')
                ->groupBy('menu', 'source_key')
                ->havingRaw('COUNT(*) > 1')
                ->get()
                ->each(function ($group): void {
                    $ids = DB::table('navigation_menu_items')
                        ->where('menu', $group->menu)
                        ->where('source_key', $group->source_key)
                        ->orderBy('id')
                        ->pluck('id');

                    $keepId = (int) $ids->first();

                    foreach ($ids->skip(1) as $duplicateId) {
                        DB::table('navigation_menu_items')
                            ->where('menu', $group->menu)
                            ->where('parent_id', $duplicateId)
                            ->update(['parent_id' => $keepId]);

                        DB::table('navigation_menu_items')
                            ->where('id', $duplicateId)
                            ->delete();
                    }
                });

            Schema::table('navigation_menu_items', function (Blueprint $table): void {
                $table->unique(['menu', 'source_key'], 'navigation_menu_items_menu_source_unique');
            });
        }
    }

    public function down(): void
    {
        if (! Schema::hasTable('navigation_menu_items')) {
            return;
        }

        try {
            Schema::table('navigation_menu_items', function (Blueprint $table): void {
                $table->dropUnique('navigation_menu_items_menu_source_unique');
            });
        } catch (\Throwable) {
            // Keep rollback idempotent across database drivers.
        }
    }
};
