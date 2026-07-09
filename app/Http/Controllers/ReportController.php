<?php

namespace App\Http\Controllers;

use App\Exports\FinancialReportExport;
use App\Models\Expense;
use App\Models\Product;
use App\Models\Transaction;
use App\Models\TransactionDetail;
use Barryvdh\DomPDF\Facade\Pdf;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Schema;
use Maatwebsite\Excel\Facades\Excel;

class ReportController extends Controller
{
    public function index(Request $request)
    {
        $period = $request->query('period', 'month');
        $startDate = $request->query('start_date');
        $endDate = $request->query('end_date');
        
        $reportData = $this->buildMonthlyReportData(paginateTransactions: true, period: $period, startDateReq: $startDate, endDateReq: $endDate);
        $reportData['activePeriod'] = $period;
        $reportData['startDate'] = $startDate;
        $reportData['endDate'] = $endDate;

        return view('reports', $reportData);
    }

    public function exportPdf(Request $request)
    {
        abort_unless(
            class_exists(Pdf::class),
            500,
            'Package barryvdh/laravel-dompdf belum terpasang. Jalankan: composer require barryvdh/laravel-dompdf'
        );

        $period = $request->query('period', 'month');
        $startDate = $request->query('start_date');
        $endDate = $request->query('end_date');
        
        $reportData = $this->buildMonthlyReportData(paginateTransactions: false, period: $period, startDateReq: $startDate, endDateReq: $endDate);
        $reportData['activePeriod'] = $period;
        $reportData['startDate'] = $startDate;
        $reportData['endDate'] = $endDate;

        $pdf = Pdf::loadView('reports.pdf', $reportData)->setPaper('a4', 'portrait');

        return $pdf->download('laporan-keuangan-'.$period.'-'.now()->format('Y-m-d').'.pdf');
    }

    public function exportExcel(Request $request)
    {
        abort_unless(
            class_exists(Excel::class),
            500,
            'Package maatwebsite/excel belum terpasang. Jalankan: composer require maatwebsite/excel'
        );

        $period = $request->query('period', 'month');
        $startDate = $request->query('start_date');
        $endDate = $request->query('end_date');
        
        $reportData = $this->buildMonthlyReportData(paginateTransactions: false, period: $period, startDateReq: $startDate, endDateReq: $endDate);
        $reportData['activePeriod'] = $period;
        $reportData['startDate'] = $startDate;
        $reportData['endDate'] = $endDate;

        return Excel::download(
            new FinancialReportExport($reportData),
            'laporan-keuangan-'.$period.'-'.now()->format('Y-m-d').'.xlsx'
        );
    }

    private function buildMonthlyReportData(bool $paginateTransactions = false, string $period = 'month', ?string $startDateReq = null, ?string $endDateReq = null): array
    {
        if ($startDateReq && $endDateReq) {
            $startDate = Carbon::parse($startDateReq)->startOfDay();
            $endDate = Carbon::parse($endDateReq)->endOfDay();
        } else {
            // Calculate date range based on period
            switch ($period) {
                case 'week':
                    $startDate = Carbon::now()->startOfWeek();
                    $endDate = Carbon::now()->endOfWeek();
                    break;
                case 'quarter':
                    $startDate = Carbon::now()->subMonths(3)->startOfDay();
                    $endDate = Carbon::now()->endOfDay();
                    break;
                case 'year':
                    $startDate = Carbon::now()->startOfYear();
                    $endDate = Carbon::now()->endOfYear();
                    break;
                case 'month':
                default:
                    $startDate = Carbon::now()->startOfMonth();
                    $endDate = Carbon::now()->endOfMonth();
                    break;
            }
        }

        $financialSummary = $this->getMonthlyFinancialSummary($startDate, $endDate);

        // Fetch transactions with user relationship filtered by active period
        $transactionsQuery = Transaction::with('user')
            ->whereBetween('created_at', [$startDate, $endDate])
            ->orderBy('created_at', 'desc');

        $transactions = $paginateTransactions
            ? $transactionsQuery->paginate(15)->withQueryString()
            : $transactionsQuery->get();

        $cashierRevenues = Transaction::query()
            ->select('user_id', DB::raw('SUM(total_harga) as total_omset'))
            ->with('user:id,name')
            ->where('status', 'success')
            ->whereBetween('created_at', [$startDate, $endDate])
            ->groupBy('user_id')
            ->orderByDesc('total_omset')
            ->get();

        // 1. Product category performance (for Bar Chart)
        $categoriesSales = TransactionDetail::query()
            ->whereHas('transaction', function ($query) use ($startDate, $endDate) {
                $query->where('status', 'success')
                    ->whereBetween('created_at', [$startDate, $endDate]);
            })
            ->join('products', 'transaction_details.product_id', '=', 'products.id')
            ->selectRaw('products.category_id, SUM(transaction_details.subtotal) as total')
            ->groupBy('products.category_id')
            ->get();

        // Load categories mapping
        $categoriesMap = \App\Models\Category::pluck('name', 'id')->toArray();
        
        $categoryLabels = array_values($categoriesMap);
        if (empty($categoryLabels)) {
            $categoryLabels = ['Sembako', 'Makanan', 'Minuman', 'Cemilan', 'Rumah Tangga'];
        }

        $categoryData = [];
        foreach ($categoryLabels as $label) {
            $catId = array_search($label, $categoriesMap);
            $total = 0.0;
            if ($catId !== false) {
                $total = (float) $categoriesSales->where('category_id', $catId)->sum('total');
            } else {
                // Fallback check
                $total = 0.0;
            }
            $categoryData[] = $total;
        }

        $categoryPerformance = [
            'labels' => $categoryLabels,
            'data' => $categoryData,
        ];

        // 2. Top Selling Products
        $topProductsDetails = TransactionDetail::query()
            ->whereHas('transaction', function ($query) use ($startDate, $endDate) {
                $query->where('status', 'success')
                    ->whereBetween('created_at', [$startDate, $endDate]);
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

        // 3. Monthly Comparison Chart (This Year vs Last Year)
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

        $expenses = Expense::query()
            ->whereBetween('tanggal', [$startDate->toDateString(), $endDate->toDateString()])
            ->orderBy('tanggal', 'desc')
            ->get();

        // Override total_pengeluaran to bypass cache lag and keep it in sync
        $financialSummary['total_pengeluaran'] = (float) $expenses->sum('nominal');
        $financialSummary['net_revenue'] = $financialSummary['gross_profit'] - $financialSummary['total_pengeluaran'];

        return compact('financialSummary', 'categoryPerformance', 'topProducts', 'monthlyComparison', 'transactions', 'cashierRevenues', 'expenses');
    }

    private function getMonthlyFinancialSummary(Carbon $startDate, Carbon $endDate): array
    {
        $cacheKey = sprintf(
            'reports:sales-summary:%s:%s',
            $startDate->toDateString(),
            $endDate->toDateString()
        );

        return Cache::remember($cacheKey, now()->addHour(), function () use ($startDate, $endDate): array {
            $successfulTransactions = Transaction::query()
                ->where('status', 'success')
                ->whereBetween('created_at', [$startDate, $endDate]);

            $totalOmzet = (float) (clone $successfulTransactions)->sum('total_harga');
            $jumlahTransaksi = (int) (clone $successfulTransactions)->count();
            $averageTicket = $jumlahTransaksi > 0 ? $totalOmzet / $jumlahTransaksi : 0;
            $grossProfit = $this->calculateGrossProfit($startDate, $endDate);

            $totalPengeluaran = (float) Expense::query()
                ->whereBetween('tanggal', [$startDate->toDateString(), $endDate->toDateString()])
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

    private function calculateGrossProfit(Carbon $startDate, Carbon $endDate): float
    {
        if (! Schema::hasTable('transaction_details')) {
            return 0;
        }

        return (float) TransactionDetail::query()
            ->whereHas('transaction', function ($query) use ($startDate, $endDate) {
                $query->where('status', 'success')
                    ->whereBetween('created_at', [$startDate, $endDate]);
            })
            ->selectRaw('COALESCE(SUM((harga_jual - harga_beli) * qty), 0) as gross_profit')
            ->value('gross_profit');
    }
}
