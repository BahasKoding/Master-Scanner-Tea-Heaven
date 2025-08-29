<?php

namespace App\Services;

use App\Models\FinishedGoods;
use App\Models\Product;
use App\Models\CatatanProduksi;
use App\Models\HistorySale;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class FinishedGoodsService
{
    protected $stockService;

    public function __construct(StockService $stockService)
    {
        $this->stockService = $stockService;
    }

    /**
     * Create or update finished goods record with proper stock integration
     *
     * @param array $data
     * @return FinishedGoods
     */
    public function createOrUpdateFinishedGoods(array $data)
    {
        return DB::transaction(function () use ($data) {
            try {
                // Get or create finished goods record
                $finishedGoods = FinishedGoods::firstOrNew(['product_id' => $data['product_id']]);

                // Set manual input values
                $finishedGoods->stok_awal = $data['stok_awal'];
                $finishedGoods->defective = $data['defective'];

                // Auto-calculate stock values from related data
                $this->updateStockFromRelatedData($finishedGoods);

                // Save the record
                $finishedGoods->save();

                // Log the operation
                $action = $finishedGoods->wasRecentlyCreated ? 'created' : 'updated';
                Log::info("FinishedGoods {$action} successfully", [
                    'finished_goods_id' => $finishedGoods->id,
                    'product_id' => $finishedGoods->product_id,
                    'stok_awal' => $finishedGoods->stok_awal,
                    'defective' => $finishedGoods->defective,
                    'live_stock' => $finishedGoods->live_stock
                ]);

                return $finishedGoods;
            } catch (\Exception $e) {
                Log::error('Failed to create/update finished goods', [
                    'error' => $e->getMessage(),
                    'data' => $data,
                    'trace' => $e->getTraceAsString()
                ]);
                throw $e;
            }
        });
    }

    /**
     * Update finished goods record
     *
     * @param FinishedGoods $finishedGoods
     * @param array $data
     * @param string|null $filterMonthYear
     * @return FinishedGoods
     */
    public function updateFinishedGoods(FinishedGoods $finishedGoods, array $data, $filterMonthYear = null)
    {
        return DB::transaction(function () use ($finishedGoods, $data, $filterMonthYear) {
            try {
                // Update manual input values
                $finishedGoods->stok_awal = $data['stok_awal'];
                $finishedGoods->defective = $data['defective'];

                // IMPORTANT: Only update auto-calculated values if no monthly filter is applied
                // When monthly filter is active, we should not modify the stored stok_masuk/stok_keluar
                // as they represent the cumulative values, not monthly values
                if (!$filterMonthYear) {
                    // Auto-calculate stock values from related data only for all-time view
                    $this->updateStockFromRelatedData($finishedGoods);
                }

                // Verify data integrity before save
                if (!is_numeric($finishedGoods->stok_awal) || !is_numeric($finishedGoods->defective)) {
                    throw new \InvalidArgumentException('Invalid numeric values for stok_awal or defective');
                }

                // Save the record
                $finishedGoods->save();

                // Only recalculate live_stock if not using monthly filter
                if (!$filterMonthYear) {
                    // Trigger recalculation to ensure live_stock is updated
                    $finishedGoods->recalculateLiveStock();
                }

                return $finishedGoods;
            } catch (\Exception $e) {
                Log::error('Failed to update finished goods', [
                    'finished_goods_id' => $finishedGoods->id ?? 'unknown',
                    'product_id' => $finishedGoods->product_id ?? 'unknown',
                    'error_message' => $e->getMessage(),
                    'input_data' => $data,
                    'filter_month_year' => $filterMonthYear
                ]);
                throw $e;
            }
        });
    }

    /**
     * Get finished goods for a product (create if not exists)
     *
     * @param int $productId
     * @return FinishedGoods
     */
    public function getFinishedGoodsForProduct(int $productId)
    {
        try {
            // Find the product first
            $product = Product::findOrFail($productId);

            // Find or create default finished goods record
            $finishedGoods = FinishedGoods::firstOrNew(['product_id' => $productId]);

            // If it's a new record, set default values
            if (!$finishedGoods->exists) {
                $finishedGoods->stok_awal = 0;
                $finishedGoods->defective = 0;
                $finishedGoods->stok_sisa = 0;

                // Auto-calculate stock values from related data
                $this->updateStockFromRelatedData($finishedGoods);
            }

            // Add product information to the response
            $finishedGoods->product_id = $productId;
            $finishedGoods->product = $product;

            return $finishedGoods;
        } catch (\Exception $e) {
            Log::error('Failed to get finished goods for product', [
                'product_id' => $productId,
                'error' => $e->getMessage()
            ]);
            throw $e;
        }
    }

    /**
     * Update stock values from related data (CatatanProduksi, Purchase, and HistorySale)
     * Consolidated approach for both monthly and all-time calculations
     *
     * @param FinishedGoods $finishedGoods
     * @param string|null $filterMonthYear
     * @return void
     */
    private function updateStockFromRelatedData(FinishedGoods $finishedGoods, $filterMonthYear = null)
    {
        try {
            // Calculate stok_masuk using unified approach
            $finishedGoods->updateStokMasukFromAllSources();

            // Calculate stok_keluar from history sales
            $finishedGoods->updateStokKeluarFromSales();

            // Calculate stok_sisa from opname data
            $finishedGoods->updateStokSisaFromOpname();

            // Recalculate live stock with the updated values
            $finishedGoods->recalculateLiveStock();
        } catch (\Exception $e) {
            Log::error('Failed to update stock from related data', [
                'product_id' => $finishedGoods->product_id,
                'filter_month_year' => $filterMonthYear,
                'error' => $e->getMessage()
            ]);
            throw $e;
        }
    }

    /**
     * Calculate stok masuk from production and purchases
     * Unified method for both monthly and all-time calculations
     *
     * @param int $productId
     * @param string|null $filterMonthYear
     * @return int
     */
    private function calculateStokMasuk($productId, $filterMonthYear = null)
    {
        try {
            // Production query
            $productionQuery = CatatanProduksi::where('product_id', $productId);
            
            // Purchase query
            $purchaseQuery = \App\Models\Purchase::where('bahan_baku_id', $productId)
                ->where('kategori', 'finished_goods');
            
            // Apply monthly filter if provided
            if ($filterMonthYear) {
                $this->applyMonthlyFilter($productionQuery, $filterMonthYear);
                $this->applyMonthlyFilter($purchaseQuery, $filterMonthYear);
            }
            
            $stokMasukProduction = $productionQuery->sum('quantity');
            $stokMasukPurchases = $purchaseQuery->sum('total_stok_masuk');
            
            return $stokMasukProduction + $stokMasukPurchases;
        } catch (\Exception $e) {
            Log::error('Error calculating stok_masuk', [
                'product_id' => $productId,
                'filter_month_year' => $filterMonthYear,
                'error' => $e->getMessage()
            ]);
            return 0;
        }
    }

    /**
     * Calculate stok keluar from sales
     * Unified method for both monthly and all-time calculations
     *
     * @param int $productId
     * @param string|null $filterMonthYear
     * @return int
     */
    private function calculateStokKeluar($productId, $filterMonthYear = null)
    {
        try {
            $product = \App\Models\Product::find($productId);
            if (!$product) {
                return 0;
            }

            // Get history sales query
            $salesQuery = HistorySale::whereNotNull('no_sku');
            
            // Apply monthly filter if provided
            if ($filterMonthYear) {
                $this->applyMonthlyFilter($salesQuery, $filterMonthYear);
            }
            
            return $this->calculateSalesFromHistory($salesQuery->get(), $product->sku);
        } catch (\Exception $e) {
            Log::error('Error calculating stok_keluar', [
                'product_id' => $productId,
                'filter_month_year' => $filterMonthYear,
                'error' => $e->getMessage()
            ]);
            return 0;
        }
    }

    /**
     * Apply monthly filter to query
     *
     * @param \Illuminate\Database\Eloquent\Builder $query
     * @param string $filterMonthYear
     * @return void
     */
    private function applyMonthlyFilter($query, $filterMonthYear)
    {
        $year = date('Y', strtotime($filterMonthYear . '-01'));
        $month = date('m', strtotime($filterMonthYear . '-01'));
        
        $query->whereYear('created_at', $year)
              ->whereMonth('created_at', $month);
    }

    /**
     * Calculate sales quantity from history sales data
     *
     * @param \Illuminate\Database\Eloquent\Collection $historySales
     * @param string $productSku
     * @return int
     */
    private function calculateSalesFromHistory($historySales, $productSku)
    {
        $totalSales = 0;
        
        foreach ($historySales as $sale) {
            try {
                $skuArray = is_string($sale->no_sku) ? json_decode($sale->no_sku, true) : $sale->no_sku;
                $qtyArray = is_string($sale->qty) ? json_decode($sale->qty, true) : $sale->qty;

                if (!is_array($skuArray) || !is_array($qtyArray) || count($skuArray) !== count($qtyArray)) {
                    continue;
                }

                foreach ($skuArray as $index => $sku) {
                    if (trim($sku) === $productSku) {
                        $quantity = $qtyArray[$index] ?? 0;
                        if (is_numeric($quantity) && $quantity > 0) {
                            $totalSales += (int)$quantity;
                        }
                    }
                }
            } catch (\Exception $e) {
                // Skip problematic sales data
                continue;
            }
        }
        
        return $totalSales;
    }

    /**
     * Sync all finished goods stock data with related tables
     * Process in chunks to prevent timeout with large datasets
     *
     * @param int|null $productId Specific product ID or null for all products
     * @param int $chunkSize Size of each processing chunk
     * @param int|null $offset Starting offset for processing
     * @param int|null $totalRecords Total records to be processed (for progress tracking)
     * @return array
     */
    public function syncFinishedGoodsStock($productId = null, $chunkSize = 50, $offset = 0, $totalRecords = null, $filterMonthYear = null)
    {
        try {
            // Get products to sync
            $query = Product::query();
            if ($productId) {
                $query->where('id', $productId);
            }
            
            // Get total count if not provided (first call)
            if ($totalRecords === null) {
                $totalRecords = $query->count();
            }
            
            // No records to process
            if ($totalRecords == 0) {
                return [
                    'success' => true,
                    'message' => 'No products found to sync',
                    'progress' => 100,
                    'completed' => true,
                    'total_records' => 0,
                    'processed_records' => 0,
                    'results' => []
                ];
            }
            
            // Get chunk of products to process in this request
            $products = $query->skip($offset)->take($chunkSize)->get();
            $syncResults = [];
            $processedCount = 0;
            
            // Process each product in the chunk using individual transactions
            foreach ($products as $product) {
                DB::beginTransaction();
                try {
                    // Get or create finished goods record
                    $finishedGoods = FinishedGoods::firstOrNew(['product_id' => $product->id]);

                    // If it's a new record, set default values
                    if (!$finishedGoods->exists) {
                        $finishedGoods->stok_awal = 0;
                        $finishedGoods->defective = 0;
                        $finishedGoods->stok_sisa = 0;
                    }

                    // IMPORTANT: Only update stored values when no monthly filter is applied
                    // Monthly filter should only affect display, not stored data
                    if (!$filterMonthYear) {
                        // Update stock from related data with monthly filter
                        $this->updateStockFromRelatedData($finishedGoods, $filterMonthYear);
                    }
                    // If monthly filter is active, we only save manual changes (stok_awal, defective)
                    // without modifying the stored stok_masuk/stok_keluar values

                    // Save the record
                    $finishedGoods->save();
                    DB::commit();
                    
                    $syncResults[] = [
                        'status' => 'success',
                        'product_id' => $product->id,
                        'product_name' => $product->name_product,
                        'stok_masuk' => $finishedGoods->stok_masuk,
                        'stok_keluar' => $finishedGoods->stok_keluar,
                        'live_stock' => $finishedGoods->live_stock,
                        'monthly_filter_applied' => $filterMonthYear ? true : false
                    ];
                    $processedCount++;
                } catch (\Exception $e) {
                    DB::rollBack();
                    $syncResults[] = [
                        'status' => 'error',
                        'product_id' => $product->id,
                        'product_name' => $product->name_product,
                        'error' => $e->getMessage()
                    ];
                    Log::error('Failed to sync individual finished goods', [
                        'product_id' => $product->id,
                        'error' => $e->getMessage()
                    ]);
                }
            }

            $successCount = collect($syncResults)->where('status', 'success')->count();
            $errorCount = collect($syncResults)->where('status', 'error')->count();
            
            // Calculate progress
            $newOffset = $offset + $chunkSize;
            $isCompleted = $newOffset >= $totalRecords;
            $progress = min(100, round(($newOffset / $totalRecords) * 100));
            
            Log::info('Finished goods stock sync chunk processed', [
                'chunk_size' => $chunkSize,
                'offset' => $offset,
                'new_offset' => $newOffset,
                'total_records' => $totalRecords,
                'success_count' => $successCount,
                'error_count' => $errorCount,
                'progress' => $progress,
                'completed' => $isCompleted
            ]);

            return [
                'success' => true,
                'message' => 'Processed ' . $processedCount . ' records (Success: ' . $successCount . ', Error: ' . $errorCount . ')',
                'progress' => $progress,
                'completed' => $isCompleted,
                'total_records' => $totalRecords,
                'processed_records' => $newOffset,
                'next_offset' => $isCompleted ? null : $newOffset,
                'results' => $syncResults
            ];
        } catch (\Exception $e) {
            Log::error('Failed to sync finished goods stock chunk', [
                'product_id' => $productId,
                'offset' => $offset,
                'chunk_size' => $chunkSize,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            
            return [
                'success' => false,
                'message' => 'Error processing batch: ' . $e->getMessage(),
                'total_records' => $totalRecords,
                'processed_records' => $offset,
                'progress' => $offset > 0 ? round(($offset / $totalRecords) * 100) : 0,
                'completed' => false,
                'error' => $e->getMessage()
            ];
        }
    }

    /**
     * Get finished goods statistics for a specific product
     *
     * @param int $productId
     * @return array
     */
    public function getFinishedGoodsStatistics(int $productId)
    {
        try {
            $product = Product::findOrFail($productId);
            $finishedGoods = FinishedGoods::where('product_id', $productId)->first();

            // Calculate dynamic values from all sources
            $stokMasukFromProduksi = CatatanProduksi::where('product_id', $productId)->sum('quantity');
            $stokMasukFromPurchases = Purchase::where('bahan_baku_id', $productId)
                ->where('kategori', 'finished_goods')
                ->sum('total_stok_masuk');
            $totalStokMasuk = $stokMasukFromProduksi + $stokMasukFromPurchases;
            $stokKeluarFromSales = $this->calculateStokKeluarFromSales($product);

            // Get production statistics
            $productionStats = CatatanProduksi::where('product_id', $productId)
                ->selectRaw('
                    COUNT(*) as total_production_records,
                    SUM(quantity) as total_quantity_produced,
                    MIN(created_at) as first_production_date,
                    MAX(created_at) as last_production_date
                ')
                ->first();

            // Get sales statistics
            $salesStats = $this->getSalesStatistics($product);

            $statistics = [
                'product_info' => [
                    'id' => $product->id,
                    'name' => $product->name_product,
                    'sku' => $product->sku,
                    'category' => $product->category_name,
                    'packaging' => $product->packaging
                ],
                'current_stock' => [
                    'stok_awal' => $finishedGoods->stok_awal ?? 0,
                    'stok_masuk' => $finishedGoods->stok_masuk ?? 0,
                    'stok_keluar' => $finishedGoods->stok_keluar ?? 0,
                    'defective' => $finishedGoods->defective ?? 0,
                    'live_stock' => $finishedGoods->live_stock ?? 0
                ],
                'dynamic_calculations' => [
                    'stok_masuk_from_produksi' => $stokMasukFromProduksi,
                    'stok_masuk_from_purchases' => $stokMasukFromPurchases,
                    'total_stok_masuk_combined' => $totalStokMasuk,
                    'stok_keluar_from_sales' => $stokKeluarFromSales,
                    'calculated_live_stock' => max(0, ($finishedGoods->stok_awal ?? 0) + $totalStokMasuk - $stokKeluarFromSales - ($finishedGoods->defective ?? 0))
                ],
                'production_statistics' => [
                    'total_production_records' => $productionStats->total_production_records ?? 0,
                    'total_quantity_produced' => $productionStats->total_quantity_produced ?? 0,
                    'first_production_date' => $productionStats->first_production_date,
                    'last_production_date' => $productionStats->last_production_date
                ],
                'sales_statistics' => $salesStats,
                'consistency_check' => [
                    'stok_masuk_consistent' => ($finishedGoods->stok_masuk ?? 0) == $stokMasukFromProduksi,
                    'stok_keluar_consistent' => ($finishedGoods->stok_keluar ?? 0) == $stokKeluarFromSales,
                    'live_stock_accurate' => ($finishedGoods->live_stock ?? 0) == max(0, ($finishedGoods->stok_awal ?? 0) + $stokMasukFromProduksi - $stokKeluarFromSales - ($finishedGoods->defective ?? 0))
                ]
            ];

            return $statistics;
        } catch (\Exception $e) {
            Log::error('Failed to get finished goods statistics', [
                'product_id' => $productId,
                'error' => $e->getMessage()
            ]);
            throw $e;
        }
    }

    /**
     * Calculate stok keluar from sales data
     *
     * @param Product $product
     * @return int
     */
    private function calculateStokKeluarFromSales(Product $product)
    {
        try {
            $totalSales = 0;
            $historySales = HistorySale::whereNotNull('no_sku')->get();

            foreach ($historySales as $sale) {
                try {
                    $skuArray = is_string($sale->no_sku) ? json_decode($sale->no_sku, true) : $sale->no_sku;
                    $qtyArray = is_string($sale->qty) ? json_decode($sale->qty, true) : $sale->qty;

                    // Validate data integrity
                    if (!is_array($skuArray) || !is_array($qtyArray)) {
                        continue;
                    }

                    // Ensure arrays have same length
                    if (count($skuArray) !== count($qtyArray)) {
                        continue;
                    }

                    foreach ($skuArray as $index => $sku) {
                        if (trim($sku) === $product->sku) {
                            $quantity = $qtyArray[$index] ?? 0;
                            if (is_numeric($quantity) && $quantity > 0) {
                                $totalSales += (int)$quantity;
                            }
                        }
                    }
                } catch (\Exception $saleError) {
                    // Skip problematic sales data
                    continue;
                }
            }

            return $totalSales;
        } catch (\Exception $e) {
            Log::error('Failed to calculate stok keluar from sales', [
                'product_id' => $product->id,
                'error' => $e->getMessage()
            ]);
            return 0;
        }
    }

    /**
     * Get sales statistics for a product
     *
     * @param Product $product
     * @return array
     */
    private function getSalesStatistics(Product $product)
    {
        try {
            $salesData = [];
            $historySales = HistorySale::whereNotNull('no_sku')->get();
            $totalSalesRecords = 0;
            $totalQuantitySold = 0;
            $firstSaleDate = null;
            $lastSaleDate = null;

            foreach ($historySales as $sale) {
                try {
                    $skuArray = is_string($sale->no_sku) ? json_decode($sale->no_sku, true) : $sale->no_sku;
                    $qtyArray = is_string($sale->qty) ? json_decode($sale->qty, true) : $sale->qty;

                    if (!is_array($skuArray) || !is_array($qtyArray)) {
                        continue;
                    }

                    if (count($skuArray) !== count($qtyArray)) {
                        continue;
                    }

                    foreach ($skuArray as $index => $sku) {
                        if (trim($sku) === $product->sku) {
                            $quantity = $qtyArray[$index] ?? 0;
                            if (is_numeric($quantity) && $quantity > 0) {
                                $totalSalesRecords++;
                                $totalQuantitySold += (int)$quantity;

                                if (!$firstSaleDate || $sale->created_at < $firstSaleDate) {
                                    $firstSaleDate = $sale->created_at;
                                }

                                if (!$lastSaleDate || $sale->created_at > $lastSaleDate) {
                                    $lastSaleDate = $sale->created_at;
                                }
                            }
                            break; // Found the SKU in this sale, no need to check other items
                        }
                    }
                } catch (\Exception $saleError) {
                    continue;
                }
            }

            return [
                'total_sales_records' => $totalSalesRecords,
                'total_quantity_sold' => $totalQuantitySold,
                'first_sale_date' => $firstSaleDate,
                'last_sale_date' => $lastSaleDate
            ];
        } catch (\Exception $e) {
            Log::error('Failed to get sales statistics', [
                'product_id' => $product->id,
                'error' => $e->getMessage()
            ]);

            return [
                'total_sales_records' => 0,
                'total_quantity_sold' => 0,
                'first_sale_date' => null,
                'last_sale_date' => null
            ];
        }
    }

    /**
     * Reset finished goods to default values
     *
     * @param int $productId
     * @return FinishedGoods
     */
    public function resetProductStock(int $productId, $filterMonthYear = null)
    {
        return DB::transaction(function () use ($productId, $filterMonthYear) {
            try {
                // Find or create the finished goods record
                $finishedGoods = FinishedGoods::firstOrNew(['product_id' => $productId]);

                // Reset manual fields to zero
                $finishedGoods->stok_awal = 0;
                $finishedGoods->defective = 0;
                $finishedGoods->stok_sisa = 0;

                // Recalculate stok_masuk and stok_keluar based on the filter
                $this->updateStockFromRelatedData($finishedGoods, $filterMonthYear);

                // Save the changes
                $finishedGoods->save();

                // Recalculate live stock
                $finishedGoods->recalculateLiveStock();

                // Log the reset operation
                Log::info('Product stock reset successfully', [
                    'product_id' => $productId,
                    'filter_month_year' => $filterMonthYear,
                    'new_stock_data' => $finishedGoods->toArray()
                ]);

                // Return the updated data
                return [
                    'id' => $finishedGoods->id,
                    'product_id' => $finishedGoods->product_id,
                    'stok_awal' => $finishedGoods->stok_awal,
                    'stok_masuk' => $finishedGoods->stok_masuk,
                    'stok_keluar' => $finishedGoods->stok_keluar,
                    'defective' => $finishedGoods->defective,
                    'live_stock' => $finishedGoods->live_stock,
                    'updated_at' => $finishedGoods->updated_at->format('Y-m-d H:i:s')
                ];
            } catch (\Exception $e) {
                Log::error('Error resetting product stock', [
                    'product_id' => $productId,
                    'filter_month_year' => $filterMonthYear,
                    'error' => $e->getMessage(),
                    'trace' => $e->getTraceAsString()
                ]);
                throw $e;
            }
        });
    }

    /**
     * Get low stock finished goods
     *
     * @param int $threshold
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public function getLowStockFinishedGoods(int $threshold = 10)
    {
        try {
            return FinishedGoods::with('product')
                ->where('live_stock', '<=', $threshold)
                ->orderBy('live_stock', 'asc')
                ->get();
        } catch (\Exception $e) {
            Log::error('Failed to get low stock finished goods', [
                'threshold' => $threshold,
                'error' => $e->getMessage()
            ]);
            throw $e;
        }
    }

    /**
     * Verify stock consistency across all related tables
     *
     * @param int|null $productId
     * @return array
     */
    public function verifyStockConsistency($productId = null)
    {
        try {
            $inconsistencies = [];

            $query = FinishedGoods::with('product');
            if ($productId) {
                $query->where('product_id', $productId);
            }

            $finishedGoods = $query->get();

            foreach ($finishedGoods as $fg) {
                // Calculate expected values
                $expectedStokMasuk = CatatanProduksi::where('product_id', $fg->product_id)->sum('quantity');
                $expectedStokKeluar = $this->calculateStokKeluarFromSales($fg->product);
                $expectedLiveStock = max(0, $fg->stok_awal + $expectedStokMasuk - $expectedStokKeluar - $fg->defective);

                // Check for inconsistencies
                $inconsistency = [];
                if ($fg->stok_masuk != $expectedStokMasuk) {
                    $inconsistency['stok_masuk'] = [
                        'stored' => $fg->stok_masuk,
                        'expected' => $expectedStokMasuk,
                        'difference' => $fg->stok_masuk - $expectedStokMasuk
                    ];
                }

                if ($fg->stok_keluar != $expectedStokKeluar) {
                    $inconsistency['stok_keluar'] = [
                        'stored' => $fg->stok_keluar,
                        'expected' => $expectedStokKeluar,
                        'difference' => $fg->stok_keluar - $expectedStokKeluar
                    ];
                }

                if ($fg->live_stock != $expectedLiveStock) {
                    $inconsistency['live_stock'] = [
                        'stored' => $fg->live_stock,
                        'expected' => $expectedLiveStock,
                        'difference' => $fg->live_stock - $expectedLiveStock
                    ];
                }

                if (!empty($inconsistency)) {
                    $inconsistencies[] = [
                        'product_id' => $fg->product_id,
                        'product_name' => $fg->product->name_product,
                        'product_sku' => $fg->product->sku,
                        'inconsistencies' => $inconsistency
                    ];
                }
            }

            return [
                'total_checked' => $finishedGoods->count(),
                'inconsistencies_found' => count($inconsistencies),
                'details' => $inconsistencies
            ];
        } catch (\Exception $e) {
            Log::error('Failed to verify stock consistency', [
                'product_id' => $productId,
                'error' => $e->getMessage()
            ]);
            throw $e;
        }
    }

    /**
     * Bulk update all finished goods stock data with chunking
     * Updates stok_masuk, stok_keluar, and live_stock for all records
     *
     * @param int $chunkSize Size of each processing chunk
     * @param int $offset Starting offset for processing
     * @param int|null $totalRecords Total records to be processed
     * @return array
     */
    public function bulkUpdateAllStock($chunkSize = 50, $offset = 0, $totalRecords = null, $filterMonthYear = null)
    {
        try {
            // Get all products that need stock updates
            $query = Product::query();
            
            // Get total count if not provided (first call)
            if ($totalRecords === null) {
                $totalRecords = $query->count();
            }
            
            // No records to process
            if ($totalRecords == 0) {
                return [
                    'success' => true,
                    'message' => 'No products found to update',
                    'progress' => 100,
                    'completed' => true,
                    'total_records' => 0,
                    'processed_records' => 0,
                    'results' => []
                ];
            }
            
            // Get chunk of products to process
            $products = $query->skip($offset)->take($chunkSize)->get();
            $updateResults = [];
            $successCount = 0;
            $errorCount = 0;
            
            // Process each product in the chunk
            foreach ($products as $product) {
                DB::beginTransaction();
                try {
                    // Get or create finished goods record
                    $finishedGoods = FinishedGoods::firstOrNew(['product_id' => $product->id]);

                    // If it's a new record, set default values
                    if (!$finishedGoods->exists) {
                        $finishedGoods->stok_awal = 0;
                        $finishedGoods->defective = 0;
                        $finishedGoods->stok_sisa = 0;
                        $finishedGoods->stok_keluar = 0; // Ensure stok_keluar is set
                    }

                    // Update all stock values from related data
                    $this->updateStockFromRelatedData($finishedGoods);
                    
                    // Force recalculation of live stock
                    $finishedGoods->recalculateLiveStock();

                    // Save the record
                    $finishedGoods->save();
                    DB::commit();
                    
                    $updateResults[] = [
                        'status' => 'success',
                        'product_id' => $product->id,
                        'product_name' => $product->name_product,
                        'sku' => $product->sku,
                        'stok_awal' => $finishedGoods->stok_awal,
                        'stok_masuk' => $finishedGoods->stok_masuk,
                        'stok_keluar' => $finishedGoods->stok_keluar,
                        'defective' => $finishedGoods->defective,
                        'live_stock' => $finishedGoods->live_stock
                    ];
                    $successCount++;
                } catch (\Exception $e) {
                    DB::rollBack();
                    $updateResults[] = [
                        'status' => 'error',
                        'product_id' => $product->id,
                        'product_name' => $product->name_product,
                        'sku' => $product->sku,
                        'error' => $e->getMessage()
                    ];
                    $errorCount++;
                    
                    Log::error('Failed to bulk update individual finished goods', [
                        'product_id' => $product->id,
                        'error' => $e->getMessage()
                    ]);
                }
            }
            
            // Calculate progress
            $newOffset = $offset + $chunkSize;
            $isCompleted = $newOffset >= $totalRecords;
            $progress = min(100, round(($newOffset / $totalRecords) * 100));
            
            Log::info('Bulk update finished goods chunk processed', [
                'chunk_size' => $chunkSize,
                'offset' => $offset,
                'new_offset' => $newOffset,
                'total_records' => $totalRecords,
                'success_count' => $successCount,
                'error_count' => $errorCount,
                'progress' => $progress,
                'completed' => $isCompleted
            ]);

            return [
                'success' => true,
                'message' => "Updated {$successCount} records successfully" . ($errorCount > 0 ? " with {$errorCount} errors" : ""),
                'progress' => $progress,
                'completed' => $isCompleted,
                'total_records' => $totalRecords,
                'processed_records' => $newOffset,
                'next_offset' => $isCompleted ? null : $newOffset,
                'success_count' => $successCount,
                'error_count' => $errorCount,
                'results' => $updateResults
            ];
        } catch (\Exception $e) {
            Log::error('Failed to bulk update finished goods stock', [
                'offset' => $offset,
                'chunk_size' => $chunkSize,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            
            return [
                'success' => false,
                'message' => 'Error processing bulk update: ' . $e->getMessage(),
                'total_records' => $totalRecords,
                'processed_records' => $offset,
                'progress' => $offset > 0 ? round(($offset / $totalRecords) * 100) : 0,
                'completed' => false,
                'error' => $e->getMessage()
            ];
        }
    }

    /**
     * Calculate dynamic stok keluar from HistorySale data
     * This method now uses the unified calculateStokKeluar approach
     *
     * @param int $productId
     * @param string|null $filterMonthYear
     * @return int
     */
    private function calculateDynamicStokKeluar($productId, $filterMonthYear = null)
    {
        // Use the unified calculation method
        return $this->calculateStokKeluar($productId, $filterMonthYear);
    }
}
