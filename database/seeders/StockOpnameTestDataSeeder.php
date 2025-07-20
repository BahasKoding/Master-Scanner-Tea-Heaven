<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\BahanBaku;
use App\Models\Product;
use App\Models\Sticker;
use App\Models\InventoryBahanBaku;
use App\Models\FinishedGoods;

class StockOpnameTestDataSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Create some test Bahan Baku if not exists
        if (BahanBaku::count() == 0) {
            BahanBaku::create([
                'nama_bahan_baku' => 'Test Tea Leaves',
                'satuan' => 'kg',
                'harga_per_satuan' => 50000
            ]);

            BahanBaku::create([
                'nama_bahan_baku' => 'Test Sugar',
                'satuan' => 'kg', 
                'harga_per_satuan' => 15000
            ]);
        }

        // Create some test Products if not exists
        if (Product::count() == 0) {
            Product::create([
                'nama_product' => 'Test Green Tea',
                'harga_product' => 25000,
                'deskripsi_product' => 'Test green tea product'
            ]);

            Product::create([
                'nama_product' => 'Test Black Tea',
                'harga_product' => 30000,
                'deskripsi_product' => 'Test black tea product'
            ]);
        }

        // Create some test Stickers if not exists
        if (Sticker::count() == 0) {
            Sticker::create([
                'nama_stiker' => 'Test Label A',
                'stok_stiker' => 100
            ]);

            Sticker::create([
                'nama_stiker' => 'Test Label B', 
                'stok_stiker' => 50
            ]);
        }

        // Create test Inventory Bahan Baku if not exists
        $bahanBakus = BahanBaku::all();
        foreach ($bahanBakus as $bahanBaku) {
            InventoryBahanBaku::firstOrCreate(
                ['id_bahan_baku' => $bahanBaku->id],
                [
                    'stok_masuk' => 100,
                    'stok_keluar' => 20,
                    'live_stok_gudang' => 80,
                    'tanggal_masuk' => now(),
                    'tanggal_keluar' => now()
                ]
            );
        }

        // Create test Finished Goods if not exists
        $products = Product::all();
        foreach ($products as $product) {
            FinishedGoods::firstOrCreate(
                ['id_product' => $product->id],
                [
                    'stok_finished_goods' => 50
                ]
            );
        }

        $this->command->info('✅ Test data untuk Stock Opname berhasil dibuat!');
    }
}
