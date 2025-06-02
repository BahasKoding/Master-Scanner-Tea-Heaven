# 🧪 Panduan Testing Sistem Sticker

## Overview
Dokumentasi ini menjelaskan cara menggunakan seeder testing yang telah dibuat untuk mensimulasikan data sticker system Tea Heaven.

## ⚠️ Peringatan Penting
- **JANGAN** jalankan seeder testing di environment **production**
- Seeder ini akan mengisi data dummy yang dirancang khusus untuk testing
- Pastikan backup database sebelum menjalankan testing

## 📁 File Seeder Testing

### 1. `TestingCatatanProduksiSeeder.php`
**Fungsi:** Mensimulasikan catatan produksi untuk produk-produk yang memiliki sticker
- Membuat 2-5 record produksi per produk selama 30 hari terakhir
- Generate quantity realistis berdasarkan packaging type
- Otomatis mempengaruhi nilai "Produksi" di tabel sticker

### 2. `TestingPurchaseStickerSeeder.php`
**Fungsi:** Mensimulasikan pembelian sticker
- Membuat 1-3 record purchase per produk selama 60 hari terakhir
- Generate jumlah A3 sheets dan stok masuk yang realistis
- Otomatis mempengaruhi nilai "Stok Masuk" di tabel sticker

### 3. `CompleteStickerTestingSeeder.php`
**Fungsi:** Master seeder yang menjalankan semua testing seeder dalam urutan yang benar

## 🚀 Cara Penggunaan

### Opsi 1: Jalankan Master Seeder (Recommended)
```bash
php artisan db:seed --class=CompleteStickerTestingSeeder
```

Seeder ini akan:
1. ✅ Konfirmasi sebelum menjalankan
2. 🔄 Jalankan StickerSeeder (data dasar sticker)
3. 💰 Jalankan TestingPurchaseStickerSeeder (simulasi pembelian)
4. 🏭 Jalankan TestingCatatanProduksiSeeder (simulasi produksi)
5. 📊 Tampilkan ringkasan lengkap

### Opsi 2: Jalankan Seeder Individual

#### Hanya simulasi catatan produksi:
```bash
php artisan db:seed --class=TestingCatatanProduksiSeeder
```

#### Hanya simulasi purchase sticker:
```bash
php artisan db:seed --class=TestingPurchaseStickerSeeder
```

## 📊 Data yang Dihasilkan

### Berdasarkan Packaging Type

| Packaging | Nama | Produksi (pcs) | A3 Sheets | Stickers/A3 |
|-----------|------|----------------|-----------|-------------|
| P1 | EXTRA SMALL PACK | 50-300 | 10-50 | 14 |
| T1 | TIN CANISTER SERIES | 20-150 | 5-30 | 17 |
| T2 | TIN CANISTER CUSTOM | 15-100 | 3-20 | 16 |
| - | TEABAGS/MATCHA | 10-80 | 2-15 | 42 |

### Simulasi Realistis
- **Tanggal Random:** Data tersebar dalam 30-60 hari terakhir
- **Variasi Quantity:** Berdasarkan packaging type yang realistis
- **Stok Masuk:** 85-100% dari yang dipesan (simulasi defect/loss)
- **Bahan Baku:** Random 2-4 bahan baku per catatan produksi

## 🧪 Testing Formula

### Formula Sisa Sticker:
```
Sisa = Stok Awal + Stok Masuk (Dynamic) - Produksi (Dynamic) - Defect
```

### Dynamic Values:
- **Stok Masuk Dynamic:** `SUM(purchase_stickers.stok_masuk)` untuk produk tersebut
- **Produksi Dynamic:** `SUM(catatan_produksis.quantity)` untuk produk tersebut

## 🎯 Cara Testing Sistem

### 1. Persiapan Data
```bash
# Jalankan master seeder
php artisan db:seed --class=CompleteStickerTestingSeeder
```

### 2. Testing di Web Interface
1. **Buka halaman Manajemen Sticker** di browser
2. **Perhatikan kolom berikut:**
   - ✏️ **Stok Awal:** Editable (default 0)
   - 🔒 **Stok Masuk:** Read-only (dari purchase sticker)
   - 🔒 **Produksi:** Read-only (dari catatan produksi)
   - ✏️ **Defect:** Editable (default 0)
   - 🔄 **Sisa:** Auto-calculated dengan color coding

### 3. Testing Inline Editing
1. **Ubah nilai** di kolom "Stok Awal" atau "Defect"
2. **Perhatikan perubahan** nilai "Sisa" secara real-time
3. **Klik tombol "Update"** untuk menyimpan
4. **Verifikasi** data tersimpan dengan benar

### 4. Testing Observer System
```bash
# Buat catatan produksi baru manual atau via seeder
php artisan tinker

# Di tinker:
$sticker = App\Models\Sticker::first();
echo "Produksi sebelum: " . $sticker->produksi_dynamic;

# Buat catatan produksi baru
App\Models\CatatanProduksi::create([
    'product_id' => $sticker->product_id,
    'packaging' => 'P1',
    'quantity' => 100,
    'sku_induk' => json_encode([1,2]),
    'gramasi' => json_encode([50,30]),
    'total_terpakai' => json_encode([5000,3000])
]);

# Refresh dan cek lagi
$sticker->refresh();
echo "Produksi sesudah: " . $sticker->produksi_dynamic;
```

## 🔍 Validasi Data

### Check Manual via Database
```sql
-- Cek total sticker
SELECT COUNT(*) as total_stickers FROM stickers;

-- Cek produk dengan sticker dan data dinamis
SELECT 
    p.sku,
    p.name_product,
    s.stok_awal,
    COALESCE(ps_sum.total_stok_masuk, 0) as stok_masuk_dynamic,
    COALESCE(cp_sum.total_produksi, 0) as produksi_dynamic,
    s.defect,
    (s.stok_awal + COALESCE(ps_sum.total_stok_masuk, 0) - COALESCE(cp_sum.total_produksi, 0) - s.defect) as sisa_calculated
FROM stickers s
JOIN products p ON s.product_id = p.id
LEFT JOIN (
    SELECT product_id, SUM(stok_masuk) as total_stok_masuk 
    FROM purchase_stickers 
    GROUP BY product_id
) ps_sum ON s.product_id = ps_sum.product_id
LEFT JOIN (
    SELECT product_id, SUM(quantity) as total_produksi 
    FROM catatan_produksis 
    GROUP BY product_id
) cp_sum ON s.product_id = cp_sum.product_id
LIMIT 10;
```

## 🧹 Clean Up Testing Data

### Reset Semua Data
```bash
php artisan migrate:fresh --seed
```

### Hapus Hanya Data Testing
```sql
-- Hapus data testing (hati-hati!)
DELETE FROM catatan_produksis;
DELETE FROM purchase_stickers;
UPDATE stickers SET stok_awal = 0, defect = 0, sisa = 0;
```

## ❓ Troubleshooting

### Issue: Sticker tidak muncul nilai dinamis
**Solusi:**
1. Pastikan StickerSeeder sudah dijalankan
2. Cek apakah ada data di `purchase_stickers` dan `catatan_produksis`
3. Refresh halaman web atau clear cache

### Issue: Observer tidak bekerja
**Solusi:**
1. Pastikan Observer terdaftar di `AppServiceProvider`
2. Clear cache: `php artisan cache:clear`
3. Restart web server

### Issue: Seeder error "Product not found"
**Solusi:**
1. Pastikan data produk sudah ada di database
2. Jalankan `php artisan db:seed` untuk seed produk dulu
3. Cek SKU yang error di console output

## 📈 Expected Results

Setelah menjalankan testing seeder, Anda harus melihat:
- ✅ Sticker dengan nilai Stok Masuk > 0 (dari purchase)
- ✅ Sticker dengan nilai Produksi > 0 (dari catatan produksi)  
- ✅ Kalkulasi Sisa yang akurat
- ✅ Color coding pada badge Sisa (merah jika < 30, hijau jika >= 30)
- ✅ Inline editing berfungsi dengan real-time calculation 