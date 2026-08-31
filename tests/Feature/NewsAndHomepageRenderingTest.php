<?php

namespace Tests\Feature;

use App\Models\CmsPage;
use App\Models\HomepageSection;
use App\Models\SiteContentItem;
use App\Models\Permission;
use App\Models\Role;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class NewsAndHomepageRenderingTest extends TestCase
{
    use RefreshDatabase;

    public function test_public_news_index_renders_without_server_error(): void
    {
        SiteContentItem::create([
            'type' => 'news',
            'title' => 'QA News Item',
            'slug' => 'qa-news-item',
            'excerpt' => 'A test publication.',
            'content' => 'QA content.',
            'status' => 'published',
            'published_at' => now(),
        ]);

        $this->get(route('news.index'))
            ->assertOk()
            ->assertSee('News &amp; Notices', false)
            ->assertSee('QA News Item');
    }

    public function test_admin_news_index_renders_without_server_error(): void
    {
        $view = Permission::firstOrCreate(['slug' => 'website.view'], ['name' => 'View website']);
        $role = Role::create(['name' => 'News QA Admin', 'slug' => 'news-qa-admin', 'is_system' => false]);
        $role->permissions()->sync([$view->id]);
        $user = User::factory()->create();
        $user->roles()->attach($role);

        SiteContentItem::create([
            'type' => 'news',
            'title' => 'Admin QA News',
            'slug' => 'admin-qa-news',
            'status' => 'draft',
        ]);

        $this->actingAs($user)
            ->get(route('admin.site-content.index', ['type' => 'news']))
            ->assertOk()
            ->assertSee('News &amp; Notices', false)
            ->assertSee('Admin QA News');
    }

    public function test_homepage_renders_with_configured_sections_and_title(): void
    {
        HomepageSection::query()->delete();
        foreach ([
            ['key' => 'hero', 'label' => 'Hero & Slider', 'description' => 'Homepage hero', 'sort_order' => 0, 'is_enabled' => true],
            ['key' => 'welcome', 'label' => 'Company Introduction', 'description' => 'Welcome', 'sort_order' => 1, 'is_enabled' => true],
        ] as $section) {
            HomepageSection::create($section);
        }

        CmsPage::create([
            'title' => 'A Better Energy Future',
            'slug' => 'home',
            'content' => '<p>QA homepage content.</p>',
            'is_published' => true,
        ]);

        $this->get(route('home'))
            ->assertOk()
            ->assertSee('A Better Energy Future');
    }
}
