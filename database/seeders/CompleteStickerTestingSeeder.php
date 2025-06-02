<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class CompleteStickerTestingSeeder extends Seeder
{
    /**
     * Run the database seeds.
     * 
     * MASTER TESTING SEEDER untuk sistem sticker
     * Menjalankan semua seeder testing dalam urutan yang benar
     */
    public function run(): void
    {
        $this->command->warn('🚀 MEMULAI COMPLETE STICKER TESTING SEEDER');
        $this->command->warn('⚠️  Ini akan mengisi data dummy untuk TESTING!');

        if (app()->environment('production')) {
            $this->command->error('❌ Testing seeder tidak bisa dijalankan di production!');
            return;
        }

        // Confirm before running
        if (!$this->command->confirm('Lanjutkan dengan testing seeder? (akan mengisi data dummy)')) {
            $this->command->info('❌ Testing seeder dibatalkan.');
            return;
        }

        $this->command->info('');
        $this->command->info('📋 URUTAN SEEDER YANG AKAN DIJALANKAN:');
        $this->command->info('1. StickerSeeder - Membuat data dasar sticker');
        $this->command->info('2. TestingPurchaseStickerSeeder - Simulasi pembelian sticker');
        $this->command->info('3. TestingCatatanProduksiSeeder - Simulasi produksi');
        $this->command->info('');

        try {
            // Step 1: Run StickerSeeder to ensure base sticker data exists
            $this->command->info('🔄 Step 1: Menjalankan StickerSeeder...');
            $this->call(StickerSeeder::class);
            $this->command->info('✅ StickerSeeder selesai');
            $this->command->info('');

            // Step 2: Run TestingPurchaseStickerSeeder for stok masuk simulation
            $this->command->info('🔄 Step 2: Menjalankan TestingPurchaseStickerSeeder...');
            $this->call(TestingPurchaseStickerSeeder::class);
            $this->command->info('✅ TestingPurchaseStickerSeeder selesai');
            $this->command->info('');

            // Step 3: Run TestingCatatanProduksiSeeder for production simulation
            $this->command->info('🔄 Step 3: Menjalankan TestingCatatanProduksiSeeder...');
            $this->call(TestingCatatanProduksiSeeder::class);
            $this->command->info('✅ TestingCatatanProduksiSeeder selesai');
            $this->command->info('');

            // Final summary
            $this->showFinalSummary();
        } catch (\Exception $e) {
            $this->command->error('❌ Error saat menjalankan seeder: ' . $e->getMessage());
            $this->command->error('Stack trace: ' . $e->getTraceAsString());
        }
    }

    /**
     * Show final summary of all testing data
     */
    private function showFinalSummary()
    {
        $this->command->info('');
        $this->command->info('🎉 COMPLETE STICKER TESTING SEEDER SELESAI!');
        $this->command->info('═══════════════════════════════════════════════');

        // Get statistics from database
        $stickerCount = \DB::table('stickers')->count();
        $purchaseCount = \DB::table('purchase_stickers')->count();
        $produksiCount = \DB::table('catatan_produksis')->count();

        $this->command->info("📊 RINGKASAN DATA TESTING:");
        $this->command->info("   • Total Stickers: {$stickerCount}");
        $this->command->info("   • Total Purchase Records: {$purchaseCount}");
        $this->command->info("   • Total Catatan Produksi: {$produksiCount}");

        // Test dynamic calculations
        $this->command->info('');
        $this->command->info('🧪 TESTING KALKULASI DINAMIS:');

        // Get a sample sticker to test
        $sampleSticker = \DB::table('stickers')
            ->join('products', 'stickers.product_id', '=', 'products.id')
            ->select('stickers.*', 'products.sku', 'products.name_product')
            ->first();

        if ($sampleSticker) {
            // Calculate dynamic values using the same logic as the model
            $stokMasuk = \DB::table('purchase_stickers')
                ->where('product_id', $sampleSticker->product_id)
                ->sum('stok_masuk');

            $produksi = \DB::table('catatan_produksis')
                ->where('product_id', $sampleSticker->product_id)
                ->sum('quantity');

            $sisa = $sampleSticker->stok_awal + $stokMasuk - $produksi - $sampleSticker->defect;

            $this->command->info("   📦 Sample: {$sampleSticker->sku} - {$sampleSticker->name_product}");
            $this->command->info("      • Stok Awal: {$sampleSticker->stok_awal}");
            $this->command->info("      • Stok Masuk (dinamis): {$stokMasuk}");
            $this->command->info("      • Produksi (dinamis): {$produksi}");
            $this->command->info("      • Defect: {$sampleSticker->defect}");
            $this->command->info("      • Sisa (dihitung): {$sisa}");
        }

        $this->command->info('');
        $this->command->info('🔗 SELANJUTNYA:');
        $this->command->info('   1. Buka halaman Manajemen Sticker di web');
        $this->command->info('   2. Lihat kolom Stok Masuk dan Produksi yang otomatis');
        $this->command->info('   3. Test inline editing pada kolom Stok Awal dan Defect');
        $this->command->info('   4. Perhatikan perubahan nilai Sisa secara real-time');
        $this->command->info('');

        $this->command->warn('💡 TIPS: Untuk reset data testing, jalankan:');
        $this->command->warn('   php artisan migrate:fresh --seed');
        $this->command->warn('   atau hapus manual dari tabel terkait');
    }
}
