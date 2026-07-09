<?php

namespace App\Models;

use App\Models\Traits\BelongsToTenant;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PaymentMethod extends Model
{
    use HasFactory, BelongsToTenant;

    protected $fillable = [
        'store_id',
        'nama_metode',
        'is_active',
    ];

    protected $casts = [
        'is_active' => 'boolean',
    ];
}
