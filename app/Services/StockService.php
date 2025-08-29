<?php

namespace App\Services;

use App\Models\CatatanProduksi;
use App\Models\FinishedGoods;
use App\Models\HistorySale;
use App\Models\Product;
use App\Models\Purchase;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class StockService
{
    /**
     * Update stok masuk pada Finished Goods dari data produksi baru
     *
     * @param CatatanProduksi $catatanProduksi
     * @return void
     */
    public function updateStockFromProduction(CatatanProduksi $catatanProduksi)
    {
        $productId = $catatanProduksi->product_id;
        $quantity = $catatanProduksi->quantity;

        try {
            DB::transaction(function () use ($productId, $quantity) {
                $finishedGood = FinishedGoods::firstOrNew(['product_id' => $productId]);

                if (!$finishedGood->exists) {
                    $finishedGood->stok_awal = 0;
                    $finishedGood->stok_masuk = 0;
                    $finishedGood->stok_keluar = 0;
                    $finishedGood->defective = 0;
                    $finishedGood->live_stock = 0;
                }

                $finishedGood->stok_masuk += $quantity;
                $this->recalculateLiveStock($finishedGood);
                $finishedGood->save();

                Log::info("Stok masuk ditambahkan: Product ID {$productId}, Quantity {$quantity}");
            });
        } catch (\Exception $e) {
            Log::error("Gagal menambahkan stok masuk: " . $e->getMessage());
            throw $e;
        }
    }

    /**
     * Update stok masuk pada Finished Goods saat data produksi diubah
     *
     * @param CatatanProduksi $catatanProduksi
     * @return void
     */
    public function updateStockFromProductionChange(CatatanProduksi $catatanProduksi)
    {
        $productId = $catatanProduksi->product_id;
        $oldQuantity = $catatanProduksi->getOriginal('quantity');
        $newQuantity = $catatanProduksi->quantity;
        $difference = $newQuantity - $oldQuantity;

        try {
            DB::transaction(function () use ($productId, $difference) {
                $finishedGood = FinishedGoods::where('product_id', $productId)->firstOrFail();
                $finishedGood->stok_masuk += $difference;
                $this->recalculateLiveStock($finishedGood);
                $finishedGood->save();

                Log::info("Stok masuk diperbarui: Product ID {$productId}, Perubahan {$difference}");
            });
        } catch (\Exception $e) {
            Log::error("Gagal memperbarui stok masuk: " . $e->getMessage());
            throw $e;
        }
    }

    /**
     * Mengurangi stok masuk pada Finished Goods saat catatan produksi dihapus
     *
     * @param CatatanProduksi $catatanProduksi
     * @return void
     */
    public function removeStockFromProduction(CatatanProduksi $catatanProduksi)
    {
        $productId = $catatanProduksi->product_id;
        $quantity = $catatanProduksi->quantity;

        try {
            DB::transaction(function () use ($productId, $quantity) {
                $finishedGood = FinishedGoods::where('product_id', $productId)->firstOrFail();
                $finishedGood->stok_masuk -= $quantity;
                $this->recalculateLiveStock($finishedGood);
                $finishedGood->save();

                Log::info("Stok masuk dikurangi: Product ID {$productId}, Quantity {$quantity}");
            });
        } catch (\Exception $e) {
            Log::error("Gagal mengurangi stok masuk: " . $e->getMessage());
            throw $e;
        }
    }

    /**
     * Menambah stok masuk pada Finished Goods dari data pembelian baru.
     * Hanya berlaku untuk pembelian dengan kategori 'finished_goods'.
     *
     * @param Purchase $purchase
     * @return void
     */
    public function addStockFromPurchase(Purchase $purchase)
    {
        if ($purchase->kategori !== 'finished_goods') {
            return;
        }

        $productId = $purchase->bahan_baku_id;
        $quantity = $purchase->total_stok_masuk;

        if ($quantity <= 0) {
            return; // Jangan proses jika tidak ada stok yang masuk
        }

        try {
            DB::transaction(function () use ($productId, $quantity) {
                $finishedGood = FinishedGoods::firstOrNew(['product_id' => $productId]);

                if (!$finishedGood->exists) {
                    $finishedGood->stok_awal = 0;
                    $finishedGood->stok_masuk = 0;
                    $finishedGood->stok_keluar = 0;
                    $finishedGood->defective = 0;
                    $finishedGood->live_stock = 0;
                }

                $finishedGood->stok_masuk += $quantity;
                $this->recalculateLiveStock($finishedGood);
                $finishedGood->save();

                Log::info("Stok masuk dari pembelian ditambahkan: Product ID {$productId}, Quantity {$quantity}");
            });
        } catch (\Exception $e) {
            Log::error("Gagal menambahkan stok dari pembelian: " . $e->getMessage());
            throw $e;
        }
    }

    /**
     * Memperbarui stok masuk pada Finished Goods saat data pembelian diubah.
     *
     * @param Purchase $purchase
     * @return void
     */
    public function updateStockFromPurchaseChange(Purchase $purchase)
    {
        if ($purchase->kategori !== 'finished_goods' || !$purchase->wasChanged('total_stok_masuk')) {
            return;
        }

        $productId = $purchase->bahan_baku_id;
        $oldQuantity = $purchase->getOriginal('total_stok_masuk');
        $newQuantity = $purchase->total_stok_masuk;
        $difference = $newQuantity - $oldQuantity;

        if ($difference == 0) {
            return;
        }

        try {
            DB::transaction(function () use ($productId, $difference) {
                $finishedGood = FinishedGoods::where('product_id', $productId)->firstOrFail();
                $finishedGood->stok_masuk += $difference;
                $this->recalculateLiveStock($finishedGood);
                $finishedGood->save();

                Log::info("Stok masuk dari pembelian diperbarui: Product ID {$productId}, Perubahan {$difference}");
            });
        } catch (\Exception $e) {
            Log::error("Gagal memperbarui stok dari pembelian: " . $e->getMessage());
            throw $e;
        }
    }

    /**
     * Mengurangi stok masuk pada Finished Goods saat data pembelian dihapus.
     *
     * @param Purchase $purchase
     * @return void
     */
    public function removeStockFromPurchase(Purchase $purchase)
    {
        if ($purchase->kategori !== 'finished_goods') {
            return;
        }

        $productId = $purchase->bahan_baku_id;
        $quantity = $purchase->total_stok_masuk;

        if ($quantity <= 0) {
            return;
        }

        try {
            DB::transaction(function () use ($productId, $quantity) {
                $finishedGood = FinishedGoods::where('product_id', $productId)->first();

                if ($finishedGood) {
                    $finishedGood->stok_masuk -= $quantity;
                    $this->recalculateLiveStock($finishedGood);
                    $finishedGood->save();

                    Log::info("Stok masuk dari pembelian dikurangi: Product ID {$productId}, Quantity {$quantity}");
                }
            });
        } catch (\Exception $e) {
            Log::error("Gagal mengurangi stok dari pembelian: " . $e->getMessage());
            throw $e;
        }
    }

    /**
     * Update stok keluar pada Finished Goods dari data penjualan baru
     *
     * @param HistorySale $historySale
     * @return void
     */
    public function updateStockFromSales(HistorySale $historySale)
    {
        // Sistem menggunakan format JSON array untuk no_sku dan qty
        $skuArray = is_string($historySale->no_sku) ? json_decode($historySale->no_sku, true) : $historySale->no_sku;
        $qtyArray = is_string($historySale->qty) ? json_decode($historySale->qty, true) : $historySale->qty;

        if (!is_array($skuArray) || !is_array($qtyArray)) {
            Log::warning("Invalid SKU or QTY data in HistorySale ID: {$historySale->id}");
            return;
        }

        if (count($skuArray) !== count($qtyArray)) {
            Log::warning("SKU and QTY array length mismatch in HistorySale ID: {$historySale->id}");
            return;
        }

        try {
            DB::transaction(function () use ($historySale, $skuArray, $qtyArray) {
                foreach ($skuArray as $index => $sku) {
                    $product = Product::where('sku', trim($sku))->first();
                    if (!$product) {
                        Log::warning("SKU tidak ditemukan: {$sku} (HistorySale ID: {$historySale->id})");
                        continue; // Skip invalid SKU instead of throwing exception
                    }

                    $quantity = $qtyArray[$index] ?? 1;
                    if (!is_numeric($quantity) || $quantity <= 0) {
                        Log::warning("Invalid quantity for SKU {$sku}: {$quantity} (HistorySale ID: {$historySale->id})");
                        continue;
                    }

                    // Perbarui stok
                    $this->updateStockForProduct($product->id, (int)$quantity);
                }
            });
        } catch (\Exception $e) {
            Log::error("Gagal menambahkan stok keluar: " . $e->getMessage(), [
                'history_sale_id' => $historySale->id,
                'error' => $e->getMessage()
            ]);
            throw $e;
        }
    }

    /**
     * Helper method untuk memperbarui stok sebuah produk
     * 
     * @param int $productId
     * @param int $quantity
     * @return void
     */
    private function updateStockForProduct($productId, $quantity)
    {
        $finishedGood = FinishedGoods::where('product_id', $productId)->first();
        if (!$finishedGood) {
            // Create new FinishedGoods record if not exists
            $finishedGood = FinishedGoods::create([
                'product_id' => $productId,
                'stok_awal' => 0,
                'stok_masuk' => 0,
                'stok_keluar' => 0,
                'defective' => 0,
                'live_stock' => 0
            ]);
            Log::info("Created new FinishedGoods record for product ID: {$productId}");
        }

        // Allow negative stock for scanner input - log warning instead of throwing exception
        if ($finishedGood->live_stock < $quantity) {
            $product = Product::find($productId);
            $sku = $product ? $product->sku : 'Unknown';
            Log::warning("Stock insufficient for {$sku}. Available: {$finishedGood->live_stock}, Requested: {$quantity}. Allowing negative stock.");
        }

        $finishedGood->stok_keluar += $quantity;
        $this->recalculateLiveStock($finishedGood);
        $finishedGood->save();

        Log::info("Stok keluar ditambahkan: Product ID {$productId}, Quantity {$quantity}");
    }

    /**
     * Update stok keluar pada Finished Goods saat data penjualan diubah
     *
     * @param HistorySale $historySale
     * @return void
     */
    public function updateStockFromSalesChange(HistorySale $historySale)
    {
        // Kembalikan stok dari data lama
        $this->restoreStockFromSales($historySale, true);

        // Tambahkan stok dari data baru
        $this->updateStockFromSales($historySale);
    }

    /**
     * Mengembalikan stok keluar pada Finished Goods saat penjualan dihapus/diubah
     *
     * @param HistorySale $historySale
     * @param bool $isUpdate Apakah ini untuk update atau delete
     * @return void
     */
    public function restoreStockFromSales(HistorySale $historySale, $isUpdate = false)
    {
        try {
            DB::transaction(function () use ($historySale, $isUpdate) {
                // Jika update, gunakan data original, jika delete gunakan data current
                $skuArray = $isUpdate ? $historySale->getOriginal('no_sku') : $historySale->no_sku;
                $qtyArray = $isUpdate ? $historySale->getOriginal('qty') : $historySale->qty;

                // Jika adalah array JSON yang disimpan, decode terlebih dahulu
                if (is_string($skuArray)) {
                    $skuArray = json_decode($skuArray, true);
                }

                if (is_string($qtyArray)) {
                    $qtyArray = json_decode($qtyArray, true);
                }

                if (!is_array($skuArray) || !is_array($qtyArray)) {
                    Log::warning("Invalid SKU or QTY data when restoring stock for HistorySale ID: {$historySale->id}");
                    return;
                }

                foreach ($skuArray as $index => $sku) {
                    $product = Product::where('sku', trim($sku))->first();
                    if (!$product) {
                        Log::warning("SKU tidak ditemukan saat restore: {$sku} (HistorySale ID: {$historySale->id})");
                        continue; // Skip jika produk tidak ditemukan
                    }

                    $quantity = $qtyArray[$index] ?? 1;
                    if (!is_numeric($quantity) || $quantity <= 0) {
                        continue;
                    }

                    $this->decreaseStockForProduct($product->id, (int)$quantity);
                }
            });
        } catch (\Exception $e) {
            Log::error("Gagal mengembalikan stok keluar: " . $e->getMessage(), [
                'history_sale_id' => $historySale->id,
                'error' => $e->getMessage()
            ]);
            throw $e;
        }
    }

    /**
     * Helper method untuk mengurangi stok keluar (mengembalikan stok)
     * 
     * @param int $productId
     * @param int $quantity
     * @return void
     */
    private function decreaseStockForProduct($productId, $quantity)
    {
        $finishedGood = FinishedGoods::where('product_id', $productId)->first();
        if (!$finishedGood) {
            return; // Skip jika finished good tidak ditemukan
        }

        $finishedGood->stok_keluar -= $quantity;
        if ($finishedGood->stok_keluar < 0) {
            $finishedGood->stok_keluar = 0; // Pastikan tidak negatif
        }

        $this->recalculateLiveStock($finishedGood);
        $finishedGood->save();

        Log::info("Stok keluar dikembalikan: Product ID {$productId}, Quantity {$quantity}");
    }

    /**
     * Menghitung ulang live stock berdasarkan formula
     *
     * @param FinishedGoods $finishedGood
     * @return void
     */
    public function recalculateLiveStock(FinishedGoods $finishedGood)
    {
        $oldLiveStock = $finishedGood->live_stock;

        $finishedGood->live_stock = $finishedGood->stok_awal +
            $finishedGood->stok_masuk -
            $finishedGood->stok_keluar -
            $finishedGood->defective;

        // Allow negative stock but log a warning
        if ($finishedGood->live_stock < 0) {
            // Get product info for better logging
            $product = Product::find($finishedGood->product_id);
            $sku = $product ? $product->sku : 'Unknown';

            Log::warning("Negative stock detected for product {$sku} (ID: {$finishedGood->product_id}). " .
                "Values: stok_awal={$finishedGood->stok_awal}, stok_masuk={$finishedGood->stok_masuk}, " .
                "stok_keluar={$finishedGood->stok_keluar}, defective={$finishedGood->defective}, " .
                "live_stock={$finishedGood->live_stock}");
        }

        // Log stock change if significant
        if ($oldLiveStock != $finishedGood->live_stock) {
            Log::info("Live stock updated for product ID {$finishedGood->product_id}: {$oldLiveStock} -> {$finishedGood->live_stock}");
        }
    }

    /**
     * Memperbarui jumlah defective dan menghitung ulang live stock
     *
     * @param int $productId
     * @param int $defectiveCount
     * @return void
     */
    public function updateDefectiveStock($productId, $defectiveCount)
    {
        try {
            DB::transaction(function () use ($productId, $defectiveCount) {
                $finishedGood = FinishedGoods::where('product_id', $productId)->firstOrFail();
                $finishedGood->defective = $defectiveCount;
                $this->recalculateLiveStock($finishedGood);
                $finishedGood->save();

                Log::info("Defective stock diperbarui: Product ID {$productId}, Count {$defectiveCount}");
            });
        } catch (\Exception $e) {
            Log::error("Gagal memperbarui defective stock: " . $e->getMessage());
            throw $e;
        }
    }
}
