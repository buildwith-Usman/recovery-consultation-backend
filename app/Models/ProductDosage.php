<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ProductDosage extends Model
{
    protected $fillable = [
        'product_id',
        'name',
        'price',
        'stock_quantity',
        'availability_status',
        'sort_order',
    ];

    protected function casts(): array
    {
        return [
            'price' => 'decimal:2',
            'stock_quantity' => 'integer',
            'sort_order' => 'integer',
        ];
    }

    protected $appends = ['final_price'];

    public function product()
    {
        return $this->belongsTo(Product::class);
    }

    /**
     * Get the final price after applying product-level discount.
     */
    public function getFinalPriceAttribute()
    {
        $product = $this->product;

        if (!$product || !$product->discount_type || !$product->discount_value) {
            return $this->price;
        }

        if ($product->discount_type === 'percentage') {
            return $this->price - ($this->price * $product->discount_value / 100);
        }

        if ($product->discount_type === 'flat') {
            return max(0, $this->price - $product->discount_value);
        }

        return $this->price;
    }

    /**
     * Update availability status based on stock quantity.
     */
    public function updateAvailabilityStatus()
    {
        if ($this->stock_quantity <= 0) {
            $this->availability_status = 'out_of_stock';
        } elseif ($this->stock_quantity < 10) {
            $this->availability_status = 'low_stock';
        } else {
            $this->availability_status = 'in_stock';
        }
        $this->save();
    }
}
