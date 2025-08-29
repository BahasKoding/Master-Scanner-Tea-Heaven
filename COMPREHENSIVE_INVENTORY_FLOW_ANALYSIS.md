# Analisis Komprehensif Alur Data Inventory Tea Heaven

## Overview Sistem
Aplikasi Tea Heaven memiliki sistem inventory terintegrasi yang mengelola:
1. **Finished Goods** (Barang Jadi)
2. **Inventory Bahan Baku** (Raw Materials)
3. **Stock Opname** (Physical Stock Count)

---

## 1. FINISHED GOODS - Alur Data Stok Masuk & Keluar

### 📊 **Tabel Database: `finished_goods`**
```sql
- product_id (FK ke products)
- stok_awal (manual input)
- stok_masuk (dari produksi + purchase)
- stok_keluar (dari penjualan)
- defective (manual input)
- live_stock (calculated: stok_awal + stok_masuk - stok_keluar - defective)
```

### 🔄 **STOK MASUK Finished Goods**

#### **Sumber 1: Catatan Produksi**
```mermaid
graph TD
    A[User Input Catatan Produksi] --> B[CatatanProduksiController.store]
    B --> C[ProductionService.createProduction]
    C --> D[Simpan ke catatan_produksis table]
    D --> E[Auto-update FinishedGoods.stok_masuk]
    E --> F[Recalculate live_stock]
    
    G[Data Fields:]
    G --> H[product_id, quantity, packaging]
    G --> I[sku_induk array - bahan baku yang digunakan]
    G --> J[total_terpakai array - jumlah bahan baku terpakai]
```

#### **Sumber 2: Purchase Finished Goods**
```mermaid
graph TD
    A[User Input Purchase] --> B[PurchaseController.store]
    B --> C[Validasi kategori = 'finished_goods']
    C --> D[PurchaseService.createPurchase]
    D --> E[Simpan ke purchases table]
    E --> F[Auto-update FinishedGoods.stok_masuk]
    F --> G[Recalculate live_stock]
    
    H[Data Fields:]
    H --> I[bahan_baku_id = product_id]
    H --> J[kategori = 'finished_goods']
    H --> K[total_stok_masuk = qty_barang_masuk - defect + retur]
```

#### **Formula Stok Masuk:**
```
finished_goods.stok_masuk = 
    SUM(catatan_produksis.quantity WHERE product_id = X) + 
    SUM(purchases.total_stok_masuk WHERE bahan_baku_id = X AND kategori = 'finished_goods')
```

### 🔄 **STOK KELUAR Finished Goods**

#### **Sumber: History Sales (Scanner Results)**
```mermaid
graph TD
    A[User Input History Sale] --> B[HistorySaleController.store]
    B --> C[Validasi SKU exists di products table]
    C --> D[SalesService.createSale]
    D --> E[Simpan ke history_sales table]
    E --> F[Auto-update FinishedGoods.stok_keluar]
    F --> G[Recalculate live_stock]
    
    H[Data Fields:]
    H --> I[no_resi - nomor resi penjualan]
    H --> J[no_sku array - SKU produk yang dijual]
    H --> K[qty array - jumlah per SKU]
```

#### **Formula Stok Keluar:**
```
finished_goods.stok_keluar = 
    SUM(qty dari history_sales WHERE no_sku contains product.sku)
```

### 📈 **Live Stock Calculation:**
```
live_stock = stok_awal + stok_masuk - stok_keluar - defective
```

---

## 2. INVENTORY BAHAN BAKU - Alur Data Stok Masuk & Keluar

### 📊 **Tabel Database: `inventory_bahan_bakus`**
```sql
- bahan_baku_id (FK ke bahan_bakus)
- stok_awal (manual input)
- stok_masuk (dari purchase)
- terpakai (dari catatan produksi)
- defect (manual input)
- live_stok_gudang (calculated: stok_awal + stok_masuk - terpakai - defect)
```

### 🔄 **STOK MASUK Bahan Baku**

#### **Sumber: Purchase Bahan Baku**
```mermaid
graph TD
    A[User Input Purchase] --> B[PurchaseController.store]
    B --> C[Validasi kategori = 'bahan_baku']
    C --> D[PurchaseService.createPurchase]
    D --> E[Simpan ke purchases table]
    E --> F[InventoryBahanBakuService.updateInventory]
    F --> G[Auto-update inventory_bahan_bakus.stok_masuk]
    G --> H[Recalculate live_stok_gudang]
    
    I[Data Fields:]
    I --> J[bahan_baku_id]
    I --> K[kategori = 'bahan_baku']
    I --> L[total_stok_masuk = qty_barang_masuk - defect + retur]
```

#### **Formula Stok Masuk:**
```
inventory_bahan_bakus.stok_masuk = 
    SUM(purchases.total_stok_masuk WHERE bahan_baku_id = X AND kategori = 'bahan_baku')
```

### 🔄 **STOK KELUAR (Terpakai) Bahan Baku**

#### **Sumber: Catatan Produksi**
```mermaid
graph TD
    A[User Input Catatan Produksi] --> B[CatatanProduksiController.store]
    B --> C[ProductionService.createProduction]
    C --> D[Simpan ke catatan_produksis table]
    D --> E[Loop through sku_induk array]
    E --> F[Update inventory_bahan_bakus.terpakai]
    F --> G[Recalculate live_stok_gudang]
    
    H[Data Structure:]
    H --> I[sku_induk: array of bahan_baku_id]
    H --> J[total_terpakai: array of quantities used]
    H --> K[Index matching between arrays]
```

#### **Formula Terpakai:**
```
inventory_bahan_bakus.terpakai = 
    SUM(catatan_produksis.total_terpakai[index] 
        WHERE sku_induk[index] = bahan_baku_id)
```

### 📈 **Live Stock Calculation:**
```
live_stok_gudang = stok_awal + stok_masuk - terpakai - defect
```

---

## 3. STOCK OPNAME - Sistem Penghitungan Fisik

### 📊 **Tabel Database: `stock_opnames` & `stock_opname_items`**
```sql
stock_opnames:
- type ('bahan_baku' | 'finished_goods')
- tanggal_opname
- status ('draft' | 'in_progress' | 'completed')
- created_by

stock_opname_items:
- opname_id (FK)
- item_id (bahan_baku_id atau product_id)
- item_name
- stok_sistem (dari live stock saat opname dibuat)
- stok_fisik (input manual hasil penghitungan)
- selisih (calculated: stok_fisik - stok_sistem)
```

### 🔄 **Alur Stock Opname**

```mermaid
graph TD
    A[User Create Stock Opname] --> B[StockOpnameController.store]
    B --> C[StockOpnameService.createStockOpname]
    C --> D[Auto-populate items berdasarkan type]
    D --> E[Set stok_sistem = current live_stock]
    
    F[User Input Stok Fisik] --> G[StockOpnameController.updateItem]
    G --> H[Calculate selisih = stok_fisik - stok_sistem]
    
    I[Complete Opname] --> J[StockOpnameController.process]
    J --> K{Update System Stock?}
    K -->|Yes| L[Update live_stock = stok_fisik]
    K -->|No| M[Keep current live_stock]
    
    N[Reset Stok Awal Option] --> O[Update stok_awal = stok_fisik]
    O --> P[Recalculate all stock values]
```

### 🔄 **Integration Points Stock Opname**

#### **Dengan Finished Goods:**
```mermaid
graph TD
    A[Stock Opname Completed] --> B{Type = finished_goods?}
    B -->|Yes| C[Update finished_goods.live_stock]
    C --> D[Optional: Reset finished_goods.stok_awal]
    D --> E[Trigger recalculation of all values]
```

#### **Dengan Inventory Bahan Baku:**
```mermaid
graph TD
    A[Stock Opname Completed] --> B{Type = bahan_baku?}
    B -->|Yes| C[Update inventory_bahan_bakus.live_stok_gudang]
    C --> D[Optional: Reset inventory_bahan_bakus.stok_awal]
    D --> E[Trigger recalculation of all values]
```

---

## 4. ACTIVITY DIAGRAM - Alur Data Lengkap

```mermaid
graph TD
    %% PURCHASE FLOW
    A[Purchase Input] --> B{Kategori?}
    B -->|bahan_baku| C[Update inventory_bahan_bakus.stok_masuk]
    B -->|finished_goods| D[Update finished_goods.stok_masuk]
    
    %% PRODUCTION FLOW
    E[Catatan Produksi Input] --> F[Update finished_goods.stok_masuk]
    E --> G[Update inventory_bahan_bakus.terpakai]
    
    %% SALES FLOW
    H[History Sale Input] --> I[Update finished_goods.stok_keluar]
    
    %% STOCK OPNAME FLOW
    J[Stock Opname] --> K{Type?}
    K -->|bahan_baku| L[Compare dengan inventory_bahan_bakus.live_stok_gudang]
    K -->|finished_goods| M[Compare dengan finished_goods.live_stock]
    
    L --> N[Update stok_sistem jika diperlukan]
    M --> N
    
    %% RECALCULATION
    C --> O[Recalculate live_stok_gudang]
    D --> P[Recalculate live_stock]
    F --> P
    G --> O
    I --> P
    N --> Q[Update live stock values]
    
    %% SERVICES INTEGRATION
    R[FinishedGoodsService] --> P
    S[InventoryBahanBakuService] --> O
    T[StockOpnameService] --> Q
    U[PurchaseService] --> C
    U --> D
    V[ProductionService] --> F
    V --> G
    W[SalesService] --> I
```

---

## 5. FITUR-FITUR KHUSUS

### 🔄 **Auto-Sync Features**
1. **Finished Goods Sync**: Otomatis update stok_masuk dari produksi + purchase
2. **Inventory Sync**: Otomatis update stok_masuk dari purchase & terpakai dari produksi
3. **Stock Consistency Check**: Verifikasi konsistensi data antar tabel
4. **Monthly Filtering**: Filter data berdasarkan bulan untuk laporan

### 🔄 **Service Layer Integration**
- **FinishedGoodsService**: Mengelola semua operasi finished goods
- **InventoryBahanBakuService**: Mengelola inventory bahan baku
- **StockOpnameService**: Mengelola stock opname operations
- **PurchaseService**: Mengelola purchase dengan auto-update inventory
- **ProductionService**: Mengelola produksi dengan auto-update inventory
- **SalesService**: Mengelola penjualan dengan auto-update stock

### 🔄 **Data Validation**
- **SKU Validation**: Semua SKU harus exist di products table
- **Purchase Calculation**: Validasi qty_barang_masuk vs defect vs retur
- **Stock Consistency**: Validasi live_stock calculations
- **Duplicate Prevention**: Validasi no_resi dan SKU duplicates

---

## 6. KESIMPULAN ALUR DATA

### **FINISHED GOODS:**
- **STOK MASUK**: Catatan Produksi + Purchase (kategori finished_goods)
- **STOK KELUAR**: History Sales (scanner results)
- **LIVE STOCK**: stok_awal + stok_masuk - stok_keluar - defective

### **INVENTORY BAHAN BAKU:**
- **STOK MASUK**: Purchase (kategori bahan_baku)
- **STOK KELUAR (Terpakai)**: Catatan Produksi (consumption)
- **LIVE STOCK**: stok_awal + stok_masuk - terpakai - defect

### **STOCK OPNAME:**
- **INTEGRATION**: Dapat update live_stock kedua jenis inventory
- **RESET CAPABILITY**: Dapat reset stok_awal berdasarkan hasil opname
- **VARIANCE ANALYSIS**: Analisis selisih antara sistem vs fisik

Sistem ini fully integrated dengan service layer untuk memastikan konsistensi data dan transaction safety.
