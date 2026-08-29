<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Cache;

class SystemSetting extends Model
{
    protected static function booted(): void
    {
        static::saved(fn () => Cache::forget('fuelfree.system_settings'));
        static::deleted(fn () => Cache::forget('fuelfree.system_settings'));
    }
    protected $fillable = ['key', 'value', 'is_sensitive'];

    protected $casts = ['is_sensitive' => 'boolean'];
}
