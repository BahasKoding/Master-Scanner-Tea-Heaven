<?php

namespace App\Console\Commands;

use App\Models\HistorySale;
use App\Models\HistorySaleDetail;
use App\Models\Product;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class MigrateHistorySalesData extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:migrate-history-sales';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Migrate history sales data from JSON format to relational model';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('Starting migration of history sales data...');

        $historySales = HistorySale::all();
        $skippedCount = 0;
        $migratedCount = 0;
        $errorCount = 0;

        $this->output->progressStart(count($historySales));

        foreach ($historySales as $historySale) {
            $this->output->progressAdvance();

            // Skip if already has details
            if ($historySale->details()->count() > 0) {
                $skippedCount++;
                continue;
            }

            $skuArray = $historySale->no_sku;
            $qtyArray = $historySale->qty;

            // Skip if no SKUs
            if (empty($skuArray) || !is_array($skuArray)) {
                $skippedCount++;
                continue;
            }

            try {
                DB::transaction(function () use ($historySale, $skuArray, $qtyArray) {
                    foreach ($skuArray as $index => $sku) {
                        if (!isset($qtyArray[$index])) {
                            continue;
                        }

                        $product = Product::where('sku', $sku)->first();
                        if (!$product) {
                            Log::warning("Product not found with SKU: {$sku} in HistorySale ID: {$historySale->id}");
                            continue;
                        }

                        HistorySaleDetail::create([
                            'history_sale_id' => $historySale->id,
                            'product_id' => $product->id,
                            'quantity' => $qtyArray[$index]
                        ]);
                    }
                });

                $migratedCount++;
            } catch (\Exception $e) {
                $errorCount++;
                Log::error("Error migrating HistorySale ID: {$historySale->id}. " . $e->getMessage());
            }
        }

        $this->output->progressFinish();

        $this->info("Migration completed!");
        $this->info("Migrated: {$migratedCount}");
        $this->info("Skipped: {$skippedCount}");
        $this->info("Errors: {$errorCount}");

        if ($errorCount > 0) {
            $this->warn("Some errors occurred during migration. Please check the log file.");
        }

        return Command::SUCCESS;
    }
}
