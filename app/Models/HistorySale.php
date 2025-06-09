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

        return \App\Models\Product::whereIn('sku', $skuArray)->get();
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

        $products = \App\Models\Product::whereIn('sku', $skuArray)->get()->keyBy('sku');
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
                $products = \App\Models\Product::whereIn('sku', $skuArray)->get();

                foreach ($products as $product) {
                    \App\Models\FinishedGoods::syncStockForProduct($product->id);
                }
            }
        } catch (\Exception $e) {
            \Illuminate\Support\Facades\Log::error("Error in syncFinishedGoodsFromHistorySale: " . $e->getMessage());
            throw $e;
        }
    }
}
