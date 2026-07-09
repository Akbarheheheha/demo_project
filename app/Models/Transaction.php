<?php

namespace App\Models;

use App\Models\Traits\BelongsToTenant;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Transaction extends Model
{
    use HasFactory, BelongsToTenant;

    protected $fillable = [
        'store_id',
        'user_id',
        'invoice',
        'total_harga',
        'payment_method',
        'tax_amount',
        'discount_amount',
        'status',
        'customer_name',
        'discount',
        'tax',
    ];

    protected $casts = [
        'total_harga' => 'decimal:2',
        'payment_method' => 'string',
        'tax_amount' => 'decimal:2',
        'discount_amount' => 'decimal:2',
        'discount' => 'decimal:2',
        'tax' => 'decimal:2',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function details()
    {
        return $this->hasMany(TransactionDetail::class);
    }
}
