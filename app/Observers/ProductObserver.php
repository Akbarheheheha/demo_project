<?php

namespace App\Observers;

use App\Models\ActivityLog;
use App\Models\Category;
use App\Models\Product;
use Illuminate\Support\Facades\Auth;

class ProductObserver
{
    public function created(Product $product): void
    {
        $this->record(
            'product.created',
            sprintf(
                'User [%s] menambahkan barang baru: [%s] | SKU: %s | Harga: Rp %s | Stok: %s | Stok Minimum: %s | Kategori: %s',
                $this->userName(),
                $product->name,
                $product->sku ?? '-',
                number_format((float) $product->price, 0, ',', '.'),
                $product->stock,
                $product->min_stock,
                $product->category ?? 'Umum'
            )
        );
    }

    public function updated(Product $product): void
    {
        $changedFields = [];
        $dirty = $product->getDirty();

        foreach ($dirty as $field => $newValue) {
            if (in_array($field, ['updated_at'])) {
                continue;
            }

            $oldValue = $product->getOriginal($field);

            $changedFields[] = match ($field) {
                'name' => sprintf('nama dari "%s" menjadi "%s"', $oldValue, $newValue),
                'sku' => sprintf('SKU dari %s menjadi %s', $oldValue ?: '-', $newValue ?: '-'),
                'price' => sprintf(
                    'harga jual dari Rp %s menjadi Rp %s',
                    number_format((float) $oldValue, 0, ',', '.'),
                    number_format((float) $newValue, 0, ',', '.')
                ),
                'purchase_price' => sprintf(
                    'harga beli dari Rp %s menjadi Rp %s',
                    number_format((float) $oldValue, 0, ',', '.'),
                    number_format((float) $newValue, 0, ',', '.')
                ),
                'stock' => sprintf('stok dari %s menjadi %s', $oldValue, $newValue),
                'min_stock' => sprintf('stok minimum dari %s menjadi %s', $oldValue, $newValue),
                'category_id' => sprintf(
                    'kategori dari %s menjadi %s',
                    Category::find($oldValue)?->name ?? 'Umum',
                    Category::find($newValue)?->name ?? 'Umum'
                ),
                default => sprintf('%s: %s → %s', $field, $oldValue, $newValue),
            };
        }

        if (!empty($changedFields)) {
            $this->record(
                'product.updated',
                sprintf(
                    'User [%s] mengubah data barang [%s] — %s.',
                    $this->userName(),
                    $product->name,
                    implode('; ', $changedFields)
                )
            );
        }

        // Low stock/Out of stock checking logic
        if ($product->wasChanged('stock')) {
            $notifiedRoles = ['Super Admin', 'Manager', 'Gudang'];
            if ($product->stock <= 0) {
                // Out of stock
                $users = \App\Models\User::role($notifiedRoles)->get();
                foreach ($users as $user) {
                    $alreadyNotified = $user->unreadNotifications()
                        ->where('data->product_id', $product->id)
                        ->where('data->type', 'out_of_stock')
                        ->exists();

                    if (!$alreadyNotified) {
                        $user->notify(new \App\Notifications\LowStockNotification($product, 'out_of_stock'));
                    }
                }
            } elseif ($product->stock < $product->min_stock) {
                // Low stock
                $users = \App\Models\User::role($notifiedRoles)->get();
                foreach ($users as $user) {
                    $alreadyNotified = $user->unreadNotifications()
                        ->where('data->product_id', $product->id)
                        ->where('data->type', 'low_stock')
                        ->exists();

                    if (!$alreadyNotified) {
                        $user->notify(new \App\Notifications\LowStockNotification($product, 'low_stock'));
                    }
                }
            }
        }
    }

    public function deleted(Product $product): void
    {
        $this->record(
            'product.deleted',
            sprintf(
                'User [%s] menghapus barang: [%s] (SKU: %s | Harga: Rp %s | Stok: %s | Kategori: %s)',
                $this->userName(),
                $product->name,
                $product->sku ?? '-',
                number_format((float) $product->price, 0, ',', '.'),
                $product->stock,
                $product->category ?? 'Umum'
            )
        );
    }

    private function record(string $action, string $description): void
    {
        ActivityLog::create([
            'user_id' => Auth::id(),
            'action' => $action,
            'description' => $description,
        ]);
    }

    private function userName(): string
    {
        return Auth::user()?->name ?? 'Sistem';
    }
}
