<?php

namespace Tests\Feature\Admin;

use App\Models\CmsPage;
use App\Models\NavigationMenuItem;
use App\Models\Permission;
use App\Models\Role;
use App\Models\User;
use App\Services\DashboardNavigationService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Database\QueryException;
use Tests\TestCase;

class NavigationMenuIntegrityTest extends TestCase
{
    use RefreshDatabase;

    public function test_a_live_source_cannot_be_stored_twice_in_the_same_menu(): void
    {
        NavigationMenuItem::create([
            'menu' => 'main', 'parent_id' => null, 'label' => 'Home',
            'url' => '/__navigation-unique', 'route_name' => 'navigation.test.unique',
            'target' => '_self', 'is_visible' => true, 'sort_order' => 0,
            'source_key' => 'route:test.navigation.unique', 'source_type' => 'route', 'area' => 'public',
        ]);

        $this->expectException(QueryException::class);

        NavigationMenuItem::create([
            'menu' => 'main', 'parent_id' => null, 'label' => 'Home again',
            'url' => '/', 'route_name' => 'home', 'target' => '_self', 'is_visible' => true,
            'sort_order' => 1, 'source_key' => 'route:test.navigation.unique', 'source_type' => 'route', 'area' => 'public',
        ]);
    }

    private function navigationAdmin(): User
    {
        $view = Permission::firstOrCreate(['slug' => 'website.view'], ['name' => 'View website']);
        $manage = Permission::firstOrCreate(['slug' => 'navigation.manage'], ['name' => 'Manage navigation']);
        $cms = Permission::firstOrCreate(['slug' => 'cms.view'], ['name' => 'View CMS']);
        $role = Role::create(['name' => 'Navigation QA', 'slug' => 'navigation-qa', 'is_system' => false]);
        $role->permissions()->sync([$view->id, $manage->id, $cms->id]);
        $user = User::factory()->create();
        $user->roles()->attach($role);
        return $user;
    }

    public function test_folder_creation_does_not_fail_because_of_source_label_field(): void
    {
        $user = $this->navigationAdmin();
        $response = $this->actingAs($user)->post(route('admin.navigation.store'), [
            'menu' => 'main', 'kind' => 'folder', 'folder_label' => 'Company',
            'target' => '_self', 'is_visible' => '1',
        ]);
        $response->assertRedirect();
        $this->assertDatabaseHas('navigation_menu_items', [
            'menu' => 'main', 'source_type' => 'folder', 'label' => 'Company', 'source_key' => null,
        ]);
    }

    public function test_navigation_source_picker_does_not_expose_technical_route_type_suffix(): void
    {
        $user = $this->navigationAdmin();
        $this->actingAs($user)->get(route('admin.navigation.index', ['menu' => 'main']))
            ->assertOk()->assertDontSee(' · ROUTE')->assertDontSee(' · CMS_PAGE');
    }

    public function test_navigation_source_picker_hides_generated_placeholder_cms_labels(): void
    {
        $user = $this->navigationAdmin();
        CmsPage::create([
            'title' => 'Generated:: Fn M Fu Dkj Zz Nk Y L Gz',
            'slug' => 'generated-placeholder-navigation-test',
            'content' => '<p>test</p>',
            'is_published' => true,
        ]);

        $this->actingAs($user)->get(route('admin.navigation.index', ['menu' => 'main']))
            ->assertOk()->assertDontSee('Generated:: Fn M Fu Dkj Zz Nk Y L Gz');
    }

    public function test_live_source_label_can_be_customized_without_changing_its_destination(): void
    {
        $user = $this->navigationAdmin();

        // Use a test-owned published CMS page so the source is a genuine live
        // registry entry without colliding with seeded navigation data.
        $page = CmsPage::create([
            'title' => 'Navigation Label QA',
            'slug' => 'navigation-label-qa',
            'content' => '<p>test</p>',
            'is_published' => true,
        ]);

        $sourceKey = 'cms_page:'.$page->id;
        $item = NavigationMenuItem::create([
            'menu' => 'main', 'parent_id' => null, 'label' => 'Navigation Label QA',
            'url' => '/navigation-label-qa', 'route_name' => 'cms.page',
            'target' => '_self', 'is_visible' => true, 'sort_order' => 0,
            'source_key' => $sourceKey, 'source_type' => 'cms_page', 'area' => 'public',
        ]);

        $response = $this->actingAs($user)->patch(route('admin.navigation.update', $item), [
            'label' => 'Profile Builder',
            'parent_id' => '',
            'target' => '_self',
            'icon' => '',
            'is_visible' => '1',
        ]);

        $response->assertRedirect();
        $this->assertDatabaseHas('navigation_menu_items', [
            'id' => $item->id,
            'label' => 'Profile Builder',
            'url' => route('cms.page', ['slug' => $page->slug]),
            'route_name' => 'cms.page',
            'source_key' => $sourceKey,
            'source_type' => 'cms_page',
        ]);
    }

    public function test_canonical_builder_sources_resolve_to_real_dashboard_urls(): void
    {
        $user = $this->navigationAdmin();
        $this->actingAs($user);

        $registry = app(\App\Services\NavigationSourceRegistry::class);

        foreach ([
            'route:admin.profile-builder.index' => 'admin.profile-builder.index',
            'route:admin.page-builder.index' => 'admin.page-builder.index',
            'route:admin.menu-builder.index' => 'admin.menu-builder.index',
        ] as $sourceKey => $routeName) {
            $source = $registry->resolveAny($sourceKey, 'dashboard');
            $this->assertNotNull($source, $sourceKey);
            $this->assertSame($routeName, $source['route_name']);
            $this->assertSame(route($routeName), $source['url']);
        }

        NavigationMenuItem::create([
            'menu' => 'dashboard', 'parent_id' => null, 'label' => 'Profile Builder',
            'url' => route('admin.profile-builder.index'), 'route_name' => 'admin.profile-builder.index',
            'target' => '_self', 'is_visible' => true, 'sort_order' => 0,
            'source_key' => 'route:admin.profile-builder.index', 'source_type' => 'route', 'area' => 'dashboard',
        ]);

        $tree = app(DashboardNavigationService::class)->tree();
        $this->assertCount(1, $tree);
        $this->assertSame(route('admin.profile-builder.index'), $tree->first()->url);
    }

    public function test_the_same_source_can_exist_in_main_and_dashboard_menus(): void
    {
        foreach (['main', 'dashboard'] as $position => $menu) {
            NavigationMenuItem::create([
                'menu' => $menu, 'parent_id' => null, 'label' => 'Home', 'url' => '/', 'route_name' => 'home',
                'target' => '_self', 'is_visible' => true, 'sort_order' => $position,
                'source_key' => 'route:test.navigation.unique', 'source_type' => 'route',
                'area' => $menu === 'dashboard' ? 'dashboard' : 'public',
            ]);
        }

        $this->assertDatabaseHas('navigation_menu_items', ['menu' => 'main', 'source_key' => 'route:test.navigation.unique']);
        $this->assertDatabaseHas('navigation_menu_items', ['menu' => 'dashboard', 'source_key' => 'route:test.navigation.unique']);
        $this->assertSame(1, NavigationMenuItem::query()->where('menu', 'main')->where('source_key', 'route:test.navigation.unique')->count());
        $this->assertSame(1, NavigationMenuItem::query()->where('menu', 'dashboard')->where('source_key', 'route:test.navigation.unique')->count());
    }
}
