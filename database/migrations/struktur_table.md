Berikut ini adalah **dokumentasi struktur database antar tabel** dalam format yang rapi dan siap untuk digunakan sebagai referensi. Dokumentasi ini mencakup **relasi antar tabel, penjelasan setiap field penting, dan alur data antar entitas**.

---

# 🧱 Dokumentasi Struktur Database Sistem Produksi & Inventory

## 🧾 DAFTAR TABEL

### 1. `tb_bahan_baku` (MASTER)

> Master data untuk seluruh bahan baku yang digunakan dalam produksi.

| Field        | Tipe    | Keterangan                    |
| ------------ | ------  | ----------------------------- |
| id           | PK      | Auto increment                |
| kategori     | integer | Kategori bahan baku           |
| sku_induk    | string  | Kode unik bahan baku          |
| nama_barang  | string  | Nama bahan baku               |
| satuan       | string  | Satuan (gram, kg, liter, dll) |

---

### 2. `tb_product` (MASTER)

> Master data produk jadi yang diproduksi dan dijual.

| Field             | Tipe   | Keterangan            |
| ----------------- | ------ | --------------------- |
| id                | PK     | Auto increment        |
| category_product  | int    | Kategori produk       |
| sku               | string | Kode unik produk jadi |
| packaging         | string | Kemasan/Packaging     |
| name_product      | string | Nama produk           |
| label             | string | Label                 |

---

### 3. `tb_stock_bahan_baku`

> Menyimpan stok awal bahan baku. **Opsional** jika `inventory_bahan_baku` sudah mencakup stok awal.

| Field           | Tipe    | Keterangan                |
| --------------- | ------- | ------------------------- |
| id              | PK      | Auto increment            |
| bahan_baku_id   | FK      | Relasi ke `tb_bahan_baku` |
| stok_awal       | integer | Jumlah stok awal          |

---

### 4. `tb_catatan_produksi`

> Menyimpan catatan produksi, termasuk produk yang dibuat dan bahan baku yang digunakan (menggunakan format JSON).

| Field             | Tipe    | Keterangan                                   |
| ----------------- | ------- | -------------------------------------------- |
| id                | PK      | Auto increment                               |
| product_id        | FK      | Relasi ke `tb_product`                       |
| quantity          | integer | Jumlah unit yang diproduksi                  |
| bahan_baku_id     | JSON    | Daftar SKU bahan baku yang digunakan         |
| gramasi           | JSON    | Jumlah gramasi per bahan baku                |
| total_terpakai    | JSON    | gramasi × qty, dihitung otomatis atau manual |

---

### 5. `tb_stiker`

> Menyimpan data stok dan kebutuhan stiker per produk.

| Field             | Tipe       | Keterangan                                          |
| ----------------- | ---------- | --------------------------------------------------- |
| id                | PK         | Auto increment                                      |
| product_id        | FK         | Relasi ke `tb_product`                              |
| ukuran            | string     | Ukuran stiker (cm atau A3, A4)                      |
| jumlah            | integer    | Jumlah stiker per A3                                |
| stok_awal         | integer    | Stok awal stiker                                    |
| stok_masuk        | integer    | Stiker masuk dari pembelian                         |
| produksi          | integer    | Stiker terpakai untuk produksi                      |
| defect            | integer    | Stiker rusak                                        |
| sisa              | integer    | Stok akhir (stok_awal + masuk - produksi - defect)  |
| status            | int        | Otomatis: <30 = "order", >=30 = "tersedia"          |

---

### 6. `tb_purchase_stiker`

> Data pembelian stiker untuk setiap SKU produk.

| Field             | Tipe    | Keterangan               |
| ----------------- | ------- | ------------------------ |
| id                | PK      | Auto increment           |
| product_id        | FK      | Relasi ke `tb_product`   |
| ukuran_stiker     | string  | Ukuran stiker            |
| jumlah_stiker     | integer | Jumlah stiker per A3     |
| jumlah_order      | integer | Jumlah order per lembar  |
| stok_masuk        | integer | Jumlah stiker diterima   |
| total_order       | integer | Total lembar yang dibeli |

---

### 7. `tb_finished_goods`

> Menyimpan data stok barang jadi di etalase.

| Field        | Tipe    | Keterangan                           |
| ------------ | ------- | ------------------------------------ |
| id           | PK      | Auto increment                       |
| product_id   | FK      | Relasi ke `tb_product`               |
| stok_awal    | integer | Stok awal barang jadi                |
| stok_masuk   | integer | Stok masuk dari produksi             |
| stok_keluar  | integer | **Dihitung dari `history_sales`**    |
| defective    | integer | Barang rusak                         |
| live_stock   | integer | stok_awal + masuk - keluar - defect  |

---

### 8. `tb_inventory_bahan_baku`

> Stok bahan baku yang aktif di gudang.

| Field               | Tipe    | Keterangan                                       |
| ------------------- | ------- | ------------------------------------------------ |
| id                  | PK      | Auto increment                                   |
| bahan_baku_id       | FK      | Relasi ke `tb_bahan_baku`                        |
| stok_awal           | integer | Stok awal bahan baku                             |
| stok_masuk          | integer | Stok masuk dari pembelian                        |
| terpakai            | integer | Bahan baku terpakai dari `catatan_produksi`     |
| surplus_stok        | integer | Surplus jika lebih dari estimasi produksi       |
| defect              | integer | Bahan baku rusak                                 |
| terjual             | integer | Bahan baku yang dijual langsung                  |
| live_stok_gudang    | integer | stok_awal + masuk - terpakai - defect - terjual |
| satuan              | string  | Satuan bahan baku                                |

---

### 9. `tb_purchase_barang`

> Menyimpan riwayat pembelian bahan baku dari supplier.

| Field                         | Tipe    | Keterangan                          |
| ----------------------------- | ------- | ----------------------------------- |
| id                            | PK      | Auto increment                      |
| bahan_baku_id                 | FK      | Relasi ke `tb_bahan_baku`           |
| qty_pembelian                 | integer | Jumlah pembelian                    |
| tanggal_kedatangan_barang     | date    | Tanggal barang diterima             |
| qty_barang_masuk              | integer | Jumlah yang benar-benar diterima    |
| barang_defect_tanpa_retur     | integer | Barang rusak tanpa retur            |
| barang_diretur_ke_supplier    | integer | Barang yang dikembalikan            |
| total_stok_masuk              | integer | qty_barang_masuk - defect + retur   |
| checker_penerima_barang       | string  | Nama penerima barang                |

---

### 10. `history_sales`

> Menyimpan data penjualan, digunakan untuk menghitung **stok keluar** barang jadi.

| Field       | Tipe   | Keterangan                                               |
| ----------- | ------ | -------------------------------------------------------- |
| id          | PK     | Auto increment                                           |
| no_resi     | string | Nomor resi penjualan                                     |
| no_sku      | JSON   | Daftar SKU produk yang terjual                           |
| qty         | JSON   | Jumlah produk per SKU (urutan sesuai dengan `no_sku`)    |
| timestamps  |        | Waktu dibuat dan diperbarui                              |
| softDeletes |        | Untuk penghapusan lembut                                 |

---

## 🔗 RELASI ANTAR TABEL (ERD Logika)

```plaintext
tb_product
   ↑
   ├── tb_catatan_produksi.product_id
   ├── tb_stiker.product_id
   ├── tb_purchase_stiker.product_id
   └── tb_finished_goods.product_id

tb_bahan_baku
   ↑
   ├── tb_stock_bahan_baku.bahan_baku_id
   ├── tb_inventory_bahan_baku.bahan_baku_id
   └── tb_purchase_barang.bahan_baku_id

tb_catatan_produksi
   └── bahan_baku_id (JSON, relasi tidak langsung ke tb_bahan_baku)

tb_finished_goods
   ←  dihitung otomatis dari →  history_sales.no_sku + qty

history_sales
   → Mempengaruhi stok_keluar di tb_finished_goods
```

---

## 🔄 ACTIVITY DIAGRAM - ALUR PROSES BISNIS

### **A. PROSES MASTER DATA**

#### 1. **SETUP BAHAN BAKU**

```plaintext
[START] Input Master Bahan Baku
    ↓
[INPUT] tb_bahan_baku
    ├── kategori (pilih dari dropdown)
    ├── sku_induk (auto generate/manual)
    ├── nama_barang
    └── satuan
    ↓
[VALIDATION] Cek Duplikasi SKU
    ↓ (Unique)
[SAVE] Insert ke tb_bahan_baku
    ↓
[AUTO-CREATE] tb_inventory_bahan_baku record
    ├── stok_awal = 0
    ├── live_stok_gudang = 0
    └── satuan = copy from bahan_baku
    ↓
[END] Master Bahan Baku Ready
```

#### 2. **SETUP PRODUK**

```plaintext
[START] Input Master Produk
    ↓
[INPUT] tb_product
    ├── category_product
    ├── sku (auto generate/manual)
    ├── packaging
    ├── name_product
    └── label
    ↓
[VALIDATION] Cek Duplikasi SKU
    ↓ (Unique)
[SAVE] Insert ke tb_product
    ↓
[AUTO-CREATE] tb_finished_goods record
    ├── stok_awal = 0
    └── live_stock = 0
    ↓
[AUTO-CREATE] tb_stiker record
    ├── stok_awal = 0
    ├── status = "order"
    └── sisa = 0
    ↓
[END] Master Produk Ready
```

### **B. PROSES PROCUREMENT**

#### 3. **PEMBELIAN BAHAN BAKU**

```plaintext
[START] Kebutuhan Bahan Baku
    ↓
[DECISION] Cek Stok di tb_inventory_bahan_baku
    ↓ (Stok < Minimum)
[PROCESS] Buat Purchase Order
    ↓
[INPUT] Input data ke tb_purchase_barang
    ├── qty_pembelian
    ├── tanggal_kedatangan_barang
    └── checker_penerima_barang
    ↓
[DECISION] Barang Diterima?
    ↓ (Ya)
[PROCESS] Quality Check
    ├── qty_barang_masuk (OK)
    ├── barang_defect_tanpa_retur (Rusak)
    └── barang_diretur_ke_supplier (Retur)
    ↓
[CALCULATE] total_stok_masuk = qty_barang_masuk - defect + retur
    ↓
[UPDATE] tb_inventory_bahan_baku.stok_masuk += total_stok_masuk
    ↓
[CALCULATE] live_stok_gudang = stok_awal + stok_masuk - terpakai - defect - terjual
    ↓
[END] Stok Bahan Baku Updated
```

#### 4. **PEMBELIAN STIKER**

```plaintext
[START] Kebutuhan Stiker
    ↓
[DECISION] Cek Status di tb_stiker
    ↓ (Status = "order" / sisa < 30)
[PROCESS] Buat Purchase Order Stiker
    ↓
[INPUT] Input ke tb_purchase_stiker
    ├── product_id
    ├── ukuran_stiker
    ├── jumlah_stiker
    ├── jumlah_order
    └── total_order
    ↓
[DECISION] Stiker Diterima?
    ↓ (Ya)
[UPDATE] tb_stiker.stok_masuk += stok_diterima
    ↓
[CALCULATE] sisa = stok_awal + stok_masuk - produksi - defect
    ↓
[UPDATE] status = (sisa >= 30) ? "tersedia" : "order"
    ↓
[END] Stok Stiker Updated
```

### **C. PROSES PRODUKSI**

#### 5. **PERENCANAAN PRODUKSI**

```plaintext
[START] Planning Produksi
    ↓
[INPUT] Pilih Produk dari tb_product
    ↓
[INPUT] Target Quantity Produksi
    ↓
[PROCESS] Cek Recipe/Formula
    ├── Bahan baku apa saja yang dibutuhkan
    └── Gramasi per unit produk
    ↓
[DECISION] Cek Stok Bahan Baku di tb_inventory_bahan_baku
    ├── (Stok Cukup) → Lanjut
    └── (Stok Kurang) → Trigger Purchase
    ↓
[DECISION] Cek Stok Stiker di tb_stiker
    ├── (Stiker Tersedia) → Lanjut
    └── (Stiker Kurang) → Trigger Purchase Stiker
    ↓
[APPROVE] Production Plan Ready
    ↓
[END] Siap Produksi
```

#### 6. **EKSEKUSI PRODUKSI**

```plaintext
[START] Mulai Produksi
    ↓
[INPUT] Catat ke tb_catatan_produksi
    ├── product_id
    ├── quantity
    ├── bahan_baku_id (JSON array)
    ├── gramasi (JSON array)
    └── total_terpakai (JSON array)
    ↓
[PROCESS] Validasi Stok Real-time
    ↓ (OK)
[UPDATE] tb_inventory_bahan_baku.terpakai += total_terpakai
    ↓
[UPDATE] tb_stiker.produksi += jumlah_stiker_terpakai
    ↓
[PROCESS] Quality Control Produk Jadi
    ├── Produk OK → tb_finished_goods.stok_masuk
    └── Produk Defect → tb_finished_goods.defective
    ↓
[CALCULATE] live_stock = stok_awal + stok_masuk - stok_keluar - defective
    ↓
[CALCULATE] live_stok_gudang bahan baku
    ↓
[END] Produksi Selesai
```

### **D. PROSES PENJUALAN**

#### 7. **PENERIMAAN ORDER**

```plaintext
[START] Customer Order
    ↓
[INPUT] Detail Order
    ├── Produk yang dipesan (multiple)
    └── Quantity per produk
    ↓
[DECISION] Cek Stok di tb_finished_goods
    ├── (Stok Tersedia) → Lanjut
    └── (Stok Kurang) → Trigger Production
    ↓
[PROCESS] Reserve Stock
    ↓
[GENERATE] Nomor Resi
    ↓
[END] Order Confirmed
```

#### 8. **PROSES PENJUALAN & PENGIRIMAN**

```plaintext
[START] Process Order
    ↓
[PROCESS] Picking & Packing
    ↓
[INPUT] Catat ke history_sales
    ├── no_resi
    ├── no_sku (JSON array)
    └── qty (JSON array)
    ↓
[TRIGGER] Auto Calculate stok_keluar
    ↓
[UPDATE] tb_finished_goods.stok_keluar += qty_terjual
    ↓
[CALCULATE] live_stock = stok_awal + stok_masuk - stok_keluar - defective
    ↓
[DECISION] Live Stock < Minimum?
    ↓ (Ya)
[ALERT] Trigger Produksi Ulang
    ↓
[PROCESS] Generate Invoice & Shipping Label
    ↓
[END] Order Shipped
```

### **E. PROSES MONITORING & REPORTING**

#### 9. **MONITORING INVENTORY HARIAN**

```plaintext
[START] Daily Inventory Check
    ↓
[PROCESS] Scan tb_inventory_bahan_baku
    ├── [ALERT] live_stok_gudang < minimum_stock
    └── [REPORT] Generate Low Stock Report
    ↓
[PROCESS] Scan tb_finished_goods
    ├── [ALERT] live_stock < minimum_stock
    └── [TRIGGER] Production Planning
    ↓
[PROCESS] Scan tb_stiker
    ├── [ALERT] status = "order"
    └── [TRIGGER] Sticker Purchase
    ↓
[PROCESS] Calculate Defect Rate
    ├── Bahan Baku Defect %
    ├── Finished Goods Defect %
    └── Stiker Defect %
    ↓
[REPORT] Generate Dashboard Report
    ↓
[END] Monitoring Complete
```

#### 10. **STOCK OPNAME**

```plaintext
[START] Scheduled Stock Opname
    ↓
[PROCESS] Physical Count Bahan Baku
    ↓
[COMPARE] Physical vs System (tb_inventory_bahan_baku)
    ├── (Match) → OK
    └── (Difference) → Investigation
    ↓
[PROCESS] Physical Count Finished Goods
    ↓
[COMPARE] Physical vs System (tb_finished_goods)
    ├── (Match) → OK
    └── (Difference) → Adjustment
    ↓
[PROCESS] Physical Count Stiker
    ↓
[COMPARE] Physical vs System (tb_stiker)
    ↓
[UPDATE] Adjustment Records
    ↓
[REPORT] Stock Opname Report
    ↓
[END] Stock Opname Complete
```

### **F. PROSES ANALISIS & OPTIMASI**

#### 11. **ANALISIS PERFORMA PRODUKSI**

```plaintext
[START] Production Analysis
    ↓
[QUERY] Data dari tb_catatan_produksi (periode tertentu)
    ↓
[CALCULATE] Metrics
    ├── Production Volume per Product
    ├── Material Usage Efficiency
    ├── Production Time Analysis
    └── Defect Rate per Product
    ↓
[PROCESS] Trend Analysis
    ├── Best Performing Products
    ├── Material Waste Analysis
    └── Production Bottlenecks
    ↓
[REPORT] Production Performance Report
    ↓
[RECOMMENDATION] Optimization Suggestions
    ↓
[END] Analysis Complete
```

#### 12. **FORECASTING & DEMAND PLANNING**

```plaintext
[START] Demand Forecasting
    ↓
[QUERY] Historical Sales (history_sales)
    ↓
[ANALYZE] Sales Patterns
    ├── Seasonal Trends
    ├── Product Popularity
    └── Growth Patterns
    ↓
[CALCULATE] Forecast Demand
    ↓
[PROCESS] Material Requirement Planning
    ├── Required Bahan Baku
    ├── Required Stiker
    └── Production Capacity
    ↓
[GENERATE] Purchase Recommendations
    ↓
[GENERATE] Production Schedule
    ↓
[END] Planning Complete
```

### **G. PROSES MAINTENANCE & ADMIN**

#### 13. **BACKUP & DATA MAINTENANCE**

```plaintext
[START] System Maintenance
    ↓
[PROCESS] Database Backup
    ↓
[PROCESS] Clean Old Data
    ├── Archive old history_sales
    ├── Archive old purchase records
    └── Clean soft deleted records
    ↓
[PROCESS] Optimize Database
    ↓
[PROCESS] Update System Logs
    ↓
[VALIDATION] Data Integrity Check
    ↓
[END] Maintenance Complete
```

#### 14. **USER MANAGEMENT & PERMISSIONS**

```plaintext
[START] User Management
    ↓
[PROCESS] User Registration/Update
    ↓
[ASSIGN] Role & Permissions
    ├── Admin (Full Access)
    ├── Production Manager (Production + Inventory)
    ├── Warehouse Staff (Inventory Only)
    └── Sales Staff (Sales + Finished Goods)
    ↓
[SETUP] Access Controls
    ↓
[LOG] User Activities
    ↓
[END] User Setup Complete
```

---

## ✅ CATATAN PENTING

* Tabel `catatan_produksi` dan `history_sales` menyimpan data dalam bentuk **JSON** untuk fleksibilitas multi-data dalam satu baris.
* Disarankan untuk membuat **accessor Laravel**, **event listener**, atau **job scheduler** untuk menghitung `stok_keluar` dan `total_terpakai` secara otomatis.
* Field `live_stock` harus **dihitung otomatis** agar data tetap akurat.
* Pastikan konsistensi penamaan field menggunakan underscore (_) untuk memudahkan implementasi di Laravel.
* **Activity Diagram** di atas menunjukkan alur proses bisnis yang harus diimplementasikan dalam sistem.

---

Dokumentasi ini dapat digunakan sebagai panduan untuk implementasi migrasi Laravel dan pengembangan sistem inventory selanjutnya.
