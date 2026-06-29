<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class InventoryController extends Controller
{
    public function index()
    {
        // Mock data barang inventaris
        $inventory = [
            [
                'id' => 1,
                'sku' => 'SMB-001',
                'name' => 'Beras Pandan Wangi 5kg',
                'category' => 'Sembako',
                'stock' => 35,
                'min_stock' => 10,
                'purchase_price' => 68000,
                'selling_price' => 78000
            ],
            [
                'id' => 2,
                'sku' => 'SMB-002',
                'name' => 'Minyak Goreng Bimoli 2L',
                'category' => 'Sembako',
                'stock' => 18,
                'min_stock' => 10,
                'purchase_price' => 33000,
                'selling_price' => 38500
            ],
            [
                'id' => 3,
                'sku' => 'SMB-003',
                'name' => 'Gula Pasir Gulaku 1kg',
                'category' => 'Sembako',
                'stock' => 24,
                'min_stock' => 12,
                'purchase_price' => 14800,
                'selling_price' => 17500
            ],
            [
                'id' => 4,
                'sku' => 'MKN-001',
                'name' => 'Indomie Goreng Spesial',
                'category' => 'Makanan',
                'stock' => 142,
                'min_stock' => 30,
                'purchase_price' => 280000 / 100, // Rp 2.800
                'selling_price' => 3500
            ],
            [
                'id' => 5,
                'sku' => 'MKN-002',
                'name' => 'Mie Sedaap Soto',
                'category' => 'Makanan',
                'stock' => 98,
                'min_stock' => 30,
                'purchase_price' => 2650,
                'selling_price' => 3300
            ],
            [
                'id' => 6,
                'sku' => 'MNM-001',
                'name' => 'Kopi Kapal Api Mix 10s',
                'category' => 'Minuman',
                'stock' => 15,
                'min_stock' => 15, // Stok kritis/menipis!
                'purchase_price' => 12000,
                'selling_price' => 14500
            ],
            [
                'id' => 7,
                'sku' => 'MNM-002',
                'name' => 'Teh Celup Sosro Box 25s',
                'category' => 'Minuman',
                'stock' => 40,
                'min_stock' => 15,
                'purchase_price' => 6200,
                'selling_price' => 8000
            ],
            [
                'id' => 8,
                'sku' => 'MNM-003',
                'name' => 'Air Mineral Aqua 600ml',
                'category' => 'Minuman',
                'stock' => 85,
                'min_stock' => 24,
                'purchase_price' => 2500,
                'selling_price' => 4000
            ],
            [
                'id' => 9,
                'sku' => 'SNC-001',
                'name' => 'Chitato Sapi Panggang 68g',
                'category' => 'Cemilan',
                'stock' => 6, // Stok kritis/menipis!
                'min_stock' => 10,
                'purchase_price' => 9500,
                'selling_price' => 12000
            ],
            [
                'id' => 10,
                'sku' => 'SNC-002',
                'name' => 'Silverqueen Almond 62g',
                'category' => 'Cemilan',
                'stock' => 11,
                'min_stock' => 5,
                'purchase_price' => 13000,
                'selling_price' => 16500
            ],
            [
                'id' => 11,
                'sku' => 'HH-001',
                'name' => 'Sabun Mandi Lifebuoy 85g',
                'category' => 'Rumah Tangga',
                'stock' => 28,
                'min_stock' => 10,
                'purchase_price' => 3400,
                'selling_price' => 4500
            ],
            [
                'id' => 12,
                'sku' => 'HH-002',
                'name' => 'Deterjen Rinso Liquid 750ml',
                'category' => 'Rumah Tangga',
                'stock' => 4, // Stok kritis!
                'min_stock' => 8,
                'purchase_price' => 17500,
                'selling_price' => 21000
            ]
        ];

        // Mock data riwayat mutasi stok
        $mutations = [
            [
                'date' => '2026-06-29 11:20',
                'sku' => 'MKN-001',
                'name' => 'Indomie Goreng Spesial',
                'type' => 'OUT',
                'qty' => 5,
                'ref' => 'POS-20260629004',
                'operator' => 'Budi (Kasir)'
            ],
            [
                'date' => '2026-06-29 10:15',
                'sku' => 'SMB-001',
                'name' => 'Beras Pandan Wangi 5kg',
                'type' => 'IN',
                'qty' => 50,
                'ref' => 'GR-20260629001',
                'operator' => 'Agus (Gudang)'
            ],
            [
                'date' => '2026-06-29 09:30',
                'sku' => 'SNC-001',
                'name' => 'Chitato Sapi Panggang 68g',
                'type' => 'OUT',
                'qty' => 2,
                'ref' => 'POS-20260629001',
                'operator' => 'Budi (Kasir)'
            ],
            [
                'date' => '2026-06-28 16:45',
                'sku' => 'HH-002',
                'name' => 'Deterjen Rinso Liquid 750ml',
                'type' => 'OUT',
                'qty' => 1,
                'ref' => 'POS-20260628084',
                'operator' => 'Siti (Kasir)'
            ],
            [
                'date' => '2026-06-28 09:00',
                'sku' => 'MNM-003',
                'name' => 'Air Mineral Aqua 600ml',
                'type' => 'IN',
                'qty' => 120,
                'ref' => 'GR-20260628001',
                'operator' => 'Agus (Gudang)'
            ],
            [
                'date' => '2026-06-27 14:10',
                'sku' => 'SMB-003',
                'name' => 'Gula Pasir Gulaku 1kg',
                'type' => 'OUT',
                'qty' => 10,
                'ref' => 'ADJ-20260627002',
                'operator' => 'Admin (Stok Opname - Barang Rusak)'
            ]
        ];

        $categories = ['Sembako', 'Makanan', 'Minuman', 'Cemilan', 'Rumah Tangga'];

        return view('inventory', compact('inventory', 'mutations', 'categories'));
    }
}
