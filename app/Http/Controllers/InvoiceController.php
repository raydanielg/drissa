<?php

namespace App\Http\Controllers;

use App\Models\Invoice;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;

class InvoiceController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function index()
    {
        $invoices = Invoice::with(['patient', 'visit'])->latest()->paginate(20);
        
        $stats = [
            'total' => Invoice::count(),
            'paid' => Invoice::where('status', 'paid')->sum('total'),
            'pending' => Invoice::where('status', '!=', 'paid')->sum('total') - Invoice::where('status', '!=', 'paid')->sum('paid'),
            'overdue' => Invoice::where('status', 'unpaid')->where('created_at', '<', now()->subDays(30))->count(),
        ];
        
        if (request()->wantsJson()) {
            return response()->json(['stats' => $stats]);
        }
        
        return view('invoices.index', compact('invoices', 'stats'));
    }

    public function show(Invoice $invoice)
    {
        $invoice->load(['patient', 'visit', 'items', 'payments']);
        return view('invoices.show', compact('invoice'));
    }

    public function downloadPdf(Invoice $invoice)
    {
        $invoice->load(['patient', 'visit', 'items']);
        $pdf = Pdf::loadView('invoices.pdf', compact('invoice'));
        return $pdf->download("invoice-{$invoice->invoice_number}.pdf");
    }

    public function downloadAllPdf()
    {
        $invoices = Invoice::with(['patient', 'visit', 'items'])->latest()->get();
        
        $pdf = Pdf::loadView('invoices.all-pdf', compact('invoices'));
        return $pdf->download("all-invoices-" . now()->format('Y-m-d') . ".pdf");
    }
}
