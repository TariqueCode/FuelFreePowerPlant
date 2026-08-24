<?php

namespace Database\Seeders;

use App\Models\Permission;
use App\Models\Role;
use Illuminate\Database\Seeder;

class RolePermissionSeeder extends Seeder
{
    public function run(): void
    {
        $roles = [
            'super-admin' => ['Super Admin', 'Full platform access.'],
            'administrator' => ['Administrator', 'Operational administration access.'],
            'project-manager' => ['Project Manager', 'Project and client operations.'],
            'support-agent' => ['Support Agent', 'Support and communication operations.'],
            'client' => ['Client', 'Client portal access.'],
        ];

        $permissions = [
            'dashboard.view' => 'View dashboard',
            'cms.view' => 'View CMS',
            'cms.manage' => 'Manage CMS',
            'users.view' => 'View users',
            'users.manage' => 'Manage users',
            'documents.view' => 'View documents',
            'documents.manage' => 'Manage documents',
            'email.view' => 'View email management',
            'email.manage' => 'Manage email accounts',
            'subdomains.view' => 'View subdomains',
            'subdomains.manage' => 'Manage subdomains',
            'support.view' => 'View support',
            'support.create' => 'Create support tickets',
            'support.reply' => 'Reply to support tickets',
            'support.manage' => 'Manage support',
            'notifications.view' => 'View notifications',
            'settings.manage' => 'Manage system settings',
        ];

        foreach ($roles as $slug => [$name, $description]) {
            Role::updateOrCreate(
                ['slug' => $slug],
                ['name' => $name, 'description' => $description, 'is_system' => true]
            );
        }

        $permissionModels = [];
        foreach ($permissions as $slug => $name) {
            $permissionModels[$slug] = Permission::updateOrCreate(
                ['slug' => $slug],
                ['name' => $name]
            );
        }

        Role::where('slug', 'super-admin')->firstOrFail()
            ->permissions()->sync(array_values($permissionModels));

        Role::where('slug', 'administrator')->firstOrFail()
            ->permissions()->sync(array_values(array_filter(
                $permissionModels,
                fn ($p, $slug) => !in_array($slug, ['settings.manage'], true),
                ARRAY_FILTER_USE_BOTH
            )));

        Role::where('slug', 'project-manager')->firstOrFail()
            ->permissions()->sync(array_values(array_filter(
                $permissionModels,
                fn ($p, $slug) => in_array($slug, [
                    'dashboard.view','users.view','documents.view','documents.manage',
                    'support.view','support.create','support.reply','support.manage',
                    'notifications.view'
                ], true),
                ARRAY_FILTER_USE_BOTH
            )));

        Role::where('slug', 'support-agent')->firstOrFail()
            ->permissions()->sync(array_values(array_filter(
                $permissionModels,
                fn ($p, $slug) => in_array($slug, [
                    'dashboard.view','support.view','support.reply','support.manage',
                    'notifications.view'
                ], true),
                ARRAY_FILTER_USE_BOTH
            )));

        Role::where('slug', 'client')->firstOrFail()
            ->permissions()->sync(array_values(array_filter(
                $permissionModels,
                fn ($p, $slug) => in_array($slug, [
                    'dashboard.view','documents.view','documents.manage','email.view','subdomains.view',
                    'support.view','support.create','support.reply','notifications.view'
                ], true),
                ARRAY_FILTER_USE_BOTH
            )));
    }
}
