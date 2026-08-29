<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class GlobalLayoutSetting extends Model
{
    protected $fillable = ['scope', 'key', 'value'];

    public static function get(string $key, mixed $default = null, string $scope = 'site'): mixed
    {
        $value = static::query()
            ->where('scope', $scope)
            ->where('key', $key)
            ->value('value');

        return $value === null ? $default : $value;
    }

    public static function set(string $key, mixed $value, string $scope = 'site'): void
    {
        static::query()->updateOrCreate(
            ['scope' => $scope, 'key' => $key],
            ['value' => is_scalar($value) || $value === null ? $value : json_encode($value)]
        );
    }
}
