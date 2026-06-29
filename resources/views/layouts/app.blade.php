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
    </style>
</head>
<body class="bg-slate-50 text-slate-800 antialiased font-sans" 
      data-active-page="@yield('active_page', 'dashboard')"
      x-data="{ sidebarOpen: false, activePage: '@yield('active_page', 'dashboard')' }"
      @set-active-page.window="activePage = $event.detail"
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
                @hasanyrole('Super Admin|Manager')
                <a href="{{ route('admin.dashboard') }}" 
                   class="flex items-center gap-3.5 px-4 py-3 rounded-xl transition-all duration-200"
                   :class="activePage === 'dashboard' ? 'bg-gradient-to-r from-indigo-600 to-violet-600 text-white font-semibold shadow-md shadow-indigo-900/30' : 'hover:bg-slate-800/60 hover:text-white'">
                    <i data-lucide="layout-dashboard" class="w-5 h-5"></i>
                    <span>Dashboard</span>
                </a>
                @endhasanyrole
                
                <!-- Kasir / POS Link -->
                @hasanyrole('Kasir|Super Admin')
                <a href="{{ route('pos') }}" 
                   class="flex items-center gap-3.5 px-4 py-3 rounded-xl transition-all duration-200"
                   :class="activePage === 'pos' ? 'bg-gradient-to-r from-indigo-600 to-violet-600 text-white font-semibold shadow-md shadow-indigo-900/30' : 'hover:bg-slate-800/60 hover:text-white'">
                    <i data-lucide="shopping-cart" class="w-5 h-5"></i>
                    <span>Kasir POS</span>
                </a>
                @endhasanyrole
                
                <!-- Inventaris Link -->
                @hasanyrole('Super Admin|Manager')
                <a href="{{ route('inventory') }}" 
                   data-spa-ignore
                   class="flex items-center gap-3.5 px-4 py-3 rounded-xl transition-all duration-200"
                   :class="activePage === 'inventory' ? 'bg-gradient-to-r from-indigo-600 to-violet-600 text-white font-semibold shadow-md shadow-indigo-900/30' : 'hover:bg-slate-800/60 hover:text-white'">
                    <i data-lucide="package" class="w-5 h-5"></i>
                    <span>Inventaris</span>
                </a>
                @endhasanyrole
                
                <!-- Laporan Link (Super Admin Only) -->
                <!-- @role('Super Admin')
                <a href="{{ route('reports') }}" 
                   class="flex items-center gap-3.5 px-4 py-3 rounded-xl transition-all duration-200"
                   :class="activePage === 'reports' ? 'bg-gradient-to-r from-indigo-600 to-violet-600 text-white font-semibold shadow-md shadow-indigo-900/30' : 'hover:bg-slate-800/60 hover:text-white' ">
                    <i data-lucide="bar-chart-3" class="w-5 h-5"></i>
                    <span>Laporan Keuangan</span>
                </a>
                @endrole -->
                
                <!-- Pengaturan Link (Super Admin Only) -->
                @role('Super Admin')
                <a href="{{ route('settings') }}" 
                   data-spa-ignore
                   class="flex items-center gap-3.5 px-4 py-3 rounded-xl transition-all duration-200"
                   :class="activePage === 'settings' ? 'bg-gradient-to-r from-indigo-600 to-violet-600 text-white font-semibold shadow-md shadow-indigo-900/30' : 'hover:bg-slate-800/60 hover:text-white'">
                    <i data-lucide="settings" class="w-5 h-5"></i>
                    <span>Pengaturan</span>
                </a>
                @endrole
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
                    
                    <!-- Search Bar -->
                    <form action="{{ route('inventory') }}" method="GET" class="hidden sm:flex items-center gap-2 bg-slate-100 rounded-xl px-3 py-1.5 w-64 text-slate-500 border border-slate-100 hover:border-slate-200 transition-colors">
                        <i data-lucide="search" class="w-4 h-4"></i>
                        <input type="text" name="search" value="{{ request('search') }}" placeholder="Cari barang berdasarkan nama atau SKU..." class="bg-transparent border-none text-xs focus:outline-none w-full text-slate-700">
                    </form>
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
                                <h4 class="text-xs font-semibold text-slate-800">{{ auth()->user()->name }}</h4>
                                <p class="text-[10px] text-slate-500 font-medium">{{ auth()->user()->roles->pluck('name')->implode(', ') }}</p>
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
                            
                            <a href="{{ route('settings') }}"
                               class="flex items-center gap-2.5 px-3 py-2.5 text-xs text-slate-700 hover:bg-slate-50 rounded-xl transition-colors">
                                <i data-lucide="settings" class="w-4 h-4 text-slate-400"></i>
                                Pengaturan
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
            const scripts = Array.from(parsedDoc.querySelectorAll('script'));
            
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

            // Phase 2: Initialize Alpine on new content BEFORE running inline scripts
            // This ensures x-data components are alive and functional
            if (window.Alpine) {
                Alpine.initTree(container);
            }

            // Phase 3: Run inline scripts (with DOMContentLoaded unwrapped)
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
