<?php

namespace App\Support;

final class Platform
{
    public static function name(): string
    {
        return config('fuelfree.company.name', 'FuelFree PowerPlant');
    }

    public static function domain(): string
    {
        return config('fuelfree.company.domain', 'fuelfreepowerplant.com');
    }

    public static function tagline(): string
    {
        return config('fuelfree.company.tagline', 'Powering a cleaner, smarter future.');
    }

    private function __construct()
    {
    }
}
