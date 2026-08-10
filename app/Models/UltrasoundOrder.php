<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class UltrasoundOrder extends Model
{
    use HasFactory;

    protected $fillable = [
        'visit_id',
        'patient_id',
        'ordered_by',
        'processed_by',
        'status',
        'clinical_notes',
        'findings',
        'impression',
        'completed_at',
    ];

    protected $casts = [
        'completed_at' => 'datetime',
    ];

    public function visit()
    {
        return $this->belongsTo(Visit::class);
    }

    public function patient()
    {
        return $this->belongsTo(Patient::class);
    }

    public function doctor()
    {
        return $this->belongsTo(User::class, 'ordered_by');
    }

    public function ultrasoundTech()
    {
        return $this->belongsTo(User::class, 'processed_by');
    }

    public function items()
    {
        return $this->hasMany(UltrasoundOrderItem::class);
    }

    public function attachments()
    {
        return $this->hasMany(UltrasoundAttachment::class);
    }
}
