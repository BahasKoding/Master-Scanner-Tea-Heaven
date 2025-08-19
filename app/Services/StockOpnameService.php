<?php

namespace App\Services;

use App\Models\StockOpname;
use App\Models\StockOpnameItem;
use App\Models\InventoryBahanBaku;
use App\Models\FinishedGoods;

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
                    // Get ALL inventory bahan baku records (matching finished goods pattern exactly)
                    $inventories = InventoryBahanBaku::with('bahanBaku')->get();
                    \Log::info('StockOpname: InventoryBahanBaku count', ['count' => $inventories->count()]);
                    
                    // Debug: Log all inventory data
                    foreach ($inventories as $inv) {
                        \Log::info('StockOpname: Inventory data', [
                            'id' => $inv->id,
                            'bahan_baku_id' => $inv->bahan_baku_id,
                            'has_bahan_baku' => $inv->bahanBaku !== null,
                            'nama_barang' => $inv->bahanBaku->nama_barang ?? 'NULL',
                            'live_stok_gudang' => $inv->live_stok_gudang
                        ]);
                    }
                    
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
                    
                    // Filter out inventories without bahanBaku relationship (same as finished goods pattern)
                    $validInventories = $inventories->filter(function ($inventory) {
                        return $inventory->bahanBaku !== null;
                    });
                    
                    \Log::info('StockOpname: Valid inventories count', ['count' => $validInventories->count()]);
                    
                    // Debug: Log valid inventory data
                    foreach ($validInventories as $inv) {
                        \Log::info('StockOpname: Valid inventory', [
                            'bahan_baku_id' => $inv->bahan_baku_id,
                            'nama_barang' => $inv->bahanBaku->nama_barang,
                            'sku_induk' => $inv->bahanBaku->sku_induk,
                            'live_stok_gudang' => $inv->live_stok_gudang
                        ]);
                    }
                    
                    // Map ALL valid inventory records (not just unique bahan baku)
                    return $validInventories->map(function ($inventory) {
                        return [
                            'item_id' => $inventory->bahan_baku_id, // Use bahan_baku_id from inventory
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
    public function updateSystemStock(StockOpname $opname): array
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
     * Reset bahan baku stock using RESET FLOW - all fields reset except stok_awal
     * NEW LOGIC: stok_awal = stok_fisik, all other fields = 0
     *
     * @param StockOpnameItem $item
     * @return void
     */
    private function updateBahanBakuStock(StockOpnameItem $item): void
    {
        $inventory = InventoryBahanBaku::where('bahan_baku_id', $item->item_id)->first();
        if (!$inventory) {
            \Log::warning('StockOpname: InventoryBahanBaku not found for stock reset', [
                'item_id' => $item->item_id,
                'item_name' => $item->item_name
            ]);
            return;
        }

        // Store old values for audit trail
        $oldValues = [
            'stok_awal' => $inventory->stok_awal,
            'stok_masuk' => $inventory->stok_masuk,
            'terpakai' => $inventory->terpakai,
            'defect' => $inventory->defect,
            'live_stok_gudang' => $inventory->live_stok_gudang
        ];
        
        // RESET FLOW: Set stok_awal = stok_fisik, reset all other fields to 0
        $inventory->stok_awal = $item->stok_fisik;
        $inventory->stok_masuk = 0;
        $inventory->terpakai = 0;
        $inventory->defect = 0;
        // live_stok_gudang will be recalculated automatically via accessor
        
        $inventory->save();
        
        // Create comprehensive audit trail
        $this->createStockAdjustmentLog(
            $item,
            'bahan_baku',
            $oldValues['live_stok_gudang'],
            $item->stok_fisik,
            $item->stok_fisik - $oldValues['live_stok_gudang'],
            "RESET FLOW: stok_awal={$item->stok_fisik}, stok_masuk=0, terpakai=0, defect=0"
        );
        
        \Log::info('StockOpname: Reset bahan baku stock via RESET FLOW', [
            'item_id' => $item->item_id,
            'item_name' => $item->item_name,
            'old_values' => $oldValues,
            'new_stok_awal' => $item->stok_fisik,
            'reset_fields' => ['stok_masuk', 'terpakai', 'defect']
        ]);
    }

    /**
     * Reset finished goods stock using RESET FLOW - all fields reset except stok_awal
     * NEW LOGIC: stok_awal = stok_fisik, all other fields = 0
     *
     * @param StockOpnameItem $item
     * @return void
     */
    private function updateFinishedGoodsStock(StockOpnameItem $item): void
    {
        $finishedGoods = FinishedGoods::where('product_id', $item->item_id)->first();
        if (!$finishedGoods) {
            \Log::warning('StockOpname: FinishedGoods not found for stock reset', [
                'item_id' => $item->item_id,
                'item_name' => $item->item_name
            ]);
            return;
        }

        // Store old values for audit trail
        $oldValues = [
            'stok_awal' => $finishedGoods->stok_awal,
            'stok_masuk' => $finishedGoods->stok_masuk,
            'stok_keluar' => $finishedGoods->stok_keluar,
            'defective' => $finishedGoods->defective,
            'live_stock' => $finishedGoods->live_stock
        ];
        
        // RESET FLOW: Set stok_awal = stok_fisik, reset all other fields to 0
        $finishedGoods->stok_awal = $item->stok_fisik;
        $finishedGoods->stok_masuk = 0;
        $finishedGoods->stok_keluar = 0;
        $finishedGoods->defective = 0;
        // live_stock will be recalculated automatically via accessor
        
        $finishedGoods->save();
        
        // Create comprehensive audit trail
        $this->createStockAdjustmentLog(
            $item,
            'finished_goods',
            $oldValues['live_stock'],
            $item->stok_fisik,
            $item->stok_fisik - $oldValues['live_stock'],
            "RESET FLOW: stok_awal={$item->stok_fisik}, stok_masuk=0, stok_keluar=0, defective=0"
        );
        
        \Log::info('StockOpname: Reset finished goods stock via RESET FLOW', [
            'item_id' => $item->item_id,
            'item_name' => $item->item_name,
            'old_values' => $oldValues,
            'new_stok_awal' => $item->stok_fisik,
            'reset_fields' => ['stok_masuk', 'stok_keluar', 'defective']
        ]);
    }

    /**
     * Reset sticker stock using RESET FLOW - all fields reset except stok_awal
     * NEW LOGIC: stok_awal = stok_fisik, all other fields = 0
     *
     * @param StockOpnameItem $item
     * @return void
     */
    private function updateStickerStock(StockOpnameItem $item): void
    {
        $sticker = \App\Models\Sticker::find($item->item_id);
        if (!$sticker) {
            \Log::warning('StockOpname: Sticker not found for stock reset', [
                'item_id' => $item->item_id,
                'item_name' => $item->item_name
            ]);
            return;
        }

        // Store old values for audit trail
        $oldValues = [
            'stok_awal' => $sticker->stok_awal ?? 0,
            'stok_masuk' => $sticker->stok_masuk ?? 0,
            'stok_keluar' => $sticker->stok_keluar ?? 0,
            'live_stok' => $sticker->live_stok ?? 0
        ];
        
        // RESET FLOW: Set stok_awal = stok_fisik, reset all other fields to 0
        $sticker->stok_awal = $item->stok_fisik;
        $sticker->stok_masuk = 0;
        $sticker->stok_keluar = 0;
        // live_stok will be recalculated automatically via accessor
        
        $sticker->save();
        
        // Create comprehensive audit trail
        $this->createStockAdjustmentLog(
            $item,
            'sticker',
            $oldValues['live_stok'],
            $item->stok_fisik,
            $item->stok_fisik - $oldValues['live_stok'],
            "RESET FLOW: stok_awal={$item->stok_fisik}, stok_masuk=0, stok_keluar=0"
        );
        
        \Log::info('StockOpname: Reset sticker stock via RESET FLOW', [
            'item_id' => $item->item_id,
            'item_name' => $item->item_name,
            'old_values' => $oldValues,
            'new_stok_awal' => $item->stok_fisik,
            'reset_fields' => ['stok_masuk', 'stok_keluar']
        ]);
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
     * KONDISI 1: Reset stok awal from completed opname results
     * Auto-reset all stok_awal values based on stok_fisik from opname
     *
     * @param StockOpname $opname
     * @return array
     * @throws Exception
     */
    public function resetStokAwalFromOpname(StockOpname $opname): array
    {
        if ($opname->status !== 'completed') {
            throw new Exception('Hanya opname yang sudah selesai yang dapat direset stok awalnya');
        }

        $items = $opname->items()->whereNotNull('stok_fisik')->get();
        $resetSummary = [
            'total_items' => $items->count(),
            'updated_items' => 0,
            'skipped_items' => 0,
            'failed_items' => 0,
            'errors' => []
        ];

        \Log::info('StockOpname: Starting stok awal reset from opname', [
            'opname_id' => $opname->id,
            'opname_type' => $opname->type,
            'total_items' => $resetSummary['total_items']
        ]);

        DB::beginTransaction();
        
        try {
            foreach ($items as $item) {
                try {
                    $updated = $this->resetStokAwalForItem($opname->type, $item);
                    
                    if ($updated) {
                        $resetSummary['updated_items']++;
                        
                        // Create audit log
                        $this->createStokAwalAuditLog($item, $opname->type, $item->stok_fisik, 'bulk_reset_from_opname');
                    } else {
                        $resetSummary['skipped_items']++;
                    }
                    
                } catch (\Exception $e) {
                    $resetSummary['failed_items']++;
                    $resetSummary['errors'][] = [
                        'item_id' => $item->item_id,
                        'item_name' => $item->item_name,
                        'error' => $e->getMessage()
                    ];
                    
                    \Log::error('StockOpname: Failed to reset stok awal for item', [
                        'item_id' => $item->item_id,
                        'item_name' => $item->item_name,
                        'error' => $e->getMessage()
                    ]);
                }
            }
            
            // Commit if acceptable error rate
            if ($resetSummary['failed_items'] == 0 || 
                ($resetSummary['failed_items'] / $resetSummary['total_items']) < 0.1) {
                DB::commit();
                
                \Log::info('StockOpname: Stok awal reset completed successfully', $resetSummary);
            } else {
                DB::rollback();
                throw new \Exception('Too many failed stok awal resets, transaction rolled back');
            }
            
        } catch (\Exception $e) {
            DB::rollback();
            \Log::error('StockOpname: Stok awal reset failed', [
                'opname_id' => $opname->id,
                'error' => $e->getMessage(),
                'summary' => $resetSummary
            ]);
            throw $e;
        }
        
        return $resetSummary;
    }

    /**
     * KONDISI 2: Update stok awal per row when individual item is updated
     * Real-time update of stok_awal when user updates stok_fisik
     *
     * @param StockOpname $opname
     * @param StockOpnameItem $item
     * @return array
     * @throws Exception
     */
    public function updateStokAwalPerRow(StockOpname $opname, StockOpnameItem $item): array
    {
        if ($item->stok_fisik === null) {
            throw new Exception('Stok fisik belum diinput, tidak dapat update stok awal');
        }

        \Log::info('StockOpname: Starting per-row stok awal update', [
            'opname_id' => $opname->id,
            'item_id' => $item->item_id,
            'item_name' => $item->item_name,
            'stok_fisik' => $item->stok_fisik
        ]);

        try {
            $updated = $this->resetStokAwalForItem($opname->type, $item);
            
            if ($updated) {
                // Create audit log
                $this->createStokAwalAuditLog($item, $opname->type, $item->stok_fisik, 'per_row_update');
                
                \Log::info('StockOpname: Per-row stok awal update successful', [
                    'item_id' => $item->item_id,
                    'item_name' => $item->item_name,
                    'new_stok_awal' => $item->stok_fisik
                ]);
                
                return [
                    'success' => true,
                    'message' => 'Stok awal berhasil diupdate',
                    'new_stok_awal' => $item->stok_fisik
                ];
            } else {
                return [
                    'success' => false,
                    'message' => 'Item tidak ditemukan atau tidak dapat diupdate'
                ];
            }
            
        } catch (\Exception $e) {
            \Log::error('StockOpname: Per-row stok awal update failed', [
                'item_id' => $item->item_id,
                'error' => $e->getMessage()
            ]);
            throw $e;
        }
    }

    /**
     * Reset stok awal for individual item based on opname type
     *
     * @param string $opnameType
     * @param StockOpnameItem $item
     * @return bool
     */
    private function resetStokAwalForItem(string $opnameType, StockOpnameItem $item): bool
    {
        try {
            switch ($opnameType) {
                case 'bahan_baku':
                    $inventory = InventoryBahanBaku::where('bahan_baku_id', $item->item_id)->first();
                    if ($inventory) {
                        $inventory->stok_awal = $item->stok_fisik;
                        $inventory->save();
                        return true;
                    }
                    break;

                case 'finished_goods':
                    $finishedGoods = FinishedGoods::where('product_id', $item->item_id)->first();
                    if ($finishedGoods) {
                        $finishedGoods->stok_awal = $item->stok_fisik;
                        $finishedGoods->save();
                        return true;
                    }
                    break;

                case 'sticker':
                    $sticker = \App\Models\Sticker::find($item->item_id);
                    if ($sticker) {
                        $sticker->stok_awal = $item->stok_fisik;
                        $sticker->save();
                        return true;
                    }
                    break;
            }
            
            return false;
        } catch (\Exception $e) {
            \Log::error('StockOpname: Failed to reset stok awal for item', [
                'opname_type' => $opnameType,
                'item_id' => $item->item_id,
                'error' => $e->getMessage()
            ]);
            throw $e;
        }
    }

    /**
     * Create comprehensive audit trail for stok awal changes
     *
     * @param StockOpnameItem $item
     * @param string $opnameType
     * @param float $newStokAwal
     * @param string $updateType
     * @return void
     */
    private function createStokAwalAuditLog(
        StockOpnameItem $item,
        string $opnameType,
        float $newStokAwal,
        string $updateType
    ): void {
        try {
            // Create detailed audit log
            \Log::info('StockOpname: Stok Awal Update Audit Trail', [
                'opname_id' => $item->opname_id,
                'item_id' => $item->item_id,
                'item_name' => $item->item_name,
                'opname_type' => $opnameType,
                'update_type' => $updateType,
                'new_stok_awal' => $newStokAwal,
                'stok_fisik' => $item->stok_fisik,
                'stok_sistem_before' => $item->stok_sistem,
                'variance' => $item->selisih,
                'user_id' => auth()->id() ?? 'system',
                'timestamp' => now()->toDateTimeString(),
                'ip_address' => request()->ip() ?? 'unknown'
            ]);
            
            // TODO: In future, create StokAwalAdjustment model record for database audit trail
            // This would store the audit information in a dedicated table for reporting
            
        } catch (\Exception $e) {
            \Log::error('StockOpname: Failed to create stok awal audit trail', [
                'error' => $e->getMessage(),
                'item_id' => $item->item_id,
                'opname_type' => $opnameType
            ]);
        }
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
            'finished_goods' => 'Dummy Finished Goods (Tidak ada data produk)'
        ];

        $dummyUnits = [
            'bahan_baku' => 'kg',
            'finished_goods' => 'pcs'
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
