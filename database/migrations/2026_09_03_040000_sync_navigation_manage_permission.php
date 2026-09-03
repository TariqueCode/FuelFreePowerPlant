<?php

use App\Models\Permission;
use App\Models\Role;
use Illuminate\Database\Migrations\Migration;

return new class extends Migration
{
    public function up(): void
    {
        $permission = Permission::firstOrCreate(
            ['slug' => 'navigation.manage'],
            ['name' => 'Manage website navigation']
        );

        // RolePermissionSeeder is authoritative for fresh installs, but existing
        // production databases do not get reseeded on deploy. Keep the live role
        // capability matrix in sync so navigation mutations do not return 403.
        foreach (['super-admin', 'administrator', 'project-manager'] as $slug) {
            $role = Role::where('slug', $slug)->first();

            if ($role && ! $role->permissions()->whereKey($permission->id)->exists()) {
                $role->permissions()->attach($permission->id);
            }
        }
    }

    public function down(): void
    {
        $permission = Permission::where('slug', 'navigation.manage')->first();

        if (! $permission) {
            return;
        }

        foreach (['super-admin', 'administrator', 'project-manager'] as $slug) {
            $role = Role::where('slug', $slug)->first();

            if ($role) {
                $role->permissions()->detach($permission->id);
            }
        }
    }
};
