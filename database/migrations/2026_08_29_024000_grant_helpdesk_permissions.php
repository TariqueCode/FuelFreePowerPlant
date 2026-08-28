<?php

use App\Models\Permission;
use App\Models\Role;
use Illuminate\Database\Migrations\Migration;

return new class extends Migration
{
    public function up(): void
    {
        $view = Permission::updateOrCreate(
            ['slug' => 'mail.view'],
            ['name' => 'View Help Desk']
        );

        $manage = Permission::updateOrCreate(
            ['slug' => 'mail.manage'],
            ['name' => 'Reply from Help Desk']
        );

        foreach (['super-admin', 'administrator', 'project-manager'] as $roleSlug) {
            $role = Role::where('slug', $roleSlug)->first();
            if (!$role) {
                continue;
            }

            $permissions = $role->permissions()->pluck('permissions.id')->all();
            $permissions[] = $view->id;

            if ($roleSlug !== 'project-manager') {
                $permissions[] = $manage->id;
            }

            $role->permissions()->syncWithoutDetaching(array_values(array_unique($permissions)));
        }
    }

    public function down(): void
    {
        $view = Permission::where('slug', 'mail.view')->first();
        $manage = Permission::where('slug', 'mail.manage')->first();

        foreach (['super-admin', 'administrator', 'project-manager'] as $roleSlug) {
            $role = Role::where('slug', $roleSlug)->first();
            if (!$role) continue;

            $remove = array_filter([$view?->id, $roleSlug !== 'project-manager' ? $manage?->id : null]);
            if ($remove) {
                $role->permissions()->detach($remove);
            }
        }
    }
};