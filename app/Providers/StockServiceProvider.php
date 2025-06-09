<?php

namespace App\Providers;

use App\Jobs\VerifyStockConsistencyJob;
use App\Models\CatatanProduksi;
use App\Models\HistorySale;
use App\Models\FinishedGoods;
use App\Observers\CatatanProduksiObserver;
use App\Observers\HistorySaleObserver;
use App\Observers\FinishedGoodsObserver;
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
    // use App\Models\FinishedGoods;
    // use App\Observers\FinishedGoodsObserver;

    public function boot()
    {
        // Mendaftarkan observer
        CatatanProduksi::observe(CatatanProduksiObserver::class);

        // Aktifkan HistorySaleObserver untuk real-time stock updates
        HistorySale::observe(HistorySaleObserver::class);

        // Aktifkan FinishedGoodsObserver untuk logging stock changes
        FinishedGoods::observe(FinishedGoodsObserver::class);

        // Mendaftarkan schedule
        $this->app->booted(function () {
            $schedule = $this->app->make(Schedule::class);
            $schedule->job(new VerifyStockConsistencyJob())->dailyAt('01:00');
        });
    }
}
