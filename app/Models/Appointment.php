<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Appointment extends Model
{
    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'pat_user_id',
        'doc_user_id',
        'date',
        'start_time',
        'end_time',
        'start_time_in_secconds',
        'end_time_in_secconds',
        'price',
        'time_slot_id'
    ];

    public function patient() {
        return $this->belongsTo(User::class, 'pat_user_id');
    }

    public function doctor() {
        return $this->belongsTo(User::class, 'doc_user_id');
    }

    public function timeSlot() {
        return $this->belongsTo(UserTimeSlot::class, 'time_slot_id');
    }
}
