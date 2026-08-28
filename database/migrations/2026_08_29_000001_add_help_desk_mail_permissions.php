<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        $now = now();

        foreach ([
            ['name' => 'View Help Desk mail', 'slug' => 'mail.view', 'description' => 'View connected mailboxes and incoming messages.'],
            ['name' => 'Manage Help Desk mail', 'slug' => 'mail.manage', 'description' => 'Connect mailboxes, send replies and manage mailbox settings.'],
        ] as $permission) {
            DB::table('permissions')->updateOrInsert(
                ['slug' => $permission['slug']],
                ['name' => $permission['name'], 'description' => $permission['description'], 'created_at' => $now, 'updated_at' => $now]
            );
        }

        $permissionIds = DB::table('permissions')->whereIn('slug', ['mail.view', 'mail.manage'])->pluck('id', 'slug');
        $roleIds = DB::table('roles')->whereIn('slug', ['super-admin', 'administrator'])->pluck('id', 'slug');

        foreach ($roleIds as $roleId) {
            foreach ($permissionIds as $permissionId) {
                DB::table('permission_role')->updateOrInsert([
                    'permission_id' => $permissionId,
                    'role_id' => $roleId,
                ], []);
            }
        }
    }

    public function down(): void
    {
        $permissionIds = DB::table('permissions')->whereIn('slug', ['mail.view', 'mail.manage'])->pluck('id');

        if ($permissionIds->isNotEmpty()) {
            DB::table('permission_role')->whereIn('permission_id', $permissionIds)->delete();
            DB::table('permissions')->whereIn('id', $permissionIds)->delete();
        }
    }
};
