<?php

return [
    'company' => [
        'name' => env('COMPANY_NAME', 'FuelFree PowerPlant'),
        'domain' => env('COMPANY_DOMAIN', 'fuelfreepowerplant.com'),
        'tagline' => env('COMPANY_TAGLINE', 'Powering a cleaner, smarter future.'),
        'timezone' => env('COMPANY_TIMEZONE', 'Asia/Dhaka'),
    ],

    'header' => [
        'home_label' => 'Home',
        'management_label' => 'Management Team',
        'gallery_label' => 'Gallery',
        'news_label' => 'News & Notices',
        'career_label' => 'Career',
        'contact_label' => 'Contact',
        'webmail_label' => 'Webmail',
        'portal_label' => 'Portal',
        'login_label' => 'Login',
    ],

    'footer' => [
        'tagline' => 'Powering a cleaner, smarter future.',
        'technology' => 'Fuel-Free Flywheel-Based Clean Energy Technology',
        'office_heading' => 'Office',
        'address' => 'House-141, 3rd Floor, Road-22, Mohakhali DOHS, Dhaka-1206, Bangladesh',
        'contact_heading' => 'Contact',
        'email' => 'info@fuelfreepowerplant.com',
        'phone' => '+880 1712-251892',
        'website' => 'www.fuelfreepowerplant.com',
        'website_url' => 'https://www.fuelfreepowerplant.com',
        'get_in_touch_label' => 'Get in touch',
        'get_in_touch_url' => '/contact',
        'copyright_text' => 'All rights reserved.',
        'developer_prefix' => 'Developed by',
        'developer_name' => 'Saif Al-Islam',
        'developer_email' => 'TariqueBN@gmail.com',
    ],

    'dashboard' => [
        'default_route' => env('DASHBOARD_ROUTE', 'dashboard'),
    ],

    'storage' => [
        'quota_bytes' => (int) env('FUELFREE_STORAGE_QUOTA_BYTES', 53687091200),
    ],

    'upload' => [
        // Application-level defaults. Admin System Settings can override these per module.
        'max_mb' => (int) env('FUELFREE_UPLOAD_MAX_MB', 50),
        'career_max_mb' => (int) env('FUELFREE_CAREER_UPLOAD_MAX_MB', 50),
        'documents_max_mb' => (int) env('FUELFREE_DOCUMENTS_UPLOAD_MAX_MB', 50),
        'gallery_max_mb' => (int) env('FUELFREE_GALLERY_UPLOAD_MAX_MB', 50),
        'sliders_max_mb' => (int) env('FUELFREE_SLIDERS_UPLOAD_MAX_MB', 50),
    ],

    'roles' => [
        'super_admin' => 'super-admin',
        'administrator' => 'administrator',
        'project_manager' => 'project-manager',
        'support_agent' => 'support-agent',
        'client' => 'client',
    ],

    'social' => [
        'platforms' => [
            'facebook' => ['label'=>'Facebook','icon'=>'fa-brands fa-facebook-f','color'=>'#1877F2'],
            'instagram' => ['label'=>'Instagram','icon'=>'fa-brands fa-instagram','color'=>'#E4405F'],
            'youtube' => ['label'=>'YouTube','icon'=>'fa-brands fa-youtube','color'=>'#FF0000'],
            'linkedin' => ['label'=>'LinkedIn','icon'=>'fa-brands fa-linkedin-in','color'=>'#0A66C2'],
            'x' => ['label'=>'X','icon'=>'fa-brands fa-x-twitter','color'=>'#FFFFFF'],
            'tiktok' => ['label'=>'TikTok','icon'=>'fa-brands fa-tiktok','color'=>'#00F2EA'],
            'telegram' => ['label'=>'Telegram','icon'=>'fa-brands fa-telegram','color'=>'#229ED9'],
            'whatsapp' => ['label'=>'WhatsApp','icon'=>'fa-brands fa-whatsapp','color'=>'#25D366'],
            'messenger' => ['label'=>'Messenger','icon'=>'fa-brands fa-facebook-messenger','color'=>'#0099FF'],
            'threads' => ['label'=>'Threads','icon'=>'fa-brands fa-threads','color'=>'#FFFFFF'],
            'pinterest' => ['label'=>'Pinterest','icon'=>'fa-brands fa-pinterest-p','color'=>'#E60023'],
            'reddit' => ['label'=>'Reddit','icon'=>'fa-brands fa-reddit-alien','color'=>'#FF4500'],
            'github' => ['label'=>'GitHub','icon'=>'fa-brands fa-github','color'=>'#FFFFFF'],
            'discord' => ['label'=>'Discord','icon'=>'fa-brands fa-discord','color'=>'#5865F2'],
            'twitch' => ['label'=>'Twitch','icon'=>'fa-brands fa-twitch','color'=>'#9146FF'],
            'vimeo' => ['label'=>'Vimeo','icon'=>'fa-brands fa-vimeo-v','color'=>'#1AB7EA'],
            'medium' => ['label'=>'Medium','icon'=>'fa-brands fa-medium','color'=>'#FFFFFF'],
            'snapchat' => ['label'=>'Snapchat','icon'=>'fa-brands fa-snapchat','color'=>'#FFFC00'],
            'spotify' => ['label'=>'Spotify','icon'=>'fa-brands fa-spotify','color'=>'#1DB954'],
            'skype' => ['label'=>'Skype','icon'=>'fa-brands fa-skype','color'=>'#00AFF0'],
            'wechat' => ['label'=>'WeChat','icon'=>'fa-brands fa-weixin','color'=>'#09B83E'],
            'vk' => ['label'=>'VK','icon'=>'fa-brands fa-vk','color'=>'#0077FF'],
            'tumblr' => ['label'=>'Tumblr','icon'=>'fa-brands fa-tumblr','color'=>'#36465D'],
            'flickr' => ['label'=>'Flickr','icon'=>'fa-brands fa-flickr','color'=>'#FF0084'],
            'quora' => ['label'=>'Quora','icon'=>'fa-brands fa-quora','color'=>'#B92B27'],
            'website' => ['label'=>'Website','icon'=>'fa-solid fa-globe','color'=>'#51D8F0'],
        ],
    ],
];