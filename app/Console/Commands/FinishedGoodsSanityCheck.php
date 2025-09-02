<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;
use App\Models\Product;
use App\Models\FinishedGoods;
use App\Models\CatatanProduksi;
use App\Models\Purchase;
use App\Models\HistorySale;
use Carbon\Carbon;

class FinishedGoodsSanityCheck extends Command
{
    protected $signature = 'fg:sanity-check 
                            {--month= : Bulan target dalam format YYYY-MM (default: bulan ini)} 
                            {--product= : Filter 1 produk (product_id)} 
                            {--fix : Update nilai FG yang mismatch}';

    protected $description = 'Cek konsistensi Finished Goods vs sumber (Produksi, Purchase FG, History Sale) per bulan + opsi auto-fix';

    public function handle()
    {
        $monthOpt = $this->option('month') ?: now()->format('Y-m');
        $productIdOpt = $this->option('product');
        $doFix = (bool) $this->option('fix');

        // range bulan
        try {
            [$startDate, $endDate] = $this->monthRange($monthOpt);
        } catch (\Throwable $e) {
            $this->error("Format --month invalid. Pakai YYYY-MM. Contoh: --month={$this->guessPrevMonth()}");
            return Command::FAILURE;
        }

        $this->line("FG Sanity Check for: <info>{$monthOpt}</info> ( {$startDate} .. {$endDate} )");
        if ($productIdOpt) $this->line("Filter product_id: <info>{$productIdOpt}</info>");
        if ($doFix) $this->warn('Mode FIX aktif: akan mengupdate nilai mismatch.');

        $productsQ = Product::query();
        if ($productIdOpt) $productsQ->where('id', $productIdOpt);

        $rows = [];
        $mismatchCount = 0; $fixed = 0;

        $productsQ->chunkById(200, function ($chunk) use (&$rows, &$mismatchCount, &$fixed, $startDate, $endDate, $monthOpt, $doFix) {
            foreach ($chunk as $product) {
                // Grab FG row (atau buat objek in-memory tanpa save)
                $fg = FinishedGoods::firstOrNew(['product_id' => $product->id]);

                // Hitung dinamis per bulan:
                $dynamicIn  = $this->calcStokMasukMonthly($product->id, $startDate, $endDate);
                $dynamicOut = $this->calcStokKeluarMonthly($product->sku, $startDate, $endDate);

                // Ambil tersimpan (bisa null)
                $storedIn   = (int) ($fg->stok_masuk ?? 0);
                $storedOut  = (int) ($fg->stok_keluar ?? 0);

                // Hitung live stock per aturan kamu:
                // stok_awal & defective direset tiap awal bulan (ditaruh sesuai DB), stok_sisa datang dari opname bulan lalu (nilai di DB)
                $stokAwal   = (int) ($fg->stok_awal ?? 0);
                $defective  = (int) ($fg->defective ?? 0);
                $stokSisa   = (int) ($fg->stok_sisa ?? 0);

                $dynamicLive = $stokAwal + $dynamicIn - $dynamicOut - $defective + $stokSisa;
                $storedLive  = (int) ($fg->live_stock ?? 0);

                $inOK   = ($storedIn  === $dynamicIn);
                $outOK  = ($storedOut === $dynamicOut);
                $liveOK = ($storedLive === $dynamicLive);

                $status = ($inOK && $outOK && $liveOK) ? 'OK' : 'MISMATCH';
                if ($status === 'MISMATCH') $mismatchCount++;

                // FIX mode: update nilai tersimpan agar match yang dinamis bulan ini
                if ($status === 'MISMATCH' && $doFix) {
                    $fg->stok_masuk  = $dynamicIn;
                    $fg->stok_keluar = $dynamicOut;
                    $fg->live_stock  = $dynamicLive;
                    $fg->save();
                    $fixed++;
                    $status = 'FIXED';
                }

                $rows[] = [
                    'product_id' => $product->id,
                    'sku'        => $product->sku,
                    'name'       => $product->name_product,
                    'IN(db/dyn)' => $storedIn . '/' . $dynamicIn,
                    'OUT(db/dyn)' => $storedOut . '/' . $dynamicOut,
                    'LIVE(db/dyn)' => $storedLive . '/' . $dynamicLive,
                    'status'     => $status,
                ];
            }
        });

        // Tampilkan ringkasan
        $this->table(['product_id', 'sku', 'name', 'IN(db/dyn)', 'OUT(db/dyn)', 'LIVE(db/dyn)', 'status'], $rows);

        $this->info("Total diperiksa : " . count($rows));
        $this->info("Mismatch        : {$mismatchCount}");
        if ($doFix) $this->info("Diperbaiki      : {$fixed}");

        return Command::SUCCESS;
    }

    private function monthRange(string $ym): array
    {
        $start = Carbon::createFromFormat('Y-m-d', $ym . '-01')->startOfDay();
        $end   = (clone $start)->endOfMonth()->endOfDay();
        return [$start->toDateTimeString(), $end->toDateTimeString()];
    }

    private function guessPrevMonth(): string
    {
        return now()->subMonth()->format('Y-m');
    }

    /**
     * Stok Masuk per bulan = Produksi (quantity) + Purchase FG (total_stok_masuk)
     */
    private function calcStokMasukMonthly(int $productId, string $start, string $end): int
    {
        $prod = (int) CatatanProduksi::where('product_id', $productId)
                    ->whereBetween('created_at', [$start, $end])
                    ->sum('quantity');

        $purch = (int) Purchase::where('kategori', 'finished_goods')
                    ->where('bahan_baku_id', $productId)
                    ->whereBetween('tanggal_kedatangan_barang', [substr($start,0,10), substr($end,0,10)]) // pakai field tanggal kedatangan
                    ->sum('total_stok_masuk'); // sudah pakai rumus baru (masuk - defect - retur)

        return $prod + $purch;
    }

    /**
     * Stok Keluar per bulan = total qty di HistorySale untuk SKU produk ini (berdasar created_at)
     * Catatan: struktur HistorySale: no_sku & qty disimpan sebagai JSON array yang sejajar
     */
    private function calcStokKeluarMonthly(?string $productSku, string $start, string $end): int
    {
        if (!$productSku) return 0;

        $sales = HistorySale::whereNotNull('no_sku')
                    ->whereBetween('created_at', [$start, $end])
                    ->select('no_sku', 'qty')
                    ->get();

        $total = 0;
        foreach ($sales as $s) {
            $skuArr = is_string($s->no_sku) ? json_decode($s->no_sku, true) : $s->no_sku;
            $qtyArr = is_string($s->qty)    ? json_decode($s->qty, true)    : $s->qty;

            if (!is_array($skuArr) || !is_array($qtyArr)) continue;
            $n = min(count($skuArr), count($qtyArr));

            for ($i = 0; $i < $n; $i++) {
                if (trim((string)$skuArr[$i]) === $productSku) {
                    $q = (int) ($qtyArr[$i] ?? 0);
                    if ($q > 0) $total += $q;
                }
            }
        }
        return $total;
    }
}
