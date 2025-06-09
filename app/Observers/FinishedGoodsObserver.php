<?php

namespace App\Observers;

use App\Models\FinishedGoods;
use Illuminate\Support\Facades\Log;

class FinishedGoodsObserver
{
    /**
     * Handle the FinishedGoods "updated" event.
     */
    public function updated(FinishedGoods $finishedGoods)
    {
        // Log stock changes for debugging
        if ($finishedGoods->wasChanged(['stok_awal', 'stok_masuk', 'stok_keluar', 'defective', 'live_stock'])) {
            Log::info('FinishedGoods stock updated', [
                'product_id' => $finishedGoods->product_id,
                'changes' => $finishedGoods->getChanges(),
                'live_stock' => $finishedGoods->live_stock
            ]);

            // Trigger real-time update event (optional - for WebSocket implementation)
        }
    }
}
