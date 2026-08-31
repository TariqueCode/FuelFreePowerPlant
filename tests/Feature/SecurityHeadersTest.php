<?php

namespace Tests\Feature;

use App\Http\Middleware\SecurityHeaders;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use Tests\TestCase;

class SecurityHeadersTest extends TestCase
{
    public function test_html_responses_receive_hardened_security_headers(): void
    {
        $request = Request::create('/', 'GET');

        $response = (new SecurityHeaders())->handle(
            $request,
            static fn () => new Response('<!doctype html><html><head></head><body>ok</body></html>', 200, [
                'Content-Type' => 'text/html; charset=UTF-8',
            ])
        );

        $this->assertSame(200, $response->getStatusCode());
        $this->assertSame('nosniff', $response->headers->get('X-Content-Type-Options'));
        $this->assertSame('SAMEORIGIN', $response->headers->get('X-Frame-Options'));
        $this->assertSame('strict-origin-when-cross-origin', $response->headers->get('Referrer-Policy'));
        $this->assertSame('camera=(), microphone=(), geolocation=()', $response->headers->get('Permissions-Policy'));
    }
}
