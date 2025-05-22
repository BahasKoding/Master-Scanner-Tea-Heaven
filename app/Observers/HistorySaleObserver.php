<?php

namespace App\Observers;

use App\Models\HistorySale;
use App\Services\StockService;

class HistorySaleObserver
{
    /**
     * Handle the HistorySale "created" event.
     * 
     * @param  \App\Models\HistorySale  $historySale
     * @return void
     */
    public function created(HistorySale $historySale)
    {
        app(StockService::class)->updateStockFromSales($historySale);
    }

    /**
     * Handle the HistorySale "updated" event.
     * 
     * @param  \App\Models\HistorySale  $historySale
     * @return void
     */
    public function updated(HistorySale $historySale)
    {
        // Jika qty atau no_sku berubah, perbarui stok keluar
        if ($historySale->isDirty('qty') || $historySale->isDirty('no_sku')) {
            app(StockService::class)->updateStockFromSalesChange($historySale);
        }
    }

    /**
     * Handle the HistorySale "deleted" event.
     * 
     * @param  \App\Models\HistorySale  $historySale
     * @return void
     */
    public function deleted(HistorySale $historySale)
    {
        // Jika penjualan dihapus, kembalikan stok keluar
        app(StockService::class)->restoreStockFromSales($historySale);
    }
}
