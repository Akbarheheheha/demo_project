# Multi-Tenant System — SmartBiz UMKM

> **Arsitektur:** Shared Database, Single Schema — Tenant Isolation via `store_id`  
> **Framework:** Laravel 11  
> **Tanggal Implementasi:** 9 Juli 2026

---

## Daftar Isi

1. [Konsep Arsitektur](#1-konsep-arsitektur)
2. [Struktur File](#2-struktur-file)
3. [Migration](#3-migration)
   - 3.1 [Tabel `stores`](#31-tabel-stores)
   - 3.2 [Tambah `store_id` ke Existing Tables](#32-tambah-store_id-ke-existing-tables)
4. [TenantManager — Service Singleton](#4-tenantmanager--service-singleton)
5. [TenantScope — Global Scope](#5-tenantscope--global-scope)
6. [BelongsToTenant — Trait Inti](#6-belongstotenant--trait-inti)
7. [SetTenantStoreId — Middleware](#7-setenantstoreid--middleware)
8. [Registrasi bootstrap/app.php](#8-registrasi-bootstrapappphp)
9. [Model Store](#9-model-store)
10. [Cara Menggunakan Trait BelongsToTenant](#10-cara-menggunakan-trait-belongstotenant)
11. [Query Without Tenant Scope](#11-query-without-tenant-scope)
12. [Diagram Alur Data](#12-diagram-alur-data)
13. [Panduan Migrasi Data](#13-panduan-migrasi-data)

---

## 1. Konsep Arsitektur

Sistem menggunakan pendekatan **Shared Database — Single Schema**:

- Semua toko berada dalam satu database.
- Baris data dibedakan oleh kolom `store_id` (foreign key ke tabel `stores`).
- Setiap user terdaftar di satu toko (`users.store_id`).
- Saat user login, `store_id` disimpan ke **Container Laravel** via `TenantManager` singleton.
- **Global Scope** (`TenantScope`) otomatis menambahkan `WHERE store_id = ?` ke setiap query model.
- **Event `creating`** pada trait `BelongsToTenant` otomatis mengisi `store_id` saat insert data.

### Alur Request

```
Request masuk
    │
    ▼
SetTenantStoreId middleware
    ├── auth()->user() → ambil store_id
    └── TenantManager::setStoreId(store_id)
    │
    ▼
Controller → Model::all() / Model::create() / dll.
    ├── TenantScope → WHERE store_id = current
    └── BelongsToTenant::creating → set store_id otomatis
```

---

## 2. Struktur File

```
app/
├── Http/
│   └── Middleware/
│       └── SetTenantStoreId.php           # Middleware — set store_id ke container
├── Models/
│   ├── Store.php                           # Model Store (parent tenant)
│   ├── Traits/
│   │   └── BelongsToTenant.php             # Trait — global scope + auto-fill creating
│   └── Scopes/
│       └── TenantScope.php                 # Global Scope — filter WHERE store_id
└── Services/
    └── TenantManager.php                   # Singleton — penyimpan store_id di container

database/
└── migrations/
    ├── 2026_07_09_000001_create_stores_table.php
    └── 2026_07_09_000002_add_store_id_to_tables.php

bootstrap/
└── app.php                                 # Registrasi singleton + middleware
```

---

## 3. Migration

### 3.1 Tabel `stores`

**File:** `database/migrations/2026_07_09_000001_create_stores_table.php`

```php
<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('stores', function (Blueprint $table): void {
            $table->id();
            $table->uuid('uuid')->unique()->index();
            $table->string('name');
            $table->string('slug')->unique()->index();
            $table->string('email')->nullable();
            $table->string('phone')->nullable();
            $table->text('address')->nullable();
            $table->string('logo')->nullable();
            $table->string('subscription_plan')->default('free');
            $table->string('status')->default('active')->index();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('stores');
    }
};
```

**Kolom:**

| Kolom | Tipe | Keterangan |
|-------|------|-----------|
| `id` | bigint (PK) | Auto increment |
| `uuid` | uuid (unique, indexed) | UUID publik untuk API/public reference |
| `name` | string | Nama toko |
| `slug` | string (unique, indexed) | Slug untuk URL/subdomain |
| `email` | string (nullable) | Email kontak toko |
| `phone` | string (nullable) | Nomor telepon |
| `address` | text (nullable) | Alamat lengkap |
| `logo` | string (nullable) | Path logo |
| `subscription_plan` | string (default: 'free') | Paket langganan |
| `status` | string (default: 'active', indexed) | Status toko (active/inactive/suspended) |
| `timestamps` | — | created_at, updated_at |

### 3.2 Tambah `store_id` ke Existing Tables

**File:** `database/migrations/2026_07_09_000002_add_store_id_to_tables.php`

```php
<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    private const TABLES = [
        'users',
        'products',
        'transactions',
        'expenses',
        'settings',
        'categories',
        'payment_methods',
        'activity_logs',
    ];

    public function up(): void
    {
        foreach (self::TABLES as $tableName) {
            Schema::table($tableName, function (Blueprint $table) use ($tableName): void {
                if (!Schema::hasColumn($tableName, 'store_id')) {
                    $table->foreignId('store_id')
                        ->after('id')
                        ->constrained('stores')
                        ->cascadeOnDelete()
                        ->index();
                }
            });
        }
    }

    public function down(): void
    {
        foreach (self::TABLES as $tableName) {
            Schema::table($tableName, function (Blueprint $table) use ($tableName): void {
                if (Schema::hasColumn($tableName, 'store_id')) {
                    $table->dropConstrainedForeignId('store_id');
                }
            });
        }
    }
};
```

**Tabel yang dimodifikasi (8 tabel):** `users`, `products`, `transactions`, `expenses`, `settings`, `categories`, `payment_methods`, `activity_logs`.

Kolom `store_id` ditambahkan **setelah kolom `id`**, merupakan foreign key ke `stores.id` dengan **`CASCADE ON DELETE`** — jika store dihapus, semua data terkait di tabel-tabel tersebut ikut terhapus.

---

## 4. TenantManager — Service Singleton

**File:** `app/Services/TenantManager.php`

```php
<?php

declare(strict_types=1);

namespace App\Services;

final class TenantManager
{
    private ?int $storeId = null;

    public function setStoreId(?int $storeId): void
    {
        $this->storeId = $storeId;
    }

    public function getStoreId(): ?int
    {
        return $this->storeId;
    }

    public function hasStoreId(): bool
    {
        return $this->storeId !== null;
    }

    public function forget(): void
    {
        $this->storeId = null;
    }
}
```

**Cara Kerja:**
- **Singleton** — satu instance selama lifecycle request.
- `setStoreId(int)` dipanggil oleh middleware `SetTenantStoreId`.
- `getStoreId()` digunakan oleh `TenantScope` dan `BelongsToTenant`.
- `forget()` — reset storeId (berguna untuk operasi super-admin yang perlu lintas toko).

---

## 5. TenantScope — Global Scope

**File:** `app/Models/Scopes/TenantScope.php`

```php
<?php

declare(strict_types=1);

namespace App\Models\Scopes;

use App\Services\TenantManager;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Scope;

final class TenantScope implements Scope
{
    public function apply(Builder $builder, Model $model): void
    {
        $storeId = app(TenantManager::class)->getStoreId();

        if ($storeId !== null) {
            $builder->where($model->getQualifiedStoreIdColumn(), $storeId);
        }
    }

    public function extend(Builder $builder): void
    {
        $builder->macro('withoutTenancy', function (Builder $builder) {
            return $builder->withoutGlobalScope($this);
        });
    }
}
```

**Cara Kerja:**
- Method `apply()` dijalankan otomatis oleh Eloquent saat query apa pun dipanggil.
- Mengambil `store_id` dari `TenantManager`.
- Menambahkan `WHERE store_id = ?` ke query (dengan qualified column — mencakup table prefix untuk join).
- Method `extend()` menambahkan macro `withoutTenancy()` sehingga bisa dikecualikan per-query.

---

## 6. BelongsToTenant — Trait Inti

**File:** `app/Models/Traits/BelongsToTenant.php`

```php
<?php

declare(strict_types=1);

namespace App\Models\Traits;

use App\Models\Scopes\TenantScope;
use App\Services\TenantManager;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

trait BelongsToTenant
{
    public static function bootBelongsToTenant(): void
    {
        static::addGlobalScope(app(TenantScope::class));

        static::creating(static function (Model $model): void {
            $storeId = app(TenantManager::class)->getStoreId();

            if ($storeId !== null) {
                $model->setAttribute($model->getStoreIdColumn(), $storeId);
            }
        });
    }

    public function store(): BelongsTo
    {
        return $this->belongsTo(\App\Models\Store::class);
    }

    protected function getStoreIdColumn(): string
    {
        return 'store_id';
    }

    public function getQualifiedStoreIdColumn(): string
    {
        return $this->qualifyColumn($this->getStoreIdColumn());
    }
}
```

**Komponen Trait:**

| Method | Fungsi |
|--------|--------|
| `bootBelongsToTenant()` | Boot trait — registrasi global scope + event `creating` |
| `store()` | Relasi `BelongsTo` ke model `Store` |
| `getStoreIdColumn()` | Nama kolom identifier (default: `store_id`), bisa di-override |
| `getQualifiedStoreIdColumn()` | Qualified column name (e.g. `products.store_id`) untuk join query |

**Boot Trait — Dua Aksi:**
1. **`addGlobalScope(TenantScope)`** — semua query model akan difilter otomatis.
2. **`creating` event** — saat insert data baru, `store_id` diisi otomatis dari `TenantManager::getStoreId()`.

---

## 7. SetTenantStoreId — Middleware

**File:** `app/Http/Middleware/SetTenantStoreId.php`

```php
<?php

declare(strict_types=1);

namespace App\Http\Middleware;

use App\Services\TenantManager;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

final class SetTenantStoreId
{
    public function __construct(
        private readonly TenantManager $tenantManager,
    ) {}

    public function handle(Request $request, Closure $next): Response
    {
        /** @var \App\Models\User|null $user */
        $user = $request->user();

        if ($user !== null && $user->store_id !== null) {
            $this->tenantManager->setStoreId((int) $user->store_id);
        }

        return $next($request);
    }
}
```

**Cara Kerja:**
- Middleware ini di-append ke **grup `web`** (semua route web).
- Setiap request yang masuk:
  - Cek apakah user sudah login (`$request->user()`).
  - Jika login dan memiliki `store_id`, simpan ke `TenantManager`.
- Middleware berjalan setelah `StartSession` dan `Authenticate`, sehingga user tersedia.

---

## 8. Registrasi bootstrap/app.php

**File:** `bootstrap/app.php`

```php
<?php

use App\Services\TenantManager;
use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use Illuminate\Http\Request;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware): void {
        $middleware->alias([
            'auth.custom' => \App\Http\Middleware\CheckLogin::class,
            'guest.custom' => \App\Http\Middleware\RedirectIfLoggedIn::class,
            'role' => \Spatie\Permission\Middleware\RoleMiddleware::class,
            'permission' => \Spatie\Permission\Middleware\PermissionMiddleware::class,
            'role_or_permission' => \Spatie\Permission\Middleware\RoleOrPermissionMiddleware::class,
        ]);

        $middleware->appendToGroup('web', \App\Http\Middleware\SetTenantStoreId::class);
    })
    ->withExceptions(function (Exceptions $exceptions): void {
        $exceptions->shouldRenderJsonWhen(
            fn (Request $request) => $request->is('api/*'),
        );
    })->create()
    ->singleton(TenantManager::class, fn () => new TenantManager());
```

**Dua registrasi penting:**

1. **`singleton(TenantManager::class, ...)`** — TenantiManager terdaftar sebagai singleton di Container. Satu instance digunakan oleh Middleware, TenantScope, dan BelongsToTenant.

2. **`appendToGroup('web', SetTenantStoreId::class)`** — Middleware ditambahkan ke grup `web` sehingga berjalan di semua route web (tidak perlu daftar manual per route).

---

## 9. Model Store

**File:** `app/Models/Store.php`

```php
<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Store extends Model
{
    use HasUuids;

    protected $fillable = [
        'uuid',
        'name',
        'slug',
        'email',
        'phone',
        'address',
        'logo',
        'subscription_plan',
        'status',
    ];

    protected function casts(): array
    {
        return [
            'uuid' => 'string',
        ];
    }

    public function uniqueIds(): array
    {
        return ['uuid'];
    }

    public function users(): HasMany
    {
        return $this->hasMany(User::class);
    }

    public function products(): HasMany
    {
        return $this->hasMany(Product::class);
    }

    public function transactions(): HasMany
    {
        return $this->hasMany(Transaction::class);
    }

    public function expenses(): HasMany
    {
        return $this->hasMany(Expense::class);
    }

    public function settings(): HasMany
    {
        return $this->hasMany(Setting::class);
    }

    public function categories(): HasMany
    {
        return $this->hasMany(Category::class);
    }

    public function paymentMethods(): HasMany
    {
        return $this->hasMany(PaymentMethod::class);
    }

    public function activityLogs(): HasMany
    {
        return $this->hasMany(ActivityLog::class);
    }
}
```

Model `Store` menggunakan trait `HasUuids` — kolom `uuid` otomatis terisi saat create. Relasi `HasMany` disediakan untuk semua model tenant.

---

## 10. Cara Menggunakan Trait BelongsToTenant

Setiap model yang perlu di-scope berdasarkan `store_id` cukup menggunakan trait `BelongsToTenant` dan menambahkan `store_id` ke `$fillable`.

### Langkah

```php
<?php

namespace App\Models;

use App\Models\Traits\BelongsToTenant;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
    use HasFactory, BelongsToTenant;          // <── tambahkan trait

    protected $fillable = [
        'store_id',                           // <── tambahkan ke fillable
        'sku',
        'name',
        'price',
        'stock',
    ];
}
```

### Yang Terjadi Otomatis

| Operasi | Sebelum | Sesudah |
|---------|---------|---------|
| `Product::all()` | `SELECT * FROM products` | `SELECT * FROM products WHERE store_id = 1` |
| `Product::find(5)` | `SELECT * FROM products WHERE id = 5` | `SELECT * FROM products WHERE id = 5 AND store_id = 1` |
| `Product::create([...])` | Harus set `store_id` manual | `store_id` terisi otomatis dari user login |
| `$product->store` | Error (relasi tidak ada) | Mengembalikan `Store` pemilik |
| `Product::where('price', '>', 1000)` | Hanya filter price | `WHERE price > 1000 AND store_id = 1` |

### Model yang Sudah Di-update

| Model | File | Status |
|-------|------|--------|
| `User` | `app/Models/User.php` | ✅ Trait + fillable `store_id` |
| `Product` | `app/Models/Product.php` | ✅ Trait + fillable `store_id` |
| `Transaction` | `app/Models/Transaction.php` | ✅ Trait + fillable `store_id` |
| `Expense` | `app/Models/Expense.php` | ✅ Trait + fillable `store_id` |
| `Setting` | `app/Models/Setting.php` | ✅ Trait + fillable `store_id` |
| `Category` | `app/Models/Category.php` | ✅ Trait + fillable `store_id` |
| `PaymentMethod` | `app/Models/PaymentMethod.php` | ✅ Trait + fillable `store_id` |
| `ActivityLog` | `app/Models/ActivityLog.php` | ✅ Trait + fillable `store_id` |

> **Catatan:** Model `TransactionDetail` tidak memiliki trait karena data transaksi detail sudah ter-isolasi melalui relasi `belongsTo(Transaction::class)` — query `TransactionDetail` akan tetap terfilter jika diakses melalui relasi (`$transaction->details`). Jika suatu saat perlu query langsung ke `TransactionDetail`, tambahkan trait `BelongsToTenant`.

---

## 11. Query Without Tenant Scope

Untuk kasus di mana super-admin perlu melihat data lintas toko (misalnya laporan global), gunakan macro `withoutTenancy()`:

```php
// Tanpa scope tenant — semua data
$allProducts = Product::withoutTenancy()->get();

// Scope tetap aktif untuk query lain
$storeProducts = Product::all(); // tetap terfilter
```

Macro ini tersedia di semua model yang menggunakan trait `BelongsToTenant`.

---

## 12. Diagram Alur Data

```
                        ┌──────────────────────┐
                        │   Database (Shared)   │
                        │  ┌──────────────────┐ │
                        │  │ stores           │ │
                        │  │ id: 1, name: ... │ │
                        │  └────────┬─────────┘ │
                        │           │           │
                        │  ┌────────▼─────────┐ │
                        │  │ products         │ │
                        │  │ store_id: 1  ←───┼─┼── FK (CASCADE)
                        │  │ name: ...        │ │
                        │  └──────────────────┘ │
                        │  ┌──────────────────┐ │
                        │  │ transactions     │ │
                        │  │ store_id: 1  ←───┼─┼── FK
                        │  │ total: ...       │ │
                        │  └──────────────────┘ │
                        │  ...dst               │
                        └──────────────────────┘
                                ▲
                                │
┌──────────────┐    ┌───────────┴──────────┐    ┌──────────────────┐
│   Browser    │───►│   Middleware Layer   │───►│  Eloquent Layer  │
│  (Request)   │    │ SetTenantStoreId     │    │ TenantScope      │
│              │    │ ↓                    │    │ ↓                │
│  auth user   │    │ TenantManager        │    │ WHERE store_id=X │
│  store_id=1  │    │ .setStoreId(1)       │    │                  │
└──────────────┘    └──────────────────────┘    │ BelongsToTenant  │
                                                │ → auto-fill      │
                                                │ store_id on save │
                                                └──────────────────┘
```

---

## 13. Panduan Migrasi Data

### 13.1 Persiapan

```bash
# 1. Cadangkan database (jika production)
php artisan db:dump  # atau backup manual via tools

# 2. Buat store default untuk data existing
#    Data yang sudah ada harus di-assign ke store_id tertentu
```

### 13.2 Buat Store Default

Buat store untuk data yang sudah ada sebelum menjalankan migration `add_store_id_to_tables`:

```php
// DatabaseSeeder.php atau migration terpisah
use App\Models\Store;
use Illuminate\Support\Str;

$store = Store::create([
    'uuid' => (string) Str::uuid(),
    'name' => 'Toko Utama',
    'slug' => 'toko-utama',
    'subscription_plan' => 'free',
    'status' => 'active',
]);
```

### 13.3 Assign store_id ke Data Existing

```php
// Seeder / migration data
$storeId = 1; // ID store default yang dibuat

\App\Models\User::query()->update(['store_id' => $storeId]);
\App\Models\Product::query()->update(['store_id' => $storeId]);
\App\Models\Transaction::query()->update(['store_id' => $storeId]);
// ... untuk semua tabel tenant
```

### 13.4 Jalankan Migration

```bash
php artisan migrate
```

### 13.5 Verifikasi

```bash
php artisan tinker
```

```php
// Pastikan scope bekerja
Auth::loginUsingId(1); // user dengan store_id tertentu
\App\Models\Product::all(); // harus terfilter

// Pastikan auto-fill bekerja
$product = \App\Models\Product::create(['name' => 'Test', 'price' => 1000]);
$product->store_id; // harus terisi otomatis

// Pastikan relasi bekerja
$product->store; // harus mengembalikan object Store
```

---

> **Dokumentasi ini dibuat untuk SmartBiz UMKM**  
> **Framework:** Laravel 11 | **Multi-Tenant Strategy:** Shared Database (store_id) | **Jul 2026**
