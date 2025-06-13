<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class HistorySale extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'no_resi',
        'no_sku',
        'qty',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array
     */
    protected $casts = [
        'no_sku' => 'array',
        'qty' => 'array',
        'deleted_at' => 'datetime',
    ];

    /**
     * Get all products associated with this history sale based on JSON SKU data
     * This replaces the old details() relationship since we store SKUs in JSON
     */
    public function getAssociatedProducts()
    {
        $skuArray = is_string($this->no_sku) ? json_decode($this->no_sku, true) : $this->no_sku;

        if (!is_array($skuArray) || empty($skuArray)) {
            return collect();
        }

        return Product::whereIn('sku', $skuArray)->get();
    }

    /**
     * Get product details in array format (backward compatibility)
     */
    public function getProductDetailsArrayAttribute()
    {
        $skuArray = is_string($this->no_sku) ? json_decode($this->no_sku, true) : $this->no_sku;
        $qtyArray = is_string($this->qty) ? json_decode($this->qty, true) : $this->qty;

        if (!is_array($skuArray) || !is_array($qtyArray)) {
            return [
                'no_sku' => [],
                'qty' => []
            ];
        }

        return [
            'no_sku' => $skuArray,
            'qty' => $qtyArray
        ];
    }

    /**
     * Get products with their quantities for this sale
     */
    public function getProductsWithQuantities()
    {
        $skuArray = is_string($this->no_sku) ? json_decode($this->no_sku, true) : $this->no_sku;
        $qtyArray = is_string($this->qty) ? json_decode($this->qty, true) : $this->qty;

        if (!is_array($skuArray) || !is_array($qtyArray)) {
            return collect();
        }

        $products = Product::whereIn('sku', $skuArray)->get()->keyBy('sku');
        $result = collect();

        foreach ($skuArray as $index => $sku) {
            if (isset($products[$sku])) {
                $result->push([
                    'product' => $products[$sku],
                    'quantity' => $qtyArray[$index] ?? 1,
                    'sku' => $sku
                ]);
            }
        }

        return $result;
    }

    /**
     * Boot method to register model events
     * 
     * Note: Stock updates are now handled by HistorySaleObserver
     * This method is kept for compatibility with existing code
     */
    protected static function boot()
    {
        parent::boot();

        // Event handlers moved to HistorySaleObserver
        // This prevents duplicate stock updates
    }

    /**
     * Sync FinishedGoods for all products affected by this HistorySale
     */
    protected static function syncFinishedGoodsFromHistorySale($historySale)
    {
        try {
            $skuArray = is_string($historySale->no_sku) ? json_decode($historySale->no_sku, true) : $historySale->no_sku;

            if (is_array($skuArray)) {
                $products = Product::whereIn('sku', $skuArray)->get();

                foreach ($products as $product) {
                    FinishedGoods::syncStockForProduct($product->id);
                }
            }
        } catch (\Exception $e) {
            \Illuminate\Support\Facades\Log::error("Error in syncFinishedGoodsFromHistorySale: " . $e->getMessage());
            throw $e;
        }
    }

    /**
     * Get top selling SKUs for a given date range
     * 
     * @param \Carbon\Carbon $startDate
     * @param \Carbon\Carbon $endDate
     * @param int $limit
     * @return array
     */
    public static function getTopSellingSKUs($startDate, $endDate, $limit = 5)
    {
        try {
            $skuSales = [];
            $sales = self::whereBetween('created_at', [$startDate, $endDate])->get();

            foreach ($sales as $sale) {
                try {
                    $skuArray = is_string($sale->no_sku) ? json_decode($sale->no_sku, true) : $sale->no_sku;
                    $qtyArray = is_string($sale->qty) ? json_decode($sale->qty, true) : $sale->qty;

                    if (is_array($skuArray) && is_array($qtyArray)) {
                        foreach ($skuArray as $index => $sku) {
                            if (!empty($sku)) {
                                $qty = isset($qtyArray[$index]) ? (int)$qtyArray[$index] : 1;

                                if (!isset($skuSales[$sku])) {
                                    $skuSales[$sku] = [
                                        'sku' => $sku,
                                        'total_sold' => 0,
                                        'transactions' => 0,
                                        'name' => ''
                                    ];
                                }

                                $skuSales[$sku]['total_sold'] += $qty;
                                $skuSales[$sku]['transactions']++;
                            }
                        }
                    }
                } catch (\Exception $e) {
                    // Skip this sale if there's an error
                    continue;
                }
            }

            // Get product names for SKUs
            if (!empty($skuSales)) {
                try {
                    $products = Product::whereIn('sku', array_keys($skuSales))->get()->keyBy('sku');
                    foreach ($skuSales as $sku => &$data) {
                        if (isset($products[$sku])) {
                            $data['name'] = $products[$sku]->name_product;
                        } else {
                            $data['name'] = 'Produk Tidak Dikenal';
                        }
                    }
                } catch (\Exception $e) {
                    \Illuminate\Support\Facades\Log::error('Error getting product names: ' . $e->getMessage());
                    // Keep SKUs without names
                }
            }

            // Sort by total sold and take top results
            return collect($skuSales)
                ->sortByDesc('total_sold')
                ->take($limit)
                ->values()
                ->toArray();
        } catch (\Exception $e) {
            \Illuminate\Support\Facades\Log::error('Error in getTopSellingSKUs: ' . $e->getMessage());
            return [];
        }
    }

    /**
     * Calculate total quantity sold for a date range
     * 
     * @param \Carbon\Carbon $startDate
     * @param \Carbon\Carbon $endDate
     * @return int
     */
    public static function getTotalQuantitySold($startDate, $endDate)
    {
        try {
            $totalQty = 0;
            $sales = self::whereBetween('created_at', [$startDate, $endDate])->get();

            foreach ($sales as $sale) {
                try {
                    $qtyArray = is_string($sale->qty) ? json_decode($sale->qty, true) : $sale->qty;
                    if (is_array($qtyArray)) {
                        $totalQty += array_sum($qtyArray);
                    } elseif (is_numeric($qtyArray)) {
                        $totalQty += $qtyArray;
                    }
                } catch (\Exception $e) {
                    // Skip this sale if there's an error parsing qty
                    continue;
                }
            }

            return $totalQty;
        } catch (\Exception $e) {
            \Illuminate\Support\Facades\Log::error('Error in getTotalQuantitySold: ' . $e->getMessage());
            return 0;
        }
    }
}
