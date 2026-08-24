<?php

namespace App\Support;

use Illuminate\Support\Facades\Storage;

class ChunkUpload
{
    public static function directory(int $userId, string $uploadId): string
    {
        return "private/{$userId}/.uploads/{$uploadId}";
    }

    public static function chunkName(int $index): string
    {
        return str_pad((string) $index, 8, '0', STR_PAD_LEFT).'.part';
    }

    public static function cleanup(int $userId, string $uploadId): void
    {
        Storage::disk('local')->deleteDirectory(self::directory($userId, $uploadId));
    }
}
