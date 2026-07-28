<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Patient extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'mrn',
        'first_name',
        'last_name',
        'date_of_birth',
        'gender',
        'phone',
        'national_id',
        'address',
        'allergies',
        'blood_group',
    ];

    protected $casts = [
        'date_of_birth' => 'date',
    ];

    public function visits()
    {
        return $this->hasMany(Visit::class)->latest();
    }

    public function consultations()
    {
        return $this->hasManyThrough(Consultation::class, Visit::class);
    }

    public function documents()
    {
        return $this->hasMany(PatientDocument::class)->latest();
    }

    public function clinicalRecords()
    {
        return $this->hasMany(ClinicalRecord::class)->latest('record_date');
    }

    public function appointments()
    {
        return $this->hasMany(Appointment::class)->latest('scheduled_at');
    }

    public function fullName(): string
    {
        return "{$this->first_name} {$this->last_name}";
    }

    public function getNameAttribute(): string
    {
        return $this->fullName();
    }
}
