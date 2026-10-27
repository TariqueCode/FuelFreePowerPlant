<?php

declare(strict_types=1);

use App\Models\Permission;
use App\Models\Role;
use App\Models\User;

require dirname(__DIR__, 2).'/vendor/autoload.php';

$app = require dirname(__DIR__, 2).'/bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

$slugs = [
    'dashboard.view',
    'website.view',
    'website.manage',
    'cms.view',
    'cms.manage',
    'cms.publish',
    'social-media.manage',
    'documents.view',
    'plants.view',
    'users.view',
    'users.manage',
    'audit.view',
    'health.view',
    'mail.view',
    'mail.manage',
    'career.view',
    'inquiries.view',
    'settings.manage',
    'navigation.manage',
];

$permissions = collect($slugs)->map(fn (string $slug) => Permission::firstOrCreate(
    ['slug' => $slug],
    ['name' => ucwords(str_replace(['.', '-'], ' ', $slug))]
));

$role = Role::updateOrCreate(
    ['slug' => 'navigation-browser-qa'],
    ['name' => 'Navigation Browser QA', 'description' => 'Temporary automated browser QA role.', 'is_system' => false]
);
$role->permissions()->sync($permissions->pluck('id')->all());

$user = User::updateOrCreate(
    ['email' => 'navigation-qa@example.test'],
    ['name' => 'Navigation Browser QA', 'password' => 'Navigation-QA-2026-10-27']
);
$user->roles()->sync([$role->id]);

fwrite(STDOUT, "Prepared isolated navigation browser QA account.\n");
