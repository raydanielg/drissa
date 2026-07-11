<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Vital extends Model
{
    use HasFactory;

    protected $fillable = [
        'visit_id',
        'temperature',
        'blood_pressure',
        'pulse',
        'weight',
        'height',
        'respiratory_rate',
        'oxygen_saturation',
        'notes',
    ];

    public function visit()
    {
        return $this->belongsTo(Visit::class);
    }
}
