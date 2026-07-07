<?php

namespace App\Http\Controllers;

use App\Models\Setting;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Spatie\Permission\Models\Role;

use App\Models\PaymentMethod;

class SettingsController extends Controller
{
    /**
     * Display settings page.
     */
    public function index()
    {
        // Load Store Profile Settings
        $store = [
            'name' => Setting::get('store_name', 'Kios Berkah Raya'),
            'email' => Setting::get('store_email', 'contact@berkahraya.com'),
            'phone' => Setting::get('store_phone', '081234567890'),
            'address' => Setting::get('store_address', 'Jl. Pemuda No. 45, Kecamatan Sukamaju, Kota Bandung, Jawa Barat 40123'),
            'receipt_header' => Setting::get('receipt_header', "KIOS BERKAH RAYA\nJl. Pemuda No. 45, Bandung"),
            'receipt_footer' => Setting::get('receipt_footer', "Terima Kasih Atas Kunjungan Anda!\nBarang yang sudah dibeli tidak dapat ditukar.")
        ];

        // Load POS configurations
        $posConfig = [
            'tax_percent' => (int) Setting::get('tax_percent', 11),
            'default_discount' => (int) Setting::get('default_discount', 0),
            'receipt_size' => Setting::get('receipt_size', '58mm'),
        ];

        // Fetch payment methods from DB, seed if empty
        $paymentMethods = PaymentMethod::all();
        if ($paymentMethods->isEmpty()) {
            PaymentMethod::create(['nama_metode' => 'Tunai', 'is_active' => true]);
            PaymentMethod::create(['nama_metode' => 'QRIS', 'is_active' => true]);
            PaymentMethod::create(['nama_metode' => 'Transfer Bank', 'is_active' => true]);
            $paymentMethods = PaymentMethod::all();
        }

        // Fetch users with their Spatie roles
        $usersFromDb = User::with('roles')->get();
        $users = $usersFromDb->map(function ($user) {
            return [
                'id' => $user->id,
                'name' => $user->name,
                'email' => $user->email,
                'role' => $user->roles->pluck('name')->first() ?? 'Kasir',
                'avatar' => 'https://images.unsplash.com/photo-1535713875002-d1d0cf377fde?auto=format&fit=crop&q=80&w=100&h=100',
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
            Setting::set('store_name', $request->input('name'));
            Setting::set('store_email', $request->input('email'));
            Setting::set('store_phone', $request->input('phone'));
            Setting::set('store_address', $request->input('address'));
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
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email',
            'role' => 'required|string'
        ]);

        $user = User::create([
            'name' => $request->input('name'),
            'email' => $request->input('email'),
            'password' => Hash::make('password'), // default password
        ]);

        // Assign Spatie Role
        $roleName = $request->input('role');
        if (in_array($roleName, ['Super Admin', 'Manager', 'Kasir'])) {
            $user->assignRole($roleName);
        } else {
            $user->assignRole('Kasir'); // Fallback
        }

        return response()->json([
            'id' => $user->id,
            'name' => $user->name,
            'email' => $user->email,
            'role' => $user->roles->pluck('name')->first() ?? $roleName,
            'avatar' => 'https://images.unsplash.com/photo-1535713875002-d1d0cf377fde?auto=format&fit=crop&q=80&w=100&h=100',
            'status' => 'Aktif'
        ]);
    }

    /**
     * Delete / Revoke employee access.
     */
    public function deleteUser($id)
    {
        $user = User::findOrFail($id);

        // Prevent deleting Super Admin Utama or current logged in user
        if ($user->id === 1 || $user->id === auth()->id()) {
            return response()->json(['error' => 'Akses ditolak.'], 403);
        }

        $user->delete();

        return response()->json(['success' => true]);
    }
}
