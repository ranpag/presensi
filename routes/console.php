<?php

use App\Services\PresensiService;
use Illuminate\Foundation\Inspiring;
use App\Console\Commands\ServerCommand;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Support\Facades\Log;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

Artisan::command('presensi:run', function () {
    Log::info('PresensiService berjalan pada ' . now());
    
    try {
        app(PresensiService::class)->generatePresensi();
        Log::info('Presensi berhasil dijalankan');
    } catch (\Exception $e) {
        Log::error('Gagal menjalankan presensi: ' . $e->getMessage());
    }
});

app(Schedule::class)->command('presensi:run')->dailyAt('05:30')->timezone('Asia/Jakarta');
app(Schedule::class)->command('presensi:run')->dailyAt('06:00')->timezone('Asia/Jakarta');
app(Schedule::class)->command('presensi:run')->dailyAt('06:36')->timezone('Asia/Jakarta');


Artisan::command('srv', function () {
    $command = new ServerCommand();

    $command->handle($this);
});

