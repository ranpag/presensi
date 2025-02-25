<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Services\PresensiService;

class PresensiJobCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'presensi:run';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Menjalankan service presensi sebagai cron job';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        app(PresensiService::class)->generatePresensi();
    }
}
