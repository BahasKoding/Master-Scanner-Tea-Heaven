<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Sticker;
use App\Models\PurchaseSticker;

class SyncStickerStock extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'sticker:sync-stock {--dry-run : Run without making changes}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Sync sticker stock with purchase stickers data';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $dryRun = $this->option('dry-run');

        if ($dryRun) {
            $this->info('DRY RUN MODE - No changes will be made');
        }

        $stickers = Sticker::with(['product', 'purchaseStickers'])->get();

        $this->info("Processing {$stickers->count()} stickers...");

        $updated = 0;

        foreach ($stickers as $sticker) {
            // Calculate dynamic values
            $oldStokMasuk = $sticker->stok_masuk;
            $oldSisa = $sticker->sisa;
            $oldStatus = $sticker->status;

            $newStokMasuk = PurchaseSticker::where('product_id', $sticker->product_id)
                ->where('ukuran_stiker', $sticker->ukuran)
                ->sum('stok_masuk');

            $newSisa = $sticker->stok_awal + $newStokMasuk + $sticker->produksi - $sticker->defect;

            $newStatus = $newSisa < 30 ? 'need_order' : 'available';

            if ($oldStokMasuk != $newStokMasuk || $oldSisa != $newSisa || $oldStatus != $newStatus) {
                $this->line("Updating sticker ID {$sticker->id} ({$sticker->product->name_product} - {$sticker->ukuran}):");
                $this->line("  Stok Masuk: {$oldStokMasuk} → {$newStokMasuk}");
                $this->line("  Sisa: {$oldSisa} → {$newSisa}");
                $this->line("  Status: {$oldStatus} → {$newStatus}");

                if (!$dryRun) {
                    $sticker->stok_masuk = $newStokMasuk;
                    $sticker->sisa = $newSisa;
                    $sticker->status = $newStatus;
                    $sticker->save();
                }

                $updated++;
            }
        }

        if ($dryRun) {
            $this->info("DRY RUN COMPLETE: {$updated} stickers would be updated");
        } else {
            $this->info("SYNC COMPLETE: {$updated} stickers updated");
        }
    }
}
