<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class LabResult extends Model
{
    use HasFactory;

    protected $fillable = [
        'lab_order_item_id',
        'parameter',
        'value',
        'unit',
        'reference_range',
        'flag',
    ];

    public function labOrderItem()
    {
        return $this->belongsTo(LabOrderItem::class);
    }
}
