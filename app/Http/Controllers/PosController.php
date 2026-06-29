<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class PosController extends Controller
{
    public function index()
    {
        // Mock data kategori
        $categories = [
            ['id' => 'all', 'name' => 'Semua Produk'],
            ['id' => 'sembako', 'name' => 'Sembako'],
            ['id' => 'makanan', 'name' => 'Makanan'],
            ['id' => 'minuman', 'name' => 'Minuman'],
            ['id' => 'snack', 'name' => 'Cemilan'],
            ['id' => 'household', 'name' => 'Rumah Tangga']
        ];

        // Mock data produk
        $products = [
            [
                'id' => 1,
                'sku' => 'SMB-001',
                'name' => 'Beras Pandan Wangi 5kg',
                'category' => 'sembako',
                'price' => 78000,
                'stock' => 35,
                'color' => 'emerald',
                'icon' => 'shopping-bag'
            ],
            [
                'id' => 2,
                'sku' => 'SMB-002',
                'name' => 'Minyak Goreng Bimoli 2L',
                'category' => 'sembako',
                'price' => 38500,
                'stock' => 18,
                'color' => 'emerald',
                'icon' => 'droplet'
            ],
            [
                'id' => 3,
                'sku' => 'SMB-003',
                'name' => 'Gula Pasir Gulaku 1kg',
                'category' => 'sembako',
                'price' => 17500,
                'stock' => 24,
                'color' => 'emerald',
                'icon' => 'database'
            ],
            [
                'id' => 4,
                'sku' => 'MKN-001',
                'name' => 'Indomie Goreng Spesial',
                'category' => 'makanan',
                'price' => 3500,
                'stock' => 142,
                'color' => 'amber',
                'icon' => 'utensils'
            ],
            [
                'id' => 5,
                'sku' => 'MKN-002',
                'name' => 'Mie Sedaap Soto',
                'category' => 'makanan',
                'price' => 3300,
                'stock' => 98,
                'color' => 'amber',
                'icon' => 'utensils'
            ],
            [
                'id' => 6,
                'sku' => 'MNM-001',
                'name' => 'Kopi Kapal Api Mix 10s',
                'category' => 'minuman',
                'price' => 14500,
                'stock' => 15,
                'color' => 'sky',
                'icon' => 'cup-tass' // Coffee cup
            ],
            [
                'id' => 7,
                'sku' => 'MNM-002',
                'name' => 'Teh Celup Sosro Box 25s',
                'category' => 'minuman',
                'price' => 8000,
                'stock' => 40,
                'color' => 'sky',
                'icon' => 'cup-tass'
            ],
            [
                'id' => 8,
                'sku' => 'MNM-003',
                'name' => 'Air Mineral Aqua 600ml',
                'category' => 'minuman',
                'price' => 4000,
                'stock' => 85,
                'color' => 'sky',
                'icon' => 'droplet'
            ],
            [
                'id' => 9,
                'sku' => 'SNC-001',
                'name' => 'Chitato Sapi Panggang 68g',
                'category' => 'snack',
                'price' => 12000,
                'stock' => 6, // Stok menipis!
                'color' => 'purple',
                'icon' => 'candy'
            ],
            [
                'id' => 10,
                'sku' => 'SNC-002',
                'name' => 'Silverqueen Almond 62g',
                'category' => 'snack',
                'price' => 16500,
                'stock' => 11,
                'color' => 'purple',
                'icon' => 'candy'
            ],
            [
                'id' => 11,
                'sku' => 'HH-001',
                'name' => 'Sabun Mandi Lifebuoy 85g',
                'category' => 'household',
                'price' => 4500,
                'stock' => 28,
                'color' => 'pink',
                'icon' => 'sparkles'
            ],
            [
                'id' => 12,
                'sku' => 'HH-002',
                'name' => 'Deterjen Rinso Liquid 750ml',
                'category' => 'household',
                'price' => 21000,
                'stock' => 4, // Stok menipis!
                'color' => 'pink',
                'icon' => 'sparkles'
            ]
        ];

        return view('pos', compact('categories', 'products'));
    }
}
