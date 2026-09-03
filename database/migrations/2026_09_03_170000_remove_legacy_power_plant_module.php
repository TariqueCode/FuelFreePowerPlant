<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Remove the module's persisted navigation entries before its routes disappear.
        if (Schema::hasTable('navigation_menu_items')) {
            DB::table('navigation_menu_items')
                ->where(function ($query): void {
                    $query->where('source_key', 'like', 'route:admin.plants.%')
                        ->orWhere('route_name', 'like', 'admin.plants.%')
                        ->orWhere('url', '/admin/plants')
                        ->orWhere('url', 'like', '/admin/plants/%');
                })
                ->delete();
        }

        // Projects/statistics are no longer homepage capabilities; Page Builder/content
        // modules are the canonical way to publish homepage content.
        if (Schema::hasTable('homepage_sections')) {
            DB::table('homepage_sections')
                ->whereIn('key', ['projects', 'statistics'])
                ->delete();
        }

        // Remove obsolete plant permissions and their role assignments.
        if (Schema::hasTable('permissions')) {
            $permissionIds = DB::table('permissions')
                ->whereIn('slug', ['plants.view', 'plants.manage'])
                ->pluck('id');

            if ($permissionIds->isNotEmpty() && Schema::hasTable('permission_role')) {
                DB::table('permission_role')->whereIn('permission_id', $permissionIds)->delete();
            }

            DB::table('permissions')->whereIn('slug', ['plants.view', 'plants.manage'])->delete();
        }

        // The plant register and performance register have no remaining runtime owner.
        Schema::dropIfExists('plant_performance');
        Schema::dropIfExists('power_plants');
    }

    public function down(): void
    {
        // This removal is intentional. The former module is not restored by rollback.
    }
};
