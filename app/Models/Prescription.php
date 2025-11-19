<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Prescription extends Model
{
    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'appointment_id',
        'prescribed_by_doctor_id',
        'patient_user_id',
        'prescription_image_id',
        'prescription_date',
        'notes',
        'status',
        'created_by'
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'prescription_date' => 'date',
        ];
    }

    /**
     * Get the appointment associated with the prescription.
     */
    public function appointment()
    {
        return $this->belongsTo(Appointment::class, 'appointment_id');
    }

    /**
     * Get the doctor who prescribed.
     */
    public function doctor()
    {
        return $this->belongsTo(User::class, 'prescribed_by_doctor_id');
    }

    /**
     * Get the patient.
     */
    public function patient()
    {
        return $this->belongsTo(User::class, 'patient_user_id');
    }

    /**
     * Get the prescription image.
     */
    public function prescriptionImage()
    {
        return $this->belongsTo(File::class, 'prescription_image_id');
    }

    /**
     * Get the user who created this prescription.
     */
    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    /**
     * Get the prescription items.
     */
    public function items()
    {
        return $this->hasMany(PrescriptionItem::class, 'prescription_id');
    }

    /**
     * Get orders associated with this prescription.
     */
    public function orders()
    {
        return $this->hasMany(Order::class, 'prescription_id');
    }

    /**
     * Scope a query to only include prescriptions for a specific patient.
     */
    public function scopeForPatient($query, $patientId)
    {
        return $query->where('patient_user_id', $patientId);
    }

    /**
     * Scope a query to only include prescriptions by a specific doctor.
     */
    public function scopeByDoctor($query, $doctorId)
    {
        return $query->where('prescribed_by_doctor_id', $doctorId);
    }

    /**
     * Scope a query to filter by status.
     */
    public function scopeByStatus($query, $status)
    {
        return $query->where('status', $status);
    }
}
