# TeaWare Flow: Bahan Baku → Produk Jadi

## Overview
TeaWare adalah produk fisik (bukan teh) yang dijual sebagai aksesoris dan peralatan untuk menyeduh teh. Berbeda dengan produk teh yang membutuhkan banyak bahan baku, TeaWare umumnya memiliki hubungan 1:1 antara bahan baku dan produk jadi.

## Mapping Bahan Baku ke Produk Jadi

### 📋 **TEA BAGS & ACCESSORIES**
| SKU | Bahan Baku | Produk Jadi | Satuan | Kategori |
|-----|------------|-------------|---------|----------|
| TA00 | TEA BAG | TEA BAG | PCS | TEAWARE |

### 🍵 **BOTTLES & INFUSERS**
| SKU | Bahan Baku | Produk Jadi | Satuan | Kategori |
|-----|------------|-------------|---------|----------|
| TA01 | INFUSER BOTTLE 420 ML SILVER | INFUSER BOTTLE 420 ML SILVER | PCS | TEAWARE |

### 🔧 **STRAINERS & TOOLS**
| SKU | Bahan Baku | Produk Jadi | Satuan | Kategori |
|-----|------------|-------------|---------|----------|
| TA02 | STRAINER STAINLESS | STRAINER STAINLESS | PCS | TEAWARE |
| TA07 | STAINLESS STRAW | STAINLESS STRAW | PCS | TEAWARE |

### 🫖 **TEAPOTS**
| SKU | Bahan Baku | Produk Jadi | Satuan | Kategori |
|-----|------------|-------------|---------|----------|
| TA03 | IRON TEAPOT 600 ML | IRON TEAPOT 600 ML | PCS | TEAWARE |
| TA04 | IRON TEAPOT 800 ML | IRON TEAPOT 800 ML | PCS | TEAWARE |
| TA05 | IRON TEAPOT 12 LITER | IRON TEAPOT 1.2 LITER | PCS | TEAWARE |

### 🍃 **MATCHA EQUIPMENT**
| SKU | Bahan Baku | Produk Jadi | Satuan | Kategori |
|-----|------------|-------------|---------|----------|
| TA06 | MATCHA WHISK ELECTRIC | MATCHA WHISK ELECTRIC | PCS | TEAWARE |
| TA08 | MATCHA WHISK 100 PRONGS | MATCHA WHISK 100 PRONGS | PCS | TEAWARE |
| TA12 | MATCHA SPOON CHASAKU | MATCHA SPOON CHASAKU | PCS | TEAWARE |

### 🥛 **CONTAINERS & GLASSES**
| SKU | Bahan Baku | Produk Jadi | Satuan | Kategori |
|-----|------------|-------------|---------|----------|
| TA09 | TEKO PITCHER 1 L | TEKO PITCHER 1 L | PCS | TEAWARE |
| TA10 | TOPLES KACA 280 ML | TOPLES KACA 280 ML | PCS | TEAWARE |
| TA11 | GELAS 3IN1 320 ML | GELAS 3IN1 320 ML | PCS | TEAWARE |

---

## 🔄 **Production Flow untuk TeaWare**

### **1. Procurement (Pengadaan)**
```
Bahan Baku TeaWare → Inventory Bahan Baku
```
- Import/pembelian peralatan teh jadi
- Tidak ada proses manufacturing internal
- Direct procurement dari supplier

### **2. Quality Control**
```
Incoming Goods → Quality Check → Approved Stock
```
- Pemeriksaan kondisi fisik
- Test fungsionalitas (untuk electronic items)
- Packaging integrity check

### **3. Processing (Simple)**
```
Bahan Baku → Cleaning/Labeling → Finished Goods
```
- Pembersihan produk
- Aplikasi label Tea Heaven (jika diperlukan)
- Repackaging ke packaging Tea Heaven

### **4. Stock Management**
```
Finished Goods → Live Stock → Sales
```
- 1:1 ratio dari bahan baku ke finished goods
- Tidak ada waste atau defective (kecuali kerusakan)
- Tracking per unit (PCS)

---

## 📊 **Karakteristik TeaWare dalam Sistem**

### **Stok Management:**
- **Stok Awal**: Manual input berdasarkan inventory fisik
- **Stok Masuk**: Otomatis dari Catatan Produksi (biasanya 1:1 dengan bahan baku)
- **Stok Keluar**: Otomatis dari History Sales Scanner
- **Defective**: Manual input untuk barang rusak/cacat
- **Live Stock**: Stok Awal + Stok Masuk - Stok Keluar - Defective

### **Production Notes:**
- Minimal processing required
- No complex recipes or formulations
- Simple quality control checks
- Direct packaging to finished goods

### **Sales Considerations:**
- Higher margin products
- Lower volume compared to tea products
- Seasonal demand (gift seasons, new tea enthusiasts)
- Bundling opportunities with tea products

---

## 🎯 **Rekomendasi Implementation**

### **1. Inventory Tracking**
- Gunakan serial numbers untuk high-value items (teapots, electric whisks)
- Batch tracking untuk consumables (tea bags)

### **2. Quality Control**
- Visual inspection checklist
- Functional testing for electronic items
- Packaging quality standards

### **3. Sales Strategy**
- Bundle TeaWare dengan tea products
- Gift package options
- Seasonal promotions

### **4. System Integration**
- Same finished goods management system
- Special handling for warranty items
- Return/exchange policies for defective items

---

**📝 Note**: TeaWare products memiliki karakteristik berbeda dari produk teh karena merupakan barang fisik yang tidak dikonsumsi. Sistem tracking harus disesuaikan untuk menangani warranty, return, dan lifecycle yang lebih panjang. 