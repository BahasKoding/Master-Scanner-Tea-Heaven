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
     * Get the details for this history sale.
     */
    public function details()
    {
        return $this->hasMany(HistorySaleDetail::class, 'history_sale_id');
    }

    /**
     * Get all products associated with this history sale through details.
     */
    public function products()
    {
        return $this->belongsToMany(Product::class, 'history_sale_details', 'history_sale_id', 'product_id')
            ->withPivot('quantity')
            ->withTimestamps();
    }

    /**
     * Helper method to get the products and quantities in a format compatible with the old system
     */
    public function getProductDetailsArrayAttribute()
    {
        $details = $this->details()->with('product')->get();

        $skus = [];
        $quantities = [];

        foreach ($details as $detail) {
            if ($detail->product) {
                $skus[] = $detail->product->sku;
                $quantities[] = $detail->quantity;
            }
        }

        return [
            'no_sku' => $skus,
            'qty' => $quantities
        ];
    }

    /**
     * Boot method to register model events
     */
    protected static function boot()
    {
        parent::boot();

        // Auto-update FinishedGoods when HistorySale is created
        static::created(function ($historySale) {
            try {
                static::syncFinishedGoodsFromHistorySale($historySale);
                \Illuminate\Support\Facades\Log::info("Auto-synced FinishedGoods after HistorySale created for resi: {$historySale->no_resi}");
            } catch (\Exception $e) {
                \Illuminate\Support\Facades\Log::error("Failed to sync FinishedGoods after HistorySale created: " . $e->getMessage());
            }
        });

        // Auto-update FinishedGoods when HistorySale is updated
        static::updated(function ($historySale) {
            try {
                static::syncFinishedGoodsFromHistorySale($historySale);
                \Illuminate\Support\Facades\Log::info("Auto-synced FinishedGoods after HistorySale updated for resi: {$historySale->no_resi}");
            } catch (\Exception $e) {
                \Illuminate\Support\Facades\Log::error("Failed to sync FinishedGoods after HistorySale updated: " . $e->getMessage());
            }
        });

        // Auto-update FinishedGoods when HistorySale is deleted
        static::deleted(function ($historySale) {
            try {
                static::syncFinishedGoodsFromHistorySale($historySale);
                \Illuminate\Support\Facades\Log::info("Auto-synced FinishedGoods after HistorySale deleted for resi: {$historySale->no_resi}");
            } catch (\Exception $e) {
                \Illuminate\Support\Facades\Log::error("Failed to sync FinishedGoods after HistorySale deleted: " . $e->getMessage());
            }
        });
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
