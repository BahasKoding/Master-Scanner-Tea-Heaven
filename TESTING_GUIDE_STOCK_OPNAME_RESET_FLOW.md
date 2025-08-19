# 🧪 Testing Guide - Stock Opname Reset Flow

## 📋 Pre-Testing Setup

### **Backup Data**
• Export database sebelum testing: `mysqldump tea_heaven > backup_before_testing.sql`
• Catat stok awal di menu Finished Goods dan Inventory Bahan Baku
• Screenshot dashboard untuk comparison

---

## 🎯 Testing Flow - Step by Step

### **STEP 1: Prepare Test Data**
• Login ke aplikasi: `http://127.0.0.1:8000`
• Buka menu **Finished Goods** → catat 3-5 produk dengan stok > 0
• Buka menu **Inventory Bahan Baku** → catat 3-5 bahan baku dengan stok > 0
• **Format catatan**: `Nama Item | Stok Awal | Stok Masuk | Stok Keluar | Live Stock`

### **STEP 2: Create Stock Opname**
• Menu **Transaction Tables** → **Stock Opname**
• Klik **"Tambah Opname Baru"**
• Pilih **Type**: `finished_goods` atau `bahan_baku`
• Pilih **Tanggal**: hari ini
• Klik **"Simpan"**
• **Expected**: Items auto-populate dari inventory

### **STEP 3: Input Physical Count**
• Di halaman Stock Opname → input **Stok Fisik** berbeda dari **Stok Sistem**
• **Test Cases**:
  - Item A: Stok Sistem = 100, Input Stok Fisik = 80 (shortage)
  - Item B: Stok Sistem = 50, Input Stok Fisik = 70 (surplus)  
  - Item C: Stok Sistem = 30, Input Stok Fisik = 30 (exact)
• **Expected**: Selisih otomatis terhitung

### **STEP 4: Process Opname (CRITICAL TEST)**
• Klik **"Selesaikan Opname"**
• **Centang**: ✅ "Update stok sistem sesuai hasil opname"
• Klik **"Proses"**
• **Expected**: Status berubah ke "completed"

### **STEP 5: Verify Reset Flow**
• **Buka menu yang sesuai** (Finished Goods atau Inventory Bahan Baku)
• **Check setiap item yang di-opname**:
  - ✅ **stok_awal** = stok_fisik dari opname
  - ✅ **stok_masuk** = 0
  - ✅ **stok_keluar** = 0  
  - ✅ **defective** = 0 (finished goods)
  - ✅ **terpakai** = 0 (bahan baku)
  - ✅ **live_stock** = stok_awal

---

## 🔍 Validation Checklist

### **Before Opname**
• [ ] Record original stock values
• [ ] Verify live stock calculations are correct
• [ ] Check system is accessible

### **During Opname**
• [ ] Items auto-populate correctly
• [ ] Physical count input works
• [ ] Variance calculation is accurate
• [ ] No error messages appear

### **After Opname (RESET FLOW)**
• [ ] **stok_awal** = physical count ✅
• [ ] **stok_masuk** = 0 ✅
• [ ] **stok_keluar** = 0 ✅
• [ ] **defective/terpakai** = 0 ✅
• [ ] **live_stock** = stok_awal ✅
• [ ] No system errors in logs

---

## 🚨 Critical Test Scenarios

### **Scenario A: Finished Goods Reset**
```
Before: stok_awal=10, stok_masuk=50, stok_keluar=20, defective=5, live_stock=35
Opname: stok_fisik=40
After:  stok_awal=40, stok_masuk=0,  stok_keluar=0,  defective=0, live_stock=40
```

### **Scenario B: Bahan Baku Reset**
```
Before: stok_awal=20, stok_masuk=100, terpakai=30, defect=10, live_stok_gudang=80
Opname: stok_fisik=75
After:  stok_awal=75, stok_masuk=0,   terpakai=0,  defect=0,  live_stok_gudang=75
```

### **Scenario C: Zero Stock Reset**
```
Before: stok_awal=5, stok_masuk=0, stok_keluar=5, live_stock=0
Opname: stok_fisik=0
After:  stok_awal=0, stok_masuk=0, stok_keluar=0, live_stock=0
```

---

## 🔧 Quick Testing Commands

### **Check Database Before/After**
```sql
-- Before Opname
SELECT product_id, stok_awal, stok_masuk, stok_keluar, defective 
FROM tb_finished_goods WHERE product_id IN (1,2,3);

-- After Opname
SELECT product_id, stok_awal, stok_masuk, stok_keluar, defective 
FROM tb_finished_goods WHERE product_id IN (1,2,3);
```

### **Check Logs**
```bash
tail -f storage/logs/laravel.log | grep "RESET FLOW"
```

---

## ⚠️ Rollback Plan

### **If Testing Fails**
• Stop testing immediately
• Restore database: `mysql tea_heaven < backup_before_testing.sql`
• Check error logs: `storage/logs/laravel.log`
• Report issues before production deployment

### **Common Issues & Solutions**
• **Error 500**: Check method visibility in StockOpnameService
• **Data not updating**: Verify database transaction commits
• **Wrong calculations**: Check model accessors for live_stock

---

## ✅ Production Readiness Criteria

### **All Tests Must Pass**
• [ ] Reset flow works for Finished Goods
• [ ] Reset flow works for Bahan Baku  
• [ ] Reset flow works for Sticker (if applicable)
• [ ] No PHP errors in logs
• [ ] Database transactions complete successfully
• [ ] UI shows correct updated values
• [ ] Live stock recalculates properly

### **Performance Check**
• [ ] Opname processing < 30 seconds for 100+ items
• [ ] No memory issues during reset
• [ ] Database locks release properly

---

## 📞 Emergency Contacts

**If Critical Issues Found**:
• Stop production deployment
• Document exact error messages
• Provide database backup
• Share error logs from `storage/logs/laravel.log`

---

**Testing Duration**: ~30-45 minutes  
**Required**: Database backup, test data, error monitoring
