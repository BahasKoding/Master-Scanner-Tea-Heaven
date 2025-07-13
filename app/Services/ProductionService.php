<?php

namespace App\Services;

use App\Models\CatatanProduksi;
use App\Models\Sticker;
use App\Models\BahanBaku;
use App\Models\InventoryBahanBaku;
use App\Models\FinishedGoods;
use App\Models\Product;
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
     * Create new production record with all related inventory updates
     *
     * @param array $data
     * @return CatatanProduksi
     */
    public function createProduction(array $data)
    {
        return DB::transaction(function () use ($data) {
            try {
                // Pastikan field unit ada dan valid (array)
                if (!isset($data['unit']) || !is_array($data['unit'])) {
                    Log::warning('ProductionService: Field unit tidak ada atau bukan array', ['unit' => $data['unit'] ?? null]);
                }
                // 1. Create production record
                $catatanProduksi = CatatanProduksi::create($data);

                // 2. Update finished goods stock (stok masuk)
                $this->stockService->updateStockFromProduction($catatanProduksi);

                // 3. Update raw materials inventory (terpakai)
                $this->updateRawMaterialsInventory($catatanProduksi, 'create');

                // 4. Update sticker production if applicable
                $this->updateStickerProduction($catatanProduksi->product_id);

                // 5. Log comprehensive activity
                $this->logProductionActivity('created', $catatanProduksi, $data);

                return $catatanProduksi;
            } catch (\Exception $e) {
                Log::error("Failed to create production", [
                    'error' => $e->getMessage(),
                    'trace' => $e->getTraceAsString(),
                    'data' => $data
                ]);
                throw $e;
            }
        });
    }

    /**
     * Update production record with all related inventory updates
     *
     * @param CatatanProduksi $catatanProduksi
     * @param array $data
     * @return CatatanProduksi
     */
    public function updateProduction(CatatanProduksi $catatanProduksi, array $data)
    {
        return DB::transaction(function () use ($catatanProduksi, $data) {
            try {
                // Pastikan field unit ada dan valid (array)
                if (!isset($data['unit']) || !is_array($data['unit'])) {
                    Log::warning('ProductionService: Field unit tidak ada atau bukan array (update)', ['unit' => $data['unit'] ?? null]);
                }
                // Store old values for inventory adjustments
                $oldProductId = $catatanProduksi->product_id;
                $oldQuantity = $catatanProduksi->quantity;
                $oldBahanBakuIds = $catatanProduksi->sku_induk ?? [];

                // 1. Update production record
                $catatanProduksi->update($data);
                $catatanProduksi->refresh();

                // 2. Update finished goods stock if product or quantity changed
                if ($oldProductId !== $catatanProduksi->product_id || $oldQuantity !== $catatanProduksi->quantity) {
                    $this->stockService->updateStockFromProductionChange($catatanProduksi);
                }

                // 3. Update raw materials inventory for both old and new materials
                $this->updateRawMaterialsInventory($catatanProduksi, 'update', $oldBahanBakuIds);

                // 4. Update sticker production for both old and new products if changed
                if ($oldProductId !== $catatanProduksi->product_id) {
                    $this->updateStickerProduction($oldProductId);
                    $this->updateStickerProduction($catatanProduksi->product_id);
                } else {
                    $this->updateStickerProduction($catatanProduksi->product_id);
                }

                // 5. Log comprehensive activity
                $this->logProductionActivity('updated', $catatanProduksi, $data, [
                    'old_product_id' => $oldProductId,
                    'old_quantity' => $oldQuantity,
                    'old_bahan_baku_ids' => $oldBahanBakuIds
                ]);

                return $catatanProduksi;
            } catch (\Exception $e) {
                Log::error("Failed to update production", [
                    'error' => $e->getMessage(),
                    'trace' => $e->getTraceAsString(),
                    'production_id' => $catatanProduksi->id,
                    'data' => $data
                ]);
                throw $e;
            }
        });
    }

    /**
     * Delete production record with all related inventory updates
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
                $bahanBakuIds = $catatanProduksi->sku_induk ?? [];

                // 1. Remove stock from finished goods
                $this->stockService->removeStockFromProduction($catatanProduksi);

                // 2. Update raw materials inventory (remove terpakai)
                $this->updateRawMaterialsInventory($catatanProduksi, 'delete');

                // 3. Delete production record
                $result = $catatanProduksi->delete();

                // 4. Update sticker production
                $this->updateStickerProduction($productId);

                // 5. Log comprehensive activity
                $this->logProductionActivity('deleted', null, [], [
                    'deleted_product_id' => $productId,
                    'deleted_quantity' => $quantity,
                    'deleted_bahan_baku_ids' => $bahanBakuIds
                ]);

                return $result;
            } catch (\Exception $e) {
                Log::error("Failed to delete production", [
                    'error' => $e->getMessage(),
                    'trace' => $e->getTraceAsString(),
                    'production_id' => $catatanProduksi->id
                ]);
                throw $e;
            }
        });
    }

    /**
     * Update raw materials inventory based on production changes
     *
     * @param CatatanProduksi $catatanProduksi
     * @param string $operation
     * @param array $oldBahanBakuIds
     * @return void
     */
    private function updateRawMaterialsInventory(CatatanProduksi $catatanProduksi, string $operation, array $oldBahanBakuIds = [])
    {
        try {
            $currentBahanBakuIds = $catatanProduksi->sku_induk ?? [];

            // For update operations, recalculate all affected bahan baku
            if ($operation === 'update') {
                $allAffectedIds = array_unique(array_merge($oldBahanBakuIds, $currentBahanBakuIds));
            } else {
                $allAffectedIds = $currentBahanBakuIds;
            }

            // Update inventory for all affected bahan baku
            foreach ($allAffectedIds as $bahanBakuId) {
                InventoryBahanBaku::recalculateTerpakaiFromProduksi($bahanBakuId);

                Log::info("Raw material inventory updated", [
                    'operation' => $operation,
                    'bahan_baku_id' => $bahanBakuId,
                    'production_id' => $catatanProduksi->id ?? 'deleted'
                ]);
            }
        } catch (\Exception $e) {
            Log::error("Failed to update raw materials inventory", [
                'error' => $e->getMessage(),
                'operation' => $operation,
                'production_id' => $catatanProduksi->id ?? 'unknown',
                'bahan_baku_ids' => $currentBahanBakuIds ?? []
            ]);
            throw $e;
        }
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
     * Log comprehensive production activity
     *
     * @param string $action
     * @param CatatanProduksi|null $catatanProduksi
     * @param array $data
     * @param array $additional
     * @return void
     */
    private function logProductionActivity(string $action, ?CatatanProduksi $catatanProduksi, array $data = [], array $additional = [])
    {
        $logData = [
            'action' => $action,
            'production_id' => $catatanProduksi->id ?? null,
            'product_id' => $catatanProduksi->product_id ?? $additional['deleted_product_id'] ?? null,
            'quantity' => $catatanProduksi->quantity ?? $additional['deleted_quantity'] ?? null,
            'packaging' => $catatanProduksi->packaging ?? null,
            'bahan_baku_ids' => $catatanProduksi->sku_induk ?? $additional['deleted_bahan_baku_ids'] ?? [],
            'timestamp' => now()->toDateTimeString()
        ];

        // Add additional context for updates
        if ($action === 'updated' && !empty($additional)) {
            $logData['changes'] = $additional;
        }

        Log::info("Production {$action} successfully", $logData);
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
            $syncResults = [
                'total_processed' => 0,
                'successful' => 0,
                'failed' => 0,
                'details' => []
            ];

            foreach ($productions as $production) {
                $syncResults['total_processed']++;

                try {
                    // Re-sync finished goods stock for this production
                    $this->stockService->updateStockFromProduction($production);

                    // Re-sync raw materials inventory
                    $this->updateRawMaterialsInventory($production, 'sync');

                    // Re-sync sticker production
                    $this->updateStickerProduction($production->product_id);

                    $syncResults['successful']++;
                    $syncResults['details'][] = [
                        'production_id' => $production->id,
                        'product_id' => $production->product_id,
                        'status' => 'success',
                        'synced_at' => now()->toDateTimeString()
                    ];
                } catch (\Exception $e) {
                    $syncResults['failed']++;
                    $syncResults['details'][] = [
                        'production_id' => $production->id,
                        'product_id' => $production->product_id,
                        'status' => 'error',
                        'message' => $e->getMessage(),
                        'failed_at' => now()->toDateTimeString()
                    ];

                    Log::error("Failed to sync production data", [
                        'production_id' => $production->id,
                        'error' => $e->getMessage()
                    ]);
                }
            }

            Log::info("Production data sync completed", $syncResults);
            return $syncResults;
        });
    }

    /**
     * Get production statistics
     *
     * @param array $filters
     * @return array
     */
    public function getProductionStatistics(array $filters = [])
    {
        try {
            $query = CatatanProduksi::query();

            // Apply filters
            if (!empty($filters['product_id'])) {
                $query->where('product_id', $filters['product_id']);
            }

            if (!empty($filters['start_date'])) {
                $query->whereDate('created_at', '>=', $filters['start_date']);
            }

            if (!empty($filters['end_date'])) {
                $query->whereDate('created_at', '<=', $filters['end_date']);
            }

            if (!empty($filters['bahan_baku_id'])) {
                $query->whereJsonContains('sku_induk', $filters['bahan_baku_id']);
            }

            $productions = $query->get();

            $statistics = [
                'summary' => [
                    'total_production_records' => $productions->count(),
                    'total_quantity_produced' => $productions->sum('quantity'),
                    'unique_products' => $productions->pluck('product_id')->unique()->count(),
                    'date_range' => [
                        'earliest' => $productions->min('created_at'),
                        'latest' => $productions->max('created_at')
                    ]
                ],
                'by_product' => [],
                'by_bahan_baku' => [],
                'by_month' => [],
                'filters_applied' => $filters,
                'generated_at' => now()->toDateTimeString()
            ];

            // Group by product
            $productGroups = $productions->groupBy('product_id');
            foreach ($productGroups as $productId => $productProductions) {
                $product = Product::find($productId);
                $statistics['by_product'][] = [
                    'product_id' => $productId,
                    'product_name' => $product->name_product ?? 'Unknown',
                    'product_sku' => $product->sku ?? 'Unknown',
                    'total_records' => $productProductions->count(),
                    'total_quantity' => $productProductions->sum('quantity'),
                    'avg_quantity_per_record' => round($productProductions->avg('quantity'), 2)
                ];
            }

            // Group by bahan baku
            $bahanBakuUsage = [];
            foreach ($productions as $production) {
                if (is_array($production->sku_induk)) {
                    foreach ($production->sku_induk as $index => $bahanBakuId) {
                        if (!isset($bahanBakuUsage[$bahanBakuId])) {
                            $bahanBaku = BahanBaku::find($bahanBakuId);
                            $bahanBakuUsage[$bahanBakuId] = [
                                'bahan_baku_id' => $bahanBakuId,
                                'nama_barang' => $bahanBaku->nama_barang ?? 'Unknown',
                                'sku_induk' => $bahanBaku->sku_induk ?? 'Unknown',
                                'usage_count' => 0,
                                'total_gramasi' => 0,
                                'total_terpakai' => 0
                            ];
                        }

                        $bahanBakuUsage[$bahanBakuId]['usage_count']++;
                        $bahanBakuUsage[$bahanBakuId]['total_gramasi'] += $production->gramasi[$index] ?? 0;
                        $bahanBakuUsage[$bahanBakuId]['total_terpakai'] += $production->total_terpakai[$index] ?? 0;
                    }
                }
            }
            $statistics['by_bahan_baku'] = array_values($bahanBakuUsage);

            // Group by month
            $monthlyGroups = $productions->groupBy(function ($production) {
                return $production->created_at->format('Y-m');
            });
            foreach ($monthlyGroups as $month => $monthProductions) {
                $statistics['by_month'][] = [
                    'month' => $month,
                    'total_records' => $monthProductions->count(),
                    'total_quantity' => $monthProductions->sum('quantity'),
                    'unique_products' => $monthProductions->pluck('product_id')->unique()->count()
                ];
            }

            return $statistics;
        } catch (\Exception $e) {
            Log::error("Failed to generate production statistics", [
                'error' => $e->getMessage(),
                'filters' => $filters
            ]);
            throw $e;
        }
    }

    /**
     * Verify production data consistency
     *
     * @param int|null $productId
     * @return array
     */
    public function verifyProductionConsistency($productId = null)
    {
        return DB::transaction(function () use ($productId) {
            $query = CatatanProduksi::query();

            if ($productId) {
                $query->where('product_id', $productId);
            }

            $productions = $query->get();
            $inconsistencies = [];

            foreach ($productions as $production) {
                $issues = [];

                // Check if product exists
                $product = Product::find($production->product_id);
                if (!$product) {
                    $issues[] = 'Product not found';
                }

                // Check if all bahan baku exist
                if (is_array($production->sku_induk)) {
                    foreach ($production->sku_induk as $bahanBakuId) {
                        $bahanBaku = BahanBaku::find($bahanBakuId);
                        if (!$bahanBaku) {
                            $issues[] = "Bahan baku ID {$bahanBakuId} not found";
                        }
                    }
                }

                // Check array lengths consistency
                if (!$production->validateArrayLengths()) {
                    $issues[] = 'Inconsistent array lengths for sku_induk, gramasi, and total_terpakai';
                }

                // Check calculation accuracy
                if (!$production->validateCalculations()) {
                    $issues[] = 'Incorrect total_terpakai calculations';
                }

                if (!empty($issues)) {
                    $inconsistencies[] = [
                        'production_id' => $production->id,
                        'product_id' => $production->product_id,
                        'issues' => $issues,
                        'data' => [
                            'sku_induk_count' => count($production->sku_induk ?? []),
                            'gramasi_count' => count($production->gramasi ?? []),
                            'total_terpakai_count' => count($production->total_terpakai ?? [])
                        ]
                    ];
                }
            }

            $result = [
                'total_checked' => $productions->count(),
                'inconsistencies_found' => count($inconsistencies),
                'is_consistent' => empty($inconsistencies),
                'details' => $inconsistencies,
                'checked_at' => now()->toDateTimeString()
            ];

            Log::info("Production consistency check completed", [
                'total_checked' => $result['total_checked'],
                'inconsistencies_found' => $result['inconsistencies_found']
            ]);

            return $result;
        });
    }
}
