<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class LabOrder extends Model
{
    use HasFactory;

    protected $fillable = [
        'visit_id',
        'ordered_by',
        'processed_by',
        'status',
        'clinical_notes',
        'completed_at',
    ];

    protected $casts = [
        'completed_at' => 'datetime',
    ];

    public function visit()
    {
        return $this->belongsTo(Visit::class);
    }

    public function items()
    {
        return $this->hasMany(LabOrderItem::class);
    }

    public function results()
    {
        return $this->hasManyThrough(LabResult::class, LabOrderItem::class);
    }

    public function doctor()
    {
        return $this->belongsTo(User::class, 'ordered_by');
    }

    public function labTech()
    {
        return $this->belongsTo(User::class, 'processed_by');
    }

    public function attachments()
    {
        return $this->hasMany(LabAttachment::class);
    }
}
