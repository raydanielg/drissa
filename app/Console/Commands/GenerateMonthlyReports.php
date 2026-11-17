<?php

namespace App\Console\Commands;

use App\Models\ManagerReport;
use App\Models\Patient;
use App\Models\Payment;
use App\Models\User;
use App\Models\Visit;
use Carbon\Carbon;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class GenerateMonthlyReports extends Command
{
    protected $signature = 'reports:generate-monthly {--month=} {--year=}';

    protected $description = 'Pre-fill monthly manager reports with system metrics for the given (or previous) month';

    public function handle(): int
    {
        $anchor = Carbon::create(
            (int) ($this->option('year') ?: now()->subMonth()->year),
            (int) ($this->option('month') ?: now()->subMonth()->month),
            1
        );

        $start = $anchor->copy()->startOfMonth();
        $end = $anchor->copy()->endOfMonth();

        $metrics = [
            'revenue_overall' => round((float) Payment::whereBetween('created_at', [$start, $end])->sum('amount'), 2),
            'new_clients' => Patient::whereBetween('created_at', [$start, $end])->count(),
            'returning_clients' => Visit::whereBetween('registered_at', [$start, $end])
                ->whereHas('patient', fn ($q) => $q->where('created_at', '<', $start))
                ->distinct()->count('patient_id'),
            'product_sales_costs' => round((float) DB::table('dispenses')
                ->join('medications', 'dispenses.medication_id', '=', 'medications.id')
                ->whereBetween('dispenses.created_at', [$start, $end])
                ->sum(DB::raw('dispenses.quantity * medications.unit_price')), 2),
        ];

        $created = 0;

        foreach (User::role('manager')->get() as $manager) {
            $exists = ManagerReport::where('user_id', $manager->id)
                ->where('type', 'monthly')
                ->where('month', $anchor->month)
                ->where('year', $anchor->year)
                ->exists();

            if ($exists) {
                continue;
            }

            ManagerReport::create(array_merge($metrics, [
                'user_id' => $manager->id,
                'type' => 'monthly',
                'report_date' => $end->toDateString(),
                'month' => $anchor->month,
                'year' => $anchor->year,
            ]));

            $created++;
        }

        $this->info("Generated {$created} monthly report(s) for {$anchor->format('F Y')}.");

        return self::SUCCESS;
    }
}
