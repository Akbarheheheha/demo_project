@csrf

<div class="grid grid-cols-1 md:grid-cols-2 gap-6">
    <!-- Full Name -->
    <div class="space-y-1.5">
        <label for="name" class="text-xs font-bold text-slate-500">Nama Lengkap</label>
        <div class="relative flex items-center bg-slate-100 rounded-xl px-3 py-2.5 border @error('name') border-rose-400 bg-rose-50/10 @else border-slate-100 focus-within:border-indigo-400 focus-within:bg-white @enderror transition-all duration-200">
            <input type="text" 
                   id="name" 
                   name="name" 
                   value="{{ old('name', $cashier->name ?? '') }}" 
                   required 
                   placeholder="Masukkan nama lengkap kasir" 
                   class="bg-transparent border-none text-xs font-semibold focus:outline-none w-full text-slate-800 placeholder-slate-400">
        </div>
        @error('name')
            <p class="text-[10px] text-rose-500 font-medium">{{ $message }}</p>
        @enderror
    </div>

    <!-- Email Address -->
    <div class="space-y-1.5">
        <label for="email" class="text-xs font-bold text-slate-500">Alamat Email</label>
        <div class="relative flex items-center bg-slate-100 rounded-xl px-3 py-2.5 border @error('email') border-rose-400 bg-rose-50/10 @else border-slate-100 focus-within:border-indigo-400 focus-within:bg-white @enderror transition-all duration-200">
            <input type="email" 
                   id="email" 
                   name="email" 
                   value="{{ old('email', $cashier->email ?? '') }}" 
                   required 
                   placeholder="contoh@domain.com" 
                   class="bg-transparent border-none text-xs font-semibold focus:outline-none w-full text-slate-800 placeholder-slate-400">
        </div>
        @error('email')
            <p class="text-[10px] text-rose-500 font-medium">{{ $message }}</p>
        @enderror
    </div>

    <!-- Password -->
    <div class="space-y-1.5">
        <label for="password" class="text-xs font-bold text-slate-500">
            Password 
            @if(isset($isEdit) && $isEdit)
                <span class="text-slate-400 font-normal">(Opsional, isi hanya untuk mengubah)</span>
            @endif
        </label>
        <div class="relative flex items-center bg-slate-100 rounded-xl px-3 py-2.5 border @error('password') border-rose-400 bg-rose-50/10 @else border-slate-100 focus-within:border-indigo-400 focus-within:bg-white @enderror transition-all duration-200">
            <input type="password" 
                   id="password" 
                   name="password" 
                   @if(!isset($isEdit) || !$isEdit) required @endif
                   placeholder="Minimal 8 karakter" 
                   class="bg-transparent border-none text-xs font-semibold focus:outline-none w-full text-slate-800 placeholder-slate-400">
        </div>
        @error('password')
            <p class="text-[10px] text-rose-500 font-medium">{{ $message }}</p>
        @enderror
    </div>

    <!-- Password Confirmation -->
    <div class="space-y-1.5">
        <label for="password_confirmation" class="text-xs font-bold text-slate-500">Konfirmasi Password</label>
        <div class="relative flex items-center bg-slate-100 rounded-xl px-3 py-2.5 border border-slate-100 focus-within:border-indigo-400 focus-within:bg-white transition-all duration-200">
            <input type="password" 
                   id="password_confirmation" 
                   name="password_confirmation" 
                   @if(!isset($isEdit) || !$isEdit) required @endif
                   placeholder="Ulangi password" 
                   class="bg-transparent border-none text-xs font-semibold focus:outline-none w-full text-slate-800 placeholder-slate-400">
        </div>
    </div>
</div>
