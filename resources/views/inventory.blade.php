@extends('layouts.app')

@section('title', 'Inventaris Barang')
@section('active_page', 'inventory')

@section('content')
<div class="space-y-6"
     x-data="inventoryComponent()"
     x-effect="stockFilter; selectedCategory; searchQuery; filteredInventory.length; refreshIcons()">

    <!-- Page Header -->
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
        <div>
            <h2 class="text-2xl font-bold text-slate-800">Manajemen Stok Barang</h2>
            <p class="text-sm text-slate-500">Kelola inventaris barang dagangan, harga beli/jual, dan lihat log keluar-masuk stok.</p>
        </div>
        
        <!-- Action Buttons (Only Super Admin or Manager) -->
        @hasanyrole('Super Admin|Manager')
        <div class="flex items-center gap-2">
            <!-- Add Product Button -->
            <button @click="openAddModal()" 
                    class="bg-gradient-to-r from-indigo-600 to-violet-600 hover:shadow-lg hover:shadow-indigo-600/20 text-white font-bold px-4 py-2.5 rounded-xl text-xs flex items-center justify-center gap-2 transition-all">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4"></path>
                </svg>
                <span>Tambah Barang</span>
            </button>
        </div>
        @endhasanyrole
    </div>

    <!-- Sleek Navigation Tabs -->
    <div class="flex border-b border-slate-200 gap-1.5 overflow-x-auto pb-0.5">
        <button @click="activeTab = 'items'"
                :class="activeTab === 'items' ? 'border-indigo-600 text-indigo-600 font-bold border-b-2' : 'border-transparent text-slate-500 hover:text-slate-700'"
                class="px-4 py-2.5 text-xs font-semibold whitespace-nowrap transition-all duration-150">
            Daftar Inventaris
        </button>
        @hasanyrole('Super Admin|Manager')
        <button @click="activeTab = 'categories'"
                :class="activeTab === 'categories' ? 'border-indigo-600 text-indigo-600 font-bold border-b-2' : 'border-transparent text-slate-500 hover:text-slate-700'"
                class="px-4 py-2.5 text-xs font-semibold whitespace-nowrap transition-all duration-150">
            Kelola Kategori
        </button>
        @endhasanyrole
    </div>
    
    <!-- PANEL A: Daftar Inventaris -->
    <div x-show="activeTab === 'items'" class="space-y-6">
        <!-- Filter Panel -->
        <div class="bg-white p-5 rounded-2xl border border-slate-200/80 shadow-sm flex flex-col md:flex-row gap-4 items-center justify-between">
            <!-- Search bar -->
            <div class="w-full md:w-80 flex items-center gap-2 bg-slate-100 rounded-xl px-3 py-2 text-slate-500 border border-slate-100 focus-within:border-indigo-400 focus-within:bg-white transition-all duration-200">
                <svg class="w-4 h-4 text-slate-400" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                </svg>
                <input type="text" 
                       placeholder="Cari SKU atau nama barang..." 
                       x-model="searchQuery"
                       class="bg-transparent border-none text-xs focus:outline-none w-full text-slate-700">
            </div>
            
            <!-- Category & Stock Alert Filters -->
            <div class="w-full md:w-auto flex flex-col sm:flex-row items-center gap-3">
                <!-- Category Filter dropdown -->
                <div class="w-full sm:w-44 flex items-center bg-slate-50 border border-slate-200 rounded-xl px-2 py-1.5 text-xs text-slate-600">
                    <svg class="w-3.5 h-3.5 text-slate-450 mr-1.5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M3 4a1 1 0 011-1h16a1 1 0 011 1v2.586a1 1 0 01-.293.707l-6.414 6.414a1 1 0 00-.293.707V17l-4 4v-6.586a1 1 0 00-.293-.707L3.293 7.293A1 1 0 013 6.586V4z"></path>
                    </svg>
                    <select x-model="selectedCategory" class="bg-transparent border-none focus:outline-none w-full font-semibold">
                        <option value="all">Semua Kategori</option>
                        <template x-for="cat in categories" :key="cat.id">
                            <option :value="cat.name" x-text="cat.name"></option>
                        </template>
                    </select>
                </div>
                
                <!-- Stock Alert Filter pills / Tab Navigation -->
                <div class="w-full sm:w-auto flex items-center bg-slate-100 p-1 rounded-xl gap-1">
                    <button @click="stockFilter = 'all'"
                            :class="stockFilter === 'all' ? 'bg-white text-slate-800 shadow-sm font-bold' : 'text-slate-500 hover:text-slate-700'"
                            class="px-3 py-1.5 rounded-lg text-[10px] font-semibold transition-all">
                        Semua
                    </button>
                    <button @click="stockFilter = 'normal'"
                            :class="stockFilter === 'normal' ? 'bg-white text-emerald-700 shadow-sm font-bold' : 'text-slate-500 hover:text-emerald-600'"
                            class="px-3 py-1.5 rounded-lg text-[10px] font-semibold transition-all">
                        Stok Aman
                    </button>
                    <button @click="stockFilter = 'low'"
                            :class="stockFilter === 'low' ? 'bg-white text-amber-700 shadow-sm font-bold' : 'text-slate-500 hover:text-amber-600'"
                            class="px-3 py-1.5 rounded-lg text-[10px] font-semibold transition-all">
                        Stok Menipis
                    </button>
                    <button @click="stockFilter = 'out'"
                            :class="stockFilter === 'out' ? 'bg-white text-rose-700 shadow-sm font-bold' : 'text-slate-500 hover:text-rose-600'"
                            class="px-3 py-1.5 rounded-lg text-[10px] font-semibold transition-all">
                        Habis
                    </button>
                </div>
            </div>
        </div>
        
        <!-- Inventory Data Table Container -->
        <div class="bg-white rounded-2xl border border-slate-200/80 shadow-sm overflow-hidden">
            <div class="overflow-x-auto">
                <table class="w-full text-left border-collapse">
                    <thead>
                        <tr class="bg-slate-50/50 border-b border-slate-100 text-[11px] font-bold text-slate-400 uppercase tracking-wider">
                            <th class="px-6 py-4">SKU</th>
                            <th class="px-6 py-4">Nama Barang</th>
                            <th class="px-6 py-4">Kategori</th>
                            <th class="px-6 py-4 text-center">Stok</th>
                            <th class="px-6 py-4 text-right">Harga Beli</th>
                            <th class="px-6 py-4 text-right">Harga Jual</th>
                            <th class="px-6 py-4 text-center">Margin</th>
                            <th class="px-6 py-4 text-center">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100 text-xs">
                        <!-- If Empty filtered -->
                        <template x-if="filteredInventory.length === 0">
                            <tr>
                                <td colspan="8" class="text-center py-12 text-slate-400 font-semibold">
                                    Tidak ada data barang yang sesuai dengan filter.
                                </td>
                            </tr>
                        </template>
                        
                        <!-- Table rows -->
                        <template x-for="item in filteredInventory" :key="item.id">
                            <tr class="hover:bg-slate-50/60 transition-colors">
                                <!-- SKU -->
                                <td class="px-6 py-3.5 font-bold text-slate-800" x-text="item.sku"></td>
                                
                                <!-- Nama Barang -->
                                <td class="px-6 py-3.5">
                                    <span class="font-semibold text-slate-800" x-text="item.name"></span>
                                </td>
                                
                                <!-- Kategori -->
                                <td class="px-6 py-3.5">
                                    <span class="px-2.5 py-1 bg-slate-100 rounded-lg text-slate-600 font-medium" x-text="item.category"></span>
                                </td>
                                
                                <!-- Stok & Badges -->
                                <td class="px-6 py-3.5 text-center">
                                    <div class="flex items-center justify-center gap-1.5">
                                        <span class="font-bold text-slate-800" x-text="item.stock"></span>
                                        
                                        <!-- Dynamic Badge status -->
                                        <span class="inline-flex items-center px-1.5 py-0.5 rounded-full text-[9px] font-extrabold uppercase tracking-wide"
                                              :class="{
                                                 'bg-emerald-50 text-emerald-600': getStockStatus(item) === 'normal',
                                                 'bg-amber-50 text-amber-600': getStockStatus(item) === 'low',
                                                 'bg-rose-50 text-rose-600': getStockStatus(item) === 'out'
                                              }">
                                            <span class="h-1 w-1 rounded-full mr-1"
                                                  :class="{
                                                     'bg-emerald-500': getStockStatus(item) === 'normal',
                                                     'bg-amber-500': getStockStatus(item) === 'low',
                                                     'bg-rose-500': getStockStatus(item) === 'out'
                                                  }"></span>
                                            <span x-text="getStockStatus(item) === 'normal' ? 'Aman' : (getStockStatus(item) === 'low' ? 'Tipis' : 'Habis')"></span>
                                        </span>
                                    </div>
                                </td>
                                
                                <!-- Harga Beli -->
                                <td class="px-6 py-3.5 text-right font-medium text-slate-600" x-text="'Rp ' + new Intl.NumberFormat('id-ID').format(item.purchase_price)"></td>
                                
                                <!-- Harga Jual -->
                                <td class="px-6 py-3.5 text-right font-bold text-slate-800" x-text="'Rp ' + new Intl.NumberFormat('id-ID').format(item.selling_price)"></td>
                                
                                <!-- Profit Margin -->
                                <td class="px-6 py-3.5 text-center font-bold text-emerald-600">
                                    <span x-text="Math.round(((item.selling_price - item.purchase_price) / item.selling_price) * 100) + '%'"></span>
                                </td>
                                
                                <!-- Actions Column -->
                                <td class="px-6 py-3.5 text-center">
                                    <div class="flex items-center justify-center gap-1">
                                        <!-- Mutation history -->
                                        <button @click="openMutationModal(item)"
                                                title="Riwayat Mutasi Stok"
                                                class="p-1.5 text-blue-600 hover:bg-blue-50 rounded-lg transition-colors">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                                                <path stroke-linecap="round" stroke-linejoin="round" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                            </svg>
                                        </button>
                                        
                                        @hasanyrole('Super Admin|Manager')
                                        <!-- Edit -->
                                        <button @click="openEditModal(item)"
                                                title="Edit Barang"
                                                class="p-1.5 text-indigo-600 hover:bg-indigo-50 rounded-lg transition-colors">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                                                <path stroke-linecap="round" stroke-linejoin="round" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
                                            </svg>
                                        </button>
                                        
                                        <!-- Delete -->
                                        <button @click="deleteProduct(item)"
                                                title="Hapus Barang"
                                                class="p-1.5 text-rose-600 hover:bg-rose-50 rounded-lg transition-colors">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                                                <path stroke-linecap="round" stroke-linejoin="round" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                                            </svg>
                                        </button>
                                        @endhasanyrole
                                    </div>
                                </td>
                            </tr>
                        </template>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- PANEL B: Kelola Kategori -->
    @hasanyrole('Super Admin|Manager')
    <div x-show="activeTab === 'categories'" class="space-y-6" style="display: none;">
        <!-- Header Panel Kategori -->
        <div class="bg-white p-5 rounded-2xl border border-slate-200/80 shadow-sm flex items-center justify-between">
            <div>
                <h3 class="font-bold text-slate-800 text-base">Kelola Kategori Barang</h3>
                <p class="text-xs text-slate-400">Atur kategori barang untuk klasifikasi produk pada database dan mesin kasir POS.</p>
            </div>
            <button @click="openAddCategoryModal()" 
                    class="bg-indigo-600 hover:bg-indigo-700 text-white font-bold px-4 py-2.5 rounded-xl text-xs flex items-center gap-2 shadow-md shadow-indigo-600/10 active:scale-[0.98] transition-all">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4"></path>
                </svg>
                <span>Tambah Kategori</span>
            </button>
        </div>

        <!-- Categories Table Card -->
        <div class="bg-white rounded-2xl border border-slate-200/80 shadow-sm overflow-hidden">
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
                        <template x-for="cat in categories" :key="cat.id">
                            <tr class="hover:bg-slate-50/50 transition-colors">
                                <td class="px-6 py-3.5">
                                    <div class="flex items-center gap-3">
                                        <div class="h-8 w-8 rounded-full bg-gradient-to-tr from-violet-500 to-indigo-500 flex items-center justify-center text-white font-bold text-xs shadow-sm">
                                            <span x-text="cat.name.substring(0, 2).toUpperCase()"></span>
                                        </div>
                                        <span class="font-bold text-slate-800" x-text="cat.name"></span>
                                    </div>
                                </td>
                                <td class="px-6 py-3.5 text-slate-550 font-mono text-[11px]" x-text="cat.slug"></td>
                                <td class="px-6 py-3.5">
                                    <span class="px-2.5 py-1 bg-slate-100 text-slate-700 rounded-lg font-semibold text-[10px]" x-text="cat.products_count + ' Produk'"></span>
                                </td>
                                <td class="px-6 py-3.5 text-center">
                                    <div class="flex items-center justify-center gap-2">
                                        <button @click="openEditCategoryModal(cat)" 
                                                class="p-1.5 rounded-lg border border-slate-200 text-slate-650 hover:text-indigo-600 hover:border-indigo-100 hover:bg-indigo-50/50 transition-all">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                                                <path stroke-linecap="round" stroke-linejoin="round" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
                                            </svg>
                                        </button>
                                        <button @click="deleteCategory(cat)" 
                                                class="p-1.5 rounded-lg border border-slate-200 text-slate-650 hover:text-rose-600 hover:border-rose-100 hover:bg-rose-50/50 transition-all">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                                                <path stroke-linecap="round" stroke-linejoin="round" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                                            </svg>
                                        </button>
                                    </div>
                                </td>
                            </tr>
                        </template>
                        <template x-if="categories.length === 0">
                            <tr>
                                <td colspan="4" class="px-6 py-12 text-center text-slate-400">
                                    Belum ada kategori terdaftar.
                                </td>
                            </tr>
                        </template>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
    @endhasanyrole

    <!-- MODAL 1: Tambah Barang -->
    <div x-show="showAddModal" 
         class="fixed inset-0 z-50 overflow-y-auto"
         style="display: none;">
         <div class="fixed inset-0 bg-slate-900/60 backdrop-blur-sm" @click="if(!isSaving) showAddModal = false"></div>
        
        <div class="flex min-h-screen items-center justify-center p-4 relative z-10">
            <div class="bg-white rounded-3xl max-w-md w-full shadow-2xl overflow-hidden border border-slate-100 flex flex-col"
                 x-show="showAddModal"
                 x-transition:enter="transition ease-out duration-300 transform scale-95"
                 x-transition:enter-start="transform scale-95 opacity-0"
                 x-transition:enter-end="transform scale-100 opacity-100">
                
                <div class="px-6 py-5 border-b border-slate-100 flex items-center justify-between bg-slate-50/50">
                    <h3 class="font-bold text-slate-800 text-sm">Tambah Barang Baru</h3>
                    <button @click="showAddModal = false" :disabled="isSaving" class="text-slate-400 hover:text-slate-600">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"></path>
                        </svg>
                    </button>
                </div>
                
                <div class="p-6 space-y-4 max-h-[70vh] overflow-y-auto">
                    <!-- SKU Field -->
                    <div class="space-y-1.5">
                        <label class="text-xs font-bold text-slate-500">Kode SKU</label>
                        <input type="text" x-model="form.sku" class="w-full text-xs bg-slate-100 rounded-xl px-3 py-2.5 border border-slate-100 focus:outline-none focus:border-indigo-500 focus:bg-white font-mono uppercase">
                    </div>
                    
                    <!-- Name Field -->
                    <div class="space-y-1.5">
                        <label class="text-xs font-bold text-slate-500">Nama Barang</label>
                        <input type="text" x-model="form.name" placeholder="Contoh: Beras Rojo Lele 10kg" class="w-full text-xs bg-slate-100 rounded-xl px-3 py-2.5 border border-slate-100 focus:outline-none focus:border-indigo-500 focus:bg-white">
                    </div>
                    
                    <!-- Category Selection -->
                    <div class="space-y-1.5">
                        <label class="text-xs font-bold text-slate-500">Kategori</label>
                        <select x-model="form.category" class="w-full text-xs rounded-xl border border-slate-200 bg-white p-2.5 text-slate-700 focus:outline-none focus:border-indigo-500">
                            <template x-for="cat in categories" :key="cat.id">
                                <option :value="cat.name" x-text="cat.name"></option>
                            </template>
                        </select>
                    </div>
                    
                    <!-- Stock Numbers Grid -->
                    <div class="grid grid-cols-2 gap-4">
                        <div class="space-y-1.5">
                            <label class="text-xs font-bold text-slate-500">Stok Awal</label>
                            <input type="number" x-model="form.stock" class="w-full text-xs bg-slate-100 rounded-xl px-3 py-2.5 border border-slate-100 focus:outline-none focus:border-indigo-500 focus:bg-white">
                        </div>
                        <div class="space-y-1.5">
                            <label class="text-xs font-bold text-slate-500">Batas Stok Minimum</label>
                            <input type="number" x-model="form.min_stock" class="w-full text-xs bg-slate-100 rounded-xl px-3 py-2.5 border border-slate-100 focus:outline-none focus:border-indigo-500 focus:bg-white">
                        </div>
                    </div>
                    
                    <!-- Pricing Grid -->
                    <div class="grid grid-cols-2 gap-4">
                        <div class="space-y-1.5">
                            <label class="text-xs font-bold text-slate-500">Harga Beli</label>
                            <input type="text" :value="formatRupiah(form.purchase_price)" @input="form.purchase_price = parseRupiah($event.target.value)" class="w-full text-xs bg-slate-100 rounded-xl px-3 py-2.5 border border-slate-100 focus:outline-none focus:border-indigo-500 focus:bg-white font-semibold">
                        </div>
                        <div class="space-y-1.5">
                            <label class="text-xs font-bold text-slate-500">Harga Jual</label>
                            <input type="text" :value="formatRupiah(form.selling_price)" @input="form.selling_price = parseRupiah($event.target.value)" class="w-full text-xs bg-slate-100 rounded-xl px-3 py-2.5 border border-slate-100 focus:outline-none focus:border-indigo-500 focus:bg-white font-semibold">
                        </div>
                    </div>
                </div>
                
                <div class="p-6 border-t border-slate-100 bg-slate-50/50 flex gap-3">
                    <button @click="showAddModal = false" :disabled="isSaving" class="flex-1 py-3 border border-slate-200 hover:bg-slate-100 text-slate-600 font-semibold rounded-2xl text-xs transition-colors">
                        Batal
                    </button>
                    <button @click="addProduct()" :disabled="isSaving" class="flex-[2] py-3 bg-indigo-600 hover:bg-indigo-700 text-white font-bold rounded-2xl text-xs flex items-center justify-center gap-2 transition-all">
                        <svg x-show="isSaving" class="animate-spin -ml-1 mr-2 h-4 w-4 text-white" fill="none" viewBox="0 0 24 24">
                            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                        </svg>
                        <span x-text="isSaving ? 'Menyimpan...' : 'Simpan Barang'"></span>
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- MODAL 2: Edit Barang -->
    <div x-show="showEditModal" 
         class="fixed inset-0 z-50 overflow-y-auto"
         style="display: none;">
        <div class="fixed inset-0 bg-slate-900/60 backdrop-blur-sm" @click="if(!isSaving) showEditModal = false"></div>
        
        <div class="flex min-h-screen items-center justify-center p-4 relative z-10">
            <div class="bg-white rounded-3xl max-w-md w-full shadow-2xl overflow-hidden border border-slate-100 flex flex-col"
                 x-show="showEditModal"
                 x-transition:enter="transition ease-out duration-300 transform scale-95"
                 x-transition:enter-start="transform scale-95 opacity-0"
                 x-transition:enter-end="transform scale-100 opacity-100">
                
                <div class="px-6 py-5 border-b border-slate-100 flex items-center justify-between bg-slate-50/50">
                    <div>
                        <h3 class="font-bold text-slate-800 text-sm">Edit Barang</h3>
                        <p class="text-[10px] text-slate-400 mt-0.5" x-text="'SKU: ' + form.sku"></p>
                    </div>
                    <button @click="showEditModal = false" :disabled="isSaving" class="text-slate-400 hover:text-slate-600">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"></path>
                        </svg>
                    </button>
                </div>
                
                <div class="p-6 space-y-4 max-h-[70vh] overflow-y-auto">
                    <!-- Name Field -->
                    <div class="space-y-1.5">
                        <label class="text-xs font-bold text-slate-500">Nama Barang</label>
                        <input type="text" x-model="form.name" class="w-full text-xs bg-slate-100 rounded-xl px-3 py-2.5 border border-slate-100 focus:outline-none focus:border-indigo-500 focus:bg-white font-semibold">
                    </div>
                    
                    <!-- Category Selection -->
                    <div class="space-y-1.5">
                        <label class="text-xs font-bold text-slate-500">Kategori</label>
                        <select x-model="form.category" class="w-full text-xs rounded-xl border border-slate-200 bg-white p-2.5 text-slate-700 focus:outline-none focus:border-indigo-500 font-semibold">
                            <template x-for="cat in categories" :key="cat.id">
                                <option :value="cat.name" x-text="cat.name" :selected="cat.name === form.category"></option>
                            </template>
                        </select>
                    </div>
                    
                    <!-- Stock Numbers Grid -->
                    <div class="grid grid-cols-2 gap-4">
                        <div class="space-y-1.5">
                            <label class="text-xs font-bold text-slate-500">Stok Sekarang</label>
                            <input type="number" x-model="form.stock" class="w-full text-xs bg-slate-100 rounded-xl px-3 py-2.5 border border-slate-100 focus:outline-none focus:border-indigo-500 focus:bg-white font-semibold">
                        </div>
                        <div class="space-y-1.5">
                            <label class="text-xs font-bold text-slate-500">Batas Stok Minimum</label>
                            <input type="number" x-model="form.min_stock" class="w-full text-xs bg-slate-100 rounded-xl px-3 py-2.5 border border-slate-100 focus:outline-none focus:border-indigo-500 focus:bg-white font-semibold">
                        </div>
                    </div>
                    
                    <!-- Pricing Grid -->
                    <div class="grid grid-cols-2 gap-4">
                        <div class="space-y-1.5">
                            <label class="text-xs font-bold text-slate-500">Harga Beli</label>
                            <input type="text" :value="formatRupiah(form.purchase_price)" @input="form.purchase_price = parseRupiah($event.target.value)" class="w-full text-xs bg-slate-100 rounded-xl px-3 py-2.5 border border-slate-100 focus:outline-none focus:border-indigo-500 focus:bg-white font-semibold">
                        </div>
                        <div class="space-y-1.5">
                            <label class="text-xs font-bold text-slate-500">Harga Jual</label>
                            <input type="text" :value="formatRupiah(form.selling_price)" @input="form.selling_price = parseRupiah($event.target.value)" class="w-full text-xs bg-slate-100 rounded-xl px-3 py-2.5 border border-slate-100 focus:outline-none focus:border-indigo-500 focus:bg-white font-semibold">
                        </div>
                    </div>
                </div>
                
                <div class="p-6 border-t border-slate-100 bg-slate-50/50 flex gap-3">
                    <button @click="showEditModal = false" :disabled="isSaving" class="flex-1 py-3 border border-slate-200 hover:bg-slate-100 text-slate-600 font-semibold rounded-2xl text-xs transition-colors">
                        Batal
                    </button>
                    <button @click="editProduct()" :disabled="isSaving" class="flex-[2] py-3 bg-indigo-600 hover:bg-indigo-700 text-white font-bold rounded-2xl text-xs flex items-center justify-center gap-2 transition-all">
                        <svg x-show="isSaving" class="animate-spin -ml-1 mr-2 h-4 w-4 text-white" fill="none" viewBox="0 0 24 24">
                            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                        </svg>
                        <span x-text="isSaving ? 'Menyimpan...' : 'Simpan Perubahan'"></span>
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- MODAL 3: Riwayat Mutasi Stok -->
    <div x-show="showMutationModal" 
         class="fixed inset-0 z-50 overflow-y-auto"
         style="display: none;">
        <div class="fixed inset-0 bg-slate-900/60 backdrop-blur-sm" @click="showMutationModal = false"></div>
        
        <div class="flex min-h-screen items-center justify-center p-4 relative z-10">
            <div class="bg-white rounded-3xl max-w-2xl w-full shadow-2xl overflow-hidden border border-slate-100 flex flex-col"
                 x-show="showMutationModal"
                 x-transition:enter="transition ease-out duration-300 transform scale-95"
                 x-transition:enter-start="transform scale-95 opacity-0"
                 x-transition:enter-end="transform scale-100 opacity-100">
                
                <div class="px-6 py-5 border-b border-slate-100 flex items-center justify-between bg-slate-50/50">
                    <div>
                        <h3 class="font-bold text-slate-800 text-sm">Riwayat Mutasi Stok</h3>
                        <p class="text-[10px] text-slate-500 mt-0.5" x-text="selectedProductForMutation ? selectedProductForMutation.name + ' (' + selectedProductForMutation.sku + ')' : ''"></p>
                    </div>
                    <button @click="showMutationModal = false" class="text-slate-400 hover:text-slate-600">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"></path>
                        </svg>
                    </button>
                </div>
                
                <div class="p-6 max-h-[60vh] overflow-y-auto">
                    <!-- Mutation Timeline -->
                    <div class="space-y-4">
                        <template x-for="mut in mutations.filter(m => !selectedProductForMutation || m.sku === selectedProductForMutation.sku)" :key="mut.date + mut.sku">
                            <div class="flex gap-4 items-start p-3 border border-slate-100 rounded-2xl hover:border-slate-200 transition-colors">
                                <div class="p-2 rounded-xl flex items-center justify-center flex-shrink-0"
                                     :class="mut.type === 'IN' ? 'bg-emerald-50 text-emerald-600' : 'bg-rose-50 text-rose-600'">
                                    <template x-if="mut.type === 'IN'">
                                        <svg class="w-4 h-4 text-emerald-600" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M19 14l-7 7m0 0l-7-7m7 7V3"></path>
                                        </svg>
                                    </template>
                                    <template x-if="mut.type === 'OUT'">
                                        <svg class="w-4 h-4 text-rose-600" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M5 10l7-7m0 0l7 7m-7-7v18"></path>
                                        </svg>
                                    </template>
                                </div>
                                <div class="flex-1 flex flex-col sm:flex-row sm:items-center justify-between gap-2">
                                    <div>
                                        <p class="text-xs font-bold text-slate-800" x-text="mut.type === 'IN' ? 'Stok Masuk (Penambahan)' : 'Stok Keluar (Penjualan/Penyesuaian)'"></p>
                                        <div class="flex items-center gap-2 mt-0.5 text-[10px] text-slate-400 font-medium">
                                            <span x-text="mut.date"></span>
                                            <span>&bull;</span>
                                            <span x-text="'Ref: ' + mut.ref"></span>
                                            <span>&bull;</span>
                                            <span x-text="'Operator: ' + mut.operator"></span>
                                        </div>
                                    </div>
                                    <div class="text-left sm:text-right">
                                        <span class="text-xs font-extrabold" 
                                              :class="mut.type === 'IN' ? 'text-emerald-600' : 'text-rose-600'" 
                                              x-text="(mut.type === 'IN' ? '+' : '-') + mut.qty + ' pcs'"></span>
                                    </div>
                                </div>
                            </div>
                        </template>
                        
                        <!-- Empty timeline case -->
                        <div x-show="mutations.filter(m => !selectedProductForMutation || m.sku === selectedProductForMutation.sku).length === 0" 
                             class="text-center py-8 text-slate-400 font-semibold text-xs">
                            Belum ada riwayat mutasi stok untuk barang ini.
                        </div>
                    </div>
                </div>
                
                <div class="p-6 border-t border-slate-100 bg-slate-50/50 flex">
                    <button @click="showMutationModal = false" class="w-full py-3 bg-slate-200 hover:bg-slate-300 text-slate-700 font-bold rounded-2xl text-xs transition-colors">
                        Tutup
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- MODAL 4: Tambah Kategori -->
    <div x-show="showAddCategoryModal" class="fixed inset-0 z-50 overflow-y-auto" style="display: none;">
        <div class="fixed inset-0 bg-slate-900/60 backdrop-blur-sm" @click="if(!isSaving) showAddCategoryModal = false"></div>
        <div class="flex min-h-screen items-center justify-center p-4 relative z-10">
            <div class="bg-white rounded-3xl max-w-md w-full shadow-2xl overflow-hidden border border-slate-100 flex flex-col"
                 x-show="showAddCategoryModal"
                 x-transition:enter="transition ease-out duration-300 transform scale-95"
                 x-transition:enter-start="transform scale-95 opacity-0"
                 x-transition:enter-end="transform scale-100 opacity-100">
                <div class="px-6 py-5 border-b border-slate-100 flex items-center justify-between bg-slate-50/50">
                    <h3 class="font-bold text-slate-800 text-sm">Tambah Kategori Baru</h3>
                    <button @click="showAddCategoryModal = false" :disabled="isSaving" class="text-slate-400 hover:text-slate-650">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"></path>
                        </svg>
                    </button>
                </div>
                <div class="p-6 space-y-4">
                    <div class="space-y-1.5">
                        <label class="text-xs font-bold text-slate-500">Nama Kategori</label>
                        <input type="text" x-model="categoryForm.name" placeholder="Contoh: Makanan Instan" class="w-full text-xs bg-slate-100 rounded-xl px-3 py-2.5 border border-slate-100 focus:outline-none focus:border-indigo-500 focus:bg-white text-slate-800 font-semibold">
                    </div>
                </div>
                <div class="p-6 border-t border-slate-100 bg-slate-50/50 flex gap-3">
                    <button @click="showAddCategoryModal = false" :disabled="isSaving" class="flex-1 py-3 border border-slate-200 hover:bg-slate-100 text-slate-650 font-semibold rounded-2xl text-xs transition-colors">
                        Batal
                    </button>
                    <button @click="addCategory()" :disabled="isSaving" class="flex-[2] py-3 bg-indigo-600 hover:bg-indigo-700 text-white font-bold rounded-2xl text-xs flex items-center justify-center gap-2 transition-all">
                        <span x-text="isSaving ? 'Menyimpan...' : 'Simpan Kategori'"></span>
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- MODAL 5: Edit Kategori -->
    <div x-show="showEditCategoryModal" class="fixed inset-0 z-50 overflow-y-auto" style="display: none;">
        <div class="fixed inset-0 bg-slate-900/60 backdrop-blur-sm" @click="if(!isSaving) showEditCategoryModal = false"></div>
        <div class="flex min-h-screen items-center justify-center p-4 relative z-10">
            <div class="bg-white rounded-3xl max-w-md w-full shadow-2xl overflow-hidden border border-slate-100 flex flex-col"
                 x-show="showEditCategoryModal"
                 x-transition:enter="transition ease-out duration-300 transform scale-95"
                 x-transition:enter-start="transform scale-95 opacity-0"
                 x-transition:enter-end="transform scale-100 opacity-100">
                <div class="px-6 py-5 border-b border-slate-100 flex items-center justify-between bg-slate-50/50">
                    <h3 class="font-bold text-slate-800 text-sm">Edit Kategori</h3>
                    <button @click="showEditCategoryModal = false" :disabled="isSaving" class="text-slate-400 hover:text-slate-650">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"></path>
                        </svg>
                    </button>
                </div>
                <div class="p-6 space-y-4">
                    <div class="space-y-1.5">
                        <label class="text-xs font-bold text-slate-500">Nama Kategori</label>
                        <input type="text" x-model="categoryForm.name" class="w-full text-xs bg-slate-100 rounded-xl px-3 py-2.5 border border-slate-100 focus:outline-none focus:border-indigo-500 focus:bg-white text-slate-800 font-semibold">
                    </div>
                </div>
                <div class="p-6 border-t border-slate-100 bg-slate-50/50 flex gap-3">
                    <button @click="showEditCategoryModal = false" :disabled="isSaving" class="flex-1 py-3 border border-slate-200 hover:bg-slate-100 text-slate-650 font-semibold rounded-2xl text-xs transition-colors">
                        Batal
                    </button>
                    <button @click="editCategory()" :disabled="isSaving" class="flex-[2] py-3 bg-indigo-600 hover:bg-indigo-700 text-white font-bold rounded-2xl text-xs flex items-center justify-center gap-2 transition-all">
                        <span x-text="isSaving ? 'Menyimpan...' : 'Simpan Perubahan'"></span>
                    </button>
                </div>
            </div>
        </div>
    </div>

</div>

@push('scripts')
<script>
function inventoryComponent() {
    return {
        // Data sources
        inventory: @json($inventory),
        mutations: @json($mutations),
        categories: @json($categories),
        
        // Navigation Tab
        activeTab: 'items', // 'items', 'categories'
        
        init() {
            const urlParams = new URLSearchParams(window.location.search);
            const editSku = urlParams.get('edit_sku');
            if (editSku) {
                this.$nextTick(() => {
                    const item = this.inventory.find(i => i.sku === editSku);
                    if (item) {
                        this.openEditModal(item);
                    }
                });
            }
        },
        
        // Modals State
        showAddModal: false,
        showEditModal: false,
        showMutationModal: false,
        showAddCategoryModal: false,
        showEditCategoryModal: false,
        isSaving: false,
        
        // Filters State
        searchQuery: '',
        selectedCategory: 'all',
        stockFilter: 'all', // 'all', 'normal', 'low', 'out'
        
        // Form Fields
        form: {
            id: null,
            sku: '',
            name: '',
            category: 'Sembako',
            stock: 0,
            min_stock: 5,
            purchase_price: 0,
            selling_price: 0
        },

        categoryForm: {
            id: null,
            name: ''
        },
        
        formatRupiah(num) {
            if (num === null || num === undefined || num === '') return '';
            const numberString = num.toString().replace(/[^0-9]/g, '');
            if (!numberString) return '';
            return 'Rp ' + new Intl.NumberFormat('id-ID', { maximumFractionDigits: 0 }).format(numberString);
        },
        
        parseRupiah(str) {
            if (!str) return 0;
            const cleaned = str.replace(/[^0-9]/g, '');
            return cleaned ? parseInt(cleaned, 10) : 0;
        },
        
        // Target product for mutation history
        selectedProductForMutation: null,
        
        // Filtered Inventory List
        get filteredInventory() {
            return this.inventory.filter(item => {
                const matchesCategory = this.selectedCategory === 'all' || item.category === this.selectedCategory;
                const matchesSearch = item.name.toLowerCase().includes(this.searchQuery.toLowerCase()) || item.sku.toLowerCase().includes(this.searchQuery.toLowerCase());
                
                const stock = Number(item.stock);
                const minStock = Number(item.min_stock);
                let matchesStock = true;
                if (this.stockFilter === 'low') {
                    matchesStock = stock <= minStock && stock > 0;
                } else if (this.stockFilter === 'out') {
                    matchesStock = stock <= 0;
                } else if (this.stockFilter === 'normal') {
                    matchesStock = stock > minStock;
                }
                
                return matchesCategory && matchesSearch && matchesStock;
            });
        },
        
        // Helper to check stock status
        getStockStatus(item) {
            const stock = Number(item.stock);
            const minStock = Number(item.min_stock);
            if (stock <= 0) return 'out';
            if (stock <= minStock) return 'low';
            return 'normal';
        },

        refreshIcons() {
            this.$nextTick(() => {
                if (window.lucide) {
                    window.lucide.createIcons();
                }
            });
        },
        
        // Reset Form Fields
        resetForm() {
            this.form = {
                id: null,
                sku: '',
                name: '',
                category: this.categories[0]?.name || 'Sembako',
                stock: 0,
                min_stock: 5,
                purchase_price: 0,
                selling_price: 0
            };
        },
        
        // Open Add Modal
        openAddModal() {
            this.resetForm();
            // Generate Auto SKU
            const random = Math.floor(1000 + Math.random() * 9000);
            this.form.sku = 'SKU-' + random;
            this.showAddModal = true;
            this.refreshIcons();
        },
        
        // Open Edit Modal
        openEditModal(item) {
            this.form = { ...item };
            this.showEditModal = true;
            this.refreshIcons();
        },
        
        // Open Mutation Modal
        openMutationModal(item) {
            this.selectedProductForMutation = item;
            this.showMutationModal = true;
            this.refreshIcons();
        },
        
        // Save New Product (Axios POST)
        async addProduct() {
            if (!this.form.name.trim() || !this.form.sku.trim()) {
                this.$dispatch('show-toast', { message: 'Nama barang dan SKU wajib diisi!', type: 'danger' });
                return;
            }
            this.isSaving = true;
            
            try {
                const response = await axios.post('/api/inventory/store', this.form);
                const newProduct = response.data.product;
                
                this.inventory.unshift(newProduct);
                
                // Add mutation log for stock initialization
                this.mutations.unshift({
                    date: new Date().toISOString().slice(0, 16).replace('T', ' '),
                    sku: newProduct.sku,
                    name: newProduct.name,
                    type: 'IN',
                    qty: newProduct.stock,
                    ref: 'INIT-STOK',
                    operator: 'Sistem'
                });
                
                // Increment product count on category locally
                const catObj = this.categories.find(c => c.name === newProduct.category);
                if (catObj) {
                    catObj.products_count = (catObj.products_count || 0) + 1;
                }
                
                this.$dispatch('show-toast', { message: 'Barang ' + newProduct.name + ' berhasil ditambahkan!', type: 'success' });
                this.showAddModal = false;
                this.resetForm();
            } catch (error) {
                console.error(error);
                this.$dispatch('show-toast', { message: 'Gagal menambahkan barang.', type: 'danger' });
            } finally {
                this.isSaving = false;
            }
        },
        
        // Edit Product (Axios PUT)
        async editProduct() {
            if (!this.form.name.trim()) {
                this.$dispatch('show-toast', { message: 'Nama barang wajib diisi!', type: 'danger' });
                return;
            }
            this.isSaving = true;
            
            try {
                const response = await axios.put('/api/inventory/update/' + this.form.id, this.form);
                const index = this.inventory.findIndex(item => item.id === this.form.id);
                if (index > -1) {
                    const updatedProduct = response.data.product;
                    const oldProduct = this.inventory[index];
                    
                    // Track stock difference for mutation history if changed
                    const oldStock = oldProduct.stock;
                    const newStock = updatedProduct.stock;
                    const diff = newStock - oldStock;
                    
                    if (diff !== 0) {
                        this.mutations.unshift({
                            date: new Date().toISOString().slice(0, 16).replace('T', ' '),
                            sku: this.form.sku,
                            name: this.form.name,
                            type: diff > 0 ? 'IN' : 'OUT',
                            qty: Math.abs(diff),
                            ref: 'PENYESUAIAN',
                            operator: 'Sistem'
                        });
                    }

                    // Update local categories count if category changed
                    if (oldProduct.category !== updatedProduct.category) {
                        const oldCat = this.categories.find(c => c.name === oldProduct.category);
                        if (oldCat) oldCat.products_count = Math.max(0, (oldCat.products_count || 0) - 1);
                        
                        const newCat = this.categories.find(c => c.name === updatedProduct.category);
                        if (newCat) newCat.products_count = (newCat.products_count || 0) + 1;
                    }
                    
                    this.inventory[index] = updatedProduct;
                }
                
                this.$dispatch('show-toast', { message: 'Barang ' + this.form.name + ' berhasil diperbarui!', type: 'success' });
                this.showEditModal = false;
                this.resetForm();
            } catch (error) {
                console.error(error);
                this.$dispatch('show-toast', { message: 'Gagal memperbarui barang.', type: 'danger' });
            } finally {
                this.isSaving = false;
            }
        },
        
        // Delete Product (Axios DELETE)
        async deleteProduct(item) {
            if (confirm('Apakah Anda yakin ingin menghapus barang ' + item.name + '?')) {
                try {
                    const response = await axios.delete('/api/inventory/delete/' + item.id);
                    
                    // Decrement category count
                    const catObj = this.categories.find(c => c.name === item.category);
                    if (catObj) {
                        catObj.products_count = Math.max(0, (catObj.products_count || 0) - 1);
                    }

                    this.inventory = this.inventory.filter(prod => prod.id !== item.id);
                    this.$dispatch('show-toast', { message: 'Barang ' + item.name + ' telah dihapus!', type: 'danger' });
                } catch (error) {
                    console.error(error);
                    this.$dispatch('show-toast', { message: 'Gagal menghapus barang.', type: 'danger' });
                }
            }
        },

        // Category CRUD API Helpers
        openAddCategoryModal() {
            this.categoryForm = { id: null, name: '' };
            this.showAddCategoryModal = true;
            this.refreshIcons();
        },

        openEditCategoryModal(cat) {
            this.categoryForm = { id: cat.id, name: cat.name };
            this.showEditCategoryModal = true;
            this.refreshIcons();
        },

        async addCategory() {
            if (!this.categoryForm.name.trim()) {
                this.$dispatch('show-toast', { message: 'Nama kategori wajib diisi!', type: 'danger' });
                return;
            }
            this.isSaving = true;
            try {
                const response = await axios.post('/api/categories/store', this.categoryForm);
                const newCat = response.data.category;
                this.categories.push(newCat);
                this.$dispatch('show-toast', { message: 'Kategori ' + newCat.name + ' berhasil dibuat!', type: 'success' });
                this.showAddCategoryModal = false;
            } catch (error) {
                console.error(error);
                this.$dispatch('show-toast', { message: 'Gagal membuat kategori baru.', type: 'danger' });
            } finally {
                this.isSaving = false;
            }
        },

        async editCategory() {
            if (!this.categoryForm.name.trim()) {
                this.$dispatch('show-toast', { message: 'Nama kategori wajib diisi!', type: 'danger' });
                return;
            }
            this.isSaving = true;
            try {
                const response = await axios.put('/api/categories/update/' + this.categoryForm.id, this.categoryForm);
                const updatedCat = response.data.category;
                const index = this.categories.findIndex(c => c.id === updatedCat.id);
                if (index > -1) {
                    const oldName = this.categories[index].name;
                    this.categories[index] = updatedCat;
                    // Update category name inside products in memory as well
                    this.inventory.forEach(prod => {
                        if (prod.category === oldName) {
                            prod.category = updatedCat.name;
                        }
                    });
                }
                this.$dispatch('show-toast', { message: 'Kategori berhasil diperbarui!', type: 'success' });
                this.showEditCategoryModal = false;
            } catch (error) {
                console.error(error);
                this.$dispatch('show-toast', { message: 'Gagal memperbarui kategori.', type: 'danger' });
            } finally {
                this.isSaving = false;
            }
        },

        async deleteCategory(cat) {
            if (confirm('Apakah Anda yakin ingin menghapus kategori ' + cat.name + '? Semua produk di kategori ini akan berubah menjadi Tidak Berkategori.')) {
                try {
                    await axios.delete('/api/categories/delete/' + cat.id);
                    
                    // Reset products category in memory
                    this.inventory.forEach(prod => {
                        if (prod.category === cat.name) {
                            prod.category = 'Umum';
                        }
                    });

                    this.categories = this.categories.filter(c => c.id !== cat.id);
                    
                    // Trigger recount of "Umum" if exists
                    const umumCat = this.categories.find(c => c.name === 'Umum');
                    if (umumCat) {
                        umumCat.products_count = (umumCat.products_count || 0) + (cat.products_count || 0);
                    }

                    this.$dispatch('show-toast', { message: 'Kategori ' + cat.name + ' telah dihapus!', type: 'danger' });
                } catch (error) {
                    console.error(error);
                    this.$dispatch('show-toast', { message: 'Gagal menghapus kategori.', type: 'danger' });
                }
            }
        }
    };
}
</script>
@endpush
@endsection
