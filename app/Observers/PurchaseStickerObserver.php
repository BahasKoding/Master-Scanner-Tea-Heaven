<?php

namespace App\Observers;

use App\Models\PurchaseSticker;
use Illuminate\Support\Facades\Log;

class PurchaseStickerObserver
{
    /**
     * Handle the PurchaseSticker "created" event.
     * 
     * Note: Core business logic (sticker stock updates) now handled by StickerService
     * This observer now only handles logging and non-critical side effects
     */
    public function created(PurchaseSticker $purchaseSticker): void
    {
        Log::info("PurchaseSticker created via Observer", [
            'purchase_sticker_id' => $purchaseSticker->id,
            'product_id' => $purchaseSticker->product_id,
            'ukuran_stiker' => $purchaseSticker->ukuran_stiker,
            'stok_masuk' => $purchaseSticker->stok_masuk
        ]);
    }

    /**
     * Handle the PurchaseSticker "updated" event.
     */
    public function updated(PurchaseSticker $purchaseSticker): void
    {
        Log::info("PurchaseSticker updated via Observer", [
            'purchase_sticker_id' => $purchaseSticker->id,
            'product_id' => $purchaseSticker->product_id,
            'changes' => $purchaseSticker->getChanges()
        ]);
    }

    /**
     * Handle the PurchaseSticker "deleted" event.
     */
    public function deleted(PurchaseSticker $purchaseSticker): void
    {
        Log::info("PurchaseSticker deleted via Observer", [
            'purchase_sticker_id' => $purchaseSticker->id,
            'product_id' => $purchaseSticker->product_id,
            'ukuran_stiker' => $purchaseSticker->ukuran_stiker,
            'stok_masuk' => $purchaseSticker->stok_masuk
        ]);
    }
}
