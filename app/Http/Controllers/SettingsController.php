<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class SettingsController extends Controller
{
    public function index()
    {
        // Mock data profil toko
        $store = [
            'name' => 'Kios Berkah Raya',
            'email' => 'contact@berkahraya.com',
            'phone' => '081234567890',
            'address' => 'Jl. Pemuda No. 45, Kecamatan Sukamaju, Kota Bandung, Jawa Barat 40123',
            'receipt_header' => 'KIOS BERKAH RAYA\nJl. Pemuda No. 45, Bandung',
            'receipt_footer' => 'Terima Kasih Atas Kunjungan Anda!\nBarang yang sudah dibeli tidak dapat ditukar.'
        ];

        // Mock data POS
        $posConfig = [
            'tax_percent' => 11,
            'default_discount' => 0,
            'receipt_size' => '58mm',
            'payment_methods' => ['Tunai', 'QRIS', 'Transfer Bank']
        ];

        // Mock data karyawan / user
        $users = [
            [
                'id' => 1,
                'name' => 'Citra Kirana',
                'email' => 'admin@smartbiz.com',
                'role' => 'Administrator',
                'avatar' => 'https://images.unsplash.com/photo-1534528741775-53994a69daeb?auto=format&fit=crop&q=80&w=100&h=100',
                'status' => 'Aktif'
            ],
            [
                'id' => 2,
                'name' => 'Budi Santoso',
                'email' => 'budi.kasir@smartbiz.com',
                'role' => 'Kasir Utama',
                'avatar' => 'https://images.unsplash.com/photo-1507003211169-0a1dd7228f2d?auto=format&fit=crop&q=80&w=100&h=100',
                'status' => 'Aktif'
            ],
            [
                'id' => 3,
                'name' => 'Siti Aminah',
                'email' => 'siti.kasir@smartbiz.com',
                'role' => 'Kasir Shift 2',
                'avatar' => 'https://images.unsplash.com/photo-1494790108377-be9c29b29330?auto=format&fit=crop&q=80&w=100&h=100',
                'status' => 'Aktif'
            ]
        ];

        return view('settings', compact('store', 'posConfig', 'users'));
    }
}
