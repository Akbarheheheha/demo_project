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

            return;
        }

        $this->record(
            'product.updated',
            sprintf(
                'User [%s] mengubah data barang [%s].',
                $this->userName(),
                $product->name
            )
        );
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
