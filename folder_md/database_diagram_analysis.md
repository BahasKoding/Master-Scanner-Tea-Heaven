# Master Scanner Tea Heaven - Database Schema Analysis

## Overview
Database schema untuk sistem Master Scanner Tea Heaven yang mencakup manajemen produk, stok, produksi, penjualan, dan sistem stiker.

## Database Diagram Code untuk dbdiagram.io

```sql
// Core Business Tables

Table products {
  id bigint [pk, increment]
  category_product integer [note: 'Product category (1-11)']
  sku varchar(255) [unique, note: 'Stock Keeping Unit']
  packaging varchar(255) [note: 'Packaging type']
  name_product varchar(255) [null, note: 'Product name']
  created_at timestamp
  updated_at timestamp
  
  Note: 'Core product table with categories like Classic Tea, Pure Tisane, etc.'
}

Table stickers {
  id bigint [pk, increment]
  product_id bigint [ref: > products.id]
  ukuran varchar(255) [note: 'Sticker size']
  jumlah varchar(255) [note: 'Sticker quantity']
  created_at timestamp
  updated_at timestamp
  
  Note: 'Stickers associated with products - affects stock management'
}

Table finished_goods {
  id bigint [pk, increment]
  id_product bigint [ref: > products.id]
  stok_awal integer [note: 'Initial stock']
  stok_masuk integer [note: 'Stock in (from production)']
  stok_keluar integer [note: 'Stock out (from sales)']
  defective integer [note: 'Defective items']
  live_stock integer [note: 'Current available stock']
  created_at timestamp
  updated_at timestamp
  
  Note: 'Stock management for finished goods. Formula: live_stock = stok_awal + stok_masuk - stok_keluar - defective'
}

// Production Management

Table bahan_bakus {
  id bigint [pk, increment]
  kategori integer [note: 'Raw material category (1-19)']
  sku_induk varchar(255) [note: 'Parent SKU']
  nama_barang varchar(255) [note: 'Item name']
  satuan varchar(255) [note: 'Unit of measurement']
  created_at timestamp
  updated_at timestamp
  
  Note: 'Raw materials used in production'
}

Table catatan_produksis {
  id bigint [pk, increment]
  product_id bigint [ref: > products.id]
  packaging varchar(255) [note: 'Production packaging']
  quantity integer [note: 'Production quantity']
  sku_induk json [note: 'Array of bahan_baku IDs']
  gramasi json [note: 'Array of gramasi values per bahan_baku']
  total_terpakai json [note: 'Array of total used per bahan_baku']
  created_at timestamp
  updated_at timestamp
  
  Note: 'Production records tracking raw materials usage'
}

// Sales Management

Table history_sales {
  id bigint [pk, increment]
  no_resi varchar(255) [note: 'Receipt number']
  no_sku json [note: 'Array of SKUs sold (legacy format)']
  qty json [note: 'Array of quantities (legacy format)']
  created_at timestamp
  updated_at timestamp
  deleted_at timestamp [null, note: 'Soft delete']
  
  Note: 'Sales history with legacy JSON format and new detail table format'
}

Table history_sale_details {
  id bigint [pk, increment]
  history_sale_id bigint [ref: > history_sales.id, note: 'CASCADE delete']
  product_id bigint [ref: > products.id, note: 'RESTRICT delete']
  quantity integer [default: 0, note: 'Quantity sold']
  created_at timestamp
  updated_at timestamp
  
  Note: 'Detailed sales records for new format'
}

// User Management and Security

Table users {
  id bigint [pk, increment]
  name varchar(255)
  email varchar(255) [unique]
  email_verified_at timestamp [null]
  password varchar(255)
  otp varchar(4) [null, note: 'One-time password']
  otp_expires_at timestamp [null]
  password_reset_token varchar(255) [null]
  remember_token varchar(100) [null]
  created_at timestamp
  updated_at timestamp
}

Table permissions {
  id bigint [pk, increment]
  name varchar(255)
  guard_name varchar(255)
  created_at timestamp
  updated_at timestamp
  
  indexes {
    (name, guard_name) [unique]
  }
}

Table roles {
  id bigint [pk, increment]
  name varchar(255)
  guard_name varchar(255)
  created_at timestamp
  updated_at timestamp
  
  indexes {
    (name, guard_name) [unique]
  }
}

Table model_has_permissions {
  permission_id bigint [ref: > permissions.id, note: 'CASCADE delete']
  model_type varchar(255)
  model_id bigint
  
  indexes {
    (permission_id, model_id, model_type) [pk]
    (model_id, model_type)
  }
}

Table model_has_roles {
  role_id bigint [ref: > roles.id, note: 'CASCADE delete']
  model_type varchar(255)
  model_id bigint
  
  indexes {
    (role_id, model_id, model_type) [pk]
    (model_id, model_type)
  }
}

Table role_has_permissions {
  permission_id bigint [ref: > permissions.id, note: 'CASCADE delete']
  role_id bigint [ref: > roles.id, note: 'CASCADE delete']
  
  indexes {
    (permission_id, role_id) [pk]
  }
}

// Activity Logging

Table activities {
  id bigint [pk, increment]
  category varchar(255) [not null, note: 'Activity category']
  action varchar(255) [not null, note: 'Action performed']
  action_id bigint [null, note: 'Related record ID']
  note text [null, note: 'Activity description']
  user_id bigint [null, note: 'User who performed action']
  created_at timestamp
  updated_at timestamp
}

// Laravel Framework Tables

Table password_resets {
  email varchar(255)
  token varchar(255)
  created_at timestamp [null]
  
  indexes {
    email
  }
}

Table password_reset_tokens {
  email varchar(255) [pk]
  token varchar(255)
  created_at timestamp [null]
}

Table personal_access_tokens {
  id bigint [pk, increment]
  tokenable_type varchar(255)
  tokenable_id bigint
  name varchar(255)
  token varchar(64) [unique]
  abilities text [null]
  last_used_at timestamp [null]
  expires_at timestamp [null]
  created_at timestamp
  updated_at timestamp
  
  indexes {
    (tokenable_type, tokenable_id)
  }
}

Table failed_jobs {
  id bigint [pk, increment]
  uuid varchar(255) [unique]
  connection text
  queue text
  payload longtext
  exception longtext
  failed_at timestamp [default: `CURRENT_TIMESTAMP`]
}

Table sessions {
  id varchar(255) [pk]
  user_id bigint [null]
  ip_address varchar(45) [null]
  user_agent text [null]
  payload longtext
  last_activity integer
  
  indexes {
    user_id
    last_activity
  }
}
```

## Relationships Analysis

### Core Business Flow
1. **Products** → Central entity untuk semua operasi
2. **Stickers** → Berelasi dengan products, akan mempengaruhi stock
3. **FinishedGoods** → Manajemen stok per product
4. **CatatanProduksi** → Recording produksi, update stok_masuk di FinishedGoods
5. **HistorySales & HistorySaleDetails** → Recording penjualan, update stok_keluar

### Stock Management Flow
```
Production → CatatanProduksi → FinishedGoods.stok_masuk ↗
                                                        → live_stock calculation
Sales → HistorySaleDetails → FinishedGoods.stok_keluar ↘
```

### Sticker Impact on Stock
- **Current**: Stickers table exists but not integrated with stock system
- **Planned**: Stickers akan mempengaruhi inventory management
- **Implementation needed**: 
  - StickerStock table untuk tracking sticker inventory
  - Integration dengan StockService
  - Validation sticker availability saat assign ke products

## Key Features

### Product Categories
1. CLASSIC TEA COLLECTION
2. PURE TISANE  
3. ARTISAN TEA
4. JAPANESE TEA
5. CHINESE TEA
6. PURE POWDER
7. SWEET POWDER
8. LATTE POWDER
9. CRAFTED TEAS
10. JAPANESE TEABAGS
11. TEA WARE

### Raw Material Categories (19 categories)
- CRAFTED TEAS, LOOSE LEAF TEA, PURE TISANE
- Various packaging types (Pouch, Foil, Vacuum, etc.)
- PRINTING & LABELLING, OUTER PACKAGING

### Stock Formula
```
live_stock = stok_awal + stok_masuk - stok_keluar - defective
```

### Data Integrity Features
- **Observers**: Auto-update stock saat production/sales
- **StockService**: Centralized stock management logic
- **Job**: VerifyStockConsistencyJob untuk data validation
- **Soft Deletes**: History sales dapat di-restore
- **Foreign Key Constraints**: Data integrity enforcement

## Technical Implementation Notes

### JSON Fields Usage
- `catatan_produksis.sku_induk`: Array of bahan_baku IDs
- `catatan_produksis.gramasi`: Array of gramasi per bahan_baku  
- `catatan_produksis.total_terpakai`: Array of usage per bahan_baku
- `history_sales.no_sku`: Legacy array format for SKUs
- `history_sales.qty`: Legacy array format for quantities

### Model Relationships
- **Product**: hasOne FinishedGoods, hasMany CatatanProduksi, hasMany Stickers
- **FinishedGoods**: belongsTo Product
- **CatatanProduksi**: belongsTo Product, custom relationship dengan BahanBaku via JSON
- **Stickers**: belongsTo Product (needs implementation)
- **HistorySales**: hasMany HistorySaleDetails, belongsToMany Products
- **HistorySaleDetails**: belongsTo HistorySale, belongsTo Product

### Security & Permissions
- Role-based access control using Spatie Laravel Permission
- Activity logging untuk audit trail
- User authentication dengan OTP support
- Session management

## Recommendations for Sticker Integration

### 1. Create StickerStock Table
```sql
Table sticker_stocks {
  id bigint [pk, increment]
  sticker_id bigint [ref: > stickers.id]
  stok_awal integer
  stok_masuk integer
  stok_keluar integer
  live_stock integer
  created_at timestamp
  updated_at timestamp
}
```

### 2. Extend StockService
- Add methods untuk sticker stock management
- Implement validation saat assign stickers ke products
- Update sticker stock saat products di-produce atau di-sell

### 3. Update Sticker Model
- Add relationships ke StickerStock
- Add fillable fields dan casting
- Implement observers untuk auto-stock management

### 4. Integration Points
- Production: Check sticker availability
- Sales: Update sticker stock saat product terjual
- Inventory: Real-time sticker stock monitoring

Reference: [dbdiagram.io](https://dbdiagram.io/d) untuk visualisasi database schema ini. 