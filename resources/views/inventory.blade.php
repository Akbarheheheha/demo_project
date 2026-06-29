@extends('layouts.app')

@section('title', 'Inventaris Barang')
@section('active_page', 'inventory')

@section('content')
<div class="space-y-6" x-data="inventoryComponent()">

    <!-- Page Header -->
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
        <div>
            <h2 class="text-2xl font-bold text-slate-800">Manajemen Stok Barang</h2>
            <p class="text-sm text-slate-500">Kelola inventaris barang dagangan, harga beli/jual, dan lihat log keluar-masuk stok.</p>
        </div>
        
        <!-- Tambah Barang Button -->
        <button @click="openAddModal()" 
                class="bg-gradient-to-r from-indigo-600 to-violet-600 hover:shadow-lg hover:shadow-indigo-600/20 text-white font-bold px-4 py-2.5 rounded-xl text-xs flex items-center justify-center gap-2 transition-all">
            <i data-lucide="plus" class="w-4 h-4"></i>
            <span>Tambah Barang</span>
        </button>
    </div>
    
    <!-- Filter Panel -->
    <div class="bg-white p-5 rounded-2xl border border-slate-200/80 shadow-sm flex flex-col md:flex-row gap-4 items-center justify-between">
        <!-- Search bar -->
        <div class="w-full md:w-80 flex items-center gap-2 bg-slate-100 rounded-xl px-3 py-2 text-slate-500 border border-slate-100 focus-within:border-indigo-400 focus-within:bg-white transition-all duration-200">
            <i data-lucide="search" class="w-4 h-4"></i>
            <input type="text" 
                   placeholder="Cari SKU atau nama barang..." 
                   x-model="searchQuery"
                   class="bg-transparent border-none text-xs focus:outline-none w-full text-slate-700">
        </div>
        
        <!-- Category & Stock Alert Filters -->
        <div class="w-full md:w-auto flex flex-col sm:flex-row items-center gap-3">
            <!-- Category Filter dropdown -->
            <div class="w-full sm:w-44 flex items-center bg-slate-50 border border-slate-200 rounded-xl px-2 py-1.5 text-xs text-slate-600">
                <i data-lucide="filter" class="w-3.5 h-3.5 text-slate-400 mr-1.5"></i>
                <select x-model="selectedCategory" class="bg-transparent border-none focus:outline-none w-full font-semibold">
                    <option value="all">Semua Kategori</option>
                    <template x-for="cat in categories" :key="cat">
                        <option :value="cat" x-text="cat"></option>
                    </template>
                </select>
            </div>
            
            <!-- Stock Alert Filter pills -->
            <div class="w-full sm:w-auto flex items-center bg-slate-100 p-1 rounded-xl gap-1">
                <button @click="stockFilter = 'all'"
                        :class="stockFilter === 'all' ? 'bg-white text-slate-800 shadow-sm font-bold' : 'text-slate-500 hover:text-slate-700'"
                        class="px-3 py-1 rounded-lg text-[10px] font-semibold transition-all">
                    Semua
                </button>
                <button @click="stockFilter = 'normal'"
                        :class="stockFilter === 'normal' ? 'bg-white text-emerald-700 shadow-sm font-bold' : 'text-slate-500 hover:text-emerald-600'"
                        class="px-3 py-1 rounded-lg text-[10px] font-semibold transition-all">
                    Stok Aman
                </button>
                <button @click="stockFilter = 'low'"
                        :class="stockFilter === 'low' ? 'bg-white text-amber-700 shadow-sm font-bold' : 'text-slate-500 hover:text-amber-600'"
                        class="px-3 py-1 rounded-lg text-[10px] font-semibold transition-all">
                    Menipis
                </button>
                <button @click="stockFilter = 'out'"
                        :class="stockFilter === 'out' ? 'bg-white text-rose-700 shadow-sm font-bold' : 'text-slate-500 hover:text-rose-600'"
                        class="px-3 py-1 rounded-lg text-[10px] font-semibold transition-all">
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
                                    <span class="font-bold" x-text="item.stock"></span>
                                    
                                    <!-- Dynamic Badge status based on Stock Status -->
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
                            
                            <!-- Profit Margin (calculated dynamically) -->
                            <td class="px-6 py-3.5 text-center font-bold text-emerald-600">
                                <span x-text="Math.round(((item.selling_price - item.purchase_price) / item.selling_price) * 100) + '%'"></span>
                            </td>
                            
                            <!-- Actions -->
                            <td class="px-6 py-3.5 text-center">
                                <div class="flex items-center justify-center gap-1">
                                    <!-- Mutation history -->
                                    <button @click="openMutationModal(item)"
                                            title="Riwayat Mutasi Stok"
                                            class="p-1.5 text-blue-600 hover:bg-blue-50 rounded-lg transition-colors">
                                        <i data-lucide="history" class="w-4 h-4"></i>
                                    </button>
                                    
                                    <!-- Edit -->
                                    <button @click="openEditModal(item)"
                                            title="Edit Barang"
                                            class="p-1.5 text-indigo-600 hover:bg-indigo-50 rounded-lg transition-colors">
                                        <i data-lucide="edit-3" class="w-4 h-4"></i>
                                    </button>
                                    
                                    <!-- Delete -->
                                    <button @click="deleteProduct(item)"
                                            title="Hapus Barang"
                                            class="p-1.5 text-rose-600 hover:bg-rose-50 rounded-lg transition-colors">
                                        <i data-lucide="trash-2" class="w-4 h-4"></i>
                                    </button>
                                </div>
                            </td>
                        </tr>
                    </template>
                </tbody>
            </table>
        </div>
    </div>

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
                        <i data-lucide="x" class="w-5 h-5"></i>
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
                            <template x-for="cat in categories" :key="cat">
                                <option :value="cat" x-text="cat"></option>
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
                            <label class="text-xs font-bold text-slate-500">Harga Beli (Rp)</label>
                            <input type="number" x-model="form.purchase_price" class="w-full text-xs bg-slate-100 rounded-xl px-3 py-2.5 border border-slate-100 focus:outline-none focus:border-indigo-500 focus:bg-white font-semibold">
                        </div>
                        <div class="space-y-1.5">
                            <label class="text-xs font-bold text-slate-500">Harga Jual (Rp)</label>
                            <input type="number" x-model="form.selling_price" class="w-full text-xs bg-slate-100 rounded-xl px-3 py-2.5 border border-slate-100 focus:outline-none focus:border-indigo-500 focus:bg-white font-semibold">
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
                        <i data-lucide="x" class="w-5 h-5"></i>
                    </button>
                </div>
                
                <div class="p-6 space-y-4 max-h-[70vh] overflow-y-auto">
                    <!-- Name Field -->
                    <div class="space-y-1.5">
                        <label class="text-xs font-bold text-slate-500">Nama Barang</label>
                        <input type="text" x-model="form.name" class="w-full text-xs bg-slate-100 rounded-xl px-3 py-2.5 border border-slate-100 focus:outline-none focus:border-indigo-500 focus:bg-white">
                    </div>
                    
                    <!-- Category Selection -->
                    <div class="space-y-1.5">
                        <label class="text-xs font-bold text-slate-500">Kategori</label>
                        <select x-model="form.category" class="w-full text-xs rounded-xl border border-slate-200 bg-white p-2.5 text-slate-700 focus:outline-none focus:border-indigo-500">
                            <template x-for="cat in categories" :key="cat">
                                <option :value="cat" x-text="cat" :selected="cat === form.category"></option>
                            </template>
                        </select>
                    </div>
                    
                    <!-- Stock Numbers Grid -->
                    <div class="grid grid-cols-2 gap-4">
                        <div class="space-y-1.5">
                            <label class="text-xs font-bold text-slate-500">Stok Sekarang</label>
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
                            <label class="text-xs font-bold text-slate-500">Harga Beli (Rp)</label>
                            <input type="number" x-model="form.purchase_price" class="w-full text-xs bg-slate-100 rounded-xl px-3 py-2.5 border border-slate-100 focus:outline-none focus:border-indigo-500 focus:bg-white font-semibold">
                        </div>
                        <div class="space-y-1.5">
                            <label class="text-xs font-bold text-slate-500">Harga Jual (Rp)</label>
                            <input type="number" x-model="form.selling_price" class="w-full text-xs bg-slate-100 rounded-xl px-3 py-2.5 border border-slate-100 focus:outline-none focus:border-indigo-500 focus:bg-white font-semibold">
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
                        <i data-lucide="x" class="w-5 h-5"></i>
                    </button>
                </div>
                
                <div class="p-6 max-h-[60vh] overflow-y-auto">
                    <!-- Mutation Timeline / List -->
                    <div class="space-y-4">
                        
                        <!-- Filter mutations for current selected product -->
                        <template x-for="mut in mutations.filter(m => !selectedProductForMutation || m.sku === selectedProductForMutation.sku)" :key="mut.date + mut.sku">
                            <div class="flex gap-4 items-start p-3 border border-slate-100 rounded-2xl hover:border-slate-200 transition-colors">
                                <div class="p-2 rounded-xl flex items-center justify-center flex-shrink-0"
                                     :class="mut.type === 'IN' ? 'bg-emerald-50 text-emerald-600' : 'bg-rose-50 text-rose-600'">
                                    <!-- IN or OUT icon -->
                                    <template x-if="mut.type === 'IN'">
                                        <i data-lucide="arrow-down-left" class="w-4 h-4"></i>
                                    </template>
                                    <template x-if="mut.type === 'OUT'">
                                        <i data-lucide="arrow-up-right" class="w-4 h-4"></i>
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

</div>

<script>
function inventoryComponent() {
    return {
        inventory: @json($inventory),
        mutations: @json($mutations),
        categories: @json($categories),
        
        // Modals State
        showAddModal: false,
        showEditModal: false,
        showMutationModal: false,
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
        
        // Target product for mutation history
        selectedProductForMutation: null,
        
        // Filtered Inventory List
        get filteredInventory() {
            return this.inventory.filter(item => {
                const matchesCategory = this.selectedCategory === 'all' || item.category === this.selectedCategory;
                const matchesSearch = item.name.toLowerCase().includes(this.searchQuery.toLowerCase()) || item.sku.toLowerCase().includes(this.searchQuery.toLowerCase());
                
                let matchesStock = true;
                if (this.stockFilter === 'low') {
                    matchesStock = item.stock <= item.min_stock && item.stock > 0;
                } else if (this.stockFilter === 'out') {
                    matchesStock = item.stock === 0;
                } else if (this.stockFilter === 'normal') {
                    matchesStock = item.stock > item.min_stock;
                }
                
                return matchesCategory && matchesSearch && matchesStock;
            });
        },
        
        // Helper to check stock status
        getStockStatus(item) {
            if (item.stock === 0) return 'out';
            if (item.stock <= item.min_stock) return 'low';
            return 'normal';
        },
        
        // Reset Form Fields
        resetForm() {
            this.form = {
                id: null,
                sku: '',
                name: '',
                category: this.categories[0] || 'Sembako',
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
        },
        
        // Open Edit Modal
        openEditModal(item) {
            this.form = { ...item };
            this.showEditModal = true;
        },
        
        // Open Mutation Modal
        openMutationModal(item) {
            this.selectedProductForMutation = item;
            this.showMutationModal = true;
            setTimeout(() => lucide.createIcons(), 50);
        },
        
        // Save New Product (Axios Mock)
        async addProduct() {
            if (!this.form.name.trim() || !this.form.sku.trim()) {
                this.$dispatch('show-toast', { message: 'Nama barang dan SKU wajib diisi!', type: 'danger' });
                return;
            }
            this.isSaving = true;
            
            try {
                // Axios POST
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
                
                this.$dispatch('show-toast', { message: 'Barang ' + newProduct.name + ' berhasil ditambahkan!', type: 'success' });
                this.showAddModal = false;
                this.resetForm();

                setTimeout(() => {
                    if (window.lucide) {
                        window.lucide.createIcons();
                    }
                }, 50);
            } catch (error) {
                console.error(error);
                this.$dispatch('show-toast', { message: 'Gagal menambahkan barang.', type: 'danger' });
            } finally {
                this.isSaving = false;
            }
        },
        
        // Edit Product
        async editProduct() {
            if (!this.form.name.trim()) {
                this.$dispatch('show-toast', { message: 'Nama barang wajib diisi!', type: 'danger' });
                return;
            }
            this.isSaving = true;
            
            try {
                // Axios PUT
                const response = await axios.put('/api/inventory/update/' + this.form.id, this.form);
                
                const index = this.inventory.findIndex(item => item.id === this.form.id);
                if (index > -1) {
                    const updatedProduct = response.data.product;
                    
                    // Track stock difference for mutation history if changed
                    const oldStock = this.inventory[index].stock;
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
        
        // Delete Product (Axios Mock)
        async deleteProduct(item) {
            if (confirm('Apakah Anda yakin ingin menghapus barang ' + item.name + '?')) {
                try {
                    // Axios DELETE Mock
                    const response = await axios.delete('/api/inventory/delete/' + item.id);
                    
                    this.inventory = this.inventory.filter(prod => prod.id !== item.id);
                    this.$dispatch('show-toast', { message: 'Barang ' + item.name + ' telah dihapus!', type: 'danger' });
                } catch (error) {
                    console.error(error);
                    this.$dispatch('show-toast', { message: 'Gagal menghapus barang.', type: 'danger' });
                }
            }
        }
    };
}
</script>
@endsection
