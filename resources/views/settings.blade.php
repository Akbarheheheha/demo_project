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

                <!-- Payment Methods Checkboxes -->
                <div class="space-y-2">
                    <label class="text-xs font-bold text-slate-500 block">Metode Pembayaran Kasir yang Aktif</label>
                    <div class="flex flex-wrap gap-4 pt-1">
                        <label class="flex items-center gap-2 cursor-pointer text-xs text-slate-600 hover:text-slate-800 transition-colors">
                            <input type="checkbox" checked disabled class="rounded border-slate-200 text-indigo-600 focus:ring-indigo-500">
                            <span>Tunai (Cash)</span>
                        </label>
                        <label class="flex items-center gap-2 cursor-pointer text-xs text-slate-600 hover:text-slate-800 transition-colors">
                            <input type="checkbox" checked disabled class="rounded border-slate-200 text-indigo-600 focus:ring-indigo-500">
                            <span>QRIS / E-Wallet</span>
                        </label>
                        <label class="flex items-center gap-2 cursor-pointer text-xs text-slate-600 hover:text-slate-800 transition-colors">
                            <input type="checkbox" checked disabled class="rounded border-slate-200 text-indigo-600 focus:ring-indigo-500">
                            <span>Transfer Bank</span>
                        </label>
                    </div>
                    <p class="text-[10px] text-slate-400">Pengaturan metode pembayaran kustom memerlukan integrasi payment gateway eksternal.</p>
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
                <!-- Tambah User Button -->
                <button @click="openAddUserModal = true" class="bg-indigo-50 hover:bg-indigo-100 text-indigo-700 border border-indigo-200 font-bold px-3 py-2 rounded-xl text-[11px] flex items-center gap-1.5 transition-colors">
                    <i data-lucide="user-plus" class="w-3.5 h-3.5"></i>
                    Tambah User
                </button>
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
        }
    };
}
</script>
@endpush
@endsection
