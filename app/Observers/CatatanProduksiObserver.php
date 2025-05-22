<?php

namespace App\Observers;

use App\Models\CatatanProduksi;
use App\Services\StockService;

class CatatanProduksiObserver
{
    /**
     * Handle the CatatanProduksi "created" event.
     * 
     * @param  \App\Models\CatatanProduksi  $catatanProduksi
     * @return void
     */
    public function created(CatatanProduksi $catatanProduksi)
    {
        app(StockService::class)->updateStockFromProduction($catatanProduksi);
    }

    /**
     * Handle the CatatanProduksi "updated" event.
     * 
     * @param  \App\Models\CatatanProduksi  $catatanProduksi
     * @return void
     */
    public function updated(CatatanProduksi $catatanProduksi)
    {
        // Hanya memperbarui stok jika quantity berubah
        if ($catatanProduksi->isDirty('quantity')) {
            app(StockService::class)->updateStockFromProductionChange($catatanProduksi);
        }
    }

    /**
     * Handle the CatatanProduksi "deleted" event.
     * 
     * @param  \App\Models\CatatanProduksi  $catatanProduksi
     * @return void
     */
    public function deleted(CatatanProduksi $catatanProduksi)
    {
        // Jika catatan produksi dihapus, kurangi stok masuk
        app(StockService::class)->removeStockFromProduction($catatanProduksi);
    }
}
