<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Appointment extends Model
{
    use HasFactory;

    protected $fillable = [
        'patient_id',
        'doctor_id',
        'scheduled_at',
        'status',
        'notes',
        'type',
    ];

    protected $casts = [
        'scheduled_at' => 'datetime',
    ];

    public function startTime(): ?string
    {
        return $this->scheduled_at?->format('H:i');
    }

    public function patient()
    {
        return $this->belongsTo(Patient::class);
    }

    public function doctor()
    {
        return $this->belongsTo(User::class, 'doctor_id');
    }
}
