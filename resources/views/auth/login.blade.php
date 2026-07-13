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
<body class="bg-gradient-to-br from-slate-100 via-indigo-50/20 to-violet-100/30 text-slate-800 min-h-screen flex items-center justify-center p-4 relative overflow-hidden select-none"
      x-data="{ 
          email: '{{ old('email') }}', 
          password: '', 
          showPassword: false,
          fillDemoAndSubmit(roleEmail) {
              this.email = roleEmail;
              this.password = 'password';
              this.$dispatch('show-toast', { message: 'Menghubungkan sebagai ' + roleEmail + '...', type: 'info' });
              this.$nextTick(() => {
                  document.querySelector('form').submit();
              });
          }
      }">

    <!-- Background decorative blur glows (subtle light-themed) -->
    <div class="absolute -top-40 -left-40 w-96 h-96 bg-indigo-200/40 rounded-full filter blur-[100px] pointer-events-none"></div>
    <div class="absolute -bottom-40 -right-40 w-96 h-96 bg-violet-200/40 rounded-full filter blur-[100px] pointer-events-none"></div>

    <!-- Main Container Card -->
    <div class="w-full max-w-md bg-white rounded-3xl border border-slate-200/80 shadow-2xl p-8 relative z-10">
        
        <!-- Header -->
        <div class="flex flex-col items-center mb-8">
            <div class="flex h-12 w-12 items-center justify-center rounded-2xl bg-gradient-to-tr from-indigo-600 to-violet-600 shadow-lg shadow-indigo-500/20 text-white font-black text-2xl mb-4">
                S
            </div>
            <h1 class="text-2xl font-extrabold text-slate-900 tracking-tight">SmartBiz ERP</h1>
            <p class="text-xs text-slate-500 mt-1.5 text-center">Silakan masuk menggunakan akun administrasi toko Anda.</p>
        </div>
        
        <!-- Laravel Form Flash Messages -->
        @if(session('login_required'))
            <div class="mb-5 p-3.5 rounded-2xl bg-amber-50 border border-amber-200 text-amber-800 text-xs flex items-center gap-2.5">
                <i data-lucide="alert-circle" class="w-4 h-4 text-amber-600 flex-shrink-0"></i>
                <span class="font-medium">{{ session('login_required') }}</span>
            </div>
        @endif

        @if(session('logout_success'))
            <div class="mb-5 p-3.5 rounded-2xl bg-emerald-50 border border-emerald-200 text-emerald-800 text-xs flex items-center gap-2.5">
                <i data-lucide="check-circle" class="w-4 h-4 text-emerald-600 flex-shrink-0"></i>
                <span class="font-medium">{{ session('logout_success') }}</span>
            </div>
        @endif
        
        <!-- Login Form -->
        <form action="{{ url('/login') }}" method="POST" class="space-y-4">
            @csrf
            
            <!-- Email Input -->
            <div class="space-y-1.5">
                <label for="email" class="text-[11px] font-bold text-slate-500 uppercase tracking-wider block mb-1.5">Alamat Email</label>
                <div class="flex items-center bg-slate-50 rounded-xl px-4 py-3 border border-slate-200 focus-within:border-indigo-600 focus-within:ring-4 focus-within:ring-indigo-100 transition-all duration-200">
                    <i data-lucide="mail" class="w-4 h-4 text-slate-400 mr-2.5"></i>
                    <input type="email" 
                            id="email" 
                            name="email" 
                            x-model="email"
                            placeholder="contoh@demo.com" 
                            required
                            class="bg-transparent border-none text-sm focus:outline-none w-full text-slate-800 placeholder-slate-400 font-medium">
                </div>
                @error('email')
                    <p class="text-[10px] text-rose-600 font-medium mt-1">{{ $message }}</p>
                @enderror
            </div>
            
            <!-- Password Input -->
            <div class="space-y-1.5">
                <label for="password" class="text-[11px] font-bold text-slate-500 uppercase tracking-wider block mb-1.5">Kata Sandi</label>
                <div class="flex items-center bg-slate-50 rounded-xl px-4 py-3 border border-slate-200 focus-within:border-indigo-600 focus-within:ring-4 focus-within:ring-indigo-100 transition-all duration-200">
                    <i data-lucide="lock" class="w-4 h-4 text-slate-400 mr-2.5"></i>
                    <input :type="showPassword ? 'text' : 'password'" 
                            id="password" 
                            name="password" 
                            x-model="password"
                            placeholder="******" 
                            required
                            class="bg-transparent border-none text-sm focus:outline-none w-full text-slate-800 placeholder-slate-400 font-medium">
                    <!-- Toggle show password -->
                    <button type="button" @click="showPassword = !showPassword" class="text-slate-400 hover:text-slate-600 transition-colors focus:outline-none flex items-center justify-center p-1 cursor-pointer">
                        <span x-show="!showPassword" class="flex"><i data-lucide="eye" class="w-4 h-4"></i></span>
                        <span x-show="showPassword" class="flex"><i data-lucide="eye-off" class="w-4 h-4"></i></span>
                    </button>
                </div>
                @error('password')
                    <p class="text-[10px] text-rose-600 font-medium mt-1">{{ $message }}</p>
                @enderror
            </div>
            
            <!-- Remember me & forgot password -->
            <div class="flex items-center justify-between text-xs pt-1.5">
                <label class="flex items-center gap-2 cursor-pointer text-slate-600 hover:text-slate-800 transition-colors">
                    <input type="checkbox" name="remember" class="rounded border-slate-350 bg-white text-indigo-600 focus:ring-indigo-500">
                    <span>Ingat Saya</span>
                </label>
                
                <a href="#" @click.prevent="$dispatch('show-toast', { message: 'Fitur lupa password tidak tersedia di demo UI.', type: 'warning' })"
                   class="text-indigo-600 hover:text-indigo-700 font-bold transition-colors">
                    Lupa Sandi?
                </a>
            </div>
            
            <!-- Submit Button -->
            <button type="submit" 
                    class="w-full py-3.5 bg-gradient-to-r from-indigo-600 to-violet-600 text-white font-bold rounded-xl text-sm hover:shadow-lg hover:shadow-indigo-600/25 active:scale-[0.98] transition-all duration-200 mt-4 flex items-center justify-center gap-2 cursor-pointer">
                <i data-lucide="log-in" class="w-4 h-4"></i>
                <span>Masuk Sistem</span>
            </button>
        </form>

        <!-- Quick Demo Accounts -->
        <div class="mt-8 pt-6 border-t border-slate-100">
            <p class="text-[10px] font-bold text-slate-400 uppercase tracking-wider text-center mb-3">Akun Demo (Klik untuk Masuk)</p>
            <div class="grid grid-cols-2 gap-2">
                <button type="button" @click="fillDemoAndSubmit('admin@demo.com')"
                        class="py-2 px-3 bg-slate-50 hover:bg-indigo-50 hover:text-indigo-600 border border-slate-200 hover:border-indigo-200 rounded-xl text-xs font-semibold text-slate-700 text-center transition-all duration-200 cursor-pointer">
                    Super Admin
                </button>
                <button type="button" @click="fillDemoAndSubmit('manager@demo.com')"
                        class="py-2 px-3 bg-slate-50 hover:bg-indigo-50 hover:text-indigo-600 border border-slate-200 hover:border-indigo-200 rounded-xl text-xs font-semibold text-slate-700 text-center transition-all duration-200 cursor-pointer">
                    Manager
                </button>
                <button type="button" @click="fillDemoAndSubmit('kasir@demo.com')"
                        class="py-2 px-3 bg-slate-50 hover:bg-indigo-50 hover:text-indigo-600 border border-slate-200 hover:border-indigo-200 rounded-xl text-xs font-semibold text-slate-700 text-center transition-all duration-200 cursor-pointer">
                    Kasir POS
                </button>
                <button type="button" @click="fillDemoAndSubmit('gudang@demo.com')"
                        class="py-2 px-3 bg-slate-50 hover:bg-indigo-50 hover:text-indigo-600 border border-slate-200 hover:border-indigo-200 rounded-xl text-xs font-semibold text-slate-700 text-center transition-all duration-200 cursor-pointer">
                    Staff Gudang
                </button>
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
                 class="p-4 rounded-xl shadow-xl flex items-center gap-3 pointer-events-auto border bg-white text-slate-800"
                 :class="{
                    'border-emerald-200 text-emerald-800 bg-emerald-50/90': toast.type === 'success',
                    'border-amber-200 text-amber-800 bg-amber-50/90': toast.type === 'warning',
                    'border-rose-200 text-rose-800 bg-rose-50/90': toast.type === 'danger',
                    'border-blue-200 text-blue-800 bg-blue-50/90': toast.type === 'info'
                 }">
                <div class="p-1.5 rounded-lg bg-white shadow-xs" 
                     :class="{
                        'text-emerald-600': toast.type === 'success',
                        'text-amber-600': toast.type === 'warning',
                        'text-rose-600': toast.type === 'danger',
                        'text-blue-600': toast.type === 'info'
                     }">
                    <i class="w-5 h-5" :data-lucide="toast.type === 'success' ? 'check-circle' : (toast.type === 'warning' ? 'alert-triangle' : (toast.type === 'danger' ? 'x-circle' : 'info'))"></i>
                </div>
                <div class="flex-1 text-xs font-semibold text-slate-800" x-text="toast.message"></div>
                <button @click="toasts = toasts.filter(t => t.id !== toast.id)" class="text-slate-400 hover:text-slate-600">
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
