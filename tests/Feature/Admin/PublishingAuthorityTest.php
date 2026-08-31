<?php

namespace Tests\Feature\Admin;

use App\Models\Permission;
use App\Models\Role;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class PublishingAuthorityTest extends TestCase
{
    use RefreshDatabase;

    public function test_administrator_has_publish_authority_but_project_manager_does_not(): void
    {
        $publish = Permission::create(['name' => 'Publish website content', 'slug' => 'website.publish']);
        $websiteManage = Permission::create(['name' => 'Manage website sections', 'slug' => 'website.manage']);

        $administrator = Role::create(['name' => 'Administrator', 'slug' => 'administrator', 'is_system' => true]);
        $administrator->permissions()->sync([$publish->id, $websiteManage->id]);

        $projectManager = Role::create(['name' => 'Project Manager', 'slug' => 'project-manager', 'is_system' => true]);
        $projectManager->permissions()->sync([$websiteManage->id]);

        $admin = User::factory()->create();
        $admin->roles()->attach($administrator);

        $manager = User::factory()->create();
        $manager->roles()->attach($projectManager);

        $this->assertTrue($admin->hasPermission('website.publish'));
        $this->assertFalse($manager->hasPermission('website.publish'));
    }

    public function test_cms_publishing_is_a_distinct_permission(): void
    {
        $manage = Permission::create(['name' => 'Manage CMS', 'slug' => 'cms.manage']);
        $publish = Permission::create(['name' => 'Publish CMS pages', 'slug' => 'cms.publish']);

        $role = Role::create(['name' => 'Content Editor', 'slug' => 'content-editor', 'is_system' => false]);
        $role->permissions()->sync([$manage->id]);

        $user = User::factory()->create();
        $user->roles()->attach($role);

        $this->assertTrue($user->hasPermission('cms.manage'));
        $this->assertFalse($user->hasPermission('cms.publish'));
    }
}
