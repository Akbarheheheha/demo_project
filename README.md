# 🏪 SmartBiz UMKM — Mini ERP & POS

[![Laravel](https://img.shields.io/badge/Laravel-13.x-red?logo=laravel)](https://laravel.com)
[![PHP](https://img.shields.io/badge/PHP-8.3+-777BB4?logo=php)](https://php.net)
[![Tailwind](https://img.shields.io/badge/Tailwind-v4-38B2AC?logo=tailwind-css)](https://tailwindcss.com)
[![License](https://img.shields.io/badge/License-MIT-green)](LICENSE)

> Sistem manajemen usaha berbasis web untuk UMKM — pencatatan transaksi, inventaris, dan laporan keuangan dalam satu aplikasi.

---

## 📖 Tentang

**SmartBiz UMKM** adalah aplikasi monolitik berbasis **Laravel 13** yang dirancang sebagai solusi **Mini ERP & POS** untuk pelaku usaha mikro, kecil, dan menengah (UMKM) seperti toko kelontong, kios ritel, dan warung.

Aplikasi ini menyediakan fitur lengkap mulai dari pencatatan transaksi kasir, manajemen inventaris barang, dashboard ringkasan bisnis real-time, laporan keuangan, hingga pengaturan toko dan manajemen akses karyawan — semuanya dalam satu aplikasi terintegrasi.

---

## 🛠️ Tech Stack

| Komponen | Teknologi |
|----------|-----------|
| **Backend** | Laravel 13 (PHP 8.3+) |
| **Frontend** | Blade Template Engine |
| **CSS** | Tailwind CSS v4 |
| **JS Reaktif** | Alpine.js v3 |
| **HTTP Client** | Axios |
| **Build Tool** | Vite v8 |
| **Icon** | Lucide Icons |
| **Chart** | Chart.js |
| **Database** | SQLite (default) / MySQL |
| **Role & Permission** | spatie/laravel-permission |
| **PDF** | barryvdh/laravel-dompdf |

---

## ✨ Fitur

- 🔐 **Autentikasi & RBAC** — Role-based access control (Super Admin, Manager, Kasir)
- 📊 **Dashboard Real-time** — Ringkasan penjualan, transaksi, dan peringatan stok menipis
- 📦 **Manajemen Inventaris** — CRUD produk, pencarian, filter kategori, dan log mutasi stok
- 🛒 **Point of Sale (POS)** — Antarmuka kasir dengan keranjang belanja, kalkulasi otomatis, dan checkout
- 📈 **Laporan Keuangan** — Analitik pendapatan, laba kotor, produk terlaris, dan tren bulanan
- ⚙️ **Pengaturan Toko** — Profil usaha, konfigurasi POS, dan manajemen karyawan
- 🧭 **SPA Navigation** — Navigasi cepat tanpa reload menggunakan custom PJAX engine
- 🔔 **Toast Notification** — Notifikasi sukses/error untuk setiap aksi

---

## 🚀 Instalasi

### Prasyarat

- PHP 8.3+
- Composer
- Node.js & NPM
- SQLite / MySQL

### Langkah-langkah

```bash
# 1. Clone repository
git clone https://github.com/Akbarheheheha/demo_project.git
cd demo_project

# 2. Install dependensi
composer install
npm install

# 3. Setup environment
cp .env.example .env
php artisan key:generate

# 4. Migrasi & seeder database
php artisan migrate --force
php artisan db:seed

# 5. Build aset frontend
npm run build

# 6. Jalankan server
php artisan serve
```

Akses aplikasi di `http://localhost:8000`

---

## 🔑 Akun Demo

| Role | Email | Password |
|------|-------|----------|
| Super Admin | `admin@demo.com` | `password` |
| Manager | `manager@demo.com` | `password` |
| Kasir | `kasir@demo.com` | `password` |

---

## 📁 Struktur Proyek

```
demo-project/
├── app/
│   ├── Http/Controllers/   # Auth, Dashboard, Inventory, POS, Reports, Settings
│   ├── Models/             # Product, Transaction, Setting, User
│   └── Services/           # PosService (logika bisnis checkout)
├── database/
│   ├── migrations/         # Skema tabel
│   └── seeders/            # Data awal (role, user, produk)
├── resources/views/        # Blade templates
├── routes/web.php          # Routing aplikasi
└── vite.config.js          # Konfigurasi Vite
```

---

## 📝 Dokumentasi Lengkap

Untuk dokumentasi teknis yang lebih detail (arsitektur, routing, database schema, cara kerja SPA engine, catatan teknis, dan roadmap), silakan baca:

👉 **[DOKUMENTASI.md](DOKUMENTASI.md)**

---

## 🧪 Testing

```bash
composer test
```

---

## 📌 Status Fitur

| Fitur | Status | Koneksi DB |
|-------|--------|-----------|
| Login & RBAC | ✅ | ✅ |
| Dashboard Real-time | ✅ | ✅ |
| CRUD Inventaris | ✅ | ✅ |
| Point of Sale (POS) | ✅ | ✅ |
| Laporan Keuangan | ✅ | ⚠️ Data Mock |
| Pengaturan Toko | ✅ | ✅ |
| Manajemen Karyawan | ✅ | ✅ |
| Navigasi SPA | ✅ | — |

---

## 🔮 Roadmap

- [ ] Koneksi data Laporan ke database
- [ ] Detail item per transaksi (tabel pivot `transaction_items`)
- [ ] Cetak struk / invoice PDF
- [ ] Export laporan ke Excel/PDF
- [ ] Halaman profil pengguna
- [ ] Notifikasi stok real-time (WebSocket)
- [ ] Multi-cabang / multi-toko
- [ ] Barcode scanner untuk POS
- [ ] Audit log aktivitas
- [ ] Dark mode toggle

---

## 📄 License

MIT License — dibuat dengan ❤️ menggunakan Laravel, Tailwind CSS, Alpine.js, dan Chart.js.
