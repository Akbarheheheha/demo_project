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
     * @param string|null $customerName
     * @param float $discountPercent
     * @param float $taxPercent
     * @return Transaction
     * @throws Exception
     */
    public function processCheckout(array $items, ?string $customerName = null, float $discountPercent = 0, float $taxPercent = 0): Transaction
    {
        return DB::transaction(function () use ($items, $customerName, $discountPercent, $taxPercent) {
            $subtotal = 0;

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

                // Add to total subtotal
                $subtotal += $product->price * $qty;
            }

            if ($subtotal <= 0) {
                throw new Exception("Keranjang belanja kosong atau data tidak valid.");
            }

            // Calculate final price with discount and tax
            $discountAmount = ($subtotal * $discountPercent) / 100;
            $taxAmount = (($subtotal - $discountAmount) * $taxPercent) / 100;
            $totalHarga = $subtotal - $discountAmount + $taxAmount;

            // Create Transaction record
            $invoice = 'TRX-' . date('Ymd') . '-' . mt_rand(1000, 9999);
            
            $transaction = Transaction::create([
                'user_id' => auth()->id(),
                'invoice' => $invoice,
                'total_harga' => $totalHarga,
                'customer_name' => $customerName,
                'discount' => $discountAmount,
                'tax' => $taxAmount,
                'status' => 'success',
            ]);

            // Insert Transaction Details
            foreach ($items as $item) {
                $productId = $item['id'] ?? null;
                $qty = (int) ($item['qty'] ?? 0);

                if (!$productId || $qty <= 0) {
                    continue;
                }

                $product = Product::find($productId);

                $transaction->details()->create([
                    'product_id' => $productId,
                    'qty' => $qty,
                    'harga_beli' => $product->purchase_price,
                    'harga_jual' => $product->price,
                    'subtotal' => $product->price * $qty,
                ]);
            }

            return $transaction;
        });
    }
}
