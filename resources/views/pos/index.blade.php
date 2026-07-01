<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>SmartBiz POS - Kasir Profesional</title>
    
    <!-- Tailwind CSS CDN -->
    <script src="https://cdn.tailwindcss.com"></script>
    
    <!-- Google Fonts: Inter & Outfit -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&family=Outfit:wght@400;500;600;700;800;900&display=swap" rel="stylesheet">
    
    <!-- Lucide Icons -->
    <script src="https://unpkg.com/lucide@latest"></script>
    
    <!-- Alpine.js -->
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>

    <style>
        body {
            font-family: 'Inter', sans-serif;
        }
        .heading-font {
            font-family: 'Outfit', sans-serif;
        }
        /* Custom scrollbar */
        ::-webkit-scrollbar {
            width: 5px;
            height: 5px;
        }
        ::-webkit-scrollbar-track {
            background: transparent;
        }
        ::-webkit-scrollbar-thumb {
            background: #cbd5e1;
            border-radius: 9999px;
        }
        ::-webkit-scrollbar-thumb:hover {
            background: #94a3b8;
        }
    </style>
</head>
<body class="bg-slate-50 text-slate-800 antialiased overflow-hidden h-screen flex flex-col" x-data="posEngine()">

    <!-- Top Navbar -->
    <header class="bg-white border-b border-slate-200 h-16 px-6 flex items-center justify-between flex-shrink-0 shadow-sm relative z-10">
        <!-- Logo & Navigation back to Dashboard -->
        <div class="flex items-center gap-3.5">
            <a href="{{ route('admin.dashboard') }}" class="flex h-9 w-9 items-center justify-center rounded-xl bg-gradient-to-tr from-rose-600 to-indigo-600 shadow-md text-white font-black text-lg hover:scale-105 transition-transform duration-200" title="Kembali ke Dashboard">
                S
            </a>
            <div>
                <h1 class="heading-font font-black text-slate-800 text-sm tracking-wide flex items-center gap-1.5">
                    SmartBiz POS <span class="bg-indigo-50 text-indigo-700 text-[10px] font-bold px-2 py-0.5 rounded-full border border-indigo-100">PRO MODE</span>
                </h1>
                <p class="text-[10px] text-slate-400">Sistem Kasir Pintar UMKM</p>
            </div>
        </div>

        <!-- Center: Real-time Clock & Info -->
        <div class="hidden sm:flex items-center gap-5 bg-slate-50 border border-slate-100 px-4 py-1.5 rounded-2xl shadow-inner text-xs font-mono font-bold text-slate-600">
            <div class="flex items-center gap-1.5">
                <i data-lucide="clock" class="w-3.5 h-3.5 text-indigo-500"></i>
                <span x-text="currentTime">00:00:00</span>
            </div>
            <div class="h-3 w-px bg-slate-200"></div>
            <div class="flex items-center gap-1.5">
                <i data-lucide="calendar" class="w-3.5 h-3.5 text-indigo-500"></i>
                <span>{{ now()->translatedFormat('d M Y') }}</span>
            </div>
        </div>

        <!-- Right Side: Cashier Info & Back -->
        <div class="flex items-center gap-4">
            <div class="text-right">
                <p class="text-xs font-bold text-slate-700">{{ auth()->user()->name }}</p>
                <p class="text-[9px] font-semibold text-indigo-600 tracking-wider uppercase font-mono">{{ auth()->user()->roles->pluck('name')->implode(', ') }}</p>
            </div>
            <div class="h-8.5 w-8.5 rounded-xl bg-indigo-50 border border-indigo-100 text-indigo-600 flex items-center justify-center font-bold text-xs">
                {{ substr(auth()->user()->name, 0, 2) }}
            </div>
            <a href="{{ route('admin.dashboard') }}" class="py-2 px-3.5 bg-slate-100 hover:bg-slate-200 text-slate-600 hover:text-slate-800 text-xs font-bold rounded-xl transition-all flex items-center gap-1.5">
                <i data-lucide="layout-dashboard" class="w-4 h-4"></i>
                Dashboard
            </a>
        </div>
    </header>

    <!-- Success & Error Toast Messages -->
    <div class="fixed top-20 right-6 z-50 flex flex-col gap-2 max-w-sm pointer-events-none">
        @if(session('success'))
            <div class="bg-white border border-emerald-100 p-4 rounded-xl shadow-lg flex items-center gap-3 text-emerald-800 pointer-events-auto" x-data="{ show: true }" x-show="show" x-init="setTimeout(() => show = false, 4000)">
                <div class="p-1.5 bg-emerald-50 text-emerald-600 rounded-lg">
                    <i data-lucide="check-circle" class="w-5 h-5"></i>
                </div>
                <div class="flex-1 text-xs font-semibold">{{ session('success') }}</div>
                <button @click="show = false" class="text-slate-400 hover:text-slate-600"><i data-lucide="x" class="w-4 h-4"></i></button>
            </div>
        @endif
        @if($errors->any())
            <div class="bg-white border border-rose-100 p-4 rounded-xl shadow-lg flex items-center gap-3 text-rose-800 pointer-events-auto" x-data="{ show: true }" x-show="show" x-init="setTimeout(() => show = false, 5000)">
                <div class="p-1.5 bg-rose-50 text-rose-600 rounded-lg">
                    <i data-lucide="x-circle" class="w-5 h-5"></i>
                </div>
                <div class="flex-1 text-xs font-semibold">
                    <ul class="list-disc pl-3">
                        @foreach($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
                <button @click="show = false" class="text-slate-400 hover:text-slate-600"><i data-lucide="x" class="w-4 h-4"></i></button>
            </div>
        @endif
    </div>

    <!-- Main Workspace Split View (Full Screen Height, No Outer Scroll) -->
    <main class="flex-1 flex overflow-hidden min-h-0">
        
        <!-- LEFT AREA: Product Directory & Search (7/12 Cols) -->
        <section class="flex-1 flex flex-col bg-slate-50 p-5 min-h-0 border-r border-slate-200">
            
            <!-- Filters, Search & Barcode Bar -->
            <div class="flex flex-col sm:flex-row gap-3 mb-4 flex-shrink-0">
                <!-- Product Search input -->
                <div class="flex-1 flex items-center gap-2 bg-white rounded-xl px-3.5 py-2.5 text-slate-500 border border-slate-200 focus-within:border-indigo-500 focus-within:ring-2 focus-within:ring-indigo-100 transition-all duration-200 shadow-sm">
                    <i data-lucide="search" class="w-4 h-4 text-slate-450"></i>
                    <input type="text" 
                           id="search-product"
                           placeholder="Cari Produk atau Scan Barcode... (Tekan F2)" 
                           x-model="searchQuery"
                           class="bg-transparent border-none text-xs focus:outline-none w-full text-slate-700 placeholder-slate-400 font-medium">
                    <!-- Clear search query -->
                    <button x-show="searchQuery !== ''" @click="searchQuery = ''" class="text-slate-450 hover:text-slate-700 transition-colors">
                        <i data-lucide="x" class="w-4 h-4"></i>
                    </button>
                </div>
            </div>

            <!-- Categories Horizontal Pills -->
            <div class="flex gap-2 overflow-x-auto pb-2 flex-shrink-0 whitespace-nowrap mb-4">
                <button @click="selectedCategory = 'all'"
                        :class="selectedCategory === 'all' ? 'bg-indigo-600 text-white shadow-sm shadow-indigo-650/15' : 'bg-white text-slate-600 border border-slate-200 hover:bg-slate-100'"
                        class="px-4 py-2 rounded-xl text-xs font-bold transition-all duration-200">
                    Semua Kategori
                </button>
                <template x-for="cat in categories" :key="cat">
                    <button @click="selectedCategory = cat"
                            :class="selectedCategory === cat ? 'bg-indigo-600 text-white shadow-sm shadow-indigo-650/15' : 'bg-white text-slate-600 border border-slate-200 hover:bg-slate-100'"
                            class="px-4 py-2 rounded-xl text-xs font-bold transition-all duration-200"
                            x-text="cat">
                    </button>
                </template>
            </div>

            <!-- Products List (Scrollable Grid) -->
            <div class="flex-1 overflow-y-auto min-h-0 pr-1">
                <!-- If search result is empty -->
                <div x-show="filteredProducts.length === 0" class="flex flex-col items-center justify-center h-full text-center py-10" style="display: none;">
                    <div class="p-4 bg-white text-slate-400 rounded-2xl border border-slate-200/50 shadow-sm mb-3">
                        <i data-lucide="package-search" class="w-10 h-10"></i>
                    </div>
                    <h4 class="font-bold text-slate-700 text-sm">Produk Tidak Ditemukan</h4>
                    <p class="text-xs text-slate-400 mt-1">Coba cari dengan kata kunci lain atau pilih kategori yang berbeda.</p>
                </div>

                <!-- Products Grid -->
                <div x-show="filteredProducts.length > 0" class="grid grid-cols-2 sm:grid-cols-3 xl:grid-cols-4 gap-4">
                    <template x-for="prod in filteredProducts" :key="prod.id">
                        <div @click="addToCart(prod)"
                             class="group cursor-pointer bg-white p-3.5 rounded-2xl border border-slate-200 shadow-sm hover:shadow-md hover:border-indigo-500 transition-all duration-200 flex flex-col relative overflow-hidden select-none">
                            
                            <!-- Category Badge -->
                            <span class="absolute top-2 left-2 text-[8px] font-bold bg-slate-100 text-slate-600 px-1.5 py-0.5 rounded-md" x-text="prod.category || 'Umum'"></span>
                            
                            <!-- Thumbnail representation -->
                            <div class="h-24 w-full bg-slate-50 text-indigo-650 rounded-xl flex items-center justify-center mt-3 mb-2.5 relative overflow-hidden group-hover:bg-indigo-50/50 transition-colors duration-200">
                                <div class="p-3 bg-white rounded-2xl shadow-sm border border-slate-100 group-hover:scale-110 transition-transform duration-200">
                                    <i data-lucide="shopping-bag" class="w-6 h-6"></i>
                                </div>
                                <span class="absolute bottom-1 right-1 text-[8px] font-bold bg-slate-900/60 text-white px-1.5 py-0.5 rounded-md" x-text="prod.sku"></span>
                                
                                <template x-if="prod.stock <= 0">
                                    <span class="absolute inset-0 bg-slate-900/60 backdrop-blur-xs flex items-center justify-center text-xs font-bold text-white uppercase tracking-wider">Habis</span>
                                </template>
                            </div>

                            <!-- Details -->
                            <div class="flex-1 flex flex-col justify-between">
                                <div>
                                    <h4 class="text-xs font-bold text-slate-800 line-clamp-2" x-text="prod.name"></h4>
                                </div>
                                <div class="mt-2.5 flex items-center justify-between">
                                    <div>
                                        <p class="text-xs font-black text-indigo-600" x-text="'Rp ' + new Intl.NumberFormat('id-ID').format(prod.price)"></p>
                                        <p class="text-[10px] font-semibold text-slate-400 mt-0.5" x-text="'Stok: ' + prod.stock"></p>
                                    </div>
                                    <div class="h-7 w-7 rounded-xl bg-slate-150 group-hover:bg-indigo-600 group-hover:text-white flex items-center justify-center text-slate-500 border border-slate-200/50 transition-all duration-200">
                                        <i data-lucide="plus" class="w-4 h-4"></i>
                                    </div>
                                </div>
                            </div>

                        </div>
                    </template>
                </div>
            </div>
            
        </section>

        <!-- RIGHT AREA: Shopping Cart & Checkout Panel (5/12 Cols) -->
        <section class="w-[390px] xl:w-[460px] bg-white flex flex-col flex-shrink-0 min-h-0 border-l border-slate-200">
            
            <!-- Cart Title / Header -->
            <div class="px-4 py-3 border-b border-slate-150 flex items-center justify-between bg-slate-50/50 flex-shrink-0">
                <div class="flex items-center gap-2">
                    <span class="p-1.5 bg-indigo-50 text-indigo-600 rounded-lg flex items-center justify-center">
                        <i data-lucide="shopping-cart" class="w-4 h-4"></i>
                    </span>
                    <h3 class="font-bold text-slate-800 text-xs heading-font">Keranjang Belanja</h3>
                </div>
                <button @click="cart = []" 
                        x-show="cart.length > 0" 
                        class="text-[10px] font-bold text-rose-600 hover:text-rose-800 flex items-center gap-1 transition-colors">
                    <i data-lucide="trash-2" class="w-3.5 h-3.5"></i> Kosongkan
                </button>
            </div>

            <!-- Customer Name, Discount, and Tax Inline Settings Bar -->
            <div class="p-3 bg-white border-b border-slate-200 grid grid-cols-12 gap-2 flex-shrink-0">
                <!-- Customer Name (6 cols) -->
                <div class="col-span-6">
                    <label class="text-[9px] font-bold text-slate-450 uppercase tracking-wider block mb-0.5">Nama Pelanggan</label>
                    <div class="flex items-center gap-1.5 bg-slate-50 border border-slate-200 rounded-lg px-2 py-1 focus-within:bg-white focus-within:border-indigo-400 transition-all duration-200">
                        <i data-lucide="user" class="w-3 h-3 text-slate-400 flex-shrink-0"></i>
                        <input type="text" 
                               placeholder="Umum / Baru..." 
                               x-model="customerName"
                               class="bg-transparent border-none text-[11px] focus:outline-none w-full text-slate-700 font-semibold p-0">
                    </div>
                </div>
                <!-- Discount Selector (3 cols) -->
                <div class="col-span-3">
                    <label class="text-[9px] font-bold text-slate-455 uppercase tracking-wider block mb-0.5">Disk (%)</label>
                    <select x-model="discountPercent" class="w-full text-[11px] font-bold bg-slate-50 rounded-lg border border-slate-200 px-2 py-1 text-slate-700 focus:outline-none focus:border-indigo-500">
                        <option :value="0">0%</option>
                        <option :value="5">5%</option>
                        <option :value="10">10%</option>
                        <option :value="15">15%</option>
                        <option :value="20">20%</option>
                    </select>
                </div>
                <!-- Tax Selector (3 cols) -->
                <div class="col-span-3">
                    <label class="text-[9px] font-bold text-slate-455 uppercase tracking-wider block mb-0.5">PPN (%)</label>
                    <select x-model="taxPercent" class="w-full text-[11px] font-bold bg-slate-50 rounded-lg border border-slate-200 px-2 py-1 text-slate-700 focus:outline-none focus:border-indigo-500">
                        <option :value="0">0%</option>
                        <option :value="10">10%</option>
                        <option :value="11">11%</option>
                    </select>
                </div>
            </div>

            <!-- Cart Items (Scrollable list - Expanded Height) -->
            <div class="flex-1 overflow-y-auto p-4 min-h-0 space-y-2.5 bg-slate-50/20">
                <!-- Empty Cart State -->
                <div x-show="cart.length === 0" class="flex flex-col items-center justify-center h-full text-center py-8">
                    <div class="p-3 bg-white text-slate-450 rounded-xl mb-2.5 border border-slate-200/60 shadow-sm">
                        <i data-lucide="shopping-basket" class="w-8 h-8"></i>
                    </div>
                    <h4 class="font-bold text-slate-700 text-xs heading-font">Keranjang Kosong</h4>
                    <p class="text-[10px] text-slate-400 mt-1 max-w-[200px]">Klik produk di sebelah kiri untuk menambah pesanan.</p>
                </div>

                <!-- Cart List -->
                <template x-for="item in cart" :key="item.product.id">
                    <div class="flex items-center gap-3 p-3 bg-white hover:bg-slate-50/50 border border-slate-200 rounded-2xl shadow-xs transition-all duration-200 group">
                        
                        <!-- Thumbnail Representation / Category Icon -->
                        <div class="h-10 w-10 rounded-xl flex-shrink-0 flex items-center justify-center bg-indigo-50 text-indigo-600 border border-indigo-100/50 shadow-inner">
                            <i data-lucide="package" class="w-4.5 h-4.5 opacity-85"></i>
                        </div>
                        
                        <!-- Item Details -->
                        <div class="flex-1 min-w-0">
                            <h4 class="text-xs font-bold text-slate-800 truncate" x-text="item.product.name" :title="item.product.name"></h4>
                            <span class="text-[9px] font-mono font-semibold text-slate-400 block mt-0.5" x-text="item.product.sku"></span>
                            <div class="mt-1 flex items-center gap-1.5">
                                <span class="text-[10px] text-slate-450 font-medium" x-text="'Rp' + new Intl.NumberFormat('id-ID').format(item.product.price)"></span>
                                <span class="text-[9px] text-slate-350 font-black">&times;</span>
                                <span class="text-[10px] text-slate-650 font-bold bg-slate-100 px-1.5 py-0.5 rounded-lg border border-slate-200/40" x-text="item.qty + ' pcs'"></span>
                            </div>
                        </div>
                        
                        <!-- Actions & Qty Control -->
                        <div class="flex flex-col items-end justify-between self-stretch flex-shrink-0 min-h-[52px]">
                            <!-- Delete Button -->
                            <button @click="removeFromCart(item.product.id)" class="text-slate-350 hover:text-rose-600 p-0.5 transition-colors">
                                <i data-lucide="trash-2" class="w-3.5 h-3.5"></i>
                            </button>
                            
                            <!-- Total for this item & Qty Adjuster -->
                            <div class="flex items-center gap-2">
                                <!-- Item total price -->
                                <span class="text-[11px] font-black text-indigo-650" x-text="'Rp ' + new Intl.NumberFormat('id-ID').format(item.product.price * item.qty)"></span>
                                
                                <!-- Compact Qty controls -->
                                <div class="flex items-center bg-slate-100 rounded-lg p-0.5 border border-slate-200/50 shadow-inner">
                                    <button @click="updateQty(item.product.id, -1)" class="h-4.5 w-4.5 rounded-md bg-white border border-slate-200 hover:bg-slate-50 flex items-center justify-center text-slate-600 text-xs font-black transition-all shadow-xs">
                                        -
                                    </button>
                                    <span class="text-[10px] font-bold text-slate-800 w-5 text-center" x-text="item.qty"></span>
                                    <button @click="updateQty(item.product.id, 1)" class="h-4.5 w-4.5 rounded-md bg-white border border-slate-200 hover:bg-slate-50 flex items-center justify-center text-slate-600 text-xs font-black transition-all shadow-xs">
                                        +
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                </template>
            </div>

            <!-- PAYMENT SUMMARY & ACTION PANEL (Compressed Layout) -->
            <div class="p-4 border-t border-slate-200 bg-slate-50/70 flex-shrink-0 space-y-3">
                
                <!-- Calculations (Subtotal, Discount, PPN) in 1 dynamic row of badges -->
                <div class="flex items-center justify-between text-[10px] text-slate-500 font-semibold px-0.5">
                    <div class="flex flex-wrap gap-x-2 gap-y-1">
                        <span>Subtotal: <strong class="text-slate-700" x-text="'Rp' + new Intl.NumberFormat('id-ID').format(subtotal)">Rp0</strong></span>
                        <span x-show="discountAmount > 0" class="text-rose-600" style="display: none;">| Diskon: <strong x-text="'-Rp' + new Intl.NumberFormat('id-ID').format(discountAmount)"></strong></span>
                        <span>| Pajak (PPN): <strong class="text-slate-700" x-text="'Rp' + new Intl.NumberFormat('id-ID').format(taxAmount)">Rp0</strong></span>
                    </div>
                </div>
                
                <!-- Split Billing Grid (Total Tagihan vs Cash Pay) -->
                <div class="grid grid-cols-12 gap-2">
                    <!-- Total Tagihan (Left 5 Cols) -->
                    <div class="col-span-5 bg-indigo-900 text-white p-3 rounded-xl flex flex-col justify-between border border-indigo-950 shadow-sm">
                        <span class="text-[8px] font-bold uppercase tracking-wider text-indigo-200">Total Tagihan</span>
                        <span class="text-xs font-black mt-1 tracking-tight truncate" x-text="'Rp ' + new Intl.NumberFormat('id-ID').format(grandTotal)">Rp 0</span>
                    </div>
                    
                    <!-- Uang Tunai/Bayar (Right 7 Cols) -->
                    <div class="col-span-7 bg-white border border-slate-200 p-2 rounded-xl flex flex-col justify-between shadow-xs focus-within:border-indigo-400 transition-colors">
                        <span class="text-[8px] font-bold uppercase tracking-wider text-slate-400">Uang Bayar (Tunai)</span>
                        <div class="flex items-center gap-1 mt-0.5">
                            <span class="text-[10px] font-extrabold text-slate-400">Rp</span>
                            <input type="number" 
                                   placeholder="0" 
                                   x-model.number="cashAmount"
                                   class="bg-transparent border-none text-xs font-black focus:outline-none w-full text-slate-800 p-0">
                        </div>
                    </div>
                </div>
                
                <!-- Kembalian Bar -->
                <div :class="isCashInsufficient ? 'bg-rose-50 border-rose-100 text-rose-800' : 'bg-emerald-50 border-emerald-100 text-emerald-950'"
                     class="p-2 px-3 rounded-xl border transition-all duration-200 flex items-center justify-between text-[11px] font-semibold">
                    <span class="font-bold text-[9px] uppercase tracking-wider flex items-center gap-1.5">
                        <i :data-lucide="isCashInsufficient ? 'alert-circle' : 'coins'" class="w-3.5 h-3.5"></i>
                        <span x-text="isCashInsufficient ? 'Uang Kurang!' : 'Uang Kembalian'"></span>
                    </span>
                    <span :class="isCashInsufficient ? 'text-rose-700' : 'text-emerald-700'"
                          class="font-black"
                          x-text="isCashInsufficient ? '- Rp ' + new Intl.NumberFormat('id-ID').format(Math.abs(changeAmount)) : 'Rp ' + new Intl.NumberFormat('id-ID').format(changeAmount)">
                    </span>
                </div>

                <!-- Hidden form submit wrapper -->
                <form action="{{ route('pos.store') }}" method="POST" id="checkout-form" @submit="submitCheck($event)">
                    @csrf
                    <!-- Dynamic cart list inputs -->
                    <template x-for="(item, idx) in cart" :key="item.product.id">
                        <div>
                            <input type="hidden" :name="'items[' + idx + '][id]'" :value="item.product.id">
                            <input type="hidden" :name="'items[' + idx + '][qty]'" :value="item.qty">
                        </div>
                    </template>
                    <input type="hidden" name="customer_name" :value="customerName">
                    <input type="hidden" name="discount_percent" :value="discountPercent">
                    <input type="hidden" name="tax_percent" :value="taxPercent">

                    <!-- Main submit button -->
                    <button type="submit"
                            id="btn-checkout"
                            :disabled="cart.length === 0 || isCashInsufficient"
                            :class="(cart.length === 0 || isCashInsufficient) ? 'bg-slate-200 text-slate-400 cursor-not-allowed border-none shadow-none' : 'bg-gradient-to-r from-indigo-600 to-violet-600 hover:shadow-md hover:shadow-indigo-650/15 active:scale-[0.99] text-white'"
                            class="w-full py-3.5 rounded-xl flex items-center justify-center gap-2 font-bold text-xs shadow-xs transition-all duration-200 mt-1">
                        <i data-lucide="check-circle" class="w-4 h-4"></i>
                        <span>SIMPAN PEMBAYARAN (F9)</span>
                    </button>
                </form>
            </div>
            
        </section>

    </main>

    <!-- Alpine.js & JavaScript scripts -->
    <script>
        function posEngine() {
            return {
                products: @json($products),
                categories: @json($categories),
                selectedCategory: 'all',
                searchQuery: '',
                
                // Cart State
                cart: [],
                customerName: '',
                discountPercent: 0,
                taxPercent: 11,
                
                // Payment State
                cashAmount: '',
                currentTime: '00:00:00',

                init() {
                    // Start digital clock
                    setInterval(() => {
                        const date = new Date();
                        this.currentTime = date.toTimeString().split(' ')[0];
                    }, 1000);

                    // Setup Keyboard shortcuts
                    window.addEventListener('keydown', (e) => {
                        if (e.key === 'F2') {
                            e.preventDefault();
                            const searchEl = document.getElementById('search-product');
                            if (searchEl) {
                                searchEl.focus();
                                searchEl.select();
                            }
                        }
                        if (e.key === 'F9') {
                            e.preventDefault();
                            const checkoutForm = document.getElementById('checkout-form');
                            if (checkoutForm && this.cart.length > 0 && !this.isCashInsufficient) {
                                checkoutForm.submit();
                            }
                        }
                    });

                    // Initialize Lucide icons
                    setTimeout(() => lucide.createIcons(), 100);
                },

                // Filters
                get filteredProducts() {
                    return this.products.filter(p => {
                        const matchesCategory = this.selectedCategory === 'all' || p.category === this.selectedCategory;
                        const matchesSearch = p.name.toLowerCase().includes(this.searchQuery.toLowerCase()) || p.sku.toLowerCase().includes(this.searchQuery.toLowerCase());
                        return matchesCategory && matchesSearch;
                    });
                },

                // Cart actions
                addToCart(product) {
                    if (product.stock <= 0) {
                        alert('Stok barang ' + product.name + ' telah habis.');
                        return;
                    }
                    
                    const existingItem = this.cart.find(item => item.product.id === product.id);
                    
                    if (existingItem) {
                        if (existingItem.qty >= product.stock) {
                            alert('Stok hanya tersedia ' + product.stock + ' pcs.');
                            return;
                        }
                        existingItem.qty++;
                    } else {
                        this.cart.push({
                            product: product,
                            qty: 1
                        });
                    }
                    setTimeout(() => lucide.createIcons(), 50);
                },

                removeFromCart(productId) {
                    this.cart = this.cart.filter(item => item.product.id !== productId);
                    setTimeout(() => lucide.createIcons(), 50);
                },

                updateQty(productId, delta) {
                    const item = this.cart.find(item => item.product.id === productId);
                    if (item) {
                        const newQty = item.qty + delta;
                        if (newQty < 1) {
                            this.removeFromCart(productId);
                            return;
                        }
                        if (newQty > item.product.stock) {
                            alert('Batas stok tercapai. Tersedia ' + item.product.stock + ' pcs.');
                            return;
                        }
                        item.qty = newQty;
                    }
                    setTimeout(() => lucide.createIcons(), 50);
                },

                // Calculations
                get subtotal() {
                    return this.cart.reduce((sum, item) => sum + (item.product.price * item.qty), 0);
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
                    return paid - this.grandTotal;
                },
                get isCashInsufficient() {
                    if (this.cart.length === 0) return false;
                    const paid = parseFloat(this.cashAmount) || 0;
                    return paid < this.grandTotal;
                },

                submitCheck(e) {
                    if (this.cart.length === 0) {
                        e.preventDefault();
                        alert('Peringatan: Keranjang masih kosong!');
                        return false;
                    }
                    if (this.isCashInsufficient) {
                        e.preventDefault();
                        alert('Uang pembayaran kurang!');
                        return false;
                    }
                    return true;
                }
            };
        }
    </script>
</body>
</html>
