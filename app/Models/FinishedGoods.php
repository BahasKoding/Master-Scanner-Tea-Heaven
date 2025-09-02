<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Log;
use App\Models\Purchase;

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
        'stok_sisa',
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
     * Auto-update stok_sisa from stock opname data
     * This method calculates stok_sisa from stock opname records
     */
    public function updateStokSisaFromOpname()
    {
        try {
            // TODO: Replace with actual stock opname table/model when available
            // For now, we'll set a default value or calculate from existing data
            
            // Example calculation - you can modify this based on your opname data structure
            // $opnameTotal = StockOpname::where('product_id', $this->product_id)
            //     ->sum('sisa_stock'); // or whatever field represents remaining stock

            // Temporary: Set to 0 until opname system is implemented
            $this->stok_sisa = 0;
            
            Log::info('Updated stok_sisa from opname data', [
                'product_id' => $this->product_id,
                'stok_sisa' => $this->stok_sisa
            ]);
            
            return $this;
        } catch (\Exception $e) {
            Log::error('Error updating stok_sisa from opname', [
                'product_id' => $this->product_id,
                'error' => $e->getMessage()
            ]);
            
            // Set default value on error
            $this->stok_sisa = 0;
            return $this;
        }
    }

    /**
     * Auto-update stok_masuk from both CatatanProduksi and Purchase (finished_goods)
     * This method calculates total quantity from production records AND purchases
     */
    public function updateStokMasukFromAllSources()
    {
        try {
            // Get total from production records
            $totalProduction = CatatanProduksi::where('product_id', $this->product_id)
                ->sum('quantity');

            // Get total from finished_goods purchases
            $totalPurchases = Purchase::where('bahan_baku_id', $this->product_id)
                ->where('kategori', 'finished_goods')
                ->sum('total_stok_masuk');

            // Combine both sources
            $this->stok_masuk = $totalProduction + $totalPurchases;
            $this->recalculateLiveStock();

            Log::info("Auto-updated stok_masuk for product_id {$this->product_id}: Production={$totalProduction}, Purchases={$totalPurchases}, Total={$this->stok_masuk}");

            return $this;
        } catch (\Exception $e) {
            Log::error("Failed to update stok_masuk from all sources for product_id {$this->product_id}: " . $e->getMessage());
            return $this;
        }
    }

    /**
     * DEPRECATED: Use updateStokMasukFromAllSources() instead
     * Auto-update stok_masuk from CatatanProduksi only
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
     * FIXED: Only update if database value is 0 or inconsistent
     * This preserves values updated by StockService
     */
    public function updateStokKeluarFromHistorySales()
    {
        try {
            $product = $this->product;
            if (!$product) {
                Log::warning("Product not found for finished_goods id {$this->id}");
                return $this;
            }

            $currentStokKeluar = $this->stok_keluar;
            $dynamicStokKeluar = $this->calculateDynamicStokKeluar();

            // FIXED: Only override if database value is 0 but we have sales data
            // This prevents overriding values that were updated by StockService
            if ($currentStokKeluar == 0 && $dynamicStokKeluar > 0) {
                $this->stok_keluar = $dynamicStokKeluar;
                $this->recalculateLiveStock();
                
                Log::info("Updated stok_keluar from dynamic calculation for product_id {$this->product_id} (SKU: {$product->sku}): {$currentStokKeluar} -> {$dynamicStokKeluar}");
            } else {
                Log::info("Keeping database stok_keluar for product_id {$this->product_id} (SKU: {$product->sku}): database={$currentStokKeluar}, dynamic={$dynamicStokKeluar}");
            }

            return $this;
        } catch (\Exception $e) {
            Log::error("Failed to update stok_keluar from history sales for product_id {$this->product_id}: " . $e->getMessage());
            return $this;
        }
    }

    /**
     * Calculate dynamic stok keluar without updating the model
     * Helper method for comparison purposes
     */
    private function calculateDynamicStokKeluar()
    {
        try {
            $product = $this->product;
            if (!$product) {
                return 0;
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
            Log::error("Failed to calculate dynamic stok_keluar for product_id {$this->product_id}: " . $e->getMessage());
            return 0;
        }
    }

    /**
     * Recalculate live_stock based on the formula:
     * live_stock = stok_awal + stok_masuk - stok_keluar - defective + stok_sisa
     * FIXED: Allow negative live_stock values for accurate representation
     */
    public function recalculateLiveStock()
    {
        $this->live_stock = $this->stok_awal + $this->stok_masuk - $this->stok_keluar - $this->defective + ($this->stok_sisa ?? 0);
        
        // Log if live stock is negative for monitoring purposes
        if ($this->live_stock < 0) {
            Log::info("Live stock calculation resulted in negative value for product_id {$this->product_id}: {$this->live_stock}");
        }

        return $this;
    }

    /**
     * Get dynamic stok_masuk (calculated from CatatanProduksi + Purchase finished_goods)
     * FIXED: Now combines both production and purchase sources
     */
    public function getStokMasukDynamicAttribute()
    {
        try {
            // Get total from production records
            $totalProduction = CatatanProduksi::where('product_id', $this->product_id)->sum('quantity');
            
            // Get total from finished_goods purchases
            $totalPurchases = Purchase::where('bahan_baku_id', $this->product_id)
                ->where('kategori', 'finished_goods')
                ->sum('total_stok_masuk');
            
            return $totalProduction + $totalPurchases;
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
     * FIXED: Allow negative values for accurate representation
     */
    public function getLiveStockDynamicAttribute()
    {
        $liveStock = $this->stok_awal + $this->stok_masuk_dynamic - $this->stok_keluar_dynamic - $this->defective + ($this->stok_sisa ?? 0);
        return $liveStock; // Allow negative values
    }

    /**
     * Sync all stock values with their dynamic counterparts
     * FIXED: Now uses combined sources for stok_masuk
     */
    public function syncStockValues()
    {
        $this->updateStokMasukFromAllSources();
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

    /**
     * Get dynamic stok_masuk for a specific month.
     * @param \Carbon\Carbon|null $date
     * @return int
     */
    public function getStokMasukForMonth($date = null)
    {
        try {
            $date = $date ?? now();
            $startDate = $date->startOfMonth()->toDateString();
            $endDate = $date->endOfMonth()->toDateString();

            // Get total from production records within the month
            $totalProduction = CatatanProduksi::where('product_id', $this->product_id)
                ->whereBetween('created_at', [$startDate, $endDate])
                ->sum('quantity');
            
            // Get total from finished_goods purchases within the month
            $totalPurchases = Purchase::where('bahan_baku_id', $this->product_id)
                ->where('kategori', 'finished_goods')
                ->whereBetween('created_at', [$startDate, $endDate])
                ->sum('total_stok_masuk');
            
            return $totalProduction + $totalPurchases;

        } catch (\Exception $e) {
            Log::error("Error calculating monthly stok_masuk for product_id {$this->product_id}: " . $e->getMessage());
            return 0;
        }
    }

    /**
     * Get dynamic stok_keluar for a specific month.
     * @param \Carbon\Carbon|null $date
     * @return int
     */
    public function getStokKeluarForMonth($date = null)
    {
        try {
            $date = $date ?? now();
            $startDate = $date->startOfMonth()->toDateString();
            $endDate = $date->endOfMonth()->toDateString();

            $product = $this->product;
            if (!$product) {
                return 0;
            }

            $totalSales = 0;
            $historySales = HistorySale::whereNotNull('no_sku')
                ->whereBetween('created_at', [$startDate, $endDate])
                ->get();

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
            Log::error("Error calculating monthly stok_keluar for product_id {$this->product_id}: " . $e->getMessage());
            return 0;
        }
    }
}
