<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PrescriptionItem extends Model
{
    protected $fillable = [
        'prescription_id',
        'product_id',
        'product_name',
        'dosage_instructions',
        'quantity',
        'duration_days'
    ];

    protected function casts(): array
    {
        return [
            'quantity' => 'integer',
            'duration_days' => 'integer',
        ];
    }

    public function prescription()
    {
        return $this->belongsTo(Prescription::class, 'prescription_id');
    }

    public function product()
    {
        return $this->belongsTo(Product::class, 'product_id');
    }
}
