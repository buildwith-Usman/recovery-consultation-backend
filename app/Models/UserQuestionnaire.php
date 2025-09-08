<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class UserQuestionnaire extends Model
{
    /**
     * The attributes that are mass assignable.
     *
     * @var array<string>
     */
    protected $fillable = [
        'user_id',
        'question',
        'options',
        'answer',
        'key',
    ];

    /**
     * Get the user that owns the questionnaire.
     */
    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }
}
