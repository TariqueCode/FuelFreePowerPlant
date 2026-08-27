<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Document extends Model
{
    protected $fillable = [
        'user_id', 'folder_id', 'original_name', 'stored_name', 'disk',
        'path', 'mime_type', 'size', 'extension', 'share_token', 'share_enabled',
    ];

    public function getShareUrlAttribute(): ?string
    {
        return $this->share_enabled && $this->share_token ? route('documents.shared-download', $this->share_token) : null;
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function folder(): BelongsTo
    {
        return $this->belongsTo(DocumentFolder::class, 'folder_id');
    }

    public function getSizeLabelAttribute(): string
    {
        $size = (float) $this->size;
        foreach (['B', 'KB', 'MB', 'GB'] as $unit) {
            if ($size < 1024 || $unit === 'GB') {
                return number_format($size, $size >= 10 || $unit === 'B' ? 0 : 1).' '.$unit;
            }
            $size /= 1024;
        }
        return '0 B';
    }
}
