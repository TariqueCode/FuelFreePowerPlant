<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        $permissions = [
            'website.view' => 'View website sections',
            'website.manage' => 'Manage website sections',
        ];
        foreach ($permissions as $slug => $name) {
            DB::table('permissions')->updateOrInsert(
                ['slug' => $slug],
                ['name' => $name, 'updated_at' => now(), 'created_at' => now()]
            );
        }

        $permissionIds = DB::table('permissions')->whereIn('slug', array_keys($permissions))->pluck('id', 'slug');
        $roleIds = DB::table('roles')->whereIn('slug', ['super-admin', 'administrator'])->pluck('id', 'slug');
        foreach ($roleIds as $roleId) {
            foreach ($permissionIds as $permissionId) {
                DB::table('permission_role')->updateOrInsert(['permission_id' => $permissionId, 'role_id' => $roleId], []);
            }
        }
    }

    public function down(): void
    {
        $permissionIds = DB::table('permissions')->whereIn('slug', ['website.view', 'website.manage'])->pluck('id');
        if ($permissionIds->isEmpty()) return;
        DB::table('permission_role')->whereIn('permission_id', $permissionIds)->delete();
        DB::table('permissions')->whereIn('id', $permissionIds)->delete();
    }
};
