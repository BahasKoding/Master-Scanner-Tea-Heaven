# 🫖 Master Scanner Tea Heaven
### Sistem Manajemen Inventori & Produksi Tea Heaven

[![Laravel](https://img.shields.io/badge/Laravel-11.x-red.svg)](https://laravel.com)
[![Bootstrap](https://img.shields.io/badge/Bootstrap-5.3-purple.svg)](https://getbootstrap.com)
[![PHP](https://img.shields.io/badge/PHP-8.2+-blue.svg)](https://php.net)
[![License](https://img.shields.io/badge/License-MIT-green.svg)](LICENSE)

## 📖 Deskripsi Aplikasi

**Master Scanner Tea Heaven** adalah sistem informasi manajemen inventory terintegrasi yang dirancang khusus untuk mengelola seluruh aspek operasional bisnis tea & tisane. Sistem ini mengotomatisasi proses dari procurement bahan baku hingga penjualan produk jadi dengan teknologi scanner untuk efisiensi maksimal.

### 🎯 Tujuan Aplikasi

- **Otomatisasi Inventory**: Mengelola stok bahan baku dan produk jadi secara real-time
- **Optimasi Produksi**: Meningkatkan efisiensi proses produksi dengan perencanaan yang tepat
- **Tracking Penjualan**: Memantau penjualan dengan sistem scanner berbasis SKU dan No Resi
- **Analisis Bisnis**: Menyediakan laporan dan analisis untuk pengambilan keputusan

---

## ✨ Fitur Utama

### 🏭 **Manajemen Produksi**
- **Catatan Produksi**: Pencatatan detail proses produksi dengan bahan baku dan output
- **Planning Produksi**: Perencanaan produksi berdasarkan ketersediaan bahan baku
- **Quality Control**: Tracking defective items dan material efficiency

### 📦 **Manajemen Inventory**
- **Master Data Produk**: Manajemen 11+ kategori produk (Classic Tea, Pure Tisane, dll.)
- **Master Bahan Baku**: Manajemen 19+ kategori bahan baku dengan 280+ item
- **Live Stock Tracking**: Perhitungan stok real-time dengan formula otomatis
- **Low Stock Alert**: Notifikasi otomatis untuk reorder point

### 🛒 **Procurement System**
- **Purchase Bahan Baku**: Manajemen pembelian dengan tracking quality dan retur
- **Purchase Stiker**: Manajemen stiker produk dengan integrasi stok
- **Supplier Management**: Pengelolaan data supplier dan vendor

### 🔍 **Scanner System**
- **Barcode Scanner**: Scanner otomatis untuk No Resi dan SKU produk
- **Real-time Validation**: Validasi instant untuk mencegah duplikasi
- **Auto-submit**: Penyimpanan otomatis dengan countdown timer
- **Multi-SKU Support**: Input multiple SKU dalam satu transaksi

### 📊 **Dashboard & Reporting**
- **Analytics Dashboard**: Overview KPI bisnis dan stock status
- **Sales Report**: Laporan penjualan detail dengan export Excel/PDF
- **Stock Movement**: Tracking pergerakan stok masuk dan keluar
- **Production Report**: Analisis efisiensi produksi dan material usage

### 👥 **User Management**
- **Role-based Access**: Sistem permission dengan multiple roles
- **Activity Log**: Tracking semua aktivitas user
- **Multi-user Support**: Dukungan tim dengan akses terkontrol

---

## 🛠️ Tech Stack

### Backend
- **Framework**: Laravel 11.x
- **Database**: MySQL/MariaDB
- **PHP**: 8.2+
- **Authentication**: Laravel Sanctum + UI

### Frontend
- **CSS Framework**: Bootstrap 5.3
- **JavaScript**: jQuery + Vanilla JS
- **UI Components**: DataTables, SweetAlert2, Chart.js
- **Icons**: Feather Icons, Font Awesome, Tabler Icons

### Build Tools
- **Asset Bundling**: Vite
- **CSS Preprocessing**: Sass
- **Package Manager**: NPM/Yarn

### Third-party Integrations
- **PDF Generation**: DomPDF
- **Image Processing**: Intervention Image
- **Excel Export**: Laravel Excel
- **Permissions**: Spatie Laravel Permission

---

## 🚀 Quick Start

### Prerequisites
- PHP 8.2 atau lebih tinggi
- Composer
- Node.js & NPM
- MySQL/MariaDB
- Web Server (Apache/Nginx)

### Installation

1. **Clone Repository**
```bash
git clone https://github.com/your-username/master-scanner-tea-heaven.git
cd master-scanner-tea-heaven
```

2. **Install Dependencies**
```bash
# Backend dependencies
composer install

# Frontend dependencies
npm install
```

3. **Environment Setup**
```bash
# Copy environment file
cp .env.example .env

# Generate application key
php artisan key:generate
```

4. **Database Configuration**
```bash
# Edit .env file with your database credentials
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=tea_heaven_db
DB_USERNAME=your_username
DB_PASSWORD=your_password
```

5. **Database Migration & Seeding**
```bash
# Run migrations
php artisan migrate

# Seed database with master data
php artisan db:seed
```

6. **Build Assets**
```bash
# Development build
npm run dev

# Production build
npm run build
```

7. **Start Development Server**
```bash
php artisan serve
```

Visit `http://localhost:8000` untuk mengakses aplikasi.

---

## 🗃️ Struktur Database

### Core Tables
- **products**: Master data produk (11 kategori)
- **bahan_bakus**: Master bahan baku (19 kategori, 280+ items)
- **finished_goods**: Manajemen stok produk jadi
- **catatan_produksi**: Record produksi harian
- **history_sales**: Transaksi penjualan

### Support Tables
- **stickers**: Manajemen stiker produk
- **inventory_bahan_baku**: Inventory bahan baku
- **purchase_barang**: Pembelian bahan baku
- **purchase_stiker**: Pembelian stiker

### System Tables
- **users**: User management
- **roles & permissions**: Role-based access control
- **activity_log**: System audit trail

---

## 📱 Fitur Mobile-Friendly

Aplikasi ini dirancang responsive dengan dukungan mobile-first:
- **Adaptive Layout**: Otomatis menyesuaikan dengan ukuran layar
- **Touch-Friendly**: Interface yang optimal untuk perangkat touch
- **Mobile Scanner**: Barcode scanner yang compatible dengan kamera mobile
- **Offline Support**: Basic offline functionality untuk operasi kritis

---

## 🔧 Development Roadmap

### ✅ **Sudah Tersedia**
- ✅ Master Data Management
- ✅ Production Recording
- ✅ Sales Scanner System
- ✅ Purchase Management
- ✅ Basic Reporting
- ✅ User Management

### 🚧 **Dalam Pengembangan**
- 🚧 Production Planning Module
- 🚧 Advanced Analytics
- 🚧 Stock Opname System
- 🚧 Forecasting Algorithm

### 📋 **Future Plans**
- 📋 API Integration
- 📋 Multi-warehouse Support
- 📋 Mobile Application
- 📋 IoT Integration

---

## 🤝 Contributing

Kami menyambut kontribusi dari developer! Silakan ikuti panduan berikut:

1. Fork repository ini
2. Buat feature branch (`git checkout -b feature/AmazingFeature`)
3. Commit perubahan (`git commit -m 'Add some AmazingFeature'`)
4. Push ke branch (`git push origin feature/AmazingFeature`)
5. Buka Pull Request

---

## 📄 License

Project ini menggunakan lisensi MIT. Lihat file [LICENSE](LICENSE) untuk detail lengkap.

---

## 📞 Support & Contact

- **Documentation**: [Wiki Pages](https://github.com/your-username/master-scanner-tea-heaven/wiki)
- **Issues**: [GitHub Issues](https://github.com/your-username/master-scanner-tea-heaven/issues)
- **Email**: support@teaheaven.com

---

## 🙏 Acknowledgments

- **Light Able Admin Template**: Base admin template dari phoenixcoded
- **Laravel Community**: Framework dan ecosystem yang luar biasa
- **Bootstrap Team**: CSS framework yang powerful
- **Tea Heaven Team**: Untuk requirements dan testing

---

<div align="center">

**Dibuat dengan ❤️ untuk Tea Heaven Indonesia**

*Sistem ini dirancang khusus untuk mengoptimalkan operasional bisnis tea & tisane dengan teknologi modern dan user experience yang intuitif.*

</div>