<?php

namespace App\Services;

use App\Models\CatatanProduksi;
use App\Models\Sticker;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class ProductionService
{
    protected $stockService;

    public function __construct(StockService $stockService)
    {
        $this->stockService = $stockService;
    }

    /**
     * Create new production record with all related updates
     *
     * @param array $data
     * @return CatatanProduksi
     */
    public function createProduction(array $data)
    {
        return DB::transaction(function () use ($data) {
            try {
                // 1. Create production record
                $catatanProduksi = CatatanProduksi::create($data);

                // 2. Update finished goods stock
                $this->stockService->updateStockFromProduction($catatanProduksi);

                // 3. Update sticker production if applicable
                $this->updateStickerProduction($catatanProduksi->product_id);

                // 4. Log activity
                Log::info("Production created successfully", [
                    'production_id' => $catatanProduksi->id,
                    'product_id' => $catatanProduksi->product_id,
                    'quantity' => $catatanProduksi->quantity
                ]);

                return $catatanProduksi;
            } catch (\Exception $e) {
                Log::error("Failed to create production", [
                    'error' => $e->getMessage(),
                    'data' => $data
                ]);
                throw $e;
            }
        });
    }

    /**
     * Update production record with all related updates
     *
     * @param CatatanProduksi $catatanProduksi
     * @param array $data
     * @return CatatanProduksi
     */
    public function updateProduction(CatatanProduksi $catatanProduksi, array $data)
    {
        return DB::transaction(function () use ($catatanProduksi, $data) {
            try {
                $oldQuantity = $catatanProduksi->quantity;

                // 1. Update production record
                $catatanProduksi->update($data);

                // 2. Update finished goods stock if quantity changed
                if ($oldQuantity !== $catatanProduksi->quantity) {
                    $this->stockService->updateStockFromProductionChange($catatanProduksi);
                }

                // 3. Update sticker production
                $this->updateStickerProduction($catatanProduksi->product_id);

                // 4. Log activity
                Log::info("Production updated successfully", [
                    'production_id' => $catatanProduksi->id,
                    'product_id' => $catatanProduksi->product_id,
                    'old_quantity' => $oldQuantity,
                    'new_quantity' => $catatanProduksi->quantity
                ]);

                return $catatanProduksi;
            } catch (\Exception $e) {
                Log::error("Failed to update production", [
                    'error' => $e->getMessage(),
                    'production_id' => $catatanProduksi->id,
                    'data' => $data
                ]);
                throw $e;
            }
        });
    }

    /**
     * Delete production record with all related updates
     *
     * @param CatatanProduksi $catatanProduksi
     * @return bool
     */
    public function deleteProduction(CatatanProduksi $catatanProduksi)
    {
        return DB::transaction(function () use ($catatanProduksi) {
            try {
                $productId = $catatanProduksi->product_id;
                $quantity = $catatanProduksi->quantity;

                // 1. Remove stock from finished goods
                $this->stockService->removeStockFromProduction($catatanProduksi);

                // 2. Delete production record
                $result = $catatanProduksi->delete();

                // 3. Update sticker production
                $this->updateStickerProduction($productId);

                // 4. Log activity
                Log::info("Production deleted successfully", [
                    'product_id' => $productId,
                    'quantity' => $quantity
                ]);

                return $result;
            } catch (\Exception $e) {
                Log::error("Failed to delete production", [
                    'error' => $e->getMessage(),
                    'production_id' => $catatanProduksi->id
                ]);
                throw $e;
            }
        });
    }

    /**
     * Update sticker production for the given product
     * 
     * @param int $productId
     * @return void
     */
    private function updateStickerProduction($productId)
    {
        try {
            // Ensure sticker exists and update its production
            $sticker = Sticker::ensureStickerExists($productId);

            if ($sticker) {
                Log::info("Sticker production updated for product ID: {$productId}", [
                    'sticker_id' => $sticker->id,
                    'new_production' => $sticker->produksi_dynamic,
                    'sisa_dynamic' => $sticker->sisa_dynamic
                ]);
            }
        } catch (\Exception $e) {
            Log::error("Failed to update sticker production for product ID: {$productId}", [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            // Don't throw here - sticker update is not critical for main operation
        }
    }

    /**
     * Sync production data for consistency check
     * This method can be used for data migration or consistency verification
     *
     * @param int|null $productId
     * @return array
     */
    public function syncProductionData($productId = null)
    {
        return DB::transaction(function () use ($productId) {
            $query = CatatanProduksi::query();

            if ($productId) {
                $query->where('product_id', $productId);
            }

            $productions = $query->get();
            $syncResults = [];

            foreach ($productions as $production) {
                try {
                    // Re-sync stock for this production
                    $this->stockService->updateStockFromProduction($production);
                    $this->updateStickerProduction($production->product_id);

                    $syncResults[] = [
                        'production_id' => $production->id,
                        'product_id' => $production->product_id,
                        'status' => 'success'
                    ];
                } catch (\Exception $e) {
                    $syncResults[] = [
                        'production_id' => $production->id,
                        'product_id' => $production->product_id,
                        'status' => 'error',
                        'message' => $e->getMessage()
                    ];
                }
            }

            return $syncResults;
        });
    }
}
