<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class UserAvailableTime extends Model
{
    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'user_id',
        'weekday',
        'session_duration',
        'status',
        'start_time',
        'end_time'
    ];

    public function user() {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function timeSlots() {
        return $this->hasMany(UserTimeSlot::class, 'available_time_id');
    }
}
