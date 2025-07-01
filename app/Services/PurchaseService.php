<?php

namespace App\Services;

use App\Models\Purchase;
use App\Models\BahanBaku;
use App\Models\Product;
use App\Models\InventoryBahanBaku;
use App\Models\FinishedGoods;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class PurchaseService
{
    /**
     * Create new purchase record with all related stock updates
     *
     * @param array $data
     * @return Purchase
     */
    public function createPurchase(array $data)
    {
        return DB::transaction(function () use ($data) {
            try {
                // 1. Create purchase record
                $purchase = Purchase::create($data);

                // 2. Update inventory based on category
                $this->updateInventoryFromPurchase($purchase, 'create');

                // 3. Log activity
                Log::info("Purchase record created successfully", [
                    'purchase_id' => $purchase->id,
                    'kategori' => $purchase->kategori,
                    'item_id' => $purchase->bahan_baku_id,
                    'qty_pembelian' => $purchase->qty_pembelian,
                    'total_stok_masuk' => $purchase->total_stok_masuk
                ]);

                return $purchase;
            } catch (\Exception $e) {
                Log::error("Failed to create purchase record", [
                    'error' => $e->getMessage(),
                    'data' => $data
                ]);
                throw $e;
            }
        });
    }

    /**
     * Update purchase record with all related stock updates
     *
     * @param Purchase $purchase
     * @param array $data
     * @return Purchase
     */
    public function updatePurchase(Purchase $purchase, array $data)
    {
        return DB::transaction(function () use ($purchase, $data) {
            try {
                $oldKategori = $purchase->kategori;
                $oldBahanBakuId = $purchase->bahan_baku_id;
                $oldTotalStokMasuk = $purchase->total_stok_masuk;

                // 1. Update purchase record
                $purchase->update($data);

                // 2. Handle inventory updates for category/item changes
                $this->handleInventoryForUpdate($purchase, $oldKategori, $oldBahanBakuId, $oldTotalStokMasuk);

                // 3. Log activity
                Log::info("Purchase record updated successfully", [
                    'purchase_id' => $purchase->id,
                    'old_kategori' => $oldKategori,
                    'new_kategori' => $purchase->kategori,
                    'old_item_id' => $oldBahanBakuId,
                    'new_item_id' => $purchase->bahan_baku_id,
                    'old_total_stok' => $oldTotalStokMasuk,
                    'new_total_stok' => $purchase->total_stok_masuk
                ]);

                return $purchase;
            } catch (\Exception $e) {
                Log::error("Failed to update purchase record", [
                    'error' => $e->getMessage(),
                    'purchase_id' => $purchase->id,
                    'data' => $data
                ]);
                throw $e;
            }
        });
    }

    /**
     * Delete purchase record with all related stock updates
     *
     * @param Purchase $purchase
     * @return bool
     */
    public function deletePurchase(Purchase $purchase)
    {
        return DB::transaction(function () use ($purchase) {
            try {
                $purchaseId = $purchase->id;
                $kategori = $purchase->kategori;
                $bahanBakuId = $purchase->bahan_baku_id;

                // 1. Remove inventory impact before deletion
                $this->updateInventoryFromPurchase($purchase, 'delete');

                // 2. Delete purchase record
                $result = $purchase->delete();

                // 3. Log activity
                Log::info("Purchase record deleted successfully", [
                    'purchase_id' => $purchaseId,
                    'kategori' => $kategori,
                    'item_id' => $bahanBakuId
                ]);

                return $result;
            } catch (\Exception $e) {
                Log::error("Failed to delete purchase record", [
                    'error' => $e->getMessage(),
                    'purchase_id' => $purchase->id
                ]);
                throw $e;
            }
        });
    }

    /**
     * Update inventory based on purchase category
     *
     * @param Purchase $purchase
     * @param string $action (create, update, delete)
     * @return void
     */
    private function updateInventoryFromPurchase(Purchase $purchase, string $action)
    {
        if ($purchase->kategori === 'bahan_baku') {
            // Update InventoryBahanBaku
            InventoryBahanBaku::recalculateStokMasukFromPurchases($purchase->bahan_baku_id);
        } elseif ($purchase->kategori === 'finished_goods') {
            // Update FinishedGoods stok_masuk
            $this->updateFinishedGoodsStockFromPurchases($purchase->bahan_baku_id);
        }
    }

    /**
     * Handle inventory updates for purchase updates (category/item changes)
     *
     * @param Purchase $purchase
     * @param string $oldKategori
     * @param int $oldBahanBakuId
     * @param int $oldTotalStokMasuk
     * @return void
     */
    private function handleInventoryForUpdate(Purchase $purchase, string $oldKategori, int $oldBahanBakuId, int $oldTotalStokMasuk)
    {
        // If category changed, we need to handle both old and new categories
        if ($oldKategori !== $purchase->kategori || $oldBahanBakuId !== $purchase->bahan_baku_id) {
            // Recalculate old item inventory
            if ($oldKategori === 'bahan_baku') {
                InventoryBahanBaku::recalculateStokMasukFromPurchases($oldBahanBakuId);
            } elseif ($oldKategori === 'finished_goods') {
                $this->updateFinishedGoodsStockFromPurchases($oldBahanBakuId);
            }

            // Update new item inventory
            $this->updateInventoryFromPurchase($purchase, 'update');
        } else {
            // Same category and item, just recalculate
            $this->updateInventoryFromPurchase($purchase, 'update');
        }
    }

    /**
     * Update FinishedGoods stok_masuk from all finished_goods purchases
     *
     * @param int $productId
     * @return void
     */
    private function updateFinishedGoodsStockFromPurchases(int $productId)
    {
        try {
            // Calculate total from all finished_goods purchases for this product
            $totalStokMasuk = Purchase::where('bahan_baku_id', $productId)
                ->where('kategori', 'finished_goods')
                ->sum('total_stok_masuk');

            // Find or create FinishedGoods record
            $finishedGoods = FinishedGoods::firstOrNew(['product_id' => $productId]);

            // Set defaults if new record
            if (!$finishedGoods->exists) {
                $finishedGoods->stok_awal = 0;
                $finishedGoods->defective = 0;
            }

            // Update stok_masuk from purchases
            $finishedGoods->stok_masuk = $totalStokMasuk;

            // Recalculate other dynamic fields
            $finishedGoods->updateStokKeluarFromHistorySales();
            $finishedGoods->recalculateLiveStock();

            $finishedGoods->save();

            Log::info("FinishedGoods stock updated from purchase", [
                'product_id' => $productId,
                'total_stok_masuk' => $totalStokMasuk,
                'live_stock' => $finishedGoods->live_stock
            ]);
        } catch (\Exception $e) {
            Log::error("Failed to update FinishedGoods stock from purchases", [
                'product_id' => $productId,
                'error' => $e->getMessage()
            ]);
            throw $e;
        }
    }

    /**
     * Sync purchase data for consistency check
     * This method can be used for data migration or consistency verification
     *
     * @param int|null $purchaseId
     * @return array
     */
    public function syncPurchaseData($purchaseId = null)
    {
        return DB::transaction(function () use ($purchaseId) {
            $query = Purchase::query();

            if ($purchaseId) {
                $query->where('id', $purchaseId);
            }

            $purchases = $query->get();
            $syncResults = [];

            foreach ($purchases as $purchase) {
                try {
                    // Re-sync inventory for this purchase
                    $this->updateInventoryFromPurchase($purchase, 'sync');

                    $syncResults[] = [
                        'purchase_id' => $purchase->id,
                        'kategori' => $purchase->kategori,
                        'item_id' => $purchase->bahan_baku_id,
                        'status' => 'success'
                    ];
                } catch (\Exception $e) {
                    $syncResults[] = [
                        'purchase_id' => $purchase->id,
                        'kategori' => $purchase->kategori,
                        'item_id' => $purchase->bahan_baku_id,
                        'status' => 'error',
                        'message' => $e->getMessage()
                    ];
                }
            }

            return $syncResults;
        });
    }

    /**
     * Get purchase statistics for a specific item
     *
     * @param string $kategori
     * @param int $itemId
     * @return array
     */
    public function getPurchaseStatistics(string $kategori, int $itemId)
    {
        try {
            $stats = Purchase::where('kategori', $kategori)
                ->where('bahan_baku_id', $itemId)
                ->selectRaw('
                    COUNT(*) as total_purchases,
                    SUM(qty_pembelian) as total_qty_pembelian,
                    SUM(qty_barang_masuk) as total_qty_masuk,
                    SUM(barang_defect_tanpa_retur) as total_defect,
                    SUM(barang_diretur_ke_supplier) as total_retur,
                    SUM(total_stok_masuk) as total_stok_masuk,
                    AVG(total_stok_masuk) as avg_stok_masuk,
                    MIN(tanggal_kedatangan_barang) as first_purchase_date,
                    MAX(tanggal_kedatangan_barang) as last_purchase_date
                ')
                ->first();

            return [
                'total_purchases' => $stats->total_purchases ?? 0,
                'total_qty_pembelian' => $stats->total_qty_pembelian ?? 0,
                'total_qty_masuk' => $stats->total_qty_masuk ?? 0,
                'total_defect' => $stats->total_defect ?? 0,
                'total_retur' => $stats->total_retur ?? 0,
                'total_stok_masuk' => $stats->total_stok_masuk ?? 0,
                'avg_stok_masuk' => round($stats->avg_stok_masuk ?? 0, 2),
                'first_purchase_date' => $stats->first_purchase_date,
                'last_purchase_date' => $stats->last_purchase_date,
            ];
        } catch (\Exception $e) {
            Log::error("Failed to get purchase statistics", [
                'kategori' => $kategori,
                'item_id' => $itemId,
                'error' => $e->getMessage()
            ]);
            return [];
        }
    }
}
