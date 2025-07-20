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
                    $item->stok_fisik = $itemData['stok_fisik'];
                    $item->selisih = $itemData['stok_fisik'] - $item->stok_sistem;
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
        $items = $this->getItemsByType($opname->type);

        if ($items->isEmpty()) {
            // Create at least one dummy item to prevent complete failure
            $this->createDummyItem($opname);
            return;
        }

        // Insert items
        foreach ($items as $itemData) {
            StockOpnameItem::create([
                'opname_id' => $opname->id,
                'item_id' => $itemData['item_id'],
                'item_name' => $itemData['item_name'],
                'item_sku' => $itemData['item_sku'] ?? null,
                'stok_sistem' => $itemData['stok_sistem'],
                'satuan' => $itemData['satuan']
            ]);
        }
    }

    /**
     * Get items by opname type
     *
     * @param string $type
     * @return Collection
     */
    private function getItemsByType(string $type): Collection
    {
        try {
            switch ($type) {
                case 'bahan_baku':
                    $inventories = InventoryBahanBaku::with('bahanBaku')->get();
                    if ($inventories->isEmpty()) {
                        // Fallback: get from BahanBaku directly
                        $bahanBakus = \App\Models\BahanBaku::all();
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
                    return $inventories->map(function ($inventory) {
                        return [
                            'item_id' => $inventory->bahanBaku->id,
                            'item_name' => $inventory->bahanBaku->nama_barang ?? 'Unknown',
                            'item_sku' => $inventory->bahanBaku->sku_induk ?? '-',
                            'stok_sistem' => $inventory->live_stok_gudang ?? 0, // From InventoryBahanBaku
                            'satuan' => $inventory->bahanBaku->satuan ?? 'kg'
                        ];
                    });

                case 'finished_goods':
                    $finishedGoods = FinishedGoods::with('product')->get();
                    if ($finishedGoods->isEmpty()) {
                        // Fallback: get from Product directly
                        $products = \App\Models\Product::all();
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
                    return $finishedGoods->map(function ($finished) {
                        return [
                            'item_id' => $finished->product->id,
                            'item_name' => $finished->product->name_product ?? 'Unknown',
                            'item_sku' => $finished->product->sku ?? '-',
                            'stok_sistem' => $finished->live_stock ?? 0, // From FinishedGoods
                            'satuan' => 'pcs'
                        ];
                    });

                case 'sticker':
                    $stickers = Sticker::all();
                    return $stickers->map(function ($sticker) {
                        return [
                            'item_id' => $sticker->id,
                            'item_name' => $sticker->ukuran ?? 'Unknown',
                            'stok_sistem' => $sticker->stok_stiker ?? 0,
                            'satuan' => 'pcs'
                        ];
                    });

                default:
                    return collect([]);
            }
        } catch (Exception $e) {
            // Return empty collection on any database error
            return collect([]);
        }
    }

    /**
     * Update system stock based on physical count
     *
     * @param StockOpname $opname
     * @return void
     * @throws Exception
     */
    private function updateSystemStock(StockOpname $opname): void
    {
        $items = $opname->items()->whereNotNull('stok_fisik')->get();

        foreach ($items as $item) {
            if ($item->selisih == 0) continue; // No difference, skip

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
            }
        }
    }

    /**
     * Update bahan baku stock
     *
     * @param StockOpnameItem $item
     * @return void
     */
    private function updateBahanBakuStock(StockOpnameItem $item): void
    {
        $inventory = InventoryBahanBaku::where('id_bahan_baku', $item->item_id)->first();
        if ($inventory) {
            // Update stok_masuk to reflect the physical count
            $newStokMasuk = $inventory->stok_masuk + $item->selisih;
            $inventory->stok_masuk = max(0, $newStokMasuk);
            $inventory->save();
        }
    }

    /**
     * Update finished goods stock
     *
     * @param StockOpnameItem $item
     * @return void
     */
    private function updateFinishedGoodsStock(StockOpnameItem $item): void
    {
        $finished = FinishedGoods::where('id_product', $item->item_id)->first();
        if ($finished) {
            $finished->stok_finished_goods = $item->stok_fisik;
            $finished->save();
        }
    }

    /**
     * Update sticker stock
     *
     * @param StockOpnameItem $item
     * @return void
     */
    private function updateStickerStock(StockOpnameItem $item): void
    {
        $sticker = Sticker::find($item->item_id);
        if ($sticker) {
            $sticker->stok_stiker = $item->stok_fisik;
            $sticker->save();
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
