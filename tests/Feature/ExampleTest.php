<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ExampleTest extends TestCase
{
    use RefreshDatabase;

    public function test_public_homepage_is_available(): void
    {
        $this->get('/')->assertSuccessful();
    }


    public function test_public_sitemap_is_available_and_xml(): void
    {
        $response = $this->get('/sitemap.xml');

        $response->assertOk();
        $response->assertHeader('Content-Type', 'application/xml; charset=UTF-8');
        $this->assertStringContainsString('<urlset', $response->getContent());
        $this->assertStringContainsString('/about-us', $response->getContent());
    }

    public function test_public_career_page_is_available(): void
    {
        $response = $this->get('/career');

        $this->assertContains($response->status(), [200, 301, 302, 303, 307, 308]);
    }
}
