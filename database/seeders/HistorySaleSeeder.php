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

        // PROSES FILE EXCEL TERBARU (file_juli_tgl1.md) - Data Mei-Juni 2025
        $this->processExcelFile();

        $totalRecords = HistorySale::count();
        $this->command->info("✅ SEEDER SELESAI! Total {$totalRecords} riwayat penjualan berhasil dibuat dari data Excel terbaru.");
        $this->command->info("📊 Data history sales periode Mei-Juni 2025 telah berhasil diproses.");
    }

    /**
     * Process Excel file data from file_juli_tgl1.md
     */
    private function processExcelFile()
    {
        $this->command->info('📂 Memproses data history sales dari file_juli_tgl1.md...');

        $filePath = base_path('file_juli_tgl_sd_19_agustus.md');

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

        // Lewati header (baris pertama) jika ada
        // Header: ID	No Resi	SKU	Jumlah	Dibuat Pada	Diperbarui Pada
        if (!empty($lines) && !is_numeric(trim(explode("\t", $lines[0])[0]))) {
            $this->command->info("📝 Mendeteksi header, melewati baris pertama: " . substr($lines[0], 0, 100));
        array_shift($lines);
        }

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
                // Parse each line dengan tab separator - dengan handling yang lebih robust
                $columns = explode("\t", $line); // Tidak trim dulu, karena bisa menghilangkan tab

                // Debug: tampilkan progress setiap 100 baris
                if ($lineNumber % 100 == 0) {
                    $this->command->info("📍 Memproses baris ke-{$lineNumber} dari " . (count($lines) + 1) . " total...");
                }

                // Pastikan ada minimal 6 kolom: ID, No Resi, SKU, Jumlah, Dibuat Pada, Diperbarui Pada
                if (count($columns) < 6) {
                    $skippedLines++;
                    $reason = "Baris {$lineNumber} tidak valid (hanya " . count($columns) . " kolom): " . substr($line, 0, 100) . "...";
                    $skippedReasons[] = $reason;

                    // Debug: tampilkan detail parsing untuk troubleshooting
                    $this->command->warn("Debug baris {$lineNumber}: '" . $line . "'");
                    $this->command->warn("Kolom yang diparsing: " . print_r($columns, true));
                    continue;
                }

                // Trim setiap kolom setelah dipisah
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

                // Validasi tracking number - lebih permisif untuk nama panjang
                if (empty($trackingNumber) || strlen($trackingNumber) < 2) {
                    $skippedLines++;
                    $reason = "Baris {$lineNumber} tidak memiliki no resi yang valid: '{$trackingNumber}'";
                    $skippedReasons[] = $reason;
                    continue;
                }

                // Parse multiple SKUs dan quantities dengan pembersihan yang lebih baik
                $skus = [];
                $quantities = [];

                // Parse SKU dengan handling yang lebih robust
                if (!empty($skuData)) {
                    $skuArray = explode(',', $skuData);
                    foreach ($skuArray as $sku) {
                        $cleanSku = trim($sku);
                        if (!empty($cleanSku) && strlen($cleanSku) >= 2) {
                            $skus[] = $cleanSku;
                        }
                    }
                }

                // Parse Quantities dengan handling yang lebih robust
                if (!empty($quantityData)) {
                    $qtyArray = explode(',', $quantityData);
                    foreach ($qtyArray as $qty) {
                        $cleanQty = intval(trim($qty));
                        if ($cleanQty > 0) {
                            $quantities[] = $cleanQty;
                        }
                    }
                }

                // Jika tidak ada SKU valid, skip
                if (empty($skus)) {
                    $skippedLines++;
                    $reason = "Baris {$lineNumber} tidak memiliki SKU valid. SKU Data: '{$skuData}'";
                    $skippedReasons[] = $reason;
                    continue;
                }

                // Jika jumlah quantity tidak sama dengan SKU, isi dengan 1
                if (count($skus) !== count($quantities)) {
                    $this->command->warn("Baris {$lineNumber}: Jumlah SKU (" . count($skus) . ") != Jumlah QTY (" . count($quantities) . "), menggunakan quantity = 1 untuk semua");
                    $quantities = array_fill(0, count($skus), 1);
                }

                // Validasi dan parsing timestamp dengan multiple format
                $createdAtParsed = $this->parseDateTime($createdAt, $lineNumber, 'created_at');
                $updatedAtParsed = $this->parseDateTime($updatedAt, $lineNumber, 'updated_at');

                // Reset arrays to have sequential numeric keys
                $skus = array_values($skus);
                $quantities = array_values($quantities);

                // Validasi final sebelum insert
                if (strlen($trackingNumber) > 255) {
                    $this->command->warn("Baris {$lineNumber}: Tracking number terlalu panjang, memotong ke 255 karakter");
                    $trackingNumber = substr($trackingNumber, 0, 255);
                }

                // Buat data untuk insert dengan format yang benar
                $recordData = [
                    'id' => $id,
                    'no_resi' => $trackingNumber,
                    'no_sku' => json_encode($skus, JSON_UNESCAPED_UNICODE),
                    'qty' => json_encode($quantities),
                    'created_at' => $createdAtParsed,
                    'updated_at' => $updatedAtParsed,
                ];

                // Debug untuk ID tertentu (seperti ID 138)
                if ($id == 138 || $id == 2) {
                    $this->command->info("🔍 Debug ID {$id}:");
                    $this->command->info("- Tracking: '{$trackingNumber}'");
                    $this->command->info("- SKUs: " . implode(', ', $skus));
                    $this->command->info("- Quantities: " . implode(', ', $quantities));
                    $this->command->info("- Created: {$createdAtParsed}");
                }

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

        // Cek database untuk memastikan ID 138 dan 2 berhasil
        $this->command->info("🔍 VERIFIKASI ID KRITIS:");
        $checkIds = [138, 2, 1];
        foreach ($checkIds as $checkId) {
            $record = \App\Models\HistorySale::find($checkId);
            if ($record) {
                $this->command->info("✅ ID {$checkId}: BERHASIL - Resi: {$record->no_resi}");
            } else {
                $this->command->error("❌ ID {$checkId}: TIDAK DITEMUKAN");
            }
        }

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
            // Coba insert batch dulu
            HistorySale::insert($salesData);
            $successfulInserts += count($salesData);
            $this->command->info("✅ Batch berhasil ({$salesData[0]['id']} - {$salesData[count($salesData) - 1]['id']}) = " . count($salesData) . " records");
        } catch (\Exception $e) {
            $this->command->error("❌ Gagal menyimpan batch pada sekitar baris {$lineNumber}: " . $e->getMessage());

            // Coba insert satu per satu untuk mengetahui data mana yang bermasalah
            $this->command->info("🔄 Mencoba insert satu per satu untuk mengidentifikasi masalah...");

            foreach ($salesData as $index => $singleRecord) {
                try {
                    // Cek apakah record dengan ID ini sudah ada
                    $existingRecord = HistorySale::find($singleRecord['id']);
                    if ($existingRecord) {
                        $this->command->warn("⚠️ ID {$singleRecord['id']} sudah ada, melakukan update...");
                        $existingRecord->update($singleRecord);
                    } else {
                    HistorySale::create($singleRecord);
                    }

                    $successfulInserts++;
                    $this->command->info("✅ Record ID {$singleRecord['id']} berhasil diproses");
                } catch (\Exception $singleError) {
                    $errorDetails = [
                        'line' => $lineNumber,
                        'id' => $singleRecord['id'],
                        'no_resi' => $singleRecord['no_resi'],
                        'error' => $singleError->getMessage(),
                        'data' => json_encode($singleRecord, JSON_UNESCAPED_UNICODE)
                    ];

                    $failedInserts[] = $errorDetails;

                    $this->command->error("❌ Gagal insert ID {$singleRecord['id']}: " . $singleError->getMessage());
                    $this->command->error("   Data: " . substr($errorDetails['data'], 0, 200) . "...");
                }
            }
        }
    }
}
