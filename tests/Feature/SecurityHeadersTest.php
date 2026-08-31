<?php

namespace Tests\Feature;

use Illuminate\Support\Facades\Cache;
use Tests\TestCase;

class SecurityHeadersTest extends TestCase
{
    public function test_html_responses_receive_hardened_security_headers(): void
    {
        config(['fuelfree.company.logo_path' => null]);
        Cache::forget('fuelfree.system_settings');

        $response = $this->get(route('home'));

        $response->assertOk()
            ->assertHeader('X-Content-Type-Options', 'nosniff')
            ->assertHeader('X-Frame-Options', 'SAMEORIGIN')
            ->assertHeader('Referrer-Policy', 'strict-origin-when-cross-origin')
            ->assertHeader('Permissions-Policy', 'camera=(), microphone=(), geolocation=()');
    }
}
