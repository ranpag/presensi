<?php

use App\Console\Commands\ServerCommand;
use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Console\Scheduling\Schedule;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

app(Schedule::class)->command('presensi:run')->dailyAt('05:30');
app(Schedule::class)->command('presensi:run')->dailyAt('06:00');
app(Schedule::class)->command('presensi:run')->dailyAt('06:30');

Artisan::command('srv', function () {
    $command = new ServerCommand();

    $command->handle($this);
});

