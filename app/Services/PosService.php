<?php

namespace App\Services;

use App\Models\Product;
use App\Models\Transaction;
use Illuminate\Support\Facades\DB;
use Exception;

class PosService
{
    /**
     * Process checkout for items in POS.
     *
     * @param array $items Array of items (e.g. [['id' => 1, 'qty' => 2], ...])
     * @return Transaction
     * @throws Exception
     */
    public function processCheckout(array $items): Transaction
    {
        return DB::transaction(function () use ($items) {
            $totalHarga = 0;

            foreach ($items as $item) {
                $productId = $item['id'] ?? null;
                $qty = (int) ($item['qty'] ?? 0);

                if (!$productId || $qty <= 0) {
                    continue;
                }

                // Fetch product with pessimistic lock
                $product = Product::lockForUpdate()->find($productId);

                if (!$product) {
                    throw new Exception("Produk dengan ID {$productId} tidak ditemukan.");
                }

                if ($product->stock < $qty) {
                    throw new Exception("Stok produk '{$product->name}' tidak mencukupi (Sisa: {$product->stock}).");
                }

                // Reduce stock
                $product->decrement('stock', $qty);

                // Add to total
                $totalHarga += $product->price * $qty;
            }

            if ($totalHarga <= 0) {
                throw new Exception("Keranjang belanja kosong atau data tidak valid.");
            }

            // Create Transaction record
            $invoice = 'TRX-' . date('Ymd') . '-' . mt_rand(1000, 9999);
            
            return Transaction::create([
                'user_id' => auth()->id(),
                'invoice' => $invoice,
                'total_harga' => $totalHarga,
                'status' => 'success',
            ]);
        });
    }
}
