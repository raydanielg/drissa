<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class UltrasoundOrderItem extends Model
{
    use HasFactory;

    protected $fillable = [
        'ultrasound_order_id',
        'ultrasound_service_id',
    ];

    public function ultrasoundOrder()
    {
        return $this->belongsTo(UltrasoundOrder::class);
    }

    public function ultrasoundService()
    {
        return $this->belongsTo(UltrasoundService::class);
    }
}
