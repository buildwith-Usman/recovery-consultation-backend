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
            'discount_value' => 'decimal:2',
            'is_visible' => 'boolean',
            'is_temporarily_hidden' => 'boolean',
        ];
    }

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
     * Get the dosage variations for the product.
     */
    public function dosages()
    {
        return $this->hasMany(ProductDosage::class)->orderBy('sort_order');
    }

    /**
     * Scope a query to only include visible products for customers.
     */
    public function scopeVisibleToCustomers($query)
    {
        return $query->where('is_visible', true)
            ->where('is_temporarily_hidden', false)
            ->whereHas('dosages', function ($q) {
                $q->whereIn('availability_status', ['in_stock', 'low_stock']);
            });
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
     * Get the additional gallery images for the product.
     */
    public function images()
    {
        return $this->hasMany(ProductImage::class)->orderBy('sort_order');
    }

    public function featuredByUsers()
    {
        return $this->belongsToMany(User::class, 'user_feature_products')
            ->withTimestamps();
    }
}
