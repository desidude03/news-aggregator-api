<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schedule;


Schedule::command('news:newsApi')->everyMinute(); //only for news api
Schedule::command('news:nyTimes')->everyMinute(); //only for nyTimes
Schedule::command('news:guardian')->everyMinute(); //only for gaurdian
Schedule::command('guardian:fetchAll')->everySixHours(); //only to fetch all article from gaurdian

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote')->hourly();
