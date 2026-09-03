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
            'super-admin'=>['Super Admin','Full platform access.'],
            'administrator'=>['Administrator','Operational administration access.'],
            'mail-manager'=>['Mail Manager','Manage configured company mailboxes and mailbox operations.'],
            'career-manager'=>['Career Manager','Review and manage career applications and candidate records.'],
            'project-manager'=>['Project Manager','Project and client operations.'],
            'client'=>['Client','Client portal access.'],
        ];
        $permissions = [
            'dashboard.view'=>'View dashboard',
            'cms.view'=>'View CMS','cms.manage'=>'Manage CMS','cms.publish'=>'Publish CMS pages','website.view'=>'View website sections','website.manage'=>'Manage website sections','website.publish'=>'Publish website content',
            'users.view'=>'View users','users.manage'=>'Manage users','documents.view'=>'View documents','documents.manage'=>'Manage documents',
            'notifications.view'=>'View notifications','settings.manage'=>'Manage system settings','audit.view'=>'View audit log','health.view'=>'View system health',
            'inquiries.view'=>'View website inquiries','inquiries.manage'=>'Manage website inquiries',
            'mail.view'=>'View Help Desk mail','mail.manage'=>'Manage Help Desk mail',
            'social-media.manage'=>'Manage social media links',
            'navigation.manage'=>'Manage website navigation','career.view'=>'View career applications','career.manage'=>'Manage career applications',
        ];
        foreach ($roles as $slug => [$name,$description]) Role::updateOrCreate(['slug'=>$slug],['name'=>$name,'description'=>$description,'is_system'=>true]);
        $models=[]; foreach ($permissions as $slug=>$name) $models[$slug]=Permission::updateOrCreate(['slug'=>$slug],['name'=>$name]);
        Role::where('slug','super-admin')->firstOrFail()->permissions()->sync(array_values($models));
        Role::where('slug','mail-manager')->firstOrFail()->permissions()->sync(array_values(array_filter($models,fn($p,$s)=>in_array($s,['mail.view','mail.manage'],true),ARRAY_FILTER_USE_BOTH)));
        Role::where('slug','career-manager')->firstOrFail()->permissions()->sync(array_values(array_filter($models,fn($p,$s)=>in_array($s,['career.view','career.manage'],true),ARRAY_FILTER_USE_BOTH)));
        Role::where('slug','administrator')->firstOrFail()->permissions()->sync(array_values(array_filter($models,fn($p,$s)=>!in_array($s,['settings.manage','health.view'],true),ARRAY_FILTER_USE_BOTH)));
        Role::where('slug','project-manager')->firstOrFail()->permissions()->sync(array_values(array_filter($models,fn($p,$s)=>in_array($s,['dashboard.view','users.view','documents.view','documents.manage','inquiries.view','inquiries.manage','notifications.view','social-media.manage','navigation.manage','career.view'],true),ARRAY_FILTER_USE_BOTH)));
        Role::where('slug','client')->firstOrFail()->permissions()->sync(array_values(array_filter($models,fn($p,$s)=>in_array($s,['dashboard.view','documents.view','documents.manage','notifications.view'],true),ARRAY_FILTER_USE_BOTH)));
    }
}
