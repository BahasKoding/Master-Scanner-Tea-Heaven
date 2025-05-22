<?php

namespace App\Services;

use App\Models\CatatanProduksi;
use App\Models\FinishedGoods;
use App\Models\HistorySale;
use App\Models\Product;
use App\Models\HistorySaleDetail;
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
                $finishedGood = FinishedGoods::firstOrNew(['id_product' => $productId]);

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
                $finishedGood = FinishedGoods::where('id_product', $productId)->firstOrFail();
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
                $finishedGood = FinishedGoods::where('id_product', $productId)->firstOrFail();
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
     * Update stok keluar pada Finished Goods dari data penjualan baru
     *
     * @param HistorySale $historySale
     * @return void
     */
    public function updateStockFromSales(HistorySale $historySale)
    {
        // Support untuk format lama (JSON array)
        if (empty($historySale->details) || $historySale->details->count() === 0) {
            $skuArray = $historySale->no_sku;
            $qtyArray = $historySale->qty;

            if (count($skuArray) !== count($qtyArray)) {
                throw new \Exception("Data SKU dan Quantity tidak sesuai");
            }

            try {
                DB::transaction(function () use ($historySale, $skuArray, $qtyArray) {
                    // Pertama, buat detail records untuk menjaga integritas data
                    foreach ($skuArray as $index => $sku) {
                        $product = Product::where('sku', $sku)->first();
                        if (!$product) {
                            throw new \Exception("SKU tidak ditemukan: {$sku}");
                        }

                        $quantity = $qtyArray[$index];

                        // Buat detail record
                        HistorySaleDetail::create([
                            'history_sale_id' => $historySale->id,
                            'product_id' => $product->id,
                            'quantity' => $quantity
                        ]);

                        // Perbarui stok
                        $this->updateStockForProduct($product->id, $quantity);
                    }
                });
            } catch (\Exception $e) {
                Log::error("Gagal menambahkan stok keluar: " . $e->getMessage());
                throw $e;
            }
        } else {
            // Format baru (menggunakan relasi)
            try {
                DB::transaction(function () use ($historySale) {
                    foreach ($historySale->details as $detail) {
                        $this->updateStockForProduct($detail->product_id, $detail->quantity);
                    }
                });
            } catch (\Exception $e) {
                Log::error("Gagal menambahkan stok keluar: " . $e->getMessage());
                throw $e;
            }
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
        $finishedGood = FinishedGoods::where('id_product', $productId)->first();
        if (!$finishedGood) {
            throw new \Exception("Finished Good tidak ditemukan untuk produk ID: {$productId}");
        }

        if ($finishedGood->live_stock < $quantity) {
            $product = Product::find($productId);
            $sku = $product ? $product->sku : 'Unknown';
            throw new \Exception("Stok tidak mencukupi untuk {$sku}. Tersedia: {$finishedGood->live_stock}, Diminta: {$quantity}");
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
                // Format lama (JSON array)
                if ($isUpdate || empty($historySale->details) || $historySale->details->count() === 0) {
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

                    foreach ($skuArray as $index => $sku) {
                        $product = Product::where('sku', $sku)->first();
                        if (!$product) {
                            continue; // Skip jika produk tidak ditemukan
                        }

                        $quantity = $qtyArray[$index];
                        $this->decreaseStockForProduct($product->id, $quantity);
                    }
                } else {
                    // Format baru menggunakan relasi
                    foreach ($historySale->details as $detail) {
                        $this->decreaseStockForProduct($detail->product_id, $detail->quantity);
                    }
                }
            });
        } catch (\Exception $e) {
            Log::error("Gagal mengembalikan stok keluar: " . $e->getMessage());
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
        $finishedGood = FinishedGoods::where('id_product', $productId)->first();
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
        $finishedGood->live_stock = $finishedGood->stok_awal +
            $finishedGood->stok_masuk -
            $finishedGood->stok_keluar -
            $finishedGood->defective;

        if ($finishedGood->live_stock < 0) {
            $finishedGood->live_stock = 0; // Pastikan live stock tidak negatif
            Log::warning("Perhitungan menyebabkan stok negatif untuk product ID: {$finishedGood->id_product}");
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
                $finishedGood = FinishedGoods::where('id_product', $productId)->firstOrFail();
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
