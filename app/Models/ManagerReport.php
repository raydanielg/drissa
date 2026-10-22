<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ManagerReport extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'type',
        'report_date',
        'week_start',
        'week_end',
        'revenue_overall',
        'supplement_sales_ppv',
        'product_sales_costs',
        'new_clients',
        'returning_clients',
        'lab_tools_expenses',
        'office_expenses',
        'department_reports',
        'staff_performance',
        'challenges',
        'action_plan',
        'meeting_reports',
        'manager_signature',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function periodLabel(): string
    {
        if ($this->type === 'weekly' && $this->week_start && $this->week_end) {
            return $this->week_start->format('d M Y').' - '.$this->week_end->format('d M Y');
        }

        return $this->report_date->format('d M Y');
    }

    public function totalExpenses(): float
    {
        return (float) $this->lab_tools_expenses + (float) $this->office_expenses;
    }

    protected function casts(): array
    {
        return [
            'report_date' => 'date',
            'week_start' => 'date',
            'week_end' => 'date',
            'department_reports' => 'array',
            'revenue_overall' => 'decimal:2',
            'supplement_sales_ppv' => 'decimal:2',
            'product_sales_costs' => 'decimal:2',
            'lab_tools_expenses' => 'decimal:2',
            'office_expenses' => 'decimal:2',
        ];
    }
}
