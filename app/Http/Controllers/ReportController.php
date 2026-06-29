<?php

namespace App\Http\Controllers;

use App\Exports\FinancialReportExport;
use App\Models\Expense;
use App\Models\Transaction;
use App\Models\TransactionDetail;
use Carbon\Carbon;
use Illuminate\Support\Facades\Schema;

class ReportController extends Controller
{
    public function index()
    {
        $reportData = $this->buildMonthlyReportData();

        return view('reports', $reportData);
    }

    public function exportPdf()
    {
        abort_unless(
            class_exists(\Barryvdh\DomPDF\Facade\Pdf::class),
            500,
            'Package barryvdh/laravel-dompdf belum terpasang. Jalankan: composer require barryvdh/laravel-dompdf'
        );

        $reportData = $this->buildMonthlyReportData();
        $pdf = \Barryvdh\DomPDF\Facade\Pdf::loadView('reports.pdf', $reportData)->setPaper('a4', 'portrait');

        return $pdf->download('laporan-keuangan-' . now()->format('Y-m') . '.pdf');
    }

    public function exportExcel()
    {
        abort_unless(
            class_exists(\Maatwebsite\Excel\Facades\Excel::class),
            500,
            'Package maatwebsite/excel belum terpasang. Jalankan: composer require maatwebsite/excel'
        );

        return \Maatwebsite\Excel\Facades\Excel::download(
            new FinancialReportExport($this->buildMonthlyReportData()),
            'laporan-keuangan-' . now()->format('Y-m') . '.xlsx'
        );
    }

    private function buildMonthlyReportData(): array
    {
        $startOfMonth = Carbon::now()->startOfMonth();
        $endOfMonth = Carbon::now()->endOfMonth();

        $successfulTransactions = Transaction::query()
            ->where('status', 'success')
            ->whereBetween('created_at', [$startOfMonth, $endOfMonth]);

        $totalOmzet = (float) (clone $successfulTransactions)->sum('total_harga');
        $jumlahTransaksi = (int) (clone $successfulTransactions)->count();
        $averageTicket = $jumlahTransaksi > 0 ? $totalOmzet / $jumlahTransaksi : 0;

        $grossProfit = $this->calculateGrossProfit($startOfMonth, $endOfMonth);

        $totalPengeluaran = (float) Expense::query()
            ->whereBetween('tanggal', [$startOfMonth->toDateString(), $endOfMonth->toDateString()])
            ->sum('nominal');

        $netRevenue = $grossProfit - $totalPengeluaran;

        $financialSummary = [
            'total_omzet' => $totalOmzet,
            'gross_profit' => $grossProfit,
            'total_pengeluaran' => $totalPengeluaran,
            'net_revenue' => $netRevenue,
            'average_ticket' => $averageTicket,
            'jumlah_transaksi' => $jumlahTransaksi,
            'revenue_growth' => 0,
            'profit_growth' => 0,
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

        return compact('financialSummary', 'categoryPerformance', 'topProducts', 'monthlyComparison');
    }

    private function calculateGrossProfit(Carbon $startOfMonth, Carbon $endOfMonth): float
    {
        if (! Schema::hasTable('transaction_details')) {
            return 0;
        }

        return (float) TransactionDetail::query()
            ->whereHas('transaction', function ($query) use ($startOfMonth, $endOfMonth) {
                $query->where('status', 'success')
                    ->whereBetween('created_at', [$startOfMonth, $endOfMonth]);
            })
            ->selectRaw('COALESCE(SUM((harga_jual - harga_beli) * qty), 0) as gross_profit')
            ->value('gross_profit');
    }
}
