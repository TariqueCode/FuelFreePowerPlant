<?php

use Illuminate\\Database\\Migrations\\Migration;
use Illuminate\\Support\\Facades\\DB;

return new class extends Migration
{
    public function up(): void
    {
        $permission = [
            'slug' => 'social-media.manage',
            'name' => 'Manage social media links',
        ];

        DB::table('permissions')->updateOrInsert(
            ['slug' => $permission['slug']],
            ['name' => $permission['name'], 'updated_at' => now(), 'created_at' => now()]
        );

        $permissionId = DB::table('permissions')->where('slug', $permission['slug'])->value('id');
        $roleIds = DB::table('roles')->whereIn('slug', ['super-admin', 'administrator'])->pluck('id');

        foreach ($roleIds as $roleId) {
            DB::table('permission_role')->updateOrInsert(
                ['permission_id' => $permissionId, 'role_id' => $roleId],
                []
            );
        }
    }

    public function down(): void
    {
        $permissionId = DB::table('permissions')->where('slug', 'social-media.manage')->value('id');

        if (! $permissionId) {
            return;
        }

        DB::table('permission_role')->where('permission_id', $permissionId)->delete();
        DB::table('permissions')->where('id', $permissionId)->delete();
    }
};
