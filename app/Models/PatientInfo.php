<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PatientInfo extends Model
{
    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'patient_infos';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'user_id',
        'looking_for',
        'completed',
        'dob',
        'gender',
        'age',
        'blood_group'
    ];

    /**
     * Get the patient that owns the info.
     */
    public function user(){
        return $this->belongsTo(User::class, 'user_id');
    }
}
