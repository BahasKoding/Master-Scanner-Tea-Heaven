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

        $this->command->info('🚀 Memulai konversi data history sales terbaru...');

        // PROSES FILE EXCEL TERBARU (file_excel_14mei_11june.md) - Data Juni 2025
        $this->processLatestExcelFile();

        $totalRecords = HistorySale::count();
        $this->command->info("✅ SEEDER SELESAI! Total {$totalRecords} riwayat penjualan berhasil dibuat dari data terbaru.");
        $this->command->info("📊 Data history sales terbaru telah berhasil diproses.");
    }

    /**
     * Process latest Excel file data from file_excel_14mei_11june.md (data terbaru)
     */
    private function processLatestExcelFile()
    {
        $this->command->info('📂 Memproses data history sales terbaru dari file_excel_14mei_11june.md...');

        $filePath = base_path('file_excel_14mei_11june.md');

        if (!file_exists($filePath)) {
            $this->command->error("File {$filePath} tidak ditemukan!");
            return;
        }

        // Baca semua baris dari file
        $lines = file($filePath, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);

        if (empty($lines)) {
            $this->command->error('File data terbaru kosong!');
            return;
        }

        // Lewati header (baris pertama)
        array_shift($lines);

        $salesData = [];
        $batchSize = 100; // Increase batch size for better performance
        $totalProcessed = 0;
        $skippedLines = 0;
        $skippedReasons = [];
        $failedInserts = [];
        $successfulInserts = 0;

        $this->command->info('Memproses ' . count($lines) . ' baris data history sales terbaru...');

        foreach ($lines as $index => $line) {
            $lineNumber = $index + 2; // +2 karena index dimulai dari 0 dan ada header

            try {
                // Parse each line with tab separator
                $columns = explode("\t", trim($line));

                // Debug: tampilkan baris yang sedang diproses setiap 200 baris
                if ($lineNumber % 200 == 0) {
                    $this->command->info("📍 Memproses baris ke-{$lineNumber}...");
                }

                // Pastikan ada minimal 6 kolom: ID, No Resi, SKU, Jumlah, Created At, Updated At
                if (count($columns) < 6) {
                    $skippedLines++;
                    $reason = "Baris {$lineNumber} tidak valid (hanya " . count($columns) . " kolom): " . substr($line, 0, 100) . "...";
                    $skippedReasons[] = $reason;
                    $this->command->warn($reason);
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
                    $this->command->warn($reason);
                    continue;
                }

                // Validasi tracking number
                if (empty($trackingNumber)) {
                    $skippedLines++;
                    $reason = "Baris {$lineNumber} tidak memiliki no resi";
                    $skippedReasons[] = $reason;
                    $this->command->warn($reason);
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
                    $this->command->warn($reason);
                    continue;
                }

                // Jika jumlah quantity tidak sama dengan SKU, isi dengan 1
                if (count($skus) !== count($quantities)) {
                    $this->command->warn("Baris {$lineNumber}: Mismatch SKU (" . count($skus) . ") dan quantity (" . count($quantities) . ") - mengisi dengan qty 1");
                    $quantities = array_fill(0, count($skus), 1);
                }

                // Validasi dan parsing timestamp dengan error handling yang lebih robust
                try {
                    $createdAtParsed = Carbon::parse($createdAt);
                    $updatedAtParsed = Carbon::parse($updatedAt);
                } catch (\Exception $e) {
                    // Jika timestamp invalid, coba format alternatif
                    try {
                        $createdAtParsed = Carbon::createFromFormat('Y-m-d H:i:s', $createdAt);
                        $updatedAtParsed = Carbon::createFromFormat('Y-m-d H:i:s', $updatedAt);
                    } catch (\Exception $e2) {
                        // Jika masih gagal, gunakan timestamp sekarang
                        $this->command->warn("Baris {$lineNumber}: Invalid timestamp format, menggunakan timestamp sekarang");
                        $createdAtParsed = Carbon::now();
                        $updatedAtParsed = Carbon::now();
                    }
                }

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
                    try {
                        HistorySale::insert($salesData);
                        $successfulInserts += count($salesData);
                        $this->command->info("✅ Batch " . ceil($totalProcessed / $batchSize) . " berhasil ({$batchSize} records) - Total sukses: {$successfulInserts}");
                        $salesData = []; // Reset array
                    } catch (\Exception $e) {
                        $this->command->error("❌ Gagal menyimpan batch pada baris sekitar {$lineNumber}: " . $e->getMessage());

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
                                $this->command->error("❌ Gagal insert ID {$singleRecord['id']} - Resi: {$singleRecord['no_resi']} - Error: " . $singleError->getMessage());
                            }
                        }
                        $salesData = [];
                    }
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
            try {
                HistorySale::insert($salesData);
                $successfulInserts += count($salesData);
                $this->command->info("✅ Batch terakhir berhasil (" . count($salesData) . " records)");
            } catch (\Exception $e) {
                $this->command->error("❌ Gagal menyimpan batch terakhir: " . $e->getMessage());

                // Coba insert satu per satu untuk batch terakhir
                foreach ($salesData as $singleRecord) {
                    try {
                        HistorySale::create($singleRecord);
                        $successfulInserts++;
                    } catch (\Exception $singleError) {
                        $failedInserts[] = [
                            'line' => 'batch_terakhir',
                            'id' => $singleRecord['id'],
                            'no_resi' => $singleRecord['no_resi'],
                            'error' => $singleError->getMessage(),
                            'data' => json_encode($singleRecord, JSON_UNESCAPED_UNICODE)
                        ];
                        $this->command->error("❌ Gagal insert ID {$singleRecord['id']} - Resi: {$singleRecord['no_resi']} - Error: " . $singleError->getMessage());
                    }
                }
            }
        }

        // SUMMARY REPORT
        $this->command->info("📊 SUMMARY REPORT - HISTORY SALES TERBARU:");
        $this->command->info("✅ Total baris diproses: {$totalProcessed}");
        $this->command->info("✅ Berhasil diinsert: {$successfulInserts}");
        $this->command->info("⚠️  Baris dilewati: {$skippedLines}");
        $this->command->info("❌ Gagal diinsert: " . count($failedInserts));

        if (!empty($skippedReasons)) {
            $this->command->warn("📋 ALASAN DATA DILEWATI (20 pertama):");
            foreach (array_slice($skippedReasons, 0, 20) as $reason) {
                $this->command->warn("- " . $reason);
            }
            if (count($skippedReasons) > 20) {
                $this->command->warn("... dan " . (count($skippedReasons) - 20) . " alasan lainnya");
            }
        }

        if (!empty($failedInserts)) {
            $this->command->error("📋 DATA YANG GAGAL DIINSERT (10 pertama):");
            foreach (array_slice($failedInserts, 0, 10) as $failed) {
                $this->command->error("- Baris {$failed['line']}: ID {$failed['id']} - Resi: {$failed['no_resi']} - Error: {$failed['error']}");
            }
            if (count($failedInserts) > 10) {
                $this->command->error("... dan " . (count($failedInserts) - 10) . " data lainnya yang gagal");
            }
        }

        $this->command->info("✅ Data history sales terbaru selesai diproses!");
    }
}
