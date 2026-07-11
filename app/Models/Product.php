<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
    use HasFactory;

    protected $fillable = [
        'name', 'sku', 'category', 'description',
        'cost_price', 'selling_price', 'quantity',
        'reorder_level', 'unit', 'is_active',
    ];

    protected $casts = [
        'cost_price' => 'decimal:2',
        'selling_price' => 'decimal:2',
        'is_active' => 'boolean',
    ];

    public function isLowStock(): bool
    {
        return $this->quantity <= $this->reorder_level;
    }
}
