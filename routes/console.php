<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schedule;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

Schedule::command('fuel-free:cleanup-upload-sessions --hours=24')->hourly();
Schedule::command('fuel-free:sync-helpdesk-mail --limit=50')->everyMinute()->withoutOverlapping(5);
