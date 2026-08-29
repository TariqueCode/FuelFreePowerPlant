<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Cache;

class GlobalLayoutSetting extends Model
{
    protected $fillable = ['scope', 'key', 'value'];

    public static function get(string $key, mixed $default = null, string $scope = 'site'): mixed
    {
        $value = Cache::rememberForever("fuelfree.layout.{$scope}.{$key}", fn () => static::query()
            ->where('scope', $scope)
            ->where('key', $key)
            ->value('value'));

        return $value === null ? $default : $value;
    }

    protected static function booted(): void
    {
        static::saved(function (self $setting) {
            Cache::forget("fuelfree.layout.all.{$setting->scope}");
            Cache::forget("fuelfree.layout.{$setting->scope}.{$setting->key}");
        });
        static::deleted(function (self $setting) {
            Cache::forget("fuelfree.layout.all.{$setting->scope}");
            Cache::forget("fuelfree.layout.{$setting->scope}.{$setting->key}");
        });
    }

    public static function set(string $key, mixed $value, string $scope = 'site'): void
    {
        static::query()->updateOrCreate(
            ['scope' => $scope, 'key' => $key],
            ['value' => is_scalar($value) || $value === null ? $value : json_encode($value)]
        );

        Cache::forget("fuelfree.layout.{$scope}.{$key}");
    }
}
