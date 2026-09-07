<?php

namespace Tests\Feature;

use App\Models\CmsPage;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class PageBuilderTest extends TestCase
{
    use RefreshDatabase;

    public function test_public_page_renders_global_cms_document_and_forces_global_shell(): void
    {
        $page = CmsPage::create([
            'title' => 'Global CMS QA Page',
            'slug' => 'global-cms-qa-page',
            'content' => '<h2>Global editor heading</h2><p><strong>Bold</strong> content with <a href="/contact">a link</a>.</p><div class="ff-columns cols-2"><div class="ff-column">Column one</div><div class="ff-column">Column two</div></div>',
            'excerpt' => 'A global CMS rendering check.',
            'is_published' => true,
            'builder_blocks' => [],
            'use_global_framework' => false,
            'use_global_header' => false,
            'use_global_footer' => false,
        ]);

        $response = $this->get(route('cms.page', $page->slug));

        $response->assertOk();
        $response->assertViewHas('useGlobalFramework', true);
        $response->assertViewHas('useGlobalHeader', true);
        $response->assertViewHas('useGlobalFooter', true);
        $response->assertSee('Global CMS QA Page');
        $response->assertSee('Global editor heading');
        $response->assertSee('<strong>Bold</strong>', false);
        $response->assertSee('Column one');
        $response->assertSee('Column two');
    }

    public function test_unpublished_page_is_not_public(): void
    {
        $page = CmsPage::create([
            'title' => 'Draft Page',
            'slug' => 'draft-page',
            'content' => '<p>Draft content</p>',
            'is_published' => false,
            'builder_blocks' => [],
        ]);

        $this->get(route('cms.page', $page->slug))->assertNotFound();
    }
}
