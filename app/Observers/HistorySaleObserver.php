<?php

namespace App\Observers;

use App\Models\HistorySale;
use Illuminate\Support\Facades\Log;

class HistorySaleObserver
{
    /**
     * Handle the HistorySale "created" event.
     * 
     * Note: Core business logic (stock updates) now handled by SalesService
     * This observer now only handles logging and non-critical side effects
     * 
     * @param  \App\Models\HistorySale  $historySale
     * @return void
     */
    public function created(HistorySale $historySale)
    {
        Log::info("HistorySale created via Observer", [
            'sale_id' => $historySale->id,
            'no_resi' => $historySale->no_resi,
            'sku_count' => is_array($historySale->no_sku) ? count($historySale->no_sku) : 0
        ]);
    }

    /**
     * Handle the HistorySale "updated" event.
     * 
     * @param  \App\Models\HistorySale  $historySale
     * @return void
     */
    public function updated(HistorySale $historySale)
    {
        Log::info("HistorySale updated via Observer", [
            'sale_id' => $historySale->id,
            'no_resi' => $historySale->no_resi,
            'changes' => $historySale->getChanges()
        ]);
    }

    /**
     * Handle the HistorySale "deleted" event.
     * 
     * @param  \App\Models\HistorySale  $historySale
     * @return void
     */
    public function deleted(HistorySale $historySale)
    {
        Log::info("HistorySale deleted via Observer", [
            'sale_id' => $historySale->id,
            'no_resi' => $historySale->no_resi,
            'sku_count' => is_array($historySale->no_sku) ? count($historySale->no_sku) : 0
        ]);
    }
}
