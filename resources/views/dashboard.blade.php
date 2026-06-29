@extends('layouts.app')

@section('title', 'Dashboard Admin')
@section('active_page', 'dashboard')

@section('content')
<div class="space-y-6" x-data="{ timeRange: '7d' }">
    
    <!-- Welcome Header -->
    <div class="flex flex-col md:flex-row md:items-center justify-between gap-4">
        <div>
            <h2 class="text-2xl font-bold text-slate-800">Ringkasan Bisnis</h2>
            <p class="text-sm text-slate-500">Pantau kinerja keuangan, transaksi, dan stok tokomu secara real-time.</p>
        </div>
        
        <!-- Action Buttons / Filter -->
        <div class="flex items-center gap-2">
            <button @click="timeRange = 'today'; $dispatch('show-toast', { message: 'Filter hari ini diterapkan (Simulasi)', type: 'info' })"
                    :class="timeRange === 'today' ? 'bg-indigo-600 text-white shadow-sm shadow-indigo-600/20' : 'bg-white hover:bg-slate-50 text-slate-700'"
                    class="px-3.5 py-2 text-xs font-semibold rounded-xl border border-slate-200 transition-all duration-200">
                Hari Ini
            </button>
            <button @click="timeRange = '7d'; $dispatch('show-toast', { message: 'Filter 7 hari terakhir diterapkan (Simulasi)', type: 'info' })"
                    :class="timeRange === '7d' ? 'bg-indigo-600 text-white shadow-sm shadow-indigo-600/20' : 'bg-white hover:bg-slate-50 text-slate-700'"
                    class="px-3.5 py-2 text-xs font-semibold rounded-xl border border-slate-200 transition-all duration-200">
                7 Hari Terakhir
            </button>
            <button @click="timeRange = '30d'; $dispatch('show-toast', { message: 'Filter 30 hari terakhir diterapkan (Simulasi)', type: 'info' })"
                    :class="timeRange === '30d' ? 'bg-indigo-600 text-white shadow-sm shadow-indigo-600/20' : 'bg-white hover:bg-slate-50 text-slate-700'"
                    class="px-3.5 py-2 text-xs font-semibold rounded-xl border border-slate-200 transition-all duration-200">
                30 Hari Terakhir
            </button>
        </div>
    </div>
    
    <!-- Statistic Cards Grid -->
    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
        
        <!-- Card 1: Total Sales -->
        <div class="group bg-white p-6 rounded-2xl border border-slate-200/80 shadow-sm hover:shadow-md hover:border-indigo-200 transition-all duration-300 relative overflow-hidden">
            <!-- Background Glow Animation -->
            <div class="absolute -right-10 -top-10 h-32 w-32 rounded-full bg-indigo-50 group-hover:bg-indigo-100/70 transition-colors duration-300 -z-0"></div>
            
            <div class="relative z-10 flex items-start justify-between">
                <div>
                    <span class="text-xs font-bold text-slate-400 uppercase tracking-wider">Total Penjualan Hari Ini</span>
                    <h3 class="text-2xl font-bold text-slate-800 mt-2">Rp {{ number_format($stats['total_sales_today'], 0, ',', '.') }}</h3>
                    <div class="flex items-center gap-1 mt-2">
                        <span class="flex items-center text-[11px] font-semibold text-emerald-600 bg-emerald-50 px-2 py-0.5 rounded-lg">
                            <i data-lucide="trending-up" class="w-3 h-3 mr-0.5"></i>
                            +{{ $stats['sales_growth'] }}%
                        </span>
                        <span class="text-[10px] text-slate-400 ml-1">vs. kemarin</span>
                    </div>
                </div>
                <div class="p-3 bg-indigo-50 text-indigo-600 rounded-xl">
                    <i data-lucide="banknote" class="w-6 h-6"></i>
                </div>
            </div>
        </div>

        <!-- Card 2: Total Transactions -->
        <div class="group bg-white p-6 rounded-2xl border border-slate-200/80 shadow-sm hover:shadow-md hover:border-violet-200 transition-all duration-300 relative overflow-hidden">
            <div class="absolute -right-10 -top-10 h-32 w-32 rounded-full bg-violet-50 group-hover:bg-violet-100/70 transition-colors duration-300 -z-0"></div>
            
            <div class="relative z-10 flex items-start justify-between">
                <div>
                    <span class="text-xs font-bold text-slate-400 uppercase tracking-wider">Total Transaksi</span>
                    <h3 class="text-2xl font-bold text-slate-800 mt-2">{{ $stats['total_transactions_today'] }} Transaksi</h3>
                    <div class="flex items-center gap-1 mt-2">
                        <span class="flex items-center text-[11px] font-semibold text-emerald-600 bg-emerald-50 px-2 py-0.5 rounded-lg">
                            <i data-lucide="trending-up" class="w-3 h-3 mr-0.5"></i>
                            +{{ $stats['transactions_growth'] }}%
                        </span>
                        <span class="text-[10px] text-slate-400 ml-1">vs. kemarin</span>
                    </div>
                </div>
                <div class="p-3 bg-violet-50 text-violet-600 rounded-xl">
                    <i data-lucide="shopping-bag" class="w-6 h-6"></i>
                </div>
            </div>
        </div>

        <!-- Card 3: Low Stock Warning -->
        <div class="group bg-white p-6 rounded-2xl border border-slate-200/80 shadow-sm hover:shadow-md hover:border-rose-200 transition-all duration-300 relative overflow-hidden">
            <div class="absolute -right-10 -top-10 h-32 w-32 rounded-full bg-rose-50 group-hover:bg-rose-100/70 transition-colors duration-300 -z-0"></div>
            
            <div class="relative z-10 flex items-start justify-between">
                <div>
                    <span class="text-xs font-bold text-slate-400 uppercase tracking-wider">Pemberitahuan Stok</span>
                    <h3 class="text-2xl font-bold text-slate-800 mt-2">{{ $stats['low_stock_count'] }} Barang</h3>
                    <div class="flex items-center gap-1 mt-2">
                        <a href="{{ route('inventory') }}" class="flex items-center text-[11px] font-semibold text-rose-600 bg-rose-50 px-2 py-0.5 rounded-lg hover:bg-rose-100 transition-colors">
                            Perlu Restock
                            <i data-lucide="arrow-right" class="w-3 h-3 ml-1"></i>
                        </a>
                    </div>
                </div>
                <div class="p-3 bg-rose-50 text-rose-600 rounded-xl">
                    <i data-lucide="alert-triangle" class="w-6 h-6"></i>
                </div>
            </div>
        </div>
        
    </div>

    <!-- Chart & Recent Activity Grid -->
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        
        <!-- Weekly Sales Chart (takes 2 cols) -->
        <div class="lg:col-span-2 bg-white p-6 rounded-2xl border border-slate-200/80 shadow-sm">
            <div class="flex items-center justify-between border-b border-slate-100 pb-4 mb-4">
                <div>
                    <h3 class="font-bold text-slate-800 text-lg">Tren Penjualan Mingguan</h3>
                    <p class="text-xs text-slate-400">Total omset pendapatan harian dari Senin sampai Minggu.</p>
                </div>
                <div class="flex items-center gap-1 bg-slate-50 border border-slate-100 p-1 rounded-lg">
                    <span class="h-2 w-2 rounded-full bg-indigo-500 ml-2"></span>
                    <span class="text-[10px] font-semibold text-slate-600 px-2 py-0.5">Omset (Rp)</span>
                </div>
            </div>
            
            <div class="h-72 w-full relative">
                <!-- Canvas for Chart.js -->
                <canvas id="weeklySalesChart"></canvas>
            </div>
        </div>

        <!-- Recent Transactions Table (takes 1 col) -->
        <div class="bg-white p-6 rounded-2xl border border-slate-200/80 shadow-sm flex flex-col">
            <div class="flex items-center justify-between border-b border-slate-100 pb-4 mb-4">
                <div>
                    <h3 class="font-bold text-slate-800 text-lg">Aktivitas Kasir</h3>
                    <p class="text-xs text-slate-400">Daftar transaksi kasir terbaru hari ini.</p>
                </div>
                <a href="{{ route('pos') }}" class="text-[10px] font-semibold text-indigo-600 hover:text-indigo-800 flex items-center gap-0.5">
                    Kasir Baru
                    <i data-lucide="plus" class="w-3.5 h-3.5"></i>
                </a>
            </div>

            <!-- Scrollable Transaction List -->
            <div class="flex-1 overflow-y-auto max-h-72 space-y-4 pr-1">
                @foreach($recentTransactions as $trx)
                    <div class="flex items-center justify-between p-3 rounded-xl border border-slate-100 hover:border-slate-200 hover:bg-slate-50/50 transition-all duration-200">
                        <div class="flex items-center gap-3">
                            <!-- Icon Method -->
                            <div class="h-9 w-9 rounded-xl flex items-center justify-center font-bold text-xs
                                 {{ $trx['payment_method'] === 'QRIS' ? 'bg-purple-50 text-purple-600' : ($trx['payment_method'] === 'Tunai' ? 'bg-emerald-50 text-emerald-600' : 'bg-blue-50 text-blue-600') }}">
                                {{ substr($trx['payment_method'], 0, 2) }}
                            </div>
                            <div>
                                <div class="flex items-center gap-1.5">
                                    <span class="text-xs font-bold text-slate-800">{{ $trx['invoice'] }}</span>
                                    <span class="text-[9px] text-slate-400">{{ $trx['time'] }}</span>
                                </div>
                                <p class="text-[10px] text-slate-500 font-medium">Pelanggan: <span class="text-slate-700 font-semibold">{{ $trx['customer'] }}</span> ({{ $trx['items_count'] }} item)</p>
                            </div>
                        </div>
                        <div class="text-right">
                            <span class="text-xs font-bold text-slate-800">Rp {{ number_format($trx['total'], 0, ',', '.') }}</span>
                            <div class="mt-1">
                                <span class="text-[9px] font-bold px-2 py-0.5 rounded-full uppercase tracking-wider
                                     {{ $trx['status'] === 'success' ? 'bg-emerald-50 text-emerald-600' : 'bg-amber-50 text-amber-600' }}">
                                    {{ $trx['status'] }}
                                </span>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
            
        </div>

    </div>

</div>

<!-- Chart.js Library -->
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        const ctx = document.getElementById('weeklySalesChart').getContext('2d');
        
        // Setup gradient background for the chart
        const gradient = ctx.createLinearGradient(0, 0, 0, 300);
        gradient.addColorStop(0, 'rgba(79, 70, 229, 0.3)');  // Indigo with transparency
        gradient.addColorStop(1, 'rgba(79, 70, 229, 0.00)'); // Fade to transparent

        const weeklySalesData = @json($weeklySales);

        new Chart(ctx, {
            type: 'line',
            data: {
                labels: weeklySalesData.labels,
                datasets: [{
                    label: 'Omset Penjualan',
                    data: weeklySalesData.data,
                    borderColor: '#4f46e5', // Indigo-600
                    borderWidth: 3.5,
                    backgroundColor: gradient,
                    fill: true,
                    tension: 0.4, // Smooth curve
                    pointBackgroundColor: '#ffffff',
                    pointBorderColor: '#4f46e5',
                    pointBorderWidth: 2.5,
                    pointRadius: 5,
                    pointHoverRadius: 7,
                    pointHoverBackgroundColor: '#4f46e5',
                    pointHoverBorderColor: '#ffffff',
                    pointHoverBorderWidth: 2
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        display: false // We use our custom legend layout
                    },
                    tooltip: {
                        backgroundColor: '#0f172a', // Slate-900
                        titleFont: {
                            family: 'Outfit',
                            size: 12,
                            weight: 'bold'
                        },
                        bodyFont: {
                            family: 'Outfit',
                            size: 12
                        },
                        padding: 12,
                        cornerRadius: 12,
                        displayColors: false,
                        callbacks: {
                            label: function(context) {
                                let label = context.dataset.label || '';
                                if (label) {
                                    label += ': ';
                                }
                                if (context.parsed.y !== null) {
                                    label += new Intl.NumberFormat('id-ID', { style: 'currency', currency: 'IDR', maximumFractionDigits: 0 }).format(context.parsed.y);
                                }
                                return label;
                            }
                        }
                    }
                },
                scales: {
                    x: {
                        grid: {
                            display: false
                        },
                        ticks: {
                            font: {
                                family: 'Outfit',
                                size: 11
                            },
                            color: '#94a3b8' // Slate-400
                        }
                    },
                    y: {
                        grid: {
                            color: '#f1f5f9',
                            drawTicks: false
                        },
                        border: {
                            dash: [5, 5]
                        },
                        ticks: {
                            font: {
                                family: 'Outfit',
                                size: 10
                            },
                            color: '#94a3b8',
                            callback: function(value) {
                                return 'Rp ' + value / 1000000 + 'jt';
                            }
                        }
                    }
                }
            }
        });
    });
</script>
@endsection
