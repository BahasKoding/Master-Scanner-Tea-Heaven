<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class TestingCatatanProduksiSeeder extends Seeder
{
    /**
     * Run the database seeds.
     * 
     * PERINGATAN: Seeder ini khusus untuk TESTING!
     * Jangan jalankan di production karena akan mengisi data dummy.
     */
    public function run(): void
    {
        $this->command->warn('🧪 TESTING SEEDER - Mengisi data dummy catatan produksi');
        $this->command->warn('⚠️  JANGAN gunakan di production!');

        if (app()->environment('production')) {
            $this->command->error('❌ Seeder testing tidak bisa dijalankan di production!');
            return;
        }

        // Get all products that have stickers
        $stickerProducts = DB::table('stickers')
            ->join('products', 'stickers.product_id', '=', 'products.id')
            ->select('products.id', 'products.sku', 'products.name_product', 'products.packaging')
            ->get();

        if ($stickerProducts->isEmpty()) {
            $this->command->error('Tidak ada produk dengan sticker ditemukan. Jalankan StickerSeeder terlebih dahulu.');
            return;
        }

        $this->command->info("Ditemukan {$stickerProducts->count()} produk dengan sticker");

        // Clear existing testing data (optional)
        if ($this->command->confirm('Hapus data catatan produksi yang ada? (untuk clean testing)')) {
            DB::table('catatan_produksis')->truncate();
            $this->command->info('Data catatan produksi dihapus.');
        }

        $catatanProduksi = [];
        $currentTimestamp = Carbon::now()->format('Y-m-d H:i:s');

        // Generate production records for each sticker product
        foreach ($stickerProducts as $product) {
            // Generate 2-5 production records per product over the last 30 days
            $recordCount = rand(2, 5);

            for ($i = 0; $i < $recordCount; $i++) {
                // Random date within last 30 days
                $randomDate = Carbon::now()->subDays(rand(1, 30));

                // Generate realistic production quantity based on packaging
                $quantity = $this->generateRealisticQuantity($product->packaging);

                // Generate dummy bahan baku data
                $bahanBakuData = $this->generateBahanBakuData($product->packaging);

                $catatanProduksi[] = [
                    'product_id' => $product->id,
                    'packaging' => $product->packaging,
                    'quantity' => $quantity,
                    'sku_induk' => json_encode($bahanBakuData['sku_induk']),
                    'gramasi' => json_encode($bahanBakuData['gramasi']),
                    'total_terpakai' => json_encode($bahanBakuData['total_terpakai']),
                    'created_at' => $randomDate->format('Y-m-d H:i:s'),
                    'updated_at' => $currentTimestamp
                ];
            }
        }

        // Insert in chunks
        if (!empty($catatanProduksi)) {
            $chunks = array_chunk($catatanProduksi, 100);
            foreach ($chunks as $chunk) {
                DB::table('catatan_produksis')->insert($chunk);
            }
        }

        $this->command->info("✅ Berhasil membuat {count($catatanProduksi)} catatan produksi testing");

        // Show summary
        $this->showSummary($stickerProducts);

        $this->command->warn('🔄 Observer akan otomatis update nilai produksi di tabel stickers');
        $this->command->info('💡 Cek tabel stickers untuk melihat perubahan nilai produksi');
    }

    /**
     * Generate realistic production quantity based on packaging type
     */
    private function generateRealisticQuantity($packaging)
    {
        switch ($packaging) {
            case 'P1': // EXTRA SMALL PACK
                return rand(50, 300); // 50-300 pcs
            case 'T1': // TIN CANISTER SERIES
                return rand(20, 150); // 20-150 pcs
            case 'T2': // TIN CANISTER CUSTOM
                return rand(15, 100); // 15-100 pcs
            case '-': // No packaging (Japanese teabags, Ceremonial matcha)
                return rand(10, 80); // 10-80 pcs
            default:
                return rand(10, 200); // Default range
        }
    }

    /**
     * Generate dummy bahan baku data for testing
     */
    private function generateBahanBakuData($packaging)
    {
        // Get some random bahan baku IDs from database
        $bahanBakuIds = DB::table('bahan_bakus')
            ->inRandomOrder()
            ->limit(rand(2, 4))
            ->pluck('id')
            ->toArray();

        if (empty($bahanBakuIds)) {
            // Fallback dummy data if no bahan baku found
            $bahanBakuIds = [1, 2, 3];
        }

        $gramasi = [];
        $totalTerpakai = [];

        foreach ($bahanBakuIds as $id) {
            // Generate realistic gramasi based on packaging
            switch ($packaging) {
                case 'P1':
                    $gramasiValue = rand(15, 100); // 15-100 gram
                    break;
                case 'T1':
                case 'T2':
                    $gramasiValue = rand(30, 250); // 30-250 gram
                    break;
                case '-':
                    $gramasiValue = rand(10, 50); // 10-50 gram
                    break;
                default:
                    $gramasiValue = rand(20, 150);
            }

            $gramasi[] = $gramasiValue;
            // Total terpakai = gramasi * quantity factor
            $totalTerpakai[] = $gramasiValue * rand(10, 50);
        }

        return [
            'sku_induk' => $bahanBakuIds,
            'gramasi' => $gramasi,
            'total_terpakai' => $totalTerpakai
        ];
    }

    /**
     * Show summary of generated data
     */
    private function showSummary($stickerProducts)
    {
        $this->command->info("\n📊 RINGKASAN DATA TESTING:");
        $this->command->info("────────────────────────────────");

        // Group by packaging
        $packagingGroups = $stickerProducts->groupBy('packaging');

        foreach ($packagingGroups as $packaging => $products) {
            $packagingName = $this->getPackagingName($packaging);
            $this->command->info("📦 {$packagingName}: {$products->count()} produk");

            // Show some example products
            $examples = $products->take(3);
            foreach ($examples as $product) {
                $totalProduksi = DB::table('catatan_produksis')
                    ->where('product_id', $product->id)
                    ->sum('quantity');
                $this->command->line("   • {$product->sku} - {$product->name_product} (Total Produksi: {$totalProduksi})");
            }

            if ($products->count() > 3) {
                $remaining = $products->count() - 3;
                $this->command->line("   ... dan {$remaining} produk lainnya");
            }
            $this->command->line("");
        }

        // Overall statistics
        $totalRecords = DB::table('catatan_produksis')->count();
        $totalProduction = DB::table('catatan_produksis')->sum('quantity');

        $this->command->info("📈 STATISTIK KESELURUHAN:");
        $this->command->info("   • Total Catatan Produksi: {$totalRecords}");
        $this->command->info("   • Total Quantity Produksi: {$totalProduction} pcs");
        $this->command->info("   • Rata-rata per Record: " . round($totalProduction / $totalRecords, 2) . " pcs");
    }

    /**
     * Get packaging display name
     */
    private function getPackagingName($packaging)
    {
        switch ($packaging) {
            case 'P1':
                return 'EXTRA SMALL PACK (P1)';
            case 'T1':
                return 'TIN CANISTER SERIES (T1)';
            case 'T2':
                return 'TIN CANISTER CUSTOM (T2)';
            case '-':
                return 'NO PACKAGING (Teabags/Matcha)';
            default:
                return "UNKNOWN ({$packaging})";
        }
    }
}
