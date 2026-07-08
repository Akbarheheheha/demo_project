@extends('layouts.app')

@section('title', 'Daftar Pengeluaran')
@section('active_page', 'expenses')

@section('content')
<div class="p-6 bg-slate-50 min-h-screen">
    <!-- Header Bagian Atas -->
    <div class="mb-8 bg-white p-6 rounded-2xl shadow-sm border border-slate-100 flex flex-col md:flex-row justify-between items-center gap-4">
        <div>
            <h1 class="text-3xl font-black text-slate-800 tracking-tight">
                Total Pengeluaran: <span class="text-rose-600">Rp. {{ number_format($totalPengeluaran, 0, ',', '.') }}</span>
            </h1>
            <p class="text-slate-500 mt-1 text-sm">Keseluruhan pengeluaran operasional bisnis.</p>
        </div>
        <a href="{{ route($rolePrefix . '.expenses.create') }}" class="px-5 py-2.5 bg-indigo-600 hover:bg-indigo-700 text-white text-sm font-bold rounded-xl shadow-md transition-all flex items-center gap-2">
            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor">
                <path fill-rule="evenodd" d="M10 5a1 1 0 011 1v3h3a1 1 0 110 2h-3v3a1 1 0 11-2 0v-3H6a1 1 0 110-2h3V6a1 1 0 011-1z" clip-rule="evenodd" />
            </svg>
            Tambah Pengeluaran
        </a>
    </div>

    <!-- Flash Message -->
    @if(session('success'))
        <div class="mb-6 p-4 bg-emerald-50 border border-emerald-200 text-emerald-700 rounded-xl flex items-center gap-3">
            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-emerald-600" viewBox="0 0 20 20" fill="currentColor">
                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd" />
            </svg>
            <span class="font-semibold text-sm">{{ session('success') }}</span>
        </div>
    @endif

    <!-- Tabel Pengeluaran -->
    <div class="bg-white rounded-2xl shadow-sm border border-slate-200 overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse">
                <thead>
                    <tr class="bg-slate-100 text-slate-600 text-xs uppercase tracking-wider">
                        <th class="px-6 py-4 font-bold border-b border-slate-200">Tanggal</th>
                        <th class="px-6 py-4 font-bold border-b border-slate-200">Keterangan</th>
                        <th class="px-6 py-4 font-bold border-b border-slate-200">Nominal</th>
                        <th class="px-6 py-4 font-bold border-b border-slate-200 text-center">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100 text-sm">
                    @forelse($expenses as $expense)
                        <tr class="hover:bg-slate-50 transition-colors">
                            <td class="px-6 py-4 text-slate-700 font-medium whitespace-nowrap">
                                {{ \Carbon\Carbon::parse($expense->tanggal)->translatedFormat('d F Y') }}
                            </td>
                            <td class="px-6 py-4 text-slate-600">
                                {{ $expense->nama_pengeluaran }}
                            </td>
                            <td class="px-6 py-4 text-rose-600 font-bold whitespace-nowrap">
                                Rp. {{ number_format($expense->nominal, 0, ',', '.') }}
                            </td>
                            <td class="px-6 py-4 flex items-center justify-center gap-3">
                                <a href="{{ route($rolePrefix . '.expenses.edit', $expense->id) }}" class="text-indigo-600 hover:text-indigo-800 transition-colors" title="Edit">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor">
                                        <path d="M13.586 3.586a2 2 0 112.828 2.828l-.793.793-2.828-2.828.793-.793zM11.379 5.793L3 14.172V17h2.828l8.379-8.379-2.828-2.828z" />
                                    </svg>
                                </a>
                                <form action="{{ route($rolePrefix . '.expenses.destroy', $expense->id) }}" method="POST" class="inline-block" onsubmit="return confirm('Apakah Anda yakin ingin menghapus catatan pengeluaran ini?');">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="text-rose-600 hover:text-rose-800 transition-colors" title="Hapus">
                                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor">
                                            <path fill-rule="evenodd" d="M9 2a1 1 0 00-.894.553L7.382 4H4a1 1 0 000 2v10a2 2 0 002 2h8a2 2 0 002-2V6a1 1 0 100-2h-3.382l-.724-1.447A1 1 0 0011 2H9zM7 8a1 1 0 012 0v6a1 1 0 11-2 0V8zm5-1a1 1 0 00-1 1v6a1 1 0 102 0V8a1 1 0 00-1-1z" clip-rule="evenodd" />
                                        </svg>
                                    </button>
                                </form>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="4" class="px-6 py-12 text-center">
                                <div class="flex flex-col items-center justify-center">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-12 w-12 text-slate-300 mb-3" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                                    </svg>
                                    <p class="text-slate-500 font-semibold">Belum ada catatan pengeluaran.</p>
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        
        <!-- Pagination -->
        @if($expenses->hasPages())
            <div class="px-6 py-4 border-t border-slate-100 bg-slate-50">
                {{ $expenses->links() }}
            </div>
        @endif
    </div>
</div>
@endsection
