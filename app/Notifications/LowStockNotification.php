<?php

namespace App\Notifications;

use App\Models\Product;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;

class LowStockNotification extends Notification
{
    use Queueable;

    /**
     * Create a new notification instance.
     */
    public function __construct(public Product $product, public string $type = 'low_stock')
    {
        //
    }

    /**
     * Get the notification's delivery channels.
     *
     * @return array<int, string>
     */
    public function via(object $notifiable): array
    {
        return ['database'];
    }

    /**
     * Get the array representation of the notification.
     *
     * @return array<string, mixed>
     */
    public function toArray(object $notifiable): array
    {
        $message = $this->type === 'out_of_stock'
            ? 'Stok barang "' . $this->product->name . '" telah habis terjual!'
            : 'Stok barang "' . $this->product->name . '" menipis (sisa ' . $this->product->stock . ' pcs), segera restock!';

        return [
            'product_id' => $this->product->id,
            'product_name' => $this->product->name,
            'current_stock' => $this->product->stock,
            'type' => $this->type,
            'message' => $message,
        ];
    }
}
