<?php

return [
    'company' => [
        'name' => env('COMPANY_NAME', 'FuelFree PowerPlant'),
        'domain' => env('COMPANY_DOMAIN', 'fuelfreepowerplant.com'),
        'tagline' => env('COMPANY_TAGLINE', 'Powering a cleaner, smarter future.'),
        'timezone' => env('COMPANY_TIMEZONE', 'Asia/Dhaka'),
    ],

    'dashboard' => [
        'default_route' => env('DASHBOARD_ROUTE', 'dashboard'),
    ],

    'roles' => [
        'super_admin' => 'super-admin',
        'administrator' => 'administrator',
        'project_manager' => 'project-manager',
        'support_agent' => 'support-agent',
        'client' => 'client',
    ],
];
