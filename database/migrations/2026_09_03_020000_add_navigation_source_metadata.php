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

        $columns = [
            'source_key' => fn (Blueprint $table) => $table->string('source_key', 255)->nullable()->index(),
            'source_type' => fn (Blueprint $table) => $table->string('source_type', 40)->default('custom'),
            'area' => fn (Blueprint $table) => $table->string('area', 20)->default('public')->index(),
            'permission_key' => fn (Blueprint $table) => $table->string('permission_key', 120)->nullable()->index(),
        ];

        foreach ($columns as $name => $definition) {
            if (! Schema::hasColumn('navigation_menu_items', $name)) {
                Schema::table('navigation_menu_items', $definition);
            }
        }

        if (Schema::hasColumn('navigation_menu_items', 'route_name')) {
            DB::table('navigation_menu_items')
                ->whereNull('source_key')
                ->whereNotNull('route_name')
                ->where('route_name', '!=', '')
                ->update([
                    'source_key' => DB::raw("CONCAT('route:', route_name)"),
                    'source_type' => 'route',
                    'area' => DB::raw("CASE WHEN menu = 'dashboard' THEN 'dashboard' ELSE 'public' END"),
                ]);
        }

        DB::table('navigation_menu_items')
            ->whereNull('source_key')
            ->where(function ($query) {
                $query->whereNull('url')->orWhere('url', '');
            })
            ->where(function ($query) {
                $query->whereNull('route_name')->orWhere('route_name', '');
            })
            ->update([
                'source_type' => 'folder',
                'area' => DB::raw("CASE WHEN menu = 'dashboard' THEN 'dashboard' ELSE 'public' END"),
            ]);
    }

    public function down(): void
    {
        if (! Schema::hasTable('navigation_menu_items')) {
            return;
        }

        foreach (['permission_key', 'area', 'source_type', 'source_key'] as $column) {
            if (Schema::hasColumn('navigation_menu_items', $column)) {
                Schema::table('navigation_menu_items', fn (Blueprint $table) => $table->dropColumn($column));
            }
        }
    }
};
