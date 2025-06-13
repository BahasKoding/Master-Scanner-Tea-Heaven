<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Log;

class FinishedGoods extends Model
{
    use HasFactory;
    protected $table = 'finished_goods';
    protected $fillable = [
        'product_id',
        'stok_awal',
        'stok_masuk',
        'stok_keluar',
        'defective',
        'live_stock'
    ];

    /**
     * Get the product that owns the finished goods
     */
    public function product()
    {
        return $this->belongsTo(Product::class, 'product_id');
    }

    /**
     * Auto-update stok_masuk from CatatanProduksi
     * This method calculates total quantity from all production records for this product
     */
    public function updateStokMasukFromCatatanProduksi()
    {
        try {
            $totalProduction = CatatanProduksi::where('product_id', $this->product_id)
                ->sum('quantity');

            $this->stok_masuk = $totalProduction;
            $this->recalculateLiveStock();

            Log::info("Auto-updated stok_masuk for product_id {$this->product_id}: {$totalProduction}");

            return $this;
        } catch (\Exception $e) {
            Log::error("Failed to update stok_masuk from catatan produksi for product_id {$this->product_id}: " . $e->getMessage());
            return $this;
        }
    }

    /**
     * Auto-update stok_keluar from HistorySale
     * This method calculates total quantity sold from all sales records for this product
     */
    public function updateStokKeluarFromHistorySales()
    {
        try {
            $product = $this->product;
            if (!$product) {
                Log::warning("Product not found for finished_goods id {$this->id}");
                return $this;
            }

            $totalSales = 0;

            // Get all history sales that contain this product's SKU
            $historySales = HistorySale::whereNotNull('no_sku')->get();

            foreach ($historySales as $sale) {
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

            $this->stok_keluar = $totalSales;
            $this->recalculateLiveStock();

            Log::info("Auto-updated stok_keluar for product_id {$this->product_id} (SKU: {$product->sku}): {$totalSales}");

            return $this;
        } catch (\Exception $e) {
            Log::error("Failed to update stok_keluar from history sales for product_id {$this->product_id}: " . $e->getMessage());
            return $this;
        }
    }

    /**
     * Recalculate live_stock based on the formula:
     * live_stock = stok_awal + stok_masuk - stok_keluar - defective
     */
    public function recalculateLiveStock()
    {
        $this->live_stock = $this->stok_awal + $this->stok_masuk - $this->stok_keluar - $this->defective;

        // Ensure live_stock is not negative
        if ($this->live_stock < 0) {
            Log::warning("Live stock calculation resulted in negative value for product_id {$this->product_id}. Setting to 0.");
            $this->live_stock = 0;
        }

        return $this;
    }

    /**
     * Get dynamic stok_masuk (calculated from CatatanProduksi)
     */
    public function getStokMasukDynamicAttribute()
    {
        try {
            return CatatanProduksi::where('product_id', $this->product_id)->sum('quantity');
        } catch (\Exception $e) {
            Log::error("Error calculating dynamic stok_masuk for product_id {$this->product_id}: " . $e->getMessage());
            return $this->stok_masuk ?? 0;
        }
    }

    /**
     * Get dynamic stok_keluar (calculated from HistorySale)
     */
    public function getStokKeluarDynamicAttribute()
    {
        try {
            $product = $this->product;
            if (!$product) {
                return $this->stok_keluar ?? 0;
            }

            $totalSales = 0;
            $historySales = HistorySale::whereNotNull('no_sku')->get();

            foreach ($historySales as $sale) {
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
            Log::error("Error calculating dynamic stok_keluar for product_id {$this->product_id}: " . $e->getMessage());
            return $this->stok_keluar ?? 0;
        }
    }

    /**
     * Get dynamic live_stock (calculated using dynamic values)
     */
    public function getLiveStockDynamicAttribute()
    {
        $liveStock = $this->stok_awal + $this->stok_masuk_dynamic - $this->stok_keluar_dynamic - $this->defective;
        return max(0, $liveStock); // Ensure not negative
    }

    /**
     * Sync all stock values with their dynamic counterparts
     */
    public function syncStockValues()
    {
        $this->updateStokMasukFromCatatanProduksi();
        $this->updateStokKeluarFromHistorySales();
        $this->save();

        return $this;
    }

    /**
     * Static method to sync stock for a specific product
     */
    public static function syncStockForProduct($productId)
    {
        try {
            $finishedGoods = static::firstOrNew(['product_id' => $productId]);

            if (!$finishedGoods->exists) {
                $finishedGoods->stok_awal = 0;
                $finishedGoods->defective = 0;
            }

            $finishedGoods->syncStockValues();

            return $finishedGoods;
        } catch (\Exception $e) {
            Log::error("Failed to sync stock for product_id {$productId}: " . $e->getMessage());
            throw $e;
        }
    }

    /**
     * Check if this product has sticker integration
     * Products with sticker should reduce finished goods when sticker is used
     */
    public function hasStickerIntegration()
    {
        $product = $this->product;
        if (!$product) {
            return false;
        }

        // Check if product has sticker (labels 1, 2, 5 according to StickerController)
        return in_array($product->label, [1, 2, 5]);
    }

    /**
     * Update stock when sticker is used (for products with sticker integration)
     */
    public function updateStockFromStickerUsage($stickerQuantity)
    {
        if (!$this->hasStickerIntegration()) {
            return $this;
        }

        try {
            // For sticker products, when sticker is used, it means finished goods are consumed
            // This should be reflected in stok_keluar or handled separately
            // For now, we'll log this for tracking
            Log::info("Sticker usage detected for product_id {$this->product_id}: {$stickerQuantity} stickers used");

            // You can implement additional logic here if needed
            // For example, if 1 sticker = 1 finished product, then:
            // $this->stok_keluar += $stickerQuantity;
            // $this->recalculateLiveStock();

            return $this;
        } catch (\Exception $e) {
            Log::error("Failed to update stock from sticker usage for product_id {$this->product_id}: " . $e->getMessage());
            return $this;
        }
    }
}
