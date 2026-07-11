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
}
