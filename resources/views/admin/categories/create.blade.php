@extends('layouts.app')

@section('title', 'Tambah Kategori Baru')
@section('active_page', 'inventory')

@section('content')
<div class="space-y-6 max-w-xl">

    <!-- Page Header -->
    <div class="flex items-center gap-3">
        <a href="{{ route('categories.index') }}" 
           class="p-2 bg-white hover:bg-slate-50 border border-slate-200 text-slate-600 rounded-xl transition-all shadow-sm">
            <i data-lucide="arrow-left" class="w-4 h-4"></i>
        </a>
        <div>
            <h2 class="text-2xl font-bold text-slate-800">Tambah Kategori Baru</h2>
            <p class="text-sm text-slate-500">Buat kategori produk baru untuk mengelompokkan barang dagangan.</p>
        </div>
    </div>

    <!-- Form Container -->
    <div class="bg-white p-6 rounded-2xl border border-slate-200/80 shadow-sm">
        <form action="{{ route('categories.store') }}" method="POST" class="space-y-6">
            <!-- Include the shared form partial -->
            @include('admin.categories.form')

            <!-- Form Actions -->
            <div class="flex items-center justify-end gap-3 pt-4 border-t border-slate-100">
                <a href="{{ route('categories.index') }}" 
                   class="px-4 py-2.5 rounded-xl border border-slate-250 text-slate-500 hover:text-slate-700 hover:bg-slate-50 font-bold text-xs transition-all">
                    Batal
                </a>
                <button type="submit" 
                        class="bg-indigo-600 hover:bg-indigo-700 text-white font-bold px-5 py-2.5 rounded-xl text-xs flex items-center gap-2 shadow-md shadow-indigo-600/10 active:scale-[0.98] transition-all">
                    <i data-lucide="save" class="w-4 h-4"></i>
                    <span>Simpan Kategori</span>
                </button>
            </div>
        </form>
    </div>
</div>
@endsection
