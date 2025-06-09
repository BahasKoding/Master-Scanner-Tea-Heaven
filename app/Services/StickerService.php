<?php

namespace App\Services;

use App\Models\PurchaseSticker;
use App\Models\Sticker;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class StickerService
{
    /**
     * Create new purchase sticker record with all related updates
     *
     * @param array $data
     * @return PurchaseSticker
     */
    public function createPurchaseSticker(array $data)
    {
        return DB::transaction(function () use ($data) {
            try {
                // 1. Create purchase sticker record
                $purchaseSticker = PurchaseSticker::create($data);

                // 2. Update related sticker stock
                $this->updateStickerStock($purchaseSticker);

                // 3. Log activity
                Log::info("Purchase sticker created successfully", [
                    'purchase_sticker_id' => $purchaseSticker->id,
                    'product_id' => $purchaseSticker->product_id,
                    'ukuran_stiker' => $purchaseSticker->ukuran_stiker,
                    'stok_masuk' => $purchaseSticker->stok_masuk
                ]);

                return $purchaseSticker;
            } catch (\Exception $e) {
                Log::error("Failed to create purchase sticker", [
                    'error' => $e->getMessage(),
                    'data' => $data
                ]);
                throw $e;
            }
        });
    }

    /**
     * Update purchase sticker record with all related updates
     *
     * @param PurchaseSticker $purchaseSticker
     * @param array $data
     * @return PurchaseSticker
     */
    public function updatePurchaseSticker(PurchaseSticker $purchaseSticker, array $data)
    {
        return DB::transaction(function () use ($purchaseSticker, $data) {
            try {
                $oldStokMasuk = $purchaseSticker->stok_masuk;

                // 1. Update purchase sticker record
                $purchaseSticker->update($data);

                // 2. Update related sticker stock
                $this->updateStickerStock($purchaseSticker);

                // 3. Log activity
                Log::info("Purchase sticker updated successfully", [
                    'purchase_sticker_id' => $purchaseSticker->id,
                    'product_id' => $purchaseSticker->product_id,
                    'old_stok_masuk' => $oldStokMasuk,
                    'new_stok_masuk' => $purchaseSticker->stok_masuk
                ]);

                return $purchaseSticker;
            } catch (\Exception $e) {
                Log::error("Failed to update purchase sticker", [
                    'error' => $e->getMessage(),
                    'purchase_sticker_id' => $purchaseSticker->id,
                    'data' => $data
                ]);
                throw $e;
            }
        });
    }

    /**
     * Delete purchase sticker record with all related updates
     *
     * @param PurchaseSticker $purchaseSticker
     * @return bool
     */
    public function deletePurchaseSticker(PurchaseSticker $purchaseSticker)
    {
        return DB::transaction(function () use ($purchaseSticker) {
            try {
                $purchaseStickerId = $purchaseSticker->id;
                $productId = $purchaseSticker->product_id;
                $stokMasuk = $purchaseSticker->stok_masuk;

                // 1. Delete purchase sticker record
                $result = $purchaseSticker->delete();

                // 2. Update related sticker stock after deletion
                $this->updateStickerStockAfterDeletion($productId, $purchaseSticker->ukuran_stiker);

                // 3. Log activity
                Log::info("Purchase sticker deleted successfully", [
                    'purchase_sticker_id' => $purchaseStickerId,
                    'product_id' => $productId,
                    'stok_masuk' => $stokMasuk
                ]);

                return $result;
            } catch (\Exception $e) {
                Log::error("Failed to delete purchase sticker", [
                    'error' => $e->getMessage(),
                    'purchase_sticker_id' => $purchaseSticker->id
                ]);
                throw $e;
            }
        });
    }

    /**
     * Update sticker stock based on purchase stickers
     * This method replicates the logic from PurchaseStickerObserver
     *
     * @param PurchaseSticker $purchaseSticker
     * @return void
     */
    private function updateStickerStock(PurchaseSticker $purchaseSticker)
    {
        try {
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
                Log::info("Sticker stock updated", [
                    'sticker_id' => $sticker->id,
                    'product_id' => $sticker->product_id,
                    'new_stok_masuk' => $totalStokMasuk,
                    'new_sisa' => $newSisa,
                    'new_status' => $sticker->status
                ]);

                // Add activity log if function exists
                if (function_exists('addActivity')) {
                    addActivity(
                        'sticker',
                        'auto_update',
                        "Sticker stock auto-updated for product {$sticker->product->name_product}. New sisa: {$newSisa}",
                        $sticker->id
                    );
                }
            } else {
                Log::warning("Sticker not found for update", [
                    'product_id' => $purchaseSticker->product_id,
                    'ukuran_stiker' => $purchaseSticker->ukuran_stiker
                ]);
            }
        } catch (\Exception $e) {
            Log::error("Failed to update sticker stock", [
                'error' => $e->getMessage(),
                'purchase_sticker_id' => $purchaseSticker->id,
                'trace' => $e->getTraceAsString()
            ]);
            // Don't throw here - sticker update is not critical for main operation
        }
    }

    /**
     * Update sticker stock after purchase sticker deletion
     *
     * @param int $productId
     * @param string $ukuranStiker
     * @return void
     */
    private function updateStickerStockAfterDeletion($productId, $ukuranStiker)
    {
        try {
            $sticker = Sticker::where('product_id', $productId)
                ->where('ukuran', $ukuranStiker)
                ->first();

            if ($sticker) {
                // Recalculate stok_masuk from remaining purchase stickers
                $totalStokMasuk = PurchaseSticker::where('product_id', $productId)
                    ->where('ukuran_stiker', $ukuranStiker)
                    ->sum('stok_masuk');

                $sticker->stok_masuk = $totalStokMasuk;

                // Recalculate sisa
                $newSisa = $sticker->stok_awal + $totalStokMasuk + $sticker->produksi - $sticker->defect;
                $sticker->sisa = $newSisa;

                // Auto-update status
                if ($newSisa < 30) {
                    $sticker->status = 'need_order';
                } else {
                    $sticker->status = 'available';
                }

                $sticker->save();

                Log::info("Sticker stock updated after purchase deletion", [
                    'sticker_id' => $sticker->id,
                    'product_id' => $productId,
                    'new_stok_masuk' => $totalStokMasuk,
                    'new_sisa' => $newSisa,
                    'new_status' => $sticker->status
                ]);
            }
        } catch (\Exception $e) {
            Log::error("Failed to update sticker stock after deletion", [
                'error' => $e->getMessage(),
                'product_id' => $productId,
                'ukuran_stiker' => $ukuranStiker
            ]);
        }
    }

    /**
     * Sync all sticker data for consistency check
     * This method can be used for data migration or consistency verification
     *
     * @param int|null $productId
     * @return array
     */
    public function syncStickerData($productId = null)
    {
        return DB::transaction(function () use ($productId) {
            $query = Sticker::query();

            if ($productId) {
                $query->where('product_id', $productId);
            }

            $stickers = $query->get();
            $syncResults = [];

            foreach ($stickers as $sticker) {
                try {
                    // Recalculate stok_masuk from purchase stickers
                    $totalStokMasuk = PurchaseSticker::where('product_id', $sticker->product_id)
                        ->where('ukuran_stiker', $sticker->ukuran)
                        ->sum('stok_masuk');

                    $sticker->stok_masuk = $totalStokMasuk;

                    // Recalculate sisa
                    $newSisa = $sticker->stok_awal + $totalStokMasuk + $sticker->produksi - $sticker->defect;
                    $sticker->sisa = $newSisa;

                    // Auto-update status
                    if ($newSisa < 30) {
                        $sticker->status = 'need_order';
                    } else {
                        $sticker->status = 'available';
                    }

                    $sticker->save();

                    $syncResults[] = [
                        'sticker_id' => $sticker->id,
                        'product_id' => $sticker->product_id,
                        'ukuran' => $sticker->ukuran,
                        'status' => 'success',
                        'new_sisa' => $newSisa
                    ];
                } catch (\Exception $e) {
                    $syncResults[] = [
                        'sticker_id' => $sticker->id,
                        'product_id' => $sticker->product_id,
                        'status' => 'error',
                        'message' => $e->getMessage()
                    ];
                }
            }

            return $syncResults;
        });
    }

    /**
     * Get sticker statistics for a product
     *
     * @param int $productId
     * @return array
     */
    public function getStickerStatistics($productId)
    {
        try {
            $stickers = Sticker::where('product_id', $productId)->get();
            $statistics = [];

            foreach ($stickers as $sticker) {
                $totalPurchases = PurchaseSticker::where('product_id', $productId)
                    ->where('ukuran_stiker', $sticker->ukuran)
                    ->sum('stok_masuk');

                $statistics[] = [
                    'sticker_id' => $sticker->id,
                    'ukuran' => $sticker->ukuran,
                    'stok_awal' => $sticker->stok_awal,
                    'total_purchases' => $totalPurchases,
                    'produksi' => $sticker->produksi,
                    'defect' => $sticker->defect,
                    'sisa' => $sticker->sisa,
                    'sisa_dynamic' => $sticker->sisa_dynamic,
                    'status' => $sticker->status
                ];
            }

            return $statistics;
        } catch (\Exception $e) {
            Log::error("Failed to get sticker statistics", [
                'error' => $e->getMessage(),
                'product_id' => $productId
            ]);
            return [];
        }
    }
}
