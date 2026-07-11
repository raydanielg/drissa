<?php

namespace App\Http\Controllers;

use App\Enums\VisitStatus;
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

        return view('dashboard', compact('user', 'stats', 'recentVisits', 'waitingForDoctor'));
    }
}
