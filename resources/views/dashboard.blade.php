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
        
        <div class="flex items-center gap-6">
            <!-- Omzet Bulan Ini Banner -->
            <div class="text-left md:text-right">
                <span class="text-[10px] font-bold text-indigo-500 uppercase tracking-widest block">Omzet Bulan Ini</span>
                <span class="text-lg font-black text-indigo-600">Rp {{ number_format($laporan_keuangan_bulanan, 0, ',', '.') }}</span>
            </div>
            
            <!-- Action Buttons / Filter (Visual dummy for scaling) -->
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
            </div>
        </div>
    </div>
    
    <!-- Statistic Cards Grid -->
    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
        
        <!-- Card 1: Total Sales Today -->
        <div class="group bg-white p-6 rounded-2xl border border-slate-200/80 shadow-sm hover:shadow-md hover:border-indigo-200 transition-all duration-300 relative overflow-hidden">
            <div class="absolute -right-10 -top-10 h-32 w-32 rounded-full bg-indigo-50 group-hover:bg-indigo-100/70 transition-colors duration-300 -z-0"></div>
            
            <div class="relative z-10 flex items-start justify-between">
                <div>
                    <span class="text-xs font-bold text-slate-400 uppercase tracking-wider">Total Penjualan Hari Ini</span>
                    <h3 class="text-2xl font-bold text-slate-800 mt-2">Rp {{ number_format($total_penjualan_hari_ini, 0, ',', '.') }}</h3>
                    <div class="flex items-center gap-1 mt-2">
                        <span class="flex items-center text-[10px] font-semibold text-emerald-600 bg-emerald-50 px-2 py-0.5 rounded-lg">
                            <i data-lucide="trending-up" class="w-3 h-3 mr-0.5"></i>
                            Real-time
                        </span>
                        <span class="text-[10px] text-slate-400 ml-1">dari DB transaksi</span>
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
                    <span class="text-xs font-bold text-slate-400 uppercase tracking-wider">Total Transaksi (Sistem)</span>
                    <h3 class="text-2xl font-bold text-slate-800 mt-2">{{ number_format($total_transaksi) }} Transaksi</h3>
                    <div class="flex items-center gap-1 mt-2">
                        <span class="flex items-center text-[10px] font-semibold text-violet-600 bg-violet-50 px-2 py-0.5 rounded-lg">
                            <i data-lucide="activity" class="w-3 h-3 mr-0.5"></i>
                            Keseluruhan
                        </span>
                        <span class="text-[10px] text-slate-400 ml-1">semua kasir</span>
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
                    <span class="text-xs font-bold text-slate-400 uppercase tracking-wider">Pemberitahuan Stok (<= 5)</span>
                    <h3 class="text-2xl font-bold text-slate-800 mt-2">{{ $stok_menipis->count() }} Barang</h3>
                    <div class="flex items-center gap-1.5 mt-2 text-[10px] text-slate-500 font-medium">
                        <span>Total unit stok produk: <strong class="text-slate-700 font-bold">{{ $total_stok }}</strong></span>
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
                    <p class="text-xs text-slate-400">Total omset pendapatan harian dari 7 hari terakhir.</p>
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

        <!-- Recent Transactions Table / Cashier Activity (takes 1 col) -->
        <div class="bg-white p-6 rounded-2xl border border-slate-200/80 shadow-sm flex flex-col">
            <div class="flex items-center justify-between border-b border-slate-100 pb-4 mb-4">
                <div>
                    <h3 class="font-bold text-slate-800 text-lg">Aktivitas Kasir</h3>
                    <p class="text-xs text-slate-400">Daftar transaksi kasir terbaru dari database.</p>
                </div>
                <i data-lucide="history" class="w-5 h-5 text-slate-400"></i>
            </div>

            <!-- Scrollable Transaction List -->
            <div class="flex-1 overflow-y-auto max-h-[300px] space-y-4 pr-1">
                @forelse($aktivitas_kasir as $trx)
                    @php
                        // Deterministic styling based on transaction ID
                        $method = $trx->id % 2 === 0 ? 'QRIS' : ($trx->id % 3 === 0 ? 'Transfer' : 'Tunai');
                        $bgClass = $method === 'QRIS' ? 'bg-purple-50 text-purple-600' : ($method === 'Tunai' ? 'bg-emerald-50 text-emerald-600' : 'bg-blue-50 text-blue-600');
                    @endphp
                    <div class="flex items-center justify-between p-3 rounded-xl border border-slate-100 hover:border-slate-200 hover:bg-slate-50/50 transition-all duration-200">
                        <div class="flex items-center gap-3">
                            <div class="h-9 w-9 rounded-xl flex items-center justify-center font-bold text-[10px] {{ $bgClass }}">
                                {{ substr($method, 0, 2) }}
                            </div>
                            <div>
                                <div class="flex items-center gap-1.5">
                                    <span class="text-xs font-bold text-slate-800">{{ $trx->invoice }}</span>
                                    <span class="text-[9px] text-slate-400">{{ $trx->created_at->format('H:i') }}</span>
                                </div>
                                <p class="text-[10px] text-slate-500 font-medium">
                                    Kasir: <span class="text-slate-700 font-semibold">{{ $trx->user ? $trx->user->name : 'Sistem' }}</span>
                                </p>
                            </div>
                        </div>
                        <div class="text-right">
                            <span class="text-xs font-bold text-slate-800">Rp {{ number_format($trx->total_harga, 0, ',', '.') }}</span>
                            <div class="mt-1">
                                <span class="text-[9px] font-bold px-2 py-0.5 rounded-full uppercase tracking-wider
                                     {{ $trx->status === 'success' ? 'bg-emerald-50 text-emerald-600' : 'bg-amber-50 text-amber-600' }}">
                                    {{ $trx->status }}
                                </span>
                            </div>
                        </div>
                    </div>
                @empty
                    <div class="text-center py-12 text-slate-450">
                        <i data-lucide="shopping-cart" class="w-8 h-8 mx-auto mb-2 text-slate-300"></i>
                        <span class="text-xs">Belum ada transaksi saat ini.</span>
                    </div>
                @endforelse
            </div>
        </div>

    </div>

    <!-- Alert Stok Menipis Details (Conditional Banner) -->
    @if($stok_menipis->isNotEmpty())
        <div class="bg-rose-50 border border-rose-100 rounded-2xl p-4 flex flex-col md:flex-row items-start md:items-center justify-between gap-4">
            <div class="flex items-start gap-3">
                <div class="p-2 bg-rose-100 text-rose-700 rounded-xl mt-0.5">
                    <i data-lucide="package-x" class="w-5 h-5"></i>
                </div>
                <div>
                    <h4 class="text-sm font-bold text-rose-800">Peringatan Stok Menipis!</h4>
                    <p class="text-xs text-rose-600">Beberapa barang berikut membutuhkan restock segera karena stoknya di bawah batas minimal:</p>
                    <div class="flex flex-wrap gap-2 mt-2">
                        @foreach($stok_menipis as $item)
                            <span class="inline-flex items-center text-[10px] font-semibold bg-white border border-rose-200 text-rose-700 px-2 py-0.5 rounded-lg">
                                {{ $item->name }} (Sisa: {{ $item->stock }})
                            </span>
                        @endforeach
                    </div>
                </div>
            </div>
            <a href="{{ route('inventory') }}" class="px-4 py-2 bg-rose-650 hover:bg-rose-700 text-white text-xs font-bold rounded-xl whitespace-nowrap transition-colors shadow-sm shadow-rose-900/10">
                Kelola Inventaris
            </a>
        </div>
    @endif

</div>

<!-- Chart.js Library -->
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        const ctx = document.getElementById('weeklySalesChart').getContext('2d');
        
        // Setup gradient background untuk chart
        const gradient = ctx.createLinearGradient(0, 0, 0, 300);
        gradient.addColorStop(0, 'rgba(79, 70, 229, 0.3)');  // Indigo dengan transparansi
        gradient.addColorStop(1, 'rgba(79, 70, 229, 0.00)'); // Pudar ke transparan

        // Injeksi data tren mingguan dari backend menggunakan directive json
        const weeklySalesData = @json($tren_penjualan_mingguan);

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
                    tension: 0.4, // Kurva mulus
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
                        display: false
                    },
                    tooltip: {
                        backgroundColor: '#0f172a',
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
                            color: '#94a3b8'
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
                                return 'Rp ' + value.toLocaleString('id-ID');
                            }
                        }
                    }
                }
            }
        });
    });
</script>
@endsection
