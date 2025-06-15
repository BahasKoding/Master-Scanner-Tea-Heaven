<?php

namespace Database\Seeders;

use App\Models\HistorySale;
use App\Models\Product;
use Illuminate\Database\Seeder;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;

class HistorySaleSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Hapus data lama jika ada - dengan aman (mengatasi foreign key constraint)
        DB::statement('SET FOREIGN_KEY_CHECKS=0;');
        HistorySale::query()->delete();
        DB::statement('SET FOREIGN_KEY_CHECKS=1;');

        $this->command->info('🚀 Memulai konversi data history sales dari file Excel terbaru...');

        // PROSES FILE EXCEL TERBARU (file_excel_14mei_14june.md) - Data Mei-Juni 2025
        $this->processExcelFile();

        $totalRecords = HistorySale::count();
        $this->command->info("✅ SEEDER SELESAI! Total {$totalRecords} riwayat penjualan berhasil dibuat dari data Excel terbaru.");
        $this->command->info("📊 Data history sales periode Mei-Juni 2025 telah berhasil diproses.");
    }

    /**
     * Process Excel file data from file_excel_14mei_14june.md
     */
    private function processExcelFile()
    {
        $this->command->info('📂 Memproses data history sales dari file_excel_14mei_14june.md...');

        $filePath = base_path('file_excel_14mei_14june.md');

        if (!file_exists($filePath)) {
            $this->command->error("File {$filePath} tidak ditemukan!");
            return;
        }

        // Baca semua baris dari file
        $lines = file($filePath, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);

        if (empty($lines)) {
            $this->command->error('File data Excel kosong!');
            return;
        }

        // Lewati header (baris pertama) 
        // Header: ID	No Resi	SKU	Jumlah	Dibuat Pada	Diperbarui Pada
        array_shift($lines);

        $salesData = [];
        $batchSize = 50; // Reduce batch size for better error handling
        $totalProcessed = 0;
        $skippedLines = 0;
        $skippedReasons = [];
        $failedInserts = [];
        $successfulInserts = 0;

        $this->command->info('Memproses ' . count($lines) . ' baris data history sales...');

        foreach ($lines as $index => $line) {
            $lineNumber = $index + 2; // +2 karena index dimulai dari 0 dan ada header

            try {
                // Parse each line dengan tab separator
                $columns = explode("\t", trim($line));

                // Debug: tampilkan progress setiap 100 baris
                if ($lineNumber % 100 == 0) {
                    $this->command->info("📍 Memproses baris ke-{$lineNumber} dari " . (count($lines) + 1) . " total...");
                }

                // Pastikan ada minimal 6 kolom: ID, No Resi, SKU, Jumlah, Dibuat Pada, Diperbarui Pada
                if (count($columns) < 6) {
                    $skippedLines++;
                    $reason = "Baris {$lineNumber} tidak valid (hanya " . count($columns) . " kolom): " . substr($line, 0, 100) . "...";
                    $skippedReasons[] = $reason;
                    continue;
                }

                $id = intval(trim($columns[0]));
                $trackingNumber = trim($columns[1]);
                $skuData = trim($columns[2]);
                $quantityData = trim($columns[3]);
                $createdAt = trim($columns[4]);
                $updatedAt = trim($columns[5]);

                // Skip jika ID kosong atau 0
                if (empty($id) || $id == 0) {
                    $skippedLines++;
                    $reason = "Baris {$lineNumber} memiliki ID tidak valid: '{$id}'";
                    $skippedReasons[] = $reason;
                    continue;
                }

                // Validasi tracking number
                if (empty($trackingNumber)) {
                    $skippedLines++;
                    $reason = "Baris {$lineNumber} tidak memiliki no resi";
                    $skippedReasons[] = $reason;
                    continue;
                }

                // Parse multiple SKUs dan quantities dengan pembersihan yang lebih baik
                $skus = array_filter(array_map('trim', explode(',', $skuData)), function ($sku) {
                    return !empty($sku) && strlen($sku) >= 2;
                });
                $quantities = array_filter(array_map('intval', array_map('trim', explode(',', $quantityData))), function ($qty) {
                    return $qty > 0;
                });

                // Jika tidak ada SKU valid, skip
                if (empty($skus)) {
                    $skippedLines++;
                    $reason = "Baris {$lineNumber} tidak memiliki SKU valid";
                    $skippedReasons[] = $reason;
                    continue;
                }

                // Jika jumlah quantity tidak sama dengan SKU, isi dengan 1
                if (count($skus) !== count($quantities)) {
                    $quantities = array_fill(0, count($skus), 1);
                }

                // Validasi dan parsing timestamp dengan multiple format
                $createdAtParsed = $this->parseDateTime($createdAt, $lineNumber, 'created_at');
                $updatedAtParsed = $this->parseDateTime($updatedAt, $lineNumber, 'updated_at');

                // Reset arrays to have sequential numeric keys
                $skus = array_values($skus);
                $quantities = array_values($quantities);

                // Buat data untuk insert dengan format yang benar
                $recordData = [
                    'id' => $id,
                    'no_resi' => $trackingNumber,
                    'no_sku' => json_encode($skus),
                    'qty' => json_encode($quantities),
                    'created_at' => $createdAtParsed,
                    'updated_at' => $updatedAtParsed,
                ];

                $salesData[] = $recordData;
                $totalProcessed++;

                // Insert dalam batch untuk performa
                if (count($salesData) >= $batchSize) {
                    $this->insertBatch($salesData, $successfulInserts, $failedInserts, $lineNumber);
                    $salesData = []; // Reset array
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
        if (!empty($salesData)) {
            $this->insertBatch($salesData, $successfulInserts, $failedInserts, 'batch_terakhir');
        }

        // SUMMARY REPORT
        $this->command->info("📊 SUMMARY REPORT - HISTORY SALES:");
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
                $this->command->error("- Baris {$failed['line']}: ID {$failed['id']} - Resi: {$failed['no_resi']} - Error: {$failed['error']}");
            }
            if (count($failedInserts) > 5) {
                $this->command->error("... dan " . (count($failedInserts) - 5) . " data lainnya yang gagal");
            }
        }

        $this->command->info("✅ Data history sales periode Mei-Juni 2025 selesai diproses!");
    }

    /**
     * Parse datetime with multiple format support
     */
    private function parseDateTime($dateTimeString, $lineNumber, $fieldName)
    {
        // Daftar format tanggal yang mungkin
        $formats = [
            'Y-m-d H:i:s',           // 2025-06-14 09:08:10
            'd/m/Y H:i:s',           // 14/06/2025 09:08:10  
            'm/d/Y H:i:s',           // 06/14/2025 09:08:10
            'Y-m-d',                 // 2025-06-14
            'd/m/Y',                 // 14/06/2025
            'm/d/Y',                 // 06/14/2025
            'j/n/Y H:i:s',           // 14/6/2025 09:08:10 (tanpa leading zero)
            'n/j/Y H:i:s',           // 6/14/2025 09:08:10 (tanpa leading zero)
        ];

        foreach ($formats as $format) {
            try {
                $parsed = Carbon::createFromFormat($format, $dateTimeString);
                if ($parsed) {
                    return $parsed;
                }
            } catch (\Exception $e) {
                // Lanjut ke format berikutnya
                continue;
            }
        }

        // Jika semua format gagal, coba Carbon::parse untuk auto-detection
        try {
            $parsed = Carbon::parse($dateTimeString);
            if ($parsed) {
                return $parsed;
            }
        } catch (\Exception $e) {
            // Jika masih gagal, gunakan timestamp sekarang dan beri warning
            $this->command->warn("Baris {$lineNumber}: Invalid {$fieldName} format '{$dateTimeString}', menggunakan timestamp sekarang");
            return Carbon::now();
        }

        // Fallback terakhir
        return Carbon::now();
    }

    /**
     * Insert batch data with error handling
     */
    private function insertBatch(&$salesData, &$successfulInserts, &$failedInserts, $lineNumber)
    {
        try {
            HistorySale::insert($salesData);
            $successfulInserts += count($salesData);
            $this->command->info("✅ Batch berhasil ({$salesData[0]['id']} - {$salesData[count($salesData) - 1]['id']}) = " . count($salesData) . " records");
        } catch (\Exception $e) {
            $this->command->error("❌ Gagal menyimpan batch pada sekitar baris {$lineNumber}: " . $e->getMessage());

            // Coba insert satu per satu untuk mengetahui data mana yang bermasalah
            foreach ($salesData as $singleRecord) {
                try {
                    HistorySale::create($singleRecord);
                    $successfulInserts++;
                } catch (\Exception $singleError) {
                    $failedInserts[] = [
                        'line' => $lineNumber,
                        'id' => $singleRecord['id'],
                        'no_resi' => $singleRecord['no_resi'],
                        'error' => $singleError->getMessage(),
                        'data' => json_encode($singleRecord, JSON_UNESCAPED_UNICODE)
                    ];
                }
            }
        }
    }
}
