<?php

namespace App\Observers;

use App\Models\PurchaseSticker;
use App\Models\Sticker;

class PurchaseStickerObserver
{
    /**
     * Handle the PurchaseSticker "created" event.
     */
    public function created(PurchaseSticker $purchaseSticker): void
    {
        $this->updateStickerStock($purchaseSticker);
    }

    /**
     * Handle the PurchaseSticker "updated" event.
     */
    public function updated(PurchaseSticker $purchaseSticker): void
    {
        $this->updateStickerStock($purchaseSticker);
    }

    /**
     * Handle the PurchaseSticker "deleted" event.
     */
    public function deleted(PurchaseSticker $purchaseSticker): void
    {
        $this->updateStickerStock($purchaseSticker);
    }

    /**
     * Update sticker stock and status based on purchase stickers
     */
    private function updateStickerStock(PurchaseSticker $purchaseSticker): void
    {
        // Find the related sticker
        $sticker = Sticker::where('product_id', $purchaseSticker->product_id)
            ->where('ukuran', $purchaseSticker->ukuran_stiker)
            ->first();

        if ($sticker) {
            // Calculate new stok_masuk from all purchase stickers
            $totalStokMasuk = PurchaseSticker::where('product_id', $purchaseSticker->product_id)
                ->where('ukuran_stiker', $purchaseSticker->ukuran_stiker)
                ->sum('stok_masuk');

            // Update stok_masuk
            $sticker->stok_masuk = $totalStokMasuk;

            // Calculate new sisa
            $newSisa = $sticker->stok_awal + $totalStokMasuk + $sticker->produksi - $sticker->defect;
            $sticker->sisa = $newSisa;

            // Auto-update status based on sisa
            if ($newSisa < 30) {
                $sticker->status = 'need_order';
            } else {
                $sticker->status = 'available';
            }

            $sticker->save();

            // Log the update
            if (function_exists('addActivity')) {
                addActivity(
                    'sticker',
                    'auto_update',
                    "Sticker stock auto-updated for product {$sticker->product->name_product}. New sisa: {$newSisa}",
                    $sticker->id
                );
            }
        }
    }
}
