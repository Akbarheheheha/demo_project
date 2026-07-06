@csrf

<div class="grid grid-cols-1 gap-6">
    <!-- Category Name -->
    <div class="space-y-1.5">
        <label for="name" class="text-xs font-bold text-slate-500">Nama Kategori</label>
        <div class="relative flex items-center bg-slate-100 rounded-xl px-3 py-2.5 border @error('name') border-rose-400 bg-rose-50/10 @else border-slate-100 focus-within:border-indigo-400 focus-within:bg-white @enderror transition-all duration-200">
            <input type="text" 
                   id="name" 
                   name="name" 
                   value="{{ old('name', $category->name ?? '') }}" 
                   required 
                   placeholder="Contoh: Makanan Ringan, Alat Tulis" 
                   class="bg-transparent border-none text-xs font-semibold focus:outline-none w-full text-slate-800 placeholder-slate-400">
        </div>
        @error('name')
            <p class="text-[10px] text-rose-500 font-medium">{{ $message }}</p>
        @enderror
    </div>
</div>
