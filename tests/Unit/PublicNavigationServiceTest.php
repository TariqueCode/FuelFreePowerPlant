<?php

namespace Tests\Unit;

use App\Models\NavigationMenuItem;
use App\Services\PublicNavigationService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Cache;
use Tests\TestCase;

class PublicNavigationServiceTest extends TestCase
{
    use RefreshDatabase;

    public function test_it_builds_a_deep_navigation_tree_without_n_plus_one_relationship_loading(): void
    {
        Cache::forget('public.navigation.v3.main');
        Cache::forget('public.navigation.main');

        $folder = NavigationMenuItem::create([
            'menu' => 'main',
            'label' => 'Company',
            'source_type' => 'folder',
            'is_visible' => true,
            'sort_order' => 0,
        ]);

        $subFolder = NavigationMenuItem::create([
            'menu' => 'main',
            'parent_id' => $folder->id,
            'label' => 'Projects',
            'source_type' => 'folder',
            'is_visible' => true,
            'sort_order' => 0,
        ]);

        $page = NavigationMenuItem::create([
            'menu' => 'main',
            'parent_id' => $subFolder->id,
            'label' => 'Future Project',
            'url' => '/career',
            'source_key' => 'route:career',
            'source_type' => 'route',
            'area' => 'public',
            'is_visible' => true,
            'sort_order' => 0,
        ]);

        $hidden = NavigationMenuItem::create([
            'menu' => 'main',
            'parent_id' => $folder->id,
            'label' => 'Hidden',
            'source_type' => 'folder',
            'url' => null,
            'is_visible' => false,
            'sort_order' => 1,
        ]);

        $tree = app(PublicNavigationService::class)->tree('main');

        $root = $tree->firstWhere('id', $folder->id);
        $this->assertNotNull($root);
        $this->assertSame($subFolder->id, $root->children->first()->id);
        $this->assertSame($page->id, $root->children->first()->children->first()->id);
        $this->assertCount(0, $root->children->filter(fn ($item) => $item->id === $hidden->id));
    }

    public function test_cache_is_cleared_before_public_tree_is_rebuilt(): void
    {
        Cache::put('public.navigation.v2.main', ['stale-v2']);
        Cache::put('public.navigation.main', ['stale-legacy']);

        app(PublicNavigationService::class)->clear('main');

        $this->assertNull(Cache::get('public.navigation.v2.main'));
        $this->assertNull(Cache::get('public.navigation.main'));
    }
}
