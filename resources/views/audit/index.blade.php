@extends('layouts.app')

@section('title', 'Log Audit & Keamanan')
@section('active_page', 'audit-logs')

@section('content')
<div class="space-y-6">

    <!-- Welcome / Header Section -->
    <div class="flex flex-col md:flex-row md:items-center justify-between gap-4">
        <div>
            <h2 class="text-2xl font-bold text-slate-800 flex items-center gap-2">
                <i data-lucide="shield-check" class="w-7 h-7 text-indigo-600"></i>
                <span>Audit & Activity Logs</span>
            </h2>
            <p class="text-sm text-slate-500">Mencatat aktivitas mutasi stok, transaksi kasir, serta perubahan data sistem untuk kebutuhan kepatuhan (compliance) dan keamanan.</p>
        </div>
        
        <!-- Time Indicator -->
        <div class="flex items-center gap-2 px-3 py-1.5 rounded-xl bg-white border border-slate-200 text-xs font-medium text-slate-600 shadow-xs">
            <i data-lucide="clock" class="w-4 h-4 text-indigo-500"></i>
            <span>Sistem Waktu Nyata</span>
        </div>
    </div>

    <!-- Quick Stats Cards (Professional ERP Feel) -->
    <div class="grid grid-cols-1 md:grid-cols-3 gap-5">
        <div class="bg-white p-4.5 rounded-2xl border border-slate-200/80 shadow-xs flex items-center gap-4">
            <div class="p-3 bg-indigo-50 text-indigo-650 rounded-xl">
                <i data-lucide="activity" class="w-6 h-6"></i>
            </div>
            <div>
                <p class="text-xs font-bold text-slate-400 uppercase tracking-wider">Total Entri Log</p>
                <h3 class="text-xl font-bold text-slate-800 mt-0.5">{{ number_format($logs->total(), 0, ',', '.') }}</h3>
            </div>
        </div>
        
        <div class="bg-white p-4.5 rounded-2xl border border-slate-200/80 shadow-xs flex items-center gap-4">
            <div class="p-3 bg-emerald-50 text-emerald-650 rounded-xl">
                <i data-lucide="plus-circle" class="w-6 h-6"></i>
            </div>
            <div>
                <p class="text-xs font-bold text-slate-400 uppercase tracking-wider">Aksi Tambah/Masuk</p>
                <h3 class="text-xl font-bold text-slate-800 mt-0.5">
                    Aktif
                </h3>
            </div>
        </div>

        <div class="bg-white p-4.5 rounded-2xl border border-slate-200/80 shadow-xs flex items-center gap-4">
            <div class="p-3 bg-rose-550/10 text-rose-600 rounded-xl">
                <i data-lucide="alert-octagon" class="w-6 h-6"></i>
            </div>
            <div>
                <p class="text-xs font-bold text-slate-400 uppercase tracking-wider">Sensitivitas Sistem</p>
                <h3 class="text-xl font-bold text-slate-800 mt-0.5">Tinggi / Aman</h3>
            </div>
        </div>
    </div>

    <!-- Main Table Card -->
    <div class="bg-white rounded-2xl border border-slate-200/80 shadow-xs overflow-hidden">
        
        <!-- Search and Filter Bar -->
        <div class="p-5 border-b border-slate-100 bg-slate-50/50 flex flex-col sm:flex-row items-center justify-between gap-4">
            <div class="w-full sm:w-auto">
                <h3 class="font-bold text-slate-800 text-sm">Daftar Rekam Aktivitas</h3>
                <p class="text-xs text-slate-400 mt-0.5">Urut berdasarkan waktu terbaru</p>
            </div>
            
            <form action="{{ route($rolePrefix . '.audit-logs') }}" method="GET" class="w-full sm:w-80 flex items-center gap-2">
                <div class="relative w-full">
                    <span class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                        <i data-lucide="search" class="h-4 w-4 text-slate-400"></i>
                    </span>
                    <input type="text" 
                           name="search" 
                           value="{{ $search }}" 
                           placeholder="Cari deskripsi, aksi, atau kasir..." 
                           class="w-full pl-9 pr-4 py-2 text-sm bg-white border border-slate-200 rounded-xl focus:outline-none focus:ring-2 focus:ring-indigo-500/20 focus:border-indigo-500 transition-all placeholder-slate-400 text-slate-700 shadow-xs">
                    @if($search)
                        <a href="{{ route($rolePrefix . '.audit-logs') }}" class="absolute inset-y-0 right-0 pr-3 flex items-center text-slate-400 hover:text-slate-600" title="Bersihkan Pencarian">
                            <i data-lucide="x" class="h-4 w-4"></i>
                        </a>
                    @endif
                </div>
                <button type="submit" class="bg-indigo-650 hover:bg-indigo-700 text-white font-semibold text-xs px-4 py-2.5 rounded-xl transition-all shadow-xs flex items-center gap-1.5">
                    Cari
                </button>
            </form>
        </div>

        <!-- Table View -->
        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse">
                <thead>
                    <tr class="bg-slate-50 border-b border-slate-200/60 text-slate-400 text-[10px] uppercase font-bold tracking-wider">
                        <th class="px-6 py-4 w-48">Waktu Kejadian</th>
                        <th class="px-6 py-4 w-52">Operator (User)</th>
                        <th class="px-6 py-4 w-40">Aksi</th>
                        <th class="px-6 py-4">Deskripsi Aktivitas</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100 text-sm">
                    @forelse($logs as $log)
                        @php
                            $action = strtoupper($log->action);
                            
                            // Human-readable action labels
                            $actionLabels = [
                                'product.created' => 'Tambah Barang',
                                'product.updated' => 'Ubah Barang',
                                'product.deleted' => 'Hapus Barang',
                                'create transaction' => 'Transaksi Baru',
                            ];
                            $displayAction = $actionLabels[strtolower($log->action)] ?? $action;
                            
                            // Color scheme mapping
                            $badgeClass = 'bg-slate-100 text-slate-700 border-slate-200';
                            $dotColor = 'bg-slate-400';
                            
                            if (str_contains($action, 'CREATE') || str_contains($action, 'ADD') || str_contains($action, 'INSERT') || str_contains($action, 'STORE')) {
                                $badgeClass = 'bg-emerald-50 text-emerald-700 border-emerald-100';
                                $dotColor = 'bg-emerald-550';
                            } elseif (str_contains($action, 'UPDATE') || str_contains($action, 'EDIT') || str_contains($action, 'MODIFY') || str_contains($action, 'SAVE')) {
                                $badgeClass = 'bg-amber-50 text-amber-700 border-amber-100';
                                $dotColor = 'bg-amber-500';
                            } elseif (str_contains($action, 'DELETE') || str_contains($action, 'REMOVE') || str_contains($action, 'DESTROY')) {
                                $badgeClass = 'bg-rose-50 text-rose-700 border-rose-100';
                                $dotColor = 'bg-rose-500';
                            } elseif (str_contains($action, 'LOGIN')) {
                                $badgeClass = 'bg-sky-50 text-sky-700 border-sky-100';
                                $dotColor = 'bg-sky-500';
                            } elseif (str_contains($action, 'LOGOUT')) {
                                $badgeClass = 'bg-slate-100 text-slate-650 border-slate-200';
                                $dotColor = 'bg-slate-500';
                            }
                            
                            // Parse structured description details
                            $descriptionParts = explode(' | ', $log->description);
                            $mainDescription = $descriptionParts[0];
                            $detailParts = array_slice($descriptionParts, 1);
                        @endphp
                        <tr class="hover:bg-slate-50/70 transition-colors">
                            <!-- Time Column -->
                            <td class="px-6 py-4.5 whitespace-nowrap text-slate-600 font-mono text-xs">
                                <div class="flex items-center gap-1.5">
                                    <i data-lucide="calendar" class="w-3.5 h-3.5 text-slate-400"></i>
                                    <span>{{ $log->created_at ? $log->created_at->format('d/m/Y H:i') : '-' }}</span>
                                </div>
                            </td>
                            
                            <!-- User Column -->
                            <td class="px-6 py-4.5 whitespace-nowrap">
                                <div class="flex items-center gap-2.5">
                                    <div class="h-8 w-8 rounded-lg bg-indigo-50 border border-indigo-100/50 flex items-center justify-center text-indigo-700 font-bold text-xs">
                                        {{ $log->user ? strtoupper(substr($log->user->name, 0, 2)) : 'SYS' }}
                                    </div>
                                    <div>
                                        <div class="font-semibold text-slate-800">{{ $log->user ? $log->user->name : 'Sistem Otomatis' }}</div>
                                        <div class="text-[10px] text-slate-400 font-mono">ID: #{{ $log->user_id ?? 'System' }}</div>
                                    </div>
                                </div>
                            </td>
                            
                            <!-- Action Badge Column -->
                            <td class="px-6 py-4.5 whitespace-nowrap">
                                <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-xs font-semibold border {{ $badgeClass }}">
                                    <span class="h-1.5 w-1.5 rounded-full {{ $dotColor }}"></span>
                                    <span>{{ $displayAction }}</span>
                                </span>
                            </td>
                            
                            <!-- Description Column -->
                            <td class="px-6 py-4.5">
                                <div class="text-slate-700 font-medium break-words max-w-xl">
                                    <span>{{ $mainDescription }}</span>
                                    @if(count($detailParts) > 0)
                                        <div class="mt-1.5 flex flex-wrap gap-1.5">
                                            @foreach($detailParts as $detail)
                                                @php
                                                    $parts = explode(': ', $detail, 2);
                                                    $label = $parts[0] ?? '';
                                                    $value = $parts[1] ?? $detail;
                                                @endphp
                                                <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-md bg-slate-50 border border-slate-200 text-[11px] font-mono text-slate-600">
                                                    @if($label && isset($parts[1]))
                                                        <span class="text-slate-400 font-semibold">{{ $label }}:</span>
                                                    @endif
                                                    <span>{{ $value }}</span>
                                                </span>
                                            @endforeach
                                        </div>
                                    @endif
                                </div>
                            </td>
                        </tr>
                    @empty
                        <!-- Empty State -->
                        <tr>
                            <td colspan="4" class="px-6 py-12 text-center">
                                <div class="flex flex-col items-center justify-center gap-3">
                                    <div class="p-4 bg-slate-50 text-slate-400 rounded-full border border-slate-100">
                                        <i data-lucide="shield-alert" class="w-10 h-10"></i>
                                    </div>
                                    <h4 class="font-bold text-slate-700">Tidak ada log aktivitas ditemukan</h4>
                                    <p class="text-xs text-slate-400 max-w-sm">
                                        @if($search)
                                            Kata kunci pencarian "{{ $search }}" tidak mencocokkan rekaman data log manapun.
                                        @else
                                            Sistem belum mencatat log aktivitas apapun saat ini.
                                        @endif
                                    </p>
                                    @if($search)
                                        <a href="{{ route($rolePrefix . '.audit-logs') }}" class="mt-2 text-xs font-bold text-indigo-600 hover:text-indigo-850 flex items-center gap-1">
                                            <span>Lihat Semua Log</span>
                                            <i data-lucide="arrow-right" class="w-3.5 h-3.5"></i>
                                        </a>
                                    @endif
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <!-- Pagination Section -->
        @if($logs->hasPages())
            <div class="p-5 border-t border-slate-100 bg-slate-50/30">
                {{ $logs->links() }}
            </div>
        @endif

    </div>
</div>
@endsection
