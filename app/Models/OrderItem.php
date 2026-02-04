<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class OrderItem extends Model
{
    protected $fillable = [
        'order_id',
        'product_id',
        'product_dosage_id',
        'product_name',
        'dosage_name',
        'quantity',
        'price',
        'dosage_info',
        'item_total'
    ];

    protected function casts(): array
    {
        return [
            'quantity' => 'integer',
            'price' => 'decimal:2',
            'item_total' => 'decimal:2',
        ];
    }

    public function order()
    {
        return $this->belongsTo(Order::class, 'order_id');
    }

    public function product()
    {
        return $this->belongsTo(Product::class, 'product_id');
    }

    public function dosage()
    {
        return $this->belongsTo(ProductDosage::class, 'product_dosage_id');
    }
}
