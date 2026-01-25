<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'medicine_name',
        'image_id',
        'category_id',
        'price',
        'stock_quantity',
        'availability_status',
        'ingredients',
        'discount_type',
        'discount_value',
        'how_to_use',
        'description',
        'is_visible',
        'is_temporarily_hidden',
        'created_by'
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'price' => 'decimal:2',
            'discount_value' => 'decimal:2',
            'stock_quantity' => 'integer',
            'is_visible' => 'boolean',
            'is_temporarily_hidden' => 'boolean',
        ];
    }

    /**
     * The accessors to append to the model's array form.
     *
     * @var array
     */
    protected $appends = ['final_price'];

    /**
     * Get the image associated with the product.
     */
    public function image()
    {
        return $this->belongsTo(File::class, 'image_id');
    }

    /**
     * Get the category of the product.
     */
    public function category()
    {
        return $this->belongsTo(Category::class, 'category_id');
    }

    /**
     * Get the admin user who created the product.
     */
    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    /**
     * Get the final price after discount.
     */
    public function getFinalPriceAttribute()
    {
        if (!$this->discount_type || !$this->discount_value) {
            return $this->price;
        }

        if ($this->discount_type === 'percentage') {
            return $this->price - ($this->price * $this->discount_value / 100);
        }

        if ($this->discount_type === 'flat') {
            return max(0, $this->price - $this->discount_value);
        }

        return $this->price;
    }

    /**
     * Scope a query to only include visible products for customers.
     */
    public function scopeVisibleToCustomers($query)
    {
        return $query->where('is_visible', true)
            ->where('is_temporarily_hidden', false)
            ->whereIn('availability_status', ['in_stock', 'low_stock']);
    }

    /**
     * Scope a query to filter by category.
     */
    public function scopeByCategory($query, $categoryId)
    {
        return $query->where('category_id', $categoryId);
    }

    /**
     * Scope a query to search by medicine name.
     */
    public function scopeSearch($query, $search)
    {
        return $query->where('medicine_name', 'like', '%' . $search . '%');
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

    public function favoritedByUsers()
    {
        return $this->belongsToMany(User::class, 'user_favorite_products')
            ->withTimestamps();
    }
}
