<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\Transaction;
use Illuminate\Http\Request;

class InventoryController extends Controller
{
    /**
     * Display inventory page.
     */
    public function index(Request $request)
    {
        $search = $request->search;

        $inventoryFromDb = Product::when($search, function ($query, $search) {
            return $query->where('name', 'like', '%' . $search . '%')
                         ->orWhere('sku', 'like', '%' . $search . '%');
        })->orderBy('sku', 'asc')->get();

        $inventory = $inventoryFromDb->map(function ($item) {
            return [
                'id' => $item->id,
                'sku' => $item->sku,
                'name' => $item->name,
                'category' => $item->category,
                'stock' => $item->stock,
                'min_stock' => $item->min_stock,
                'purchase_price' => (float) $item->purchase_price,
                'selling_price' => (float) $item->price,
                'price' => (float) $item->price
            ];
        });

        // Ambil data mutasi dinamis berdasarkan riwayat transaksi di database
        $mutations = Transaction::with('user')
            ->orderBy('created_at', 'desc')
            ->limit(10)
            ->get()
            ->map(function ($trx) {
                return [
                    'date' => $trx->created_at->format('Y-m-d H:i'),
                    'sku' => $trx->invoice,
                    'name' => 'Penjualan POS',
                    'type' => 'OUT',
                    'qty' => 1,
                    'ref' => $trx->invoice,
                    'operator' => $trx->user ? $trx->user->name : 'Sistem'
                ];
            })
            ->toArray();

        $categories = \App\Models\Category::withCount('products')->get()->map(function ($cat) {
            return [
                'id' => $cat->id,
                'name' => $cat->name,
                'slug' => $cat->slug,
                'products_count' => $cat->products_count
            ];
        })->toArray();

        return view('inventory', compact('inventory', 'mutations', 'categories'));
    }

    /**
     * Store a newly created product in database.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'sku' => 'required|string|unique:products,sku',
            'name' => 'required|string|max:255',
            'category' => 'required|string',
            'stock' => 'required|integer|min:0',
            'min_stock' => 'required|integer|min:0',
            'purchase_price' => 'required|numeric|min:0',
            'selling_price' => 'required|numeric|min:0'
        ]);

        $product = Product::create([
            'sku' => $validated['sku'],
            'name' => $validated['name'],
            'category' => $validated['category'],
            'stock' => $validated['stock'],
            'min_stock' => $validated['min_stock'],
            'purchase_price' => $validated['purchase_price'],
            'price' => $validated['selling_price'], // price kolom mewakili selling_price didatabase
        ]);

        return response()->json([
            'success' => true,
            'product' => [
                'id' => $product->id,
                'sku' => $product->sku,
                'name' => $product->name,
                'category' => $product->category,
                'stock' => $product->stock,
                'min_stock' => $product->min_stock,
                'purchase_price' => (float) $product->purchase_price,
                'selling_price' => (float) $product->price
            ]
        ]);
    }

    /**
     * Update the specified product in database.
     */
    public function update(Request $request, $id)
    {
        $product = Product::findOrFail($id);

        $validated = $request->validate([
            'sku' => 'required|string|unique:products,sku,' . $product->id,
            'name' => 'required|string|max:255',
            'category' => 'required|string',
            'stock' => 'required|integer|min:0',
            'min_stock' => 'required|integer|min:0',
            'purchase_price' => 'required|numeric|min:0',
            'selling_price' => 'required|numeric|min:0',
            'tambah_stok' => 'nullable|integer|min:0',
            'stok_kadaluarsa' => 'nullable|integer|min:0',
            'catatan' => 'nullable|string'
        ]);

        $tambah = $request->input('tambah_stok', 0);
        $rusak = $request->input('stok_kadaluarsa', 0);

        if ($rusak > 0 && empty($request->input('catatan'))) {
            return response()->json([
                'success' => false,
                'message' => 'Catatan wajib diisi jika ada stok kadaluarsa/rusak.'
            ], 422);
        }
        
        // Kalkulasi ulang dengan aman di level backend
        if ($request->has('tambah_stok') || $request->has('stok_kadaluarsa')) {
            $validated['stock'] = max(0, $product->stock + $tambah - $rusak);
        }

        $product->update([
            'sku' => $validated['sku'],
            'name' => $validated['name'],
            'category' => $validated['category'],
            'stock' => $validated['stock'],
            'min_stock' => $validated['min_stock'],
            'purchase_price' => $validated['purchase_price'],
            'price' => $validated['selling_price'],
        ]);

        return response()->json([
            'success' => true,
            'product' => [
                'id' => $product->id,
                'sku' => $product->sku,
                'name' => $product->name,
                'category' => $product->category,
                'stock' => $product->stock,
                'min_stock' => $product->min_stock,
                'purchase_price' => (float) $product->purchase_price,
                'selling_price' => (float) $product->price
            ]
        ]);
    }

    /**
     * Remove the specified product from database.
     */
    public function destroy($id)
    {
        $product = Product::findOrFail($id);
        $product->delete();

        return response()->json(['success' => true]);
    }
}
