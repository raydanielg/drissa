<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Visit extends Model
{
    use HasFactory;

    protected $fillable = [
        'visit_number',
        'patient_id',
        'doctor_id',
        'received_by',
        'status',
        'chief_complaint',
        'type',
        'registered_at',
        'completed_at',
    ];

    protected $casts = [
        'registered_at' => 'datetime',
        'completed_at' => 'datetime',
    ];

    public function patient()
    {
        return $this->belongsTo(Patient::class);
    }

    public function doctor()
    {
        return $this->belongsTo(User::class, 'doctor_id');
    }

    public function receptionist()
    {
        return $this->belongsTo(User::class, 'received_by');
    }

    public function vitals()
    {
        return $this->hasOne(Vital::class);
    }

    public function consultation()
    {
        return $this->hasOne(Consultation::class);
    }

    public function labOrders()
    {
        return $this->hasMany(LabOrder::class);
    }

    public function prescriptions()
    {
        return $this->hasMany(Prescription::class);
    }

    public function invoice()
    {
        return $this->hasOne(Invoice::class);
    }

    public function statusLogs()
    {
        return $this->hasMany(VisitStatusLog::class);
    }

    public function clinicalRecord()
    {
        return $this->hasOne(ClinicalRecord::class);
    }
}
