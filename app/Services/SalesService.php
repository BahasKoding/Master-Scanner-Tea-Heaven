<?php

namespace App\Services;

use App\Models\HistorySale;

use App\Models\Product;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class SalesService
{
    protected $stockService;

    public function __construct(StockService $stockService)
    {
        $this->stockService = $stockService;
    }

    /**
     * Create new sales record with all related updates
     *
     * @param array $data
     * @return HistorySale
     */
    public function createSale(array $data)
    {
        return DB::transaction(function () use ($data) {
            try {
                // 1. Create history sale record
                $historySale = HistorySale::create($data);

                // 2. Update finished goods stock
                $this->stockService->updateStockFromSales($historySale);

                // 3. Log activity
                Log::info("Sales record created successfully", [
                    'sale_id' => $historySale->id,
                    'no_resi' => $historySale->no_resi,
                    'product_count' => is_array($historySale->no_sku) ? count($historySale->no_sku) : 0
                ]);

                return $historySale;
            } catch (\Exception $e) {
                Log::error("Failed to create sales record", [
                    'error' => $e->getMessage(),
                    'data' => $data
                ]);
                throw $e;
            }
        });
    }

    /**
     * Update sales record with all related updates
     *
     * @param HistorySale $historySale
     * @param array $data
     * @return HistorySale
     */
    public function updateSale(HistorySale $historySale, array $data)
    {
        return DB::transaction(function () use ($historySale, $data) {
            try {
                $oldQuantity = $historySale->qty;
                $oldSku = $historySale->no_sku;

                // 1. Update sales record
                $historySale->update($data);

                // 2. Update finished goods stock if qty or no_sku changed
                if ($oldQuantity !== $historySale->qty || $oldSku !== $historySale->no_sku) {
                    $this->stockService->updateStockFromSalesChange($historySale);
                }

                // 3. Log activity
                Log::info("Sales record updated successfully", [
                    'sale_id' => $historySale->id,
                    'no_resi' => $historySale->no_resi,
                    'quantity_changed' => $oldQuantity !== $historySale->qty,
                    'sku_changed' => $oldSku !== $historySale->no_sku
                ]);

                return $historySale;
            } catch (\Exception $e) {
                Log::error("Failed to update sales record", [
                    'error' => $e->getMessage(),
                    'sale_id' => $historySale->id,
                    'data' => $data
                ]);
                throw $e;
            }
        });
    }

    /**
     * Delete sales record with all related updates
     *
     * @param HistorySale $historySale
     * @return bool
     */
    public function deleteSale(HistorySale $historySale)
    {
        return DB::transaction(function () use ($historySale) {
            try {
                $saleId = $historySale->id;
                $noResi = $historySale->no_resi;

                // 1. Restore stock from sales
                $this->stockService->restoreStockFromSales($historySale);

                // 2. Delete sales record
                $result = $historySale->delete();

                // 3. Log activity
                Log::info("Sales record deleted successfully", [
                    'sale_id' => $saleId,
                    'no_resi' => $noResi
                ]);

                return $result;
            } catch (\Exception $e) {
                Log::error("Failed to delete sales record", [
                    'error' => $e->getMessage(),
                    'sale_id' => $historySale->id
                ]);
                throw $e;
            }
        });
    }



    /**
     * Sync sales data for consistency check
     * This method can be used for data migration or consistency verification
     *
     * @param int|null $saleId
     * @return array
     */
    public function syncSalesData($saleId = null)
    {
        return DB::transaction(function () use ($saleId) {
            $query = HistorySale::query();

            if ($saleId) {
                $query->where('id', $saleId);
            }

            $sales = $query->get();
            $syncResults = [];

            foreach ($sales as $sale) {
                try {
                    // Re-sync stock for this sale
                    $this->stockService->updateStockFromSales($sale);

                    $syncResults[] = [
                        'sale_id' => $sale->id,
                        'no_resi' => $sale->no_resi,
                        'status' => 'success'
                    ];
                } catch (\Exception $e) {
                    $syncResults[] = [
                        'sale_id' => $sale->id,
                        'no_resi' => $sale->no_resi,
                        'status' => 'error',
                        'message' => $e->getMessage()
                    ];
                }
            }

            return $syncResults;
        });
    }

    /**
     * Calculate total sales for a specific product
     *
     * @param int $productId
     * @param string|null $startDate
     * @param string|null $endDate
     * @return int
     */
    public function calculateProductSales($productId, $startDate = null, $endDate = null)
    {
        try {
            $product = Product::findOrFail($productId);
            $totalSales = 0;

            $query = HistorySale::query();

            if ($startDate && $endDate) {
                $query->whereBetween('created_at', [$startDate, $endDate]);
            }

            $sales = $query->whereNotNull('no_sku')->get();

            foreach ($sales as $sale) {
                $skuArray = is_string($sale->no_sku) ? json_decode($sale->no_sku, true) : $sale->no_sku;
                $qtyArray = is_string($sale->qty) ? json_decode($sale->qty, true) : $sale->qty;

                if (is_array($skuArray) && is_array($qtyArray)) {
                    foreach ($skuArray as $index => $sku) {
                        if ($sku === $product->sku) {
                            $totalSales += $qtyArray[$index] ?? 0;
                        }
                    }
                }
            }

            return $totalSales;
        } catch (\Exception $e) {
            Log::error("Failed to calculate product sales", [
                'error' => $e->getMessage(),
                'product_id' => $productId
            ]);
            return 0;
        }
    }
}
