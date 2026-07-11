<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ClinicalRecord extends Model
{
    use HasFactory;

    protected $fillable = [
        'patient_id', 'visit_id', 'doctor_id', 'appointment_id',
        'chief_complaint', 'symptoms', 'diagnosis', 'treatment_plan',
        'notes', 'prescription', 'record_date',
    ];

    protected $casts = [
        'record_date' => 'date',
    ];

    public function patient()
    {
        return $this->belongsTo(Patient::class);
    }

    public function visit()
    {
        return $this->belongsTo(Visit::class);
    }

    public function doctor()
    {
        return $this->belongsTo(User::class, 'doctor_id');
    }

    public function appointment()
    {
        return $this->belongsTo(Appointment::class);
    }
}
