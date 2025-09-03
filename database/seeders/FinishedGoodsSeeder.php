<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use App\Models\Product;
use App\Models\FinishedGoods;

class FinishedGoodsSeeder extends Seeder
{
    public function run(): void
    {
        $this->command->info('🔄 Sinkronisasi Finished Goods dari daftar Product...');

        // Ambil semua product_id
        $productIds = Product::query()->pluck('id')->all();
        if (empty($productIds)) {
            $this->command->warn('Tidak ada product untuk disinkron.');
            return;
        }

        // Ambil product_id yang sudah punya FG agar tidak duplikat
        $existing = FinishedGoods::query()->pluck('product_id')->all();
        $existing = array_flip($existing);

        $now = now();
        $rows = [];

        foreach ($productIds as $pid) {
            if (isset($existing[$pid])) continue;

            $rows[] = [
                'product_id'  => $pid,
                'stok_awal'   => 0,
                'stok_masuk'  => 0,
                'stok_keluar' => 0,
                'defective'   => 0,
                'stok_sisa'   => 0,     // WAJIB diisi (hindari 1364)
                'live_stock'  => 0,
                'created_at'  => $now,
                'updated_at'  => $now,
            ];
        }

        if (count($rows)) {
            // Upsert biar idempotent (aman di rerun)
            // Pastikan ada unique index di product_id (lihat catatan di bawah)
            DB::table('finished_goods')->upsert(
                $rows,
                ['product_id'],                        // unique-by
                ['stok_awal','stok_masuk','stok_keluar','defective','stok_sisa','live_stock','updated_at'] // update cols
            );
            $this->command->info('✅ Finished Goods tersinkron: '.count($rows).' record baru.');
        } else {
            $this->command->info('✅ Semua product sudah punya baris Finished Goods.');
        }
    }
}
