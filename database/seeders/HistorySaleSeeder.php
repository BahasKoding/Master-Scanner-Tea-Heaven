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

        $this->command->info('🚀 Memulai konversi data dari kedua file excel...');

        // 1. PROSES FILE EXCEL LAMA (file_excel.md) - 2780 data
        $this->processOldExcelFile();

        // 2. PROSES FILE EXCEL TERBARU (file_excel_terbaru.md) - 715 data dengan offset ID
        $this->processNewExcelFile();

        $totalRecords = HistorySale::count();
        $this->command->info("✅ SEEDER SELESAI! Total {$totalRecords} riwayat penjualan berhasil dibuat dari kedua file.");
        $this->command->info("📊 Kombinasi: file_excel.md (2780 data) + file_excel_terbaru.md (715 data)");
    }

    /**
     * Process file_excel.md (original file with 2780 records)
     */
    private function processOldExcelFile()
    {
        $this->command->info('📂 Memproses file_excel.md (2780 data)...');

        $filePath = base_path('file_excel.md');

        if (!file_exists($filePath)) {
            $this->command->error("File {$filePath} tidak ditemukan!");
            return;
        }

        // Baca semua baris dari file
        $lines = file($filePath, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);

        if (empty($lines)) {
            $this->command->error('File excel lama kosong!');
            return;
        }

        // Lewati header (baris pertama)
        array_shift($lines);

        $salesData = [];
        $batchSize = 100;
        $totalProcessed = 0;

        $this->command->info('Memproses ' . count($lines) . ' baris dari file excel lama...');

        foreach ($lines as $index => $line) {
            try {
                // Parse each line
                $columns = explode("\t", trim($line));

                // Pastikan ada minimal 6 kolom
                if (count($columns) < 6) {
                    $this->command->warn("Baris " . ($index + 2) . " tidak valid: " . $line);
                    continue;
                }

                $id = intval(trim($columns[0]));
                $trackingNumber = trim($columns[1]);
                $skuData = trim($columns[2]);
                $quantityData = trim($columns[3]);
                $createdAt = trim($columns[4]);
                $updatedAt = trim($columns[5]);

                // Parse multiple SKUs dan quantities
                $skus = array_map('trim', explode(',', $skuData));
                $quantities = array_map('intval', array_map('trim', explode(',', $quantityData)));

                // Pastikan jumlah SKU dan quantity sama
                if (count($skus) !== count($quantities)) {
                    $this->command->warn("Mismatch SKU dan quantity pada baris " . ($index + 2));
                    continue;
                }

                // Buat data untuk insert dengan format yang benar
                $salesData[] = [
                    'id' => $id, // Gunakan ID asli dari file
                    'no_resi' => $trackingNumber,
                    'no_sku' => json_encode($skus), // Simpan sebagai JSON
                    'qty' => json_encode($quantities), // Simpan sebagai JSON
                    'created_at' => Carbon::parse($createdAt),
                    'updated_at' => Carbon::parse($updatedAt),
                ];

                $totalProcessed++;

                // Insert dalam batch untuk performa
                if (count($salesData) >= $batchSize) {
                    try {
                        HistorySale::insert($salesData);
                        $this->command->info("✓ Batch " . ceil($totalProcessed / $batchSize) . " file lama ({$batchSize} records)");
                        $salesData = []; // Reset array
                    } catch (\Exception $e) {
                        $this->command->error("Gagal menyimpan batch file lama: " . $e->getMessage());
                        $salesData = [];
                    }
                }
            } catch (\Exception $e) {
                $this->command->warn("Error parsing baris " . ($index + 2) . " file lama: " . $e->getMessage());
                continue;
            }
        }

        // Insert sisa data
        if (!empty($salesData)) {
            try {
                HistorySale::insert($salesData);
                $this->command->info("✓ Batch terakhir file lama (" . count($salesData) . " records)");
            } catch (\Exception $e) {
                $this->command->error("Gagal menyimpan batch terakhir file lama: " . $e->getMessage());
            }
        }

        $this->command->info("✅ File excel lama selesai diproses! ({$totalProcessed} baris)");
    }

    /**
     * Process file_excel_terbaru.md (new file with 715 records)
     */
    private function processNewExcelFile()
    {
        $this->command->info('📂 Memproses file_excel_terbaru.md (715 data)...');

        $filePath = base_path('file_excel_terbaru.md');

        if (!file_exists($filePath)) {
            $this->command->error("File {$filePath} tidak ditemukan!");
            return;
        }

        // Baca semua baris dari file
        $lines = file($filePath, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);

        if (empty($lines)) {
            $this->command->error('File excel terbaru kosong!');
            return;
        }

        // Lewati header (baris pertama)
        array_shift($lines);

        $salesData = [];
        $batchSize = 100;
        $totalProcessed = 0;
        $idOffset = 10000; // Offset lebih kecil karena file terbaru hanya 715 data

        $this->command->info('Memproses ' . count($lines) . ' baris dari file excel terbaru...');

        foreach ($lines as $index => $line) {
            try {
                // Parse each line
                $columns = explode("\t", trim($line));

                // Pastikan ada minimal 6 kolom
                if (count($columns) < 6) {
                    $this->command->warn("Baris " . ($index + 2) . " tidak valid: " . $line);
                    continue;
                }

                $id = intval(trim($columns[0]));
                $trackingNumber = trim($columns[1]);
                $skuData = trim($columns[2]);
                $quantityData = trim($columns[3]);
                $createdAt = trim($columns[4]);
                $updatedAt = trim($columns[5]);

                // Parse multiple SKUs dan quantities
                $skus = array_map('trim', explode(',', $skuData));
                $quantities = array_map('intval', array_map('trim', explode(',', $quantityData)));

                // Pastikan jumlah SKU dan quantity sama
                if (count($skus) !== count($quantities)) {
                    $this->command->warn("Mismatch SKU dan quantity pada baris " . ($index + 2));
                    continue;
                }

                // Buat data untuk insert dengan format yang benar + offset ID
                $salesData[] = [
                    'id' => $idOffset + $id, // ID dengan offset untuk file baru
                    'no_resi' => $trackingNumber,
                    'no_sku' => json_encode($skus), // Simpan sebagai JSON
                    'qty' => json_encode($quantities), // Simpan sebagai JSON
                    'created_at' => Carbon::parse($createdAt),
                    'updated_at' => Carbon::parse($updatedAt),
                ];

                $totalProcessed++;

                // Insert dalam batch untuk performa
                if (count($salesData) >= $batchSize) {
                    try {
                        HistorySale::insert($salesData);
                        $this->command->info("✓ Batch " . ceil($totalProcessed / $batchSize) . " file terbaru ({$batchSize} records)");
                        $salesData = []; // Reset array
                    } catch (\Exception $e) {
                        $this->command->error("Gagal menyimpan batch file terbaru: " . $e->getMessage());
                        $salesData = [];
                    }
                }
            } catch (\Exception $e) {
                $this->command->warn("Error parsing baris " . ($index + 2) . " file terbaru: " . $e->getMessage());
                continue;
            }
        }

        // Insert sisa data
        if (!empty($salesData)) {
            try {
                HistorySale::insert($salesData);
                $this->command->info("✓ Batch terakhir file terbaru (" . count($salesData) . " records)");
            } catch (\Exception $e) {
                $this->command->error("Gagal menyimpan batch terakhir file terbaru: " . $e->getMessage());
            }
        }

        $this->command->info("✅ File excel terbaru selesai diproses! ({$totalProcessed} baris)");
    }
}
