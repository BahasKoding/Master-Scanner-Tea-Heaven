<?php

namespace App\Providers;

use App\Jobs\VerifyStockConsistencyJob;
use App\Models\CatatanProduksi;
use App\Models\HistorySale;
use App\Observers\CatatanProduksiObserver;
use App\Observers\HistorySaleObserver;
use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Support\ServiceProvider;

class StockServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap services.
     */
    public function boot(): void
    {
        // Mendaftarkan observer
        CatatanProduksi::observe(CatatanProduksiObserver::class);

        // TEMPORARILY DISABLED: History Sales integration with stock system
        // To re-enable this integration, uncomment the line below:
        // HistorySale::observe(HistorySaleObserver::class);

        // Mendaftarkan schedule
        $this->app->booted(function () {
            $schedule = $this->app->make(Schedule::class);
            $schedule->job(new VerifyStockConsistencyJob())->dailyAt('01:00');
        });
    }
}
