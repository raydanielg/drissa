<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class UltrasoundAttachment extends Model
{
    use HasFactory;

    protected $fillable = [
        'ultrasound_order_id',
        'file_name',
        'file_path',
        'mime_type',
        'file_size',
        'uploaded_by',
    ];

    public function ultrasoundOrder()
    {
        return $this->belongsTo(UltrasoundOrder::class);
    }

    public function uploadedBy()
    {
        return $this->belongsTo(User::class, 'uploaded_by');
    }
}
