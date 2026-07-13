<?php

namespace App\Http\Controllers;

use App\Models\Setting;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;
use App\Models\PaymentMethod;

class SettingsController extends Controller
{
    /**
     * Display settings page.
     */
    public function index()
    {
        // PERBAIKAN: Ambil profil toko dari tabel 'stores' bawaan user yang login
        $currentStore = auth()->user()->store;

        $store = [
            'name' => $currentStore->name ?? 'Kios Berkah Raya',
            'email' => $currentStore->email ?? 'contact@berkahraya.com',
            'phone' => $currentStore->phone ?? '081234567890',
            'address' => $currentStore->address ?? 'Alamat Toko',
            'receipt_header' => Setting::get('receipt_header', "KIOS BERKAH RAYA\nJl. Pemuda No. 45"),
            'receipt_footer' => Setting::get('receipt_footer', "Terima Kasih Atas Kunjungan Anda!")
        ];

        // Load POS configurations
        $posConfig = [
            'tax_percent' => (int) Setting::get('tax_percent', 11),
            'default_discount' => (int) Setting::get('default_discount', 0),
            'receipt_size' => Setting::get('receipt_size', '58mm'),
        ];

        // Fetch payment methods
        $paymentMethods = PaymentMethod::all();
        if ($paymentMethods->isEmpty()) {
            PaymentMethod::create(['nama_metode' => 'Tunai', 'is_active' => true]);
            PaymentMethod::create(['nama_metode' => 'QRIS', 'is_active' => true]);
            PaymentMethod::create(['nama_metode' => 'Transfer Bank', 'is_active' => true]);
            $paymentMethods = PaymentMethod::all(); // Refresh setelah di-seed
        }

        // Fetch users (Otomatis ter-filter berdasarkan toko berkat TenantScope!)
        $usersFromDb = User::with('roles')->get();
        $users = $usersFromDb->map(function ($user) {
            return [
                'id' => $user->id,
                'name' => $user->name,
                'email' => $user->email,
                'role' => $user->getRoleNames()->first() ?? '-',
                'avatar' => 'https://ui-avatars.com/api/?name=' . urlencode($user->name) . '&background=random',
                'status' => 'Aktif'
            ];
        });

        return view('settings', compact('store', 'posConfig', 'users', 'paymentMethods'));
    }

    /**
     * Save settings (Profile or POS).
     */
    public function save(Request $request, $category)
    {
        if ($category === 'profile') {
            // PERBAIKAN: Update data utama ke tabel 'stores'
            $store = auth()->user()->store;
            $store->update([
                'name' => $request->input('name'),
                'email' => $request->input('email'),
                'phone' => $request->input('phone'),
                'address' => $request->input('address'),
            ]);

            // Untuk pengaturan spesifik nota, tetap pakai Settings table
            Setting::set('receipt_header', $request->input('receipt_header'));
            Setting::set('receipt_footer', $request->input('receipt_footer'));

        } elseif ($category === 'pos') {
            Setting::set('tax_percent', $request->input('tax_percent'));
            Setting::set('default_discount', $request->input('default_discount'));
            Setting::set('receipt_size', $request->input('receipt_size'));
        }

        return response()->json(['success' => true]);
    }

    /**
     * Store new User / Employee.
     */
    public function storeUser(Request $request)
    {
        // PERBAIKAN: Hapus 'Super Admin' dari daftar agar tidak ada pembajakan privilege!
        $allowedRoles = ['Manager', 'Kasir', 'Gudang'];

        $request->validate([
            'name' => 'required|string|max:255',
            'email' => ['required', 'email', 'max:255', Rule::unique('users')->where('store_id', auth()->user()->store_id)],
            'role' => ['required', 'string', Rule::in($allowedRoles)],
        ]);

        // Sihir BelongsToTenant akan otomatis mengisi store_id milik bosnya ke akun ini
        $user = User::create([
            'name' => $request->input('name'),
            'email' => $request->input('email'),
            'password' => Hash::make('password'),
        ]);

        $user->assignRole($request->input('role'));

        return response()->json([
            'id' => $user->id,
            'name' => $user->name,
            'email' => $user->email,
            'role' => $user->getRoleNames()->first(),
            'avatar' => 'https://ui-avatars.com/api/?name=' . urlencode($user->name) . '&background=random',
            'status' => 'Aktif'
        ]);
    }

    /**
     * Delete / Revoke employee access.
     */
    public function deleteUser($id)
    {
        // TenantScope otomatis mencegah bos menghapus user dari toko lain!
        $user = User::findOrFail($id);

        if ($user->id === auth()->id() || $user->hasRole('Tenant Owner')) {
            return response()->json(['error' => 'Akses ditolak. Tidak bisa menghapus diri sendiri atau Pemilik Toko.'], 403);
        }

        $user->delete();

        return response()->json(['success' => true]);
    }
}