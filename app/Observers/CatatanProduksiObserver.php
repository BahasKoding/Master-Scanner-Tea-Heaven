<?php

namespace App\Observers;

use App\Models\CatatanProduksi;
use App\Models\InventoryBahanBaku;
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

        // Trigger inventory sync for affected bahan baku
        $this->syncAffectedInventory($catatanProduksi, 'created');
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

        // Trigger inventory sync for affected bahan baku
        $this->syncAffectedInventory($catatanProduksi, 'updated');
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
            'packaging' => $catatanProduksi->packaging,
            'affected_bahan_baku' => $catatanProduksi->sku_induk ?? []
        ]);

        // Critical: Ensure inventory is properly synced after deletion
        $this->syncAffectedInventory($catatanProduksi, 'deleted');
    }

    /**
     * Sync inventory for all bahan baku affected by this catatan produksi
     * This ensures inventory consistency across all operations
     * 
     * @param \App\Models\CatatanProduksi $catatanProduksi
     * @param string $operation
     * @return void
     */
    private function syncAffectedInventory(CatatanProduksi $catatanProduksi, string $operation)
    {
        try {
            $bahanBakuIds = $catatanProduksi->sku_induk ?? [];

            if (empty($bahanBakuIds)) {
                Log::info("No bahan baku to sync for catatan produksi {$catatanProduksi->id}");
                return;
            }

            Log::info("Syncing inventory for affected bahan baku", [
                'operation' => $operation,
                'production_id' => $catatanProduksi->id,
                'bahan_baku_ids' => $bahanBakuIds
            ]);

            foreach ($bahanBakuIds as $bahanBakuId) {
                try {
                    // Recalculate terpakai from all remaining catatan produksi
                    InventoryBahanBaku::recalculateTerpakaiFromProduksi($bahanBakuId);

                    Log::debug("Inventory synced for bahan baku {$bahanBakuId} after {$operation}");
                } catch (\Exception $syncError) {
                    Log::error("Failed to sync inventory for bahan baku {$bahanBakuId}", [
                        'error' => $syncError->getMessage(),
                        'operation' => $operation,
                        'production_id' => $catatanProduksi->id
                    ]);
                }
            }

            Log::info("Inventory sync completed for {$operation} operation", [
                'production_id' => $catatanProduksi->id,
                'synced_items' => count($bahanBakuIds)
            ]);
        } catch (\Exception $e) {
            Log::error("Critical error during inventory sync after {$operation}", [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
                'production_id' => $catatanProduksi->id
            ]);
        }
    }
}
