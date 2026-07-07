@extends('layouts.app')

@section('title', 'Pengaturan Sistem')
@section('active_page', 'settings')

@section('content')
<div class="space-y-6" x-data="settingsComponent()">

    <!-- Page Header -->
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
        <div>
            <h2 class="text-2xl font-bold text-slate-800">Pengaturan ERP & POS</h2>
            <p class="text-sm text-slate-500">Konfigurasi informasi toko, default tarif pajak kasir, dan kelola hak akses masuk karyawan.</p>
        </div>
    </div>

    <!-- Navigation Tabs (Sleek pills layout) -->
    <div class="flex border-b border-slate-200 gap-1.5 overflow-x-auto pb-0.5">
        <!-- Profile Tab button -->
        <button @click="activeTab = 'profile'"
                :class="activeTab === 'profile' ? 'border-indigo-600 text-indigo-600 font-bold border-b-2' : 'border-transparent text-slate-500 hover:text-slate-700'"
                class="px-4 py-2.5 text-xs font-semibold whitespace-nowrap transition-all duration-150">
            Profil Toko
        </button>
        <!-- POS Configuration Tab button -->
        <button @click="activeTab = 'pos'"
                :class="activeTab === 'pos' ? 'border-indigo-600 text-indigo-600 font-bold border-b-2' : 'border-transparent text-slate-500 hover:text-slate-700'"
                class="px-4 py-2.5 text-xs font-semibold whitespace-nowrap transition-all duration-150">
            Sistem POS & Keuangan
        </button>
        <!-- Users & Access Permissions Tab button -->
        <button @click="activeTab = 'users'"
                :class="activeTab === 'users' ? 'border-indigo-600 text-indigo-600 font-bold border-b-2' : 'border-transparent text-slate-500 hover:text-slate-700'"
                class="px-4 py-2.5 text-xs font-semibold whitespace-nowrap transition-all duration-150">
            Pengguna & Hak Akses
        </button>
    </div>

    <!-- Tab Contents Panels -->
    <div class="mt-4">
        
        <!-- PANEL A: Profil Toko -->
        <div x-show="activeTab === 'profile'" class="bg-white p-6 rounded-2xl border border-slate-200/80 shadow-sm space-y-6">
            <div class="border-b border-slate-100 pb-3">
                <h3 class="font-bold text-slate-800 text-base">Informasi Identitas Toko</h3>
                <p class="text-xs text-slate-400">Data ini akan dicantumkan pada bagian atas struk kasir dan faktur penjualan.</p>
            </div>
            
            <form @submit.prevent="saveSettings('profile')" class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <!-- Store Name -->
                <div class="space-y-1.5">
                    <label class="text-xs font-bold text-slate-500">Nama Toko / UMKM</label>
                    <input type="text" x-model="store.name" required class="w-full text-xs bg-slate-100 rounded-xl px-3 py-2.5 border border-slate-100 focus:outline-none focus:border-indigo-500 focus:bg-white text-slate-800 font-semibold">
                </div>
                
                <!-- Store Phone -->
                <div class="space-y-1.5">
                    <label class="text-xs font-bold text-slate-500">No. Telepon / WhatsApp</label>
                    <input type="text" x-model="store.phone" required class="w-full text-xs bg-slate-100 rounded-xl px-3 py-2.5 border border-slate-100 focus:outline-none focus:border-indigo-500 focus:bg-white text-slate-800">
                </div>

                <!-- Store Email -->
                <div class="space-y-1.5">
                    <label class="text-xs font-bold text-slate-500">Email Kontak</label>
                    <input type="email" x-model="store.email" required class="w-full text-xs bg-slate-100 rounded-xl px-3 py-2.5 border border-slate-100 focus:outline-none focus:border-indigo-500 focus:bg-white text-slate-800">
                </div>

                <!-- Store Address -->
                <div class="space-y-1.5 md:col-span-2">
                    <label class="text-xs font-bold text-slate-500">Alamat Lengkap Toko</label>
                    <textarea x-model="store.address" required rows="3" class="w-full text-xs bg-slate-100 rounded-xl px-3 py-2.5 border border-slate-100 focus:outline-none focus:border-indigo-500 focus:bg-white text-slate-800 leading-relaxed"></textarea>
                </div>
                
                <hr class="md:col-span-2 border-slate-100">
                
                <!-- Receipt Header template info -->
                <div class="space-y-1.5">
                    <label class="text-xs font-bold text-slate-500">Header Struk Kasir (Plain Text)</label>
                    <textarea x-model="store.receipt_header" rows="3" class="w-full text-xs bg-slate-100 rounded-xl px-3 py-2.5 border border-slate-100 focus:outline-none focus:border-indigo-500 focus:bg-white text-slate-800 font-mono"></textarea>
                    <p class="text-[10px] text-slate-400">Gunakan \n untuk memisahkan baris baru.</p>
                </div>

                <!-- Receipt Footer template info -->
                <div class="space-y-1.5">
                    <label class="text-xs font-bold text-slate-500">Footer Struk Kasir (Plain Text)</label>
                    <textarea x-model="store.receipt_footer" rows="3" class="w-full text-xs bg-slate-100 rounded-xl px-3 py-2.5 border border-slate-100 focus:outline-none focus:border-indigo-500 focus:bg-white text-slate-800 font-mono"></textarea>
                    <p class="text-[10px] text-slate-400">Kalimat penutup atau syarat retur barang.</p>
                </div>
                
                <!-- Submit profile button -->
                <div class="md:col-span-2 pt-2 flex justify-end">
                    <button type="submit" :disabled="isSaving" class="bg-indigo-600 hover:bg-indigo-700 text-white font-bold px-5 py-3 rounded-2xl text-xs flex items-center gap-2 shadow-md shadow-indigo-600/10 active:scale-[0.98] transition-all">
                        <svg x-show="isSaving" class="animate-spin -ml-1 mr-2 h-4 w-4 text-white" fill="none" viewBox="0 0 24 24">
                            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                        </svg>
                        <i x-show="!isSaving" data-lucide="save" class="w-4 h-4"></i>
                        <span x-text="isSaving ? 'Menyimpan...' : 'Simpan Profil Toko'"></span>
                    </button>
                </div>
            </form>
        </div>
        
        <!-- PANEL B: Sistem POS & Keuangan -->
        <div x-show="activeTab === 'pos'" class="bg-white p-6 rounded-2xl border border-slate-200/80 shadow-sm space-y-6" style="display: none;">
            <div class="border-b border-slate-100 pb-3">
                <h3 class="font-bold text-slate-800 text-base">Konfigurasi Modul Kasir & Transaksi</h3>
                <p class="text-xs text-slate-400">Atur perpajakan default, ukuran pencetakan struk thermal, dan metode transaksi kasir.</p>
            </div>
            
            <form @submit.prevent="saveSettings('pos')" class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <!-- Tax Percentage default -->
                <div class="space-y-1.5">
                    <label class="text-xs font-bold text-slate-500">Tarif PPN Default (%)</label>
                    <div class="flex items-center bg-slate-100 rounded-xl px-3 py-2 border border-slate-100 focus-within:border-indigo-400 focus-within:bg-white transition-all duration-200">
                        <input type="number" x-model="pos.tax_percent" required class="bg-transparent border-none text-xs font-bold focus:outline-none w-full text-slate-800">
                        <span class="text-xs font-bold text-slate-400 ml-1.5">%</span>
                    </div>
                </div>

                <!-- Default Discount percentage -->
                <div class="space-y-1.5">
                    <label class="text-xs font-bold text-slate-500">Persentase Diskon Default (%)</label>
                    <div class="flex items-center bg-slate-100 rounded-xl px-3 py-2 border border-slate-100 focus-within:border-indigo-400 focus-within:bg-white transition-all duration-200">
                        <input type="number" x-model="pos.default_discount" required class="bg-transparent border-none text-xs font-bold focus:outline-none w-full text-slate-800">
                        <span class="text-xs font-bold text-slate-400 ml-1.5">%</span>
                    </div>
                </div>

                <!-- Receipt print size dropdown -->
                <div class="space-y-1.5">
                    <label class="text-xs font-bold text-slate-500">Ukuran Kertas Struk (Printer Thermal)</label>
                    <select x-model="pos.receipt_size" class="w-full text-xs rounded-xl border border-slate-200 bg-white p-2.5 text-slate-800 focus:outline-none focus:border-indigo-500 font-semibold">
                        <option value="58mm">58 mm (Standar Kecil)</option>
                        <option value="80mm">80 mm (Standar Lebar)</option>
                        <option value="A4">A4 / Letter (Faktur Kertas)</option>
                    </select>
                </div>

                <!-- Payment Methods Section (Dynamic CRUD) -->
                <div class="space-y-4 md:col-span-2 bg-slate-50/50 p-5 rounded-2xl border border-slate-200/60">
                    <div>
                        <label class="text-xs font-bold text-slate-700 block mb-1">Master Metode Pembayaran</label>
                        <p class="text-[11px] text-slate-400">Kelola opsi pembayaran yang tersedia pada sistem POS secara dinamis.</p>
                    </div>

                    <!-- Input for New Payment Method -->
                    <div class="flex items-center gap-2 max-w-md">
                        <input type="text" 
                               x-model="newPaymentMethod" 
                               @keydown.enter.prevent="addPaymentMethod()"
                               placeholder="Tambah metode baru (misal: ShopeePay, OVO)..." 
                               class="w-full text-xs bg-white rounded-xl px-3.5 py-2.5 border border-slate-200 focus:outline-none focus:border-indigo-500 text-slate-800 font-medium">
                        
                        <button type="button" 
                                @click="addPaymentMethod()" 
                                class="bg-indigo-600 hover:bg-indigo-700 text-white font-bold px-4 py-2.5 rounded-xl text-xs whitespace-nowrap active:scale-[0.98] transition-all flex items-center gap-1.5 shadow-md shadow-indigo-600/10">
                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 4v16m8-8H4"/>
                            </svg>
                            Tambah
                        </button>
                    </div>

                    <!-- Payment Methods Badges/List -->
                    <div class="space-y-2">
                        <label class="text-[10px] font-bold text-slate-400 uppercase tracking-wider block">Metode Pembayaran Saat Ini</label>
                        
                        <div class="flex flex-wrap gap-2.5">
                            <template x-for="method in paymentMethods" :key="method.id">
                                <div :class="method.is_active ? 'bg-white border-slate-200 text-slate-700' : 'bg-slate-100 border-slate-200/50 text-slate-400'" 
                                     class="flex items-center justify-between gap-3 border pl-3.5 pr-2.5 py-2 rounded-xl transition-all duration-200 shadow-sm hover:shadow">
                                    
                                    <!-- Method Name -->
                                    <div class="flex items-center gap-2">
                                        <span :class="method.is_active ? 'font-bold text-slate-800' : 'font-medium text-slate-400 line-through'"
                                              class="text-xs" 
                                              x-text="method.nama_metode"></span>
                                    </div>

                                    <!-- Actions: Toggle & Delete -->
                                    <div class="flex items-center gap-2.5 border-l border-slate-100 pl-2.5">
                                        <!-- Custom Toggle Switch -->
                                        <label class="relative inline-flex items-center cursor-pointer">
                                            <input type="checkbox" 
                                                   :checked="method.is_active" 
                                                   @change="togglePaymentMethod(method)" 
                                                   class="sr-only peer">
                                            <div class="w-8 h-4 bg-slate-200 peer-focus:outline-none rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-slate-300 after:border after:rounded-full after:h-3 after:w-3 after:transition-all peer-checked:bg-indigo-600"></div>
                                        </label>

                                        <!-- Delete X Button -->
                                        <button type="button" 
                                                @click="deletePaymentMethod(method)" 
                                                class="text-slate-400 hover:text-red-500 hover:bg-red-50 p-1 rounded-md transition-all active:scale-90"
                                                title="Hapus Metode">
                                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                                            </svg>
                                        </button>
                                    </div>
                                </div>
                            </template>

                            <template x-if="paymentMethods.length === 0">
                                <p class="text-xs text-slate-400 italic">Belum ada metode pembayaran yang dikonfigurasi.</p>
                            </template>
                        </div>
                    </div>
                </div>

                <!-- Submit pos button -->
                <div class="md:col-span-2 pt-2 flex justify-end">
                    <button type="submit" :disabled="isSaving" class="bg-indigo-600 hover:bg-indigo-700 text-white font-bold px-5 py-3 rounded-2xl text-xs flex items-center gap-2 shadow-md shadow-indigo-600/10 active:scale-[0.98] transition-all">
                        <svg x-show="isSaving" class="animate-spin -ml-1 mr-2 h-4 w-4 text-white" fill="none" viewBox="0 0 24 24">
                            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                        </svg>
                        <i x-show="!isSaving" data-lucide="save" class="w-4 h-4"></i>
                        <span x-text="isSaving ? 'Menyimpan...' : 'Simpan Konfigurasi POS'"></span>
                    </button>
                </div>
            </form>
        </div>
        
        <!-- PANEL C: Pengguna & Hak Akses -->
        <div x-show="activeTab === 'users'" class="bg-white rounded-2xl border border-slate-200/80 shadow-sm overflow-hidden" style="display: none;">
            
            <div class="px-6 py-5 border-b border-slate-100 flex items-center justify-between">
                <div>
                    <h3 class="font-bold text-slate-800 text-base">Daftar Akun Pengguna</h3>
                    <p class="text-xs text-slate-400">Atur hak akses masuk sistem, tambahkan karyawan kasir, atau hapus akses akun.</p>
                </div>
                <div class="flex items-center gap-2">
                    <a href="{{ route('cashiers.index') }}" class="bg-indigo-600 hover:bg-indigo-700 text-white font-bold px-3 py-2 rounded-xl text-[11px] flex items-center gap-1.5 transition-all shadow-md shadow-indigo-600/10 active:scale-[0.98]">
                        <i data-lucide="users" class="w-3.5 h-3.5"></i>
                        Kelola Akun Kasir (CRUD)
                    </a>
                    <!-- Tambah User Button -->
                    <button @click="showAddUserModal = true" class="bg-slate-50 hover:bg-slate-100 text-slate-700 border border-slate-200 font-bold px-3 py-2 rounded-xl text-[11px] flex items-center gap-1.5 transition-colors">
                        <i data-lucide="user-plus" class="w-3.5 h-3.5"></i>
                        Tambah User
                    </button>
                </div>
            </div>
            
            <!-- Users Table List -->
            <div class="overflow-x-auto">
                <table class="w-full text-left border-collapse">
                    <thead>
                        <tr class="bg-slate-50/50 border-b border-slate-100 text-[11px] font-bold text-slate-400 uppercase tracking-wider">
                            <th class="px-6 py-4">Nama Pengguna</th>
                            <th class="px-6 py-4">Alamat Email</th>
                            <th class="px-6 py-4">Peran Hak Akses</th>
                            <th class="px-6 py-4 text-center">Status</th>
                            <th class="px-6 py-4 text-center">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100 text-xs">
                        <template x-for="user in users" :key="user.id">
                            <tr class="hover:bg-slate-50/50 transition-colors">
                                <!-- User name & avatar -->
                                <td class="px-6 py-3.5">
                                    <div class="flex items-center gap-3">
                                        <img class="h-8 w-8 rounded-full object-cover border border-slate-100" :src="user.avatar" alt="Avatar">
                                        <span class="font-bold text-slate-800" x-text="user.name"></span>
                                    </div>
                                </td>
                                
                                <!-- Email address -->
                                <td class="px-6 py-3.5 text-slate-600 font-medium" x-text="user.email"></td>
                                
                                <!-- Role profile -->
                                <td class="px-6 py-3.5">
                                    <span class="px-2.5 py-1 bg-indigo-50 text-indigo-700 rounded-lg font-semibold" x-text="user.role"></span>
                                </td>
                                
                                <!-- Status badge -->
                                <td class="px-6 py-3.5 text-center">
                                    <span class="inline-flex items-center px-2 py-0.5 rounded-full text-[9px] font-bold bg-emerald-50 text-emerald-600">
                                        <span class="h-1 w-1 rounded-full bg-emerald-500 mr-1"></span>
                                        <span x-text="user.status"></span>
                                    </span>
                                </td>
                                
                                <!-- User Actions -->
                                <td class="px-6 py-3.5 text-center">
                                    <button @click="deleteUser(user)"
                                            :disabled="user.id === 1"
                                            :class="user.id === 1 ? 'text-slate-300 cursor-not-allowed' : 'text-rose-600 hover:bg-rose-50'"
                                            class="p-1.5 rounded-lg transition-colors"
                                            title="Cabut Akses Karyawan">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M13 7a4 4 0 11-8 0 4 4 0 018 0zM9 14a6 6 0 00-6 6v1h12v-1a6 6 0 00-6-6zM21 12h-6"></path>
                                        </svg>
                                    </button>
                                </td>
                            </tr>
                        </template>
                    </tbody>
                </table>
            </div>
            
        </div>
        
    </div>

    <!-- MODAL: Tambah Pengguna Baru -->
    <div x-show="showAddUserModal" 
         class="fixed inset-0 z-50 overflow-y-auto"
         style="display: none;">
        <div class="fixed inset-0 bg-slate-900/60 backdrop-blur-sm" @click="if(!isSaving) showAddUserModal = false"></div>
        
        <div class="flex min-h-screen items-center justify-center p-4 relative z-10">
            <div class="bg-white rounded-3xl max-w-md w-full shadow-2xl overflow-hidden border border-slate-100 flex flex-col"
                 x-show="showAddUserModal"
                 x-transition:enter="transition ease-out duration-300 transform scale-95"
                 x-transition:enter-start="transform scale-95 opacity-0"
                 x-transition:enter-end="transform scale-100 opacity-100">
                
                <div class="px-6 py-5 border-b border-slate-100 flex items-center justify-between bg-slate-50/50">
                    <h3 class="font-bold text-slate-800 text-sm">Tambah Pengguna / Karyawan</h3>
                    <button @click="showAddUserModal = false" :disabled="isSaving" class="text-slate-400 hover:text-slate-600">
                        <i data-lucide="x" class="w-5 h-5"></i>
                    </button>
                </div>
                
                <div class="p-6 space-y-4">
                    <!-- User Name Field -->
                    <div class="space-y-1.5">
                        <label class="text-xs font-bold text-slate-500">Nama Lengkap Karyawan</label>
                        <input type="text" x-model="newUserForm.name" placeholder="Contoh: Rian Hidayat" required class="w-full text-xs bg-slate-100 rounded-xl px-3 py-2.5 border border-slate-100 focus:outline-none focus:border-indigo-500 focus:bg-white text-slate-800">
                    </div>
                    
                    <!-- Email Field -->
                    <div class="space-y-1.5">
                        <label class="text-xs font-bold text-slate-500">Alamat Email Karyawan</label>
                        <input type="email" x-model="newUserForm.email" placeholder="rian.kasir@smartbiz.com" required class="w-full text-xs bg-slate-100 rounded-xl px-3 py-2.5 border border-slate-100 focus:outline-none focus:border-indigo-500 focus:bg-white text-slate-800">
                    </div>
                    
                    <!-- Role Selection -->
                    <div class="space-y-1.5">
                        <label class="text-xs font-bold text-slate-500">Hak Akses / Peran</label>
                        <select x-model="newUserForm.role" class="w-full text-xs rounded-xl border border-slate-200 bg-white p-2.5 text-slate-800 focus:outline-none focus:border-indigo-500 font-semibold">
                            <option value="Kasir">Kasir</option>
                            <option value="Gudang">Gudang</option>
                            <option value="Manager">Manager</option>
                            <option value="Super Admin">Super Admin</option>
                        </select>
                    </div>

                    <!-- Password Field Mock info -->
                    <div class="space-y-1.5">
                        <label class="text-xs font-bold text-slate-500">Kata Sandi Default</label>
                        <input type="text" value="password" disabled class="w-full text-xs bg-slate-50 rounded-xl px-3 py-2.5 border border-slate-100 text-slate-400 font-mono select-none">
                        <p class="text-[10px] text-slate-400">Kata sandi default untuk pertama kali login. Dapat diganti oleh karyawan nanti.</p>
                    </div>
                </div>
                
                <div class="p-6 border-t border-slate-100 bg-slate-50/50 flex gap-3">
                    <button @click="showAddUserModal = false" :disabled="isSaving" class="flex-1 py-3 border border-slate-200 hover:bg-slate-100 text-slate-600 font-semibold rounded-2xl text-xs transition-colors">
                        Batal
                    </button>
                    <button @click="addUser()" :disabled="isSaving" class="flex-[2] py-3 bg-indigo-600 hover:bg-indigo-700 text-white font-bold rounded-2xl text-xs flex items-center justify-center gap-2 transition-all">
                        <svg x-show="isSaving" class="animate-spin -ml-1 mr-2 h-4 w-4 text-white" fill="none" viewBox="0 0 24 24">
                            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                        </svg>
                        <span x-text="isSaving ? 'Menyimpan...' : 'Tambah & Undang'"></span>
                    </button>
                </div>
            </div>
        </div>
    </div>

</div>

@push('scripts')
<script>
function settingsComponent() {
    return {
        // Tab Navigation State
        activeTab: 'profile', // 'profile', 'pos', 'users'
        
        // Data structures loaded from Controller
        store: @json($store),
        pos: @json($posConfig),
        users: @json($users),
        paymentMethods: @json($paymentMethods),
        newPaymentMethod: '',
        
        // Modals & Action State
        showAddUserModal: false,
        isSaving: false,
        
        // New User Form State
        newUserForm: {
            name: '',
            email: '',
            role: 'Kasir',
            avatar: 'https://images.unsplash.com/photo-1535713875002-d1d0cf377fde?auto=format&fit=crop&q=80&w=100&h=100',
            status: 'Aktif'
        },
        
        // Reset User form fields
        resetUserForm() {
            this.newUserForm = {
                name: '',
                email: '',
                role: 'Kasir',
                avatar: 'https://images.unsplash.com/photo-1535713875002-d1d0cf377fde?auto=format&fit=crop&q=80&w=100&h=100',
                status: 'Aktif'
            };
        },
        
        // Save configurations general function
        async saveSettings(category) {
            this.isSaving = true;
            try {
                const dataToSave = (category === 'profile') ? this.store : this.pos;
                const response = await axios.post('/api/settings/save/' + category, dataToSave);
                
                this.$dispatch('show-toast', { 
                    message: 'Pengaturan ' + (category === 'profile' ? 'Profil Toko' : 'Sistem POS') + ' berhasil disimpan!', 
                    type: 'success' 
                });
            } catch (error) {
                console.error(error);
                this.$dispatch('show-toast', { message: 'Gagal menyimpan pengaturan.', type: 'danger' });
            } finally {
                this.isSaving = false;
            }
        },
        
        // Add Employee
        async addUser() {
            if (!this.newUserForm.name.trim() || !this.newUserForm.email.trim()) {
                this.$dispatch('show-toast', { message: 'Nama dan email wajib diisi!', type: 'danger' });
                return;
            }
            this.isSaving = true;
            
            try {
                const response = await axios.post('/api/settings/users/store', this.newUserForm);
                const addedUser = response.data;
                
                this.users.push(addedUser);
                this.$dispatch('show-toast', { message: 'Pengguna ' + addedUser.name + ' berhasil ditambahkan!', type: 'success' });
                this.showAddUserModal = false;
                this.resetUserForm();
            } catch (error) {
                console.error(error);
                this.$dispatch('show-toast', { message: 'Gagal menambahkan pengguna.', type: 'danger' });
            } finally {
                this.isSaving = false;
            }
        },
        
        // Remove User
        async deleteUser(user) {
            if (user.id === 1) {
                this.$dispatch('show-toast', { message: 'Tidak dapat menghapus Administrator Utama!', type: 'danger' });
                return;
            }
            
            if (confirm('Apakah Anda yakin ingin menghapus akses karyawan ' + user.name + '?')) {
                try {
                    const response = await axios.delete('/api/settings/users/delete/' + user.id);
                    this.users = this.users.filter(u => u.id !== user.id);
                    this.$dispatch('show-toast', { message: 'Akses Karyawan ' + user.name + ' dicabut.', type: 'warning' });
                } catch (error) {
                    console.error(error);
                    this.$dispatch('show-toast', { message: 'Gagal menghapus pengguna.', type: 'danger' });
                }
            }
        },

        // Add Payment Method via API
        async addPaymentMethod() {
            const name = this.newPaymentMethod.trim();
            if (!name) return;
            
            this.isSaving = true;
            try {
                const response = await axios.post('/api/settings/payment-methods', {
                    nama_metode: name
                });
                this.paymentMethods.push(response.data);
                this.newPaymentMethod = '';
                this.$dispatch('show-toast', { message: 'Metode pembayaran ' + name + ' berhasil ditambahkan!', type: 'success' });
            } catch (error) {
                console.error(error);
                let msg = 'Gagal menambahkan metode pembayaran.';
                if (error.response && error.response.data && error.response.data.errors) {
                    const errs = error.response.data.errors;
                    if (errs.nama_metode) {
                        msg = errs.nama_metode[0];
                    }
                }
                this.$dispatch('show-toast', { message: msg, type: 'danger' });
            } finally {
                this.isSaving = false;
            }
        },

        // Toggle Active Status via API
        async togglePaymentMethod(method) {
            try {
                const response = await axios.patch('/api/settings/payment-methods/' + method.id + '/toggle');
                method.is_active = response.data.is_active;
                this.$dispatch('show-toast', { 
                    message: 'Status metode ' + method.nama_metode + ' diubah menjadi ' + (method.is_active ? 'Aktif' : 'Nonaktif') + '.', 
                    type: 'success' 
                });
            } catch (error) {
                console.error(error);
                this.$dispatch('show-toast', { message: 'Gagal mengubah status metode pembayaran.', type: 'danger' });
            }
        },

        // Delete Payment Method via API
        async deletePaymentMethod(method) {
            if (confirm('Apakah Anda yakin ingin menghapus metode pembayaran ' + method.nama_metode + '?')) {
                try {
                    await axios.delete('/api/settings/payment-methods/' + method.id);
                    this.paymentMethods = this.paymentMethods.filter(m => m.id !== method.id);
                    this.$dispatch('show-toast', { message: 'Metode pembayaran ' + method.nama_metode + ' dihapus.', type: 'warning' });
                } catch (error) {
                    console.error(error);
                    this.$dispatch('show-toast', { message: 'Gagal menghapus metode pembayaran.', type: 'danger' });
                }
            }
        }
    };
}
</script>
@endpush
@endsection
