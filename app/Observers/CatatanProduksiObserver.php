<?php

namespace App\Observers;

use App\Models\CatatanProduksi;
use Illuminate\Support\Facades\Log;

class CatatanProduksiObserver
{
    /**
     * Handle the CatatanProduksi "created" event.
     * 
     * Note: Core business logic (stock updates, sticker updates) now handled by ProductionService
     * This observer now only handles logging and non-critical side effects
     * 
     * @param  \App\Models\CatatanProduksi  $catatanProduksi
     * @return void
     */
    public function created(CatatanProduksi $catatanProduksi)
    {
        Log::info("CatatanProduksi created via Observer", [
            'production_id' => $catatanProduksi->id,
            'product_id' => $catatanProduksi->product_id,
            'quantity' => $catatanProduksi->quantity,
            'packaging' => $catatanProduksi->packaging
        ]);
    }

    /**
     * Handle the CatatanProduksi "updated" event.
     * 
     * @param  \App\Models\CatatanProduksi  $catatanProduksi
     * @return void
     */
    public function updated(CatatanProduksi $catatanProduksi)
    {
        Log::info("CatatanProduksi updated via Observer", [
            'production_id' => $catatanProduksi->id,
            'product_id' => $catatanProduksi->product_id,
            'changes' => $catatanProduksi->getChanges()
        ]);
    }

    /**
     * Handle the CatatanProduksi "deleted" event.
     * 
     * @param  \App\Models\CatatanProduksi  $catatanProduksi
     * @return void
     */
    public function deleted(CatatanProduksi $catatanProduksi)
    {
        Log::info("CatatanProduksi deleted via Observer", [
            'product_id' => $catatanProduksi->product_id,
            'quantity' => $catatanProduksi->quantity,
            'packaging' => $catatanProduksi->packaging
        ]);
    }
}
