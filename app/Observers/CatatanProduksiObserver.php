<?php

namespace App\Observers;

use App\Models\CatatanProduksi;
use App\Models\Sticker;
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

        // Update sticker produksi
        $this->updateStickerProduksi($catatanProduksi->product_id);
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

        // Update sticker produksi whenever catatan produksi is updated
        $this->updateStickerProduksi($catatanProduksi->product_id);
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

        // Update sticker produksi
        $this->updateStickerProduksi($catatanProduksi->product_id);
    }

    /**
     * Update sticker produksi for the given product
     * 
     * @param int $productId
     * @return void
     */
    private function updateStickerProduksi($productId)
    {
        try {
            // Ensure sticker exists and update its produksi
            $sticker = Sticker::ensureStickerExists($productId);

            if ($sticker) {
                \Log::info("Sticker produksi updated for product ID: {$productId}", [
                    'sticker_id' => $sticker->id,
                    'new_produksi' => $sticker->produksi_dynamic,
                    'sisa_dynamic' => $sticker->sisa_dynamic
                ]);
            }
        } catch (\Exception $e) {
            \Log::error("Failed to update sticker produksi for product ID: {$productId}", [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
        }
    }
}
