<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Dispense extends Model
{
    use HasFactory;

    protected $fillable = [
        'prescription_id',
        'medication_id',
        'quantity',
        'dispensed_by',
    ];

    public function prescription()
    {
        return $this->belongsTo(Prescription::class);
    }

    public function medication()
    {
        return $this->belongsTo(Medication::class);
    }

    public function pharmacist()
    {
        return $this->belongsTo(User::class, 'dispensed_by');
    }
}
