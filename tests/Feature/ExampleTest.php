<?php

namespace Tests\Feature;

use Tests\TestCase;

class ExampleTest extends TestCase
{
    public function test_application_health_endpoint_is_available(): void
    {
        $this->get('/up')->assertSuccessful();
    }

    public function test_public_career_page_is_available(): void
    {
        $response = $this->get('/career');

        $this->assertContains($response->status(), [200, 301, 302, 303, 307, 308]);
    }
}
