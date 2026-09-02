<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        DB::table('permissions')->updateOrInsert(
            ['slug' => 'website.publish'],
            ['name' => 'Publish website content', 'updated_at' => now(), 'created_at' => now()]
        );

        $permissionId = DB::table('permissions')
            ->where('slug', 'website.publish')
            ->value('id');

        $roleIds = DB::table('roles')
            ->whereIn('slug', ['super-admin', 'administrator'])
            ->pluck('id');

        foreach ($roleIds as $roleId) {
            DB::table('permission_role')->updateOrInsert(
                ['permission_id' => $permissionId, 'role_id' => $roleId],
                []
            );
        }
    }

    public function down(): void
    {
        $permissionId = DB::table('permissions')
            ->where('slug', 'website.publish')
            ->value('id');

        if (! $permissionId) {
            return;
        }

        DB::table('permission_role')
            ->where('permission_id', $permissionId)
            ->whereIn('role_id', DB::table('roles')->whereIn('slug', ['super-admin', 'administrator'])->pluck('id'))
            ->delete();

        DB::table('permissions')
            ->where('id', $permissionId)
            ->delete();
    }
};
