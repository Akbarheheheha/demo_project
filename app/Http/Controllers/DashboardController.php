<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\Transaction;
use Carbon\Carbon;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    /**
     * Display the admin dashboard.
     *
     * @return \Illuminate\View\View
     */
    public function index()
    {
        // a. $total_stok (Sum dari kolom 'stok' di tabel products)
        $total_stok = (int) Product::sum('stock');

        // b. $total_penjualan_hari_ini (Sum dari kolom 'total_harga' di tabel transactions di mana created_at adalah hari ini)
        $total_penjualan_hari_ini = (float) Transaction::whereDate('created_at', Carbon::today())
            ->where('status', 'success')
            ->sum('total_harga');

        // c. $total_transaksi (Count dari tabel transactions)
        $total_transaksi = Transaction::count();

        // d. $stok_menipis (Ambil 5 produk dengan stok <= 5, order by stok asc)
        $stok_menipis = Product::where('stock', '<=', 5)
            ->orderBy('stock', 'asc')
            ->limit(5)
            ->get();

        // e. $aktivitas_kasir (Ambil 5 transaksi terakhir, beserta relasi ke user/kasir yang melakukannya)
        $aktivitas_kasir = Transaction::with('user')
            ->orderBy('created_at', 'desc')
            ->limit(5)
            ->get();

        // f. $laporan_keuangan_bulanan (Total omzet bulan ini)
        $laporan_keuangan_bulanan = (float) Transaction::whereMonth('created_at', Carbon::now()->month)
            ->whereYear('created_at', Carbon::now()->year)
            ->where('status', 'success')
            ->sum('total_harga');

        // g. $tren_penjualan_mingguan (Mengelompokkan total penjualan per hari selama 7 hari terakhir - Efisien / No N+1 Query)
        $sevenDaysAgo = Carbon::now()->subDays(6)->startOfDay();
        
        $salesData = Transaction::where('status', 'success')
            ->where('created_at', '>=', $sevenDaysAgo)
            ->selectRaw('DATE(created_at) as date, SUM(total_harga) as total')
            ->groupBy('date')
            ->pluck('total', 'date')
            ->toArray();

        $tren_penjualan_mingguan = [
            'labels' => [],
            'data' => []
        ];

        // Set locale ke Bahasa Indonesia untuk penamaan hari
        Carbon::setLocale('id');
        for ($i = 6; $i >= 0; $i--) {
            $date = Carbon::now()->subDays($i);
            $dateString = $date->toDateString();
            $dayName = $date->translatedFormat('l'); // e.g. 'Senin', 'Selasa'
            
            // Gunakan date string langsung (seperti yang disimpan di database)
            $totalSales = 0.0;
            if (array_key_exists($dateString, $salesData)) {
                $totalSales = (float) $salesData[$dateString];
            }

            $tren_penjualan_mingguan['labels'][] = $dayName;
            $tren_penjualan_mingguan['data'][] = $totalSales;
        }

        // Fetch AI Business Insight (Cached for 30 minutes for performance)
        $ai_insight = cache()->remember('ai_business_insight', 1800, function() {
            return (new \App\Services\AiInsightService())->getBusinessInsights();
        });

        return view('dashboard', compact(
            'total_stok',
            'total_penjualan_hari_ini',
            'total_transaksi',
            'stok_menipis',
            'aktivitas_kasir',
            'laporan_keuangan_bulanan',
            'tren_penjualan_mingguan',
            'ai_insight'
        ));
    }

    public function getLowStockApi()
    {
        $stok_menipis = Product::whereColumn('stock', '<=', 'min_stock')
            ->orWhere('stock', '<=', 5)
            ->orderBy('stock', 'asc')
            ->limit(5)
            ->get();

        return response()->json($stok_menipis);
    }
}
