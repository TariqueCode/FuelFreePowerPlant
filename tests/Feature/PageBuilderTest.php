<?php

namespace Tests\Feature;

use App\Models\CmsPage;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class PageBuilderTest extends TestCase
{
    use RefreshDatabase;

    public function test_public_page_renders_structured_sections_and_forces_global_shell(): void
    {
        $page = CmsPage::create([
            'title' => 'Builder QA Page',
            'slug' => 'builder-qa-page',
            'excerpt' => 'A page-builder rendering check.',
            'is_published' => true,
            'builder_blocks' => [
                ['type' => 'hero', 'eyebrow' => 'TEST', 'title' => 'Hero section', 'content' => '<p>Safe builder content.</p>', 'tone' => 'accent', 'visible' => true],
                ['type' => 'stats', 'title' => 'Metrics', 'items' => [['value' => '100%', 'label' => 'Clean'], ['value' => '24/7', 'label' => 'Ready']], 'visible' => true],
            ],
            'use_global_framework' => false,
            'use_global_header' => false,
            'use_global_footer' => false,
        ]);

        $response = $this->get(route('cms.page', $page->slug));

        $response->assertOk();
        $response->assertViewHas('useGlobalFramework', true);
        $response->assertViewHas('useGlobalHeader', true);
        $response->assertViewHas('useGlobalFooter', true);
        $response->assertSee('Builder QA Page');
        $response->assertSee('Hero section');
        $response->assertSee('Metrics');
    }

    public function test_unpublished_page_is_not_public(): void
    {
        $page = CmsPage::create([
            'title' => 'Draft Page',
            'slug' => 'draft-page',
            'is_published' => false,
            'builder_blocks' => [],
        ]);

        $this->get(route('cms.page', $page->slug))->assertNotFound();
    }
}
