<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class UserTimeSlot extends Model
{
    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'user_id',
        'available_time_id',
        'weekday',
        'slot_start_time',
        'slot_end_time',
        'is_booked'
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'is_booked' => 'boolean',
        ];
    }

    public function user() {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function availableTime() {
        return $this->belongsTo(UserAvailableTime::class, 'available_time_id');
    }
}
