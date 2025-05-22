<?php

namespace App\Jobs;

use App\Models\CatatanProduksi;
use App\Models\FinishedGoods;
use App\Models\HistorySale;
use App\Models\Product;
use App\Models\HistorySaleDetail;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;

class VerifyStockConsistencyJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * Create a new job instance.
     */
    public function __construct()
    {
        //
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        Log::info('Memulai verifikasi konsistensi stok');

        $finishedGoods = FinishedGoods::all();
        $issuesFound = 0;

        foreach ($finishedGoods as $finishedGood) {
            // Hitung total stok masuk dari catatan produksi
            $totalProduction = CatatanProduksi::where('product_id', $finishedGood->id_product)
                ->sum('quantity');

            // Hitung total stok keluar dari history sales
            $productSku = Product::find($finishedGood->id_product)->sku;
            $totalSales = $this->calculateTotalSales($productSku);

            // Verifikasi data stok masuk
            if ($finishedGood->stok_masuk != $totalProduction) {
                $issuesFound++;
                Log::warning("Perbedaan stok masuk untuk product_id: {$finishedGood->id_product}. " .
                    "DB: {$finishedGood->stok_masuk}, Hitung: {$totalProduction}");

                // Auto-koreksi opsional (uncomment to enable)
                // $finishedGood->stok_masuk = $totalProduction;
                // $needsSave = true;
            }

            // Verifikasi data stok keluar
            if ($finishedGood->stok_keluar != $totalSales) {
                $issuesFound++;
                Log::warning("Perbedaan stok keluar untuk product_id: {$finishedGood->id_product}. " .
                    "DB: {$finishedGood->stok_keluar}, Hitung: {$totalSales}");

                // Auto-koreksi opsional (uncomment to enable)
                // $finishedGood->stok_keluar = $totalSales;
                // $needsSave = true;
            }

            // Verifikasi live stock
            $calculatedLiveStock = $finishedGood->stok_awal +
                $finishedGood->stok_masuk -
                $finishedGood->stok_keluar -
                $finishedGood->defective;

            if ($calculatedLiveStock < 0) {
                $calculatedLiveStock = 0;
            }

            if ($finishedGood->live_stock != $calculatedLiveStock) {
                $issuesFound++;
                Log::warning("Perbedaan live stock untuk product_id: {$finishedGood->id_product}. " .
                    "DB: {$finishedGood->live_stock}, Hitung: {$calculatedLiveStock}");

                // Auto-koreksi opsional (uncomment to enable)
                // $finishedGood->live_stock = $calculatedLiveStock;
                // $needsSave = true;
            }

            // Simpan perubahan jika auto-koreksi diaktifkan
            // if (isset($needsSave) && $needsSave) {
            //     $finishedGood->save();
            //     Log::info("Koreksi stok otomatis untuk product_id: {$finishedGood->id_product}");
            // }
        }

        Log::info("Verifikasi konsistensi stok selesai. Masalah ditemukan: {$issuesFound}");
    }

    /**
     * Menghitung total penjualan berdasarkan SKU produk
     *
     * @param string $sku
     * @return int
     */
    private function calculateTotalSales($sku)
    {
        // Dapatkan product_id dari SKU
        $product = Product::where('sku', $sku)->first();
        if (!$product) {
            return 0;
        }

        // Metode yang lebih efisien menggunakan relasi
        $total = HistorySaleDetail::where('product_id', $product->id)
            ->sum('quantity');

        // Jika tidak ada data di detail, coba hitung dari format lama (JSON)
        if ($total === 0) {
            $total = $this->calculateTotalSalesFromLegacyFormat($sku);
        }

        return $total;
    }

    /**
     * Menghitung total penjualan berdasarkan format JSON lama
     *
     * @param string $sku
     * @return int
     */
    private function calculateTotalSalesFromLegacyFormat($sku)
    {
        $total = 0;
        $historySales = HistorySale::all();

        foreach ($historySales as $sale) {
            $skuArray = $sale->no_sku;
            $qtyArray = $sale->qty;

            foreach ($skuArray as $index => $saleSku) {
                if ($saleSku == $sku && isset($qtyArray[$index])) {
                    $total += $qtyArray[$index];
                }
            }
        }

        return $total;
    }
}
