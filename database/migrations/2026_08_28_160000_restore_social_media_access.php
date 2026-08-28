<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        $permissionId = DB::table('permissions')
            ->where('slug', 'social-media.manage')
            ->value('id');

        if (! $permissionId) {
            $permissionId = DB::table('permissions')->insertGetId([
                'slug' => 'social-media.manage',
                'name' => 'Manage social media links',
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }

        $roleIds = DB::table('roles')
            ->whereIn('slug', ['super-admin', 'administrator', 'project-manager'])
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
            ->where('slug', 'social-media.manage')
            ->value('id');

        if (! $permissionId) {
            return;
        }

        DB::table('permission_role')
            ->where('permission_id', $permissionId)
            ->whereIn('role_id', DB::table('roles')->whereIn('slug', ['super-admin', 'administrator', 'project-manager'])->pluck('id'))
            ->delete();
    }
};
