<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class TestingPurchaseStickerSeeder extends Seeder
{
    /**
     * Run the database seeds.
     * 
     * PERINGATAN: Seeder ini khusus untuk TESTING!
     * Jangan jalankan di production karena akan mengisi data dummy.
     */
    public function run(): void
    {
        $this->command->warn('🧪 TESTING SEEDER - Mengisi data dummy purchase sticker');
        $this->command->warn('⚠️  JANGAN gunakan di production!');

        if (app()->environment('production')) {
            $this->command->error('❌ Seeder testing tidak bisa dijalankan di production!');
            return;
        }

        // Get all products that have stickers
        $stickerProducts = DB::table('stickers')
            ->join('products', 'stickers.product_id', '=', 'products.id')
            ->select('products.id', 'products.sku', 'products.name_product', 'products.packaging', 'stickers.ukuran', 'stickers.jumlah')
            ->get();

        if ($stickerProducts->isEmpty()) {
            $this->command->error('Tidak ada produk dengan sticker ditemukan. Jalankan StickerSeeder terlebih dahulu.');
            return;
        }

        $this->command->info("Ditemukan {$stickerProducts->count()} produk dengan sticker");

        // Clear existing testing data (optional)
        if ($this->command->confirm('Hapus data purchase sticker yang ada? (untuk clean testing)')) {
            DB::table('purchase_stickers')->truncate();
            $this->command->info('Data purchase sticker dihapus.');
        }

        $purchaseStickers = [];
        $currentTimestamp = Carbon::now()->format('Y-m-d H:i:s');

        // Generate purchase records for each sticker product
        foreach ($stickerProducts as $product) {
            // Generate 1-3 purchase records per product over the last 60 days
            $recordCount = rand(1, 3);

            for ($i = 0; $i < $recordCount; $i++) {
                // Random date within last 60 days
                $randomDate = Carbon::now()->subDays(rand(1, 60));

                // Generate realistic purchase quantities
                $quantities = $this->generatePurchaseQuantities($product->packaging, $product->jumlah);

                $purchaseStickers[] = [
                    'product_id' => $product->id,
                    'ukuran_stiker' => $product->ukuran,
                    'jumlah_stiker' => $product->jumlah,
                    'jumlah_order' => $quantities['jumlah_order'],
                    'stok_masuk' => $quantities['stok_masuk'],
                    'total_order' => $quantities['total_order'],
                    'created_at' => $randomDate->format('Y-m-d H:i:s'),
                    'updated_at' => $currentTimestamp
                ];
            }
        }

        // Insert in chunks
        if (!empty($purchaseStickers)) {
            $chunks = array_chunk($purchaseStickers, 100);
            foreach ($chunks as $chunk) {
                DB::table('purchase_stickers')->insert($chunk);
            }
        }

        $this->command->info("✅ Berhasil membuat " . count($purchaseStickers) . " purchase sticker testing");

        // Show summary
        $this->showSummary($stickerProducts);

        $this->command->warn('🔄 Stok masuk di sticker akan otomatis terhitung dari purchase ini');
        $this->command->info('💡 Cek tabel stickers untuk melihat perubahan nilai stok masuk dinamis');
    }

    /**
     * Generate realistic purchase quantities based on packaging type
     */
    private function generatePurchaseQuantities($packaging, $jumlahPerA3)
    {
        // Calculate based on A3 sheets ordered
        $a3SheetsOrdered = $this->generateA3SheetsOrder($packaging);

        // Calculate total stickers from A3 sheets
        $totalStickers = $a3SheetsOrdered * intval($jumlahPerA3);

        // Simulate some variation in actual received vs ordered
        $receivedPercentage = rand(85, 100) / 100; // 85-100% received
        $stokMasuk = intval($totalStickers * $receivedPercentage);

        return [
            'jumlah_order' => $a3SheetsOrdered,
            'stok_masuk' => $stokMasuk,
            'total_order' => $totalStickers
        ];
    }

    /**
     * Generate realistic A3 sheets order based on packaging type
     */
    private function generateA3SheetsOrder($packaging)
    {
        switch ($packaging) {
            case 'P1': // EXTRA SMALL PACK - high volume
                return rand(10, 50); // 10-50 A3 sheets
            case 'T1': // TIN CANISTER SERIES - medium volume
                return rand(5, 30); // 5-30 A3 sheets
            case 'T2': // TIN CANISTER CUSTOM - lower volume
                return rand(3, 20); // 3-20 A3 sheets
            case '-': // No packaging - very low volume but high stickers per sheet
                return rand(2, 15); // 2-15 A3 sheets
            default:
                return rand(5, 25); // Default range
        }
    }

    /**
     * Show summary of generated data
     */
    private function showSummary($stickerProducts)
    {
        $this->command->info("\n📊 RINGKASAN DATA PURCHASE STICKER TESTING:");
        $this->command->info("──────────────────────────────────────────────");

        // Group by packaging
        $packagingGroups = $stickerProducts->groupBy('packaging');

        foreach ($packagingGroups as $packaging => $products) {
            $packagingName = $this->getPackagingName($packaging);
            $this->command->info("📦 {$packagingName}: {$products->count()} produk");

            // Calculate total stok masuk for this packaging type
            $productIds = $products->pluck('id')->toArray();
            $totalStokMasuk = DB::table('purchase_stickers')
                ->whereIn('product_id', $productIds)
                ->sum('stok_masuk');

            $this->command->line("   💰 Total Stok Masuk: {$totalStokMasuk} stickers");

            // Show some example products
            $examples = $products->take(3);
            foreach ($examples as $product) {
                $productStokMasuk = DB::table('purchase_stickers')
                    ->where('product_id', $product->id)
                    ->sum('stok_masuk');
                $this->command->line("   • {$product->sku}: {$productStokMasuk} stickers");
            }

            if ($products->count() > 3) {
                $remaining = $products->count() - 3;
                $this->command->line("   ... dan {$remaining} produk lainnya");
            }
            $this->command->line("");
        }

        // Overall statistics
        $totalRecords = DB::table('purchase_stickers')->count();
        $totalStokMasuk = DB::table('purchase_stickers')->sum('stok_masuk');
        $totalA3Sheets = DB::table('purchase_stickers')->sum('jumlah_order');

        $this->command->info("📈 STATISTIK KESELURUHAN:");
        $this->command->info("   • Total Purchase Records: {$totalRecords}");
        $this->command->info("   • Total A3 Sheets Ordered: {$totalA3Sheets}");
        $this->command->info("   • Total Stok Masuk: {$totalStokMasuk} stickers");
        $this->command->info("   • Rata-rata Stok Masuk per Record: " . round($totalStokMasuk / $totalRecords, 2) . " stickers");
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
