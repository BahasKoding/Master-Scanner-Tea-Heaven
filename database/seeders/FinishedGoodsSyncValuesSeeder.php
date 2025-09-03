<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

use App\Models\Product;
use App\Models\FinishedGoods;   // model kamu (jamak)
use App\Models\HistorySale;

class FinishedGoodsSyncValuesSeeder extends Seeder
{
    public function run(): void
    {
        $this->command->info('🔁 Sinkronisasi FG: hitung stok_keluar dari HistorySale + update live_stock');

        // ------------------------------------------------------------
        // 0) Produk & normalisasi SKU
        // ------------------------------------------------------------
        $this->command->info('📦 Memuat produk & normalisasi SKU...');
        $products = Product::query()->select('id','sku')->get();

        if ($products->isEmpty()) {
            $this->command->warn('⚠️ Tidak ada data produk. Seeder dihentikan.');
            return;
        }

        $skuToProductId = [];
        $productIdToSku = [];
        foreach ($products as $p) {
            $norm = $this->normalizeSku($p->sku);
            if ($norm !== '') $skuToProductId[$norm] = (int) $p->id;
            $productIdToSku[(int)$p->id] = $p->sku;
        }

        // ------------------------------------------------------------
        // 1) stok_awal & defective existing (untuk live_stock)
        // ------------------------------------------------------------
        $this->command->info('📥 Memuat stok_awal & defective existing dari finished_goods...');
        $existingFg = FinishedGoods::query()
            ->select('product_id','stok_awal','defective','created_at')
            ->get();

        $stokAwalByPid  = [];
        $defectByPid    = [];
        $createdAtByPid = [];
        foreach ($existingFg as $fg) {
            $pid = (int)$fg->product_id;
            $stokAwalByPid[$pid]  = (int)$fg->stok_awal;
            $defectByPid[$pid]    = (int)$fg->defective;
            $createdAtByPid[$pid] = $fg->created_at;
        }

        // ------------------------------------------------------------
        // 2) Agregasi SALES all-time dari HistorySale (no_sku & qty = JSON sejajar)
        // ------------------------------------------------------------
        $this->command->info('🧮 Mengagregasi penjualan all-time dari HistorySale...');

        $totalSaleRows    = 0;
        $mappedSkuCount   = 0;
        $unmappedSkuCount = 0;
        $unmappedSkuSeen  = [];
        $stokKeluarByPid  = [];

        HistorySale::query()
            ->whereNotNull('no_sku')
            ->orderBy('id')
            ->cursor()
            ->each(function ($sale) use (&$totalSaleRows, &$mappedSkuCount, &$unmappedSkuCount, &$unmappedSkuSeen, &$stokKeluarByPid, $skuToProductId) {
                $totalSaleRows++;

                $skuArray = is_string($sale->no_sku) ? json_decode($sale->no_sku, true) : $sale->no_sku;
                $qtyArray = is_string($sale->qty)    ? json_decode($sale->qty, true)    : $sale->qty;

                if (!is_array($skuArray) || !is_array($qtyArray)) return;

                $len = min(count($skuArray), count($qtyArray));
                for ($i = 0; $i < $len; $i++) {
                    $rawSku = $skuArray[$i];
                    $qty    = (int)($qtyArray[$i] ?? 0);
                    if ($qty <= 0) continue;

                    $normSku = $this->normalizeSku($rawSku);
                    if ($normSku === '') continue;

                    if (!isset($skuToProductId[$normSku])) {
                        $unmappedSkuCount++;
                        if (count($unmappedSkuSeen) < 10) $unmappedSkuSeen[$normSku] = true;
                        continue;
                    }

                    $pid = $skuToProductId[$normSku];
                    if (!isset($stokKeluarByPid[$pid])) $stokKeluarByPid[$pid] = 0;
                    $stokKeluarByPid[$pid] += $qty;
                    $mappedSkuCount++;
                }
            });

        $this->command->info("   • HistorySale rows    : {$totalSaleRows}");
        $this->command->info("   • SKU terpetakan      : {$mappedSkuCount}");
        $this->command->info("   • SKU TIDAK terpetakan: {$unmappedSkuCount}");
        if (!empty($unmappedSkuSeen)) {
            $sample = implode(', ', array_slice(array_keys($unmappedSkuSeen), 0, 10));
            $this->command->warn("   • Contoh SKU tak terpetakan (normalisasi): {$sample}");
        }

        // ------------------------------------------------------------
        // 3) Susun payload upsert: stok_keluar + live_stock
        //    live_stock = stok_awal + stok_masuk - stok_keluar - defective + stok_sisa
        // ------------------------------------------------------------
        $this->command->info('💾 Menyiapkan payload upsert (stok_keluar + live_stock)...');

        $now     = now();
        $updates = [];
        $batch   = 0;

        // PENTING: sertakan kolom 'id' di SELECT + orderBy('id') untuk chunkById()
        FinishedGoods::query()
            ->select('id','product_id','stok_awal','stok_masuk','stok_sisa','defective','live_stock','created_at')
            ->orderBy('id')
            ->chunkById(1000, function ($chunk) use (&$updates, &$batch, $stokKeluarByPid, $now) {
                foreach ($chunk as $fg) {
                    $pid         = (int)$fg->product_id;
                    $stokKeluar  = (int)($stokKeluarByPid[$pid] ?? 0);

                    $stokAwal    = (int)$fg->stok_awal;
                    $stokMasuk   = (int)$fg->stok_masuk;
                    $stokSisa    = (int)$fg->stok_sisa;
                    $defective   = (int)$fg->defective;

                    $liveStock   = $stokAwal + $stokMasuk - $stokKeluar - $defective + $stokSisa;

                    $updates[] = [
                        'product_id'  => $pid,                     // UNIQUE KEY
                        'stok_keluar' => $stokKeluar,
                        'live_stock'  => $liveStock,
                        'updated_at'  => $now,
                        'created_at'  => $fg->created_at ?? $now,  // untuk INSERT jika belum ada row
                    ];
                }

                if (count($updates) >= 1000) {
                    DB::table('finished_goods')->upsert(
                        $updates,
                        ['product_id'],
                        ['stok_keluar', 'live_stock', 'updated_at']
                    );
                    $batch += count($updates);
                    $updates = [];
                    $this->command->info("   -> upserted {$batch}+");
                }
            });

        // Tambahkan product yang BELUM punya baris FG sama sekali
        $fgPidSet = FinishedGoods::query()->pluck('product_id')->all();
        $fgPidSet = array_flip(array_map('intval', $fgPidSet));

        foreach ($stokKeluarByPid as $pid => $keluar) {
            if (isset($fgPidSet[(int)$pid])) continue;
            $updates[] = [
                'product_id'  => (int)$pid,
                'stok_keluar' => (int)$keluar,
                'live_stock'  => 0 - (int)$keluar, // stok_awal=0, stok_masuk=0, defective=0, stok_sisa=0
                'updated_at'  => $now,
                'created_at'  => $now,
            ];
        }

        if (!empty($updates)) {
            DB::table('finished_goods')->upsert(
                $updates,
                ['product_id'],
                ['stok_keluar', 'live_stock', 'updated_at']
            );
            $batch += count($updates);
        }

        $this->command->info("✅ Selesai. Upsert total: {$batch} baris FG (stok_keluar + live_stock).");
    }

    /**
     * Normalisasi SKU:
     * - trim
     * - hapus whitespace
     * - uppercase
     */
    private function normalizeSku(?string $sku): string
    {
        if ($sku === null) return '';
        $sku = trim($sku);
        if ($sku === '') return '';
        $sku = preg_replace('/\s+/u', '', $sku);      // hapus semua whitespace
        $sku = str_replace("\xC2\xA0", '', $sku);     // hapus NBSP
        return Str::upper($sku);
    }
}
