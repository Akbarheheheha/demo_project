@extends('layouts.app')

@section('title', 'Laporan Analisis Bisnis')
@section('active_page', 'reports')

@section('content')
<div class="space-y-6" x-data="{ dateFilter: 'this_month' }">

    <!-- Welcome Header -->
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
        <div>
            <h2 class="text-2xl font-bold text-slate-800">Laporan & Analisis Penjualan</h2>
            <p class="text-sm text-slate-500">Tinjau pendapatan bersih, laba kotor, dan komparasi performa dari periode sebelumnya.</p>
        </div>
        
        <!-- Action Buttons (Export) -->
        <div class="flex items-center gap-2">
            <!-- Export PDF -->
            <a href="{{ route('reports.export.pdf') }}"
               class="bg-white hover:bg-slate-50 border border-slate-200 text-slate-700 font-semibold px-3.5 py-2 rounded-xl text-xs flex items-center gap-1.5 transition-all shadow-sm">
                <i data-lucide="file-text" class="w-4 h-4 text-rose-500"></i>
                <span>Export PDF</span>
            </a>
            
            <!-- Export Excel -->
            <a href="{{ route('reports.export.excel') }}"
               class="bg-white hover:bg-slate-50 border border-slate-200 text-slate-700 font-semibold px-3.5 py-2 rounded-xl text-xs flex items-center gap-1.5 transition-all shadow-sm">
                <i data-lucide="file-spreadsheet" class="w-4 h-4 text-emerald-500"></i>
                <span>Export Excel</span>
            </a>
        </div>
    </div>

    <!-- Quick Date Filter Panel -->
    <div class="bg-white p-4 rounded-2xl border border-slate-200/80 shadow-sm flex flex-wrap gap-2 items-center">
        <span class="text-xs font-bold text-slate-400 uppercase tracking-wider mr-2">Periode Laporan:</span>
        <button @click="dateFilter = 'this_week'; $dispatch('show-toast', { message: 'Filter Minggu Ini diterapkan', type: 'info' })"
                :class="dateFilter === 'this_week' ? 'bg-indigo-50 border-indigo-200 text-indigo-700 font-bold' : 'bg-slate-50 hover:bg-slate-100 border-slate-200 text-slate-600'"
                class="px-3.5 py-1.5 text-xs font-medium rounded-xl border transition-all">
            Minggu Ini
        </button>
        <button @click="dateFilter = 'this_month'; $dispatch('show-toast', { message: 'Filter Bulan Ini diterapkan', type: 'info' })"
                :class="dateFilter === 'this_month' ? 'bg-indigo-50 border-indigo-200 text-indigo-700 font-bold' : 'bg-slate-50 hover:bg-slate-100 border-slate-200 text-slate-600'"
                class="px-3.5 py-1.5 text-xs font-medium rounded-xl border transition-all">
            Bulan Ini
        </button>
        <button @click="dateFilter = 'last_quarter'; $dispatch('show-toast', { message: 'Filter Kuartal Terakhir diterapkan', type: 'info' })"
                :class="dateFilter === 'last_quarter' ? 'bg-indigo-50 border-indigo-200 text-indigo-700 font-bold' : 'bg-slate-50 hover:bg-slate-100 border-slate-200 text-slate-600'"
                class="px-3.5 py-1.5 text-xs font-medium rounded-xl border transition-all">
            Kuartal Terakhir
        </button>
        <button @click="dateFilter = 'this_year'; $dispatch('show-toast', { message: 'Filter Tahun Ini diterapkan', type: 'info' })"
                :class="dateFilter === 'this_year' ? 'bg-indigo-50 border-indigo-200 text-indigo-700 font-bold' : 'bg-slate-50 hover:bg-slate-100 border-slate-200 text-slate-600'"
                class="px-3.5 py-1.5 text-xs font-medium rounded-xl border transition-all">
            Tahun Ini
        </button>
    </div>

    <!-- Financial Statistics Cards -->
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6">
        <!-- Card 1: Total Omzet -->
        <div class="bg-white p-5 rounded-2xl border border-slate-200/80 shadow-sm relative overflow-hidden">
            <div class="flex items-start justify-between">
                <div>
                    <span class="text-xs font-bold text-slate-400 uppercase tracking-wider">Total Omzet</span>
                    <h3 class="text-xl font-bold text-slate-800 mt-2">Rp {{ number_format($financialSummary['total_omzet'], 0, ',', '.') }}</h3>
                    <p class="text-[10px] text-slate-400 mt-2">{{ number_format($financialSummary['jumlah_transaksi'], 0, ',', '.') }} transaksi sukses bulan ini</p>
                </div>
                <div class="p-2.5 bg-sky-50 text-sky-600 rounded-xl">
                    <i data-lucide="banknote" class="w-5.5 h-5.5"></i>
                </div>
            </div>
        </div>
        
        <!-- Card 2: Pendapatan Bersih -->
        <div class="bg-white p-5 rounded-2xl border border-slate-200/80 shadow-sm relative overflow-hidden">
            <div class="flex items-start justify-between">
                <div>
                    <span class="text-xs font-bold text-slate-400 uppercase tracking-wider">Pendapatan Bersih</span>
                    <h3 class="text-xl font-bold text-slate-800 mt-2">Rp {{ number_format($financialSummary['net_revenue'], 0, ',', '.') }}</h3>
                    <div class="flex items-center gap-1 mt-2">
                        <span class="text-[10px] font-bold text-emerald-600 bg-emerald-50 px-2 py-0.5 rounded-lg flex items-center">
                            <i data-lucide="trending-up" class="w-2.5 h-2.5 mr-0.5"></i>
                            +{{ $financialSummary['revenue_growth'] }}%
                        </span>
                        <span class="text-[9px] text-slate-400">vs bulan lalu</span>
                    </div>
                </div>
                <div class="p-2.5 bg-indigo-50 text-indigo-600 rounded-xl">
                    <i data-lucide="line-chart" class="w-5.5 h-5.5"></i>
                </div>
            </div>
        </div>

        <!-- Card 3: Laba Kotor -->
       
        <!-- Card 4: Total Pengeluaran -->
        <div class="bg-white p-5 rounded-2xl border border-slate-200/80 shadow-sm relative overflow-hidden">
            <div class="flex items-start justify-between">
                <div>
                    <span class="text-xs font-bold text-slate-400 uppercase tracking-wider">Total Pengeluaran</span>
                    <h3 class="text-xl font-bold text-slate-800 mt-2">Rp {{ number_format($financialSummary['total_pengeluaran'], 0, ',', '.') }}</h3>
                    <p class="text-[10px] text-slate-400 mt-2">Biaya operasional bulan berjalan</p>
                </div>
                <div class="p-2.5 bg-rose-50 text-rose-600 rounded-xl">
                    <i data-lucide="shopping-bag" class="w-5.5 h-5.5"></i>
                </div>
            </div>
        </div>

        <!-- Card 5: Rata-rata Transaksi -->
      
        
    </div>



    <!-- Table: Barang Terlaris (Top Products) -->
    <div class="bg-white rounded-2xl border border-slate-200/80 shadow-sm overflow-hidden">
        <div class="px-6 py-5 border-b border-slate-100 flex items-center justify-between">
            <div>
                <h3 class="font-bold text-slate-800 text-base">Top 5 Produk Terlaris</h3>
                <p class="text-xs text-slate-400">Daftar produk dengan kuantitas penjualan tertinggi periode ini.</p>
            </div>
            <span class="text-[10px] font-bold bg-indigo-50 text-indigo-700 px-3 py-1 rounded-xl uppercase tracking-wider">Produk Populer</span>
        </div>
        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse">
                <thead>
                    <tr class="bg-slate-50/50 border-b border-slate-100 text-[11px] font-bold text-slate-400 uppercase tracking-wider">
                        <th class="px-6 py-4">SKU</th>
                        <th class="px-6 py-4">Nama Produk</th>
                        <th class="px-6 py-4">Kategori</th>
                        <th class="px-6 py-4 text-center">Jumlah Terjual</th>
                        <th class="px-6 py-4 text-right">Total Omset</th>
                        <th class="px-6 py-4 text-center">Margin Keuntungan</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100 text-xs">
                    @foreach($topProducts as $prod)
                        <tr class="hover:bg-slate-50/50 transition-colors">
                            <td class="px-6 py-3.5 font-mono font-bold text-slate-800">{{ $prod['sku'] }}</td>
                            <td class="px-6 py-3.5 font-semibold text-slate-800">{{ $prod['name'] }}</td>
                            <td class="px-6 py-3.5">
                                <span class="px-2.5 py-1 bg-slate-100 rounded-lg text-slate-600 font-semibold">{{ $prod['category'] }}</span>
                            </td>
                            <td class="px-6 py-3.5 text-center font-bold text-slate-700">{{ number_format($prod['sold_qty'], 0, ',', '.') }} pcs</td>
                            <td class="px-6 py-3.5 text-right font-black text-indigo-600">Rp {{ number_format($prod['total_revenue'], 0, ',', '.') }}</td>
                            <td class="px-6 py-3.5 text-center font-extrabold text-emerald-600">
                                <span class="flex items-center justify-center gap-1.5">
                                    <i data-lucide="percent" class="w-3 h-3"></i>
                                    {{ $prod['margin'] }}
                                </span>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>

    <!-- Table: Semua Transaksi Penjualan (Real Database) -->
    <div class="bg-white rounded-2xl border border-slate-200/80 shadow-sm overflow-hidden mt-6">
        <div class="px-6 py-5 border-b border-slate-100 flex items-center justify-between">
            <div>
                <h3 class="font-bold text-slate-800 text-base">Riwayat Lengkap Transaksi Penjualan</h3>
                <p class="text-xs text-slate-400">Daftar transaksi penjualan terdaftar dalam database.</p>
            </div>
            <span class="text-[10px] font-bold bg-indigo-50 text-indigo-700 px-3 py-1 rounded-xl uppercase tracking-wider">Database Real</span>
        </div>
        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse">
                <thead>
                    <tr class="bg-slate-50/50 border-b border-slate-100 text-[11px] font-bold text-slate-400 uppercase tracking-wider">
                        <th class="px-6 py-4">Nomor Invoice</th>
                        <th class="px-6 py-4">Operator / Kasir</th>
                        <th class="px-6 py-4">Tanggal Transaksi</th>
                        <th class="px-6 py-4 text-right">Total Nominal</th>
                        <th class="px-6 py-4 text-center">Status</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100 text-xs">
                    @forelse($transactions as $trx)
                        <tr class="hover:bg-slate-50/50 transition-colors">
                            <td class="px-6 py-3.5 font-mono font-bold text-slate-800">{{ $trx->invoice }}</td>
                            <td class="px-6 py-3.5 font-semibold text-slate-800">{{ $trx->user ? $trx->user->name : 'Sistem' }}</td>
                            <td class="px-6 py-3.5 text-slate-500 font-medium">{{ $trx->created_at->format('d M Y H:i') }}</td>
                            <td class="px-6 py-3.5 text-right font-black text-indigo-600">Rp {{ number_format($trx->total_harga, 0, ',', '.') }}</td>
                            <td class="px-6 py-3.5 text-center">
                                <span class="px-2.5 py-0.5 rounded-full text-[10px] font-bold uppercase tracking-wider {{ $trx->status === 'success' ? 'bg-emerald-50 text-emerald-600' : 'bg-amber-50 text-amber-600' }}">
                                    {{ $trx->status }}
                                </span>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="text-center py-12 text-slate-400 font-semibold">
                                Belum ada data transaksi penjualan di database.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

</div>

@push('scripts')
<!-- Chart.js Library -->
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        // --- 1. Monthly Comparison Chart (Line / Area) ---
        const comparisonCanvas = document.getElementById('monthlyComparisonChart');
        if (comparisonCanvas) {
            const comparisonCtx = comparisonCanvas.getContext('2d');
            const compData = @json($monthlyComparison);
            
            // Gradients
            const gradThisYear = comparisonCtx.createLinearGradient(0, 0, 0, 240);
            gradThisYear.addColorStop(0, 'rgba(79, 70, 229, 0.25)');
            gradThisYear.addColorStop(1, 'rgba(79, 70, 229, 0.0)');
            
            new Chart(comparisonCtx, {
                type: 'line',
                data: {
                    labels: compData.labels,
                    datasets: [
                        {
                            label: 'Tahun Ini (2026)',
                            data: compData.this_year,
                            borderColor: '#4f46e5',
                            borderWidth: 3,
                            backgroundColor: gradThisYear,
                            fill: true,
                            tension: 0.4,
                            pointBackgroundColor: '#ffffff',
                            pointBorderColor: '#4f46e5',
                            pointBorderWidth: 2,
                            pointRadius: 4
                        },
                        {
                            label: 'Tahun Lalu (2025)',
                            data: compData.last_year,
                            borderColor: '#94a3b8',
                            borderWidth: 2,
                            borderDash: [5, 5],
                            fill: false,
                            tension: 0.4,
                            pointBackgroundColor: '#ffffff',
                            pointBorderColor: '#94a3b8',
                            pointBorderWidth: 1.5,
                            pointRadius: 3
                        }
                    ]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        legend: {
                            position: 'top',
                            labels: {
                                font: { family: 'Outfit', size: 11 },
                                boxWidth: 20
                            }
                        },
                        tooltip: {
                            padding: 10,
                            cornerRadius: 10,
                            bodyFont: { family: 'Outfit' }
                        }
                    },
                    scales: {
                        x: { grid: { display: false }, ticks: { font: { family: 'Outfit', size: 10 } } },
                        y: { 
                            grid: { color: '#f1f5f9' },
                            ticks: {
                                font: { family: 'Outfit', size: 9 },
                                callback: function(val) { return 'Rp ' + val / 1000000 + 'jt'; }
                            }
                        }
                    }
                }
            });
        }

        // --- 2. Category Performance Chart (Bar Chart) ---
        const categoryCanvas = document.getElementById('categoryPerformanceChart');
        if (categoryCanvas) {
            const categoryCtx = categoryCanvas.getContext('2d');
            const catData = @json($categoryPerformance);
            
            new Chart(categoryCtx, {
                type: 'bar',
                data: {
                    labels: catData.labels,
                    datasets: [{
                        label: 'Omset Penjualan',
                        data: catData.data,
                        backgroundColor: [
                            '#10b981', // Sembako (Emerald)
                            '#f59e0b', // Makanan (Amber)
                            '#0ea5e9', // Minuman (Sky)
                            '#8b5cf6', // Cemilan (Purple)
                            '#ec4899'  // Rumah Tangga (Pink)
                        ],
                        borderRadius: 8,
                        maxBarThickness: 32
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        legend: { display: false },
                        tooltip: {
                            padding: 10,
                            cornerRadius: 10,
                            bodyFont: { family: 'Outfit' }
                        }
                    },
                    scales: {
                        x: { grid: { display: false }, ticks: { font: { family: 'Outfit', size: 10 } } },
                        y: { 
                            grid: { color: '#f1f5f9' },
                            ticks: {
                                font: { family: 'Outfit', size: 9 },
                                callback: function(val) { return 'Rp ' + val / 1000000 + 'jt'; }
                            }
                        }
                    }
                }
            });
        }
    });
</script>
@endpush
@endsection
