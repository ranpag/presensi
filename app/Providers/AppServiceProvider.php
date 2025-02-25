<?php

namespace App\Providers;

use App\Services\PresensiService;
use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\App;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        $this->app->singleton(PresensiService::class);
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        if (App::runningInConsole() && $this->isRunningServeCommand()) {
            app(PresensiService::class)->generatePresensi();
        }
    }

    private function isRunningServeCommand(): bool
    {
        // Pastikan ada argumen di CLI
        if (!isset($_SERVER['argv'][1])) {
            return false;
        }

        // Ambil perintah Artisan yang dijalankan
        $command = $_SERVER['argv'][1];

        return in_array($command, ['serve', 'ser']);
    }
}
