<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        $now = now();

        $roles = [
            ['name'=>'Mail Manager','slug'=>'mail-manager','description'=>'Manage configured company mailboxes and mailbox operations.','is_system'=>true,'created_at'=>$now,'updated_at'=>$now],
            ['name'=>'Career Manager','slug'=>'career-manager','description'=>'Review and manage career applications and candidate records.','is_system'=>true,'created_at'=>$now,'updated_at'=>$now],
        ];

        foreach ($roles as $role) {
            DB::table('roles')->updateOrInsert(
                ['slug' => $role['slug']],
                $role
            );
        }

        $permissions = [
            ['name'=>'View mail','slug'=>'mail.view','description'=>'Access company mailboxes.','created_at'=>$now,'updated_at'=>$now],
            ['name'=>'Manage mail','slug'=>'mail.manage','description'=>'Add mailboxes and send/manage messages.','created_at'=>$now,'updated_at'=>$now],
            ['name'=>'View career applications','slug'=>'career.view','description'=>'View candidate applications.','created_at'=>$now,'updated_at'=>$now],
            ['name'=>'Manage career applications','slug'=>'career.manage','description'=>'Update candidate application status.','created_at'=>$now,'updated_at'=>$now],
        ];

        foreach ($permissions as $permission) {
            DB::table('permissions')->updateOrInsert(
                ['slug' => $permission['slug']],
                $permission
            );
        }

        $mailRole = DB::table('roles')->where('slug', 'mail-manager')->value('id');
        $careerRole = DB::table('roles')->where('slug', 'career-manager')->value('id');
        $superRole = DB::table('roles')->where('slug', 'super-admin')->value('id');

        $mailPermissions = DB::table('permissions')
            ->whereIn('slug', ['mail.view', 'mail.manage'])
            ->pluck('id');

        $careerPermissions = DB::table('permissions')
            ->whereIn('slug', ['career.view', 'career.manage'])
            ->pluck('id');

        // A role must exist before inserting into the non-nullable pivot table.
        if ($mailRole !== null) {
            foreach ($mailPermissions as $permissionId) {
                DB::table('permission_role')->updateOrInsert([
                    'permission_id' => $permissionId,
                    'role_id' => $mailRole,
                ], []);
            }
        }

        if ($careerRole !== null) {
            foreach ($careerPermissions as $permissionId) {
                DB::table('permission_role')->updateOrInsert([
                    'permission_id' => $permissionId,
                    'role_id' => $careerRole,
                ], []);
            }
        }

        if ($superRole !== null) {
            $allNew = $mailPermissions->merge($careerPermissions)->unique();

            foreach ($allNew as $permissionId) {
                DB::table('permission_role')->updateOrInsert([
                    'permission_id' => $permissionId,
                    'role_id' => $superRole,
                ], []);
            }
        }
    }

    public function down(): void
    {
        $roleIds = DB::table('roles')
            ->whereIn('slug', ['mail-manager', 'career-manager'])
            ->pluck('id');

        DB::table('permission_role')->whereIn('role_id', $roleIds)->delete();
        DB::table('role_user')->whereIn('role_id', $roleIds)->delete();
        DB::table('roles')->whereIn('id', $roleIds)->delete();

        $permissionIds = DB::table('permissions')
            ->whereIn('slug', ['mail.view', 'mail.manage', 'career.view', 'career.manage'])
            ->pluck('id');

        DB::table('permission_role')->whereIn('permission_id', $permissionIds)->delete();
        DB::table('permissions')->whereIn('id', $permissionIds)->delete();
    }
};
