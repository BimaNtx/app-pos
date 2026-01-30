<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schedule;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

/**
 * Schedule the model:prune command to run daily.
 * This will permanently delete soft-deleted transactions after 30 days.
 */
Schedule::command('model:prune')->daily();
