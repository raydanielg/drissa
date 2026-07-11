<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class LabEquipment extends Model
{
    use HasFactory;

    protected $table = 'lab_equipment';

    protected $fillable = [
        'name', 'model', 'serial_number', 'manufacturer',
        'purchase_date', 'last_service_date', 'next_service_date',
        'status', 'notes',
    ];

    protected $casts = [
        'purchase_date' => 'date',
        'last_service_date' => 'date',
        'next_service_date' => 'date',
    ];
}
