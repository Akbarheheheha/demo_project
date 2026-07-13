@extends('layouts.app')

@section('title', 'Kasir POS')
@section('active_page', 'pos')

@section('content')
<div class="h-[calc(100vh-8.5rem)] flex flex-col gap-6" x-data="posComponent()">
     
    <!-- Split Layout Grid -->
    <div class="flex-1 grid grid-cols-1 lg:grid-cols-12 gap-6 min-h-0">
        
        <!-- Left Column: Products Listing (8 Cols) -->
        <div class="lg:col-span-7 xl:col-span-8 flex flex-col gap-4 min-h-0 bg-white p-5 rounded-2xl border border-slate-200/80 shadow-sm">
            
            <!-- Filters Section -->
            <div class="flex flex-col sm:flex-row sm:items-center gap-3">
                <!-- Search bar input -->
                <div class="flex-1 flex items-center gap-2 bg-slate-100 rounded-xl px-3 py-2 text-slate-500 border border-slate-100 focus-within:border-indigo-400 focus-within:bg-white focus-within:ring-2 focus-within:ring-indigo-100 transition-all duration-200">
                    <i data-lucide="search" class="w-4 h-4"></i>
                    <input type="text" 
                           id="search-product"
                           placeholder="Cari SKU atau Nama Produk..." 
                           x-model="searchQuery"
                           class="bg-transparent border-none text-xs focus:outline-none w-full text-slate-700">
                    <!-- Clear search query -->
                    <button x-show="searchQuery !== ''" @click="searchQuery = ''" class="text-slate-400 hover:text-slate-600">
                        <i data-lucide="x" class="w-4 h-4"></i>
                    </button>
                </div>
                
                <!-- Category dropdown for mobile -->
                <div class="sm:hidden">
                    <select x-model="selectedCategory" class="w-full text-xs font-semibold rounded-xl border border-slate-200 bg-white p-2.5 text-slate-700 focus:outline-none focus:border-indigo-500">
                        <template x-for="cat in categories" :key="cat.id">
                            <option :value="cat.id" x-text="cat.name"></option>
                        </template>
                    </select>
                </div>
            </div>
            
            <!-- Categories Horizontal Pills (Desktop) -->
            <div class="hidden sm:flex gap-1.5 overflow-x-auto pb-1 flex-shrink-0">
                <template x-for="cat in categories" :key="cat.id">
                    <button @click="selectedCategory = cat.id"
                            :class="selectedCategory === cat.id ? 'bg-indigo-600 text-white shadow-sm shadow-indigo-600/20' : 'bg-slate-100 text-slate-600 hover:bg-slate-200/70'"
                            class="px-4 py-2 rounded-xl text-xs font-bold transition-all duration-200 whitespace-nowrap"
                            x-text="cat.name">
                    </button>
                </template>
            </div>
            
            <!-- Products Grid Container -->
            <div class="flex-1 overflow-y-auto min-h-0 pr-1">
                <!-- If search result is empty -->
                <div x-show="filteredProducts.length === 0" class="flex flex-col items-center justify-center h-full text-center py-10">
                    <div class="p-4 bg-slate-50 text-slate-400 rounded-2xl mb-3">
                        <i data-lucide="package-search" class="w-10 h-10"></i>
                    </div>
                    <h4 class="font-bold text-slate-700 text-sm">Produk Tidak Ditemukan</h4>
                    <p class="text-xs text-slate-400 mt-1">Coba cari dengan kata kunci lain atau pilih kategori yang berbeda.</p>
                </div>
                
                <!-- Products Grid -->
                <div x-show="filteredProducts.length > 0" class="grid grid-cols-2 sm:grid-cols-3 xl:grid-cols-4 gap-4">
                    <template x-for="prod in filteredProducts" :key="prod.id">
                        <div @click="addToCart(prod)"
                             class="group cursor-pointer bg-white p-3.5 rounded-2xl border border-slate-200/80 shadow-sm hover:shadow-md hover:border-indigo-400 transition-all duration-200 flex flex-col relative overflow-hidden select-none">
                            
                            <!-- Category Color Stripe overlay -->
                            <div class="absolute top-0 inset-x-0 h-1"
                                 :class="{
                                    'bg-emerald-500': prod.color === 'emerald',
                                    'bg-amber-500': prod.color === 'amber',
                                    'bg-sky-500': prod.color === 'sky',
                                    'bg-purple-500': prod.color === 'purple',
                                    'bg-pink-500': prod.color === 'pink'
                                 }"></div>
                            
                            <!-- Thumbnail / Icon Representation -->
                            <div class="h-24 w-full rounded-xl flex items-center justify-center mb-3 relative overflow-hidden"
                                 :class="{
                                    'bg-emerald-50 text-emerald-600': prod.color === 'emerald',
                                    'bg-amber-50 text-amber-600': prod.color === 'amber',
                                    'bg-sky-50 text-sky-600': prod.color === 'sky',
                                    'bg-purple-50 text-purple-600': prod.color === 'purple',
                                    'bg-pink-50 text-pink-600': prod.color === 'pink'
                                 }">
                                
                                <!-- Icon representation (standardizing size/look) -->
                                <div class="p-3 bg-white/70 backdrop-blur-sm rounded-2xl shadow-sm border border-slate-100 group-hover:scale-110 transition-transform duration-200">
                                    <template x-if="prod.icon === 'shopping-bag'">
                                        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="w-6 h-6 text-indigo-650">
                                            <path d="M6 2L3 6v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2V6l-3-4z"></path>
                                            <line x1="3" y1="6" x2="21" y2="6"></line>
                                            <path d="M16 10a4 4 0 0 1-8 0"></path>
                                        </svg>
                                    </template>
                                    <template x-if="prod.icon === 'droplet'">
                                        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="w-6 h-6 text-sky-600">
                                            <path d="M12 2.69l5.66 5.66a8 8 0 1 1-11.31 0z"></path>
                                        </svg>
                                    </template>
                                    <template x-if="prod.icon === 'database'">
                                        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="w-6 h-6 text-slate-600">
                                            <ellipse cx="12" cy="5" rx="9" ry="3"></ellipse>
                                            <path d="M3 5v14c0 1.66 4 3 9 3s9-1.34 9-3V5"></path>
                                            <path d="M3 12c0 1.66 4 3 9 3s9-1.34 9-3"></path>
                                        </svg>
                                    </template>
                                    <template x-if="prod.icon === 'utensils'">
                                        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="w-6 h-6 text-amber-600">
                                            <path d="M3 2v7c0 1.1.9 2 2 2h4a2 2 0 0 0 2-2V2"></path>
                                            <path d="M7 2v20"></path>
                                            <path d="M21 15V2v0a5 5 0 0 0-5 5v6c0 1.1.9 2 2 2h3H21z"></path>
                                            <path d="M19 15v7"></path>
                                        </svg>
                                    </template>
                                    <template x-if="prod.icon === 'cup-tass'">
                                        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="w-6 h-6 text-rose-600">
                                            <path d="M18 8h1a4 4 0 0 1 0 8h-1"></path>
                                            <path d="M2 8h16v9a4 4 0 0 1-4 4H6a4 4 0 0 1-4-4V8z"></path>
                                            <line x1="6" y1="2" x2="6" y2="4"></line>
                                            <line x1="10" y1="2" x2="10" y2="4"></line>
                                            <line x1="14" y1="2" x2="14" y2="4"></line>
                                        </svg>
                                    </template>
                                    <template x-if="prod.icon === 'candy'">
                                        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="w-6 h-6 text-pink-650">
                                            <circle cx="12" cy="12" r="6"></circle>
                                            <path d="M12 2v4M12 18v4M4.93 4.93l2.83 2.83M16.24 16.24l2.83 2.83M2 12h4M18 12h4M4.93 19.07l2.83-2.83M16.24 7.76l2.83-2.83"></path>
                                        </svg>
                                    </template>
                                    <template x-if="prod.icon === 'sparkles'">
                                        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="w-6 h-6 text-purple-600">
                                            <path d="M12 3v3m0 12v3m9-9h-3M6 12H3m14.07-7.07l-2.12 2.12M8.05 15.95l-2.12 2.12M15.95 15.95l2.12 2.12M8.05 8.05L5.93 5.93"></path>
                                        </svg>
                                    </template>
                                </div>
                                
                                <!-- SKU floating badge -->
                                <span class="absolute bottom-1.5 left-1.5 text-[9px] font-bold bg-slate-900/60 backdrop-blur-sm text-white px-2 py-0.5 rounded-lg" x-text="prod.sku"></span>
                                
                                <!-- Stock floating warning if low -->
                                <template x-if="prod.stock <= 10 && prod.stock > 0">
                                    <span class="absolute top-2.5 right-2 flex h-2 w-2">
                                        <span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-amber-400 opacity-75"></span>
                                        <span class="relative inline-flex rounded-full h-2 w-2 bg-amber-500"></span>
                                    </span>
                                </template>
                                <template x-if="prod.stock == 0">
                                    <span class="absolute inset-0 bg-slate-900/60 backdrop-blur-sm flex items-center justify-center text-xs font-bold text-white uppercase tracking-wider">Habis</span>
                                </template>
                            </div>
                            
                            <!-- Product Details -->
                            <div class="flex-1 flex flex-col justify-between">
                                <div>
                                    <h4 class="text-xs font-bold text-slate-800 line-clamp-2" x-text="prod.name"></h4>
                                </div>
                                <div class="mt-2.5 flex items-center justify-between">
                                    <div>
                                        <p class="text-xs font-black text-indigo-600" x-text="'Rp ' + new Intl.NumberFormat('id-ID').format(prod.price)"></p>
                                        <p class="text-[10px] text-slate-400 mt-0.5" x-text="'Stok: ' + prod.stock"></p>
                                    </div>
                                    <div class="h-7 w-7 rounded-xl bg-slate-100 group-hover:bg-indigo-600 group-hover:text-white flex items-center justify-center text-slate-500 transition-colors duration-200">
                                        <i data-lucide="plus" class="w-4 h-4"></i>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </template>
                </div>
            </div>
            
        </div>

        <!-- Right Column: Shopping Cart (4 Cols) -->
        <div class="lg:col-span-5 xl:col-span-4 bg-white rounded-2xl border border-slate-200/80 shadow-sm flex flex-col overflow-hidden">
            
            <!-- Cart Title -->
            <div class="p-5 border-b border-slate-100 flex items-center justify-between bg-slate-50/50 flex-shrink-0">
                <div class="flex items-center gap-2">
                    <i data-lucide="receipt" class="w-5 h-5 text-indigo-600"></i>
                    <h3 class="font-bold text-slate-800 text-sm">Struk Belanja</h3>
                </div>
                <button @click="cart = []" 
                        x-show="cart.length > 0" 
                        class="text-xs font-bold text-rose-600 hover:text-rose-800 transition-colors">
                    Kosongkan
                </button>
            </div>
            
            <!-- Cart Items List (Scrollable) -->
            <div class="flex-1 overflow-y-auto p-5 min-h-0">
                <!-- Empty Cart State -->
                <div x-show="cart.length === 0" class="flex flex-col items-center justify-center h-full text-center py-8">
                    <div class="p-4 bg-slate-50 text-slate-400 rounded-2xl mb-3">
                        <i data-lucide="shopping-basket" class="w-10 h-10"></i>
                    </div>
                    <h4 class="font-bold text-slate-600 text-xs">Keranjang Kosong</h4>
                    <p class="text-[10px] text-slate-400 mt-1 max-w-[180px]">Klik produk di sebelah kiri untuk menambah pesanan.</p>
                </div>
                
                <!-- Cart Items -->
                <div x-show="cart.length > 0" class="space-y-3.5">
                    <template x-for="item in cart" :key="item.product.id">
                        <div class="flex items-start justify-between gap-3 p-3 rounded-xl border border-slate-100 hover:border-slate-200 transition-all duration-200">
                            <div class="flex-1">
                                <h4 class="text-xs font-bold text-slate-800" x-text="item.product.name"></h4>
                                <span class="text-[10px] text-slate-400" x-text="item.product.sku"></span>
                                <div class="mt-1">
                                    <span class="text-xs font-bold text-indigo-600" x-text="'Rp ' + new Intl.NumberFormat('id-ID').format(item.product.price)"></span>
                                </div>
                            </div>
                            
                            <div class="flex flex-col items-end gap-2.5">
                                <!-- Delete Item -->
                                <button @click="removeFromCart(item.product.id)" class="text-slate-400 hover:text-rose-600 transition-colors">
                                    <i data-lucide="trash-2" class="w-4 h-4"></i>
                                </button>
                                
                                <!-- Qty controls -->
                                <div class="flex items-center gap-2 bg-slate-100 rounded-lg p-0.5 border border-slate-200/50">
                                    <button @click="updateQty(item.product.id, -1)" class="h-6 w-6 rounded-md bg-white border border-slate-200 hover:bg-slate-50 flex items-center justify-center text-slate-600 font-bold transition-all">
                                        -
                                    </button>
                                    <span class="text-xs font-bold text-slate-800 w-6 text-center" x-text="item.qty"></span>
                                    <button @click="updateQty(item.product.id, 1)" class="h-6 w-6 rounded-md bg-white border border-slate-200 hover:bg-slate-50 flex items-center justify-center text-slate-600 font-bold transition-all">
                                        +
                                    </button>
                                </div>
                            </div>
                        </div>
                    </template>
                </div>
            </div>
            
            <!-- Calculations Box (Sticky bottom) -->
            <div class="p-5 border-t border-slate-100 bg-slate-50/50 flex-shrink-0 space-y-4">
                
                <!-- Calculation values -->
                <div class="space-y-2 text-xs">
                    <div class="flex justify-between text-slate-500">
                        <span>Subtotal</span>
                        <span class="font-bold text-slate-700" x-text="'Rp ' + new Intl.NumberFormat('id-ID').format(subtotal)"></span>
                    </div>
                    
                    <!-- Discount row with selector toggle -->
                    <div class="flex justify-between items-center text-slate-500">
                        <div class="flex items-center gap-1.5">
                            <span>Diskon</span>
                            <select x-model="discountPercent" class="text-[10px] font-bold bg-indigo-50 text-indigo-700 px-1.5 py-0.5 rounded-lg border-none focus:outline-none">
                                <option :value="0">0%</option>
                                <option :value="5">5%</option>
                                <option :value="10">10%</option>
                                <option :value="15">15%</option>
                                <option :value="20">20%</option>
                            </select>
                        </div>
                        <span class="font-bold text-rose-600" x-text="'- Rp ' + new Intl.NumberFormat('id-ID').format(discountAmount)"></span>
                    </div>
                    
                    <div class="flex justify-between text-slate-500">
                        <span x-text="'PPN (' + taxPercent + '%)'">PPN</span>
                        <span class="font-bold text-slate-700" x-text="'Rp ' + new Intl.NumberFormat('id-ID').format(taxAmount)"></span>
                    </div>
                    
                    <hr class="border-slate-200">
                    
                    <div class="flex justify-between items-end">
                        <span class="text-sm font-bold text-slate-800">Total Akhir</span>
                        <span class="text-lg font-black text-indigo-600" x-text="'Rp ' + new Intl.NumberFormat('id-ID').format(grandTotal)"></span>
                    </div>
                </div>
                
                <!-- Bayar Sekarang Button -->
                <button id="btn-checkout"
                        @click="initiatePayment()"
                        :disabled="cart.length === 0"
                        :class="cart.length === 0 ? 'bg-slate-200 text-slate-400 cursor-not-allowed' : 'bg-gradient-to-r from-indigo-600 to-violet-600 text-white font-bold hover:shadow-lg hover:shadow-indigo-600/20 active:scale-[0.98]'"
                        class="w-full py-3.5 rounded-2xl flex items-center justify-center gap-2 shadow-md transition-all duration-200">
                    <i data-lucide="credit-card" class="w-5 h-5"></i>
                    <span>BAYAR SEKARANG</span>
                </button>
            </div>
            
        </div>
        
    </div>

    <!-- Payment Checkout Modal Container -->
    <div x-show="paymentModalOpen" 
         class="fixed inset-0 z-50 overflow-y-auto"
         x-transition:enter="transition ease-out duration-300"
         x-transition:enter-start="opacity-0"
         x-transition:enter-end="opacity-100"
         x-transition:leave="transition ease-in duration-200"
         style="display: none;">
        
        <!-- Backdrop -->
        <div class="fixed inset-0 bg-slate-900/60 backdrop-blur-sm" @click="if(!isProcessing) paymentModalOpen = false"></div>
        
        <!-- Modal Content Centered -->
        <div class="flex min-h-screen items-center justify-center p-4 relative z-10">
            <div class="bg-white rounded-3xl max-w-md w-full shadow-2xl overflow-hidden border border-slate-100 flex flex-col"
                 x-transition:enter="transition ease-out duration-300 transform scale-95"
                 x-transition:enter-start="transform scale-95 opacity-0"
                 x-transition:enter-end="transform scale-100 opacity-100">
                
                <!-- Modal Header -->
                <div class="px-6 py-5 border-b border-slate-100 flex items-center justify-between bg-slate-50/50">
                    <div>
                        <h3 class="font-bold text-slate-800 text-md">Konfirmasi Transaksi</h3>
                        <p class="text-[10px] text-slate-500 mt-0.5" x-text="invoiceNumber"></p>
                    </div>
                    <button @click="paymentModalOpen = false" :disabled="isProcessing" class="text-slate-400 hover:text-slate-600 transition-colors">
                        <i data-lucide="x" class="w-5 h-5"></i>
                    </button>
                </div>
                
                <!-- Modal Body -->
                <div class="p-6 space-y-5">
                    
                    <!-- Items List -->
                    <div class="bg-slate-50 rounded-2xl p-4 border border-slate-100 space-y-2 text-xs">
                        <label class="text-xs font-bold text-slate-500">Daftar Barang</label>
                        <div class="space-y-1.5 max-h-40 overflow-y-auto">
                            <template x-for="(item, index) in cart" :key="item.product.id">
                                <div class="flex justify-between items-start">
                                    <div class="flex-1">
                                        <span class="font-semibold text-slate-800" x-text="item.product.name"></span>
                                        <span class="text-slate-500 ml-1" x-text="item.product.sku"></span>
                                        <span class="text-slate-400 text-[10px] ml-1" x-text="item.qty + ' x Rp ' + new Intl.NumberFormat('id-ID').format(item.product.price)"></span>
                                    </div>
                                    <span class="font-semibold text-slate-800" x-text="'Rp ' + new Intl.NumberFormat('id-ID').format(item.product.price * item.qty)"></span>
                                </div>
                            </template>
                        </div>
                    </div>
                    
                    <!-- Billing Summary -->
                    <div class="bg-slate-50 rounded-2xl p-4 border border-slate-100 space-y-1.5 text-xs text-slate-600">
                        <div class="flex justify-between">
                            <span>Subtotal</span>
                            <span class="font-semibold text-slate-800" x-text="'Rp ' + new Intl.NumberFormat('id-ID').format(subtotal)"></span>
                        </div>
                        <div class="flex justify-between" x-show="discountAmount > 0">
                            <span>Diskon</span>
                            <span class="font-semibold text-rose-600" x-text="'- Rp ' + new Intl.NumberFormat('id-ID').format(discountAmount)"></span>
                        </div>
                        <div class="flex justify-between">
                            <span x-text="'Pajak (PPN ' + taxPercent + '%)'">Pajak (PPN)</span>
                            <span class="font-semibold text-slate-800" x-text="'Rp ' + new Intl.NumberFormat('id-ID').format(taxAmount)"></span>
                        </div>
                        <hr class="border-slate-200 my-1">
                        <div class="flex justify-between items-end text-sm">
                            <span class="font-bold text-slate-800">Total Tagihan</span>
                            <span class="text-base font-extrabold text-indigo-600" x-text="'Rp ' + new Intl.NumberFormat('id-ID').format(grandTotal)"></span>
                        </div>
                    </div>
                    
                    <!-- Payment Methods selection -->
                    <div class="space-y-2">
                        <label class="text-xs font-bold text-slate-500">Metode Pembayaran</label>
                        <div class="grid grid-cols-3 gap-2.5">
                            <!-- Tunai -->
                            <button @click="paymentMethod = 'cash'; cashAmount = ''"
                                    :class="paymentMethod === 'cash' ? 'border-indigo-600 bg-indigo-50 text-indigo-700' : 'border-slate-200 text-slate-600 hover:bg-slate-50'"
                                    class="p-3.5 border rounded-2xl text-xs font-bold transition-all duration-200 flex flex-col items-center gap-1">
                                <i data-lucide="wallet" class="w-5 h-5"></i>
                                <span>Tunai</span>
                            </button>
                            <!-- QRIS -->
                            <button @click="paymentMethod = 'qris'; cashAmount = grandTotal.toString()"
                                    :class="paymentMethod === 'qris' ? 'border-indigo-600 bg-indigo-50 text-indigo-700' : 'border-slate-200 text-slate-600 hover:bg-slate-50'"
                                    class="p-3.5 border rounded-2xl text-xs font-bold transition-all duration-200 flex flex-col items-center gap-1">
                                <i data-lucide="qr-code" class="w-5 h-5"></i>
                                <span>QRIS</span>
                            </button>
                            <!-- Transfer -->
                            <button @click="paymentMethod = 'transfer'; cashAmount = grandTotal.toString()"
                                    :class="paymentMethod === 'transfer' ? 'border-indigo-600 bg-indigo-50 text-indigo-700' : 'border-slate-200 text-slate-600 hover:bg-slate-50'"
                                    class="p-3.5 border rounded-2xl text-xs font-bold transition-all duration-200 flex flex-col items-center gap-1">
                                <i data-lucide="landmark" class="w-5 h-5"></i>
                                <span>Transfer</span>
                            </button>
                        </div>
                    </div>
                    
                    <!-- Cash amount inputs (Only if payment method is cash) -->
                    <div class="space-y-2" x-show="paymentMethod === 'cash'">
                        <label class="text-xs font-bold text-slate-500">Nominal Uang Bayar</label>
                        <div class="flex items-center bg-slate-100 rounded-xl px-3 py-3 border border-slate-100 focus-within:border-indigo-400 focus-within:bg-white focus-within:ring-2 focus-within:ring-indigo-100 transition-all duration-200">
                            <span class="text-xs font-bold text-slate-500 mr-1.5">Rp</span>
                            <input type="number" 
                                   placeholder="0" 
                                   x-model="cashAmount"
                                   class="bg-transparent border-none text-sm font-extrabold focus:outline-none w-full text-slate-800">
                        </div>
                        
                        <!-- Quick cash helper buttons -->
                        <div class="flex gap-1.5 mt-2 overflow-x-auto pb-0.5">
                            <button @click="cashAmount = grandTotal.toString()" class="bg-slate-100 hover:bg-slate-200 text-slate-600 font-bold px-3 py-1.5 text-[10px] rounded-lg border border-slate-200/50 transition-colors whitespace-nowrap">Uang Pas</button>
                            <button @click="cashAmount = (Math.ceil(grandTotal / 50000) * 50000).toString()" class="bg-slate-100 hover:bg-slate-200 text-slate-600 font-bold px-3 py-1.5 text-[10px] rounded-lg border border-slate-200/50 transition-colors whitespace-nowrap">Kelipatan 50k</button>
                            <button @click="cashAmount = (Math.ceil(grandTotal / 100000) * 100000).toString()" class="bg-slate-100 hover:bg-slate-200 text-slate-600 font-bold px-3 py-1.5 text-[10px] rounded-lg border border-slate-200/50 transition-colors whitespace-nowrap">Kelipatan 100k</button>
                        </div>
                        
                        <!-- Change Return Info -->
                        <div class="mt-4 bg-emerald-50 text-emerald-800 p-3 rounded-2xl flex items-center justify-between border border-emerald-100">
                            <span class="text-[10px] font-bold uppercase tracking-wider">Kembalian</span>
                            <span class="text-base font-black text-emerald-700" x-text="'Rp ' + new Intl.NumberFormat('id-ID').format(changeAmount)"></span>
                        </div>
                    </div>
                    
                </div>
                
                <!-- Modal Footer -->
                <div class="p-6 border-t border-slate-100 bg-slate-50/50 flex gap-3">
                    <button @click="paymentModalOpen = false" 
                            :disabled="isProcessing"
                            class="flex-1 py-3 border border-slate-200 hover:bg-slate-100 text-slate-600 font-semibold rounded-2xl text-xs transition-colors">
                        Batal
                    </button>
                    
                    <button @click="processCheckout()" 
                            :disabled="!isValidPayment || isProcessing"
                            :class="(!isValidPayment || isProcessing) ? 'bg-slate-200 text-slate-400 cursor-not-allowed' : 'bg-indigo-600 hover:bg-indigo-700 text-white hover:shadow-lg hover:shadow-indigo-600/10'"
                            class="flex-[2] py-3 font-bold rounded-2xl text-xs flex items-center justify-center gap-2 transition-all">
                        
                        <!-- Spinner loading dynamic indicator -->
                        <svg x-show="isProcessing" class="animate-spin -ml-1 mr-2 h-4 w-4 text-white" fill="none" viewBox="0 0 24 24">
                            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                        </svg>
                        
                        <span x-text="isProcessing ? 'Memproses...' : 'Proses & Cetak Struk'"></span>
                    </button>
                </div>
                
            </div>
        </div>
        
    </div>

</div>

<script>
function posComponent() {
    return {
        products: @json($products),
        categories: @json($categories),
        selectedCategory: 'all',
        searchQuery: '',
        
        // Cart State
        cart: [],
        discountPercent: 0,
        taxPercent: 11,
        
        // Payment State
        paymentModalOpen: false,
        paymentMethod: 'cash',
        cashAmount: '',
        isProcessing: false,
        invoiceNumber: '',
        
        // Filtered products list
        get filteredProducts() {
            return this.products.filter(p => {
                const matchesCategory = this.selectedCategory === 'all' || p.category === this.selectedCategory;
                const matchesSearch = p.name.toLowerCase().includes(this.searchQuery.toLowerCase()) || p.sku.toLowerCase().includes(this.searchQuery.toLowerCase());
                return matchesCategory && matchesSearch;
            });
        },
        
        // Add to Cart
        addToCart(product) {
            if (product.stock <= 0) {
                this.$dispatch('show-toast', { message: 'Gagal! Stok barang ' + product.name + ' telah habis.', type: 'danger' });
                return;
            }
            
            const existingItem = this.cart.find(item => item.product.id === product.id);
            
            if (existingItem) {
                if (existingItem.qty >= product.stock) {
                    this.$dispatch('show-toast', { message: 'Batas stok tercapai! Stok hanya tersedia ' + product.stock + ' pcs.', type: 'warning' });
                    return;
                }
                existingItem.qty++;
            } else {
                this.cart.push({
                    product: product,
                    qty: 1
                });
            }
            this.$dispatch('show-toast', { message: product.name + ' ditambahkan ke keranjang.', type: 'success' });
            
            // Reinitialize Lucide icons for cart
            setTimeout(() => lucide.createIcons(), 50);
        },
        
        // Remove from Cart
        removeFromCart(productId) {
            const itemIndex = this.cart.findIndex(item => item.product.id === productId);
            if (itemIndex > -1) {
                const itemName = this.cart[itemIndex].product.name;
                this.cart.splice(itemIndex, 1);
                this.$dispatch('show-toast', { message: itemName + ' dihapus dari keranjang.', type: 'info' });
            }
        },
        
        // Update Qty
        updateQty(productId, delta) {
            const item = this.cart.find(item => item.product.id === productId);
            if (item) {
                const newQty = item.qty + delta;
                if (newQty < 1) {
                    this.removeFromCart(productId);
                    return;
                }
                if (newQty > item.product.stock) {
                    this.$dispatch('show-toast', { message: 'Batas stok tercapai! Stok hanya tersedia ' + item.product.stock + ' pcs.', type: 'warning' });
                    return;
                }
                item.qty = newQty;
            }
        },
        
        // Calculations
        get subtotal() {
            return this.cart.reduce((total, item) => total + (item.product.price * item.qty), 0);
        },
        get discountAmount() {
            return (this.subtotal * this.discountPercent) / 100;
        },
        get taxAmount() {
            return ((this.subtotal - this.discountAmount) * this.taxPercent) / 100;
        },
        get grandTotal() {
            return this.subtotal - this.discountAmount + this.taxAmount;
        },
        get changeAmount() {
            const paid = parseFloat(this.cashAmount) || 0;
            return Math.max(0, paid - this.grandTotal);
        },
        get isValidPayment() {
            if (this.cart.length === 0) return false;
            if (this.paymentMethod === 'cash') {
                const paid = parseFloat(this.cashAmount) || 0;
                return paid >= this.grandTotal;
            }
            return true;
        },
        
        // Generate Invoice Code
        initiatePayment() {
            if (this.cart.length === 0) {
                this.$dispatch('show-toast', { message: 'Peringatan: Keranjang masih kosong!', type: 'warning' });
                return;
            }
            const date = new Date();
            const format = date.getFullYear() +
                String(date.getMonth() + 1).padStart(2, '0') +
                String(date.getDate()).padStart(2, '0');
            const random = Math.floor(100 + Math.random() * 900);
            this.invoiceNumber = 'TRX-' + format + '-' + random;
            this.cashAmount = '';
            this.paymentMethod = 'cash';
            this.paymentModalOpen = true;
        },
        
        // Local simulation (no server call)
        async processCheckout() {
            if (!this.isValidPayment) return;
            this.isProcessing = true;
            
            // Simulate processing delay
            await new Promise(resolve => setTimeout(resolve, 500));
            
            this.cart.forEach(item => {
                const prod = this.products.find(p => p.id === item.product.id);
                if (prod) {
                    prod.stock -= item.qty;
                }
            });
            
            this.$dispatch('show-toast', { message: 'Checkout Berhasil! Invoice ' + this.invoiceNumber + ' disimpan.', type: 'success' });
            this.cart = [];
            this.paymentModalOpen = false;
            this.isProcessing = false;
        }
    };
}

// Global Keyboard Shortcuts
window.addEventListener('keydown', function(e) {
    if (e.key === 'F2') {
        e.preventDefault();
        const searchInput = document.getElementById('search-product');
        if (searchInput) {
            searchInput.focus();
            searchInput.select();
        }
    }
    if (e.key === 'F9') {
        e.preventDefault();
        const checkoutBtn = document.getElementById('btn-checkout');
        if (checkoutBtn && !checkoutBtn.disabled) {
            checkoutBtn.click();
        }
    }
});
</script>
@endsection
