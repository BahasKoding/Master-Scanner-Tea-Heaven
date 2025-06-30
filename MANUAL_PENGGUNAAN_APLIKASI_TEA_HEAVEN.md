# Manual Penggunaan Aplikasi Tea Heaven Inventory Management

## Daftar Isi
1. [Pengenalan Sistem](#pengenalan-sistem)
2. [Dashboard](#dashboard)
3. [Master Data](#master-data)
4. [Transaction Tables](#transaction-tables)
5. [Sales & Transactions](#sales--transactions)
6. [Reports & Analytics](#reports--analytics)
7. [System Management](#system-management)
8. [Tips Umum](#tips-umum)
9. [Error Handling](#error-handling)

---

## Pengenalan Sistem

Tea Heaven Inventory Management adalah sistem manajemen inventory/stok untuk industri teh yang fokus pada tracking stock masuk dan stock keluar. Sistem ini dirancang untuk mengelola pergerakan stok dari bahan baku hingga produk jadi dengan pencatatan yang akurat.

### Konsep Dasar Stock Tracking:

**STOCK MASUK** (Penambahan Inventory):
- **Purchase Items**: Pembelian bahan baku dari supplier
- **Purchase Stiker**: Pembelian stiker packaging
- **Catatan Produksi**: Hasil produksi (produk jadi bertambah)

**STOCK KELUAR** (Pengurangan Inventory):
- **Catatan Produksi**: Penggunaan bahan baku untuk produksi
- **Scanner Sales**: Penjualan produk jadi ke customer

**LIVE STOCK CALCULATION**:
Sistem menghitung stok real-time dengan formula:
`Live Stock = Stok Awal + Stock Masuk - Stock Keluar - Defect`

### Fitur Utama:
- **Master Data**: Produk dan bahan baku
- **Stock Masuk**: Purchase Items dan Purchase Stiker (tracking barang masuk)
- **Stock Keluar**: Catatan Produksi (penggunaan bahan baku) dan Sales Scanner (produk terjual)
- **Real-time Inventory**: Monitoring stok live dengan kalkulasi otomatis
- **Stock Tracking**: Pencatatan lengkap pergerakan stok masuk dan keluar
- **Reporting**: Laporan stok dan pergerakan inventory
- **User Access Control**: Manajemen pengguna dengan role-based permissions

---

## Dashboard

### Fungsi Utama
Dashboard menampilkan ringkasan data dan statistik penting untuk monitoring operasional harian.

### Cara Menggunakan
1. **Akses Dashboard**: Klik menu "Dashboard" di sidebar atau langsung setelah login
2. **Melihat Ringkasan**: Dashboard menampilkan:
   - Statistik penjualan
   - Status inventory
   - Aktivitas produksi terbaru
   - Alert stok rendah
3. **Navigasi Cepat**: Gunakan widget/card untuk akses cepat ke modul terkait

### Tips
- Dashboard auto-refresh setiap beberapa menit untuk data real-time
- Gunakan filter tanggal untuk melihat data periode tertentu
- Klik pada chart/grafik untuk detail lebih lanjut

---

## Master Data

### 2.1 Products (Produk)

#### Fungsi
Mengelola master data semua produk yang diproduksi/dijual.

#### Cara Menggunakan

**Melihat Data Produk:**
1. Klik "Master Data" → "Products"
2. Gunakan filter untuk mencari produk:
   - Filter berdasarkan kategori
   - Filter berdasarkan label
3. Gunakan search box untuk pencarian cepat
4. Pilih jumlah data per halaman (10/25/50)

**Menambah Produk Baru:**
1. Klik tombol "Tambah Produk"
2. Isi form yang muncul:
   - **Kategori**: Pilih kategori produk (wajib)
   - **SKU**: Masukkan SKU unik (sistem akan validasi duplikasi)
   - **Nama Produk**: Nama lengkap produk (wajib)
   - **Packaging**: Jenis kemasan (wajib)
   - **Label**: Pilih label produk (opsional)
3. Klik "Simpan" atau "Simpan & Tambah Lagi"

**Mengedit Produk:**
1. Klik tombol "Edit" (ikon pensil) pada baris produk
2. Ubah data yang diperlukan
3. Klik "Perbarui"

**Menghapus Produk:**
1. Klik tombol "Delete" (ikon sampah) pada baris produk
2. Konfirmasi penghapusan
3. Data akan dihapus permanen

**Export Data:**
1. Klik tombol "Export Excel"
2. File akan otomatis terdownload
3. Dapat difilter sebelum export

#### Validasi Penting
- SKU harus unik, sistem akan memberikan peringatan jika duplikat
- Semua field bertanda (*) wajib diisi
- Kategori dan Label harus dipilih dari pilihan yang tersedia

---

### 2.2 Bahan Baku

#### Fungsi
Mengelola master data bahan baku yang digunakan dalam produksi.

#### Cara Menggunakan

**Melihat Data Bahan Baku:**
1. Klik "Master Data" → "Bahan Baku"
2. Gunakan filter:
   - Kategori bahan baku
   - SKU Induk
   - Nama barang
   - Satuan
3. Klik "Hapus Filter" untuk reset semua filter

**Menambah Bahan Baku:**
1. Klik "Tambah Bahan Baku Baru"
2. Isi form:
   - **Kategori**: Pilih kategori bahan baku
   - **SKU Induk**: Kode unik bahan baku (akan divalidasi)
   - **Nama Barang**: Nama lengkap bahan baku
   - **Satuan**: Pilih satuan (PCS/GRAM/KG/LITER/ML)
3. Klik "Simpan"

**Mengedit Bahan Baku:**
1. Klik tombol "Edit" pada baris yang diinginkan
2. Form edit akan muncul dengan data yang sudah terisi
3. Ubah data yang diperlukan
4. Klik "Perbarui"

**Menghapus Bahan Baku:**
1. Klik tombol "Delete" pada baris yang diinginkan
2. Konfirmasi penghapusan
3. Data akan dihapus permanen

#### Fitur Khusus
- **Real-time SKU Validation**: Sistem akan memberikan peringatan jika SKU Induk sudah digunakan
- **Reset Button**: Tombol untuk reset kategori dan item dengan cepat
- **Export**: Tersedia export ke Excel, CSV, dan Print

---

## Transaction Tables (Stock Tracking)

### 3.1 Purchase Items - STOCK MASUK

#### Fungsi
Tracking dan pencatatan stock masuk dari pembelian bahan baku dan finished goods dari supplier.

#### Cara Menggunakan

**Melihat Data Purchase:**
1. Klik "All Transactions" → "Purchase Items"
2. Gunakan filter:
   - **Kategori**: Bahan Baku atau Finished Goods
   - **Item**: Pilih item spesifik (otomatis terfilter berdasarkan kategori)
   - **Tanggal**: Rentang tanggal kedatangan barang
3. Klik "Filter" untuk menerapkan atau "Clear" untuk reset

**Menambah Purchase Baru:**
1. Klik "Tambah Purchase"
2. Isi form:
   - **Kategori**: Pilih "Bahan Baku" atau "Finished Goods"
   - **Item**: Pilih item (list akan muncul setelah kategori dipilih)
   - **Qty Pembelian**: Jumlah yang dibeli
   - **Tanggal Kedatangan**: Kapan barang datang
   - **Penerima Barang**: Nama yang menerima

**Detail Penerimaan Barang:**
- **Qty Barang Masuk**: Jumlah yang benar-benar diterima
- **Barang Defect**: Jumlah barang rusak/cacat
- **Barang Retur**: Jumlah barang yang diretur ke supplier
- **Total Stok Masuk**: Otomatis terhitung (Masuk - Defect + Retur)

**Mengedit/Detail Purchase:**
1. Klik tombol "Detail" untuk melihat detail lengkap
2. Klik tombol "Edit" untuk mengubah data
3. Klik tombol "Hapus" untuk menghapus data

#### Hubungan dengan Modul Lain
- Data purchase akan otomatis mempengaruhi **Inventory Bahan Baku**
- Stok masuk akan otomatis bertambah di inventory terkait

---

### 3.2 Purchase Stiker - STOCK MASUK

#### Fungsi
Tracking dan pencatatan stock masuk dari pembelian stiker untuk packaging produk.

#### Cara Menggunakan
1. Akses melalui "All Transactions" → "Purchase Stiker"
2. Prosedur sama dengan Purchase Items
3. Khusus untuk stiker packaging
4. Data akan mempengaruhi **Sticker Stock**

---

### 3.3 Catatan Produksi - STOCK KELUAR (Penggunaan Bahan Baku)

#### Fungsi
Tracking dan pencatatan stock keluar berupa penggunaan bahan baku untuk produksi, sekaligus mencatat stock masuk produk jadi hasil produksi.

#### Cara Menggunakan

**Melihat Catatan Produksi:**
1. Klik "All Transactions" → "Catatan Produksi"
2. Gunakan filter:
   - **SKU Produk**: Filter berdasarkan SKU
   - **Nama Produk**: Filter berdasarkan nama
   - **Packaging**: Filter berdasarkan kemasan
   - **Label**: Filter berdasarkan label produk
   - **Bahan Baku**: Filter berdasarkan bahan baku yang digunakan
   - **Tanggal**: Rentang tanggal produksi

**Menambah Catatan Produksi:**
1. Klik "Tambah Catatan Produksi"
2. **Tab Produk yang Diproduksi**:
   - Pilih produk yang diproduksi
   - Masukkan jumlah yang diproduksi
   - Masukkan packaging yang digunakan
3. **Tab Bahan Baku yang Digunakan**:
   - Pilih bahan baku yang digunakan
   - Masukkan jumlah gramasi yang digunakan
   - Sistem akan validasi dengan stok tersedia
4. Klik "Simpan Catatan Produksi"

**Mengedit Catatan:**
1. Klik tombol "Edit" pada baris yang diinginkan
2. Form edit akan terbuka dengan data yang sudah ada
3. Ubah data yang diperlukan
4. Klik "Perbarui"

#### Fitur Khusus
- **Multi-tab Interface**: Terpisah antara data produk dan bahan baku
- **Auto Validation**: Sistem akan mengecek ketersediaan stok bahan baku
- **Real-time Calculation**: Total gramasi dan packaging otomatis terhitung

#### Hubungan dengan Modul Lain
- Otomatis mengurangi stok di **Inventory Bahan Baku**
- Otomatis menambah stok di **Finished Goods**
- Data digunakan untuk **Laporan Catatan Produksi**

---

### 3.4 Inventory Bahan Baku

#### Fungsi
Monitoring dan manajemen inventory/stok bahan baku real-time.

#### Cara Menggunakan

**Melihat Inventory:**
1. Klik "All Transactions" → "Inventory Bahan Baku"
2. Tabel menampilkan:
   - **SKU & Nama**: Identitas bahan baku
   - **Kategori & Satuan**: Klasifikasi dan unit
   - **Stok Awal**: Input manual
   - **Stok Masuk**: Otomatis dari Purchase (tidak bisa diedit)
   - **Terpakai**: Otomatis dari Catatan Produksi (tidak bisa diedit)
   - **Defect**: Input manual untuk barang rusak
   - **Live Stok**: Kalkulasi otomatis real-time
   - **Status**: Indikator stok (Normal/Low Stock)

**Mengupdate Inventory:**
1. Edit field yang bisa diubah (Stok Awal dan Defect)
2. Klik "Update" untuk menyimpan perubahan
3. Live Stock akan otomatis terupdate

**Sinkronisasi Data:**
1. Klik tombol "Sync" untuk menghitung ulang dari data Purchase dan Produksi
2. Berguna jika ada ketidaksesuaian data

#### Fitur Khusus
- **Real-time Calculation**: Formula: Stok Awal + Stok Masuk - Terpakai - Defect
- **Color Coding**: Stok rendah (≤10) ditampilkan dengan warna merah
- **Auto-refresh**: Data terupdate otomatis saat ada transaksi baru
- **Inline Editing**: Edit langsung di tabel tanpa popup

---

### 3.5 Finished Goods Stock

#### Fungsi
Manajemen stok produk jadi dengan tracking lengkap pergerakan stok.

#### Cara Menggunakan

**Melihat Stok Finished Goods:**
1. Klik "All Transactions" → "Finished Goods Stock"
2. Tabel menampilkan:
   - **SKU & Produk**: Identitas produk
   - **Stok Awal**: Input manual (editable)
   - **Stok Masuk**: Otomatis dari Catatan Produksi (auto)
   - **Stok Keluar**: Otomatis dari Sales Scanner (auto)
   - **Defective**: Input manual untuk produk cacat (editable)
   - **Live Stock**: Kalkulasi real-time

**Mengelola Stok:**
1. **Update Manual**: Edit field "Stok Awal" dan "Defective"
2. **Klik Update**: Simpan perubahan
3. **Reset**: Mengatur ulang data manual ke 0

#### Info Box Penjelasan
Sistem menyediakan info box yang menjelaskan:
- Field mana yang bisa diedit manual
- Field mana yang otomatis dari sistem
- Formula perhitungan Live Stock

#### Hubungan dengan Modul Lain
- **Stok Masuk**: Otomatis dari Catatan Produksi
- **Stok Keluar**: Otomatis dari Scanner History Sales
- **Live Stock**: Digunakan untuk validasi penjualan

---

### 3.6 Sticker Stock

#### Fungsi
Manajemen stok stiker untuk packaging produk.

#### Cara Menggunakan
1. Akses melalui "All Transactions" → "Sticker Stock"
2. Interface dan fungsi mirip dengan Finished Goods Stock
3. Stok masuk dari Purchase Stiker
4. Stok keluar dari penggunaan produksi

---

## Sales & Transactions

### 4.1 Scanner - STOCK KELUAR (Penjualan)

#### Fungsi
Tracking dan pencatatan stock keluar berupa penjualan produk menggunakan barcode scanner atau input manual.

#### Cara Menggunakan

**Persiapan Scanner:**
1. Akses "Sales & Transactions" → "Scanner"
2. Pastikan scanner barcode terkoneksi (jika ada)
3. Field "No Resi" akan aktif dan siap menerima input

**Input No Resi:**
- **Mode Auto-Scan** (Recommended):
  - Toggle "Resi Auto-Scan" dalam posisi ON
  - Scan atau ketik nomor resi
  - Sistem akan langsung memvalidasi nomor resi
  - Jika VALID: Field SKU akan aktif dan kursor pindah otomatis
  - Jika INVALID: Muncul pesan error, form akan reset setelah 3 detik
- **Mode Manual**:
  - Toggle "Resi Auto-Scan" ke OFF
  - Ketik nomor resi manual
  - Duplikat diperbolehkan dalam mode ini

**Input SKU Produk:**
Setelah No Resi valid, ada 2 mode input SKU:

- **Mode Scanner/Manual** (Default):
  - Scan barcode SKU atau ketik manual
  - Sistem akan validasi SKU dengan delay 2-3 detik
  - Jika valid: Field baru akan muncul otomatis
  - Jika tidak valid: Field akan dikosongkan

- **Mode Dropdown**:
  - Klik tombol "Toggle" untuk switch ke mode dropdown
  - Ketik minimal 2 huruf untuk search produk
  - Pilih produk dari dropdown
  - Field baru akan muncul setelah memilih

**Mengatur Quantity:**
1. Setiap SKU memiliki field "Jumlah" di sebelah kanan
2. Default jumlah adalah 1
3. Klik field dan ubah sesuai kebutuhan
4. Minimum jumlah adalah 1

**Menghapus SKU:**
1. Klik tombol minus (-) merah di sebelah kanan field
2. Jika lebih dari 1 SKU: Field akan dihapus
3. Jika hanya 1 SKU: Field akan dikosongkan (tidak bisa dihapus)

**Menyimpan Data:**
- **Via Mouse**: Klik tombol "Simpan Data" (hijau)
- **Via Keyboard**: Tekan CTRL + ENTER bersamaan

**Reset Form:**
- **Otomatis**: Form reset otomatis setelah data tersimpan
- **Manual**: Klik tombol "Reset Form" (kuning)

#### Tips untuk Efisiensi
1. Gunakan Mode Auto-Scan untuk menghindari duplikat
2. Manfaatkan keyboard shortcut CTRL+ENTER untuk menyimpan cepat
3. Mode dropdown bagus untuk produk yang tidak ada barcode
4. Periksa data sebelum menyimpan: pastikan No Resi benar dan semua SKU valid

#### Error Handling Scanner
- **Scanner tidak respond**: Coba input manual atau refresh halaman
- **SKU tidak ditemukan**: Pastikan produk sudah terdaftar di Master Data Products
- **No Resi duplikat**: Gunakan mode manual jika memang ingin input duplikat

---

### 4.2 Sales Management

#### Fungsi
CRUD (Create, Read, Update, Delete) untuk manajemen data penjualan yang sudah diinput.

#### Cara Menggunakan

**Melihat Data Penjualan:**
1. Akses "Sales & Transactions" → "Sales Management"
2. Pilih tab:
   - **Data Aktif**: Data penjualan yang masih aktif
   - **Data Terarsip**: Data yang sudah di-soft delete
3. Gunakan search box untuk mencari berdasarkan No Resi atau SKU
4. Atur jumlah data per halaman (10/25/50)

**Mengedit Data Penjualan:**
1. Klik tombol "Edit" (ikon pensil biru) pada baris yang diinginkan
2. Modal edit akan muncul dengan data yang bisa diubah:
   - No Resi
   - SKU dan quantity masing-masing produk
3. Ubah data sesuai kebutuhan
4. Klik "Update" untuk menyimpan

**Menghapus Data (Soft Delete):**
1. Klik tombol "Delete" (ikon sampah merah)
2. Konfirmasi penghapusan
3. Data akan dipindah ke tab "Data Terarsip"
4. Data masih bisa di-restore jika diperlukan

**Mengembalikan Data Terarsip:**
1. Buka tab "Data Terarsip"
2. Klik tombol "Restore" (ikon restore hijau)
3. Data akan kembali ke "Data Aktif"

**Menghapus Permanen:**
1. Di tab "Data Terarsip", klik "Delete Permanently"
2. Konfirmasi penghapusan permanen
3. **Hati-hati**: Data akan hilang selamanya

#### Hubungan dengan Modul Lain
- Data edit akan mempengaruhi kalkulasi di **Finished Goods Stock**
- Data hapus akan mengembalikan stok yang sudah terkurangi

---

### 4.3 Sales Report

#### Fungsi
Laporan penjualan dengan berbagai filter dan opsi export.

#### Cara Menggunakan
1. Akses "Sales & Transactions" → "Sales Report"
2. Gunakan filter tanggal untuk periode tertentu
3. Filter berdasarkan produk atau kategori
4. Export dalam format Excel/PDF
5. View ringkasan penjualan dan trend

---

## Reports & Analytics

### 5.1 Laporan Purchase

#### Fungsi
Laporan lengkap semua aktivitas pembelian (Purchase Items dan Purchase Stiker).

#### Cara Menggunakan
1. Akses "Reports & Analytics" → "Reports" → "Laporan Purchase"
2. **Filter Laporan**:
   - **Periode**: Pilih rentang tanggal
   - **Kategori**: Bahan Baku atau Finished Goods
   - **Item**: Pilih item spesifik
   - **Status**: Semua, Completed, Pending
3. **View Laporan**: Data ditampilkan dalam tabel dengan informasi:
   - Detail purchase
   - Jumlah pembelian vs penerimaan
   - Defect dan retur
   - Total nilai pembelian
4. **Export**: Tersedia format Excel dan PDF

---

### 5.2 Laporan Catatan Produksi

#### Fungsi
Laporan aktivitas produksi dengan detail penggunaan bahan baku.

#### Cara Menggunakan
1. Akses "Reports & Analytics" → "Reports" → "Laporan Catatan Produksi"
2. **Filter Laporan**:
   - **Periode Produksi**: Tanggal produksi
   - **Produk**: Filter berdasarkan produk tertentu
   - **Bahan Baku**: Filter berdasarkan bahan baku yang digunakan
3. **Informasi Laporan**:
   - Produk yang diproduksi
   - Bahan baku yang digunakan
   - Efisiensi produksi
   - Waste/defect rate
4. **Export**: Excel dan PDF tersedia

---

### 5.3 Laporan Scanner

#### Fungsi
Laporan data penjualan dari input scanner dengan analisis detail.

#### Cara Menggunakan
1. Akses "Reports & Analytics" → "Reports" → "Laporan Scanner"
2. **Filter Laporan**:
   - **Periode Penjualan**: Tanggal input data
   - **Produk**: Filter berdasarkan SKU/produk
   - **User**: Filter berdasarkan user yang input
3. **Analisis Tersedia**:
   - Volume penjualan per produk
   - Trend penjualan harian
   - Top selling products
   - User activity report
4. **Export**: Multiple format (Excel, PDF, CSV)

---

## System Management

### 6.1 Users

#### Fungsi
Manajemen pengguna sistem dengan kontrol akses.

#### Cara Menggunakan

**Melihat Daftar User:**
1. Akses "System" → "User Management" → "Users"
2. Tabel menampilkan informasi user:
   - Nama dan email
   - Role/jabatan
   - Status aktif/tidak aktif
   - Tanggal bergabung

**Menambah User Baru:**
1. Klik "Tambah User" atau "Create User"
2. Isi form:
   - **Nama Lengkap**: Nama user
   - **Email**: Email unik untuk login
   - **Password**: Password minimal 8 karakter
   - **Konfirmasi Password**: Ulangi password
   - **Role**: Pilih role/jabatan user
3. Klik "Simpan"

**Mengedit User:**
1. Klik tombol "Edit" pada user yang diinginkan
2. Ubah informasi yang diperlukan
3. **Password**: Kosongkan jika tidak ingin ubah password
4. Klik "Update"

**Menghapus User:**
1. Klik tombol "Delete"
2. Konfirmasi penghapusan
3. User akan dinonaktifkan (soft delete)

**Mengatur Role User:**
1. Di form edit user, ubah field "Role"
2. Role menentukan permission/hak akses user
3. Perubahan role berlaku immediately

---

### 6.2 Roles (Khusus Super Admin)

#### Fungsi
Manajemen role/jabatan dengan pengaturan permission.

#### Cara Menggunakan

**Melihat Daftar Role:**
1. Akses "System" → "User Management" → "Roles"
2. Hanya tersedia untuk user dengan role "Super Admin"

**Membuat Role Baru:**
1. Klik "Tambah Role"
2. Isi nama role (misal: "Manager Produksi")
3. Pilih permission yang diberikan ke role tersebut
4. Klik "Simpan"

**Mengatur Permission Role:**
1. Klik "Edit" pada role yang diinginkan
2. Checklist/unchecklist permission yang diinginkan
3. Permission tersedia per modul (Products, Sales, Reports, dll)
4. Klik "Update"

**Menghapus Role:**
1. Pastikan tidak ada user yang menggunakan role tersebut
2. Klik "Delete" dan konfirmasi

---

### 6.3 Permissions (Khusus Super Admin)

#### Fungsi
Manajemen permission/hak akses detail per fitur.

#### Cara Menggunakan
1. Akses "System" → "User Management" → "Permissions"
2. **Melihat Permission**: List semua permission yang tersedia
3. **Tambah Permission**: Buat permission baru untuk fitur custom
4. **Edit Permission**: Ubah nama atau detail permission
5. **Hapus Permission**: Hapus permission yang tidak digunakan

#### Jenis Permission
- **List**: Hak untuk melihat daftar data
- **Create**: Hak untuk menambah data baru
- **Update**: Hak untuk mengubah data
- **Delete**: Hak untuk menghapus data
- **View**: Hak untuk melihat detail data
- **Export**: Hak untuk export data

---

### 6.4 Activity Log

#### Fungsi
Monitoring semua aktivitas user dalam sistem untuk audit trail.

#### Cara Menggunakan

**Melihat Activity Log:**
1. Akses "Activity Log" di sidebar
2. Tabel menampilkan:
   - **User**: Siapa yang melakukan aktivitas
   - **Kategori**: Modul yang diakses (sales, product, dll)
   - **Aksi**: Jenis aktivitas (create, update, delete, view)
   - **Deskripsi**: Detail aktivitas
   - **Waktu**: Timestamp kapan aktivitas terjadi

**Filter Activity:**
1. **Filter berdasarkan User**: Pilih user tertentu
2. **Filter berdasarkan Kategori**: Pilih modul tertentu
3. **Filter berdasarkan Tanggal**: Pilih periode waktu
4. Klik "Apply Filter"

**Melihat Detail User Activity:**
1. Klik pada nama user di kolom "User"
2. Modal akan muncul dengan detail aktivitas user tersebut
3. Lihat pattern dan frequency aktivitas

#### Kegunaan Activity Log
- **Audit Trail**: Tracking siapa mengubah apa dan kapan
- **Security Monitoring**: Deteksi aktivitas mencurigakan
- **Performance Analysis**: Melihat usage pattern sistem
- **Compliance**: Untuk keperluan audit internal/eksternal

---

## Tips Umum

### Navigasi Sistem
1. **Sidebar Menu**: Menu utama di sebelah kiri
2. **Breadcrumb**: Petunjuk lokasi Anda di atas konten
3. **Back Button**: Gunakan tombol browser atau breadcrumb untuk kembali
4. **Auto-save**: Beberapa form auto-save draft

### Filter dan Search
1. **Global Search**: Tersedia di setiap tabel untuk search cepat
2. **Advanced Filter**: Gunakan multiple filter untuk hasil yang spesifik
3. **Clear Filter**: Tombol untuk reset semua filter
4. **Export Filtered Data**: Export hanya data yang sudah difilter

### Data Management
1. **Bulk Operations**: Beberapa modul support bulk edit/delete
2. **Inline Editing**: Edit langsung di tabel tanpa popup
3. **Auto-refresh**: Data penting (inventory, sales) auto-refresh
4. **Real-time Validation**: Form input divalidasi real-time

### Performance Tips
1. **Pagination**: Gunakan pagination untuk data besar
2. **Targeted Filter**: Filter data sebelum export
3. **Browser Cache**: Clear cache jika ada masalah tampilan
4. **Session Timeout**: Login ulang jika session habis

---

## Error Handling

### Masalah Umum dan Solusi

#### "Data tidak muncul di tabel"
**Penyebab**: Filter terlalu ketat atau data belum ada
**Solusi**:
1. Clear semua filter
2. Refresh halaman (F5)
3. Periksa permission akses data

#### "Form tidak bisa disimpan"
**Penyebab**: Validasi error atau field required kosong
**Solusi**:
1. Periksa semua field bertanda (*)
2. Lihat pesan error di bawah field
3. Pastikan format data benar (email, angka, dll)

#### "Scanner tidak berfungsi"
**Penyebab**: Scanner tidak terdeteksi atau tidak dikonfigurasi
**Solusi**:
1. Coba input manual
2. Refresh halaman
3. Hubungi IT support jika masalah berlanjut

#### "Stok tidak sesuai"
**Penyebab**: Data tidak sinkron antar modul
**Solusi**:
1. Gunakan fitur "Sync" di Inventory Bahan Baku
2. Periksa data Purchase dan Catatan Produksi
3. Refresh halaman untuk update terbaru

#### "Permission denied"
**Penyebab**: User tidak memiliki hak akses untuk fitur tertentu
**Solusi**:
1. Hubungi admin untuk mengatur permission
2. Login dengan user yang memiliki akses
3. Periksa role user di User Management

#### "Export gagal"
**Penyebab**: Data terlalu besar atau server busy
**Solusi**:
1. Filter data untuk mengurangi jumlah record
2. Coba export di jam yang berbeda
3. Refresh halaman dan coba lagi

#### "Form input lambat/hang"
**Penyebab**: Browser performance atau koneksi internet
**Solusi**:
1. Refresh halaman (F5)
2. Clear browser cache
3. Tutup tab browser lain yang tidak perlu
4. Periksa koneksi internet

### Error Recovery
1. **Auto-save**: Beberapa form menyimpan draft otomatis
2. **Browser Back**: Gunakan tombol back browser jika stuck
3. **Force Refresh**: Ctrl+F5 untuk hard refresh
4. **Clear Cache**: Clear browser cache jika ada masalah persistent

### Kapan Harus Refresh Halaman
- Setelah mengubah role/permission user
- Jika data tidak sync antar modul
- Setelah update sistem
- Jika tampilan tidak normal
- Setelah upload file besar

### Kontak Support
Jika mengalami masalah yang tidak bisa diselesaikan:
1. **Screenshot** error atau masalah
2. **Catat** langkah-langkah yang dilakukan
3. **Note** browser dan versi yang digunakan
4. **Hubungi** tim IT support atau admin sistem

---

*Manual ini dibuat untuk membantu user memahami dan menggunakan sistem Tea Heaven Inventory Management secara efektif. Untuk pertanyaan atau saran perbaikan, silakan hubungi tim pengembang.* 