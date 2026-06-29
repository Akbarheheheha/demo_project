<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Mini ERP & POS') - SmartBiz UMKM</title>
    
    <!-- Google Fonts: Outfit for Modern ERP Theme -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    
    <!-- Tailwind CSS & Vite Assets -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    
    <!-- Lucide Icons CDN -->
    <script src="https://unpkg.com/lucide@latest"></script>
    
    <style>
        body {
            font-family: 'Outfit', sans-serif;
        }
        /* Custom scrollbar for better visual look */
        ::-webkit-scrollbar {
            width: 6px;
            height: 6px;
        }
        ::-webkit-scrollbar-track {
            background: #f1f5f9;
        }
        ::-webkit-scrollbar-thumb {
            background: #cbd5e1;
            border-radius: 4px;
        }
        ::-webkit-scrollbar-thumb:hover {
            background: #94a3b8;
        }
    </style>
</head>
<body class="bg-slate-50 text-slate-800 antialiased" 
      x-data="{ sidebarOpen: false, activePage: '@yield('active_page', 'dashboard')' }"
      x-init="
        @if(session('login_success'))
            setTimeout(() => { $dispatch('show-toast', { message: '{{ session('login_success') }}', type: 'success' }) }, 400);
        @endif
      ">

    <!-- Toast Notification Container -->
    <div x-data="{ 
            toasts: [],
            addToast(message, type = 'success') {
                const id = Date.now();
                this.toasts.push({ id, message, type });
                setTimeout(() => {
                    this.toasts = this.toasts.filter(t => t.id !== id);
                }, 4000);
            }
         }"
         @show-toast.window="addToast($event.detail.message, $event.detail.type)"
         class="fixed top-5 right-5 z-50 flex flex-col gap-2 max-w-sm w-full pointer-events-none">
        
        <template x-for="toast in toasts" :key="toast.id">
            <div x-transition:enter="transition ease-out duration-300 transform translate-y-[-10px] opacity-0"
                 x-transition:enter-start="transform translate-y-[-10px] opacity-0"
                 x-transition:enter-end="transform translate-y-0 opacity-100"
                 x-transition:leave="transition ease-in duration-200 transform translate-x-[20px] opacity-0"
                 class="p-4 rounded-xl shadow-lg flex items-center gap-3 pointer-events-auto border"
                 :class="{
                    'bg-white border-emerald-100 text-emerald-800': toast.type === 'success',
                    'bg-white border-amber-100 text-amber-800': toast.type === 'warning',
                    'bg-white border-rose-100 text-rose-800': toast.type === 'danger',
                    'bg-white border-blue-100 text-blue-800': toast.type === 'info'
                 }">
                <!-- Icon mapping -->
                <div class="p-1.5 rounded-lg" 
                     :class="{
                        'bg-emerald-50 text-emerald-600': toast.type === 'success',
                        'bg-amber-50 text-amber-600': toast.type === 'warning',
                        'bg-rose-50 text-rose-600': toast.type === 'danger',
                        'bg-blue-50 text-blue-600': toast.type === 'info'
                     }">
                    <i class="w-5 h-5" :data-lucide="toast.type === 'success' ? 'check-circle' : (toast.type === 'warning' ? 'alert-triangle' : (toast.type === 'danger' ? 'x-circle' : 'info'))"></i>
                </div>
                <div class="flex-1 text-sm font-medium" x-text="toast.message"></div>
                <button @click="toasts = toasts.filter(t => t.id !== toast.id)" class="text-slate-400 hover:text-slate-600 transition-colors">
                    <i data-lucide="x" class="w-4 h-4"></i>
                </button>
            </div>
        </template>
    </div>

    <!-- Layout Wrapper -->
    <div class="flex min-h-screen">
        
        <!-- Sidebar Navigation -->
        <aside class="fixed inset-y-0 left-0 z-40 w-64 transform bg-gradient-to-b from-slate-900 to-indigo-950 text-slate-300 transition-transform duration-300 ease-in-out md:translate-x-0"
               :class="sidebarOpen ? 'translate-x-0' : '-translate-x-full'">
            
            <!-- Sidebar Header / Logo -->
            <div class="flex h-16 items-center justify-between px-6 border-b border-slate-800">
                <a href="{{ route('dashboard') }}" class="flex items-center gap-2.5">
                    <div class="flex h-9 w-9 items-center justify-center rounded-xl bg-gradient-to-tr from-indigo-500 to-violet-500 shadow-md shadow-indigo-500/20 text-white font-bold text-lg">
                        S
                    </div>
                    <div>
                        <h1 class="font-bold text-white tracking-wide text-md">SmartBiz ERP</h1>
                        <p class="text-[10px] text-indigo-400 font-medium">Mini ERP & Advanced POS</p>
                    </div>
                </a>
                
                <!-- Close sidebar (Mobile Only) -->
                <button class="rounded-lg p-1.5 hover:bg-slate-800 text-slate-400 hover:text-white md:hidden" @click="sidebarOpen = false">
                    <i data-lucide="x" class="h-5 w-5"></i>
                </button>
            </div>
            
            <!-- Sidebar Navigation Links -->
            <nav class="flex-1 space-y-1.5 px-4 py-6">
                <!-- Dashboard Link -->
                <a href="{{ route('dashboard') }}" 
                   class="flex items-center gap-3.5 px-4 py-3 rounded-xl transition-all duration-200"
                   :class="activePage === 'dashboard' ? 'bg-gradient-to-r from-indigo-600 to-violet-600 text-white font-semibold shadow-md shadow-indigo-900/30' : 'hover:bg-slate-800/60 hover:text-white'">
                    <i data-lucide="layout-dashboard" class="w-5 h-5"></i>
                    <span>Dashboard</span>
                </a>
                
                <!-- Kasir / POS Link -->
                <a href="{{ route('pos') }}" 
                   class="flex items-center gap-3.5 px-4 py-3 rounded-xl transition-all duration-200"
                   :class="activePage === 'pos' ? 'bg-gradient-to-r from-indigo-600 to-violet-600 text-white font-semibold shadow-md shadow-indigo-900/30' : 'hover:bg-slate-800/60 hover:text-white'">
                    <i data-lucide="shopping-cart" class="w-5 h-5"></i>
                    <span>Kasir POS</span>
                </a>
                
                <!-- Inventaris Link -->
                <a href="{{ route('inventory') }}" 
                   class="flex items-center gap-3.5 px-4 py-3 rounded-xl transition-all duration-200"
                   :class="activePage === 'inventory' ? 'bg-gradient-to-r from-indigo-600 to-violet-600 text-white font-semibold shadow-md shadow-indigo-900/30' : 'hover:bg-slate-800/60 hover:text-white'">
                    <i data-lucide="package" class="w-5 h-5"></i>
                    <span>Inventaris</span>
                </a>
                
                <!-- Laporan Link -->
                <a href="{{ route('reports') }}" 
                   class="flex items-center gap-3.5 px-4 py-3 rounded-xl transition-all duration-200"
                   :class="activePage === 'reports' ? 'bg-gradient-to-r from-indigo-600 to-violet-600 text-white font-semibold shadow-md shadow-indigo-900/30' : 'hover:bg-slate-800/60 hover:text-white'">
                    <i data-lucide="bar-chart-3" class="w-5 h-5"></i>
                    <span>Laporan</span>
                </a>
                
                <!-- Pengaturan Link -->
                <a href="{{ route('settings') }}" 
                   class="flex items-center gap-3.5 px-4 py-3 rounded-xl transition-all duration-200"
                   :class="activePage === 'settings' ? 'bg-gradient-to-r from-indigo-600 to-violet-600 text-white font-semibold shadow-md shadow-indigo-900/30' : 'hover:bg-slate-800/60 hover:text-white'">
                    <i data-lucide="settings" class="w-5 h-5"></i>
                    <span>Pengaturan</span>
                </a>
            </nav>
            
            <!-- Sidebar Footer / UMKM Info -->
            <div class="absolute bottom-0 w-full p-4 border-t border-slate-800 bg-slate-950/40">
                <div class="flex items-center gap-3">
                    <div class="h-9 w-9 rounded-full bg-slate-800 flex items-center justify-center text-indigo-400 font-semibold border border-indigo-500/20">
                        KB
                    </div>
                    <div class="overflow-hidden">
                        <h4 class="text-sm font-semibold text-white truncate">Kios Berkah Raya</h4>
                        <p class="text-[10px] text-slate-400">UMKM Retail & Kelontong</p>
                    </div>
                </div>
            </div>
        </aside>

        <!-- Main Content Area -->
        <div class="flex flex-1 flex-col md:pl-64">
            
            <!-- Topbar sticky header -->
            <header class="sticky top-0 z-30 flex h-16 w-full items-center justify-between border-b border-slate-200 bg-white/80 backdrop-blur-md px-6 dark:bg-slate-900/80 dark:border-slate-800">
                
                <!-- Mobile Sidebar Toggle -->
                <div class="flex items-center gap-4">
                    <button class="rounded-lg p-2 hover:bg-slate-100 text-slate-600 md:hidden transition-colors" @click="sidebarOpen = !sidebarOpen">
                        <i data-lucide="menu" class="h-6 w-6"></i>
                    </button>
                    
                    <!-- Search Bar (Dummy) -->
                    <div class="hidden sm:flex items-center gap-2 bg-slate-100 rounded-xl px-3 py-1.5 w-64 text-slate-500 border border-slate-100 hover:border-slate-200 transition-colors">
                        <i data-lucide="search" class="w-4 h-4"></i>
                        <input type="text" placeholder="Cari transaksi, barang..." class="bg-transparent border-none text-xs focus:outline-none w-full text-slate-700">
                    </div>
                </div>
                
                <!-- Topbar Actions -->
                <div class="flex items-center gap-4">
                    
                    <!-- Connection Status Indicator -->
                    <div class="hidden md:flex items-center gap-1.5 px-3 py-1 rounded-full bg-emerald-50 border border-emerald-100 text-[10px] font-semibold text-emerald-700">
                        <span class="h-1.5 w-1.5 rounded-full bg-emerald-500 animate-pulse"></span>
                        Cloud Sync: Online
                    </div>
                    
                    <!-- Notifications Dropdown -->
                    <div class="relative" x-data="{ open: false }">
                        <button @click="open = !open" @click.away="open = false" 
                                class="relative rounded-xl p-2 hover:bg-slate-100 text-slate-600 hover:text-indigo-600 transition-all duration-200">
                            <i data-lucide="bell" class="h-5.5 w-5.5"></i>
                            <!-- Badge -->
                            <span class="absolute top-1.5 right-1.5 flex h-2 w-2">
                                <span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-rose-400 opacity-75"></span>
                                <span class="relative inline-flex rounded-full h-2 w-2 bg-rose-500"></span>
                            </span>
                        </button>
                        
                        <!-- Notifications List Menu -->
                        <div x-show="open" 
                             x-transition:enter="transition ease-out duration-200"
                             x-transition:enter-start="opacity-0 scale-95 translate-y-2"
                             x-transition:enter-end="opacity-100 scale-100 translate-y-0"
                             x-transition:leave="transition ease-in duration-75"
                             class="absolute right-0 mt-2 w-80 origin-top-right rounded-2xl bg-white p-2 shadow-xl border border-slate-100 z-50 text-slate-800">
                            <div class="px-4 py-2.5 border-b border-slate-100 flex items-center justify-between">
                                <h3 class="font-bold text-sm">Notifikasi Baru</h3>
                                <span class="text-[10px] font-semibold bg-rose-50 text-rose-600 px-2 py-0.5 rounded-full">2 Baru</span>
                            </div>
                            <div class="max-h-64 overflow-y-auto py-1">
                                <!-- Notif 1 -->
                                <a href="{{ route('inventory') }}" class="flex gap-3 px-4 py-3 hover:bg-slate-50 rounded-xl transition-colors">
                                    <div class="p-2 bg-amber-50 text-amber-600 rounded-xl h-9 w-9 flex items-center justify-center flex-shrink-0">
                                        <i data-lucide="alert-triangle" class="w-4 h-4"></i>
                                    </div>
                                    <div>
                                        <p class="text-xs font-semibold text-slate-800">Stok Menipis Terdeteksi!</p>
                                        <p class="text-[10px] text-slate-500 mt-0.5">Stok Deterjen Rinso sisa 4 pcs.</p>
                                    </div>
                                </a>
                                <!-- Notif 2 -->
                                <a href="{{ route('inventory') }}" class="flex gap-3 px-4 py-3 hover:bg-slate-50 rounded-xl transition-colors">
                                    <div class="p-2 bg-rose-50 text-rose-600 rounded-xl h-9 w-9 flex items-center justify-center flex-shrink-0">
                                        <i data-lucide="x-circle" class="w-4 h-4"></i>
                                    </div>
                                    <div>
                                        <p class="text-xs font-semibold text-slate-800">Stok Habis!</p>
                                        <p class="text-[10px] text-slate-500 mt-0.5">Chitato Sapi Panggang habis terjual.</p>
                                    </div>
                                </a>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Profile Menu Dropdown -->
                    <div class="relative" x-data="{ open: false }">
                        <button @click="open = !open" @click.away="open = false" 
                                class="flex items-center gap-2.5 rounded-xl p-1 hover:bg-slate-50 transition-colors focus:outline-none">
                            <img class="h-9 w-9 rounded-xl object-cover border border-slate-100" 
                                 src="https://images.unsplash.com/photo-1534528741775-53994a69daeb?auto=format&fit=crop&q=80&w=100&h=100" 
                                 alt="Profile">
                            <div class="hidden lg:block text-left pr-2">
                                <h4 class="text-xs font-semibold text-slate-800">Citra Kirana</h4>
                                <p class="text-[10px] text-slate-500 font-medium">Administrator</p>
                            </div>
                            <i data-lucide="chevron-down" class="w-3.5 h-3.5 text-slate-400"></i>
                        </button>
                        
                        <!-- Dropdown Menu -->
                        <div x-show="open" 
                             x-transition:enter="transition ease-out duration-200"
                             x-transition:enter-start="opacity-0 scale-95 translate-y-2"
                             x-transition:enter-end="opacity-100 scale-100 translate-y-0"
                             x-transition:leave="transition ease-in duration-75"
                             class="absolute right-0 mt-2 w-48 origin-top-right rounded-2xl bg-white p-1.5 shadow-xl border border-slate-100 z-50">
                            
                            <a href="#" @click.prevent="$dispatch('show-toast', { message: 'Profil Saya akan hadir pada versi berikutnya!', type: 'info' })"
                               class="flex items-center gap-2.5 px-3 py-2.5 text-xs text-slate-700 hover:bg-slate-50 rounded-xl transition-colors">
                                <i data-lucide="user" class="w-4 h-4 text-slate-400"></i>
                                Profil Saya
                            </a>
                            
                            <a href="#" @click.prevent="$dispatch('show-toast', { message: 'Pengaturan Toko akan hadir pada versi berikutnya!', type: 'info' })"
                               class="flex items-center gap-2.5 px-3 py-2.5 text-xs text-slate-700 hover:bg-slate-50 rounded-xl transition-colors">
                                <i data-lucide="sliders" class="w-4 h-4 text-slate-400"></i>
                                Pengaturan Toko
                            </a>
                            
                            <hr class="my-1 border-slate-100">
                            
                            <!-- Form Logout -->
                            <form id="logout-form" action="{{ route('logout') }}" method="POST" class="hidden">
                                @csrf
                            </form>
                            <a href="#" @click.prevent="document.getElementById('logout-form').submit()"
                               class="flex items-center gap-2.5 px-3 py-2.5 text-xs text-rose-600 hover:bg-rose-50 rounded-xl transition-colors">
                                <i data-lucide="log-out" class="w-4 h-4"></i>
                                Log Out
                            </a>
                        </div>
                    </div>
                    
                </div>
            </header>
            
            <!-- Page Main Content Slot -->
            <main class="flex-1 p-6">
                @yield('content')
            </main>
            
            <!-- Sticky Dashboard Footer -->
            <footer class="py-4 px-6 bg-white border-t border-slate-200 text-center text-xs text-slate-400">
                &copy; 2026 SmartBiz UMKM. Powered by Laravel 13 & Tailwind CSS v4.
            </footer>
        </div>
    </div>
    
    <script>
        // Initialize Lucide icons on page load
        document.addEventListener('DOMContentLoaded', function() {
            lucide.createIcons();
        });
        
        // Auto reinizialize icons on Alpine modifications if needed
        document.addEventListener('alpine:initialized', () => {
            lucide.createIcons();
        });
    </script>
</body>
</html>
