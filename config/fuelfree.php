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

    'storage' => [
        // Hosting plan capacity. This is a display/quota reference, not a PHP upload limit.
        'quota_bytes' => (int) env('FUELFREE_STORAGE_QUOTA_BYTES', 53687091200), // 50 GiB
    ],

    'roles' => [
        'super_admin' => 'super-admin',
        'administrator' => 'administrator',
        'project_manager' => 'project-manager',
        'support_agent' => 'support-agent',
        'client' => 'client',
    ],
];
