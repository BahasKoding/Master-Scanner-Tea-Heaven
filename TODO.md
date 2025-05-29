# 📋 TODO LIST - Master Scanner Tea Heaven

## 🎯 PRIORITAS PENGEMBANGAN FITUR

### ✅ SUDAH SELESAI

#### Master Data
- [x] Master Data Products (`ProductController.php`)
- [x] Master Data Bahan Baku (`BahanBakuController.php`)
- [x] Sticker Management (`StickerController.php`)

#### Transaksi & Operasional
- [x] Dashboard
- [x] Catatan Produksi (`CatatanProduksiController.php`) 
- [x] Finished Goods Stock (`FinishedGoodsController.php`)
- [x] Sales Scanner, Management & Report (`HistorySaleController.php`)
- [x] Purchase Bahan Baku (`PurchaseController.php`)  <!-- harus di cek lgi -->
- [x] Inventory Bahan Baku (`InventoryBahanBakuController.php`)

#### User Management & System
- [x] User Management (`UserController.php`)
- [x] Roles & Permissions (`RoleController.php`, `PermissionController.php`)
- [x] Activity Log (`ActivityController.php`)

---

## 🚀 PRIORITAS 1 - MUDAH (Lengkapi Controller yang Sudah Ada)

### 📦 Purchase Management

#### 1. Purchase Bahan Baku (`PurchaseController.php`) ✅ **SELESAI**
- [x] **Model**: Update `Purchase.php` model ✅ 
  - [x] Relasi ke `BahanBaku` model ✅
  - [x] Fillable fields sesuai `tb_purchase_barang` ✅
  - [x] Accessor untuk calculated fields ✅
- [x] **Migration**: Migration `tb_purchase_barang` sudah ada ✅
- [x] **Controller**: Lengkapi `PurchaseController.php` ✅
  - [x] `index()` - List semua purchase ✅
  - [x] `create()` - Form tambah purchase ✅
  - [x] `store()` - Simpan purchase baru ✅
  - [x] `show()` - Detail purchase ✅
  - [x] `edit()` - Form edit purchase ✅
  - [x] `update()` - Update purchase ✅
  - [x] `destroy()` - Hapus purchase ✅
  - [x] `data()` - DataTables AJAX ✅
- [x] **Views**: Folder `resources/views/purchase/` ✅
  - [x] `index.blade.php` - List purchase dengan DataTables ✅
  - [x] `show.blade.php` - Detail view modal ✅
- [x] **Routes**: Routes purchase di `web.php` ✅
- [x] **Integration**: Auto update untuk live stock calculation ✅
- [x] **Features Implemented**: ✅
  - [x] Modal-based CRUD operations ✅
  - [x] Real-time total stock calculation ✅
  - [x] Advanced filtering (bahan baku, date range) ✅
  - [x] Server-side DataTables with search ✅
  - [x] Percentage calculations (defect %, retur %) ✅
  - [x] Activity logging ✅
  - [x] Permission middleware ✅
  - [x] Responsive design ✅
  - [x] Indonesian language support ✅

#### 2. Purchase Stiker (`PurchaseStickerController.php`)
- [ ] **Model**: Buat/Update `PurchaseSticker.php` model
  - [ ] Relasi ke `Product` model
  - [ ] Fillable fields sesuai `tb_purchase_stiker`
- [ ] **Migration**: Cek/buat migration `tb_purchase_stiker`
- [ ] **Controller**: Lengkapi `PurchaseStickerController.php`
  - [ ] Semua method CRUD seperti di atas
  - [ ] `data()` - DataTables AJAX
- [ ] **Views**: Buat folder `resources/views/purchase-sticker/`
  - [ ] Semua view files seperti di atas
- [ ] **Routes**: Tambah routes di `web.php`
- [ ] **Integration**: Auto update `tb_stiker.stok_masuk`

#### 3. Inventory Bahan Baku (`InventoryBahanBakuController.php`) ✅ **SELESAI**
- [x] **Model**: Update `InventoryBahanBaku.php` model ✅
  - [x] Relasi ke `BahanBaku` model ✅
  - [x] Accessor untuk `live_stok_gudang` ✅
  - [x] Scope untuk low stock alert ✅
- [x] **Migration**: Migration `tb_inventory_bahan_baku` sudah ada ✅
- [x] **Controller**: Lengkapi `InventoryBahanBakuController.php` ✅
  - [x] `index()` - Dashboard inventory ✅
  - [x] `data()` - DataTables dengan live stock ✅
  - [x] `update()` - Manual adjustment ✅
  - [x] `edit()` - Form edit inventory ✅
  - [x] `store()` - Store inventory data ✅
- [x] **Views**: Folder `resources/views/inventory-bahan-baku/` ✅
  - [x] `index.blade.php` - Dashboard inventory ✅
- [x] **Routes**: Routes inventory di `web.php` ✅
- [x] **Integration**: Auto update untuk live stock calculation ✅
- [x] **Features Implemented**: ✅
  - [x] Inline editing untuk semua field inventory ✅
  - [x] Real-time calculation `live_stok_gudang` ✅
  - [x] Advanced filtering (bahan baku, kategori) ✅
  - [x] Server-side DataTables with search ✅
  - [x] Low stock alert (red badge jika ≤ 10) ✅
  - [x] Activity logging ✅
  - [x] Permission middleware ✅
  - [x] Responsive design ✅
  - [x] SweetAlert notifications ✅

---

## 🎯 PRIORITAS 2 - SEDANG (Buat Controller Baru dengan Pattern yang Ada)

### 📊 Stock Management

#### 4. Stock Bahan Baku
- [ ] **Controller**: Buat `StockBahanBakuController.php`
  - [ ] Copy pattern dari `FinishedGoodsController.php`
  - [ ] Sesuaikan dengan struktur `tb_stock_bahan_baku`
- [ ] **Model**: Buat `StockBahanBaku.php`
- [ ] **Migration**: Buat migration `tb_stock_bahan_baku`
- [ ] **Views**: Buat folder `resources/views/stock-bahan-baku/`
- [ ] **Routes**: Tambah routes di `web.php`
- [ ] **Menu**: Update menu-list.blade.php

#### 5. Stock Opname
- [ ] **Controller**: Buat `StockOpnameController.php`
  - [ ] `index()` - List opname records
  - [ ] `create()` - Start new opname
  - [ ] `process()` - Process opname data
  - [ ] `adjust()` - Stock adjustment
  - [ ] `report()` - Opname report
- [ ] **Model**: Buat `StockOpname.php`
- [ ] **Migration**: Buat migration `tb_stock_opname`
- [ ] **Views**: Buat folder `resources/views/stock-opname/`
- [ ] **Routes**: Tambah routes di `web.php`

---

## 🎯 PRIORITAS 3 - MENENGAH (Logic Baru tapi Masih Simple)

### 🏭 Production Management

#### 6. Production Planning
- [ ] **Controller**: Buat `ProductionPlanningController.php`
  - [ ] `index()` - List production plans
  - [ ] `create()` - Create production plan
  - [ ] `checkMaterials()` - Check material availability
  - [ ] `checkStickers()` - Check sticker availability
  - [ ] `approve()` - Approve production plan
- [ ] **Model**: Buat `ProductionPlanning.php`
- [ ] **Migration**: Buat migration `tb_production_planning`
- [ ] **Views**: Buat folder `resources/views/production-planning/`
- [ ] **Routes**: Tambah routes di `web.php`
- [ ] **Integration**: Link dengan `CatatanProduksi`

---

## 🎯 PRIORITAS 4 - LANJUTAN (Reports & Analytics)

### 📈 Basic Reports

#### 7. Stock Movement Report
- [ ] **Controller**: Buat `StockMovementController.php`
  - [ ] `index()` - Stock movement dashboard
  - [ ] `report()` - Generate movement report
  - [ ] `export()` - Export to Excel/PDF
- [ ] **Views**: Buat folder `resources/views/stock-movement/`
- [ ] **Routes**: Tambah routes di `web.php`

#### 8. Low Stock Alert System
- [ ] **Controller**: Buat `LowStockAlertController.php`
  - [ ] `index()` - Alert dashboard
  - [ ] `checkAll()` - Check all items
  - [ ] `settings()` - Alert settings
- [ ] **Model**: Buat `LowStockAlert.php`
- [ ] **Migration**: Buat migration `tb_low_stock_alerts`
- [ ] **Views**: Buat folder `resources/views/low-stock-alert/`
- [ ] **Scheduler**: Laravel scheduler untuk auto check

#### 9. Production Performance Report
- [ ] **Controller**: Buat `ProductionReportController.php`
  - [ ] `index()` - Performance dashboard
  - [ ] `efficiency()` - Material efficiency
  - [ ] `defectAnalysis()` - Defect analysis
  - [ ] `export()` - Export reports
- [ ] **Views**: Buat folder `resources/views/production-report/`
- [ ] **Routes**: Tambah routes di `web.php`

---

## 🎯 PRIORITAS 5 - ADVANCED (Complex Features)

### 🔮 Advanced Analytics

#### 10. Material Usage Analysis
- [ ] **Controller**: Buat `MaterialUsageController.php`
- [ ] **Views**: Advanced charts dan analytics
- [ ] **Integration**: Dengan Chart.js atau similar

#### 11. Forecasting System
- [ ] **Controller**: Buat `ForecastingController.php`
- [ ] **Algorithm**: Simple forecasting algorithm
- [ ] **Views**: Forecasting dashboard

#### 12. Inventory Optimization
- [ ] **Controller**: Buat `InventoryOptimizationController.php`
- [ ] **Algorithm**: Optimization suggestions
- [ ] **Views**: Optimization dashboard

---

## 🛠️ TECHNICAL TASKS

### Database & Models
- [ ] **Review Migrations**: Pastikan semua table sesuai struktur_table.md
- [ ] **Model Relationships**: Setup semua relasi antar model
- [ ] **Seeders**: Buat seeders untuk data dummy
- [ ] **Factories**: Buat factories untuk testing

### Frontend & UI
- [ ] **Consistent Design**: Pastikan semua view menggunakan template yang sama
- [ ] **DataTables**: Standardize DataTables implementation
- [ ] **Charts**: Implement Chart.js untuk reports
- [ ] **Responsive**: Pastikan mobile-friendly

### Integration & Automation
- [ ] **Auto Calculations**: Implement auto calculation untuk live_stock
- [ ] **Event Listeners**: Setup events untuk auto update
- [ ] **Schedulers**: Setup Laravel scheduler untuk tasks
- [ ] **Notifications**: Implement notification system

### Testing & Quality
- [ ] **Unit Tests**: Buat unit tests untuk controllers
- [ ] **Feature Tests**: Buat feature tests untuk workflows
- [ ] **Code Review**: Review dan refactor existing code
- [ ] **Documentation**: Update documentation

---

## 📅 TIMELINE ESTIMASI

### **Week 1-2: Purchase Management**
- Purchase Bahan Baku
- Purchase Stiker
- Inventory Bahan Baku

### **Week 3-4: Stock Management**
- Stock Bahan Baku
- Stock Opname

### **Week 5-6: Production & Reports**
- Production Planning
- Basic Reports (Stock Movement, Low Stock Alert)

### **Week 7-8: Advanced Features**
- Production Performance Report
- Material Usage Analysis

### **Week 9-10: Polish & Testing**
- Testing
- Bug fixes
- Documentation
- Deployment preparation

---

## 🎯 QUICK WINS (Bisa Dikerjakan Kapan Saja)

- [ ] **Fix Menu Icons**: Update icons di menu-list.blade.php
- [ ] **Add Breadcrumbs**: Implement breadcrumbs di semua pages
- [ ] **Improve Error Handling**: Better error messages
- [ ] **Add Loading States**: Loading indicators untuk AJAX
- [ ] **Optimize Queries**: Review dan optimize database queries
- [ ] **Add Tooltips**: Help tooltips untuk user guidance
- [ ] **Implement Search**: Global search functionality
- [ ] **Add Filters**: Advanced filtering options
- [ ] **Export Features**: Add export to Excel/PDF di semua reports
- [ ] **Print Features**: Add print functionality

---

## 📝 NOTES

- Prioritas utama adalah melengkapi controller yang sudah ada
- Gunakan pattern yang sudah ada untuk consistency
- Pastikan integration dengan database structure yang sudah didefinisikan
- Focus pada user experience dan data accuracy
- Implement proper error handling dan validation
- Add proper logging untuk audit trail

---

**Last Updated**: [Current Date]
**Status**: In Progress
**Next Priority**: Purchase Management (PurchaseController.php) 