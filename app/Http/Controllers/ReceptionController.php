<?php

namespace App\Http\Controllers;

use App\Enums\VisitStatus;
use App\Models\ActivityLog;
use App\Models\Invoice;
use App\Models\Patient;
use App\Models\Payment;
use App\Models\User;
use App\Models\Visit;
use App\Services\VisitWorkflow;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ReceptionController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function dashboard()
    {
        $todayVisits = Visit::with('patient')
            ->whereDate('registered_at', today())
            ->latest()
            ->get();

        $waitingForPayment = Visit::with('patient')
            ->where('status', VisitStatus::WaitingForPayment->value)
            ->get();

        $doctors = User::role('doctor')->get();

        return view('reception.dashboard', compact('todayVisits', 'waitingForPayment', 'doctors'));
    }

    public function storePatient(Request $request)
    {
        $data = $request->validate([
            'first_name' => 'required|string|max:255',
            'last_name' => 'required|string|max:255',
            'date_of_birth' => 'required|date',
            'gender' => 'required|in:male,female,other',
            'phone' => 'nullable|string|max:50',
            'national_id' => 'nullable|string|max:50',
            'address' => 'nullable|string',
            'allergies' => 'nullable|string',
            'blood_group' => 'nullable|string|max:10',
        ]);

        $data['mrn'] = 'MRN-' . now()->format('Y') . '-' . str_pad(Patient::count() + 1, 5, '0', STR_PAD_LEFT);

        $patient = Patient::create($data);

        ActivityLog::log('patient_registered', $patient, "Registered patient {$patient->fullName()}");

        return redirect()->route('reception.dashboard')
            ->with('status', "Patient {$patient->fullName()} registered.");
    }

    public function storeVisit(Request $request)
    {
        $data = $request->validate([
            'patient_id' => 'required|exists:patients,id',
            'chief_complaint' => 'nullable|string',
            'type' => 'required|in:outpatient,emergency,followup',
        ]);

        $visit = Visit::create([
            'visit_number' => 'VIS-' . now()->format('Y') . '-' . str_pad(Visit::count() + 1, 6, '0', STR_PAD_LEFT),
            'patient_id' => $data['patient_id'],
            'received_by' => auth()->id(),
            'status' => VisitStatus::Registered->value,
            'chief_complaint' => $data['chief_complaint'] ?? null,
            'type' => $data['type'],
            'registered_at' => now(),
        ]);

        ActivityLog::log('visit_created', $visit, "Created visit {$visit->visit_number}");

        return redirect()->route('reception.dashboard')
            ->with('status', "Visit {$visit->visit_number} created.");
    }

    public function assignDoctor(Request $request, Visit $visit, VisitWorkflow $flow)
    {
        $data = $request->validate([
            'doctor_id' => 'required|exists:users,id',
        ]);

        $visit->update(['doctor_id' => $data['doctor_id']]);
        $flow->transition($visit, VisitStatus::WaitingForDoctor);

        ActivityLog::log('doctor_assigned', $visit, "Assigned doctor to visit {$visit->visit_number}");

        return back()->with('status', 'Patient sent to doctor queue.');
    }

    public function storePayment(Request $request, Visit $visit, VisitWorkflow $flow)
    {
        $data = $request->validate([
            'amount' => 'required|numeric|min:0',
            'method' => 'required|in:cash,card,mobile_money,insurance',
            'reference' => 'nullable|string|max:255',
        ]);

        DB::transaction(function () use ($visit, $data, $flow) {
            Payment::create([
                'invoice_id' => $visit->invoice->id,
                'received_by' => auth()->id(),
                'amount' => $data['amount'],
                'method' => $data['method'],
                'reference' => $data['reference'] ?? null,
            ]);

            $invoice = $visit->invoice;
            $totalPaid = $invoice->payments()->sum('amount');
            $invoice->update([
                'paid' => $totalPaid,
                'status' => $totalPaid >= $invoice->total ? 'paid' : ($totalPaid > 0 ? 'partial' : 'unpaid'),
            ]);

            if ($invoice->status === 'paid') {
                $flow->transition($visit, VisitStatus::Completed);
            }
        });

        ActivityLog::log('payment_recorded', $visit, "Recorded payment of TSh {$data['amount']} for visit {$visit->visit_number}");

        return back()->with('status', 'Payment recorded.');
    }
}
