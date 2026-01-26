<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schedule;

/*
|--------------------------------------------------------------------------
| Artisan Commands
|--------------------------------------------------------------------------
*/

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');


/*
|--------------------------------------------------------------------------
| Scheduled Commands (Laravel 11)
|--------------------------------------------------------------------------
*/

// Repeat Daily / Weekly / Monthly tasks
Schedule::command('calendar:repeat')->daily();

// Reminder notification (every minute)
Schedule::command('calendar:reminder')->everyMinute();
