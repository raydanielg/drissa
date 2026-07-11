<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Prescription extends Model
{
    use HasFactory;

    protected $fillable = [
        'visit_id',
        'doctor_id',
        'status',
    ];

    public function visit()
    {
        return $this->belongsTo(Visit::class);
    }

    public function doctor()
    {
        return $this->belongsTo(User::class, 'doctor_id');
    }

    public function items()
    {
        return $this->hasMany(PrescriptionItem::class);
    }

    public function dispenses()
    {
        return $this->hasMany(Dispense::class);
    }
}
