@extends('layouts.app')

@section('title', 'Kelola Kasir')
@section('active_page', 'settings')

@section('content')
<div class="space-y-6">

    <!-- Page Header -->
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
        <div>
            <h2 class="text-2xl font-bold text-slate-800">Manajemen Akun Kasir</h2>
            <p class="text-sm text-slate-500">Buat, perbarui, dan nonaktifkan akses kasir untuk transaksi POS.</p>
        </div>
        <div>
            <a href="{{ route('cashiers.create') }}" 
               class="bg-indigo-600 hover:bg-indigo-700 text-white font-bold px-4 py-2.5 rounded-xl text-xs flex items-center gap-2 shadow-md shadow-indigo-600/10 active:scale-[0.98] transition-all">
                <i data-lucide="plus" class="w-4 h-4"></i>
                <span>Tambah Kasir</span>
            </a>
        </div>
    </div>

    <!-- Main Content Card -->
    <div class="bg-white rounded-2xl border border-slate-200/80 shadow-sm overflow-hidden">
        
        <!-- Table wrapper -->
        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse">
                <thead>
                    <tr class="bg-slate-50/50 border-b border-slate-100 text-[11px] font-bold text-slate-400 uppercase tracking-wider">
                        <th class="px-6 py-4">Nama Kasir</th>
                        <th class="px-6 py-4">Alamat Email</th>
                        <th class="px-6 py-4">Terdaftar Pada</th>
                        <th class="px-6 py-4 text-center">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100 text-xs">
                    @forelse ($cashiers as $cashier)
                        <tr class="hover:bg-slate-50/50 transition-colors">
                            <!-- Cashier Name & Avatar -->
                            <td class="px-6 py-3.5">
                                <div class="flex items-center gap-3">
                                    <div class="h-8 w-8 rounded-full bg-gradient-to-tr from-indigo-500 to-violet-500 flex items-center justify-center text-white font-bold text-xs shadow-sm">
                                        {{ strtoupper(substr($cashier->name, 0, 2)) }}
                                    </div>
                                    <span class="font-bold text-slate-800">{{ $cashier->name }}</span>
                                </div>
                            </td>
                            
                            <!-- Email -->
                            <td class="px-6 py-3.5 text-slate-600 font-medium">
                                {{ $cashier->email }}
                            </td>
                            
                            <!-- Joined Date -->
                            <td class="px-6 py-3.5 text-slate-500 font-medium">
                                {{ $cashier->created_at->timezone('Asia/Jakarta')->format('d M Y, H:i') }} WIB
                            </td>
                            
                            <!-- Actions -->
                            <td class="px-6 py-3.5 text-center">
                                <div class="flex items-center justify-center gap-2">
                                    <!-- Edit Button -->
                                    <a href="{{ route('cashiers.edit', $cashier->id) }}" 
                                       class="p-1.5 rounded-lg border border-slate-200 text-slate-600 hover:text-indigo-600 hover:border-indigo-100 hover:bg-indigo-50/50 transition-all"
                                       title="Edit Kasir">
                                        <i data-lucide="edit-2" class="w-4 h-4"></i>
                                    </a>
                                    
                                    <!-- Delete Button (using simple confirmation form) -->
                                    <form action="{{ route('cashiers.destroy', $cashier->id) }}" 
                                          method="POST" 
                                          onsubmit="return confirm('Apakah Anda yakin ingin menghapus akun kasir {{ $cashier->name }}?');"
                                          class="inline-block">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" 
                                                class="p-1.5 rounded-lg border border-slate-200 text-slate-600 hover:text-rose-600 hover:border-rose-100 hover:bg-rose-50/50 transition-all"
                                                title="Hapus Kasir">
                                            <i data-lucide="trash-2" class="w-4 h-4"></i>
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="4" class="px-6 py-12 text-center">
                                <div class="flex flex-col items-center justify-center gap-3">
                                    <div class="p-3 bg-slate-50 rounded-2xl text-slate-400">
                                        <i data-lucide="users" class="w-8 h-8"></i>
                                    </div>
                                    <div class="text-slate-500 font-medium">Belum ada akun kasir terdaftar</div>
                                    <div class="text-[11px] text-slate-400">Klik tombol Tambah Kasir di atas untuk mulai membuat akun.</div>
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <!-- Pagination -->
        @if ($cashiers->hasPages())
            <div class="px-6 py-4 border-t border-slate-100 bg-slate-50/30">
                {{ $cashiers->links() }}
            </div>
        @endif

    </div>
</div>
@endsection
