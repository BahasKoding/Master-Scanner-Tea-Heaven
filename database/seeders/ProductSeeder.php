<?php

namespace Database\Seeders;

use App\Models\Product;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class ProductSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Hapus data lama jika ada - dengan aman (mengatasi foreign key constraint)
        DB::statement('SET FOREIGN_KEY_CHECKS=0;');
        Product::query()->delete();
        DB::statement('SET FOREIGN_KEY_CHECKS=1;');

        $this->command->info('🚀 Memulai konversi data produk dari file master_product_seeder.md...');

        // PROSES FILE MASTER PRODUCT
        $this->processProductFile();

        $totalRecords = Product::count();
        $this->command->info("✅ SEEDER SELESAI! Total {$totalRecords} produk berhasil dibuat dari data master product.");
    }

    /**
     * Process product file data from master_product_seeder.md
     * Format expected: Line Number, SKU, Product Name, Pack Code, Category, Pack Size
     */
    private function processProductFile()
    {
        $this->command->info('📂 Memproses data produk dari master_product_seeder.md...');

        $filePath = base_path('master_product_seeder.md');

        if (!file_exists($filePath)) {
            $this->command->error("File {$filePath} tidak ditemukan!");
            return;
        }

        // Baca semua baris dari file
        $lines = file($filePath, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);

        if (empty($lines)) {
            $this->command->error('File data produk kosong!');
            return;
        }

        $productsData = [];
        $batchSize = 50; // Batch size untuk insert
        $totalProcessed = 0;
        $skippedLines = 0;
        $skippedReasons = [];
        $failedInserts = [];
        $successfulInserts = 0;

        $this->command->info('Memproses ' . count($lines) . ' baris data produk...');

        foreach ($lines as $index => $line) {
            $lineNumber = $index + 1;

            try {
                // Parse each line dengan tab separator
                $columns = explode("\t", trim($line));

                // Debug: tampilkan progress setiap 100 baris
                if ($lineNumber % 100 == 0) {
                    $this->command->info("📍 Memproses baris ke-{$lineNumber} dari " . count($lines) . " total...");
                }

                // Format baru dari master_product_seeder.md: 
                // [0] = Line Number (ignore), [1] = SKU, [2] = Product Name, [3] = Pack Code, [4] = Category, [5] = Pack Size
                // Pastikan ada minimal 6 kolom dalam format baru
                if (count($columns) < 6) {
                    // Cek jika mungkin ini adalah baris dengan format lama (5 kolom)
                    if (count($columns) == 5) {
                        // Coba konversi dari format lama (category, sku, packcode, productname, packsize)
                        $lineNumber = $index + 1; // Line number dari indeks
                        $category = trim($columns[0]);
                        $sku = trim($columns[1]);
                        $packCode = trim($columns[2]);
                        $productName = trim($columns[3]);
                        $packSize = trim($columns[4]);
                        
                        $this->command->info("⚠️ Baris {$lineNumber} menggunakan format lama, mencoba konversi...");
                    } else {
                        $skippedLines++;
                        $reason = "Baris {$lineNumber} tidak valid (hanya " . count($columns) . " kolom): " . substr($line, 0, 100) . "...";
                        $skippedReasons[] = $reason;
                        continue;
                    }
                } else {
                    // Format baru: nomor baris, SKU, nama produk, pack code, kategori, pack size
                    $sku = trim($columns[1]);
                    $productName = trim($columns[2]);
                    $packCode = trim($columns[3]);
                    $category = trim($columns[4]);
                    $packSize = trim($columns[5]);
                }

                // Validasi SKU (harus ada dan tidak kosong)
                if (empty($sku)) {
                    $skippedLines++;
                    $reason = "Baris {$lineNumber} tidak memiliki SKU yang valid";
                    $skippedReasons[] = $reason;
                    continue;
                }

                // Validasi Product Name (harus ada dan tidak kosong)
                if (empty($productName)) {
                    $skippedLines++;
                    $reason = "Baris {$lineNumber} tidak memiliki nama produk yang valid";
                    $skippedReasons[] = $reason;
                    continue;
                }

                // Set default values untuk data yang kosong
                if (empty($category)) {
                    $category = 'UNKNOWN CATEGORY';
                }

                if (empty($packCode)) {
                    $packCode = '-';
                }

                if (empty($packSize)) {
                    $packSize = '-';
                }

                // Konversi category menjadi integer ID
                $categoryId = $this->getCategoryId($category);

                // Konversi label/pack size menjadi integer ID
                $labelId = $this->getLabelId($packSize);

                // Buat data untuk insert sesuai struktur tabel yang ada
                $productData = [
                    'category_product' => $categoryId,
                    'sku' => $sku,
                    'packaging' => $packCode,
                    'name_product' => $productName,
                    'label' => $labelId,
                    'created_at' => now(),
                    'updated_at' => now(),
                ];

                $productsData[] = $productData;
                $totalProcessed++;

                // Insert dalam batch untuk performa
                if (count($productsData) >= $batchSize) {
                    $this->insertBatch($productsData, $successfulInserts, $failedInserts, $lineNumber);
                    $productsData = []; // Reset array
                }
            } catch (\Exception $e) {
                $skippedLines++;
                $reason = "Error parsing baris {$lineNumber}: " . $e->getMessage() . " - Data: " . substr($line, 0, 100) . "...";
                $skippedReasons[] = $reason;
                $this->command->error($reason);
                continue;
            }
        }

        // Insert sisa data
        if (!empty($productsData)) {
            $this->insertBatch($productsData, $successfulInserts, $failedInserts, 'batch_terakhir');
        }

        // SUMMARY REPORT
        $this->command->info("📊 SUMMARY REPORT - PRODUCTS:");
        $this->command->info("✅ Total baris diproses: {$totalProcessed}");
        $this->command->info("✅ Berhasil diinsert: {$successfulInserts}");
        $this->command->info("⚠️  Baris dilewati: {$skippedLines}");
        $this->command->info("❌ Gagal diinsert: " . count($failedInserts));

        if (!empty($skippedReasons)) {
            $this->command->warn("📋 ALASAN DATA DILEWATI (10 pertama):");
            foreach (array_slice($skippedReasons, 0, 10) as $reason) {
                $this->command->warn("- " . $reason);
            }
            if (count($skippedReasons) > 10) {
                $this->command->warn("... dan " . (count($skippedReasons) - 10) . " alasan lainnya");
            }
        }

        if (!empty($failedInserts)) {
            $this->command->error("📋 DATA YANG GAGAL DIINSERT (5 pertama):");
            foreach (array_slice($failedInserts, 0, 5) as $failed) {
                $this->command->error("- Baris {$failed['line']}: SKU {$failed['sku']} - {$failed['name']} - Error: {$failed['error']}");
            }
            if (count($failedInserts) > 5) {
                $this->command->error("... dan " . (count($failedInserts) - 5) . " data lainnya yang gagal");
            }
        }

        $this->command->info("✅ Data produk selesai diproses!");
    }

    /**
     * Get category ID from category name
     */
    private function getCategoryId($categoryName)
    {
        // Mapping kategori ke ID (sesuaikan dengan data kategori yang ada di master_product_seeder.md)
        $categoryMapping = [
            'CLASSIC TEA COLLECTION' => 1,
            'PURE TISANE' => 2,
            'ARTISAN TEA' => 3,
            'JAPANESE TEA' => 4,
            'JAPANESE TEABAGS' => 4, // Masukkan ke kategori JAPANESE TEA
            'CHINESE TEA' => 5,
            'PURE POWDER' => 6,
            'SWEET POWDER' => 7,
            'LATTE POWDER' => 8,
            'EMPTY' => 9,
            'CRAFTED TEAS' => 10,
            'TEA WARE' => 10, // Masukkan ke kategori CRAFTED TEAS
            'UNKNOWN CATEGORY' => 11,
            '-' => 11, // Kategori yang kosong dianggap UNKNOWN
        ];

        // Debug
        if (!isset($categoryMapping[$categoryName])) {
            $this->command->warn("⚠️ Kategori tidak dikenal: '{$categoryName}' - menggunakan default category ID 11");
        }
        
        return $categoryMapping[$categoryName] ?? 11; // Default ke ID 11 untuk unknown
    }

    /**
     * Get label ID from label/pack size name
     */
    private function getLabelId($labelName)
    {
        // Mapping label ke ID (sesuai dengan Product::getLabelOptions() dan master_product_seeder.md)
        $labelMapping = [
            '-' => 0,
            'EXTRA SMALL PACK (15-100 GRAM)' => 1,
            'SMALL PACK (50-250 GRAM)' => 2,
            'MEDIUM PACK (500 GRAM)' => 3,
            'BIG PACK (1 Kg)' => 4,          // Format baru: 'Kg' bukan 'KILO'
            'TIN CANISTER SERIES' => 5,
            'REFILL PACK, SAMPLE & GIFT' => 6,
            'CRAFTED TEAS' => 7,
            'JAPANESE TEABAGS' => 8,
            'TEA WARE' => 9,
            'NON LABEL 500 GR-1000 GR' => 10,
            'HERBATA NON LABEL 500 GR-1000 GR' => 11,
        ];

        // Debug
        if (!isset($labelMapping[$labelName])) {
            $this->command->warn("⚠️ Label tidak dikenal: '{$labelName}' - menggunakan default label ID 0");
        }
        
        return $labelMapping[$labelName] ?? 0; // Default ke ID 0 untuk "-"
    }

    /**
     * Insert batch data with error handling
     */
    private function insertBatch(&$productsData, &$successfulInserts, &$failedInserts, $lineNumber)
    {
        try {
            Product::insert($productsData);
            $successfulInserts += count($productsData);
            $this->command->info("✅ Batch berhasil ({$productsData[0]['sku']} - {$productsData[count($productsData) - 1]['sku']}) = " . count($productsData) . " records");
        } catch (\Exception $e) {
            $this->command->error("❌ Gagal menyimpan batch pada sekitar baris {$lineNumber}: " . $e->getMessage());
            
            // Debug info - print the first failed record
            if (isset($productsData[0])) {
                $this->command->warn("⚠️ Contoh data batch yang gagal: " . json_encode($productsData[0], JSON_UNESCAPED_UNICODE));
            }

            // Coba insert satu per satu untuk mengetahui data mana yang bermasalah
            foreach ($productsData as $singleRecord) {
                try {
                    Product::create($singleRecord);
                    $successfulInserts++;
                } catch (\Exception $singleError) {
                    $failedInserts[] = [
                        'line' => $lineNumber,
                        'sku' => $singleRecord['sku'],
                        'name' => $singleRecord['name_product'],
                        'error' => $singleError->getMessage(),
                        'data' => json_encode($singleRecord, JSON_UNESCAPED_UNICODE)
                    ];
                }
            }
        }
    }
}
