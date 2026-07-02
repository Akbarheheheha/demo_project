<?php

$_ENV['SESSION_DRIVER'] = 'array';
$_ENV['CACHE_STORE'] = 'array';
$_ENV['DB_CONNECTION'] = 'sqlite';
$_ENV['DB_DATABASE'] = ':memory:';

// Bootstrap Laravel
require __DIR__ . '/../vendor/autoload.php';
$app = require_once __DIR__ . '/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

// Run migrations in memory
Illuminate\Support\Facades\Artisan::call('migrate');

// Mock auth user (skip DB query by using raw object)
$user = new App\Models\User();
$user->id = 1;
$user->name = 'Test User';
Auth::login($user);

// Render view
try {
    // Mock products collection using standard Eloquent collection filled with mock models
    $mockProduct1 = new App\Models\Product();
    $mockProduct1->id = 1;
    $mockProduct1->sku = 'SKU-001';
    $mockProduct1->name = "Product A's Awesome";
    $mockProduct1->stock = 3;
    $mockProduct1->min_stock = 5;

    $stok_menipis = collect([$mockProduct1]);

    $html = view('dashboard', [
        'total_stok' => 100,
        'total_penjualan_hari_ini' => 100000,
        'total_transaksi' => 10,
        'stok_menipis' => $stok_menipis,
        'aktivitas_kasir' => collect([]),
        'laporan_keuangan_bulanan' => 5000000,
        'tren_penjualan_mingguan' => ['labels' => ['Senin'], 'data' => [100000]],
        'ai_insight' => 'Test insight'
    ])->render();

    // Extract the card element
    if (preg_match('/<div class="container_scale bg-white p-6 rounded-2xl border border-slate-200\/80 shadow-sm flex flex-col"[^>]*>/i', $html, $matches)) {
        echo "MATCHED WIDGET CONTAINER:\n";
        echo $matches[0] . "\n";
    } else {
        echo "Widget container not found!\n";
    }
} catch (Exception $e) {
    echo "ERROR: " . $e->getMessage() . "\n";
}
