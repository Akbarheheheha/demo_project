<?php

namespace App\Observers;

use App\Models\ActivityLog;
use App\Models\Product;
use Illuminate\Support\Facades\Auth;

class ProductObserver
{
    public function created(Product $product): void
    {
        $this->record(
            'product.created',
            sprintf(
                'User [%s] menambahkan barang [%s].',
                $this->userName(),
                $product->name
            )
        );
    }

    public function updated(Product $product): void
    {
        if ($product->wasChanged('price')) {
            $this->record(
                'product.updated',
                sprintf(
                    'User [%s] mengubah harga barang [%s] dari Rp %s menjadi Rp %s.',
                    $this->userName(),
                    $product->name,
                    number_format((float) $product->getOriginal('price'), 0, ',', '.'),
                    number_format((float) $product->price, 0, ',', '.')
                )
            );
        } else {
            $this->record(
                'product.updated',
                sprintf(
                    'User [%s] mengubah data barang [%s].',
                    $this->userName(),
                    $product->name
                )
            );
        }

        // Low stock/Out of stock checking logic
        if ($product->wasChanged('stock')) {
            if ($product->stock <= 0) {
                // Out of stock
                $admins = \App\Models\User::role(['Super Admin', 'Manager'])->get();
                foreach ($admins as $admin) {
                    $alreadyNotified = $admin->unreadNotifications()
                        ->where('data->product_id', $product->id)
                        ->where('data->type', 'out_of_stock')
                        ->exists();

                    if (!$alreadyNotified) {
                        $admin->notify(new \App\Notifications\LowStockNotification($product, 'out_of_stock'));
                    }
                }
            } elseif ($product->stock < $product->min_stock) {
                // Low stock
                $admins = \App\Models\User::role(['Super Admin', 'Manager'])->get();
                foreach ($admins as $admin) {
                    $alreadyNotified = $admin->unreadNotifications()
                        ->where('data->product_id', $product->id)
                        ->where('data->type', 'low_stock')
                        ->exists();

                    if (!$alreadyNotified) {
                        $admin->notify(new \App\Notifications\LowStockNotification($product, 'low_stock'));
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
                'User [%s] menghapus barang [%s].',
                $this->userName(),
                $product->name
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
