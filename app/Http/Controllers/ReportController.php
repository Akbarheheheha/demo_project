<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class ReportController extends Controller
{
    public function index()
    {
        // Mock data rangkuman keuangan
        $financialSummary = [
            'net_revenue' => 34840000, // Pendapatan Bersih
            'cogs' => 28200000,        // HPP (Harga Pokok Penjualan)
            'gross_profit' => 6640000,  // Laba Kotor
            'average_ticket' => 65200,  // Rata-rata Nilai Transaksi
            'revenue_growth' => 14.8,   // % pertumbuhan bulanan
            'profit_growth' => 18.2     // % pertumbuhan laba bulanan
        ];

        // Mock data performa kategori produk (untuk Bar Chart)
        $categoryPerformance = [
            'labels' => ['Sembako', 'Makanan', 'Minuman', 'Cemilan', 'Rumah Tangga'],
            'data' => [14200000, 8900000, 5400000, 3800000, 2540000]
        ];

        // Mock data penjualan terlaris (Top Selling Products)
        $topProducts = [
            [
                'sku' => 'SMB-001',
                'name' => 'Beras Pandan Wangi 5kg',
                'category' => 'Sembako',
                'sold_qty' => 120,
                'total_revenue' => 9360000,
                'margin' => '12.8%'
            ],
            [
                'sku' => 'MKN-001',
                'name' => 'Indomie Goreng Spesial',
                'category' => 'Makanan',
                'sold_qty' => 840,
                'total_revenue' => 2940000,
                'margin' => '20%'
            ],
            [
                'sku' => 'SMB-002',
                'name' => 'Minyak Goreng Bimoli 2L',
                'category' => 'Sembako',
                'sold_qty' => 75,
                'total_revenue' => 2887500,
                'margin' => '14.2%'
            ],
            [
                'sku' => 'MNM-003',
                'name' => 'Air Mineral Aqua 600ml',
                'category' => 'Minuman',
                'sold_qty' => 450,
                'total_revenue' => 1800000,
                'margin' => '37.5%'
            ],
            [
                'sku' => 'SNC-002',
                'name' => 'Silverqueen Almond 62g',
                'category' => 'Cemilan',
                'sold_qty' => 95,
                'total_revenue' => 1567500,
                'margin' => '21.2%'
            ]
        ];

        // Mock data tren bulanan perbandingan (Tahun Ini vs Tahun Lalu)
        $monthlyComparison = [
            'labels' => ['Jan', 'Feb', 'Mar', 'Apr', 'Mei', 'Jun'],
            'this_year' => [18500000, 22400000, 25800000, 29000000, 31200000, 34840000],
            'last_year' => [15000000, 16800000, 19200000, 22000000, 25400000, 28100000]
        ];

        return view('reports', compact('financialSummary', 'categoryPerformance', 'topProducts', 'monthlyComparison'));
    }
}
