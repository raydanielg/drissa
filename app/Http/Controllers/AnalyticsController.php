<?php

namespace App\Http\Controllers;

use App\Models\Appointment;
use App\Models\Invoice;
use App\Models\Patient;
use App\Models\Visit;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class AnalyticsController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
        $this->middleware('role:admin');
    }

    public function index()
    {
        $totalPatients = Patient::count();
        $totalDoctors = User::role('doctor')->count();
        $totalAppointments = Appointment::count();
        $totalRevenue = Invoice::where('paid', true)->sum('total') ?? 0;

        $todayVisits = Visit::whereDate('registered_at', today())->count();
        $todayRevenue = Invoice::whereDate('updated_at', today())->where('paid', true)->sum('total') ?? 0;

        $monthlyVisits = collect();
        $visitLabels = [];
        for ($i = 5; $i >= 0; $i--) {
            $date = now()->subMonths($i);
            $count = Visit::whereMonth('registered_at', $date->month)
                ->whereYear('registered_at', $date->year)
                ->count();
            $monthlyVisits->push($count);
            $visitLabels[] = $date->format('M Y');
        }

        $genderStats = Patient::select('gender', DB::raw('COUNT(*) as count'))
            ->groupBy('gender')
            ->get();

        $typeStats = Appointment::select('type', DB::raw('COUNT(*) as count'))
            ->whereNotNull('type')
            ->groupBy('type')
            ->get();

        $monthlyRevenue = collect();
        $revenueLabels = [];
        for ($i = 5; $i >= 0; $i--) {
            $date = now()->subMonths($i);
            $rev = Invoice::where('paid', true)
                ->whereMonth('updated_at', $date->month)
                ->whereYear('updated_at', $date->year)
                ->sum('total') ?? 0;
            $monthlyRevenue->push($rev);
            $revenueLabels[] = $date->format('M Y');
        }

        $topDoctors = User::role('doctor')
            ->withCount(['visits as visits_count' => function ($q) {
                $q->whereDate('registered_at', '>=', now()->subDays(30));
            }])
            ->orderByDesc('visits_count')
            ->limit(5)
            ->get();

        $visitStatusStats = Visit::select('status', DB::raw('COUNT(*) as count'))
            ->whereDate('registered_at', today())
            ->groupBy('status')
            ->get();

        return view('analytics.index', compact(
            'totalPatients', 'totalDoctors', 'totalAppointments', 'totalRevenue',
            'todayVisits', 'todayRevenue',
            'monthlyVisits', 'visitLabels',
            'genderStats', 'typeStats',
            'monthlyRevenue', 'revenueLabels',
            'topDoctors', 'visitStatusStats'
        ));
    }
}
