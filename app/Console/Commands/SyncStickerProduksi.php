<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Sticker;
use App\Models\CatatanProduksi;
use App\Models\Product;

class SyncStickerProduksi extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'sticker:sync-produksi {--dry-run : Run without making changes}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Sync sticker produksi data from catatan produksi records';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $isDryRun = $this->option('dry-run');

        if ($isDryRun) {
            $this->info('Running in DRY RUN mode - no changes will be made');
        }

        $this->info('Starting sticker produksi sync...');

        // Get all products that have catatan produksi
        $productsWithProduksi = CatatanProduksi::select('product_id')
            ->distinct()
            ->get()
            ->pluck('product_id');

        $this->info("Found {$productsWithProduksi->count()} products with production records");

        $synced = 0;
        $created = 0;
        $skipped = 0;

        foreach ($productsWithProduksi as $productId) {
            $product = Product::find($productId);

            if (!$product) {
                $this->warn("Product ID {$productId} not found, skipping...");
                $skipped++;
                continue;
            }

            // Check if product is eligible for stickers
            if (!in_array($product->label, [1, 5, 10])) {
                $this->info("Product {$product->sku} (label: {$product->label}) is not eligible for stickers, skipping...");
                $skipped++;
                continue;
            }

            // Calculate total production for this product
            $totalProduksi = CatatanProduksi::where('product_id', $productId)->sum('quantity');

            // Check if sticker exists
            $sticker = Sticker::where('product_id', $productId)->first();

            if (!$sticker) {
                $this->info("Creating new sticker for product {$product->sku}...");

                if (!$isDryRun) {
                    $sticker = Sticker::ensureStickerExists($productId);
                    $created++;
                } else {
                    $this->line("  [DRY RUN] Would create sticker with produksi: {$totalProduksi}");
                    $created++;
                }
            } else {
                $this->info("Updating sticker for product {$product->sku}...");
                $this->line("  Current produksi: {$sticker->produksi}");
                $this->line("  New produksi: {$totalProduksi}");

                if (!$isDryRun) {
                    $sticker->update(['produksi' => $totalProduksi]);
                    $synced++;
                } else {
                    $this->line("  [DRY RUN] Would update produksi to: {$totalProduksi}");
                    $synced++;
                }
            }
        }

        $this->info("\nSync completed!");
        $this->table(
            ['Action', 'Count'],
            [
                ['Stickers Created', $created],
                ['Stickers Updated', $synced],
                ['Products Skipped', $skipped],
                ['Total Processed', $created + $synced + $skipped]
            ]
        );

        if ($isDryRun) {
            $this->warn('This was a DRY RUN - no actual changes were made');
            $this->info('Run without --dry-run flag to apply changes');
        }

        return 0;
    }
}
