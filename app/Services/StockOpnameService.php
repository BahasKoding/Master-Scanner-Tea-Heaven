<?php

namespace App\Services;

use App\Models\StockOpname;
use App\Models\StockOpnameItem;
use App\Models\InventoryBahanBaku;
use App\Models\FinishedGoods;
use App\Models\Sticker;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Collection;
use Exception;

class StockOpnameService
{
    /**
     * Create a new stock opname session with auto-populated items
     *
     * @param array $data
     * @return StockOpname
     * @throws Exception
     */
    public function createStockOpname(array $data): StockOpname
    {
        DB::beginTransaction();
        try {
            // Create stock opname session
            $opname = StockOpname::create([
                'type' => $data['type'],
                'tanggal_opname' => $data['tanggal_opname'],
                'status' => 'draft',
                'created_by' => Auth::id(),
                'notes' => $data['notes'] ?? null
            ]);

            // Auto-populate items based on type
            $this->populateItems($opname);

            DB::commit();
            return $opname;
        } catch (Exception $e) {
            DB::rollback();
            throw new Exception('Failed to create Stock Opname: ' . $e->getMessage());
        }
    }

    /**
     * Update stock opname items with physical count
     *
     * @param StockOpname $stockOpname
     * @param array $items
     * @return bool
     * @throws Exception
     */
    public function updatePhysicalCount(StockOpname $stockOpname, array $items): bool
    {
        DB::beginTransaction();
        try {
            foreach ($items as $itemData) {
                $item = StockOpnameItem::find($itemData['id']);
                if ($item && $item->opname_id == $stockOpname->id) {
                    // Get current live stock for accurate variance calculation
                    $currentLiveStock = $this->getCurrentLiveStock($stockOpname->type, $item->item_id);
                    
                    // Update item data
                    $item->stok_fisik = $itemData['stok_fisik'];
                    $item->stok_sistem = $currentLiveStock; // Update to current live stock
                    $item->selisih = $itemData['stok_fisik'] - $currentLiveStock; // Calculate based on live stock
                    $item->notes = $itemData['notes'] ?? null;
                    $item->save();
                }
            }

            // Update opname status to in_progress if it was draft
            if ($stockOpname->status === 'draft') {
                $stockOpname->status = 'in_progress';
                $stockOpname->save();
            }

            DB::commit();
            return true;
        } catch (Exception $e) {
            DB::rollback();
            throw new Exception('Failed to update physical count: ' . $e->getMessage());
        }
    }

    /**
     * Process/finalize the stock opname
     *
     * @param StockOpname $stockOpname
     * @param bool $updateStock
     * @return bool
     * @throws Exception
     */
    public function processStockOpname(StockOpname $stockOpname, bool $updateStock = false): bool
    {
        if ($stockOpname->status === 'completed') {
            throw new Exception('Stock Opname sudah selesai.');
        }

        DB::beginTransaction();
        try {
            if ($updateStock) {
                $this->updateSystemStock($stockOpname);
            }

            $stockOpname->status = 'completed';
            $stockOpname->completed_at = now();
            $stockOpname->save();

            DB::commit();
            return true;
        } catch (Exception $e) {
            DB::rollback();
            throw new Exception('Failed to process Stock Opname: ' . $e->getMessage());
        }
    }

    /**
     * Get variance analysis for stock opname
     *
     * @param StockOpname $stockOpname
     * @return array
     */
    public function getVarianceAnalysis(StockOpname $stockOpname): array
    {
        // Refresh stok_sistem to current live stock before analysis
        $this->refreshStokSistem($stockOpname);
        
        $items = $stockOpname->items()->whereNotNull('stok_fisik')->get();
        
        $analysis = [
            'total_items' => $items->count(),
            'surplus_items' => 0,
            'shortage_items' => 0,
            'exact_items' => 0,
            'total_surplus_value' => 0,
            'total_shortage_value' => 0,
            'items_by_category' => []
        ];

        foreach ($items as $item) {
            $selisih = $item->selisih ?? 0;
            
            if ($selisih > 0) {
                $analysis['surplus_items']++;
                $analysis['total_surplus_value'] += $selisih;
            } elseif ($selisih < 0) {
                $analysis['shortage_items']++;
                $analysis['total_shortage_value'] += abs($selisih);
            } else {
                $analysis['exact_items']++;
            }

            // Categorize by variance level
            $category = $this->categorizeVariance($selisih, $item->stok_sistem);
            if (!isset($analysis['items_by_category'][$category])) {
                $analysis['items_by_category'][$category] = 0;
            }
            $analysis['items_by_category'][$category]++;
        }

        return $analysis;
    }

    /**
     * Get stock opname recommendations
     *
     * @param StockOpname $stockOpname
     * @return array
     */
    public function getRecommendations(StockOpname $stockOpname): array
    {
        $items = $stockOpname->items()->whereNotNull('stok_fisik')->get();
        $recommendations = [];

        foreach ($items as $item) {
            $selisih = $item->selisih ?? 0;
            $percentage = $item->stok_sistem > 0 ? ($selisih / $item->stok_sistem) * 100 : 0;

            if (abs($percentage) > 10) { // Significant variance (>10%)
                $recommendations[] = [
                    'item_id' => $item->id,
                    'item_name' => $item->item_name,
                    'variance' => $selisih,
                    'percentage' => round($percentage, 2),
                    'priority' => $this->getPriority($percentage),
                    'recommendation' => $this->getRecommendationText($selisih, $percentage),
                    'action_required' => abs($percentage) > 20
                ];
            }
        }

        // Sort by priority (high to low)
        usort($recommendations, function ($a, $b) {
            $priorities = ['high' => 3, 'medium' => 2, 'low' => 1];
            return $priorities[$b['priority']] - $priorities[$a['priority']];
        });

        return $recommendations;
    }

    /**
     * Auto-populate items based on opname type
     *
     * @param StockOpname $opname
     * @return void
     * @throws Exception
     */
    private function populateItems(StockOpname $opname): void
    {
        \Log::info('StockOpname: Starting populateItems', [
            'opname_id' => $opname->id,
            'type' => $opname->type
        ]);
        
        $items = $this->getItemsByType($opname->type);
        
        \Log::info('StockOpname: Items retrieved', [
            'count' => $items->count(),
            'type' => $opname->type
        ]);

        if ($items->isEmpty()) {
            \Log::warning('StockOpname: No items found, creating dummy item', [
                'opname_id' => $opname->id,
                'type' => $opname->type
            ]);
            // Create at least one dummy item to prevent complete failure
            $this->createDummyItem($opname);
            return;
        }

        // Insert items
        $insertedCount = 0;
        foreach ($items as $itemData) {
            try {
                StockOpnameItem::create([
                    'opname_id' => $opname->id,
                    'item_id' => $itemData['item_id'],
                    'item_name' => $itemData['item_name'],
                    'item_sku' => $itemData['item_sku'] ?? null,
                    'stok_sistem' => $itemData['stok_sistem'],
                    'satuan' => $itemData['satuan']
                ]);
                $insertedCount++;
            } catch (Exception $e) {
                \Log::error('StockOpname: Failed to insert item', [
                    'opname_id' => $opname->id,
                    'item_data' => $itemData,
                    'error' => $e->getMessage()
                ]);
            }
        }
        
        \Log::info('StockOpname: Items inserted successfully', [
            'opname_id' => $opname->id,
            'inserted_count' => $insertedCount,
            'total_items' => $items->count()
        ]);
    }

    /**
     * Get items by opname type
     *
     * @param string $type
     * @return Collection
     */
    private function getItemsByType(string $type): Collection
    {
        \Log::info('StockOpname: Getting items by type', ['type' => $type]);
        
        try {
            switch ($type) {
                case 'bahan_baku':
                    // Try to get from InventoryBahanBaku first
                    $inventories = InventoryBahanBaku::with('bahanBaku')->get();
                    \Log::info('StockOpname: InventoryBahanBaku count', ['count' => $inventories->count()]);
                    
                    if ($inventories->isEmpty()) {
                        \Log::info('StockOpname: InventoryBahanBaku empty, using fallback to BahanBaku');
                        // Fallback: get from BahanBaku directly
                        $bahanBakus = \App\Models\BahanBaku::all();
                        \Log::info('StockOpname: BahanBaku fallback count', ['count' => $bahanBakus->count()]);
                        
                        return $bahanBakus->map(function ($bahanBaku) {
                            return [
                                'item_id' => $bahanBaku->id,
                                'item_name' => $bahanBaku->nama_barang ?? 'Unknown',
                                'item_sku' => $bahanBaku->sku_induk ?? '-',
                                'stok_sistem' => 0,
                                'satuan' => $bahanBaku->satuan ?? 'kg'
                            ];
                        });
                    }
                    
                    // Filter out inventories without bahanBaku relationship
                    $validInventories = $inventories->filter(function ($inventory) {
                        return $inventory->bahanBaku !== null;
                    });
                    
                    \Log::info('StockOpname: Valid inventories count', ['count' => $validInventories->count()]);
                    
                    return $validInventories->map(function ($inventory) {
                        return [
                            'item_id' => $inventory->bahanBaku->id,
                            'item_name' => $inventory->bahanBaku->nama_barang ?? 'Unknown',
                            'item_sku' => $inventory->bahanBaku->sku_induk ?? '-',
                            'stok_sistem' => $inventory->live_stok_gudang ?? 0,
                            'satuan' => $inventory->bahanBaku->satuan ?? 'kg'
                        ];
                    });

                case 'finished_goods':
                    // Try to get from FinishedGoods first
                    $finishedGoods = FinishedGoods::with('product')->get();
                    \Log::info('StockOpname: FinishedGoods count', ['count' => $finishedGoods->count()]);
                    
                    if ($finishedGoods->isEmpty()) {
                        \Log::info('StockOpname: FinishedGoods empty, using fallback to Product');
                        // Fallback: get from Product directly
                        $products = \App\Models\Product::all();
                        \Log::info('StockOpname: Product fallback count', ['count' => $products->count()]);
                        
                        return $products->map(function ($product) {
                            return [
                                'item_id' => $product->id,
                                'item_name' => $product->name_product ?? 'Unknown',
                                'item_sku' => $product->sku ?? '-',
                                'stok_sistem' => 0,
                                'satuan' => 'pcs'
                            ];
                        });
                    }
                    
                    // Filter out finished goods without product relationship
                    $validFinishedGoods = $finishedGoods->filter(function ($finished) {
                        return $finished->product !== null;
                    });
                    
                    \Log::info('StockOpname: Valid finished goods count', ['count' => $validFinishedGoods->count()]);
                    
                    return $validFinishedGoods->map(function ($finished) {
                        return [
                            'item_id' => $finished->product->id,
                            'item_name' => $finished->product->name_product ?? 'Unknown',
                            'item_sku' => $finished->product->sku ?? '-',
                            'stok_sistem' => $finished->live_stock ?? 0,
                            'satuan' => 'pcs'
                        ];
                    });

                case 'sticker':
                    $stickers = Sticker::all();
                    \Log::info('StockOpname: Stickers count', ['count' => $stickers->count()]);
                    
                    return $stickers->map(function ($sticker) {
                        return [
                            'item_id' => $sticker->id,
                            'item_name' => $sticker->ukuran ?? 'Unknown',
                            'stok_sistem' => $sticker->stok_stiker ?? 0,
                            'satuan' => 'pcs'
                        ];
                    });

                default:
                    \Log::warning('StockOpname: Unknown opname type', ['type' => $type]);
                    return collect([]);
            }
        } catch (Exception $e) {
            // Log the error for debugging
            \Log::error('StockOpname: Error getting items by type', [
                'type' => $type,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            return collect([]);
        }
    }

    /**
     * Get current live stock for an item based on type and item_id
     * This method provides real-time stock calculation instead of static snapshot
     *
     * @param string $type
     * @param int $itemId
     * @return int
     */
    public function getCurrentLiveStock(string $type, int $itemId): int
    {
        try {
            switch ($type) {
                case 'bahan_baku':
                    $inventory = InventoryBahanBaku::where('bahan_baku_id', $itemId)->first();
                    return $inventory ? ($inventory->live_stok_gudang ?? 0) : 0;

                case 'finished_goods':
                    $finished = FinishedGoods::where('product_id', $itemId)->first();
                    return $finished ? ($finished->live_stock ?? 0) : 0;

                case 'sticker':
                    $sticker = Sticker::find($itemId);
                    return $sticker ? ($sticker->stok_stiker ?? 0) : 0;

                default:
                    return 0;
            }
        } catch (Exception $e) {
            // Log error and return 0 as fallback
            \Log::error('Error getting current live stock: ' . $e->getMessage(), [
                'type' => $type,
                'item_id' => $itemId
            ]);
            return 0;
        }
    }

    /**
     * Update stok_sistem for all items in an opname to current live stock
     * This should be called before variance calculation to ensure accuracy
     *
     * @param StockOpname $opname
     * @return array Returns summary of stock changes
     */
    public function refreshStokSistem(StockOpname $opname): array
    {
        $items = $opname->items;
        $changes = [];
        $totalChanges = 0;
        
        foreach ($items as $item) {
            $originalStock = $item->stok_sistem;
            $currentStock = $this->getCurrentLiveStock($opname->type, $item->item_id);
            $stockChange = $currentStock - $originalStock;
            
            // Update stok_sistem to current live stock
            $item->stok_sistem = $currentStock;
            
            // Track changes for notification
            if ($stockChange != 0) {
                $changes[] = [
                    'item_name' => $item->item_name,
                    'original_stock' => $originalStock,
                    'current_stock' => $currentStock,
                    'change' => $stockChange,
                    'change_type' => $stockChange > 0 ? 'increase' : 'decrease'
                ];
                $totalChanges++;
            }
            
            // Recalculate selisih if stok_fisik exists
            if ($item->stok_fisik !== null) {
                $item->selisih = $item->stok_fisik - $currentStock;
            }
            
            $item->save();
        }
        
        // Log stock changes for audit trail
        if ($totalChanges > 0) {
            \Log::info('StockOpname: Stock sistem refreshed with changes', [
                'opname_id' => $opname->id,
                'total_changes' => $totalChanges,
                'changes' => $changes
            ]);
        }
        
        return [
            'total_changes' => $totalChanges,
            'changes' => $changes,
            'message' => $totalChanges > 0 
                ? "Stock sistem diperbarui untuk {$totalChanges} item karena ada perubahan transaksi"
                : 'Stock sistem sudah up-to-date, tidak ada perubahan'
        ];
    }

    /**
     * Update system stock based on physical count using absolute values
     * This method now uses transaction safety and comprehensive error handling
     *
     * @param StockOpname $opname
     * @return array Returns summary of stock updates
     * @throws Exception
     */
    private function updateSystemStock(StockOpname $opname): array
    {
        $items = $opname->items()->whereNotNull('stok_fisik')->get();
        $updateSummary = [
            'total_items' => $items->count(),
            'updated_items' => 0,
            'skipped_items' => 0,
            'failed_items' => 0,
            'errors' => []
        ];

        \Log::info('StockOpname: Starting system stock update', [
            'opname_id' => $opname->id,
            'opname_type' => $opname->type,
            'total_items' => $updateSummary['total_items']
        ]);

        // Use database transaction for data consistency
        \DB::beginTransaction();
        
        try {
            foreach ($items as $item) {
                try {
                    // Skip items with no variance (already accurate)
                    if ($item->selisih == 0) {
                        $updateSummary['skipped_items']++;
                        continue;
                    }

                    // Update stock based on opname type
                    switch ($opname->type) {
                        case 'bahan_baku':
                            $this->updateBahanBakuStock($item);
                            break;

                        case 'finished_goods':
                            $this->updateFinishedGoodsStock($item);
                            break;

                        case 'sticker':
                            $this->updateStickerStock($item);
                            break;
                            
                        default:
                            throw new \Exception("Unsupported opname type: {$opname->type}");
                    }
                    
                    $updateSummary['updated_items']++;
                    
                } catch (\Exception $e) {
                    $updateSummary['failed_items']++;
                    $updateSummary['errors'][] = [
                        'item_id' => $item->item_id,
                        'item_name' => $item->item_name,
                        'error' => $e->getMessage()
                    ];
                    
                    \Log::error('StockOpname: Failed to update individual item stock', [
                        'item_id' => $item->item_id,
                        'item_name' => $item->item_name,
                        'error' => $e->getMessage()
                    ]);
                }
            }
            
            // Commit transaction if all updates successful or acceptable error rate
            if ($updateSummary['failed_items'] == 0 || 
                ($updateSummary['failed_items'] / $updateSummary['total_items']) < 0.1) {
                \DB::commit();
                
                \Log::info('StockOpname: System stock update completed successfully', $updateSummary);
            } else {
                \DB::rollback();
                throw new \Exception('Too many failed stock updates, transaction rolled back');
            }
            
        } catch (\Exception $e) {
            \DB::rollback();
            \Log::error('StockOpname: System stock update failed', [
                'opname_id' => $opname->id,
                'error' => $e->getMessage(),
                'summary' => $updateSummary
            ]);
            throw $e;
        }
        
        return $updateSummary;
    }

    /**
     * Update bahan baku stock using absolute physical count value
     * This method now uses proper stock adjustment logic with audit trail
     *
     * @param StockOpnameItem $item
     * @return void
     */
    private function updateBahanBakuStock(StockOpnameItem $item): void
    {
        $inventory = InventoryBahanBaku::where('bahan_baku_id', $item->item_id)->first();
        if (!$inventory) {
            \Log::warning('StockOpname: InventoryBahanBaku not found for stock update', [
                'item_id' => $item->item_id,
                'item_name' => $item->item_name
            ]);
            return;
        }

        // Calculate what the new stok_masuk should be to achieve physical count
        $currentLiveStock = $inventory->live_stok_gudang ?? 0;
        $targetStock = $item->stok_fisik;
        $stockAdjustment = $targetStock - $currentLiveStock;
        
        // Only adjust stok_masuk if there's a difference
        if ($stockAdjustment != 0) {
            $oldStokMasuk = $inventory->stok_masuk;
            $newStokMasuk = max(0, $oldStokMasuk + $stockAdjustment);
            
            // Create audit trail before update
            $this->createStockAdjustmentLog(
                $item,
                'bahan_baku',
                $currentLiveStock,
                $targetStock,
                $stockAdjustment,
                "Adjustment stok_masuk dari {$oldStokMasuk} ke {$newStokMasuk}"
            );
            
            // Update the stock
            $inventory->stok_masuk = $newStokMasuk;
            $inventory->save();
            
            \Log::info('StockOpname: Bahan baku stock updated', [
                'item_id' => $item->item_id,
                'item_name' => $item->item_name,
                'old_stok_masuk' => $oldStokMasuk,
                'new_stok_masuk' => $newStokMasuk,
                'stock_adjustment' => $stockAdjustment,
                'target_stock' => $targetStock
            ]);
        }
    }

    /**
     * Update finished goods stock using absolute physical count value
     * This method now uses proper stock adjustment logic with audit trail
     *
     * @param StockOpnameItem $item
     * @return void
     */
    private function updateFinishedGoodsStock(StockOpnameItem $item): void
    {
        $finishedGoods = FinishedGoods::where('product_id', $item->item_id)->first();
        if (!$finishedGoods) {
            \Log::warning('StockOpname: FinishedGoods not found for stock update', [
                'item_id' => $item->item_id,
                'item_name' => $item->item_name
            ]);
            return;
        }

        $currentLiveStock = $finishedGoods->live_stock ?? 0;
        $targetStock = $item->stok_fisik;
        $stockAdjustment = $targetStock - $currentLiveStock;
        
        // Only update if there's a difference
        if ($stockAdjustment != 0) {
            // Create audit trail before update
            $this->createStockAdjustmentLog(
                $item,
                'finished_goods',
                $currentLiveStock,
                $targetStock,
                $stockAdjustment,
                "Direct adjustment live_stock dari {$currentLiveStock} ke {$targetStock}"
            );
            
            // Update the stock directly to physical count
            $finishedGoods->live_stock = $targetStock;
            $finishedGoods->save();
            
            \Log::info('StockOpname: Finished goods stock updated', [
                'item_id' => $item->item_id,
                'item_name' => $item->item_name,
                'old_live_stock' => $currentLiveStock,
                'new_live_stock' => $targetStock,
                'stock_adjustment' => $stockAdjustment
            ]);
        }
    }

    /**
     * Update sticker stock using absolute physical count value
     * This method now uses proper stock adjustment logic with audit trail
     *
     * @param StockOpnameItem $item
     * @return void
     */
    private function updateStickerStock(StockOpnameItem $item): void
    {
        $sticker = Sticker::find($item->item_id);
        if (!$sticker) {
            \Log::warning('StockOpname: Sticker not found for stock update', [
                'item_id' => $item->item_id,
                'item_name' => $item->item_name
            ]);
            return;
        }

        $currentStock = $sticker->stok_stiker ?? 0;
        $targetStock = $item->stok_fisik;
        $stockAdjustment = $targetStock - $currentStock;
        
        // Only update if there's a difference
        if ($stockAdjustment != 0) {
            // Create audit trail before update
            $this->createStockAdjustmentLog(
                $item,
                'sticker',
                $currentStock,
                $targetStock,
                $stockAdjustment,
                "Direct adjustment stok_stiker dari {$currentStock} ke {$targetStock}"
            );
            
            // Update the stock directly to physical count
            $sticker->stok_stiker = $targetStock;
            $sticker->save();
            
            \Log::info('StockOpname: Sticker stock updated', [
                'item_id' => $item->item_id,
                'item_name' => $item->item_name,
                'old_stok_stiker' => $currentStock,
                'new_stok_stiker' => $targetStock,
                'stock_adjustment' => $stockAdjustment
            ]);
        }
    }

    /**
     * Create comprehensive audit trail for stock adjustments
     * This method logs all stock changes with detailed information for compliance
     *
     * @param StockOpnameItem $item
     * @param string $stockType
     * @param float $oldStock
     * @param float $newStock
     * @param float $adjustment
     * @param string $details
     * @return void
     */
    private function createStockAdjustmentLog(
        StockOpnameItem $item,
        string $stockType,
        float $oldStock,
        float $newStock,
        float $adjustment,
        string $details
    ): void {
        try {
            // Create detailed audit log
            \Log::info('StockOpname: Stock Adjustment Audit Trail', [
                'opname_id' => $item->opname_id,
                'item_id' => $item->item_id,
                'item_name' => $item->item_name,
                'stock_type' => $stockType,
                'adjustment_type' => $adjustment > 0 ? 'increase' : 'decrease',
                'old_stock' => $oldStock,
                'new_stock' => $newStock,
                'adjustment_amount' => $adjustment,
                'physical_count' => $item->stok_fisik,
                'system_stock_before' => $item->stok_sistem,
                'variance' => $item->selisih,
                'details' => $details,
                'user_id' => auth()->id() ?? 'system',
                'timestamp' => now()->toDateTimeString(),
                'ip_address' => request()->ip() ?? 'unknown'
            ]);
            
            // TODO: In future, create StockAdjustment model record for database audit trail
            // This would store the audit information in a dedicated table for reporting
            
        } catch (\Exception $e) {
            \Log::error('StockOpname: Failed to create audit trail', [
                'error' => $e->getMessage(),
                'item_id' => $item->item_id,
                'stock_type' => $stockType
            ]);
        }
    }

    /**
     * Categorize variance by severity
     *
     * @param float $variance
     * @param float $systemStock
     * @return string
     */
    private function categorizeVariance(float $variance, float $systemStock): string
    {
        if ($variance == 0) return 'exact';
        
        $percentage = $systemStock > 0 ? abs($variance / $systemStock) * 100 : 100;
        
        if ($percentage > 20) return 'critical';
        if ($percentage > 10) return 'high';
        if ($percentage > 5) return 'medium';
        return 'low';
    }

    /**
     * Get priority level based on variance percentage
     *
     * @param float $percentage
     * @return string
     */
    private function getPriority(float $percentage): string
    {
        $absPercentage = abs($percentage);
        
        if ($absPercentage > 20) return 'high';
        if ($absPercentage > 10) return 'medium';
        return 'low';
    }

    /**
     * Get recommendation text based on variance
     *
     * @param float $variance
     * @param float $percentage
     * @return string
     */
    private function getRecommendationText(float $variance, float $percentage): string
    {
        if ($variance > 0) {
            return "Surplus " . abs($percentage) . "%. Periksa kemungkinan kesalahan pencatatan atau penerimaan yang belum tercatat.";
        } else {
            return "Kekurangan " . abs($percentage) . "%. Periksa kemungkinan kehilangan, kerusakan, atau penggunaan yang belum tercatat.";
        }
    }

    /**
     * Get stock opname statistics
     *
     * @param array $filters
     * @return array
     */
    public function getStatistics(array $filters = []): array
    {
        $query = StockOpname::query();

        // Apply filters
        if (!empty($filters['type'])) {
            $query->where('type', $filters['type']);
        }

        if (!empty($filters['status'])) {
            $query->where('status', $filters['status']);
        }

        if (!empty($filters['date_from'])) {
            $query->whereDate('tanggal_opname', '>=', $filters['date_from']);
        }

        if (!empty($filters['date_to'])) {
            $query->whereDate('tanggal_opname', '<=', $filters['date_to']);
        }

        $opnames = $query->with('items')->get();

        return [
            'total_opnames' => $opnames->count(),
            'completed_opnames' => $opnames->where('status', 'completed')->count(),
            'in_progress_opnames' => $opnames->where('status', 'in_progress')->count(),
            'draft_opnames' => $opnames->where('status', 'draft')->count(),
            'total_items_counted' => $opnames->sum(function ($opname) {
                return $opname->items->whereNotNull('stok_fisik')->count();
            }),
            'average_variance_percentage' => $this->calculateAverageVariance($opnames),
            'by_type' => [
                'bahan_baku' => $opnames->where('type', 'bahan_baku')->count(),
                'finished_goods' => $opnames->where('type', 'finished_goods')->count(),
                'sticker' => $opnames->where('type', 'sticker')->count(),
            ]
        ];
    }

    /**
     * Calculate average variance percentage
     *
     * @param Collection $opnames
     * @return float
     */
    private function calculateAverageVariance(Collection $opnames): float
    {
        $totalVariance = 0;
        $totalItems = 0;

        foreach ($opnames as $opname) {
            foreach ($opname->items->whereNotNull('stok_fisik') as $item) {
                if ($item->stok_sistem > 0) {
                    $percentage = abs($item->selisih / $item->stok_sistem) * 100;
                    $totalVariance += $percentage;
                    $totalItems++;
                }
            }
        }

        return $totalItems > 0 ? round($totalVariance / $totalItems, 2) : 0;
    }

    /**
     * Check if there are concurrent transactions that might affect stock during opname
     * This method helps identify potential stock changes during opname period
     *
     * @param StockOpname $opname
     * @return array
     */
    public function checkConcurrentTransactions(StockOpname $opname): array
    {
        $warnings = [];
        $opnameDate = $opname->created_at;
        
        try {
            switch ($opname->type) {
                case 'bahan_baku':
                    // Check for recent purchases after opname creation
                    $recentPurchases = \App\Models\Purchase::where('created_at', '>', $opnameDate)
                        ->whereIn('bahan_baku_id', $opname->items->pluck('item_id'))
                        ->count();
                    
                    if ($recentPurchases > 0) {
                        $warnings[] = "Ada {$recentPurchases} transaksi pembelian bahan baku setelah opname dibuat";
                    }
                    break;
                    
                case 'finished_goods':
                    // Check for recent production after opname creation
                    $recentProduction = \App\Models\CatatanProduksi::where('created_at', '>', $opnameDate)
                        ->whereIn('product_id', $opname->items->pluck('item_id'))
                        ->count();
                    
                    // Check for recent sales after opname creation
                    $recentSales = \App\Models\HistorySale::where('created_at', '>', $opnameDate)
                        ->whereIn('product_id', $opname->items->pluck('item_id'))
                        ->count();
                    
                    if ($recentProduction > 0) {
                        $warnings[] = "Ada {$recentProduction} transaksi produksi setelah opname dibuat";
                    }
                    
                    if ($recentSales > 0) {
                        $warnings[] = "Ada {$recentSales} transaksi penjualan setelah opname dibuat";
                    }
                    break;
                    
                case 'sticker':
                    // Check for recent sticker purchases (if applicable)
                    // This would depend on your sticker purchase tracking system
                    break;
            }
        } catch (Exception $e) {
            \Log::error('Error checking concurrent transactions: ' . $e->getMessage());
            $warnings[] = 'Tidak dapat memeriksa transaksi concurrent';
        }
        
        return $warnings;
    }

    /**
     * Get stock movement summary during opname period
     * This helps understand what changed between opname creation and current time
     *
     * @param StockOpname $opname
     * @return array
     */
    public function getStockMovementSummary(StockOpname $opname): array
    {
        $summary = [];
        $opnameDate = $opname->created_at;
        
        foreach ($opname->items as $item) {
            $currentStock = $this->getCurrentLiveStock($opname->type, $item->item_id);
            $originalStock = $item->stok_sistem; // Original snapshot when opname was created
            $movement = $currentStock - $originalStock;
            
            if ($movement != 0) {
                $summary[] = [
                    'item_id' => $item->item_id,
                    'item_name' => $item->item_name,
                    'original_stock' => $originalStock,
                    'current_stock' => $currentStock,
                    'movement' => $movement,
                    'movement_type' => $movement > 0 ? 'increase' : 'decrease'
                ];
            }
        }
        
        return $summary;
    }

    /**
     * Create a dummy item when no actual items are found
     *
     * @param StockOpname $opname
     * @return void
     */
    private function createDummyItem(StockOpname $opname): void
    {
        $dummyNames = [
            'bahan_baku' => 'Dummy Bahan Baku (Tidak ada data inventory)',
            'finished_goods' => 'Dummy Finished Goods (Tidak ada data produk)',
            'sticker' => 'Dummy Sticker (Tidak ada data sticker)'
        ];

        $dummyUnits = [
            'bahan_baku' => 'kg',
            'finished_goods' => 'pcs',
            'sticker' => 'pcs'
        ];

        StockOpnameItem::create([
            'opname_id' => $opname->id,
            'item_id' => 0, // Use 0 for dummy items
            'item_name' => $dummyNames[$opname->type] ?? 'Dummy Item',
            'stok_sistem' => 0,
            'satuan' => $dummyUnits[$opname->type] ?? 'pcs'
        ]);
    }
}
