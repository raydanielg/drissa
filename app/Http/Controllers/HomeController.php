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

        $recentVisits = Visit::with('patient')
            ->latest()
            ->limit(10)
            ->get();

        $waitingForDoctor = Visit::with('patient')
            ->where('status', VisitStatus::WaitingForDoctor->value)
            ->when($user->isDoctor(), fn ($q) => $q->where('doctor_id', $user->id))
            ->limit(5)
            ->get();

        // Weekly revenue chart
        $revenueDays = collect();
        $dayLabels = [];
        for ($i = 6; $i >= 0; $i--) {
            $date = now()->subDays($i);
            $amount = Payment::whereDate('created_at', $date)->sum('amount') ?? 0;
            $revenueDays->push((int) $amount);
            $dayLabels[] = $date->format('D');
        }

        // Performance rings
        $completedVisits = Visit::where('status', VisitStatus::Completed->value)->count();
        $totalClosed = Visit::whereIn('status', [VisitStatus::Completed->value, VisitStatus::Cancelled->value])->count();
        $successRate = $totalClosed > 0 ? round(($completedVisits / $totalClosed) * 100) : 96;
        $todayVisits = max(Visit::whereDate('registered_at', today())->count(), 1);
        $occupied = Visit::whereDate('registered_at', today())->whereNotIn('status', [VisitStatus::Completed->value, VisitStatus::Cancelled->value])->count();
        $occupancyRate = min(100, round(($occupied / $todayVisits) * 100));
        $monthlyTarget = 120;
        $treatmentTargetPct = min(100, round(Visit::where('status', VisitStatus::Completed->value)->whereMonth('registered_at', now()->month)->count() / $monthlyTarget * 100));

        // Today's schedule
        $todayAppointments = Appointment::whereDate('appointment_date', today())
            ->with('patient', 'doctor')
            ->orderBy('start_time')
            ->get();

        $recentPatients = Patient::latest()->limit(5)->get();

        // Lists for quick forms
        $patientsList = Patient::orderBy('name')->get();
        $doctorsList = User::role('doctor')->get();

        return view('dashboard', compact(
            'user',
            'stats',
            'recentVisits',
            'waitingForDoctor',
            'revenueDays',
            'dayLabels',
            'successRate',
            'occupancyRate',
            'treatmentTargetPct',
            'todayAppointments',
            'recentPatients',
            'patientsList',
            'doctorsList'
        ));
    }
}
