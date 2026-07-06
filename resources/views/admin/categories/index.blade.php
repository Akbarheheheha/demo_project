@extends('layouts.app')

@section('title', 'Kategori Produk')
@section('active_page', 'inventory')

@section('content')
<div class="space-y-6">

    <!-- Page Header -->
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
        <div>
            <h2 class="text-2xl font-bold text-slate-800">Kategori Produk</h2>
            <p class="text-sm text-slate-500">Kelola kategori barang untuk merapikan inventaris dan POS.</p>
        </div>
        <div>
            <a href="{{ route('categories.create') }}" 
               class="bg-indigo-600 hover:bg-indigo-700 text-white font-bold px-4 py-2.5 rounded-xl text-xs flex items-center gap-2 shadow-md shadow-indigo-600/10 active:scale-[0.98] transition-all">
                <i data-lucide="plus" class="w-4 h-4"></i>
                <span>Tambah Kategori</span>
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
                        <th class="px-6 py-4">Nama Kategori</th>
                        <th class="px-6 py-4">Slug (URL)</th>
                        <th class="px-6 py-4">Jumlah Produk</th>
                        <th class="px-6 py-4 text-center">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100 text-xs">
                    @forelse ($categories as $category)
                        <tr class="hover:bg-slate-50/50 transition-colors">
                            <!-- Category Name -->
                            <td class="px-6 py-3.5">
                                <div class="flex items-center gap-3">
                                    <div class="h-8 w-8 rounded-full bg-gradient-to-tr from-violet-500 to-indigo-500 flex items-center justify-center text-white font-bold text-xs shadow-sm">
                                        {{ strtoupper(substr($category->name, 0, 2)) }}
                                    </div>
                                    <span class="font-bold text-slate-800">{{ $category->name }}</span>
                                </div>
                            </td>
                            
                            <!-- Slug -->
                            <td class="px-6 py-3.5 text-slate-500 font-mono text-[11px]">
                                {{ $category->slug }}
                            </td>
                            
                            <!-- Product Count -->
                            <td class="px-6 py-3.5">
                                <span class="px-2.5 py-1 bg-slate-100 text-slate-700 rounded-lg font-semibold text-[10px]">
                                    {{ $category->products_count }} Produk
                                </span>
                            </td>
                            
                            <!-- Actions -->
                            <td class="px-6 py-3.5 text-center">
                                <div class="flex items-center justify-center gap-2">
                                    <!-- Edit Button -->
                                    <a href="{{ route('categories.edit', $category->id) }}" 
                                       class="p-1.5 rounded-lg border border-slate-200 text-slate-600 hover:text-indigo-600 hover:border-indigo-100 hover:bg-indigo-50/50 transition-all"
                                       title="Edit Kategori">
                                        <i data-lucide="edit-2" class="w-4 h-4"></i>
                                    </a>
                                    
                                    <!-- Delete Button -->
                                    <form action="{{ route('categories.destroy', $category->id) }}" 
                                          method="POST" 
                                          onsubmit="return confirm('Apakah Anda yakin ingin menghapus kategori {{ $category->name }}? Produk dengan kategori ini akan diubah kategorinya menjadi Tidak Berkategori.');"
                                          class="inline-block">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" 
                                                class="p-1.5 rounded-lg border border-slate-200 text-slate-600 hover:text-rose-600 hover:border-rose-100 hover:bg-rose-50/50 transition-all"
                                                title="Hapus Kategori">
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
                                        <i data-lucide="tags" class="w-8 h-8"></i>
                                    </div>
                                    <div class="text-slate-500 font-medium">Belum ada kategori terdaftar</div>
                                    <div class="text-[11px] text-slate-400">Klik tombol Tambah Kategori di atas untuk mulai membuat kategori baru.</div>
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <!-- Pagination -->
        @if ($categories->hasPages())
            <div class="px-6 py-4 border-t border-slate-100 bg-slate-50/30">
                {{ $categories->links() }}
            </div>
        @endif

    </div>
</div>
@endsection
