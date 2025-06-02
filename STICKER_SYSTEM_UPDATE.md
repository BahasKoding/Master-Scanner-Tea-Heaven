# Update Sistem Sticker - Tea Heaven

## Perubahan yang Dilakukan

### 1. Formula Perhitungan Sisa Sticker
**Sebelum:**
```
sisa = stok_awal + stok_masuk + produksi - defect
```

**Sesudah:**
```
sisa = stok_awal + stok_masuk - produksi - defect
```

**Alasan:** Produksi mengurangi stok sticker karena sticker digunakan untuk proses produksi.

### 2. Otomatisasi Kolom Produksi
- Kolom `produksi` di tabel sticker sekarang **otomatis** berdasarkan data dari `catatan_produksi`
- User tidak perlu input manual untuk kolom produksi
- Nilai produksi akan ter-update otomatis ketika:
  - Catatan produksi baru dibuat
  - Catatan produksi diupdate
  - Catatan produksi dihapus

### 3. Filter Produk yang Eligible untuk Sticker
Hanya produk dengan label berikut yang memiliki sticker:
- **Label 1:** EXTRA SMALL PACK (15-100 GRAM) - Packaging P1
- **Label 5:** TIN CANISTER SERIES - Packaging T1, T2
- **Label 10:** JAPANESE TEABAGS - No packaging (-)

### 4. Mapping Ukuran dan Jumlah Sticker per A3
| Packaging | Ukuran Sticker | Jumlah per A3 | Kategori |
|-----------|----------------|---------------|----------|
| P1        | 5 X 17         | 14           | EXTRA SMALL PACK |
| T1        | 11.5 X 5.7     | 17           | TIN CANISTER SERIES |
| T2        | 13 X 5         | 16           | TIN CANISTER CUSTOM |
| -         | 10 X 3         | 42           | JAPANESE TEABAGS |

### 5. Observer Pattern untuk Auto-Update
- `CatatanProduksiObserver` akan otomatis update sticker produksi
- Method `Sticker::ensureStickerExists()` akan membuat sticker baru jika belum ada
- Method `Sticker::updateProduksiFromCatatanProduksi()` untuk sync produksi

### 6. Command untuk Sync Data
```bash
# Dry run untuk melihat perubahan tanpa eksekusi
php artisan sticker:sync-produksi --dry-run

# Eksekusi sync produksi sticker
php artisan sticker:sync-produksi
```

### 7. Perubahan UI/UX
- Kolom produksi di form add/edit menjadi **readonly**
- Ditambahkan keterangan "(auto)" di kolom produksi di tabel
- Ditambahkan informasi "Nilai otomatis berdasarkan catatan produksi"

### 8. Dynamic Attributes
- `stok_masuk_dynamic`: Mengambil dari `purchase_stickers`
- `produksi_dynamic`: Mengambil dari `catatan_produksi`
- `sisa_dynamic`: Kalkulasi otomatis berdasarkan formula baru
- `auto_status`: Status otomatis berdasarkan sisa (< 30 = need_order)

## Testing
1. Buat catatan produksi untuk produk yang eligible
2. Cek apakah sticker otomatis terbuat/terupdate
3. Verifikasi formula sisa sudah benar
4. Test export data sticker
5. Jalankan command sync untuk data existing

## File yang Dimodifikasi
- `app/Models/Sticker.php`
- `app/Observers/CatatanProduksiObserver.php`
- `app/Http/Controllers/StickerController.php`
- `app/Http/Controllers/PurchaseStickerController.php`
- `app/Console/Commands/SyncStickerProduksi.php`
- `resources/views/sticker/index.blade.php` 