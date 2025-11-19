<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Order extends Model
{
    protected $fillable = [
        'order_number',
        'user_id',
        'subtotal',
        'delivery_fee',
        'tax_amount',
        'other_fees',
        'total_amount',
        'payment_method',
        'payment_status',
        'order_status',
        'customer_name',
        'customer_phone',
        'delivery_address',
        'city',
        'country',
        'postal_code',
        'prescription_id',
        'is_prescription_order',
        'placed_at',
        'dispatched_at',
        'delivered_at',
        'completed_at'
    ];

    protected function casts(): array
    {
        return [
            'subtotal' => 'decimal:2',
            'delivery_fee' => 'decimal:2',
            'tax_amount' => 'decimal:2',
            'other_fees' => 'decimal:2',
            'total_amount' => 'decimal:2',
            'is_prescription_order' => 'boolean',
            'placed_at' => 'datetime',
            'dispatched_at' => 'datetime',
            'delivered_at' => 'datetime',
            'completed_at' => 'datetime',
        ];
    }

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function prescription()
    {
        return $this->belongsTo(Prescription::class, 'prescription_id');
    }

    public function items()
    {
        return $this->hasMany(OrderItem::class, 'order_id');
    }

    public function scopeByStatus($query, $status)
    {
        return $query->where('order_status', $status);
    }

    public function scopeForUser($query, $userId)
    {
        return $query->where('user_id', $userId);
    }

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($order) {
            if (empty($order->order_number)) {
                $order->order_number = self::generateOrderNumber();
            }
            if (empty($order->placed_at)) {
                $order->placed_at = now();
            }
        });
    }

    public static function generateOrderNumber()
    {
        do {
            $orderNumber = 'MED-' . str_pad(mt_rand(1, 999999), 6, '0', STR_PAD_LEFT);
        } while (self::where('order_number', $orderNumber)->exists());

        return $orderNumber;
    }
}
