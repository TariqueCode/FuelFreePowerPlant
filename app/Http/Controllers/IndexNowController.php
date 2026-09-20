<?php

namespace App\Http\Controllers;

use App\Models\SystemSetting;
use Illuminate\Http\Response;

class IndexNowController extends Controller
{
    public function key(string $key): Response
    {
        $configuredKey = trim((string) SystemSetting::query()->where('key', 'seo.indexnow_key')->value('value'));

        abort_unless($configuredKey !== '' && hash_equals($configuredKey, $key), 404);

        return response($configuredKey, 200, [
            'Content-Type' => 'text/plain; charset=UTF-8',
            'Cache-Control' => 'public, max-age=3600',
            'X-Robots-Tag' => 'noindex, nofollow',
        ]);
    }
}
