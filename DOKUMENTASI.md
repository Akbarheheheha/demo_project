# 📋 Dokumentasi Proyek: SmartBiz UMKM — Mini ERP & POS

> **Versi:** 1.0.0  
> **Framework:** Laravel 13 (PHP 8.3+)  
> **Frontend:** Blade + Tailwind CSS v4 + Alpine.js  
> **Tanggal Dokumentasi:** 29 Juni 2026  
> **Repository:** [github.com/Akbarheheheha/demo_project](https://github.com/Akbarheheheha/demo_project)

---

## 📖 Deskripsi Proyek

**SmartBiz UMKM** adalah aplikasi web monolitik berbasis Laravel yang dirancang sebagai sistem **Mini ERP (Enterprise Resource Planning)** dan **POS (Point of Sale)** untuk pelaku usaha mikro, kecil, dan menengah (UMKM) seperti toko kelontong, kios ritel, dan warung.

Aplikasi ini menyediakan fitur lengkap mulai dari pencatatan transaksi kasir, manajemen inventaris barang, dashboard ringkasan bisnis real-time, laporan keuangan, hingga pengaturan toko dan manajemen akses karyawan — semuanya dalam satu aplikasi terintegrasi.

---

## 🏗️ Arsitektur & Tech Stack

| Komponen | Teknologi |
|----------|-----------|
| **Backend** | Laravel 13 (PHP 8.3+) |
| **Frontend** | Blade Template Engine |
| **CSS Framework** | Tailwind CSS v4 |
| **JS Reaktif** | Alpine.js v3 |
| **HTTP Client** | Axios |
| **Build Tool** | Vite v8 |
| **Icon Library** | Lucide Icons (CDN) |
| **Chart Library** | Chart.js (CDN) |
| **Database** | SQLite (default) / MySQL |
| **Role Management** | spatie/laravel-permission |
| **SPA Engine** | Custom PJAX (Axios + DOMParser + History API) |

---

## 📁 Struktur Proyek

```
demo-project/
├── app/
│   ├── Http/
│   │   ├── Controllers/
│   │   │   ├── AuthController.php        # Login, logout, redirect berbasis role
│   │   │   ├── DashboardController.php   # Ringkasan bisnis real-time dari DB
│   │   │   ├── InventoryController.php   # CRUD inventaris produk
│   │   │   ├── PosController.php         # Antarmuka kasir & checkout
│   │   │   ├── ReportController.php      # Laporan keuangan & analitik
│   │   │   └── SettingsController.php    # Pengaturan toko & manajemen user
│   │   └── Middleware/
│   │       └── (Custom auth & guest middleware)
│   ├── Models/
│   │   ├── Product.php                   # Model produk inventaris
│   │   ├── Transaction.php               # Model transaksi penjualan
│   │   ├── Setting.php                   # Model key-value konfigurasi toko
│   │   └── User.php                      # Model user dengan Spatie HasRoles
│   └── Services/
│       └── PosService.php                # Logika bisnis checkout POS
├── database/
│   ├── migrations/
│   │   ├── create_products_table         # Tabel produk (sku, name, category, stock, dll)
│   │   ├── create_transactions_table     # Tabel transaksi (invoice, total_harga, status)
│   │   ├── create_settings_table         # Tabel konfigurasi key-value
│   │   └── create_permission_tables      # Tabel Spatie (roles, permissions, model_has_roles)
│   └── seeders/
│       ├── DatabaseSeeder.php            # Seeder utama (produk, transaksi, settings)
│       └── RoleAndPermissionSeeder.php   # Seeder role & user demo
├── resources/
│   ├── views/
│   │   ├── layouts/
│   │   │   └── app.blade.php             # Layout utama + Sidebar + SPA Engine
│   │   ├── auth/
│   │   │   └── login.blade.php           # Halaman login
│   │   ├── dashboard.blade.php           # Dashboard ringkasan bisnis
│   │   ├── inventory.blade.php           # Manajemen inventaris barang
│   │   ├── pos.blade.php                 # Antarmuka kasir POS
│   │   ├── reports.blade.php             # Laporan keuangan & chart
│   │   └── settings.blade.php           # Pengaturan toko & karyawan
│   └── js/
│       └── app.js                        # Bootstrap Axios + CSRF Token
└── routes/
    └── web.php                           # Routing aplikasi dengan middleware role
```

---

## 🔐 Sistem Autentikasi & Otorisasi

### Role-Based Access Control (RBAC)

Aplikasi menggunakan **spatie/laravel-permission** untuk mengelola 3 level akses:

| Role | Hak Akses | Redirect Setelah Login |
|------|-----------|----------------------|
| **Super Admin** | Semua fitur: Dashboard, Inventaris, POS, Laporan, Pengaturan | `/admin/dashboard` |
| **Manager** | Dashboard, Inventaris, Laporan (tanpa Pengaturan) | `/admin/dashboard` |
| **Kasir** | Hanya POS (Point of Sale) | `/pos` |

### Akun Demo (Default)

| Role | Email | Password |
|------|-------|----------|
| Super Admin | `admin@demo.com` | `password` |
| Manager | `manager@demo.com` | `password` |
| Kasir | `kasir@demo.com` | `password` |

### Flow Autentikasi

```
Login → Auth::attempt() → Cek Role User
├── Kasir      → redirect ke /pos
├── Manager    → redirect ke /admin/dashboard
└── Super Admin → redirect ke /admin/dashboard
```

---

## ✨ Fitur-Fitur Aplikasi

### 1. 🔑 Halaman Login

- Form login dengan validasi email & password
- Pesan error dalam Bahasa Indonesia
- Fitur "Remember Me"
- Redirect otomatis berdasarkan role pengguna
- Proteksi route: guest tidak bisa akses halaman dalam, user login tidak bisa akses halaman login

---

### 2. 📊 Dashboard Admin (`/admin/dashboard`)

**Akses:** Super Admin, Manager

Dashboard menampilkan ringkasan bisnis secara **real-time dari database**:

#### Kartu Statistik
| Metrik | Sumber Data |
|--------|------------|
| Total Penjualan Hari Ini | `SUM(total_harga)` dari transaksi hari ini |
| Total Transaksi | `COUNT` seluruh transaksi |
| Pemberitahuan Stok (≤ 5) | Produk dengan stok di bawah batas minimal |

#### Chart Tren Penjualan Mingguan
- **Library:** Chart.js
- **Tipe:** Line chart dengan gradient fill
- **Data:** Total omzet harian selama 7 hari terakhir
- **Label:** Nama hari dalam Bahasa Indonesia (Senin, Selasa, dst.)

#### Aktivitas Kasir Terbaru
- 5 transaksi terakhir beserta info kasir, invoice, dan nominal
- Relasi Eloquent `Transaction::with('user')`

#### Banner Peringatan Stok Menipis
- Menampilkan daftar produk dengan stok ≤ 5
- Tombol langsung ke halaman Inventaris

#### Ringkasan Omzet Bulanan
- Total omzet bulan berjalan ditampilkan di header

---

### 3. 📦 Manajemen Inventaris (`/admin/inventory`)

**Akses:** Super Admin, Manager

Sistem CRUD inventaris barang yang **terhubung langsung ke database** melalui API Axios:

#### Fitur CRUD
| Operasi | Endpoint | Method |
|---------|----------|--------|
| Baca semua produk | `/admin/inventory` | GET (Blade) |
| Tambah produk baru | `/api/inventory/store` | POST |
| Edit produk | `/api/inventory/update/{id}` | PUT |
| Hapus produk | `/api/inventory/delete/{id}` | DELETE |

#### Field Produk
| Field | Keterangan |
|-------|-----------|
| SKU | Kode unik produk (contoh: SMB-001) |
| Nama Barang | Nama lengkap produk |
| Kategori | Sembako, Makanan, Minuman, Cemilan, Rumah Tangga |
| Stok | Jumlah stok saat ini |
| Stok Minimum | Batas minimum untuk peringatan |
| Harga Beli | Harga pembelian/modal |
| Harga Jual | Harga jual ke konsumen |
| Profit Margin | Dihitung otomatis (%) |

#### Fitur Pencarian & Filter
- **Search:** Cari berdasarkan SKU atau nama barang
- **Filter Kategori:** Dropdown kategori produk
- **Filter Stok:** Normal / Stok Tipis / Stok Habis

#### Status Stok (Badge Dinamis)
| Status | Kondisi | Warna |
|--------|---------|-------|
| Aman | `stok > min_stock` | 🟢 Hijau |
| Tipis | `stok ≤ min_stock && stok > 0` | 🟡 Kuning |
| Habis | `stok === 0` | 🔴 Merah |

#### Log Mutasi Stok
- Riwayat 10 transaksi terakhir sebagai log pergerakan stok
- Informasi operator, tanggal, referensi invoice

#### Tombol Aksi Per Produk
- 📜 **Riwayat Mutasi** — Lihat log keluar-masuk stok
- ✏️ **Edit** — Update data produk
- 🗑️ **Hapus** — Hapus produk dari database

---

### 4. 🛒 Point of Sale / POS (`/pos`)

**Akses:** Super Admin, Kasir

Antarmuka kasir untuk memproses transaksi penjualan:

- Daftar produk yang tersedia dari database
- Keranjang belanja interaktif
- Kalkulasi subtotal, pajak, dan total
- Proses checkout dengan validasi stok
- Generate nomor invoice otomatis
- Pencatatan transaksi ke database

---

### 5. 📈 Laporan Keuangan (`/admin/reports`)

**Akses:** Super Admin

Halaman analitik bisnis dengan visualisasi data:

#### Rangkuman Keuangan
| Metrik | Keterangan |
|--------|-----------|
| Pendapatan Bersih | Total revenue setelah HPP |
| HPP (Harga Pokok Penjualan) | Cost of goods sold |
| Laba Kotor | Revenue dikurangi HPP |
| Rata-rata Nilai Transaksi | Average ticket size |
| Pertumbuhan Revenue | Persentase pertumbuhan bulanan |
| Pertumbuhan Laba | Persentase pertumbuhan laba |

#### Chart Performa Kategori Produk
- **Tipe:** Bar chart
- **Data:** Revenue per kategori (Sembako, Makanan, Minuman, dll.)

#### Tabel Produk Terlaris
- Top 5 produk berdasarkan quantity terjual
- Info SKU, nama, kategori, total revenue, dan margin

#### Chart Perbandingan Bulanan
- **Tipe:** Dual line chart
- **Data:** Revenue tahun ini vs tahun lalu (Jan–Jun)

> ⚠️ **Catatan:** Data laporan saat ini masih menggunakan **data mock/dummy** dari controller. Belum terhubung ke query database real.

---

### 6. ⚙️ Pengaturan (`/admin/settings`)

**Akses:** Super Admin saja

Halaman konfigurasi toko dan manajemen akses karyawan:

#### Tab 1: Profil Toko
| Setting | Keterangan |
|---------|-----------|
| Nama Toko | Nama usaha |
| Email Toko | Kontak email |
| Nomor Telepon | Nomor HP/telepon toko |
| Alamat | Alamat lengkap toko |
| Header Struk | Teks yang muncul di atas struk |
| Footer Struk | Teks yang muncul di bawah struk |

#### Tab 2: Konfigurasi POS
| Setting | Keterangan |
|---------|-----------|
| Pajak (%) | Persentase pajak per transaksi |
| Diskon Default (%) | Diskon default yang diterapkan |
| Ukuran Struk | 58mm / 80mm |
| Metode Pembayaran | Tunai, QRIS, Transfer Bank |

#### Tab 3: Manajemen Karyawan / Akses User
| Operasi | Endpoint | Method |
|---------|----------|--------|
| Daftar user | `/admin/settings` | GET (Blade) |
| Tambah karyawan baru | `/api/settings/users/store` | POST |
| Hapus / Cabut akses | `/api/settings/users/delete/{id}` | DELETE |

- Assign role Spatie saat membuat user baru
- Password default: `password`
- Proteksi: tidak bisa menghapus Super Admin utama (ID: 1) atau diri sendiri

Semua pengaturan disimpan ke tabel `settings` menggunakan model key-value.

---

## 🧭 Navigasi SPA (Single Page Application)

Aplikasi menggunakan **Custom SPA Engine** berbasis PJAX yang dibangun di dalam layout utama:

### Cara Kerja
1. Klik navigasi sidebar diintersep oleh event listener
2. Konten halaman baru diambil via `axios.get(url)`
3. HTML response di-parse menggunakan `DOMParser`
4. Konten `<main>` lama diganti dengan konten `<main>` baru
5. Browser history di-update via `pushState`
6. Script inline & eksternal dieksekusi ulang secara bertahap

### Pipeline Eksekusi (4 Fase)
| Fase | Proses |
|------|--------|
| **1. External Scripts** | Load CDN (Chart.js, dll.) secara dinamis, di-cache agar tidak duplikat |
| **2. Alpine Init** | Destroy Alpine tree lama, re-initialize pada konten baru |
| **3. Inline Scripts** | Eksekusi `<script>` inline, otomatis unwrap `DOMContentLoaded` wrapper |
| **4. Lucide Icons** | Re-render semua ikon SVG Lucide setelah Alpine selesai render |

### Fitur SPA
- ✅ Progress bar animasi saat loading halaman
- ✅ Browser back/forward button berfungsi normal (`popstate`)
- ✅ Sidebar highlight aktif ter-sync otomatis
- ✅ Chart.js & Lucide icons di-reinisialisasi setiap navigasi
- ✅ Alpine.js components di-destroy & re-init dengan benar

---

## 🗄️ Skema Database

### Tabel `users`
| Kolom | Tipe | Keterangan |
|-------|------|-----------|
| id | bigint (PK) | Auto increment |
| name | string | Nama lengkap |
| email | string (unique) | Email login |
| password | string | Hashed password |
| created_at | timestamp | Waktu dibuat |
| updated_at | timestamp | Waktu diperbarui |

### Tabel `products`
| Kolom | Tipe | Keterangan |
|-------|------|-----------|
| id | bigint (PK) | Auto increment |
| sku | string (unique) | Kode SKU produk |
| name | string | Nama produk |
| category | string | Kategori produk |
| purchase_price | decimal | Harga beli/modal |
| price | decimal | Harga jual |
| stock | integer | Jumlah stok |
| min_stock | integer (default: 5) | Batas minimum stok |
| created_at | timestamp | Waktu dibuat |
| updated_at | timestamp | Waktu diperbarui |

### Tabel `transactions`
| Kolom | Tipe | Keterangan |
|-------|------|-----------|
| id | bigint (PK) | Auto increment |
| user_id | bigint (FK → users) | ID kasir yang memproses |
| invoice | string (unique) | Nomor invoice |
| total_harga | decimal | Total harga transaksi |
| status | string (default: pending) | Status: success/pending |
| created_at | timestamp | Waktu transaksi |
| updated_at | timestamp | Waktu diperbarui |

### Tabel `settings`
| Kolom | Tipe | Keterangan |
|-------|------|-----------|
| id | bigint (PK) | Auto increment |
| key | string (unique) | Nama konfigurasi |
| value | text (nullable) | Nilai konfigurasi |
| created_at | timestamp | Waktu dibuat |
| updated_at | timestamp | Waktu diperbarui |

### Tabel Spatie Permission
- `roles` — Daftar role (Super Admin, Manager, Kasir)
- `permissions` — Daftar permission (belum digunakan)
- `model_has_roles` — Relasi user ↔ role
- `model_has_permissions` — Relasi user ↔ permission
- `role_has_permissions` — Relasi role ↔ permission

---

## 🛣️ Routing

### Rute Publik (Guest)
| Method | URL | Controller | Keterangan |
|--------|-----|-----------|-----------|
| GET | `/login` | `AuthController@showLogin` | Halaman login |
| POST | `/login` | `AuthController@login` | Proses autentikasi |

### Rute Terproteksi (Authenticated)
| Method | URL | Controller | Role |
|--------|-----|-----------|------|
| POST | `/logout` | `AuthController@logout` | Semua |
| GET | `/` | (Closure) | Redirect berdasarkan role |
| GET | `/pos` | `PosController@index` | Kasir, Super Admin |
| POST | `/pos` | `PosController@store` | Kasir, Super Admin |
| GET | `/admin/dashboard` | `DashboardController@index` | Super Admin, Manager |
| GET | `/admin/inventory` | `InventoryController@index` | Super Admin, Manager |
| GET | `/admin/reports` | `ReportController@index` | Super Admin, Manager |
| GET | `/admin/settings` | `SettingsController@index` | Super Admin |

### API Internal (Axios CRUD)
| Method | URL | Controller | Role |
|--------|-----|-----------|------|
| POST | `/api/inventory/store` | `InventoryController@store` | Super Admin, Manager |
| PUT | `/api/inventory/update/{id}` | `InventoryController@update` | Super Admin, Manager |
| DELETE | `/api/inventory/delete/{id}` | `InventoryController@destroy` | Super Admin, Manager |
| POST | `/api/settings/save/{category}` | `SettingsController@save` | Super Admin |
| POST | `/api/settings/users/store` | `SettingsController@storeUser` | Super Admin |
| DELETE | `/api/settings/users/delete/{id}` | `SettingsController@deleteUser` | Super Admin |

---

## 🚀 Cara Instalasi & Menjalankan

### Prasyarat
- PHP 8.3+
- Composer
- Node.js & NPM
- SQLite atau MySQL

### Langkah Instalasi

```bash
# 1. Clone repository
git clone https://github.com/Akbarheheheha/demo_project.git
cd demo_project

# 2. Install dependensi PHP
composer install

# 3. Salin file environment
cp .env.example .env

# 4. Generate application key
php artisan key:generate

# 5. Install dependensi Node.js
npm install

# 6. Build aset frontend
npm run build

# 7. Jalankan migrasi database
php artisan migrate:fresh

# 8. Jalankan seeder (role, user, produk, transaksi, settings)
php artisan db:seed

# 9. Jalankan server
php artisan serve
```

### Akses Aplikasi
Buka browser dan akses: `http://localhost:8000/login`

---

## 📝 Catatan Teknis Penting

### 1. Quirk Kompilator Blade
Penggunaan kata kunci directive Blade (`@json`, `@php`) di dalam komentar JavaScript (`//`) atau HTML **tetap akan dievaluasi** oleh parser Blade. Hindari menggunakan kata kunci directive di dalam komentar.

### 2. Inline `x-data` dan `@json()`
Menggunakan `@json()` di dalam atribut HTML `x-data="..."` rentan terhadap **bentrokan tanda kutip ganda**. Solusi: pindahkan inisialisasi state Alpine ke fungsi JavaScript di dalam tag `<script>` terpisah.

### 3. SPA & DOMContentLoaded
Script yang dibungkus `document.addEventListener('DOMContentLoaded', ...)` tidak akan dieksekusi saat navigasi SPA karena event tersebut sudah fired. SPA engine memiliki fungsi `unwrapDCL()` untuk otomatis mengekstrak dan mengeksekusi kode di dalamnya.

### 4. Lucide Icons & Alpine x-for
Lucide icons di dalam loop `x-for` Alpine memerlukan re-inisialisasi setelah Alpine selesai rendering. SPA engine menangani ini dengan delay 150ms setelah Alpine init.

---

## 📌 Status Fitur

| Fitur | Status | Koneksi DB |
|-------|--------|-----------|
| Login & Logout | ✅ Selesai | ✅ Ya |
| Role-Based Access Control | ✅ Selesai | ✅ Ya |
| Dashboard Admin | ✅ Selesai | ✅ Ya (real-time) |
| Chart Tren Mingguan | ✅ Selesai | ✅ Ya (Eloquent) |
| CRUD Inventaris | ✅ Selesai | ✅ Ya (Axios API) |
| Point of Sale (POS) | ✅ Selesai | ✅ Ya |
| Laporan Keuangan | ✅ Selesai | ⚠️ Data Mock |
| Pengaturan Profil Toko | ✅ Selesai | ✅ Ya |
| Konfigurasi POS | ✅ Selesai | ✅ Ya |
| Manajemen Karyawan | ✅ Selesai | ✅ Ya |
| Navigasi SPA | ✅ Selesai | — |
| Toast Notification | ✅ Selesai | — |

---

## 🔮 Roadmap / Fitur Mendatang

- [ ] Koneksi data Laporan Keuangan ke database (mengganti data mock)
- [ ] Detail item per transaksi (tabel pivot `transaction_items`)
- [ ] Cetak struk / invoice (PDF/thermal printer)
- [ ] Export laporan ke Excel/PDF
- [ ] Halaman profil pengguna
- [ ] Notifikasi stok menipis secara real-time (WebSocket/Pusher)
- [ ] Multi-cabang / multi-toko
- [ ] Barcode scanner untuk POS
- [ ] Audit log aktivitas pengguna
- [ ] Dark mode toggle

---

> **Dibuat dengan ❤️ menggunakan Laravel 13, Tailwind CSS v4, Alpine.js, dan Chart.js**
