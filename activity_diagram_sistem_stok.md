# Activity Diagram Sistem Stok - Master Scanner Tea Heaven (Versi Optimasi)

## 0. Struktur Database Optimasi

```
+-------------------+       +----------------------+       +-------------------+
|      Product      |       |    HistorySale       |       |   CatatanProduksi |
+-------------------+       +----------------------+       +-------------------+
| id                |       | id                   |       | id                |
| sku               |       | no_resi              |       | product_id  ------+----> Referensi ke Product
| name_product      |       | no_sku (deprecated)  |       | quantity          |
| packaging         |       | qty (deprecated)     |       | ...               |
| ...               |       | ...                  |       +-------------------+
+-------------------+       +----------------------+
       ^  ^                        ^  |
       |  |                        |  |
       |  |     +-----------------+|  |
       |  +-----|  FinishedGoods  ||  |
       |        +-----------------+|  |
       |        | id              ||  |
       |        | id_product  ----++  |
       |        | stok_masuk      |   |
       |        | stok_keluar     |   |
       |        | ...             |   |
       |        +-----------------+   |
       |                              V
       |         +---------------------+
       +---------|  HistorySaleDetail  |
                 +---------------------+
                 | id                  |
                 | history_sale_id  ---+----> Referensi ke HistorySale
                 | product_id  --------+----> Referensi ke Product
                 | quantity            |
                 +---------------------+
```

## 1. Alur Stok Masuk (Produksi)

```
[Start] --> "Pengguna membuat catatan produksi baru"
        --> "Observer CatatanProduksiObserver menangkap event created"
        --> "StockService.updateStockFromProduction() dipanggil"
        --> "Cek apakah produk sudah ada di FinishedGoods"
        --> [Decision] "Produk ada di FinishedGoods?"
            --> [Ya] "Tambahkan quantity ke stok_masuk"
            --> "Hitung ulang live_stock"
            --> "Simpan perubahan ke database"
            --> [End]
            
            [Decision] --> [Tidak] "Buat record FinishedGoods baru"
            --> "Set nilai awal (stok_awal=0, stok_keluar=0, defective=0)"
            --> "Set stok_masuk = quantity produksi"
            --> "Hitung live_stock"
            --> "Simpan ke database"
            --> [End]
```

## 2. Alur Stok Keluar (Penjualan)

```
[Start] --> "Pengguna mencatat penjualan (Scanner)"
        --> "Observer HistorySaleObserver menangkap event created"
        --> "StockService.updateStockFromSales() dipanggil"
        --> [Decision] "Menggunakan format lama (JSON) atau format baru (relasi)?"
            --> [Format Lama] "Untuk setiap SKU dalam penjualan"
                --> "Cari product_id berdasarkan SKU"
                --> "Buat record HistorySaleDetail (migrasi ke format baru)"
                --> "Panggil updateStockForProduct()"
                --> [End]
                
            [Decision] --> [Format Baru] "Untuk setiap detail dalam historySale.details"
                --> "Panggil updateStockForProduct()"
                --> [End]

[Subgraph: updateStockForProduct]
[Start] --> "Cari data FinishedGoods untuk product_id"
        --> [Decision] "FinishedGoods ditemukan?"
            --> [Tidak] "Tampilkan error dan batalkan transaksi"
            --> [End Error]
            
            [Decision] --> [Ya] "Cek apakah stok mencukupi (live_stock >= quantity)"
            --> [Decision] "Stok mencukupi?"
                --> [Tidak] "Tampilkan error dan batalkan transaksi"
                --> [End Error]
                
                [Decision] --> [Ya] "Tambahkan quantity ke stok_keluar"
                --> "Hitung ulang live_stock"
                --> "Simpan perubahan ke database"
                --> [End]
```

## 3. Alur Update Stok Defective

```
[Start] --> "Pengguna input jumlah barang defective"
        --> "Kirim request ke FinishedGoodsController.updateDefective()"
        --> "StockService.updateDefectiveStock() dipanggil"
        --> "Cari data FinishedGoods untuk product_id tersebut"
        --> "Update nilai defective"
        --> "Hitung ulang live_stock"
        --> "Simpan perubahan ke database"
        --> [End]
```

## 4. Alur Verifikasi Konsistensi Data (Scheduled Job)

```
[Start] --> "VerifyStockConsistencyJob dijalankan otomatis oleh scheduler (01:00 AM)"
        --> "Ambil semua data FinishedGoods"
        --> "Untuk setiap FinishedGoods"
        --> "Hitung total stok_masuk dari CatatanProduksi"
        --> "Hitung total stok_keluar dari HistorySaleDetail (menggunakan relasi)"
            --> [Decision] "Data di HistorySaleDetail tersedia?"
                --> [Ya] "Gunakan data dari HistorySaleDetail"
                --> [Lanjutkan verifikasi]
                
                [Decision] --> [Tidak] "Hitung dari format JSON lama (fallback)"
                --> [Lanjutkan verifikasi]
                
        --> "Bandingkan nilai di database dengan nilai hasil perhitungan"
        --> [Decision] "Ada perbedaan?"
            --> [Ya] "Catat warning di log"
            --> "Opsional: Auto-koreksi nilai di database (jika diaktifkan)"
            --> "Lanjut ke FinishedGoods berikutnya"
            --> [End]
            
            [Decision] --> [Tidak] "Lanjut ke FinishedGoods berikutnya"
            --> [End]
```

## 5. Alur Migrasi Data Lama ke Format Baru

```
[Start] --> "Jalankan command: php artisan app:migrate-history-sales"
        --> "Ambil semua HistorySale"
        --> "Untuk setiap HistorySale"
            --> [Decision] "Sudah memiliki detail?"
                --> [Ya] "Skip (sudah dimigrasi)"
                --> [Lanjut ke record berikutnya]
                
                [Decision] --> [Tidak] "Untuk setiap SKU dan quantity di JSON"
                    --> "Cari product_id berdasarkan SKU"
                    --> "Buat HistorySaleDetail baru"
                    --> "Lanjut ke SKU berikutnya (jika ada)"
            --> "Lanjut ke HistorySale berikutnya"
        --> "Tampilkan ringkasan proses migrasi"
        --> [End]
```

## 6. Alur Perubahan Catatan Produksi

```
[Start] --> "Pengguna mengubah data catatan produksi"
        --> "Observer CatatanProduksiObserver menangkap event updated"
        --> [Decision] "Nilai quantity berubah?"
            --> [Tidak] "Tidak ada aksi untuk stok"
            --> [End No Action]
            
            [Decision] --> [Ya] "StockService.updateStockFromProductionChange() dipanggil"
            --> "Hitung selisih quantity baru dan lama"
            --> "Update stok_masuk di FinishedGoods"
            --> "Hitung ulang live_stock"
            --> "Simpan perubahan ke database"
            --> [End]
```

## 7. Alur Penghapusan Catatan Produksi

```
[Start] --> "Pengguna menghapus catatan produksi"
        --> "Observer CatatanProduksiObserver menangkap event deleted"
        --> "StockService.removeStockFromProduction() dipanggil"
        --> "Kurangi stok_masuk sesuai quantity yang dihapus"
        --> "Hitung ulang live_stock"
        --> "Simpan perubahan ke database"
        --> [End]
```

## 8. Alur Perubahan Data Penjualan

```
[Start] --> "Pengguna mengubah data penjualan"
        --> "Observer HistorySaleObserver menangkap event updated"
        --> [Decision] "Nilai no_sku atau qty berubah?"
            --> [Tidak] "Tidak ada aksi untuk stok"
            --> [End No Action]
            
            [Decision] --> [Ya] "StockService.updateStockFromSalesChange() dipanggil"
            --> "Kembalikan stok dari data lama (restoreStockFromSales dengan isUpdate=true)"
            --> "Tambahkan stok dari data baru (updateStockFromSales)"
            --> [End]
```

## 9. Alur Penghapusan Data Penjualan

```
[Start] --> "Pengguna menghapus data penjualan"
        --> "Observer HistorySaleObserver menangkap event deleted"
        --> "StockService.restoreStockFromSales() dipanggil"
        --> "Untuk setiap SKU dalam penjualan yang dihapus"
        --> "Cari product_id berdasarkan SKU"
        --> "Cari data FinishedGoods untuk product_id tersebut"
        --> "Kurangi stok_keluar sesuai quantity"
        --> "Hitung ulang live_stock"
        --> "Simpan perubahan ke database"
        --> "Lanjut ke SKU berikutnya (jika ada)"
        --> [End]
```

## Integrasi Sistem

Untuk mengaktifkan sistem ini, pastikan komponen-komponen berikut telah terpasang dengan benar:

1. **Migrasi Database**
   - Jalankan migrasi untuk membuat tabel `history_sale_details`
   - Jalankan command `php artisan app:migrate-history-sales` untuk memigrasikan data lama

2. **Service Provider**
   - `StockServiceProvider` harus didaftarkan di `config/app.php`
   - Provider ini akan mendaftarkan observer dan scheduler

3. **Observer**
   - `CatatanProduksiObserver` untuk model `CatatanProduksi`
   - `HistorySaleObserver` untuk model `HistorySale`

4. **Service Layer**
   - `StockService` yang berisi logika bisnis terkait stok

5. **Jobs**
   - `VerifyStockConsistencyJob` untuk verifikasi otomatis

6. **Model**
   - `HistorySaleDetail` menghubungkan `HistorySale` dengan `Product`
   - Relasi di `HistorySale` dan `Product` untuk mengakses data terkait

7. **Routes & Controllers**
   - Route untuk mengupdate stok defective
   - Controller yang sesuai

Sistem ini menggunakan pendekatan hybrid untuk mendukung format lama (no_sku dan qty sebagai JSON) dan format baru (menggunakan relasi). Format baru akan mengurangi beban database dan mempercepat kueri, serta menjaga integritas referensial.

Note: Setelah semua data berhasil dimigrasi, kolom no_sku dan qty di tabel history_sales dapat ditandai sebagai deprecated (tidak digunakan lagi) untuk mengurangi redundansi data. 



