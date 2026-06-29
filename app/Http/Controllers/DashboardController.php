<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class DashboardController extends Controller
{
    public function index()
    {
        // Mock data untuk statistik ringkas
        $stats = [
            'total_sales_today' => 5420000,
            'total_transactions_today' => 84,
            'low_stock_count' => 5,
            'sales_growth' => 12.5, // % kenaikan dari kemarin
            'transactions_growth' => 8.0 // % kenaikan dari kemarin
        ];

        // Mock data tren penjualan mingguan (Senin - Minggu)
        $weeklySales = [
            'labels' => ['Senin', 'Selasa', 'Rabu', 'Kamis', 'Jumat', 'Sabtu', 'Minggu'],
            'data' => [3200000, 4100000, 3800000, 4900000, 5200000, 6800000, 5420000]
        ];

        // Mock data transaksi terbaru
        $recentTransactions = [
            [
                'invoice' => 'TRX-20260629-001',
                'customer' => 'Budi Santoso',
                'time' => '11:45',
                'items_count' => 3,
                'total' => 125000,
                'status' => 'success',
                'payment_method' => 'QRIS'
            ],
            [
                'invoice' => 'TRX-20260629-002',
                'customer' => 'Siti Aminah',
                'time' => '11:30',
                'items_count' => 1,
                'total' => 45000,
                'status' => 'success',
                'payment_method' => 'Tunai'
            ],
            [
                'invoice' => 'TRX-20260629-003',
                'customer' => 'Agus Wijaya',
                'time' => '11:15',
                'items_count' => 5,
                'total' => 380000,
                'status' => 'pending',
                'payment_method' => 'Transfer'
            ],
            [
                'invoice' => 'TRX-20260629-004',
                'customer' => 'Lani Siregar',
                'time' => '10:50',
                'items_count' => 2,
                'total' => 95000,
                'status' => 'success',
                'payment_method' => 'Tunai'
            ],
            [
                'invoice' => 'TRX-20260629-005',
                'customer' => 'Dewi Lestari',
                'time' => '10:10',
                'items_count' => 4,
                'total' => 220000,
                'status' => 'success',
                'payment_method' => 'QRIS'
            ]
        ];

        return view('dashboard', compact('stats', 'weeklySales', 'recentTransactions'));
    }
}
