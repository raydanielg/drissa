<?php

namespace App\Http\Controllers;

use App\Models\Invoice;
use App\Models\Medication;
use App\Models\Patient;
use App\Models\Payment;
use App\Models\User;
use App\Models\Visit;
use Illuminate\Http\Request;

class ReportController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function index()
    {
        $stats = [
            'total_patients' => Patient::count(),
            'total_visits' => Visit::count(),
            'visits_today' => Visit::whereDate('registered_at', today())->count(),
            'total_doctors' => User::role('doctor')->count(),
            'revenue_today' => Payment::whereDate('created_at', today())->sum('amount'),
            'revenue_this_month' => Payment::whereMonth('created_at', now()->month)->sum('amount'),
            'low_stock' => Medication::whereColumn('stock_quantity', '<=', 'reorder_level')->count(),
            'unpaid_invoices' => Invoice::where('status', '!=', 'paid')->count(),
        ];

        $payments = Payment::with('invoice.visit')
            ->latest()
            ->limit(10)
            ->get();

        return view('reports.index', compact('stats', 'payments'));
    }

    public function sales(Request $request)
    {
        $from = $request->input('from', now()->subDays(30)->format('Y-m-d'));
        $to = $request->input('to', now()->format('Y-m-d'));
        $payments = Payment::whereDate('created_at', '>=', $from)
            ->whereDate('created_at', '<=', $to)
            ->latest()
            ->paginate(30);
        $total = Payment::whereDate('created_at', '>=', $from)
            ->whereDate('created_at', '<=', $to)
            ->sum('amount');
        return view('reports.sales', compact('payments', 'total', 'from', 'to'));
    }

    public function patients(Request $request)
    {
        $from = $request->input('from', now()->subDays(30)->format('Y-m-d'));
        $to = $request->input('to', now()->format('Y-m-d'));
        $patients = Patient::whereDate('created_at', '>=', $from)
            ->whereDate('created_at', '<=', $to)
            ->latest()
            ->paginate(30);
        return view('reports.patients', compact('patients', 'from', 'to'));
    }

    public function doctorPerformance(Request $request)
    {
        $from = $request->input('from', now()->subDays(30)->format('Y-m-d'));
        $to = $request->input('to', now()->format('Y-m-d'));
        $doctors = User::role('doctor')->withCount(['visitsAsDoctor' => function ($q) use ($from, $to) {
            $q->whereDate('registered_at', '>=', $from)->whereDate('registered_at', '<=', $to);
        }])->get();
        return view('reports.doctors', compact('doctors', 'from', 'to'));
    }

    public function stock()
    {
        $products = \App\Models\Product::latest()->paginate(30);
        $medications = Medication::latest()->paginate(30);
        return view('reports.stock', compact('products', 'medications'));
    }

    public function revenue(Request $request)
    {
        $year = $request->input('year', now()->year);
        $monthly = [];
        for ($m = 1; $m <= 12; $m++) {
            $monthly[] = Payment::whereYear('created_at', $year)->whereMonth('created_at', $m)->sum('amount');
        }
        return view('reports.revenue', compact('monthly', 'year'));
    }

    public function systemHealth()
    {
        $logs = \App\Models\ActivityLog::with('user')->latest()->limit(50)->get();
        $users = User::count();
        $activeUsers = User::where('is_active', true)->count();
        return view('reports.health', compact('logs', 'users', 'activeUsers'));
    }
}
