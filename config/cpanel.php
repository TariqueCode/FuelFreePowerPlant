<?php

return [
    'enabled' => (bool) env('CPANEL_ENABLED', false),
    'host' => env('CPANEL_HOST'),
    'port' => (int) env('CPANEL_PORT', 2083),
    'username' => env('CPANEL_USERNAME'),
    'token' => env('CPANEL_API_TOKEN'),
    'domain' => env('CPANEL_EMAIL_DOMAIN', 'fuelfreepowerplant.com'),
    'quota' => (int) env('CPANEL_EMAIL_QUOTA', 0), // 0 = unlimited in cPanel.
    'mail_host' => env('CPANEL_MAIL_HOST', 'mail.fuelfreepowerplant.com'),
    'webmail_url' => env('CPANEL_WEBMAIL_URL', 'https://mail.fuelfreepowerplant.com'),
];
