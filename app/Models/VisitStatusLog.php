<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class VisitStatusLog extends Model
{
    use HasFactory;

    protected $fillable = [
        'visit_id',
        'from_status',
        'to_status',
        'changed_by',
        'note',
    ];

    public function visit()
    {
        return $this->belongsTo(Visit::class);
    }

    public function changedBy()
    {
        return $this->belongsTo(User::class, 'changed_by');
    }
}
