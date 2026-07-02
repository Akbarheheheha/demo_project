@extends('layouts.app')

@section('title', 'Dashboard Admin')
@section('active_page', 'dashboard')

@section('content')
<style>
    .btn-kelola{
        background: #4f46e5;
    }
    .btn-kelola:hover{
        color: #4f46e5;
        transition: 0.2s ease;
        background: rgba(79, 70, 229, 0.15);
        backdrop-filter:blur(4px);
        box-shadow: 0 0 10px rgba(79, 70, 229, 0.25);
    }
    .container_scale{
        transition: transform 0.2s ease;
    }
    .container_scale:hover{
        transform: scale(1.02);
    }
    .container_stok{
        border: 1px solid rgba(79, 70, 229, 0.2);
    }
    .container_stok:hover{
        background: #4f46e5;
        border: 1px solid #4f46e5;
    }
    .container_stok:hover h4,
    .container_stok:hover span {
        color: #ffffff !important;
    }
    .btn_kelola2{
        background: #4f46e5;
    }
    .btn_kelola2:hover{
        color: #4f46e5;
        transition: 0.2s ease;
        background: rgba(79, 70, 229, 0.15);
        backdrop-filter:blur(4px);
        box-shadow: 0 0 10px rgba(79, 70, 229, 0.25);
    }
</style>
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
    
    <!-- AI Business Insight Banner -->
    <div class="container_scale bg-gradient-to-r from-indigo-900 via-indigo-950 to-slate-900 text-white p-6 rounded-2xl border border-indigo-500/25 shadow-xl relative overflow-hidden"
         x-data="{ loading: false }"
         x-init="loading = false">
        <!-- Background decorative glows -->
        <div class="absolute -right-20 -bottom-20 h-64 w-64 rounded-full bg-indigo-500/10 blur-3xl pointer-events-none"></div>
        <div class="absolute -left-20 -top-20 h-64 w-64 rounded-full bg-violet-500/10 blur-3xl pointer-events-none"></div>
        
        <div class="relative z-10 flex flex-col md:flex-row md:items-center gap-5">
            <!-- Icon -->
            <div class="p-3.5 bg-white/10 backdrop-blur-md text-indigo-300 rounded-2xl border border-white/15 shadow-inner self-start md:self-center flex-shrink-0">
                <i data-lucide="sparkles" class="w-7 h-7"></i>
            </div>
            
            <!-- Content -->
            <div class="flex-1 space-y-1">
                <div class="flex items-center gap-2">
                    <span class="px-2 py-0.5 text-[9px] font-mono font-semibold bg-indigo-500/25 border border-indigo-400/30 text-indigo-200 rounded-md tracking-wider uppercase">AI Advisor</span>
                    <h3 class="font-bold text-sm text-slate-100 tracking-wide">✨ AI Business Insight</h3>
                </div>
                
                <!-- Loading State -->
                <div x-show="loading" class="flex items-center gap-2 mt-3">
                    <svg class="animate-spin w-4 h-4 text-indigo-300" fill="none" viewBox="0 0 24 24">
                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                        <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                    </svg>
                    <span class="text-xs text-slate-450">Sedang menganalisis data bisnis...</span>
                </div>
                
                <!-- Insight Content -->
                <div x-show="!loading" class="text-xs text-slate-300 leading-relaxed font-sans mt-2 pr-4">
                    {!! nl2br(e($ai_insight)) !!}
                </div>
            </div>
        </div>
    </div>
    
    <!-- Statistic Cards Grid -->
    <!-- Proteksi Livewire SPA DOM Morphing -->
    <div class="grid grid-cols-1 md:grid-cols-3 gap-6" wire:key="dashboard-stats">
        
        <!-- Card 1: Total Sales Today -->
        <div class="container_scale group bg-white p-6 rounded-2xl border border-slate-200/80 shadow-sm hover:shadow-md hover:border-indigo-200 transition-all duration-300 relative overflow-hidden">
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
        <div class="container_scale group bg-white p-6 rounded-2xl border border-slate-200/80 shadow-sm hover:shadow-md hover:border-violet-200 transition-all duration-300 relative overflow-hidden">
            <div class="absolute -right-10 -top-10 h-32 w-32 rounded-full bg-violet-50 group-hover:bg-violet-100/70 transition-colors duration-300 -z-0"></div>
            
            <div class="relative z-10 flex items-start justify-between">
                <div>
                    <span class="text-xs font-bold text-slate-400 uppercase tracking-wider">Total Transaksi</span>
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
        <a href="{{ route('inventory') }}" class="container_scale block group bg-white p-6 rounded-2xl border border-slate-200/80 shadow-sm hover:shadow-md hover:border-rose-200 transition-all duration-300 relative overflow-hidden">
            <div class="absolute -right-10 -top-10 h-32 w-32 rounded-full bg-rose-50 group-hover:bg-rose-100/70 transition-colors duration-300 -z-0"></div>
            
            <div class="relative z-10 flex items-start justify-between">
                <div>
                    <span class="text-xs font-bold text-slate-400 uppercase tracking-wider">Pemberitahuan Stok menipis</span>
                    <h3 class="text-2xl font-bold text-slate-800 mt-2">{{ $stok_menipis->count() }} Barang</h3>
                    <div class="flex items-center gap-1.5 mt-2 text-[10px] text-slate-500 font-medium">
                        <span>Total unit stok produk: <strong class="text-slate-700 font-bold">{{ $total_stok }}</strong></span>
                    </div>
                </div>
                <div class="p-3 bg-rose-50 text-rose-600 rounded-xl">
                    <i data-lucide="alert-triangle" class="w-6 h-6"></i>
                </div>
            </div>
        </a>
        
    </div>

    <!-- Chart & Recent Activity Grid -->
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        
        <!-- Weekly Sales Chart (takes 2 cols) -->
        <!-- Proteksi Livewire SPA DOM Morphing -->
        <div class="container_scale lg:col-span-2 bg-white p-6 rounded-2xl border border-slate-200/80 shadow-sm" wire:key="dashboard-charts">
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

        <!-- Widget Area: Low Stock or Cashier Log (takes 1 col) -->
        <div class="container_scale bg-white p-6 rounded-2xl border border-slate-200/80 shadow-sm flex flex-col" 
             x-data="{
                showLog: false,
                stokMenipis: {{ json_encode($stok_menipis) }},
                timer: null,
                init() {
                    this.timer = setInterval(async () => {
                        try {
                            const response = await fetch('{{ route('dashboard.low-stock') }}');
                            if (response.ok) {
                                this.stokMenipis = await response.json();
                                this.$nextTick(() => {
                                    if (window.lucide) {
                                        window.lucide.createIcons();
                                    }
                                });
                            }
                        } catch (e) {
                            console.error('Error fetching low stock:', e);
                        }
                    }, 3000);
                },
                destroy() {
                    if (this.timer) {
                        clearInterval(this.timer);
                    }
                }
             }" 
             wire:key="aktivitas-kasir-container">
            
            <!-- Widget Header -->
            <div class="flex items-center justify-between border-b border-slate-100 pb-4 mb-4">
                <div>
                    <h3 class="font-bold text-slate-800 text-lg" x-text="showLog ? 'Aktivitas Kasir' : 'Peringatan Stok Menipis'">Peringatan Stok Menipis</h3>
                    <p class="text-xs text-slate-400" x-text="showLog ? 'Daftar transaksi kasir terbaru dari database.' : 'Daftar produk dengan stok menipis saat ini.'">Daftar produk dengan stok menipis saat ini.</p>
                </div>
                <!-- Toggle Button -->
                <button @click="showLog = !showLog; $nextTick(() => { if(window.lucide) { window.lucide.createIcons(); } })" 
                        class="p-2 rounded-xl bg-slate-50 border border-slate-200/60 hover:bg-slate-100 text-slate-600 transition-all flex items-center justify-center"
                        :title="showLog ? 'Tampilkan Stok Menipis' : 'Tampilkan Aktivitas Kasir'">
                    <i :data-lucide="showLog ? 'alert-triangle' : 'history'" class="w-4 h-4"></i>
                </button>
            </div>

            <!-- Widget Body: Low Stock List (Default) -->
            <div x-show="!showLog" class="flex-1 flex flex-col justify-between min-h-[300px]">
                <div class="overflow-y-auto max-h-[300px] space-y-3 pr-1 flex-1">
                    <template x-for="item in stokMenipis" :key="item.id">
                        <a :href="'{{ route('inventory') }}?edit_sku=' + item.sku" 
                           data-spa-ignore
                           class="container_stok flex items-center justify-between p-3 rounded-xl bg-indigo-50/20 hover:bg-indigo-50/50 transition-all duration-200 block">
                            <div class="flex items-center gap-3">
                                <div class="h-9 w-9 rounded-xl flex items-center justify-center font-bold bg-indigo-50 text-indigo-600">
                                    <i data-lucide="package" class="w-4 h-4"></i>
                                </div>
                                <div>
                                    <h4 class="text-xs font-bold text-slate-850" x-text="item.name"></h4>
                                    <span class="text-[9px] text-slate-450" x-text="item.sku"></span>
                                </div>
                            </div>
                            <div class="text-right">
                                <span class="text-xs font-black text-indigo-600" x-text="item.stock + ' Sisa'"></span>
                                <div class="mt-0.5">
                                    <span class="text-[8px] font-semibold text-slate-400 font-medium" x-text="'Min: ' + item.min_stock"></span>
                                </div>
                            </div>
                        </a>
                    </template>
                    
                    <template x-if="stokMenipis.length === 0">
                        <div class="flex flex-col items-center justify-center h-full text-center py-12 text-slate-450">
                            <div class="p-3 bg-emerald-50 text-emerald-600 rounded-2xl mb-2">
                                <i data-lucide="check" class="w-6 h-6"></i>
                            </div>
                            <h4 class="text-xs font-bold text-slate-700">Semua Stok Aman</h4>
                            <p class="text-[10px] text-slate-400 mt-0.5">Tidak ada barang dengan stok menipis.</p>
                        </div>
                    </template>
                </div>
                
                <div class="mt-4 pt-4 border-t border-slate-100">
                    <a href="{{ route('inventory', ['filter' => 'low_stock']) }}" 
                       class="btn-kelola w-full py-2.5 text-white text-xs font-bold rounded-xl flex items-center justify-center gap-2 transition-all shadow-sm shadow-indigo-900/10 active:scale-[0.98]">
                        <i data-lucide="settings-2" class="w-4 h-4"></i>
                        <span>Kelola & Restock Barang</span>
                    </a>
                </div>
            </div>

            <!-- Widget Body: Cashier Log (Aktivitas Kasir) -->
            <div x-show="showLog" class="flex-1 flex flex-col justify-between min-h-[300px]">
                <div class="overflow-y-auto max-h-[300px] space-y-3 pr-1 flex-1">
                    @forelse($aktivitas_kasir as $trx)
                        <div wire:key="trx-item-{{ $trx->id }}" class="flex items-center justify-between p-3 rounded-xl border border-slate-100 hover:border-slate-200 hover:bg-slate-50/50 transition-all duration-200">
                            <div class="flex items-center gap-3">
                                <div class="h-9 w-9 rounded-xl flex items-center justify-center font-bold text-xs bg-indigo-50 text-indigo-600">
                                    <i data-lucide="receipt" class="w-4 h-4"></i>
                                </div>
                                <div>
                                    <div class="flex items-center gap-1.5">
                                        <span class="text-xs font-bold text-slate-800">{{ $trx->invoice }}</span>
                                        <span class="text-[9px] text-slate-450 font-mono">{{ $trx->created_at ? $trx->created_at->format('H:i') : '-' }}</span>
                                    </div>
                                    <p class="text-[10px] text-slate-500 font-medium">Pelanggan: <span class="text-slate-700 font-semibold">{{ $trx->customer_name ?? 'Umum' }}</span></p>
                                </div>
                            </div>
                            <div class="text-right">
                                <span class="text-xs font-bold text-slate-800">Rp {{ number_format($trx->total_harga, 0, ',', '.') }}</span>
                                <div class="mt-1">
                                    <span class="text-[9px] font-bold px-2 py-0.5 rounded-full uppercase tracking-wider bg-emerald-50 text-emerald-600">
                                        {{ $trx->status }}
                                    </span>
                                </div>
                            </div>
                        </div>
                    @empty
                        <div wire:key="trx-empty-state" class="flex flex-col items-center justify-center h-full text-center py-12 text-slate-450">
                            <div class="p-3 bg-slate-50 text-slate-400 rounded-2xl mb-2">
                                <i data-lucide="receipt" class="w-6 h-6"></i>
                            </div>
                            <h4 class="text-xs font-bold text-slate-700">Tidak Ada Transaksi</h4>
                            <p class="text-[10px] text-slate-400 mt-0.5">Belum ada transaksi kasir hari ini.</p>
                        </div>
                    @endforelse
                </div>
            </div>

        </div>

    </div>

</div>

@push('scripts')
<!-- Chart.js Library -->
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

<script>
    function initWeeklySalesChart() {
        const canvas = document.getElementById('weeklySalesChart');
        if (!canvas) return;
        const ctx = canvas.getContext('2d');

        // Destroy previous instance to prevent "Canvas is already in use" and memory leaks
        if (window.weeklySalesChartInstance) {
            window.weeklySalesChartInstance.destroy();
        }
        
        // Setup gradient background untuk chart
        const gradient = ctx.createLinearGradient(0, 0, 0, 300);
        gradient.addColorStop(0, 'rgba(79, 70, 229, 0.3)');  // Indigo dengan transparansi
        gradient.addColorStop(1, 'rgba(79, 70, 229, 0.00)'); // Pudar ke transparan
 
        // Injeksi data tren mingguan dari backend menggunakan directive json
        const weeklySalesData = @json($tren_penjualan_mingguan);
 
        window.weeklySalesChartInstance = new Chart(ctx, {
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

        if (window.ResizeObserver) {
            const observer = new ResizeObserver(() => {
                if (window.weeklySalesChartInstance) {
                    window.weeklySalesChartInstance.resize();
                }
            });
            observer.observe(canvas);
        }
    }

    // Run immediately for initial load and custom Axios SPA page insertion
    initWeeklySalesChart();

    // Listen to Livewire navigate event (Livewire 3 SPA mode support)
    document.addEventListener('livewire:navigated', initWeeklySalesChart);


</script>
@endpush
@endsection
