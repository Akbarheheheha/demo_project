<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Mini ERP & POS') - SmartBiz UMKM</title>
    
    <!-- Vite Assets (CSS & JS) -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    
    <style>
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
        /* Custom progress bar for SPA loader */
        #spa-progressbar {
            position: fixed;
            top: 0;
            left: 0;
            height: 3.5px;
            background: linear-gradient(to right, #4f46e5, #8b5cf6);
            z-index: 9999;
            transition: width 0.3s ease, opacity 0.3s ease;
            width: 0%;
            pointer-events: none;
        }
        /* Moving gradient animation for sidebar */
        @keyframes sidebar-gradient {
            0% {
                background-position: 0% 50%;
            }
            50% {
                background-position: 100% 50%;
            }
            100% {
                background-position: 0% 50%;
            }
        }
        .sidebar-animate-bg {
            background: linear-gradient(270deg, #1e1b4b, #330854ff);
            background-size: 400% 400%;
            animation: sidebar-gradient 12s ease infinite;
        }
    </style>
</head>
<body class="bg-slate-50 text-slate-800 antialiased font-sans" 
      data-active-page="@yield('active_page', 'dashboard')"
      x-data="{ sidebarOpen: window.innerWidth >= 768, activePage: '@yield('active_page', 'dashboard')' }"
      @set-active-page.window="activePage = $event.detail"
      x-init="
        @if(session('login_success'))
            setTimeout(() => { $dispatch('show-toast', { message: '{{ session('login_success') }}', type: 'success' }) }, 400);
        @endif
        @if(session('success'))
            setTimeout(() => { $dispatch('show-toast', { message: '{{ session('success') }}', type: 'success' }) }, 400);
        @endif
        @if(session('error'))
            setTimeout(() => { $dispatch('show-toast', { message: '{{ session('error') }}', type: 'danger' }) }, 400);
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
        @unless(auth()->user()->hasRole('Gudang'))
            <!-- Sidebar Backdrop (Mobile Only) -->
        <div x-show="sidebarOpen" 
             @click="sidebarOpen = false" 
             x-transition:enter="transition-opacity ease-out duration-300"
             x-transition:enter-start="opacity-0"
             x-transition:enter-end="opacity-100"
             x-transition:leave="transition-opacity ease-in duration-200"
             x-transition:leave-start="opacity-100"
             x-transition:leave-end="opacity-0"
             class="fixed inset-0 z-30 bg-slate-900/60 backdrop-blur-xs md:hidden"
             style="display: none;"></div>
             @endunless

        @unless(auth()->user()->hasRole('Gudang'))
        <!-- Sidebar Navigation -->
        <aside class="fixed inset-y-0 left-0 z-40 transform sidebar-animate-bg text-slate-300 transition-all duration-300 ease-in-out"
               :class="sidebarOpen ? 'w-64 translate-x-0' : 'w-20 md:translate-x-0 -translate-x-full'">
            
            <!-- Sidebar Header / Logo -->
            <div class="flex h-16 items-center border-b border-slate-800 transition-all duration-300"
                 :class="sidebarOpen ? 'justify-between px-6' : 'justify-center px-0'">
                
                <!-- Logo & Brand (Only visible when sidebar is open) -->
                <a href="{{ route($rolePrefix . '.dashboard') }}" class="flex items-center gap-2.5" x-show="sidebarOpen" x-transition:enter="transition-opacity ease-out duration-200" x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100">
                    <div class="flex h-9 w-9 items-center justify-center rounded-xl bg-gradient-to-tr from-indigo-500 to-violet-500 shadow-md shadow-indigo-500/20 text-white font-bold text-lg flex-shrink-0">
                        S
                    </div>
                    <div class="overflow-hidden whitespace-nowrap">
                        <h1 class="font-bold text-white tracking-wide text-md">SmartBiz ERP</h1>
                        <p class="text-[10px] text-indigo-400 font-medium">Mini ERP & Advanced POS</p>
                    </div>
                </a>

                <!-- Collapsed State Logo Button -->
                <div x-show="!sidebarOpen" class="flex flex-col items-center justify-center">
                    <button class="group flex h-9 w-9 items-center justify-center rounded-xl text-slate-400 hover:bg-slate-850 hover:text-white transition-all duration-200"
                            @click="sidebarOpen = !sidebarOpen">
                        <!-- Default text 'S' with logo gradient -->
                        <div class="flex h-9 w-9 items-center justify-center rounded-xl bg-gradient-to-tr from-indigo-500 to-violet-500 shadow-md shadow-indigo-500/20 text-white font-bold text-lg flex-shrink-0 group-hover:hidden">
                            S
                        </div>
                        <!-- Hover icon 'panel-left-open' -->
                        <i data-lucide="panel-left-open" class="hidden group-hover:block h-5 w-5"></i>
                    </button>
                </div>

                <!-- Toggle Button inside Sidebar -->
                <button x-show="sidebarOpen" 
                        class="rounded-lg p-1.5 hover:bg-slate-800 text-slate-400 hover:text-white transition-colors" 
                        @click="sidebarOpen = !sidebarOpen">
                    <i data-lucide="panel-left-close" class="h-5 w-5"></i>
                </button>
            </div>
            
            <!-- Sidebar Navigation Links -->
            <nav class="flex-1 space-y-1.5 px-3 py-6" @click="if(window.innerWidth < 768) sidebarOpen = false">
                <!-- Dashboard Link -->
                @hasanyrole('Super Admin|Manager')
                <a href="{{ route($rolePrefix . '.dashboard') }}" 
                   class="flex items-center gap-3.5 px-4 py-3 rounded-xl transition-all duration-200"
                   :class="activePage === 'dashboard' ? 'bg-gradient-to-r from-indigo-600 to-violet-600 text-white font-semibold shadow-md shadow-indigo-900/30' : 'hover:bg-slate-800/60 hover:text-white'"
                   :title="!sidebarOpen ? 'Dashboard' : ''">
                    <i data-lucide="layout-dashboard" class="w-5 h-5 flex-shrink-0"></i>
                    <span x-show="sidebarOpen" x-transition.opacity>Dashboard</span>
                </a>
                @endhasanyrole

                
                <!-- Kasir / POS Link -->
                @hasanyrole('Kasir|Super Admin')
                <a href="{{ route('pos') }}" 
                   data-spa-ignore
                   class="flex items-center gap-3.5 px-4 py-3 rounded-xl transition-all duration-200"
                   :class="activePage === 'pos' ? 'bg-gradient-to-r from-indigo-600 to-violet-600 text-white font-semibold shadow-md shadow-indigo-900/30' : 'hover:bg-slate-800/60 hover:text-white'"
                   :title="!sidebarOpen ? 'Kasir POS' : ''">
                    <i data-lucide="shopping-cart" class="w-5 h-5 flex-shrink-0"></i>
                    <span x-show="sidebarOpen" x-transition.opacity>Kasir POS</span>
                </a>
                @endhasanyrole
                
                <!-- Inventaris Link -->
                @hasanyrole('Super Admin|Manager')
                <a href="{{ route($rolePrefix . '.inventory') }}" 
                   data-spa-ignore
                   class="flex items-center gap-3.5 px-4 py-3 rounded-xl transition-all duration-200"
                   :class="activePage === 'inventory' ? 'bg-gradient-to-r from-indigo-600 to-violet-600 text-white font-semibold shadow-md shadow-indigo-900/30' : 'hover:bg-slate-800/60 hover:text-white'"
                   :title="!sidebarOpen ? 'Inventaris' : ''">
                    <i data-lucide="package" class="w-5 h-5 flex-shrink-0"></i>
                    <span x-show="sidebarOpen" x-transition.opacity>Inventaris</span>
                </a>
                @endhasanyrole
                
                <!-- Laporan Link -->
                @hasanyrole('Super Admin|Manager')
                <a href="{{ route($rolePrefix . '.reports') }}" 
                   class="flex items-center gap-3.5 px-4 py-3 rounded-xl transition-all duration-200"
                   :class="activePage === 'reports' ? 'bg-gradient-to-r from-indigo-600 to-violet-600 text-white font-semibold shadow-md shadow-indigo-900/30' : 'hover:bg-slate-800/60 hover:text-white' "
                   :title="!sidebarOpen ? 'Laporan Keuangan' : ''">
                    <i data-lucide="bar-chart-3" class="w-5 h-5 flex-shrink-0"></i>
                    <span x-show="sidebarOpen" x-transition.opacity>Laporan Keuangan</span>
                </a>

                <a href="{{ route($rolePrefix . '.expenses.index') }}" 
                   class="flex items-center gap-3.5 px-4 py-3 rounded-xl transition-all duration-200"
                   :class="activePage === 'expenses' ? 'bg-gradient-to-r from-indigo-600 to-violet-600 text-white font-semibold shadow-md shadow-indigo-900/30' : 'hover:bg-slate-800/60 hover:text-white' "
                   :title="!sidebarOpen ? 'Pengeluaran' : ''">
                    <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-banknote-arrow-down-icon lucide-banknote-arrow-down"><path d="M12 18H4a2 2 0 0 1-2-2V8a2 2 0 0 1 2-2h16a2 2 0 0 1 2 2v5"/><path d="m16 19 3 3 3-3"/><path d="M18 12h.01"/><path d="M19 16v6"/><path d="M6 12h.01"/><circle cx="12" cy="12" r="2"/></svg>
                   <span x-show="sidebarOpen" x-transition.opacity>Pengeluaran</span>
                </a>

                <!-- Audit Log Link -->
                <a href="{{ route($rolePrefix . '.audit-logs') }}" 
                   class="flex items-center gap-3.5 px-4 py-3 rounded-xl transition-all duration-200"
                   :class="activePage === 'audit-logs' ? 'bg-gradient-to-r from-indigo-600 to-violet-600 text-white font-semibold shadow-md shadow-indigo-900/30' : 'hover:bg-slate-800/60 hover:text-white'"
                   :title="!sidebarOpen ? 'Log Audit' : ''">
                    <i data-lucide="shield-check" class="w-5 h-5 flex-shrink-0"></i>
                    <span x-show="sidebarOpen" x-transition.opacity>Log Audit</span>
                </a>
                @endhasanyrole
                
                <!-- Pengaturan Link (Super Admin Only) -->
                @role('Super Admin')
                <a href="{{ route('admin.settings') }}" 
                   data-spa-ignore
                   class="flex items-center gap-3.5 px-4 py-3 rounded-xl transition-all duration-200"
                   :class="activePage === 'settings' ? 'bg-gradient-to-r from-indigo-600 to-violet-600 text-white font-semibold shadow-md shadow-indigo-900/30' : 'hover:bg-slate-800/60 hover:text-white'"
                   :title="!sidebarOpen ? 'Pengaturan' : ''">
                    <i data-lucide="settings" class="w-5 h-5 flex-shrink-0"></i>
                    <span x-show="sidebarOpen" x-transition.opacity>Pengaturan</span>
                </a>
                @endrole
            </nav>
            
            <!-- Sidebar Footer / UMKM Info -->
            <div class="absolute bottom-0 w-full p-4 border-t border-slate-800 bg-slate-950/40">
                <div class="flex items-center gap-3" :class="sidebarOpen ? 'justify-start' : 'justify-center'">
                    <div class="h-9 w-9 rounded-full bg-slate-800 flex items-center justify-center text-indigo-400 font-semibold border border-indigo-500/20 flex-shrink-0">
                        KB
                    </div>
                    <div class="overflow-hidden" x-show="sidebarOpen" x-transition.opacity>
                        <h4 class="text-sm font-semibold text-white truncate">Kios Berkah Raya</h4>
                        <p class="text-[10px] text-slate-400">UMKM Retail & Kelontong</p>
                    </div>
                </div>
            </div>
        </aside>
        @endunless
        <!-- Main Content Area -->
        <div class="flex flex-1 flex-col transition-all duration-300"
             @unless(auth()->user()->hasRole('Gudang')) :class="sidebarOpen ? 'md:ml-64' : 'md:ml-20'" @endunless>
            
            <!-- Topbar sticky header -->
            <header class="sticky top-0 z-30 flex h-16 items-center justify-between border-b border-indigo-500/20 bg-white/30 backdrop-blur-xl px-6 shadow-sm transition-all duration-300">
                
                <!-- Mobile Sidebar Toggle -->
                <div class="flex items-center gap-4 bg-transparent">
                    @unless(auth()->user()->hasRole('Gudang'))
                    <button class="rounded-xl p-2 text-slate-500 hover:text-slate-800 dark:text-slate-400 dark:hover:text-white md:hidden transition-all active:scale-95 hover:bg-slate-100 dark:hover:bg-slate-800" @click="sidebarOpen = !sidebarOpen">
                        <i data-lucide="menu" class="h-5 w-5"></i>
                    </button>
                    @endunless
                    <!-- Search Bar -->
                    <form action="{{ route($rolePrefix . '.inventory') }}" method="GET" class="hidden sm:flex items-center gap-2 rounded-xl px-3 py-1.5 w-64 text-slate-500 dark:text-slate-400 border border-indigo-500/60 focus-within:border-indigo-500/60 focus-within:ring-1 focus-within:ring-indigo-500/30 transition-all duration-200">
                        <i data-lucide="search" class="w-4 h-4 text-slate-400 dark:text-slate-500"></i>
                        <input type="text" name="search" value="{{ request('search') }}" placeholder="Search system database..." class="bg-transparent border-none focus:outline-none w-full  placeholder-slate-400 dark:placeholder-slate-650 font-mono">
                    </form>
                </div>
                
                <!-- Topbar Actions -->
                <div class="flex items-center gap-4">
                    
                    <!-- Connection Status Indicator -->
                    <div class="hidden md:flex items-center gap-2 px-2.5 py-1 rounded-md bg-emerald-50 dark:bg-emerald-500/10 border border-emerald-100 dark:border-emerald-500/20 text-[9px] font-mono font-semibold text-emerald-600 dark:text-emerald-400 tracking-wider uppercase shadow-[0_0_8px_rgba(16,185,129,0.05)]">
                        <span class="h-1.5 w-1.5 rounded-full bg-emerald-500 dark:bg-emerald-400 animate-pulse shadow-[0_0_8px_rgba(52,211,153,0.8)]"></span>
                        SYS: ACTIVE
                    </div>
                    
                    <!-- Notifications Dropdown -->
                    <div class="relative" x-data="{ open: false }">
                        <button @click="open = !open" @click.away="open = false" 
                                class="relative rounded-xl p-2 bg-white/20 border border-indigo-500/60 text-slate-500 dark:text-slate-400 transition-all duration-200">
                            <i data-lucide="bell" class="h-5 w-5"></i>
                            <!-- Badge -->
                            @if($unreadCount > 0)
                            <span class="absolute top-1 right-1 flex h-2 w-2">
                                <span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-rose-450 opacity-75"></span>
                                <span class="relative inline-flex rounded-full h-2 w-2 bg-rose-500 shadow-[0_0_6px_rgba(244,63,94,0.8)]"></span>
                            </span>
                            @endif
                        </button>
                        
                        <!-- Notifications List Menu -->
                        <div x-show="open" 
                             x-transition:enter="transition ease-out duration-200"
                             x-transition:enter-start="opacity-0 scale-95 translate-y-2"
                             x-transition:enter-end="opacity-100 scale-100 translate-y-0"
                             x-transition:leave="transition ease-in duration-75"
                             class="absolute right-0 mt-2 w-80 origin-top-right rounded-2xl bg-gradient-to-br from-[#1e1b4b] to-[#330854] border border-indigo-900/60 p-2 shadow-2xl z-50 text-slate-200">
                            <div class="px-4 py-2.5 border-b border-white/10 flex items-center justify-between">
                                <h3 class="font-bold text-xs uppercase tracking-wider text-slate-300 font-mono">System Alerts</h3>
                                <span class="text-[9px] font-mono font-semibold bg-rose-500/20 border border-rose-500/40 text-rose-350 px-2 py-0.5 rounded-full">{{ $unreadCount }} Alerts</span>
                            </div>
                            <div class="max-h-64 overflow-y-auto py-1">
                                @forelse ($notifications as $notification)
                                    <div class="group relative flex gap-3 px-4 py-3 hover:bg-white/10 rounded-xl transition-colors">
                                        <a href="{{ route($rolePrefix . '.inventory') }}" class="flex gap-3 flex-1">
                                            @if(($notification->data['type'] ?? '') === 'out_of_stock')
                                                <div class="p-2 bg-rose-500/10 text-rose-400 border border-rose-500/30 rounded-xl h-9 w-9 flex items-center justify-center flex-shrink-0">
                                                    <i data-lucide="x-circle" class="w-4 h-4"></i>
                                                </div>
                                            @else
                                                <div class="p-2 bg-amber-500/10 text-amber-400 border border-amber-500/30 rounded-xl h-9 w-9 flex items-center justify-center flex-shrink-0">
                                                    <i data-lucide="alert-triangle" class="w-4 h-4"></i>
                                                </div>
                                            @endif
                                            <div>
                                                <p class="text-xs font-bold text-white">
                                                    {{ ($notification->data['type'] ?? '') === 'out_of_stock' ? 'Stok Habis!' : 'Stok Menipis Terdeteksi!' }}
                                                </p>
                                                <p class="text-[10px] text-slate-350 mt-0.5 font-medium leading-normal">
                                                    {{ $notification->data['message'] ?? '' }}
                                                </p>
                                            </div>
                                        </a>
                                        
                                        <!-- Mark as Read Button -->
                                        <form action="{{ route('notifications.read', $notification->id) }}" method="POST" class="absolute right-2 top-2 opacity-0 group-hover:opacity-100 transition-opacity">
                                            @csrf
                                            <button type="submit" class="p-1 rounded-md text-slate-400 hover:text-white bg-slate-800 hover:bg-slate-700 transition-all" title="Tandai terbaca">
                                                <i data-lucide="check" class="w-3 h-3"></i>
                                            </button>
                                        </form>
                                    </div>
                                @empty
                                    <div class="px-4 py-6 text-center text-slate-400 text-xs">
                                        <i data-lucide="bell-off" class="w-6 h-6 mx-auto mb-2 text-slate-500"></i>
                                        Tidak ada notifikasi sistem baru.
                                    </div>
                                @endforelse
                            </div>
                            
                            @if($unreadCount > 0)
                                <div class="p-1.5 border-t border-white/5 text-center">
                                    <form action="{{ route('notifications.read-all') }}" method="POST">
                                        @csrf
                                        <button type="submit" class="w-full py-1.5 text-[10px] font-bold text-indigo-400 hover:text-indigo-300 hover:bg-white/5 rounded-lg transition-all">
                                            Tandai Semua Terbaca
                                        </button>
                                    </form>
                                </div>
                            @endif
                        </div>
                    </div>
                    
                    <!-- Profile Menu Dropdown -->
                    <div class="relative" x-data="{ open: false }">
                        <button @click="open = !open" @click.away="open = false" 
                                class="flex items-center gap-2.5 rounded-xl p-1.5 bg-white/20 border border-indigo-500/60 focus:outline-none transition-all duration-200">
                            <img class="h-8.5 w-8.5 rounded-lg object-cover border border-slate-200" 
                                 src="https://images.unsplash.com/photo-1534528741775-53994a69daeb?auto=format&fit=crop&q=80&w=100&h=100" 
                                  alt="Profile">
                            <div class="hidden lg:block text-left pr-2">
                                <h4 class="text-xs font-bold text-slate-800">{{ auth()->user()->name }}</h4>
                                <p class="text-[9px] text-indigo-600 font-mono tracking-wider font-semibold uppercase">{{ auth()->user()->roles->pluck('name')->implode(', ') }}</p>
                            </div>
                            <i data-lucide="chevron-down" class="w-3.5 h-3.5 text-slate-400"></i>
                        </button>
                        
                        <!-- Dropdown Menu -->
                        <div x-show="open" 
                             x-transition:enter="transition ease-out duration-200"
                             x-transition:enter-start="opacity-0 scale-95 translate-y-2"
                             x-transition:enter-end="opacity-100 scale-100 translate-y-0"
                             x-transition:leave="transition ease-in duration-75"
                             class="absolute right-0 mt-2 w-48 origin-top-right rounded-2xl bg-gradient-to-br from-[#1e1b4b] to-[#330854] border border-indigo-900/60 p-1.5 shadow-2xl z-50 text-slate-200">
                            
                            <a href="{{ route('profile') }}"
                               class="flex items-center gap-2.5 px-3 py-2.5 text-xs rounded-xl font-bold text-slate-200 hover:bg-white/10 hover:text-white transition-colors">
                                <i data-lucide="user" class="w-4 h-4 text-slate-350"></i>
                                Profil Saya
                            </a>
                            
                            @hasanyrole('Super Admin|Manager')
                            <a href="{{ route($rolePrefix . '.settings') }}"
                               class="flex items-center gap-2.5 px-3 py-2.5 text-xs rounded-xl font-bold text-slate-200 hover:bg-white/10 hover:text-white transition-colors">
                                <i data-lucide="settings" class="w-4 h-4 text-slate-355"></i>
                                Pengaturan
                            </a>
                            @endhasanyrole
                            
                            <hr class="my-1 border-white/10">
                            
                            <!-- Form Logout -->
                            <form id="logout-form" action="{{ route('logout') }}" method="POST" class="hidden">
                                @csrf
                            </form>
                            <a href="#" @click.prevent="document.getElementById('logout-form').submit()"
                               class="flex items-center gap-2.5 px-3 py-2.5 text-xs text-rose-450 hover:bg-rose-950/30 rounded-xl font-bold transition-colors">
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
    </div>
    
    @stack('scripts')

    <script>
        // Save initial state for browser navigation
        window.history.replaceState({ url: window.location.href }, document.title, window.location.href);

        // Intercept navigation clicks
        document.addEventListener('click', function(e) {
            const link = e.target.closest('a');
            if (!link) return;
            
            const href = link.getAttribute('href');
            if (!href) return;
            
            // Ignore hashes, javascript:, external links, logout, and data-spa-ignore links
            if (href.startsWith('#') || href.startsWith('javascript:') || href.includes('logout') || link.getAttribute('target') === '_blank' || link.hasAttribute('data-spa-ignore')) return;
            
            // Validate internal origin
            try {
                const url = new URL(link.href, window.location.href);
                if (url.origin !== window.location.origin) return;
                if (url.pathname === '/login' || url.pathname === '/logout') return;
                
                e.preventDefault();
                spaNavigate(url.href);
            } catch (err) {
                console.error(err);
            }
        });

        // Cache of already-loaded external script URLs
        const _loadedScripts = new Set();

        // Load an external script by URL, returns a Promise
        function loadExternalScript(src) {
            // If already loaded (or currently in page), resolve immediately
            if (_loadedScripts.has(src)) return Promise.resolve();
            // Also check if it already exists in the page <head>
            if (document.querySelector('script[src="' + src + '"]')) {
                _loadedScripts.add(src);
                return Promise.resolve();
            }
            return new Promise(function(resolve, reject) {
                const s = document.createElement('script');
                s.src = src;
                s.onload = function() { _loadedScripts.add(src); resolve(); };
                s.onerror = reject;
                document.head.appendChild(s);
            });
        }

        // Unwrap DOMContentLoaded listeners from inline script text so they run immediately
        function unwrapDCL(code) {
            // Pattern: document.addEventListener('DOMContentLoaded', function() { ... });
            const re = /document\.addEventListener\(\s*['"]DOMContentLoaded['"]\s*,\s*function\s*\(\s*\)\s*\{/;
            if (!re.test(code)) return code;
            // Remove the wrapper – find the opening and strip it, then remove the trailing });
            let unwrapped = code.replace(re, '(function(){');
            // The closing of the wrapper is    });   at the end – replace last }); with })();
            const lastIdx = unwrapped.lastIndexOf('});');
            if (lastIdx !== -1) {
                unwrapped = unwrapped.substring(0, lastIdx) + '})();';
            }
            return unwrapped;
        }

        // SPA Navigation Function
        function spaNavigate(url, pushState) {
            if (pushState === undefined) pushState = true;
            
            let progress = document.getElementById('spa-progressbar');
            if (!progress) {
                progress = document.createElement('div');
                progress.id = 'spa-progressbar';
                document.body.appendChild(progress);
            }
            progress.style.width = '10%';
            progress.style.opacity = '1';
            
            let w = 10;
            const interval = setInterval(function() {
                if (w < 80) {
                    w += 10;
                    progress.style.width = w + '%';
                }
            }, 100);
            
            axios.get(url)
                .then(function(response) {
                    clearInterval(interval);
                    progress.style.width = '100%';
                    
                    const parser = new DOMParser();
                    const doc = parser.parseFromString(response.data, 'text/html');
                    
                    // --- Clean up existing Alpine components inside <main> ---
                    const currentMain = document.querySelector('main');
                    if (currentMain && window.Alpine) {
                        // Destroy Alpine trees attached to old content
                        currentMain.querySelectorAll('[x-data]').forEach(function(el) {
                            if (el._x_dataStack) {
                                try { Alpine.destroyTree(el); } catch(e) {}
                            }
                        });
                    }
                    
                    // Swap main content
                    const newMain = doc.querySelector('main');
                    if (newMain && currentMain) {
                        currentMain.innerHTML = newMain.innerHTML;
                    }
                    
                    // Update Title
                    document.title = doc.title;
                    
                    // Update history
                    if (pushState) {
                        window.history.pushState({ url: url }, doc.title, url);
                    }
                    
                    // Update active page sidebar tab
                    const activePage = doc.body.getAttribute('data-active-page') || 'dashboard';
                    window.dispatchEvent(new CustomEvent('set-active-page', { detail: activePage }));
                    
                    // --- Execute scripts inside swapped content ---
                    // Separate external and inline scripts, maintain order
                    if (newMain) {
                        executeScriptsAsync(doc);
                    }
                    
                    setTimeout(function() {
                        progress.style.opacity = '0';
                        setTimeout(function() {
                            progress.style.width = '0%';
                        }, 300);
                    }, 100);
                })
                .catch(function(err) {
                    clearInterval(interval);
                    console.error('SPA load error, redirecting:', err);
                    window.location.href = url;
                });
        }

        // Async script executor – loads external scripts first, then runs inline scripts
        async function executeScriptsAsync(parsedDoc) {
            const scripts = Array.from(parsedDoc.querySelectorAll('script')).filter(function(script) {
                return script.type !== 'module'
                    && !script.innerHTML.includes('function spaNavigate(')
                    && !script.innerHTML.includes('window.history.replaceState');
            });
            const container = document.querySelector('main');
            
            // Phase 1: Load all external scripts (CDNs like Chart.js)
            const externalLoads = [];
            scripts.forEach(function(s) {
                if (s.src) {
                    externalLoads.push(loadExternalScript(s.src));
                }
            });
            // Wait for all external scripts to finish loading
            if (externalLoads.length > 0) {
                try { await Promise.all(externalLoads); } catch(e) { console.error('Failed to load external script:', e); }
            }

            // Phase 2: Run inline scripts (with DOMContentLoaded unwrapped)
            scripts.forEach(function(oldScript) {
                if (oldScript.src) return; // Skip externals, already loaded
                const newScript = document.createElement('script');
                Array.from(oldScript.attributes).forEach(function(attr) {
                    newScript.setAttribute(attr.name, attr.value);
                });
                // Unwrap DOMContentLoaded so the code executes immediately
                const code = unwrapDCL(oldScript.innerHTML);
                newScript.appendChild(document.createTextNode(code));
                document.body.appendChild(newScript);
            });

            // Phase 3: Initialize Alpine on new content AFTER inline scripts
            // This ensures x-data="someFunction()" can find the function definition
            if (window.Alpine) {
                Alpine.initTree(container);
            }

            // Re-create Lucide icons immediately (for sidebar & existing content)
            if (window.lucide) {
                window.lucide.createIcons();
            }

            // Phase 4: Re-create Lucide icons again after Alpine x-for/x-if templates render
            setTimeout(function() {
                if (window.lucide) {
                    window.lucide.createIcons();
                }
            }, 100);
        }

        // Popstate listener for back/forward browser actions
        window.addEventListener('popstate', function(event) {
            if (event.state && event.state.url) {
                spaNavigate(event.state.url, false);
            } else {
                spaNavigate(window.location.href, false);
            }
        });
    </script>
</body>
</html>
