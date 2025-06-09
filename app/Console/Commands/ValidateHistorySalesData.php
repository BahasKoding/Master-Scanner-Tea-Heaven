<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\HistorySale;
use App\Models\Product;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class ValidateHistorySalesData extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'sales:validate-data {--fix : Otomatis perbaiki data yang bermasalah} {--dry-run : Hanya tampilkan laporan tanpa mengubah data}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Validasi dan perbaiki data HistorySale yang bermasalah dengan table Product';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $isDryRun = $this->option('dry-run');
        $shouldFix = $this->option('fix');

        $this->info('🔍 Memulai validasi data HistorySales...');
        $this->newLine();

        $validationResults = $this->validateSalesData();
        $this->displayValidationResults($validationResults);

        if (!$isDryRun && $shouldFix && $validationResults['has_issues']) {
            $this->info('🔧 Memulai perbaikan data...');
            $this->fixInvalidData($validationResults);
        } elseif ($validationResults['has_issues'] && !$shouldFix) {
            $this->newLine();
            $this->warn('💡 Untuk memperbaiki data otomatis, jalankan: php artisan sales:validate-data --fix');
            $this->warn('💡 Untuk preview saja: php artisan sales:validate-data --dry-run');
        }

        return Command::SUCCESS;
    }

    private function validateSalesData()
    {
        $this->info('📊 Menganalisis data HistorySales...');

        $totalSales = HistorySale::count();
        $validSales = 0;
        $invalidSkuSales = [];
        $corruptedDataSales = [];
        $arrayMismatchSales = [];

        $progressBar = $this->output->createProgressBar($totalSales);
        $progressBar->start();

        HistorySale::chunk(100, function ($sales) use (&$validSales, &$invalidSkuSales, &$corruptedDataSales, &$arrayMismatchSales, $progressBar) {
            foreach ($sales as $sale) {
                $progressBar->advance();

                $issues = $this->validateSingleSale($sale);

                if (empty($issues)) {
                    $validSales++;
                } else {
                    foreach ($issues as $issue) {
                        if ($issue['type'] === 'invalid_sku') {
                            $invalidSkuSales[] = $issue;
                        } elseif ($issue['type'] === 'corrupted_data') {
                            $corruptedDataSales[] = $issue;
                        } elseif ($issue['type'] === 'array_mismatch') {
                            $arrayMismatchSales[] = $issue;
                        }
                    }
                }
            }
        });

        $progressBar->finish();
        $this->newLine(2);

        return [
            'total_sales' => $totalSales,
            'valid_sales' => $validSales,
            'invalid_sku_sales' => $invalidSkuSales,
            'corrupted_data_sales' => $corruptedDataSales,
            'array_mismatch_sales' => $arrayMismatchSales,
            'has_issues' => !empty($invalidSkuSales) || !empty($corruptedDataSales) || !empty($arrayMismatchSales)
        ];
    }

    private function validateSingleSale($sale)
    {
        $issues = [];

        try {
            // Decode JSON data
            $skuArray = is_string($sale->no_sku) ? json_decode($sale->no_sku, true) : $sale->no_sku;
            $qtyArray = is_string($sale->qty) ? json_decode($sale->qty, true) : $sale->qty;

            // Check if data can be decoded properly
            if (!is_array($skuArray) || !is_array($qtyArray)) {
                $issues[] = [
                    'type' => 'corrupted_data',
                    'sale_id' => $sale->id,
                    'no_resi' => $sale->no_resi,
                    'message' => 'Data SKU atau QTY tidak dapat di-decode sebagai array',
                    'no_sku_raw' => $sale->no_sku,
                    'qty_raw' => $sale->qty
                ];
                return $issues;
            }

            // Check if arrays have same length
            if (count($skuArray) !== count($qtyArray)) {
                $issues[] = [
                    'type' => 'array_mismatch',
                    'sale_id' => $sale->id,
                    'no_resi' => $sale->no_resi,
                    'message' => 'Jumlah SKU dan QTY tidak sama',
                    'sku_count' => count($skuArray),
                    'qty_count' => count($qtyArray),
                    'skus' => $skuArray,
                    'qtys' => $qtyArray
                ];
            }

            // Check if SKUs exist in products table
            foreach ($skuArray as $index => $sku) {
                if (!empty(trim($sku))) {
                    $product = Product::where('sku', trim($sku))->first();
                    if (!$product) {
                        $issues[] = [
                            'type' => 'invalid_sku',
                            'sale_id' => $sale->id,
                            'no_resi' => $sale->no_resi,
                            'sku' => $sku,
                            'sku_index' => $index,
                            'message' => "SKU '$sku' tidak ditemukan di tabel products"
                        ];
                    }
                }
            }
        } catch (\Exception $e) {
            $issues[] = [
                'type' => 'corrupted_data',
                'sale_id' => $sale->id,
                'no_resi' => $sale->no_resi,
                'message' => 'Error saat memvalidasi: ' . $e->getMessage(),
                'error' => $e->getMessage()
            ];
        }

        return $issues;
    }

    private function displayValidationResults($results)
    {
        $this->info('📋 HASIL VALIDASI DATA HISTORY SALES');
        $this->info('=' . str_repeat('=', 50));

        $this->table(
            ['Metrik', 'Jumlah', 'Persentase'],
            [
                ['Total Sales', number_format($results['total_sales']), '100%'],
                ['Sales Valid', number_format($results['valid_sales']), number_format(($results['valid_sales'] / max($results['total_sales'], 1)) * 100, 1) . '%'],
                ['Sales dengan SKU Invalid', number_format(count($results['invalid_sku_sales'])), number_format((count($results['invalid_sku_sales']) / max($results['total_sales'], 1)) * 100, 1) . '%'],
                ['Sales dengan Data Rusak', number_format(count($results['corrupted_data_sales'])), number_format((count($results['corrupted_data_sales']) / max($results['total_sales'], 1)) * 100, 1) . '%'],
                ['Sales dengan Array Mismatch', number_format(count($results['array_mismatch_sales'])), number_format((count($results['array_mismatch_sales']) / max($results['total_sales'], 1)) * 100, 1) . '%'],
            ]
        );

        if (!empty($results['invalid_sku_sales'])) {
            $this->newLine();
            $this->error('🚨 SKU INVALID DITEMUKAN:');
            $invalidSkuTable = [];
            foreach (array_slice($results['invalid_sku_sales'], 0, 10) as $issue) {
                $invalidSkuTable[] = [
                    $issue['sale_id'],
                    $issue['no_resi'],
                    $issue['sku'],
                    'SKU tidak ada di tabel products'
                ];
            }
            $this->table(['Sale ID', 'No Resi', 'SKU Invalid', 'Masalah'], $invalidSkuTable);

            if (count($results['invalid_sku_sales']) > 10) {
                $this->warn('... dan ' . (count($results['invalid_sku_sales']) - 10) . ' SKU invalid lainnya');
            }
        }

        if (!empty($results['corrupted_data_sales'])) {
            $this->newLine();
            $this->error('🔴 DATA RUSAK DITEMUKAN:');
            $corruptedTable = [];
            foreach (array_slice($results['corrupted_data_sales'], 0, 5) as $issue) {
                $corruptedTable[] = [
                    $issue['sale_id'],
                    $issue['no_resi'],
                    substr($issue['message'], 0, 50) . '...'
                ];
            }
            $this->table(['Sale ID', 'No Resi', 'Masalah'], $corruptedTable);
        }

        if (!empty($results['array_mismatch_sales'])) {
            $this->newLine();
            $this->error('⚠️ ARRAY MISMATCH DITEMUKAN:');
            $mismatchTable = [];
            foreach (array_slice($results['array_mismatch_sales'], 0, 5) as $issue) {
                $mismatchTable[] = [
                    $issue['sale_id'],
                    $issue['no_resi'],
                    $issue['sku_count'],
                    $issue['qty_count']
                ];
            }
            $this->table(['Sale ID', 'No Resi', 'SKU Count', 'QTY Count'], $mismatchTable);
        }
    }

    private function fixInvalidData($results)
    {
        DB::transaction(function () use ($results) {
            $fixedCount = 0;
            $deletedCount = 0;

            // Fix invalid SKUs by removing them
            foreach ($results['invalid_sku_sales'] as $issue) {
                $sale = HistorySale::find($issue['sale_id']);
                if ($sale) {
                    $this->fixInvalidSku($sale, $issue);
                    $fixedCount++;
                }
            }

            // Handle corrupted data by deleting records
            foreach ($results['corrupted_data_sales'] as $issue) {
                $sale = HistorySale::find($issue['sale_id']);
                if ($sale) {
                    $this->warn("Menghapus sale ID {$issue['sale_id']} (data rusak tidak dapat diperbaiki)");
                    $sale->delete();
                    $deletedCount++;
                }
            }

            // Fix array mismatch by truncating to shorter array
            foreach ($results['array_mismatch_sales'] as $issue) {
                $sale = HistorySale::find($issue['sale_id']);
                if ($sale) {
                    $this->fixArrayMismatch($sale, $issue);
                    $fixedCount++;
                }
            }

            $this->info("✅ Perbaikan selesai:");
            $this->info("   - $fixedCount sales diperbaiki");
            $this->info("   - $deletedCount sales dihapus (data rusak)");

            // Log activity
            Log::info("Sales data validation and fix completed", [
                'fixed_count' => $fixedCount,
                'deleted_count' => $deletedCount,
                'total_issues' => count($results['invalid_sku_sales']) + count($results['corrupted_data_sales']) + count($results['array_mismatch_sales'])
            ]);
        });
    }

    private function fixInvalidSku($sale, $issue)
    {
        $skuArray = is_string($sale->no_sku) ? json_decode($sale->no_sku, true) : $sale->no_sku;
        $qtyArray = is_string($sale->qty) ? json_decode($sale->qty, true) : $sale->qty;

        // Remove invalid SKU and its corresponding quantity
        unset($skuArray[$issue['sku_index']]);
        unset($qtyArray[$issue['sku_index']]);

        // Reindex arrays
        $skuArray = array_values($skuArray);
        $qtyArray = array_values($qtyArray);

        if (empty($skuArray)) {
            $this->warn("Sale ID {$sale->id} tidak memiliki SKU valid, dihapus");
            $sale->delete();
        } else {
            $sale->update([
                'no_sku' => $skuArray,
                'qty' => $qtyArray
            ]);
            $this->info("Sale ID {$sale->id}: SKU invalid '{$issue['sku']}' dihapus");
        }
    }

    private function fixArrayMismatch($sale, $issue)
    {
        $skuArray = $issue['skus'];
        $qtyArray = $issue['qtys'];

        // Truncate to shorter array length
        $minLength = min(count($skuArray), count($qtyArray));
        $skuArray = array_slice($skuArray, 0, $minLength);
        $qtyArray = array_slice($qtyArray, 0, $minLength);

        $sale->update([
            'no_sku' => $skuArray,
            'qty' => $qtyArray
        ]);

        $this->info("Sale ID {$sale->id}: Array mismatch diperbaiki (dipotong ke $minLength items)");
    }
}
