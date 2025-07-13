# Activity Logger - Cara Penggunaan

## Import Helper
```php
use App\Helpers\ActivityLogger;
```

## Contoh Penggunaan

### 1. Login/Logout Activities
```php
// Di controller login
ActivityLogger::auth('login', 'User berhasil login');

// Di controller logout  
ActivityLogger::auth('logout', 'User logout dari sistem');
```

### 2. User Management Activities
```php
// Saat membuat user baru
ActivityLogger::user('create', 'User baru dibuat: ' . $user->name);

// Saat update user
ActivityLogger::user('update', 'Data user diupdate: ' . $user->name);

// Saat hapus user
ActivityLogger::user('delete', 'User dihapus: ' . $user->name);
```

### 3. Product Activities
```php
// Saat menambah produk
ActivityLogger::product('create', 'Produk baru: ' . $product->name);

// Saat update stok
ActivityLogger::product('stock_update', 'Stok produk diupdate: ' . $product->name);
```

### 4. Sales Activities
```php
// Saat transaksi baru
ActivityLogger::sale('create', 'Transaksi baru dengan total: Rp ' . number_format($sale->total));

// Saat scan barcode
ActivityLogger::sale('scan', 'Scan produk: ' . $product->sku);
```

### 5. System Activities
```php
// Saat backup database
ActivityLogger::system('backup', 'Database backup completed');

// Saat maintenance
ActivityLogger::system('maintenance', 'System maintenance started');
```

## Catatan
- Helper ini otomatis menyimpan user_id dari user yang sedang login
- Error logging tidak akan mengganggu aplikasi utama
- Activity disimpan dengan timestamp otomatis 