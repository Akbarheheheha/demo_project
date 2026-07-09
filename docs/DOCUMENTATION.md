# Dokumentasi Aplikasi SmartBiz UMKM — Mini ERP & POS

> **Nama Proyek:** SmartBiz UMKM  
> **Tech Stack:** Laravel 11, MySQL, Alpine.js, Tailwind CSS, Vite  
> **Role System:** Spatie Permission (Super Admin, Manager, Kasir, Gudang)

---

## Daftar Isi

1. [Arsitektur Sistem](#1-arsitektur-sistem)
2. [Role & Hak Akses](#2-role--hak-akses)
3. [Database & Model](#3-database--model)
4. [Daftar Route & Endpoint](#4-daftar-route--endpoint)
5. [Fitur Lengkap per Halaman](#5-fitur-lengkap-per-halaman)
6. [Flow Transaksi POS](#6-flow-transaksi-pos)
7. [Flow Laporan Keuangan](#7-flow-laporan-keuangan)
8. [AI Business Insight](#8-ai-business-insight)
9. [Frontend Components](#9-frontend-components)
10. [Settings & Konfigurasi](#10-settings--konfigurasi)

---

## 1. Arsitektur Sistem

```
┌──────────────────────────────────────────────────────────────────┐
│                        Browser (Client)                          │
│  Alpine.js SPA (custom) + Axios + Chart.js + Lucide Icons       │
└──────────────────────────┬───────────────────────────────────────┘
                           │ HTTP / JSON
┌──────────────────────────▼───────────────────────────────────────┐
│                     Laravel 11 Backend                           │
│  ┌──────────────┐  ┌────────────────┐  ┌──────────────────────┐ │
│  │  Controllers  │  │   Services     │  │      Models          │ │
│  │  (10 files)   │  │  PosService,   │  │  (8 Eloquent Models) │ │
│  │               │  │  AiInsight     │  │                      │ │
│  └──────┬───────┘  └────────────────┘  └──────────┬───────────┘ │
└─────────┼──────────────────────────────────────────┼────────────┘
          │                                          │
┌─────────▼──────────────────────────────────────────▼────────────┐
│                        MySQL Database                            │
│  Tables: users, products, categories, transactions,              │
│  transaction_details, expenses, settings, payment_methods,       │
│  activity_logs, notifications, model_has_roles, etc.             │
└──────────────────────────────────────────────────────────────────┘
```

### Stack Detail

| Layer | Teknologi |
|-------|-----------|
| **Backend** | Laravel 11 (PHP 8.2+) |
| **Database** | MySQL |
| **Frontend** | Alpine.js 3.x (custom SPA engine), Tailwind CSS |
| **Asset Bundler** | Vite + Laravel Vite plugin |
| **Auth** | Session-based + Spatie Laravel Permission |
| **Icons** | Lucide (via npm) |
| **Charts** | Chart.js (CDN) |
| **HTTP Client** | Axios (via CDN) |
| **Export** | barryvdh/laravel-dompdf (PDF), maatwebsite/laravel-excel (Excel) |
| **Notifications** | Laravel Database Notifications |
| **AI** | Google Gemini API (generativelanguage.googleapis.com) |

### Custom SPA Engine

Aplikasi menggunakan **SPA custom** (bukan Turbolinks atau Livewire). Mekanismenya ada di `resources/views/layouts/app.blade.php`:

- Semua link navigasi dicegat oleh `spaNavigate()` — fetch HTML via `fetch()`, extract konten dari `#spa-content`, lalu swap DOM
- `executeScriptsAsync()` menjalankan ulang script dan memanggil `Alpine.initTree()` untuk mereinisialisasi komponen Alpine
- Progress bar (`#spa-progressbar`) menampilkan status loading

---

## 2. Role & Hak Akses

| Role | Prefix | Dashboard | Inventory | Reports | POS | Settings | Audit Log | Cashiers | Categories | Payment Methods | Expenses |
|------|--------|-----------|-----------|---------|-----|----------|-----------|----------|------------|----------------|----------|
| **Super Admin** | `/admin` | ✅ | ✅ | ✅ | ✅ | ✅ | ✅ | ✅ | ✅ | ✅ | ✅ |
| **Manager** | `/manager` | ✅ | ✅ | ✅ | ✅ | ✅ | ✅ | ✅ | ✅ | ✅ | ✅ |
| **Kasir** | — | ❌ | ❌ | ❌ | ✅ | ❌ | ❌ | ❌ | ❌ | ❌ | ❌ |
| **Gudang** | `/gudang` | ❌ | ✅ | ❌ | ❌ | ❌ | ❌ | ❌ | ❌ | ❌ | ❌ |

### Middleware

| Middleware | Fungsi |
|------------|--------|
| `auth.custom` | Cek session login + redirect ke `/login` jika blm login |
| `guest.custom` | Hanya untuk halaman login (redirect jika sudah login) |
| `role:X\|Y` | Cek Spatie role — akses ditolak jika tidak punya role yg sesuai |

---

## 3. Database & Model

### 3.1 Tabel & Model

| Tabel | Model | Fillable Fields | Relasi |
|-------|-------|----------------|--------|
| `users` | `User` | name, email, password | HasRoles (Spatie) |
| `products` | `Product` | sku, name, category_id, category, purchase_price, price, selling_price, stock, min_stock | belongsTo(Category) |
| `categories` | `Category` | name, slug | hasMany(Product) |
| `transactions` | `Transaction` | user_id, invoice, total_harga, payment_method, tax_amount, discount_amount, status, customer_name, discount, tax | belongsTo(User), hasMany(TransactionDetail) |
| `transaction_details` | `TransactionDetail` | transaction_id, product_id, qty, harga_beli, harga_jual, subtotal | belongsTo(Transaction), belongsTo(Product) |
| `expenses` | `Expense` | nama_pengeluaran, nominal, tanggal, deskripsi | — |
| `settings` | `Setting` | key, value | — (key-value store) |
| `payment_methods` | `PaymentMethod` | nama_metode, is_active | — |
| `activity_logs` | `ActivityLog` | user_id, action, description | belongsTo(User) |

### 3.2 Key-Value Settings (`settings` table)

| Key | Default | Deskripsi |
|-----|---------|-----------|
| `store_name` | Kios Berkah Raya | Nama toko untuk struk |
| `store_email` | contact@berkahraya.com | Email kontak |
| `store_phone` | 081234567890 | Telepon toko |
| `store_address` | Jl. Pemuda No. 45, Bandung | Alamat toko |
| `receipt_header` | KIOS BERKAH RAYA... | Header struk |
| `receipt_footer` | Terima Kasih... | Footer struk |
| `tax_percent` | 11 | Tarif PPN default (%) |
| `default_discount` | 0 | Diskon default (%) |
| `receipt_size` | 58mm | Ukuran kertas struk (58mm/80mm/A4) |

### 3.3 Payment Methods (`payment_methods` table)

| Default Data | Status |
|-------------|--------|
| Tunai | Active |
| QRIS | Active |
| Transfer Bank | Active |

---

## 4. Daftar Route & Endpoint

### 4.1 Web Routes (Authenticated)

#### Auth (Guest)
| Method | URI | Controller | Nama Route |
|--------|-----|------------|------------|
| GET | `/login` | AuthController@showLogin | `login` |
| POST | `/login` | AuthController@login | — |

#### POS (Role: Kasir, Super Admin)
| Method | URI | Controller | Nama Route |
|--------|-----|------------|------------|
| GET | `/pos` | PosController@launcher | `pos` |
| GET | `/pos/fullscreen` | PosController@index | `pos.fullscreen` |
| POST | `/pos` | PosController@store | `pos.store` |
| GET | `/pos/receipt/{transaction}` | PosController@receipt | `pos.receipt` |

#### Super Admin — `/admin` (Role: Super Admin)
| Method | URI | Controller | Nama Route |
|--------|-----|------------|------------|
| GET | `/admin/dashboard` | DashboardController@index | `admin.dashboard` |
| GET | `/admin/inventory` | InventoryController@index | `admin.inventory` |
| GET | `/admin/reports` | ReportController@index | `admin.reports` |
| GET | `/admin/reports/export/pdf` | ReportController@exportPdf | `admin.reports.export.pdf` |
| GET | `/admin/reports/export/excel` | ReportController@exportExcel | `admin.reports.export.excel` |
| GET | `/admin/audit-logs` | AuditLogController@index | `admin.audit-logs` |
| GET | `/admin/settings` | SettingsController@index | `admin.settings` |
| GET | `/admin/payment-methods` | PaymentMethodController@index | `admin.payment-methods.index` |
| POST | `/admin/payment-methods` | PaymentMethodController@store | `admin.payment-methods.store` |
| PUT | `/admin/payment-methods/{id}` | PaymentMethodController@update | `admin.payment-methods.update` |
| PATCH | `/admin/payment-methods/{id}/toggle` | PaymentMethodController@toggleActive | `admin.payment-methods.toggle` |
| Resource | `/admin/cashiers` | CashierController | `admin.cashiers.*` |
| Resource | `/admin/categories` | CategoryController | `admin.categories.*` |
| Resource | `/admin/expenses` | ExpenseController | `admin.expenses.*` |

#### Manager — `/manager` (Role: Manager)
Sama dengan Super Admin, tanpa `payment-methods` (read-only) dan tanpa `settings` untuk manage user.

#### Gudang — `/gudang` (Role: Gudang)
| Method | URI | Nama Route |
|--------|-----|------------|
| GET | `/gudang/inventory` | `gudang.inventory` |

### 4.2 API Endpoints (Internal)

| Method | URI | Middleware | Fungsi |
|--------|-----|-----------|--------|
| POST | `/api/inventory/store` | Super Admin, Manager, Gudang | Tambah produk |
| PUT | `/api/inventory/update/{id}` | Super Admin, Manager, Gudang | Edit produk |
| DELETE | `/api/inventory/delete/{id}` | Super Admin, Manager, Gudang | Hapus produk |
| POST | `/api/categories/store` | Super Admin, Manager, Gudang | Tambah kategori |
| PUT | `/api/categories/update/{id}` | Super Admin, Manager, Gudang | Edit kategori |
| DELETE | `/api/categories/delete/{id}` | Super Admin, Manager, Gudang | Hapus kategori |
| POST | `/api/expenses` | Super Admin, Manager | Tambah pengeluaran |
| DELETE | `/api/expenses/{expense}` | Super Admin, Manager | Hapus pengeluaran |
| POST | `/api/settings/save/{category}` | Super Admin | Simpan settings |
| POST | `/api/settings/users/store` | Super Admin | Tambah user |
| DELETE | `/api/settings/users/delete/{id}` | Super Admin | Hapus user |
| POST | `/api/settings/payment-methods` | Super Admin | Tambah metode bayar |
| PATCH | `/api/settings/payment-methods/{id}/toggle` | Super Admin | Aktif/nonaktif metode |
| DELETE | `/api/settings/payment-methods/{id}` | Super Admin | Hapus metode |
| GET | `/admin/api/dashboard/low-stock` | Super Admin, Manager | Data stok menipis (JSON) |
| GET | `/admin/api/dashboard/sales-trend` | Super Admin, Manager | Data tren penjualan (JSON) |
| POST | `/notifications/{id}/read` | Auth | Tandai notifikasi dibaca |
| POST | `/notifications/read-all` | Auth | Tandai semua dibaca |

---

## 5. Fitur Lengkap per Halaman

### 5.1 Login (`auth/login.blade.php`)

**Deskripsi:** Halaman login dengan form email + password  
**Controller:** `AuthController`  
**Fitur:**
- Validasi form dengan custom error messages (Bahasa Indonesia)
- Remember me checkbox
- Redirect berdasarkan role setelah login (Kasir → POS, Super Admin → Dashboard Admin, dll)
- Session flash messages (login_success, logout_success)

### 5.2 Dashboard (`dashboard.blade.php`)

**Route:** `/admin/dashboard` | `/manager/dashboard`  
**Controller:** `DashboardController@index`  

**Komponen UI:**
1. **AI Business Insight Banner** — Card gradient indigo-to-slate dengan icon sparkles. Menampilkan analisis bisnis dari Gemini API atau fallback local. Label "AI Advisor".
2. **Stat Cards (x4):**
   - Total Stok (jumlah semua produk)
   - Penjualan Hari Ini (omzet hari ini)
   - Total Transaksi (count all transactions)
   - Omzet Bulan Ini (sum total_harga where status=success)
3. **Low Stock Alert** — Tabel 5 produk dengan stok <= min_stock atau stok <= 5
4. **Aktivitas Kasir Terakhir** — 5 transaksi terbaru dengan nama kasir
5. **Tren Penjualan 7 Hari** — Chart.js line chart per hari (Senin-Minggu)
6. **Range Filter Dropdown** — 7 Hari, Hari Ini, Bulan Ini, Custom Range
7. **Tombol Kelola** — shortcut ke Inventory, Reports, POS

**Data yang dikirim ke view:**
`$total_stok`, `$total_penjualan_hari_ini`, `$total_transaksi`, `$stok_menipis`, `$aktivitas_kasir`, `$laporan_keuangan_bulanan`, `$tren_penjualan_mingguan`, `$ai_insight`

### 5.3 Inventaris (`inventory.blade.php`)

**Route:** `/admin/inventory` | `/manager/inventory` | `/gudang/inventory`  
**Controller:** `InventoryController@index`  

**Fitur:**
- **Tab Daftar Inventaris** — Tabel produk (SKU, Nama, Kategori, Stok, Min Stok, Harga Beli, Harga Jual)
  - Search by nama/SKU
  - Filter by kategori (dropdown)
  - Filter stok: All, Stok Menipis, Stok Habis
  - Sorting klik header kolom (ASC/DESC)
  - Inline edit stok (klik angka stok → modal edit stok)
  - CRUD produk via modal (Tambah/Edit/Hapus)
  - Status bar stok (progress bar visual)
- **Tab Kelola Kategori** — CRUD kategori (nama + slug otomatis)
- **Tab Mutasi Stok** — Log transaksi keluar-masuk (10 terakhir)

**Fitur Khusus:**
- Product Observer (`ProductObserver`) untuk logging otomatis perubahan stok
- Low Stock Notification — notifikasi database ke user Super Admin/Manager/Gudang saat stok <= 5

### 5.4 POS Kasir (`pos/index.blade.php`)

**Route:** `/pos/fullscreen`  
**Controller:** `PosController@index`  

**Deskripsi:** Layar penuh POS (standalone, tidak via SPA layout) dengan Alpine.js component `posEngine()`.

**Komponen UI:**
1. **Top Navbar** — Logo, jam real-time (Alpine $interval), nama kasir, tombol kembali ke dashboard
2. **Panel Kiri — Produk:**
   - Search produk by nama
   - Filter kategori (pill buttons)
   - Grid produk (card: nama, harga, stok)
   - Klik card → tambah ke cart
3. **Panel Kanan — Cart:**
   - Daftar item (nama, qty ±, subtotal, hapus)
   - Input Nama Pelanggan
   - Diskon (%) — default dari Settings
   - PPN (%) — default dari Settings
   - Subtotal, Diskon, PPN, Grand Total (kalkulasi real-time)
   - Pilih Metode Bayar (dari Payment Methods aktif)
   - Input Jumlah Bayar + hitung Kembalian otomatis
   - Tombol **Bayar** → POST ke `PosController@store`
4. **Modal Bayar** — Konfirmasi pembayaran

**Kalkulasi:**
```
Subtotal       = Σ(qty × harga_jual)
Diskon amount  = Subtotal × (discountPercent / 100)
PPN amount     = (Subtotal - Diskon amount) × (taxPercent / 100)
Grand Total    = Subtotal - Diskon amount + PPN amount
```

### 5.5 Struk Belanja (`pos/receipt.blade.php`)

**Route:** `/pos/receipt/{transaction}`  
**Controller:** `PosController@receipt`  

**Deskripsi:** Halaman cetak struk thermal (standalone, CSS @media print).  
**Fitur:**
- Ukuran kertas dinamis dari Settings (`58mm` / `80mm` / `A4`)
- Nama & alamat toko dari Settings
- Nomor Invoice, Tanggal, Kasir, Metode Bayar
- Daftar item (nama, qty, harga, subtotal)
- Diskon, PPN, Grand Total
- Jumlah Bayar, Kembalian
- Tombol Cetak / Kembali
- CSS khusus untuk print (sembunyikan navbar, sidebar, dll)

### 5.6 Laporan Keuangan (`reports.blade.php`)

**Route:** `/admin/reports` | `/manager/reports`  
**Controller:** `ReportController@index`  

**Fitur:**
- **Filter Periode** — Minggu Ini, Bulan Ini, Kuartal Terakhir, Tahun Ini, Custom Range (start_date - end_date)
- **Card Financial Summary (3):**
  - Total Omzet (dengan jumlah transaksi)
  - Pendapatan Bersih (gross_profit - total_pengeluaran)
  - Laba Kotor (total penjualan - total modal)
- **Chart Tahunan (Line/Area)** — Perbandingan Jan-Jun tahun ini vs tahun lalu (Chart.js)
- **Chart Kategori (Bar)** — Omset per kategori produk (Chart.js)
- **Tabel Omset per Kasir** — Group by user_id
- **Tabel Riwayat Transaksi** — Paginated (15/page), semua transaksi dengan status
- **Modal Pengeluaran (Expense)** — Tombol "Total Pengeluaran" → modal Alpine.js:
  - Daftar pengeluaran periode berjalan
  - Tambah pengeluaran baru (inline form)
  - Hapus pengeluaran
- **Export PDF & Excel** — Download laporan

### 5.7 Pengaturan (`settings.blade.php`)

**Route:** `/admin/settings` | `/manager/settings`  
**Controller:** `SettingsController@index`  

**3 Tab Settings:**

1. **Profil Toko:**
   - Nama Toko, Telepon, Email, Alamat
   - Header Struk (plain text)
   - Footer Struk (plain text)
   - Tombol Simpan → POST `/api/settings/save/profile`

2. **Sistem POS & Keuangan:**
   - Tarif PPN Default (%) — dikirim ke POS
   - Diskon Default (%) — dikirim ke POS
   - Ukuran Kertas Struk (58mm / 80mm / A4) — dikirim ke receipt
   - Metode Pembayaran (Tunai, QRIS, Transfer Bank) — add/edit/toggle active/hapus
   - Tombol Simpan → POST `/api/settings/save/pos`

3. **Pengguna & Hak Akses (Super Admin only):**
   - Daftar users dengan role & status
   - Tambah user baru (name, email, role)
   - Hapus user (kecuali user ID 1 dan diri sendiri)

### 5.8 Audit Log (`audit/index.blade.php`)

**Route:** `/admin/audit-logs` | `/manager/audit-logs`  
**Controller:** `AuditLogController@index`  

**Fitur:**
- Tabel log aktivitas (User, Aksi, Deskripsi, Waktu)
- Search by deskripsi, aksi, atau nama user
- Pagination 15/page

### 5.9 Manajemen Kategori (`admin/categories/*`)

**Route:** CRUD `/admin/categories` | `/manager/categories`  
**Controller:** `CategoryController`  

**Fitur:**
- Index: daftar kategori + jumlah produk per kategori
- Create: form nama → auto-generate slug
- Edit: update nama → re-generate slug (dengan unique check)
- Delete: soft cascade ke products

### 5.10 Manajemen Kasir (`admin/cashiers/*`)

**Route:** CRUD `/admin/cashiers` | `/manager/cashiers`  
**Controller:** `CashierController`  

**Fitur:**
- Index: daftar user dengan role Kasir (paginated)
- Create: name, email, password + confirm
- Edit: name, email, password (optional)
- Delete: hapus akun kasir

### 5.11 Manajemen Pengeluaran (`admin/expenses/*`)

**Route:** CRUD `/admin/expenses` | `/manager/expenses`  
**Controller:** `ExpenseController`  

**Fitur:**
- Index: daftar pengeluaran (paginated) + total nominal
- Create: tanggal, nama pengeluaran, nominal, deskripsi
- Edit: update semua field
- Delete: hapus catatan

### 5.12 Metode Pembayaran (`admin/payment-methods`)

**Route:** `/admin/payment-methods` | `/manager/payment-methods`  
**Controller:** `PaymentMethodController`  

**Fitur:**
- Index: daftar metode bayar + status active/inactive
- Store: tambah metode baru
- Update: edit nama + status
- Toggle Active: aktif/nonaktif (via PATCH)
- Seeder otomatis jika tabel kosong (Tunai, QRIS, Transfer Bank)

---

## 6. Flow Transaksi POS

```
User memilih produk → klik card
        │
        ▼
Produk masuk ke cart (Alpine array)
Qty bisa diubah (±), item bisa dihapus
        │
        ▼
Kalkulasi real-time:
  - Subtotal
  - Diskon (dari input %)
  - PPN (dari input %)
  - Grand Total
        │
        ▼
Input Nama Pelanggan (optional)
Pilih Metode Bayar (dari DB payment_methods)
Input Jumlah Bayar → Kembalian otomatis
        │
        ▼
Klik "Bayar"
        │
        ▼
POST /pos → PosController@store
        │
        ├── PosService::processCheckout()
        │     ├── DB::transaction()
        │     │   ├── Lock produk (lockForUpdate)
        │     │   ├── Cek stok → decrement stock
        │     │   ├── Hitung subtotal, diskon, PPN, total
        │     │   ├── Create Transaction
        │     │   ├── Insert TransactionDetails
        │     │   ├── Log ActivityLog
        │     │   ├── Cek low stock → Notifikasi
        │     │   └── Return Transaction
        │     └── Hapus cache reports
        │
        ├── Response JSON → { print_url }
        │
        ▼
Buka URL struk (pos.receipt)
        │
        ▼
Cetak struk thermal (window.print())
```

### Detail Kalkulasi (PosService)

```php
$subtotal       = Σ(qty × product->price)
$discountAmount = ($subtotal × $discountPercent) / 100
$taxAmount      = (($subtotal - $discountAmount) × $taxPercent) / 100
$totalHarga     = $subtotal - $discountAmount + $taxAmount
```

### Data Flow POS → Struk

| POS (index) | Receipt | Source |
|-------------|---------|--------|
| `discountPercent` | `$transaction->discount` | User input / Setting default |
| `taxPercent` | `$transaction->tax` | User input / Setting default |
| `$paymentMethods` | `$paymentMethod` | PaymentMethods table |
| `$defaultDiscount` | — | Settings: `default_discount` |
| `$defaultTaxPercent` | — | Settings: `tax_percent` |
| — | `$receiptSize` | Settings: `receipt_size` |
| — | `$shopName` | Settings: `store_name` |
| — | `$shopAddress` | Settings: `store_address` |
| — | `$shopPhone` | Settings: `store_phone` |

---

## 7. Flow Laporan Keuangan

```
ReportController@index(Request $request)
        │
        ├── Parse period (week/month/quarter/year/custom)
        │
        ├── buildMonthlyReportData()
        │     ├── Cache key: reports:sales-summary:{start}:{end} (1 jam)
        │     ├── getMonthlyFinancialSummary()
        │     │     ├── total_omzet (SUM total_harga)
        │     │     ├── gross_profit (SUM((harga_jual - harga_beli) * qty))
        │     │     ├── total_pengeluaran (SUM nominal expenses)
        │     │     ├── net_revenue (= gross_profit - total_pengeluaran)
        │     │     ├── average_ticket (= total_omzet / count)
        │     │     └── jumlah_transaksi
        │     │
        │     ├── Transactions (paginated)
        │     ├── Cashier Revenues (group by user_id)
        │     ├── Category Performance (Chart data)
        │     ├── Top Selling Products (5 terlaris)
        │     └── Monthly Comparison (Chart: Jan-Jun this year vs last year)
        │
        └── View: reports.blade.php
              ├── Financial Summary Cards
              ├── Monthly Comparison Chart (Chart.js)
              ├── Category Performance Chart (Chart.js)
              ├── Omset per Kasir Table
              ├── Riwayat Transaksi Table (paginated)
              ├── Modal Pengeluaran (Alpine.js)
              └── Export buttons (PDF / Excel)
```

---

## 8. AI Business Insight

### Arsitektur

```
DashboardController@index
  └─ cache()->remember('ai_business_insight', 1800 detik)
       └─ AiInsightService::getBusinessInsights([
            'daily_trend' => [...]
          ])
            ├── gatherMetrics()
            │     ├── total_omset (month)
            │     ├── transaction_count
            │     ├── avg_transaction_value
            │     ├── top_products (top 3 by qty)
            │     ├── bottom_products (bottom 3)
            │     ├── total_products
            │     ├── low_stock_count
            │     └── daily_trend (7 hari)
            │
            ├── tryGeminiApi(metrics)
            │     ├── buildPrompt() → string dari data array
            │     ├── POST https://generativelanguage.googleapis.com/v1beta/...
            │     │     ├── Timeout: 15s, connect_timeout: 5s
            │     │     ├── Retry: 1x jika 429 atau ConnectionException
            │     │     └── Temperature: 0.7, maxOutputTokens: 350
            │     │
            │     ├── Success → parse response → return insight
            │     ├── HTTP error → log + return user-friendly message
            │     ├── 429 → retry → return null (→ fallback)
            │     └── Exception → log + return null (→ fallback)
            │
            └── generateLocalInsight(metrics)
                  └── Rule-based fallback (if omset > 5jt, 1jt, etc.)
```

### Prompt ke Gemini

Prompt berisi data real dalam format teks rapi:
- Ringkasan Keuangan (omset, total transaksi, rata-rata)
- Tren Penjualan 7 Hari
- Top 3 & Bottom 3 produk (dengan stok)
- Total produk & stok menipis

System instruction: "Konsultan Bisnis UMKM profesional di Indonesia. Berikan rekomendasi operasional konkret dalam 1 paragraf maks 4 kalimat. Bahasa Indonesia."

### Fallback (Saat AI Gagal)

`generateLocalInsight()` menghasilkan insight berbasis aturan sederhana:
- Threshold omset (5jt+ → "Kinerja baik", 1jt+ → "Mulai tumbuh", <1jt → "Perlu dorongan")
- Produk terlaris → saran promo bundle
- Produk kurang laku → saran diskon/evaluasi
- Stok menipis → saran restok
- Hari tertinggi/terendah dari tren 7 hari

---

## 9. Frontend Components

### 9.1 Layout (`layouts/app.blade.php`)

- **Sidebar** — Navigasi utama dengan icon Lucide, indikator active page, badge notifikasi, animated gradient background
- **Topbar** — Breadcrumb, notifikasi bell (dropdown dengan unread count), avatar user, tombol toggle sidebar
- **SPA Content Container** — `<div id="spa-content">` untuk SPA swapping
- **Toast Notification System** — Alpine component dengan auto-dismiss 4 detik
- **SPA Progress Bar** — Fixed top bar dengan animasi width
- **Notifikasi Dropdown** — Daftar notifikasi + mark as read/mark all as read

### 9.2 Alpine.js Components

| Component | File | Fungsi |
|-----------|------|--------|
| `posEngine()` | `pos/index.blade.php` (inline) | Engine POS fullscreen (cart, kalkulasi, bayar) |
| `reportsComponent` | `reports.blade.php` (Alpine.data) | Modal pengeluaran (expenses CRUD) |
| `inventoryComponent()` | `inventory.blade.php` (inline) | CRUD produk, filter, search, sorting, kategori |
| `settingsComponent()` | `settings.blade.php` (inline) | Multi-tab settings (profile, pos, users) |
| Layout toast | `layouts/app.blade.php` (inline) | Sistem notifikasi toast |
| Layout notif | `layouts/app.blade.php` (inline) | Dropdown notifikasi |

### 9.3 CSS & Styling

- **Framework:** Tailwind CSS (via Vite)
- **Font:** Outfit (heading), system fonts (body)
- **Icons:** Lucide (`i data-lucide="..."`)
- **Custom CSS:** 
  - `pos.css` — styling khusus POS (grid produk, cart)
  - Custom scrollbar (webkit)
  - Sidebar animated gradient (`@keyframes sidebar-gradient`)
  - SPA progress bar animation

### 9.4 JavaScript Libraries (via CDN)

| Library | Version | Lokasi |
|---------|---------|--------|
| Alpine.js | 3.x | `resources/js/app.js` (npm) |
| Axios | latest | CDN di `layouts/app.blade.php` |
| Chart.js | 4.x | CDN di `reports.blade.php` |
| Lucide | latest | `resources/js/app.js` (npm) |

---

## 10. Settings & Konfigurasi

### 10.1 File Environment (`.env`)

| Key | Contoh Value | Fungsi |
|-----|-------------|--------|
| `APP_NAME` | SmartBiz UMKM | Nama aplikasi |
| `GEMINI_API_KEY` | AIzaSyC... | API Key Google Gemini |

### 10.2 Service Config (`config/services.php`)

```php
'gemini' => [
    'key' => env('GEMINI_API_KEY'),
],
```

### 10.3 Cache Strategy

| Cache Key | Duration | Lokasi |
|-----------|----------|--------|
| `ai_business_insight` | 1800s (30 menit) | DashboardController |
| `reports:sales-summary:{start}:{end}` | 3600s (1 jam) | ReportController |
| `pos/store` | — | Clear cache reports on new transaction |

### 10.4 Notifications

- **Type:** Database Notifications (Laravel)
- **Channel:** in-app (bell icon dropdown)
- **Trigger:** Stok produk ≤ 5 setelah transaksi POS
- **Target:** Super Admin, Manager, Gudang
- **Deduplikasi:** Cek `unreadNotifications()->where('data->product_id', ...)` sebelum create

---

## Catatan Arsitektur Penting

1. **POS bukan SPA** — Halaman `pos/index.blade.php` adalah standalone HTML (tidak mewarisi `layouts.app`), diakses via route `/pos/fullscreen`. Launcher (`/pos`) adalah halaman perantara sebelum fullscreen.

2. **Cache invalidation** — Setelah transaksi POS berhasil, cache laporan keuangan dihapus (`Cache::forget(...)`) dengan key berdasarkan tanggal awal/akhir bulan.

3. **Pessimistic Locking** — `Product::lockForUpdate()` digunakan saat checkout untuk mencegah race condition stok.

4. **Activity Logging** — Setiap transaksi POS dicatat ke `activity_logs` dengan action "Create Transaction".

5. **Alpine.data() vs inline** — Komponen yang perlu persist antar SPA navigasi (reports) menggunakan `Alpine.data('nama', fn)` agar tetap terdaftar di registry Alpine. Komponen sekali pakai (dashboard, inventory) menggunakan inline `x-data`.
