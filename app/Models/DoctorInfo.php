<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class DoctorInfo extends Model
{
    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'doctor_infos';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'user_id',
        'specialization',
        'experience',
        'dob',
        'degree',
        'license_no',
        'country_id',
        'gender',
        'age',
        'commision_type',
        'commision_value',
        'completed',
        'status'
    ];

    /**
     * Get the doctor that owns the info.
     */
    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }
}
