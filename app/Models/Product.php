<?php

namespace App\Models;

use App\Models\Traits\BelongsToTenant;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
    use HasFactory, BelongsToTenant;

    protected $fillable = [
        'store_id',
        'sku',
        'name',
        'category_id',
        'category',
        'purchase_price',
        'price',
        'selling_price',
        'stock',
        'min_stock',
    ];

    protected $appends = [
        'category',
    ];

    /**
     * Get the category relationship.
     */
    public function categoryRelation()
    {
        return $this->belongsTo(Category::class, 'category_id');
    }

    /**
     * Accessor for category.
     * Keeps backward compatibility for code expecting $product->category string.
     */
    public function getCategoryAttribute($value)
    {
        return $this->categoryRelation?->name ?? $value ?? 'Umum';
    }

    /**
     * Mutator for category.
     * Automatically maps string inputs to correct category_id.
     */
    public function setCategoryAttribute($value)
    {
        if (is_numeric($value)) {
            $this->attributes['category_id'] = $value;
        } else {
            $catName = trim($value ?? '');
            if ($catName === '') {
                $catName = 'Umum';
            }

            $category = Category::firstOrCreate([
                'name' => $catName,
            ], [
                'slug' => \Illuminate\Support\Str::slug($catName)
            ]);

            $this->attributes['category_id'] = $category->id;
        }
    }

    public function getSellingPriceAttribute($value)
    {
        return $value ?? $this->price;
    }

    public function getPriceAttribute($value)
    {
        return $value;
    }
}
