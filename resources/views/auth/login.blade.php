<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Masuk - SmartBiz ERP</title>
    
    <!-- Google Fonts: Outfit -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    
    <!-- Tailwind CSS & Vite Assets -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    
    <!-- Lucide Icons CDN -->
    <script src="https://unpkg.com/lucide@latest"></script>
    
    <style>
        body {
            font-family: 'Outfit', sans-serif;
        }
    </style>
</head>
<body class="bg-slate-900 text-slate-100 min-h-screen flex items-center justify-center p-4 relative overflow-hidden select-none"
      x-data="{ 
          email: '{{ old('email') }}', 
          password: '', 
          showPassword: false,
          fillDemo(roleEmail) {
              this.email = roleEmail;
              this.password = 'password';
              this.$dispatch('show-toast', { message: 'Kredensial demo ' + roleEmail + ' diisi!', type: 'info' });
          }
      }">

    <!-- Background decorative blur glows -->
    <div class="absolute -top-40 -left-40 w-96 h-96 bg-indigo-600/25 rounded-full filter blur-[100px] pointer-events-none"></div>
    <div class="absolute -bottom-40 -right-40 w-96 h-96 bg-violet-600/25 rounded-full filter blur-[100px] pointer-events-none"></div>
    <div class="absolute top-1/2 left-1/2 -translate-x-1/2 -translate-y-1/2 w-[500px] h-[500px] bg-slate-800/10 rounded-full filter blur-[120px] pointer-events-none"></div>

    <!-- Main Container Card -->
    <div class="w-full max-w-4xl bg-slate-950/40 backdrop-blur-xl rounded-3xl border border-slate-800/80 shadow-2xl overflow-hidden grid grid-cols-1 md:grid-cols-12 min-h-[500px] relative z-10">
        
        <!-- Left Side: Branding / Marketing (5 Cols) -->
        <div class="md:col-span-5 bg-gradient-to-br from-indigo-900 via-indigo-950 to-slate-950 p-8 flex flex-col justify-between border-r border-slate-800/40 relative overflow-hidden">
            <!-- Pattern overlay -->
            <div class="absolute inset-0 opacity-10 bg-[radial-gradient(#e2e8f0_1px,transparent_1px)] [background-size:16px_16px] pointer-events-none"></div>
            
            <!-- Logo Header -->
            <div class="flex items-center gap-2.5 relative z-10">
                <div class="flex h-10 w-10 items-center justify-center rounded-xl bg-gradient-to-tr from-indigo-500 to-violet-500 shadow-lg shadow-indigo-500/20 text-white font-black text-xl">
                    S
                </div>
                <div>
                    <h2 class="font-extrabold text-white tracking-wide text-md">SmartBiz ERP</h2>
                    <p class="text-[9px] text-indigo-400 font-semibold uppercase tracking-wider">Scale-Up UMKM</p>
                </div>
            </div>
            
            <!-- Welcome Info Section -->
            <div class="my-12 relative z-10 space-y-4">
                <span class="text-[10px] font-bold text-indigo-400 bg-indigo-950 border border-indigo-800/30 px-3 py-1 rounded-full uppercase tracking-wider">ERP & POS v1.1 (RBAC)</span>
                <h1 class="text-2xl md:text-3xl font-black text-white leading-tight">Kelola Bisnis UMKM Lebih Efisien.</h1>
                <p class="text-xs text-slate-400 leading-relaxed">Satu sistem terintegrasi untuk kasir POS penjualan, manajemen stok inventaris gudang, hingga laporan bisnis otomatis.</p>
            </div>
            
            <!-- Branding Footer stats -->
            <div class="relative z-10 pt-4 border-t border-slate-800 flex justify-between items-center gap-4">
                <div>
                    <h4 class="text-xs font-bold text-white">100% Aman</h4>
                    <p class="text-[9px] text-slate-500">Cloud Sync & Backup</p>
                </div>
                <div class="text-right">
                    <h4 class="text-xs font-bold text-white">UMKM Go-Digital</h4>
                    <p class="text-[9px] text-slate-500">Karya Anak Bangsa</p>
                </div>
            </div>
        </div>

        <!-- Right Side: Login Form (7 Cols) -->
        <div class="md:col-span-7 p-8 md:p-12 flex flex-col justify-center bg-slate-950/20">
            
            <!-- Form Title -->
            <div class="mb-6">
                <h2 class="text-xl font-bold text-white">Selamat Datang Kembali!</h2>
                <p class="text-xs text-slate-400 mt-1">Silakan masuk menggunakan akun administrasi toko Anda.</p>
            </div>
            
            <!-- Laravel Form Flash Messages -->
            @if(session('login_required'))
                <div class="mb-5 p-3 rounded-2xl bg-amber-950/40 border border-amber-900/30 text-amber-300 text-xs flex items-center gap-2">
                    <i data-lucide="alert-circle" class="w-4 h-4 flex-shrink-0"></i>
                    <span>{{ session('login_required') }}</span>
                </div>
            @endif

            @if(session('logout_success'))
                <div class="mb-5 p-3 rounded-2xl bg-emerald-950/40 border border-emerald-900/30 text-emerald-300 text-xs flex items-center gap-2">
                    <i data-lucide="check-circle" class="w-4 h-4 flex-shrink-0"></i>
                    <span>{{ session('logout_success') }}</span>
                </div>
            @endif
            
            <!-- Login Form Form -->
            <form action="{{ url('/login') }}" method="POST" class="space-y-4">
                @csrf
                
                <!-- Email Input -->
                <div class="space-y-1.5">
                    <label for="email" class="text-[11px] font-bold text-slate-400 uppercase tracking-wider">Alamat Email</label>
                    <div class="flex items-center bg-slate-900/80 rounded-xl px-3.5 py-3 border border-slate-800 focus-within:border-indigo-500 focus-within:ring-2 focus-within:ring-indigo-900/20 transition-all duration-200">
                        <i data-lucide="mail" class="w-4 h-4 text-slate-500 mr-2.5"></i>
                        <input type="email" 
                                id="email" 
                                name="email" 
                                x-model="email"
                                placeholder="contoh@demo.com" 
                                required
                                class="bg-transparent border-none text-xs focus:outline-none w-full text-slate-200">
                    </div>
                    @error('email')
                        <p class="text-[10px] text-rose-500 font-medium mt-1">{{ $message }}</p>
                    @enderror
                </div>
                
                <!-- Password Input -->
                <div class="space-y-1.5">
                    <label for="password" class="text-[11px] font-bold text-slate-400 uppercase tracking-wider">Kata Sandi</label>
                    <div class="flex items-center bg-slate-900/80 rounded-xl px-3.5 py-3 border border-slate-800 focus-within:border-indigo-500 focus-within:ring-2 focus-within:ring-indigo-900/20 transition-all duration-200">
                        <i data-lucide="lock" class="w-4 h-4 text-slate-500 mr-2.5"></i>
                        <input :type="showPassword ? 'text' : 'password'" 
                                id="password" 
                                name="password" 
                                x-model="password"
                                placeholder="******" 
                                required
                                class="bg-transparent border-none text-xs focus:outline-none w-full text-slate-200">
                        <!-- Toggle show password -->
                        <button type="button" @click="showPassword = !showPassword" class="text-slate-500 hover:text-slate-300 transition-colors focus:outline-none">
                            <i :data-lucide="showPassword ? 'eye-off' : 'eye'" class="w-4 h-4"></i>
                        </button>
                    </div>
                    @error('password')
                        <p class="text-[10px] text-rose-500 font-medium mt-1">{{ $message }}</p>
                    @enderror
                </div>
                
                <!-- Remember me & forgot password -->
                <div class="flex items-center justify-between text-xs pt-1">
                    <label class="flex items-center gap-2 cursor-pointer text-slate-400 hover:text-slate-200 transition-colors">
                        <input type="checkbox" name="remember" class="rounded border-slate-800 bg-slate-900 text-indigo-600 focus:ring-indigo-500">
                        <span>Ingat Saya</span>
                    </label>
                    
                    <a href="#" @click.prevent="$dispatch('show-toast', { message: 'Fitur lupa password tidak tersedia di demo UI.', type: 'warning' })"
                       class="text-indigo-400 hover:text-indigo-300 font-bold transition-colors">
                        Lupa Sandi?
                    </a>
                </div>
                
                <!-- Submit Button -->
                <button type="submit" 
                        class="w-full py-3.5 bg-gradient-to-r from-indigo-600 to-violet-600 text-white font-bold rounded-xl text-xs hover:shadow-lg hover:shadow-indigo-600/20 active:scale-[0.98] transition-all duration-200 mt-2 flex items-center justify-center gap-2">
                    <i data-lucide="log-in" class="w-4 h-4"></i>
                    <span>Masuk Sistem</span>
                </button>
            </form>
            
            <!-- Demo Accounts Helper Info Card -->
            <div class="mt-6 p-4 bg-slate-900/60 border border-slate-850 rounded-2xl">
                <h4 class="text-xs font-bold text-indigo-300 flex items-center gap-1.5 mb-2">
                    <i data-lucide="info" class="w-4 h-4 text-indigo-400"></i>
                    Akun Demo Pengujian (Sandi: password)
                </h4>
                <div class="grid grid-cols-3 gap-2">
                    <button @click="fillDemo('admin@demo.com')" class="px-2 py-1.5 bg-slate-850 hover:bg-slate-800 text-[10px] font-bold rounded-lg text-slate-200 border border-slate-800 hover:border-indigo-500 transition-all">
                        Super Admin
                    </button>
                    <button @click="fillDemo('manager@demo.com')" class="px-2 py-1.5 bg-slate-850 hover:bg-slate-800 text-[10px] font-bold rounded-lg text-slate-200 border border-slate-800 hover:border-indigo-500 transition-all">
                        Manager
                    </button>
                    <button @click="fillDemo('kasir@demo.com')" class="px-2 py-1.5 bg-slate-850 hover:bg-slate-800 text-[10px] font-bold rounded-lg text-slate-200 border border-slate-800 hover:border-indigo-500 transition-all">
                        Kasir
                    </button>
                </div>
            </div>
            
        </div>
        
    </div>

    <!-- Toast Notification Container for Login Page -->
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
                 class="p-4 rounded-xl shadow-xl flex items-center gap-3 pointer-events-auto border bg-slate-900 text-slate-200"
                 :class="{
                    'border-emerald-500/30 text-emerald-300': toast.type === 'success',
                    'border-amber-500/30 text-amber-300': toast.type === 'warning',
                    'border-rose-500/30 text-rose-300': toast.type === 'danger',
                    'border-blue-500/30 text-blue-300': toast.type === 'info'
                 }">
                <div class="p-1.5 rounded-lg bg-slate-800" 
                     :class="{
                        'text-emerald-500': toast.type === 'success',
                        'text-amber-500': toast.type === 'warning',
                        'text-rose-500': toast.type === 'danger',
                        'text-blue-500': toast.type === 'info'
                     }">
                    <i class="w-5 h-5" :data-lucide="toast.type === 'success' ? 'check-circle' : (toast.type === 'warning' ? 'alert-triangle' : (toast.type === 'danger' ? 'x-circle' : 'info'))"></i>
                </div>
                <div class="flex-1 text-xs font-semibold" x-text="toast.message"></div>
                <button @click="toasts = toasts.filter(t => t.id !== toast.id)" class="text-slate-500 hover:text-slate-300">
                    <i data-lucide="x" class="w-4 h-4"></i>
                </button>
            </div>
        </template>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            lucide.createIcons();
        });
        document.addEventListener('alpine:initialized', () => {
            lucide.createIcons();
        });
    </script>
</body>
</html>
