@extends('layouts.app')

@section('title', 'Tambah Pengeluaran')
@section('active_page', 'expenses')

@section('content')
<div class="p-6 sm:p-10 space-y-6 max-w-4xl mx-auto">
    <!-- Header Section -->
    <div class="flex flex-col md:flex-row md:items-center justify-between gap-4 mb-2">
        <div>
            <h1 class="text-2xl font-black text-slate-800 tracking-tight flex items-center gap-2">
                <i data-lucide="plus-circle" class="w-7 h-7 text-indigo-600"></i>
                Tambah Pengeluaran Baru
            </h1>
            <p class="text-sm text-slate-500 mt-1">Catat biaya operasional atau pengeluaran lainnya.</p>
        </div>
    </div>

    @if($errors->any())
        <div class="bg-rose-50 border border-rose-200 text-rose-700 px-5 py-4 rounded-2xl mb-6 shadow-sm flex gap-3 items-start">
            <i data-lucide="alert-circle" class="w-5 h-5 flex-shrink-0 mt-0.5"></i>
            <ul class="list-disc list-inside text-sm font-medium space-y-1">
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <div class="bg-white rounded-3xl shadow-sm border border-slate-200 overflow-hidden">
        <form action="{{ route($rolePrefix . '.expenses.store') }}" method="POST">
            @csrf
            
            <div class="p-6 sm:p-8 space-y-6">
                <!-- Tanggal -->
                <div>
                    <label for="tanggal" class="block mb-2 text-xs font-bold text-slate-500 uppercase tracking-wider">Tanggal</label>
                    <div class="relative">
                        <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none">
                            <i data-lucide="calendar" class="w-5 h-5 text-slate-400"></i>
                        </div>
                        <input type="date" id="tanggal" name="tanggal" value="{{ old('tanggal') ?? today()->format('Y-m-d') }}" 
                               class="bg-slate-50 border border-slate-200 text-slate-800 text-sm rounded-xl focus:ring-2 focus:ring-indigo-500/20 focus:border-indigo-500 block w-full pl-11 p-3 transition duration-200 font-medium" required>
                    </div>
                </div>

                <!-- Deskripsi -->
                <div>
                    <label for="deskripsi" class="block mb-2 text-xs font-bold text-slate-500 uppercase tracking-wider">Deskripsi Pengeluaran</label>
                    <div class="relative">
                        <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none">
                            <i data-lucide="file-text" class="w-5 h-5 text-slate-400"></i>
                        </div>
                        <input type="text" id="deskripsi" name="deskripsi" value="{{ old('deskripsi') }}" placeholder="Contoh: Bayar listrik bulan ini..."
                               class="bg-slate-50 border border-slate-200 text-slate-800 text-sm rounded-xl focus:ring-2 focus:ring-indigo-500/20 focus:border-indigo-500 block w-full pl-11 p-3 transition duration-200 font-medium" required>
                    </div>
                </div>

                <!-- Nominal -->
                <div>
                    <label for="nominal" class="block mb-2 text-xs font-bold text-slate-500 uppercase tracking-wider">Nominal (Rp)</label>
                    <div class="relative">
                        <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none">
                            <span class="text-slate-500 font-bold">Rp</span>
                        </div>
                        <input type="number" id="nominal" name="nominal" value="{{ old('nominal') }}" placeholder="0"
                               class="bg-slate-50 border border-slate-200 text-slate-800 text-sm rounded-xl focus:ring-2 focus:ring-indigo-500/20 focus:border-indigo-500 block w-full pl-12 p-3 transition duration-200 font-bold tracking-wide" required min="0" step="1">
                    </div>
                </div>
            </div>

            <!-- Form Actions -->
            <div class="bg-slate-50 p-6 sm:px-8 border-t border-slate-200 flex flex-col-reverse sm:flex-row items-center justify-end gap-3 rounded-b-3xl">
                <a href="{{ route($rolePrefix . '.expenses.index') }}" 
                   data-spa-ignore
                   class="w-full sm:w-auto px-6 py-2.5 bg-white border border-slate-200 text-slate-600 font-semibold rounded-xl hover:bg-slate-100 hover:text-slate-800 transition-all flex items-center justify-center gap-2">
                    <i data-lucide="x" class="w-4 h-4"></i>
                    Batal
                </a>
                <button type="submit" 
                        class="w-full sm:w-auto px-6 py-2.5 bg-gradient-to-r from-indigo-600 to-violet-600 hover:from-indigo-700 hover:to-violet-700 text-white font-bold rounded-xl shadow-md shadow-indigo-200 transition-all flex items-center justify-center gap-2 active:scale-95">
                    <i data-lucide="save" class="w-4 h-4"></i>
                    Simpan Pengeluaran
                </button>
            </div>
        </form>
    </div>
</div>
@endsection