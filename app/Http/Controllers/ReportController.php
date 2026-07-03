<?php

namespace App\Http\Controllers;

use App\Exports\FinancialReportExport;
use App\Models\Expense;
use App\Models\Product;
use App\Models\Transaction;
use App\Models\TransactionDetail;
use Barryvdh\DomPDF\Facade\Pdf;
use Carbon\Carbon;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Schema;
use Maatwebsite\Excel\Facades\Excel;

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
            class_exists(Pdf::class),
            500,
            'Package barryvdh/laravel-dompdf belum terpasang. Jalankan: composer require barryvdh/laravel-dompdf'
        );

        $reportData = $this->buildMonthlyReportData();
        $pdf = Pdf::loadView('reports.pdf', $reportData)->setPaper('a4', 'portrait');

        return $pdf->download('laporan-keuangan-'.now()->format('Y-m').'.pdf');
    }

    public function exportExcel()
    {
        abort_unless(
            class_exists(Excel::class),
            500,
            'Package maatwebsite/excel belum terpasang. Jalankan: composer require maatwebsite/excel'
        );

        return Excel::download(
            new FinancialReportExport($this->buildMonthlyReportData()),
            'laporan-keuangan-'.now()->format('Y-m').'.xlsx'
        );
    }

    private function buildMonthlyReportData(): array
    {
        $startOfMonth = Carbon::now()->startOfMonth();
        $endOfMonth = Carbon::now()->endOfMonth();

        $financialSummary = $this->getMonthlyFinancialSummary($startOfMonth, $endOfMonth);

        // Fetch transactions with user relationship to avoid N+1 queries
        $transactions = Transaction::with('user')->orderBy('created_at', 'desc')->get();

        // 1. Kinerja kategori produk (untuk Bar Chart)
        $categoriesSales = TransactionDetail::query()
            ->whereHas('transaction', function ($query) {
                $query->where('status', 'success');
            })
            ->join('products', 'transaction_details.product_id', '=', 'products.id')
            ->selectRaw('products.category, SUM(transaction_details.subtotal) as total')
            ->groupBy('products.category')
            ->pluck('total', 'products.category')
            ->toArray();

        $categoryLabels = ['Sembako', 'Makanan', 'Minuman', 'Cemilan', 'Rumah Tangga'];
        $categoryData = [];
        foreach ($categoryLabels as $cat) {
            $categoryData[] = (float) ($categoriesSales[$cat] ?? 0.0);
        }

        $categoryPerformance = [
            'labels' => $categoryLabels,
            'data' => $categoryData,
        ];

        // 2. Penjualan terlaris (Top Selling Products)
        $topProductsDetails = TransactionDetail::query()
            ->whereHas('transaction', function ($query) {
                $query->where('status', 'success');
            })
            ->selectRaw('product_id, SUM(qty) as sold_qty, SUM(subtotal) as total_revenue, SUM(harga_beli * qty) as total_cost')
            ->groupBy('product_id')
            ->orderBy('sold_qty', 'desc')
            ->limit(5)
            ->with('product')
            ->get();

        $topProducts = [];
        foreach ($topProductsDetails as $detail) {
            $product = $detail->product;
            if (!$product) {
                continue;
            }
            
            $revenue = (float) $detail->total_revenue;
            $cost = (float) $detail->total_cost;
            $marginVal = $revenue > 0 ? (($revenue - $cost) / $revenue) * 100 : 0.0;

            $topProducts[] = [
                'sku' => $product->sku,
                'name' => $product->name,
                'category' => $product->category,
                'sold_qty' => (int) $detail->sold_qty,
                'total_revenue' => $revenue,
                'margin' => round($marginVal, 1) . '%',
            ];
        }

        // 3. Tren bulanan perbandingan (Tahun Ini vs Tahun Lalu)
        $thisYear = Carbon::now()->year;
        $lastYear = $thisYear - 1;

        $salesThisYear = Transaction::where('status', 'success')
            ->whereBetween('created_at', [
                Carbon::create($thisYear, 1, 1)->startOfDay(),
                Carbon::create($thisYear, 6, 30)->endOfDay()
            ])
            ->select('created_at', 'total_harga')
            ->get()
            ->groupBy(function ($item) {
                return Carbon::parse($item->created_at)->month;
            })
            ->map(function ($group) {
                return (float) $group->sum('total_harga');
            })
            ->toArray();

        $salesLastYear = Transaction::where('status', 'success')
            ->whereBetween('created_at', [
                Carbon::create($lastYear, 1, 1)->startOfDay(),
                Carbon::create($lastYear, 6, 30)->endOfDay()
            ])
            ->select('created_at', 'total_harga')
            ->get()
            ->groupBy(function ($item) {
                return Carbon::parse($item->created_at)->month;
            })
            ->map(function ($group) {
                return (float) $group->sum('total_harga');
            })
            ->toArray();

        $monthlyComparison = [
            'labels' => ['Jan', 'Feb', 'Mar', 'Apr', 'Mei', 'Jun'],
            'this_year' => [],
            'last_year' => [],
        ];

        for ($m = 1; $m <= 6; $m++) {
            $monthlyComparison['this_year'][] = (float) ($salesThisYear[$m] ?? 0.0);
            $monthlyComparison['last_year'][] = (float) ($salesLastYear[$m] ?? 0.0);
        }

        return compact('financialSummary', 'categoryPerformance', 'topProducts', 'monthlyComparison', 'transactions');
    }

    private function getMonthlyFinancialSummary(Carbon $startOfMonth, Carbon $endOfMonth): array
    {
        $cacheKey = sprintf(
            'reports:sales-summary:%s:%s',
            $startOfMonth->toDateString(),
            $endOfMonth->toDateString()
        );

        return Cache::remember($cacheKey, now()->addHour(), function () use ($startOfMonth, $endOfMonth): array {
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

                return [
                    'total_omzet' => $totalOmzet,
                    'gross_profit' => $grossProfit,
                    'total_pengeluaran' => $totalPengeluaran,
                    'net_revenue' => $grossProfit - $totalPengeluaran,
                    'average_ticket' => $averageTicket,
                    'jumlah_transaksi' => $jumlahTransaksi,
                    'revenue_growth' => 0,
                    'profit_growth' => 0,
                ];
            });
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
