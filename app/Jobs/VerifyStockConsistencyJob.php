<?php

namespace App\Jobs;

use App\Models\CatatanProduksi;
use App\Models\FinishedGoods;
use App\Models\HistorySale;
use App\Models\Product;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;

class VerifyStockConsistencyJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $discrepancies = [];
    protected $correctionsMade = 0;

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
            // Hitung total produksi dari CatatanProduksi
            $totalProduction = CatatanProduksi::where('product_id', $finishedGood->product_id)
                ->sum('quantity');

            // Hitung total penjualan dari HistorySale
            $productSku = Product::find($finishedGood->product_id)->sku;
            $totalSales = $this->calculateTotalSales($productSku);

            // Verifikasi stok masuk
            if (abs($finishedGood->stok_masuk - $totalProduction) > 0.01) {
                $issuesFound++;
                Log::warning("Perbedaan stok masuk untuk product_id: {$finishedGood->product_id}. " .
                    "Database: {$finishedGood->stok_masuk}, Calculated: {$totalProduction}");

                $this->discrepancies['stok_masuk'][] = [
                    'product_id' => $finishedGood->product_id,
                    'database' => $finishedGood->stok_masuk,
                    'calculated' => $totalProduction
                ];

                $this->correctionsMade++;
            }

            // Verifikasi stok keluar
            if (abs($finishedGood->stok_keluar - $totalSales) > 0.01) {
                $issuesFound++;
                Log::warning("Perbedaan stok keluar untuk product_id: {$finishedGood->product_id}. " .
                    "Database: {$finishedGood->stok_keluar}, Calculated: {$totalSales}");

                $this->discrepancies['stok_keluar'][] = [
                    'product_id' => $finishedGood->product_id,
                    'database' => $finishedGood->stok_keluar,
                    'calculated' => $totalSales
                ];

                $this->correctionsMade++;
            }

            // Hitung live stock yang benar
            $calculatedLiveStock = $finishedGood->stok_awal + $totalProduction - $totalSales - $finishedGood->defective;

            // Pastikan live stock tidak negatif
            $calculatedLiveStock = max(0, $calculatedLiveStock);

            // Verifikasi live stock
            if (abs($finishedGood->live_stock - $calculatedLiveStock) > 0.01) {
                $issuesFound++;
                Log::warning("Perbedaan live stock untuk product_id: {$finishedGood->product_id}. " .
                    "Database: {$finishedGood->live_stock}, Calculated: {$calculatedLiveStock}");

                $this->discrepancies['live_stock'][] = [
                    'product_id' => $finishedGood->product_id,
                    'database' => $finishedGood->live_stock,
                    'calculated' => $calculatedLiveStock
                ];

                $this->correctionsMade++;
            }

            // if ($this->shouldAutoCorrect) {
            //     $finishedGood->stok_masuk = $totalProduction;
            //     $finishedGood->stok_keluar = $totalSales;
            //     $finishedGood->live_stock = $calculatedLiveStock;
            //     $finishedGood->save();
            //     Log::info("Koreksi stok otomatis untuk product_id: {$finishedGood->product_id}");
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
        $total = 0;
        $historySales = HistorySale::whereNotNull('no_sku')->get();

        foreach ($historySales as $sale) {
            $skuArray = is_string($sale->no_sku) ? json_decode($sale->no_sku, true) : $sale->no_sku;
            $qtyArray = is_string($sale->qty) ? json_decode($sale->qty, true) : $sale->qty;

            if (!is_array($skuArray) || !is_array($qtyArray)) {
                continue;
            }

            foreach ($skuArray as $index => $saleSku) {
                if (trim($saleSku) === $sku && isset($qtyArray[$index])) {
                    $quantity = $qtyArray[$index];
                    if (is_numeric($quantity) && $quantity > 0) {
                        $total += (int)$quantity;
                    }
                }
            }
        }

        return $total;
    }
}
