<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schedule;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

/*
|--------------------------------------------------------------------------
| Subscription Billing Scheduler
|--------------------------------------------------------------------------
|
| Run the billing dispatcher once every day.
|
| It will:
| - Dispatch daily usage aggregation jobs
| - Dispatch final-day aggregation jobs
| - Dispatch cycle-end invoice generation jobs
|
*/

Schedule::command('billing:dispatch')
    ->dailyAt('00:15');