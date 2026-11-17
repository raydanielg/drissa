<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schedule;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

// Generate pre-filled monthly manager reports on the 1st of each month at 06:00 EAT
Schedule::command('reports:generate-monthly')
    ->monthlyOn(1, '06:00')
    ->timezone('Africa/Dar_es_Salaam')
    ->withoutOverlapping()
    ->appendOutputTo(storage_path('logs/monthly-reports.log'));
