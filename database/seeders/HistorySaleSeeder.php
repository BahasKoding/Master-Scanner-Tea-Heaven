<?php

namespace Database\Seeders;

use App\Models\HistorySale;
use App\Models\Product;
use Illuminate\Database\Seeder;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;

class HistorySaleSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Hapus data lama jika ada - dengan aman (mengatasi foreign key constraint)
        DB::statement('SET FOREIGN_KEY_CHECKS=0;');
        HistorySale::query()->delete();
        // Kita tidak menggunakan truncate() karena masalah foreign key constraint
        DB::statement('SET FOREIGN_KEY_CHECKS=1;');

        // Ambil semua SKU produk dari database
        $products = Product::select('sku', 'category_product')->get();

        if ($products->isEmpty()) {
            $this->command->warn('Tidak ada produk ditemukan. Pastikan product seeder sudah dijalankan sebelumnya.');
            return;
        }

        $this->command->info('Membuat data riwayat penjualan dari ' . $products->count() . ' produk.');

        // Distribusi tanggal
        // - 20% data dari 5-6 bulan yang lalu
        // - 30% data dari 3-4 bulan yang lalu
        // - 40% data dari 1-2 bulan yang lalu
        // - 10% data dari minggu terakhir

        $totalRecords = 250;
        $recordsOld = (int)($totalRecords * 0.2); // 5-6 bulan lalu
        $recordsMedium = (int)($totalRecords * 0.3); // 3-4 bulan lalu
        $recordsRecent = (int)($totalRecords * 0.4); // 1-2 bulan lalu
        $recordsLatest = $totalRecords - $recordsOld - $recordsMedium - $recordsRecent; // minggu terakhir

        $sales = [];
        $count = 0;

        // 1. Data 5-6 bulan lalu
        for ($i = 0; $i < $recordsOld; $i++) {
            $date = Carbon::now()->subMonths(rand(5, 6))->subDays(rand(0, 30));
            $sales[] = $this->generateSale($products, $date, $count++);
        }

        // 2. Data 3-4 bulan lalu
        for ($i = 0; $i < $recordsMedium; $i++) {
            $date = Carbon::now()->subMonths(rand(3, 4))->subDays(rand(0, 30));
            $sales[] = $this->generateSale($products, $date, $count++);
        }

        // 3. Data 1-2 bulan lalu
        for ($i = 0; $i < $recordsRecent; $i++) {
            $date = Carbon::now()->subMonths(rand(1, 2))->subDays(rand(0, 30));
            $sales[] = $this->generateSale($products, $date, $count++);
        }

        // 4. Data minggu terakhir
        for ($i = 0; $i < $recordsLatest; $i++) {
            $date = Carbon::now()->subDays(rand(0, 7));
            $sales[] = $this->generateSale($products, $date, $count++);
        }

        // Insert ke database dalam batches
        foreach (array_chunk($sales, 50) as $chunk) {
            HistorySale::insert($chunk);
        }

        $this->command->info("$totalRecords riwayat penjualan berhasil dibuat dengan distribusi tanggal yang bervariasi.");
    }

    /**
     * Generate satu record penjualan
     * 
     * @param \Illuminate\Database\Eloquent\Collection $products
     * @param \Carbon\Carbon $date
     * @param int $index
     * @return array
     */
    private function generateSale($products, $date, $index)
    {
        // Buat nomor resi dengan format RS + tanggal + 3 digit nomor
        $resiNumber = 'RS' . $date->format('Ymd') . str_pad($index % 1000, 3, '0', STR_PAD_LEFT);

        // Pilih 1-3 produk random
        $numberOfProducts = rand(1, 3);
        $selectedProducts = $products->random($numberOfProducts);

        $skus = [];
        $quantities = [];

        foreach ($selectedProducts as $product) {
            $skus[] = $product->sku;

            // Set jumlah berdasarkan kategori produk
            switch ($product->category_product) {
                case 1: // CLASSIC TEA COLLECTION
                case 2: // PURE TISANE
                case 3: // ARTISAN TEA
                    $quantities[] = rand(1, 5); // Teh biasanya dibeli dalam jumlah kecil
                    break;
                case 7: // SWEET POWDER
                case 8: // LATTE POWDER
                    $quantities[] = rand(1, 3); // Powder biasanya dibeli dalam jumlah sedang
                    break;
                case 11: // TEA WARE
                    $quantities[] = rand(1, 2); // Peralatan teh biasanya dibeli sedikit
                    break;
                default:
                    $quantities[] = rand(1, 4); // Default untuk kategori lain
            }
        }

        return [
            'no_resi' => $resiNumber,
            'no_sku' => json_encode($skus),
            'qty' => json_encode($quantities),
            'created_at' => $date,
            'updated_at' => $date,
        ];
    }
}
