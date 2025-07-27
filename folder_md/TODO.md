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
- [x] Purchase Bahan Baku (`PurchaseController.php`)
- [x] Inventory Bahan Baku (`InventoryBahanBakuController.php`)
- [x] Stock Consistency Verification System
- [x] Production Data Integration & Analytics

#### User Management & System
- [x] User Management (`UserController.php`)
- [x] Roles & Permissions (`RoleController.php`, `PermissionController.php`)
- [x] Activity Log (`ActivityController.php`)
- [x] Enhanced Error Handling & Logging

---

## 🚀 PRIORITAS 1 - MUDAH (Lengkapi Controller yang Sudah Ada)

### ✅ Stock Opname System (COMPLETED)

#### 1. Stock Opname Implementation - FULL FEATURE ✅ **SELESAI**

##### Database Setup ✅
- [x] **Migration**: Create `stock_opnames` table ✅
  - [x] Fields: id, type (bahan_baku/finished_goods/sticker), tanggal_opname, status, created_by, notes ✅
- [x] **Migration**: Create `stock_opname_items` table ✅ 
  - [x] Fields: id, opname_id, item_id, item_name, stok_sistem, stok_fisik, selisih, satuan ✅
- [x] **Migration**: Create `stock_adjustments` table ✅
  - [x] Fields: id, opname_id, item_id, adjustment_type, old_stock, new_stock, reason ✅
- [x] **Model**: Create `StockOpname.php` with full relationships ✅
- [x] **Model**: Create `StockOpnameItem.php` with full relationships ✅
- [x] **Model**: Create `StockAdjustment.php` with relationships ✅

##### Controller & Service Layer ✅
- [x] **Controller**: Create `StockOpnameController.php` ✅
  - [x] `index()` - List opname sessions with advanced filtering ✅
  - [x] `create()` - Form pilih jenis opname (Bahan Baku/Finished Goods/Sticker) ✅
  - [x] `store()` - Simpan opname baru & auto-populate items ✅
  - [x] `show()` - Input stok fisik & lihat selisih (with real-time updates) ✅
  - [x] `edit()` - Edit opname details ✅
  - [x] `update()` - Update stok fisik (individual & bulk) ✅
  - [x] `process()` - Finalisasi opname (update stok sistem jika perlu) ✅
  - [x] `varianceAnalysis()` - Variance analysis with recommendations ✅
  - [x] `statistics()` - Statistics with filtering ✅
- [x] **Service**: Create `StockOpnameService.php` ✅
  - [x] Business logic separation ✅
  - [x] Auto-population logic ✅
  - [x] Variance calculation & categorization ✅
  - [x] Recommendations system ✅
  - [x] Stock adjustment processing ✅

##### Views & UI ✅
- [x] **Views**: Create `resources/views/stock-opname/` ✅
  - [x] `index.blade.php` - List opname sessions (Advanced DataTable) ✅
  - [x] `create.blade.php` - Form pilih jenis & tanggal opname ✅
  - [x] `show.blade.php` - Input stok fisik & tampilkan selisih (Enhanced UI) ✅
  - [x] `edit.blade.php` - Edit opname details ✅
  - [x] `variance-report.blade.php` - Variance analysis report ✅
  - [x] `adjustment-history.blade.php` - Stock adjustment history ✅
- [x] **Partials**: Create reusable components ✅
  - [x] `item-row.blade.php` - Item row component ✅
  - [x] `variance-summary.blade.php` - Variance summary component ✅
  - [x] `adjustment-modal.blade.php` - Adjustment modal component ✅

##### Menu Integration ✅
- [x] **Menu**: Add to Transaction Tables section ✅
  - [x] Update `menu-list.blade.php` - tambah "Stock Opname" ✅
  - [x] Complete routes di `web.php` ✅
  - [x] Advanced permissions (Stock Opname List, Create, Edit, Process) ✅

##### Core Features ✅
- [x] **Opname Bahan Baku**: ✅
  - [x] Auto-populate dari `tb_inventory_bahan_baku` ✅
  - [x] Input stok fisik manual (integer-only) ✅
  - [x] Hitung selisih otomatis dengan real-time updates ✅
- [x] **Opname Finished Gophp arods**: ✅
  - [x] Auto-populate dari `tb_finished_goods` ✅
  - [x] Input stok fisik manual (integer-only) ✅
  - [x] Hitung selisih otomatis dengan real-time updates ✅
- [x] **Opname Sticker**: ✅
  - [x] Auto-populate dari `tb_stiker` ✅
  - [x] Input stok fisik manual (integer-only) ✅
  - [x] Hitung selisih otomatis dengan real-time updates ✅
- [x] **Advanced Features**: ✅
  - [x] Variance analysis dengan tolerance settings ✅
  - [x] Priority-based recommendations ✅
  - [x] Stock adjustment dengan audit trail ✅
  - [x] Comprehensive reporting ✅
  - [x] Real-time AJAX updates ✅
  - [x] Advanced filtering & pagination ✅
  - [x] Indonesian number formatting (1.000) ✅
  - [x] Empty state handling ✅
  - [x] Responsive design ✅

##### Technical Enhancements ✅
- [x] **Number Formatting**: Indonesian format with thousands separator ✅
- [x] **Integer-Only Input**: No decimal support for stock values ✅
- [x] **Real-time Updates**: AJAX-based real-time calculations ✅
- [x] **Advanced Filtering**: Search, status, pagination ✅
- [x] **Empty State Handling**: User-friendly messages when no data ✅
- [x] **Error Handling**: Comprehensive error handling ✅
- [x] **Validation**: Request validation classes ✅
- [x] **Permissions**: Role-based access control ✅

##### Status: 🔄 **ENHANCEMENT IN PROGRESS**
- **Database & Model**: ✅ Completed
- **Controller & Service**: ✅ Completed  
- **Views & UI**: ✅ Completed
- **Menu Integration**: ✅ Completed
- **Advanced Features**: ✅ Completed
- **Testing & Polish**: ✅ Completed
- **Dynamic Stock Calculation**: ✅ **IMPLEMENTED** (Phase 1)
- **Real-time Stock Movement**: ✅ **IMPLEMENTED** (Phase 2)
- **Stok Awal Integration**: 🔄 **IN PROGRESS** (Phase 3)

#### 2. Stock Opname Advanced Features - ENHANCEMENT REQUIRED ⚠️

##### Critical Issues to Fix 🚨
- [x] **Dynamic Stock Calculation**: ✅ **COMPLETED** (Phase 1)
  - [x] Implement real-time stock calculation during opname period ✅
  - [x] Replace snapshot-based `stok_sistem` with dynamic calculation ✅
  - [x] Handle concurrent transactions during opname period ✅
  - [x] Add getCurrentLiveStock() method in StockOpnameService ✅
  - [x] Add refreshStokSistem() with change notifications ✅
  - [x] Add checkConcurrentTransactions() method ✅
  - [x] Add getStockMovementSummary() method ✅

- [x] **Real-time Stock Movement**: ✅ **COMPLETED** (Phase 2)
  - [x] Fix updateSystemStock() to use physical count as absolute value ✅
  - [x] Implement proper stock movement calculation ✅
  - [x] Add stock adjustment logging with audit trail ✅
  - [x] Handle negative stock scenarios correctly ✅
  - [x] Add createStockAdjustmentLog() for comprehensive audit trail ✅
  - [x] Implement database transaction safety ✅
  - [x] Enhanced error handling and rollback mechanisms ✅

- [ ] **Stok Awal Integration**: 🔄 **IN PROGRESS** (Phase 3)
  - [ ] **Kondisi 1**: Auto-reset stok awal setelah opname selesai
    - [ ] Reset semua stok awal di finished goods/inventory bahan baku
    - [ ] Ambil stok awal dari stok fisik (hasil opname)
    - [ ] Otomatis update ke semua record terkait saat status = "selesai"
    - [ ] Integration dengan `tb_finished_goods.stok_awal`
    - [ ] Integration dengan `tb_inventory_bahan_baku.stok_awal`
    - [ ] Integration dengan `tb_stiker.stok_awal`
    - [ ] **NEW**: Implement resetStokAwalFromOpname() method
  - [ ] **Kondisi 2**: Per-row update langsung ke stok awal
    - [ ] Stok fisik (opname) langsung jadi stok awal saat update per baris
    - [ ] Real-time update tanpa perlu finalisasi
    - [ ] Apply ke barang jadi/bahan baku yang bersangkutan
    - [ ] Maintain audit trail untuk setiap perubahan stok awal
    - [ ] **NEW**: Implement updateStokAwalPerRow() method
    - [ ] **NEW**: AJAX integration untuk immediate stok awal update

##### Enhancement Features 🔧
- [ ] **Concurrent Opname Protection**:
  - [ ] Add database locks during stock updates
  - [ ] Prevent multiple opname sessions for same item type
  - [ ] Add warning for ongoing transactions during opname

- [ ] **Advanced Variance Analysis**:
  - [ ] Real-time variance calculation based on current stock
  - [ ] Historical variance trending
  - [ ] Predictive variance alerts
  - [ ] Category-wise variance analysis

- [ ] **Stock Movement Integration**:
  - [ ] Integration with StockService for real-time updates
  - [ ] Auto-sync with FinishedGoodsService
  - [ ] Purchase/Sale transaction impact during opname
  - [ ] Production impact calculation

- [ ] **Audit & Compliance**:
  - [ ] Complete audit trail for all stock movements
  - [ ] Stock adjustment approval workflow
  - [ ] Compliance reporting for stock discrepancies
  - [ ] Manager approval for significant variances (>20%)

- [ ] **Performance Optimization**:
  - [ ] Batch processing for large item counts
  - [ ] Background processing for stock calculations
  - [ ] Caching for frequently accessed stock data
  - [ ] Database query optimization

##### Technical Implementation Plan 📋
1. **Phase 1 - Critical Fixes**: ✅ **COMPLETED**
   - [x] Modify StockOpnameService::populateItems() for dynamic stock ✅
   - [x] Fix StockOpnameService::updateSystemStock() logic ✅
   - [x] Update StockOpnameItem::calculateCorrectVariance() for real-time ✅
   - [x] Add proper error handling and rollback mechanisms ✅
   - [x] **IMPLEMENTED**: getCurrentLiveStock() method ✅
   - [x] **IMPLEMENTED**: refreshStokSistem() with notifications ✅
   - [x] **IMPLEMENTED**: checkConcurrentTransactions() method ✅
   - [x] **IMPLEMENTED**: Fixed column name mismatches (product_id, bahan_baku_id) ✅
   - [x] **IMPLEMENTED**: Enhanced updateBahanBakuStock() with absolute values ✅
   - [x] **IMPLEMENTED**: Enhanced updateFinishedGoodsStock() with absolute values ✅
   - [x] **IMPLEMENTED**: Enhanced updateStickerStock() with absolute values ✅
   - [x] **IMPLEMENTED**: createStockAdjustmentLog() for comprehensive audit trail ✅
   - [x] **IMPLEMENTED**: Database transaction safety in updateSystemStock() ✅
   - [ ] **NEXT**: Implement resetStokAwalFromOpname() method
     - [ ] Create method di StockOpnameService untuk kondisi 1
     - [ ] Auto-trigger saat opname status berubah ke "selesai"
     - [ ] Update stok_awal di tb_finished_goods, tb_inventory_bahan_baku, tb_stiker
   - [ ] **NEXT**: Implement updateStokAwalPerRow() method
     - [ ] Create method di StockOpnameService untuk kondisi 2
     - [ ] Real-time update saat user input stok fisik per baris
     - [ ] AJAX integration untuk immediate stok awal update

2. **Phase 2 - Enhanced Features**: ✅ **COMPLETED**
   - [x] Implement stock movement logging ✅
   - [x] Add concurrent transaction protection ✅
   - [x] Create advanced variance analysis ✅
   - [x] Enhanced notification system for stock changes ✅
   - [x] Comprehensive audit trail system ✅
   - [x] Transaction safety with rollback mechanisms ✅

3. **Phase 3 - Stok Awal Integration**: 🔄 **IN PROGRESS**
   - [ ] Implement resetStokAwalFromOpname() method
   - [ ] Implement updateStokAwalPerRow() method
   - [ ] AJAX integration for real-time stok_awal updates
   - [ ] Auto-trigger mechanism when opname status = "selesai"
   - [ ] Integration with tb_finished_goods.stok_awal
   - [ ] Integration with tb_inventory_bahan_baku.stok_awal
   - [ ] Integration with tb_stiker.stok_awal

4. **Phase 4 - Integration & Optimization**: ⏳ **PLANNED**
   - [ ] Full integration with existing stock services
   - [ ] Performance optimization
   - [ ] Advanced reporting and analytics
   - [ ] Mobile-responsive enhancements

##### Priority Level: 🟡 **MEDIUM** (Updated from Critical)
**Status Update**: Critical issues (Phase 1 & 2) have been resolved ✅
- ✅ **FIXED**: Dynamic stock calculation now uses real-time values
- ✅ **FIXED**: Stock update logic now uses absolute physical count values
- ✅ **FIXED**: Comprehensive audit trail implemented
- ✅ **FIXED**: Auto-populate bug resolved (column name mismatches)
- ✅ **FIXED**: Notification system for stock changes

**Remaining Work**: Phase 3 - Stok Awal Integration
**Timeline**: 1 week for Phase 3 completion

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