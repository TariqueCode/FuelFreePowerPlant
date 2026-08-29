<?php

namespace App\Services;

use App\Models\GlobalLayoutSetting;
use Illuminate\Support\Facades\Cache;

class GlobalLayoutService
{
    public function all(string $scope = 'site'): array
    {
        return Cache::rememberForever("fuelfree.layout.all.{$scope}", function () use ($scope) {
            return GlobalLayoutSetting::query()
                ->where('scope', $scope)
                ->pluck('value', 'key')
                ->all();
        });
    }

    public function get(string $key, mixed $default = null, string $scope = 'site'): mixed
    {
        return $this->all($scope)[$key] ?? $default;
    }

    public function set(string $key, mixed $value, string $scope = 'site'): void
    {
        GlobalLayoutSetting::set($key, $value, $scope);
        Cache::forget("fuelfree.layout.all.{$scope}");
        Cache::forget("fuelfree.layout.{$scope}.{$key}");
    }
}
