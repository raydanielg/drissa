<?php

namespace App\Http\Controllers;

use App\Enums\VisitStatus;
use App\Models\Appointment;
use App\Models\Invoice;
use App\Models\Medication;
use App\Models\Patient;
use App\Models\Payment;
use App\Models\Prescription;
use App\Models\User;
use App\Models\Visit;
use Illuminate\Http\Request;

class HomeController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function index()
    {
        $user = auth()->user();

        $stats = [
            'total_patients' => Patient::count(),
            'total_visits' => Visit::count(),
            'visits_today' => Visit::whereDate('registered_at', today())->count(),
            'total_doctors' => User::role('doctor')->count(),
            'revenue_today' => Payment::whereDate('created_at', today())->sum('amount'),
            'revenue_this_week' => Payment::whereBetween('created_at', [now()->startOfWeek(), now()->endOfWeek()])->sum('amount'),
            'pending_payments' => Visit::where('status', VisitStatus::WaitingForPayment->value)->count(),
            'waiting_lab' => Visit::where('status', VisitStatus::WaitingForLab->value)->count(),
            'waiting_pharmacy' => Prescription::where('status', 'pending')->count(),
            'low_stock' => Medication::whereColumn('stock_quantity', '<=', 'reorder_level')->count(),
        ];

        $recentVisits = Visit::with(['patient' => fn ($q) => $q->withTrashed()])
            ->latest()
            ->limit(10)
            ->get();

        $waitingForDoctor = Visit::with(['patient' => fn ($q) => $q->withTrashed()])
            ->where('status', VisitStatus::WaitingForDoctor->value)
            ->when($user->isDoctor(), fn ($q) => $q->where('doctor_id', $user->id))
            ->limit(5)
            ->get();

        // Revenue per day (last 30 days) — one grouped query, split into 7/30 day charts
        $revenueByDay = Payment::where('created_at', '>=', now()->subDays(29)->startOfDay())
            ->selectRaw('DATE(created_at) as d, SUM(amount) as total')
            ->groupBy('d')
            ->pluck('total', 'd');

        $revenueDays = collect();
        $dayLabels = [];
        $monthlyDays = collect();
        $monthLabels = [];
        for ($i = 29; $i >= 0; $i--) {
            $date = now()->subDays($i);
            $amount = (int) ($revenueByDay[$date->toDateString()] ?? 0);
            $monthlyDays->push($amount);
            $monthLabels[] = $date->format('d M');
            if ($i <= 6) {
                $revenueDays->push($amount);
                $dayLabels[] = $date->format('D');
            }
        }

        // Performance metrics
        $completedVisits = Visit::where('status', VisitStatus::Completed->value)->count();
        $totalClosed = Visit::whereIn('status', [VisitStatus::Completed->value, VisitStatus::Cancelled->value])->count();
        $successRate = $totalClosed > 0 ? round(($completedVisits / $totalClosed) * 100) : 96;
        $todayVisits = max(Visit::whereDate('registered_at', today())->count(), 1);
        $occupied = Visit::whereDate('registered_at', today())->whereNotIn('status', [VisitStatus::Completed->value, VisitStatus::Cancelled->value])->count();
        $occupancyRate = min(100, round(($occupied / $todayVisits) * 100));
        $monthlyTarget = 120;
        $treatmentTargetPct = min(100, round(Visit::where('status', VisitStatus::Completed->value)->whereMonth('registered_at', now()->month)->count() / $monthlyTarget * 100));

        // Top performing doctors
        $topDoctors = User::role('doctor')
            ->withCount(['visitsAsDoctor as completed_visits' => fn($q) => $q->where('status', VisitStatus::Completed->value)])
            ->orderByDesc('completed_visits')
            ->limit(5)
            ->get();

        // Department performance
        $visitsByDept = Visit::join('users', 'visits.doctor_id', '=', 'users.id')
            ->whereMonth('visits.registered_at', now()->month)
            ->selectRaw('users.department_id, count(*) as c')
            ->groupBy('users.department_id')
            ->pluck('c', 'department_id');

        $departmentPerformance = \App\Models\Department::where('is_active', true)
            ->withCount(['users as doctors_count' => fn($q) => $q->role('doctor')])
            ->get()
            ->map(fn($dept) => [
                'name' => $dept->name,
                'visits' => (int) ($visitsByDept[$dept->id] ?? 0),
            ])
            ->sortByDesc('visits')
            ->values();

        // Visit trend (last 7 days) — one grouped query
        $visitsByDay = Visit::where('registered_at', '>=', now()->subDays(6)->startOfDay())
            ->selectRaw('DATE(registered_at) as d, count(*) as c')
            ->groupBy('d')
            ->pluck('c', 'd');

        $visitTrend = collect();
        $visitLabels = [];
        for ($i = 6; $i >= 0; $i--) {
            $date = now()->subDays($i);
            $visitTrend->push((int) ($visitsByDay[$date->toDateString()] ?? 0));
            $visitLabels[] = $date->format('D');
        }

        // Today's schedule
        $todayAppointments = Appointment::whereDate('scheduled_at', today())
            ->with(['patient' => fn ($q) => $q->withTrashed(), 'doctor'])
            ->orderBy('scheduled_at')
            ->get();

        $recentPatients = Patient::latest()->limit(5)->get();

        // Lists for quick forms
        $patientsList = Patient::select('id', 'first_name', 'last_name', 'mrn')->orderBy('first_name')->limit(400)->get();
        $doctorsList = User::role('doctor')->get();

        return view('dashboard', compact(
            'user',
            'stats',
            'recentVisits',
            'waitingForDoctor',
            'revenueDays',
            'dayLabels',
            'monthlyDays',
            'monthLabels',
            'successRate',
            'occupancyRate',
            'treatmentTargetPct',
            'todayAppointments',
            'recentPatients',
            'patientsList',
            'doctorsList',
            'topDoctors',
            'departmentPerformance',
            'visitTrend',
            'visitLabels'
        ));
    }

    public function stats()
    {
        $stats = [
            'total_patients' => Patient::count(),
            'total_visits' => Visit::count(),
            'visits_today' => Visit::whereDate('registered_at', today())->count(),
            'revenue_today' => Payment::whereDate('created_at', today())->sum('amount'),
            'revenue_this_week' => Payment::whereBetween('created_at', [now()->startOfWeek(), now()->endOfWeek()])->sum('amount'),
            'pending_payments' => Visit::where('status', VisitStatus::WaitingForPayment->value)->count(),
        ];

        $revenueDays = collect();
        $dayLabels = [];
        for ($i = 6; $i >= 0; $i--) {
            $date = now()->subDays($i);
            $revenueDays->push((int) (Payment::whereDate('created_at', $date)->sum('amount') ?? 0));
            $dayLabels[] = $date->format('D');
        }

        $visitTrend = collect();
        $visitLabels = [];
        for ($i = 6; $i >= 0; $i--) {
            $date = now()->subDays($i);
            $visitTrend->push(Visit::whereDate('registered_at', $date)->count());
            $visitLabels[] = $date->format('D');
        }

        return response()->json([
            'stats' => $stats,
            'revenueDays' => $revenueDays->values(),
            'dayLabels' => $dayLabels,
            'visitTrend' => $visitTrend->values(),
            'visitLabels' => $visitLabels,
        ]);
    }
}
