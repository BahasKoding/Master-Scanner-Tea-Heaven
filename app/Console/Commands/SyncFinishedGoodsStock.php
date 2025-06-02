<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\FinishedGoods;
use App\Models\Product;
use Illuminate\Support\Facades\Log;

class SyncFinishedGoodsStock extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'finished-goods:sync-stock {--product-id= : Sync specific product ID} {--force : Force sync even if no changes detected}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Sync FinishedGoods stock values from CatatanProduksi and HistorySale';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('Starting FinishedGoods stock synchronization...');

        $productId = $this->option('product-id');
        $force = $this->option('force');

        try {
            if ($productId) {
                // Sync specific product
                $this->syncSingleProduct($productId, $force);
            } else {
                // Sync all products
                $this->syncAllProducts($force);
            }

            $this->info('FinishedGoods stock synchronization completed successfully!');
            return Command::SUCCESS;
        } catch (\Exception $e) {
            $this->error('Error during synchronization: ' . $e->getMessage());
            Log::error('FinishedGoods sync command failed: ' . $e->getMessage());
            return Command::FAILURE;
        }
    }

    /**
     * Sync stock for a single product
     */
    protected function syncSingleProduct($productId, $force = false)
    {
        $product = Product::find($productId);
        if (!$product) {
            $this->error("Product with ID {$productId} not found.");
            return;
        }

        $this->info("Syncing stock for product: {$product->sku} - {$product->name_product}");

        $finishedGoods = FinishedGoods::syncStockForProduct($productId);

        $this->line("  - Stok Awal: {$finishedGoods->stok_awal}");
        $this->line("  - Stok Masuk: {$finishedGoods->stok_masuk} (from CatatanProduksi)");
        $this->line("  - Stok Keluar: {$finishedGoods->stok_keluar} (from HistorySale)");
        $this->line("  - Defective: {$finishedGoods->defective}");
        $this->line("  - Live Stock: {$finishedGoods->live_stock}");

        $this->info("✓ Product {$product->sku} synced successfully");
    }

    /**
     * Sync stock for all products
     */
    protected function syncAllProducts($force = false)
    {
        // Get all products that have either CatatanProduksi or HistorySale records
        $productsWithProduction = Product::whereHas('catatanProduksi')->pluck('id')->toArray();

        // Get products that appear in HistorySale
        $productsWithSales = [];
        $historySales = \App\Models\HistorySale::whereNotNull('no_sku')->get();

        foreach ($historySales as $sale) {
            $skuArray = is_string($sale->no_sku) ? json_decode($sale->no_sku, true) : $sale->no_sku;
            if (is_array($skuArray)) {
                $products = Product::whereIn('sku', $skuArray)->pluck('id')->toArray();
                $productsWithSales = array_merge($productsWithSales, $products);
            }
        }

        // Get all existing FinishedGoods records
        $existingFinishedGoods = FinishedGoods::pluck('product_id')->toArray();

        // Combine all product IDs that need syncing
        $allProductIds = array_unique(array_merge($productsWithProduction, $productsWithSales, $existingFinishedGoods));

        if (empty($allProductIds)) {
            $this->info('No products found that need stock synchronization.');
            return;
        }

        $this->info('Found ' . count($allProductIds) . ' products to sync.');

        $progressBar = $this->output->createProgressBar(count($allProductIds));
        $progressBar->start();

        $syncedCount = 0;
        $errorCount = 0;

        foreach ($allProductIds as $productId) {
            try {
                $product = Product::find($productId);
                if (!$product) {
                    $this->newLine();
                    $this->warn("Product with ID {$productId} not found. Skipping...");
                    continue;
                }

                $finishedGoods = FinishedGoods::syncStockForProduct($productId);
                $syncedCount++;

                if ($this->output->isVerbose()) {
                    $this->newLine();
                    $this->line("Synced: {$product->sku} - Live Stock: {$finishedGoods->live_stock}");
                }
            } catch (\Exception $e) {
                $errorCount++;
                $this->newLine();
                $this->error("Error syncing product ID {$productId}: " . $e->getMessage());
                Log::error("Error syncing product ID {$productId}: " . $e->getMessage());
            }

            $progressBar->advance();
        }

        $progressBar->finish();
        $this->newLine(2);

        $this->info("Synchronization Summary:");
        $this->line("  - Total products processed: " . count($allProductIds));
        $this->line("  - Successfully synced: {$syncedCount}");
        $this->line("  - Errors: {$errorCount}");

        if ($errorCount > 0) {
            $this->warn("Some errors occurred during synchronization. Check the log files for details.");
        }
    }
}
