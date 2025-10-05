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
}
