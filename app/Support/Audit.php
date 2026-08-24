<?php

namespace App\Support;

use App\Models\AuditLog;
use Illuminate\Http\Request;

final class Audit
{
    public static function record(
        Request $request,
        string $action,
        ?string $module = null,
        mixed $target = null,
        array $metadata = []
    ): void {
        $targetType = null;
        $targetId = null;

        if (is_object($target) && method_exists($target, 'getMorphClass')) {
            $targetType = $target->getMorphClass();
            $targetId = $target->getKey();
        }

        AuditLog::create([
            'user_id' => $request->user()?->id,
            'action' => $action,
            'module' => $module,
            'target_type' => $targetType,
            'target_id' => $targetId,
            'ip_address' => $request->ip(),
            'user_agent' => substr((string) $request->userAgent(), 0, 1000),
            'metadata' => self::sanitize($metadata),
        ]);
    }

    private static function sanitize(array $metadata): array
    {
        $sensitive = ['password', 'password_confirmation', 'token', 'secret', 'api_key', 'authorization'];
        foreach ($sensitive as $key) {
            unset($metadata[$key]);
        }
        return $metadata;
    }
}
