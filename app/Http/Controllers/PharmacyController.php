<?php

namespace App\Http\Controllers;

use App\Enums\VisitStatus;
use App\Models\ActivityLog;
use App\Models\Dispense;
use App\Models\Prescription;
use App\Services\VisitWorkflow;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class PharmacyController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function queue()
    {
        $prescriptions = Prescription::with(['visit.patient', 'items.medication'])
            ->where('status', 'pending')
            ->latest()
            ->get();

        return view('pharmacy.queue', compact('prescriptions'));
    }

    public function dispense(Request $request, Prescription $prescription, VisitWorkflow $flow)
    {
        DB::transaction(function () use ($prescription) {
            foreach ($prescription->items as $item) {
                $item->medication->decrement('stock_quantity', $item->quantity);

                Dispense::create([
                    'prescription_id' => $prescription->id,
                    'medication_id' => $item->medication_id,
                    'quantity' => $item->quantity,
                    'dispensed_by' => auth()->id(),
                ]);
            }

            $prescription->update(['status' => 'dispensed']);
        });

        $flow->transition($prescription->visit, VisitStatus::WaitingForPayment);

        ActivityLog::log('drugs_dispensed', $prescription->visit, "Dispensed drugs for visit {$prescription->visit->visit_number}");

        return back()->with('status', 'Drugs dispensed.');
    }
}
