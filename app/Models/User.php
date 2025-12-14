<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Passport\HasApiTokens;

class User extends Authenticatable
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasApiTokens, HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'type',
        'phone',
        'profile_image_id',
        'bio',
        'email_verification_code',
        'email_verification_code_expires_at',
        'is_verified',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'email_verification_code_expires_at' => 'datetime',
            'password' => 'hashed',
            'is_verified' => 'boolean',
        ];
    }

    public function patientInfo() {
        return $this->hasOne(PatientInfo::class, 'user_id', 'id');
    }

    public function doctorInfo() {
        return $this->hasOne(DoctorInfo::class, 'user_id', 'id');
    }

    public function questionnaires() {
        return $this->hasMany(UserQuestionnaire::class, 'user_id', 'id');
    }

    public function userLanguages() {
        return $this->hasMany(UserLanguage::class, 'user_id', 'id');
    }

    public function reviews() {
        return $this->hasMany(UserReview::class, 'receiver_id', 'id');
    }

    public function file() {
        return $this->belongsTo(File::class, 'profile_image_id');
    }

    public function doc_appointments() {
        return $this->hasMany(Appointment::class, 'doc_user_id');
    }

    public function available_times() {
        return $this->hasMany(UserAvailableTime::class, 'user_id');
    }

    public function timeSlots() {
        return $this->hasMany(UserTimeSlot::class, 'user_id');
    }
}
