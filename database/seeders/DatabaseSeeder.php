<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\Product;
use App\Models\Transaction;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // Seed Roles & Users
        $this->call(RoleAndPermissionSeeder::class);

        // Seed dummy products
        $products = [
            [
                'sku' => 'SMB-001',
                'name' => 'Beras Pandan Wangi 5kg',
                'category' => 'Sembako',
                'purchase_price' => 68000,
                'price' => 78000,
                'stock' => 35,
                'min_stock' => 10,
            ],
            [
                'sku' => 'SMB-002',
                'name' => 'Minyak Goreng Bimoli 2L',
                'category' => 'Sembako',
                'purchase_price' => 33000,
                'price' => 38500,
                'stock' => 18,
                'min_stock' => 10,
            ],
            [
                'sku' => 'SMB-003',
                'name' => 'Gula Pasir Gulaku 1kg',
                'category' => 'Sembako',
                'purchase_price' => 14800,
                'price' => 17500,
                'stock' => 24,
                'min_stock' => 12,
            ],
            [
                'sku' => 'MKN-001',
                'name' => 'Indomie Goreng Spesial',
                'category' => 'Makanan',
                'purchase_price' => 2800,
                'price' => 3500,
                'stock' => 142,
                'min_stock' => 30,
            ],
            [
                'sku' => 'MNM-001',
                'name' => 'Kopi Kapal Api Mix 10s',
                'category' => 'Minuman',
                'purchase_price' => 12000,
                'price' => 14500,
                'stock' => 15,
                'min_stock' => 15,
            ],
        ];

        foreach ($products as $prodData) {
            \App\Models\Product::updateOrCreate(['sku' => $prodData['sku']], $prodData);
        }

        // Seed dummy transactions for the last 7 days
        $kasir = User::where('email', 'kasir@demo.com')->first();
        $admin = User::where('email', 'admin@demo.com')->first();

        $now = \Carbon\Carbon::now();
        for ($i = 6; $i >= 0; $i--) {
            $date = (clone $now)->subDays($i);
            $numTrx = rand(2, 5);
            for ($j = 1; $j <= $numTrx; $j++) {
                Transaction::create([
                    'user_id' => rand(0, 1) ? $kasir->id : $admin->id,
                    'invoice' => 'TRX-' . $date->format('Ymd') . '-' . rand(1000, 9999),
                    'total_harga' => rand(5, 50) * 10000,
                    'status' => 'success',
                    'created_at' => $date->copy()->subHours(rand(1, 10)),
                    'updated_at' => $date->copy()->subHours(rand(1, 10)),
                ]);
            }
        }

        // Seed default store and POS settings
        \App\Models\Setting::set('store_name', 'Kios Berkah Raya');
        \App\Models\Setting::set('store_email', 'contact@berkahraya.com');
        \App\Models\Setting::set('store_phone', '081234567890');
        \App\Models\Setting::set('store_address', 'Jl. Pemuda No. 45, Kecamatan Sukamaju, Kota Bandung, Jawa Barat 40123');
        \App\Models\Setting::set('receipt_header', "KIOS BERKAH RAYA\\nJl. Pemuda No. 45, Bandung");
        \App\Models\Setting::set('receipt_footer', "Terima Kasih Atas Kunjungan Anda!\\nBarang yang sudah dibeli tidak dapat ditukar.");

        \App\Models\Setting::set('tax_percent', '11');
        \App\Models\Setting::set('default_discount', '0');
        \App\Models\Setting::set('receipt_size', '58mm');
    }
}
