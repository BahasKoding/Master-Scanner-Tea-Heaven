# 🧪 TESTING GUIDE - Phase 3 Stok Awal Integration

## 📋 **Persiapan Testing**

### **1. Cek Data Awal**
Sebelum testing, catat nilai `stok_awal` saat ini:

```sql
-- Cek stok awal finished goods
SELECT product_id, stok_awal, live_stock FROM tb_finished_goods LIMIT 5;

-- Cek stok awal bahan baku  
SELECT bahan_baku_id, stok_awal, live_stok_gudang FROM tb_inventory_bahan_baku LIMIT 5;
```

### **2. Buat Stock Opname Baru**
1. Buka `/stock-opname/create`
2. Pilih type: **Finished Goods** atau **Bahan Baku**
3. Set tanggal opname: **hari ini**
4. Klik **Simpan**

---

## 🧪 **TEST CASE 1: KONDISI 2 - Per-Row Update Stok Awal**

### **Langkah Testing:**

1. **Buka Opname yang Baru Dibuat**
   - Klik "Input Stok" pada opname yang baru dibuat
   - Lihat daftar items yang ter-populate otomatis

2. **Input Stok Fisik pada 1 Item**
   - Pilih 1 item, input `stok_fisik` (misal: 50)
   - Klik tombol **"Update"** pada item tersebut
   - ✅ **Expected Result**: Item berhasil diupdate

3. **Cek Stok Awal Langsung Berubah**
   ```sql
   -- Untuk finished goods (ganti product_id sesuai item yang ditest)
   SELECT product_id, stok_awal, live_stock FROM tb_finished_goods WHERE product_id = [ID_ITEM];
   
   -- Untuk bahan baku (ganti bahan_baku_id sesuai item yang ditest)
   SELECT bahan_baku_id, stok_awal, live_stok_gudang FROM tb_inventory_bahan_baku WHERE bahan_baku_id = [ID_ITEM];
   ```
   - ✅ **Expected Result**: `stok_awal` = nilai `stok_fisik` yang diinput

4. **Cek Log Audit**
   - Buka `storage/logs/laravel.log`
   - Cari log: `"StockOpname: Per-row stok awal update"`
   - ✅ **Expected Result**: Ada log audit lengkap

---

## 🧪 **TEST CASE 2: KONDISI 1 - Auto-Reset Saat Opname Selesai**

### **Langkah Testing:**

1. **Input Stok Fisik untuk Beberapa Items**
   - Input `stok_fisik` untuk minimal 3-5 items
   - Klik **"Simpan Semua"**
   - ✅ **Expected Result**: Semua data tersimpan

2. **Selesaikan Opname dengan Reset Stok Awal**
   - Klik tombol **"Selesaikan Opname"**
   - ✅ **CENTANG** checkbox: **"Reset stok awal sesuai hasil opname"**
   - ✅ **CENTANG** checkbox: **"Update stok sistem sesuai hasil opname"** (opsional)
   - Klik **"Ya, Selesaikan"**

3. **Verifikasi Auto-Reset Stok Awal**
   ```sql
   -- Cek semua items yang ada di opname
   SELECT 
       soi.item_name,
       soi.stok_fisik,
       CASE 
           WHEN so.type = 'finished_goods' THEN fg.stok_awal
           WHEN so.type = 'bahan_baku' THEN ib.stok_awal
       END as stok_awal_setelah_reset
   FROM stock_opname_items soi
   JOIN stock_opnames so ON soi.opname_id = so.id
   LEFT JOIN tb_finished_goods fg ON so.type = 'finished_goods' AND fg.product_id = soi.item_id
   LEFT JOIN tb_inventory_bahan_baku ib ON so.type = 'bahan_baku' AND ib.bahan_baku_id = soi.item_id
   WHERE so.id = [OPNAME_ID] AND soi.stok_fisik IS NOT NULL;
   ```
   - ✅ **Expected Result**: `stok_awal_setelah_reset` = `stok_fisik` untuk semua items

4. **Cek Log Audit Bulk Reset**
   - Buka `storage/logs/laravel.log`
   - Cari log: `"StockOpname: Auto-reset stok awal completed"`
   - ✅ **Expected Result**: Ada summary reset dengan jumlah items yang berhasil

---

## 🧪 **TEST CASE 3: Manual Reset Stok Awal (API)**

### **Testing via Browser Console atau Postman:**

```javascript
// Test manual reset stok awal
fetch('/stock-opname/[OPNAME_ID]/reset-stok-awal', {
    method: 'POST',
    headers: {
        'Content-Type': 'application/json',
        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
    }
})
.then(response => response.json())
.then(data => console.log(data));
```

- ✅ **Expected Result**: Response sukses dengan summary reset

---

## 🧪 **TEST CASE 4: Error Handling**

### **Test Skenario Error:**

1. **Reset Stok Awal pada Opname Belum Selesai**
   - Coba akses: `/stock-opname/[OPNAME_ID]/reset-stok-awal` untuk opname status "draft"
   - ✅ **Expected Result**: Error 422 - "Hanya stock opname yang sudah selesai"

2. **Update Per-Row tanpa Stok Fisik**
   - Coba update item tanpa input `stok_fisik`
   - ✅ **Expected Result**: Error validation

3. **Update pada Opname Completed**
   - Coba update item pada opname yang sudah "completed"
   - ✅ **Expected Result**: Error 422 - "Stock Opname sudah selesai"

---

## 📊 **Checklist Hasil Testing**

### **KONDISI 2 - Per-Row Update:**
- [ ] Stok awal berubah langsung saat update per item
- [ ] Log audit tercatat dengan benar
- [ ] Validation error handling bekerja
- [ ] UI response sesuai (success message)

### **KONDISI 1 - Auto-Reset:**
- [ ] Bulk reset stok awal saat opname selesai
- [ ] Semua items ter-update sesuai stok fisik
- [ ] Log audit bulk reset tercatat
- [ ] Transaction rollback jika ada error

### **Error Handling:**
- [ ] Validation untuk opname status
- [ ] Validation untuk stok fisik required
- [ ] Proper error messages
- [ ] Log error tercatat

---

## 🚨 **Troubleshooting**

### **Jika Stok Awal Tidak Berubah:**
1. Cek log error di `storage/logs/laravel.log`
2. Pastikan relationship model benar (product_id, bahan_baku_id)
3. Cek permission user untuk update stock opname

### **Jika Error 500:**
1. Cek syntax error di controller/service
2. Cek database connection
3. Cek missing model imports

### **Jika Log Tidak Muncul:**
1. Cek `config/logging.php` 
2. Pastikan log level = 'debug'
3. Cek permission write ke storage/logs

---

**Testing Selesai!** 🎉

Jika semua test case berhasil, Phase 3 - Stok Awal Integration sudah siap production.
