<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;

class FuelFreeServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        // Keep FuelFree application bindings here as the platform grows.
    }

    public function boot(): void
    {
        date_default_timezone_set(config('fuelfree.company.timezone', 'Asia/Dhaka'));
    }
}
