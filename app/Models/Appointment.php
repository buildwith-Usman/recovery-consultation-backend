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
        'price'
    ];
}
