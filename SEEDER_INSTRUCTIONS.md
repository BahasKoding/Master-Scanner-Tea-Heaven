# Instruksi Menjalankan Product Seeder

## Persyaratan
1. Pastikan file `master_product_seeder.md` ada di root project (sama level dengan file `composer.json`)
2. Pastikan tabel `products` sudah ada di database

## Format Data di File master_product_seeder.md
File harus berformat TSV (Tab Separated Values) dengan struktur:
```
CATEGORY    SKU    PACK_CODE    PRODUCT_NAME    PACK_SIZE
```

Contoh:
```
CLASSIC TEA COLLECTION	VB40P	P1	BOURBON VANILLA	EXTRA SMALL PACK (15-100 GRAM)
PURE TISANE	BP15P	P1	BUTTERFLY PEA	EXTRA SMALL PACK (15-100 GRAM)
ARTISAN TEA	BB40P	P1	BLUEBERRY BLACK TEA	EXTRA SMALL PACK (15-100 GRAM)
```

## Menjalankan Seeder

### 1. Menjalankan Seeder Produk Saja
```bash
php artisan db:seed --class=ProductSeeder
```

### 2. Menjalankan Semua Seeder (termasuk Product Seeder)
```bash
php artisan db:seed
```

### 3. Refresh Database dan Jalankan Semua Seeder
```bash
php artisan migrate:fresh --seed
```

## Struktur Tabel Products
Seeder ini mengasumsikan tabel `products` memiliki kolom:
- `id` (Primary Key)
- `category_product` (Integer - ID kategori)
- `sku` (String, Unique)
- `packaging` (String)
- `name_product` (String)
- `label` (Integer - ID label)
- `created_at` (Timestamp)
- `updated_at` (Timestamp)

## Mapping Kategori
Seeder akan mengkonversi nama kategori menjadi ID:
- CLASSIC TEA COLLECTION → 1
- PURE TISANE → 2
- ARTISAN TEA → 3
- JAPANESE TEA → 4
- CHINESE TEA → 5
- PURE POWDER → 6
- SWEET POWDER → 7
- LATTE POWDER → 8
- EMPTY → 9
- CRAFTED TEAS → 10
- UNKNOWN CATEGORY → 11

## Mapping Label/Pack Size
Seeder akan mengkonversi nama label menjadi ID:
- "-" → 0
- EXTRA SMALL PACK (15-100 GRAM) → 1
- SMALL PACK (50-250 GRAM) → 2
- MEDIUM PACK (500 GRAM) → 3
- BIG PACK (1 KILO) → 4
- TIN CANISTER SERIES → 5
- REFILL PACK, SAMPLE & GIFT → 6
- CRAFTED TEAS → 7
- JAPANESE TEABAGS → 8
- TEA WARE → 9
- NON LABEL 500 GR-1000 GR → 10
- HERBATA NON LABEL 500 GR-1000 GR → 11

## Default Values
Jika data kosong, maka akan diisi dengan:
- **Category kosong**: "UNKNOWN CATEGORY" (ID: 11)
- **Pack Code kosong**: "-"
- **Pack Size kosong**: "-" (ID: 0)

## Fitur Seeder

### ✅ Validasi Data
- SKU harus ada dan tidak kosong
- Product Name harus ada dan tidak kosong
- Baris dengan data tidak lengkap akan dilewati

### ✅ Batch Processing
- Insert data dalam batch (50 record per batch)
- Performa optimal untuk data besar

### ✅ Error Handling
- Penanganan error untuk setiap record
- Laporan detail tentang data yang berhasil/gagal

### ✅ Progress Tracking
- Menampilkan progress setiap 100 data
- Summary report lengkap

### ✅ Data Mapping
- Automatic mapping category name ke category ID
- Automatic mapping label name ke label ID
- Konsisten dengan struktur database

## Contoh Output Seeder
```
🚀 Memulai konversi data produk dari file master_product_seeder.md...
📂 Memproses data produk dari master_product_seeder.md...
Memproses 538 baris data produk...
📍 Memproses baris ke-100 dari 538 total...
📍 Memproses baris ke-200 dari 538 total...
...
✅ Batch berhasil (VB40P - BB40P) = 50 records
✅ Batch berhasil (CC40P - MSC50P) = 50 records
...
📊 SUMMARY REPORT - PRODUCTS:
✅ Total baris diproses: 536
✅ Berhasil diinsert: 536
⚠️  Baris dilewati: 2
❌ Gagal diinsert: 0
✅ SEEDER SELESAI! Total 536 produk berhasil dibuat dari data master product.
```

## Troubleshooting

### Error: File tidak ditemukan
- Pastikan file `master_product_seeder.md` ada di root project
- Check path file dengan `ls -la master_product_seeder.md`

### Error: Foreign key constraint
- Pastikan tabel `products` sudah ada
- Jalankan migration terlebih dahulu: `php artisan migrate`

### Error: Duplicate SKU
- Check data di file MD, pastikan tidak ada SKU yang duplikat
- Bersihkan tabel products: `php artisan migrate:fresh`

### Data tidak sesuai
- Check mapping kategori dan label di seeder
- Pastikan format file menggunakan TAB separator, bukan space