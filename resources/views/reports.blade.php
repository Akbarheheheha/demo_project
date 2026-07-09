@extends('layouts.app')

@section('title', 'Laporan Analisis Bisnis')
@section('active_page', 'reports')

@section('content')
<div class="space-y-6" x-data="reportsComponent">

    <!-- Welcome Header -->
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
        <div>
            <h2 class="text-2xl font-bold text-slate-800">Laporan & Analisis Penjualan</h2>
            <p class="text-sm text-slate-500">Tinjau pendapatan bersih, laba kotor, dan komparasi performa dari periode sebelumnya.</p>
        </div>
        
        <!-- Action Buttons (Export) -->
        <div class="flex items-center gap-2">
            <!-- Export PDF -->
            <button onclick="window.location.href='{{ route($rolePrefix . '.reports.export.pdf', array_merge(request()->query(), ['period' => $activePeriod])) }}'"
               class="bg-white hover:bg-slate-50 border border-slate-200 text-slate-700 font-semibold px-3.5 py-2 rounded-xl text-xs flex items-center gap-1.5 transition-all shadow-sm cursor-pointer">
                <i data-lucide="file-text" class="w-4 h-4 text-rose-500"></i>
                <span>Export PDF</span>
            </button>
            
            <!-- Export Excel -->
            <button onclick="window.location.href='{{ route($rolePrefix . '.reports.export.excel', array_merge(request()->query(), ['period' => $activePeriod])) }}'"
               class="bg-white hover:bg-slate-50 border border-slate-200 text-slate-700 font-semibold px-3.5 py-2 rounded-xl text-xs flex items-center gap-1.5 transition-all shadow-sm cursor-pointer">
                <i data-lucide="file-spreadsheet" class="w-4 h-4 text-emerald-500"></i>
                <span>Export Excel</span>
            </button>
        </div>
    </div>

    <!-- Quick Date Filter Panel -->
    <div class="bg-white p-4 rounded-2xl border border-slate-200/80 shadow-sm flex flex-wrap gap-2 items-center">
        <span class="text-xs font-bold text-slate-400 uppercase tracking-wider mr-2">Periode Laporan:</span>
        <a href="{{ route($rolePrefix . '.reports', ['period' => 'week']) }}"
           class="px-3.5 py-1.5 text-xs font-semibold rounded-xl border transition-all {{ $activePeriod === 'week' ? 'bg-indigo-50 border-indigo-200 text-indigo-700' : 'bg-slate-50 hover:bg-slate-105 border-slate-200 text-slate-600' }}">
            Minggu Ini
        </a>
        <a href="{{ route($rolePrefix . '.reports', ['period' => 'month']) }}"
           class="px-3.5 py-1.5 text-xs font-semibold rounded-xl border transition-all {{ $activePeriod === 'month' ? 'bg-indigo-50 border-indigo-200 text-indigo-700' : 'bg-slate-50 hover:bg-slate-105 border-slate-200 text-slate-600' }}">
            Bulan Ini
        </a>
        <a href="{{ route($rolePrefix . '.reports', ['period' => 'quarter']) }}"
           class="px-3.5 py-1.5 text-xs font-semibold rounded-xl border transition-all {{ $activePeriod === 'quarter' ? 'bg-indigo-50 border-indigo-200 text-indigo-700' : 'bg-slate-50 hover:bg-slate-105 border-slate-200 text-slate-600' }}">
            Kuartal Terakhir
        </a>
        <a href="{{ route($rolePrefix . '.reports', ['period' => 'year']) }}"
           class="px-3.5 py-1.5 text-xs font-semibold rounded-xl border transition-all {{ $activePeriod === 'year' ? 'bg-indigo-50 border-indigo-200 text-indigo-700' : 'bg-slate-50 hover:bg-slate-105 border-slate-200 text-slate-600' }}">
            Tahun Ini
        </a>
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
        <button type="button" @click="showExpenseModal = true" class="bg-white p-5 rounded-2xl border border-slate-200/80 shadow-sm relative overflow-hidden text-left w-full hover:shadow-md hover:border-slate-300 transition-all duration-200 active:scale-[0.99] cursor-pointer focus:outline-none">
            <div class="flex items-start justify-between">
                <div>
                    <span class="text-xs font-bold text-slate-400 uppercase tracking-wider block">Total Pengeluaran</span>
                    <h3 class="text-xl font-bold text-slate-800 mt-2" x-text="'Rp ' + new Intl.NumberFormat('id-ID').format(totalPengeluaran)">Rp {{ number_format($financialSummary['total_pengeluaran'], 0, ',', '.') }}</h3>
                    <p class="text-[10px] text-slate-400 mt-2 flex items-center gap-1">
                        <span>Biaya operasional bulan berjalan</span>
                        <svg class="w-3 h-3 text-rose-500 inline-block" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                        </svg>
                    </p>
                </div>
                <div class="p-2.5 bg-rose-50 text-rose-600 rounded-xl">
                    <i data-lucide="shopping-bag" class="w-5.5 h-5.5"></i>
                </div>
            </div>
        </button>

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

    <!-- Table: Total Omset per Kasir -->
    <div class="bg-white rounded-2xl border border-slate-200/80 shadow-sm overflow-hidden mt-6">
        <div class="px-6 py-5 border-b border-slate-100 flex items-center justify-between">
            <div>
                <h3 class="font-bold text-slate-800 text-base">Total Omset per Kasir Bulan Ini</h3>
                <p class="text-xs text-slate-400">Ringkasan transaksi sukses yang dikelompokkan berdasarkan akun kasir.</p>
            </div>
            <span class="text-[10px] font-bold bg-emerald-50 text-emerald-700 px-3 py-1 rounded-xl uppercase tracking-wider">Bulan Ini</span>
        </div>
        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse">
                <thead>
                    <tr class="bg-slate-50/50 border-b border-slate-100 text-[11px] font-bold text-slate-400 uppercase tracking-wider">
                        <th class="px-6 py-4">Nama Kasir</th>
                        <th class="px-6 py-4 text-right">Total Omset</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100 text-xs">
                    @forelse($cashierRevenues as $row)
                        <tr class="hover:bg-slate-50/50 transition-colors">
                            <td class="px-6 py-3.5 font-semibold text-slate-800">{{ $row->user->name ?? 'Sistem' }}</td>
                            <td class="px-6 py-3.5 text-right font-black text-emerald-600">Rp {{ number_format($row->total_omset, 0, ',', '.') }}</td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="2" class="text-center py-12 text-slate-400 font-semibold">
                                Belum ada omset kasir bulan ini.
                            </td>
                        </tr>
                    @endforelse
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
                        <th class="px-6 py-4">Metode Bayar</th>
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
                            <td class="px-6 py-3.5 text-slate-600 font-bold">{{ $trx->payment_method ?? 'Tunai' }}</td>
                            <td class="px-6 py-3.5 text-right font-black text-indigo-600">Rp {{ number_format($trx->total_harga, 0, ',', '.') }}</td>
                            <td class="px-6 py-3.5 text-center">
                                <span class="px-2.5 py-0.5 rounded-full text-[10px] font-bold uppercase tracking-wider {{ $trx->status === 'success' ? 'bg-emerald-50 text-emerald-600' : 'bg-amber-50 text-amber-600' }}">
                                    {{ $trx->status }}
                                </span>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="text-center py-12 text-slate-400 font-semibold">
                                Belum ada data transaksi penjualan di database.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <!-- Pagination -->
        @if ($transactions instanceof \Illuminate\Contracts\Pagination\LengthAwarePaginator && $transactions->hasPages())
            <div class="px-6 py-4 border-t border-slate-100 bg-slate-50/30">
                {{ $transactions->links() }}
            </div>
        @endif
    <!-- Modal Detail Pengeluaran -->
    <div x-show="showExpenseModal" 
         class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-slate-900/60 backdrop-blur-sm"
         x-transition:enter="transition ease-out duration-300"
         x-transition:enter-start="opacity-0"
         x-transition:enter-end="opacity-100"
         x-transition:leave="transition ease-in duration-200"
         x-transition:leave-start="opacity-100"
         x-transition:leave-end="opacity-0"
         style="display: none;"
         @keydown.escape.window="showExpenseModal = false">
        
        <!-- Modal Content Container -->
        <div class="bg-white w-full max-w-2xl rounded-2xl border border-slate-200/80 shadow-2xl overflow-hidden"
             @click.away="showExpenseModal = false"
             x-transition:enter="transition ease-out duration-300"
             x-transition:enter-start="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
             x-transition:enter-end="opacity-100 translate-y-0 sm:scale-100"
             x-transition:leave="transition ease-in duration-200"
             x-transition:leave-start="opacity-100 translate-y-0 sm:scale-100"
             x-transition:leave-end="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95">
            
            <!-- Modal Header -->
            <div class="px-6 py-5 border-b border-slate-100 flex items-center justify-between bg-slate-50/50">
                <div>
                    <h3 class="font-bold text-slate-800 text-base">Rincian Pengeluaran</h3>
                    <p class="text-xs text-slate-400">Daftar lengkap biaya operasional pada periode yang dipilih.</p>
                </div>
                <div class="flex items-center gap-2">
                    <button type="button" 
                            @click="showAddExpenseForm = !showAddExpenseForm" 
                            class="bg-rose-50 hover:bg-rose-100 border border-rose-100 text-rose-700 font-bold px-3.5 py-2 rounded-xl text-xs flex items-center gap-1.5 transition-all active:scale-95">
                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 4v16m8-8H4"/>
                        </svg>
                        <span x-text="showAddExpenseForm ? 'Batal' : 'Catat Pengeluaran'"></span>
                    </button>
                    <button type="button" 
                            @click="showExpenseModal = false" 
                            class="text-slate-400 hover:text-slate-600 hover:bg-slate-100 p-1.5 rounded-xl transition-all active:scale-95">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                        </svg>
                    </button>
                </div>
            </div>

            <!-- Form Input Pengeluaran Baru -->
            <div x-show="showAddExpenseForm" 
                 x-transition:enter="transition ease-out duration-200"
                 x-transition:enter-start="opacity-0 transform -translate-y-2"
                 x-transition:enter-end="opacity-100 transform translate-y-0"
                 x-transition:leave="transition ease-in duration-150"
                 x-transition:leave-start="opacity-100 transform translate-y-0"
                 x-transition:leave-end="opacity-0 transform -translate-y-2"
                 class="border-b border-slate-100 bg-slate-50/50 p-5 space-y-4"
                 style="display: none;">
                <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                    <!-- Nama Pengeluaran -->
                    <div class="space-y-1">
                        <label class="text-[10px] font-bold text-slate-500 uppercase tracking-wider">Nama Pengeluaran</label>
                        <input type="text" 
                               x-model="newExpense.nama_pengeluaran" 
                               placeholder="Beli kertas kasir, dll" 
                               class="w-full text-xs bg-white rounded-xl px-3 py-2.5 border border-slate-200 focus:outline-none focus:border-rose-500 text-slate-800 font-medium">
                    </div>
                    <!-- Nominal -->
                    <div class="space-y-1">
                        <label class="text-[10px] font-bold text-slate-500 uppercase tracking-wider">Nominal (Rp)</label>
                        <div class="flex items-center gap-1.5 bg-white border border-slate-200 rounded-xl px-3.5 py-2.5 focus-within:border-rose-500 transition-all">
                            <span class="text-xs font-bold text-slate-400">Rp</span>
                            <input type="text" 
                                   :value="formattedNominal" 
                                   @input="setNominal($event.target.value)"
                                   placeholder="0" 
                                   class="bg-transparent border-none text-xs font-semibold focus:outline-none w-full text-slate-800 p-0">
                        </div>
                    </div>
                    <!-- Tanggal -->
                    <div class="space-y-1">
                        <label class="text-[10px] font-bold text-slate-500 uppercase tracking-wider">Tanggal</label>
                        <input type="date" 
                               x-model="newExpense.tanggal" 
                               class="w-full text-xs bg-white rounded-xl px-3 py-2.5 border border-slate-200 focus:outline-none focus:border-rose-500 text-slate-800 font-medium">
                    </div>
                </div>
                <!-- Deskripsi/Keterangan (Nullable) -->
                <div class="space-y-1">
                    <label class="text-[10px] font-bold text-slate-500 uppercase tracking-wider">Deskripsi / Keterangan (Opsional)</label>
                    <textarea x-model="newExpense.deskripsi" 
                              placeholder="Keterangan tambahan mengenai pengeluaran..." 
                              rows="2"
                              class="w-full text-xs bg-white rounded-xl px-3 py-2 border border-slate-200 focus:outline-none focus:border-rose-500 text-slate-800 font-medium"></textarea>
                </div>
                <div class="flex justify-end gap-2.5">
                    <button type="button" 
                            @click="showAddExpenseForm = false" 
                            class="px-4 py-2 border border-slate-200 text-slate-600 font-semibold rounded-xl text-xs hover:bg-slate-100 transition-colors">
                        Batal
                    </button>
                    <button type="button" 
                            @click="saveNewExpense()" 
                            :disabled="isSavingExpense"
                            class="bg-rose-600 hover:bg-rose-700 text-white font-bold px-5 py-2 rounded-xl text-xs flex items-center gap-1.5 transition-all shadow-md shadow-rose-600/10 active:scale-[0.98]">
                        <svg x-show="isSavingExpense" class="animate-spin -ml-0.5 mr-1 h-3.5 w-3.5 text-white" fill="none" viewBox="0 0 24 24">
                            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                        </svg>
                        <span x-text="isSavingExpense ? 'Menyimpan...' : 'Simpan Pengeluaran'"></span>
                    </button>
                </div>
            </div>

            <!-- Modal Body (Table of Expenses) -->
            <div class="max-h-[60vh] overflow-y-auto">
                <table class="w-full text-left border-collapse">
                    <thead>
                        <tr class="bg-slate-50 border-b border-slate-100 text-[10px] font-bold text-slate-400 uppercase tracking-wider sticky top-0">
                            <th class="px-6 py-3">No.</th>
                            <th class="px-6 py-3">Nama Pengeluaran</th>
                            <th class="px-6 py-3 text-right">Nominal</th>
                            <th class="px-6 py-3 text-center">Tanggal</th>
                            <th class="px-6 py-3 text-center">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100 text-xs">
                        <template x-for="(exp, index) in expenses" :key="exp.id">
                            <tr class="hover:bg-slate-50/30 transition-colors">
                                <td class="px-6 py-4 text-slate-400 font-medium" x-text="index + 1"></td>
                                <td class="px-6 py-4 font-bold text-slate-800">
                                    <span x-text="exp.nama_pengeluaran"></span>
                                    <p class="text-[10px] text-slate-400 font-normal mt-0.5" x-show="exp.deskripsi" x-text="exp.deskripsi"></p>
                                </td>
                                <td class="px-6 py-4 font-black font-mono text-right text-rose-600" 
                                    x-text="'Rp ' + new Intl.NumberFormat('id-ID').format(exp.nominal)"></td>
                                <td class="px-6 py-4 text-center text-slate-500 font-semibold" 
                                    x-text="formatDate(exp.tanggal)"></td>
                                <td class="px-6 py-4 text-center">
                                    <button type="button" 
                                            @click="deleteExpense(exp)" 
                                            class="text-slate-400 hover:text-red-500 hover:bg-red-50 p-1.5 rounded-lg transition-all active:scale-90"
                                            title="Hapus Pengeluaran">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                                        </svg>
                                    </button>
                                </td>
                            </tr>
                        </template>

                        <template x-if="expenses.length === 0">
                            <tr>
                                <td colspan="5" class="px-6 py-8 text-center text-slate-400 italic">
                                    Tidak ada catatan pengeluaran pada periode ini.
                                </td>
                            </tr>
                        </template>
                    </tbody>
                </table>
            </div>

            <!-- Modal Footer -->
            <div class="px-6 py-4 border-t border-slate-100 bg-slate-50/50 flex items-center justify-between">
                <div class="text-xs text-slate-500">
                    Total: <span class="font-bold text-slate-700" x-text="expenses.length"></span> item pengeluaran
                </div>
                <button type="button" 
                        @click="showExpenseModal = false" 
                        class="bg-slate-100 hover:bg-slate-200 text-slate-700 font-bold px-4 py-2 rounded-xl text-xs transition-colors active:scale-95">
                    Tutup
                </button>
            </div>
        </div>
    </div>

</div>

@push('scripts')
<script>
function reportsComponent() {
    return {
        showExpenseModal: false,
        showAddExpenseForm: false,
        isSavingExpense: false,
        expenses: @json($expenses),
        totalPengeluaran: {{ (float) $financialSummary['total_pengeluaran'] }},
        formattedNominal: '',
        newExpense: {
            nama_pengeluaran: '',
            nominal: '',
            tanggal: '{{ date('Y-m-d') }}',
            deskripsi: ''
        },
        formatDate(dateStr) {
            if (!dateStr) return '';
            return dateStr.split('T')[0];
        },
        setNominal(value) {
            const rawValue = value.replace(/\D/g, '');
            this.newExpense.nominal = rawValue;
            if (rawValue) {
                this.formattedNominal = new Intl.NumberFormat('id-ID').format(rawValue);
            } else {
                this.formattedNominal = '';
            }
        },
        async saveNewExpense() {
            if (!this.newExpense.nama_pengeluaran || !this.newExpense.nominal || !this.newExpense.tanggal) {
                this.$dispatch('show-toast', { message: 'Semua field wajib diisi!', type: 'danger' });
                return;
            }
            this.isSavingExpense = true;
            try {
                const response = await axios.post('/api/expenses', this.newExpense);
                this.expenses.unshift(response.data);
                this.totalPengeluaran += parseFloat(response.data.nominal);
                this.newExpense = {
                    nama_pengeluaran: '',
                    nominal: '',
                    tanggal: '{{ date('Y-m-d') }}',
                    deskripsi: ''
                };
                this.formattedNominal = '';
                this.showAddExpenseForm = false;
                this.$dispatch('show-toast', { message: 'Pengeluaran berhasil dicatat!', type: 'success' });
            } catch (error) {
                console.error(error);
                this.$dispatch('show-toast', { message: 'Gagal mencatat pengeluaran.', type: 'danger' });
            } finally {
                this.isSavingExpense = false;
            }
        },
        async deleteExpense(expense) {
            if (confirm('Apakah Anda yakin ingin menghapus catatan pengeluaran "' + expense.nama_pengeluaran + '"?')) {
                try {
                    await axios.delete('/api/expenses/' + expense.id);
                    this.expenses = this.expenses.filter(e => e.id !== expense.id);
                    this.totalPengeluaran -= parseFloat(expense.nominal);
                    this.$dispatch('show-toast', { message: 'Catatan pengeluaran berhasil dihapus.', type: 'warning' });
                } catch (error) {
                    console.error(error);
                    this.$dispatch('show-toast', { message: 'Gagal menghapus pengeluaran.', type: 'danger' });
                }
            }
        }
    };
}
if (typeof Alpine !== 'undefined') {
    Alpine.data('reportsComponent', reportsComponent);
}
</script>

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
