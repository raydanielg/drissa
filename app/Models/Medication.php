<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Medication extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'generic_name',
        'form',
        'batch_no',
        'manufacturer',
        'category',
        'description',
        'stock_quantity',
        'reorder_level',
        'unit_price',
        'purchase_price',
        'expiry_date',
        'is_active',
    ];

    protected $casts = [
        'expiry_date' => 'date',
        'is_active' => 'boolean',
        'unit_price' => 'decimal:2',
        'purchase_price' => 'decimal:2',
    ];

    public function profitPerUnit(): float
    {
        return (float) $this->unit_price - (float) $this->purchase_price;
    }

    public function totalProfitPotential(): float
    {
        return $this->profitPerUnit() * (int) $this->stock_quantity;
    }

    public function stockValue(): float
    {
        return (float) $this->purchase_price * (int) $this->stock_quantity;
    }

    public function retailValue(): float
    {
        return (float) $this->unit_price * (int) $this->stock_quantity;
    }
}
