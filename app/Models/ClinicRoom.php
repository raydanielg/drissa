<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ClinicRoom extends Model
{
    use HasFactory;

    protected $fillable = [
        'name', 'code', 'department_id', 'type', 'status', 'description', 'capacity',
    ];

    public function department()
    {
        return $this->belongsTo(Department::class);
    }
}
