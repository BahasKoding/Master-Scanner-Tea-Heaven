# 📱 Tutorial Sistem Scanner History Sales

## Daftar Isi
1. [Pengenalan Sistem](#pengenalan-sistem)
2. [Cara Menggunakan Scanner](#cara-menggunakan-scanner)
3. [Mengelola Data Penjualan](#mengelola-data-penjualan)
4. [Tips dan Trik](#tips-dan-trik)
5. [Troubleshooting](#troubleshooting)

---

## 🌟 Pengenalan Sistem

Sistem Scanner History Sales adalah aplikasi untuk mencatat riwayat penjualan dengan cara yang mudah dan cepat. Anda bisa menggunakan barcode scanner atau mengetik manual untuk memasukkan data.

### Fitur Utama:
- ✅ **Scan Barcode**: Pindai nomor resi dan SKU produk
- ✅ **Input Manual**: Ketik data secara manual jika diperlukan
- ✅ **Pencarian Produk**: Cari produk dari database dengan mudah
- ✅ **Riwayat Lengkap**: Lihat semua data penjualan yang pernah diinput
- ✅ **Edit & Hapus**: Ubah atau hapus data yang sudah diinput

---

## 🚀 Cara Menggunakan Scanner

### Langkah 1: Membuka Halaman Scanner

Saat pertama kali membuka halaman, Anda akan melihat:
- **Field "No Resi"** yang sudah aktif (kursor berkedip)
- **Toggle "Resi Auto-Scan"** dalam posisi ON
- **Field SKU** yang masih tidak aktif (abu-abu)
- **Tabel riwayat penjualan** di bagian bawah

### Langkah 2: Memasukkan Nomor Resi

#### 🔄 Mode Auto-Scan (Direkomendasikan)
```
1. Pastikan toggle "Resi Auto-Scan" dalam posisi ON
2. Arahkan scanner ke barcode nomor resi ATAU ketik manual
3. Sistem akan langsung memvalidasi nomor resi
4. Jika VALID: Field SKU akan aktif dan kursor pindah otomatis
5. Jika INVALID: Muncul pesan error, form akan reset setelah 3 detik
```

#### ✋ Mode Manual
```
1. Klik toggle "Resi Auto-Scan" untuk OFF
2. Ketik nomor resi secara manual
3. Klik di luar field atau tekan Tab untuk validasi
4. Duplikat nomor resi diperbolehkan dalam mode ini
```

> **💡 Tips**: Mode Auto-Scan lebih cepat dan mencegah duplikat data!

### Langkah 3: Memasukkan SKU Produk

Setelah nomor resi valid, Anda bisa memasukkan SKU produk dengan 2 cara:

#### 📝 Mode Scanner/Manual (Default)
```
1. Arahkan scanner ke barcode SKU ATAU ketik manual
2. Tunggu 2-3 detik, sistem akan memvalidasi SKU
3. Jika SKU valid: Field baru akan muncul otomatis
4. Jika SKU tidak valid: Field akan dikosongkan, fokus kembali ke field tersebut
5. Ulangi untuk SKU berikutnya
```

#### 🔍 Mode Select Dropdown
```
1. Klik tombol "Toggle" untuk beralih ke mode dropdown
2. Ketik minimal 2 huruf untuk mencari produk
3. Pilih produk dari daftar yang muncul
4. Field baru akan muncul otomatis setelah memilih
5. Ulangi untuk SKU berikutnya
```

> **💡 Tips**: Mode dropdown bagus untuk mencari produk yang tidak tahu SKU-nya!

### Langkah 4: Mengatur Jumlah (Quantity)

- Setiap SKU memiliki field **"Jumlah"** di sebelah kanannya
- Default jumlah adalah **1**
- Klik field jumlah dan ubah sesuai kebutuhan
- Minimum jumlah adalah **1**

### Langkah 5: Menghapus SKU (Jika Salah Input)

- Klik tombol **minus (-)** merah di sebelah kanan field
- Jika ada lebih dari 1 SKU: Field akan dihapus
- Jika hanya 1 SKU: Field akan dikosongkan (tidak bisa dihapus)

### Langkah 6: Menyimpan Data

Ada 2 cara untuk menyimpan:

#### 🖱️ Menggunakan Mouse
```
Klik tombol "Simpan Data" (hijau) di bagian bawah form
```

#### ⌨️ Menggunakan Keyboard
```
Tekan CTRL + ENTER bersamaan
```

> **⚠️ Penting**: Pastikan sudah ada minimal 1 No Resi dan 1 SKU yang valid sebelum menyimpan!

### Langkah 7: Reset Form (Mulai Input Baru)

Setelah data tersimpan:
- Form akan **otomatis reset**
- Kursor akan kembali ke field "No Resi"
- Siap untuk input data berikutnya

Jika ingin reset manual:
- Klik tombol **"Reset Form"** (kuning)

---

## 📊 Mengelola Data Penjualan

### Melihat Data

#### 📅 Filter Berdasarkan Status
- **Data Aktif**: Klik tombol "Data Aktif" (biru)
- **Data Terarsip**: Klik tombol "Data Terarsip" (kuning)

#### 🔍 Mencari Data
- Gunakan kotak pencarian di atas tabel
- Bisa mencari berdasarkan **No Resi** atau **SKU**
- Ketik dan tekan Enter

#### 📄 Mengatur Tampilan
- Pilih jumlah data per halaman: **10, 25, atau 50**
- Gunakan navigasi halaman di bawah tabel
- Di mobile: geser tabel ke kiri-kanan untuk melihat semua kolom

### Mengedit Data

```
1. Klik tombol "Edit" (ikon pensil biru) pada baris yang ingin diubah
2. Modal popup akan muncul dengan data yang bisa diedit
3. Ubah data sesuai kebutuhan
4. Klik "Update" untuk menyimpan perubahan
```

### Menghapus Data

#### 🗑️ Hapus Sementara (Soft Delete)
```
1. Klik tombol "Delete" (ikon sampah merah)
2. Data akan dipindah ke "Data Terarsip"
3. Data masih bisa dikembalikan jika diperlukan
```

#### ♻️ Mengembalikan Data Terarsip
```
1. Klik tab "Data Terarsip"
2. Klik tombol "Restore" (ikon restore hijau)
3. Data akan kembali ke "Data Aktif"
```

#### ⚠️ Hapus Permanen
```
1. Di tab "Data Terarsip", klik tombol "Delete Permanently"
2. Data akan dihapus selamanya dan tidak bisa dikembalikan
3. Gunakan dengan hati-hati!
```

---

## 💡 Tips dan Trik

### Untuk Efisiensi Kerja

1. **Gunakan Mode Auto-Scan**
   - Lebih cepat dan mencegah duplikat
   - Validasi otomatis real-time

2. **Manfaatkan Keyboard Shortcuts**
   - `CTRL + ENTER`: Simpan data cepat
   - `Tab`: Pindah antar field
   - `Enter`: Konfirmasi input (pada beberapa field)

3. **Toggle Mode SKU Sesuai Kebutuhan**
   - **Scanner/Manual**: Untuk barcode scanning
   - **Dropdown**: Untuk mencari produk yang tidak tahu SKU

4. **Periksa Data Sebelum Simpan**
   - Pastikan No Resi benar
   - Pastikan semua SKU valid
   - Cek jumlah (quantity) setiap item

### Untuk Menghindari Error

1. **Jangan Input SKU yang Sama dengan No Resi**
   - Sistem akan menolak dan memberikan peringatan

2. **Hindari SKU Duplikat dalam Satu Form**
   - Sistem akan mendeteksi dan memberikan peringatan

3. **Pastikan Koneksi Internet Stabil**
   - Validasi SKU memerlukan koneksi ke database

4. **Tunggu Proses Validasi Selesai**
   - Jangan terburu-buru, biarkan sistem memvalidasi SKU

---

## 🔧 Troubleshooting

### Masalah Umum dan Solusi

#### ❌ "No Resi sudah ada dalam sistem"
**Penyebab**: Nomor resi sudah pernah diinput sebelumnya
**Solusi**: 
- Periksa kembali nomor resi
- Atau matikan mode Auto-Scan jika memang ingin input duplikat

#### ❌ "SKU tidak ditemukan di database"
**Penyebab**: SKU tidak terdaftar dalam master data produk
**Solusi**:
- Periksa kembali SKU yang diinput
- Pastikan produk sudah terdaftar di master data
- Hubungi admin untuk menambah produk baru

#### ❌ "SKU duplikat terdeteksi"
**Penyebab**: SKU yang sama diinput lebih dari sekali dalam form yang sama
**Solusi**:
- Hapus salah satu SKU yang duplikat
- Atau gabungkan quantity-nya

#### ❌ Field SKU tidak aktif
**Penyebab**: No Resi belum valid atau belum diinput
**Solusi**:
- Pastikan No Resi sudah diinput dan valid
- Tunggu proses validasi selesai

#### ❌ Tombol "Simpan Data" tidak berfungsi
**Penyebab**: Data belum lengkap atau tidak valid
**Solusi**:
- Pastikan ada minimal 1 No Resi valid
- Pastikan ada minimal 1 SKU valid
- Periksa pesan error di layar

#### ❌ Data tidak muncul di tabel
**Penyebab**: Filter atau pencarian yang salah
**Solusi**:
- Periksa tab "Data Aktif" vs "Data Terarsip"
- Kosongkan kotak pencarian
- Refresh halaman (F5)

#### ❌ Scanner tidak berfungsi
**Penyebab**: Scanner tidak terdeteksi atau tidak dikonfigurasi
**Solusi**:
- Pastikan scanner terhubung dengan benar
- Coba input manual terlebih dahulu
- Hubungi IT support jika masalah berlanjut

### Kontak Bantuan

Jika mengalami masalah yang tidak tercantum di atas:
1. **Screenshot** layar error
2. **Catat** langkah-langkah yang dilakukan sebelum error
3. **Hubungi** tim IT support atau admin sistem

---

## 📝 Kesimpulan

Sistem Scanner History Sales dirancang untuk memudahkan pencatatan data penjualan dengan cepat dan akurat. Dengan mengikuti tutorial ini, Anda sudah bisa:

✅ Menggunakan scanner untuk input data  
✅ Beralih antara mode scanner dan manual  
✅ Mengelola data penjualan (lihat, edit, hapus)  
✅ Mengatasi masalah umum yang mungkin terjadi  

**Ingat**: Latihan membuat sempurna! Semakin sering digunakan, semakin mahir Anda mengoperasikan sistem ini.

---

*📅 Dokumen ini dibuat untuk membantu pengguna memahami sistem Scanner History Sales. Jika ada pertanyaan atau saran perbaikan, silakan hubungi tim pengembang.* 