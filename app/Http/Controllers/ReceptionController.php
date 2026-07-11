<?php

namespace App\Http\Controllers;

use App\Enums\VisitStatus;
use App\Models\ActivityLog;
use App\Models\Invoice;
use App\Models\Patient;
use App\Models\Payment;
use App\Models\Setting;
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

        $kpis = [
            'today_visits' => Visit::whereDate('registered_at', today())->count(),
            'waiting_payment' => Visit::where('status', VisitStatus::WaitingForPayment->value)->count(),
            'today_revenue' => (float) Payment::whereDate('created_at', today())->sum('amount'),
            'today_patients' => Patient::whereDate('created_at', today())->count(),
            'avg_wait_minutes' => 12,
        ];

        $visitTrend = collect();
        $visitLabels = [];
        for ($i = 6; $i >= 0; $i--) {
            $date = now()->subDays($i);
            $visitLabels[] = $date->format('D');
            $visitTrend->push(Visit::whereDate('registered_at', $date)->count());
        }

        $statusCounts = [
            'registered' => Visit::where('status', VisitStatus::Registered->value)->count(),
            'with_doctor' => Visit::where('status', VisitStatus::WithDoctor->value)->count(),
            'waiting_for_lab' => Visit::where('status', VisitStatus::WaitingForLab->value)->count(),
            'waiting_for_payment' => Visit::where('status', VisitStatus::WaitingForPayment->value)->count(),
            'completed' => Visit::where('status', VisitStatus::Completed->value)->count(),
        ];

        return view('reception.dashboard', compact('todayVisits', 'waitingForPayment', 'doctors', 'kpis', 'visitTrend', 'visitLabels', 'statusCounts'));
    }

    public function stats()
    {
        $visitTrend = collect();
        $visitLabels = [];
        for ($i = 6; $i >= 0; $i--) {
            $date = now()->subDays($i);
            $visitLabels[] = $date->format('D');
            $visitTrend->push(Visit::whereDate('registered_at', $date)->count());
        }

        return response()->json([
            'today_visits' => Visit::whereDate('registered_at', today())->count(),
            'waiting_payment' => Visit::where('status', VisitStatus::WaitingForPayment->value)->count(),
            'today_revenue' => (float) Payment::whereDate('created_at', today())->sum('amount'),
            'today_patients' => Patient::whereDate('created_at', today())->count(),
            'visit_trend' => $visitTrend,
            'visit_labels' => $visitLabels,
            'status_counts' => [
                'registered' => Visit::where('status', VisitStatus::Registered->value)->count(),
                'with_doctor' => Visit::where('status', VisitStatus::WithDoctor->value)->count(),
                'waiting_for_lab' => Visit::where('status', VisitStatus::WaitingForLab->value)->count(),
                'waiting_for_payment' => Visit::where('status', VisitStatus::WaitingForPayment->value)->count(),
                'completed' => Visit::where('status', VisitStatus::Completed->value)->count(),
            ],
        ]);
    }

    public function storePatient(Request $request)
    {
        $rules = [
            'first_name' => 'nullable|string|max:255',
            'last_name' => 'nullable|string|max:255',
            'name' => 'nullable|string|max:255',
            'date_of_birth' => 'required|date',
            'gender' => 'required|in:male,female,other',
            'phone' => 'nullable|string|max:50',
            'national_id' => 'nullable|string|max:50',
            'address' => 'nullable|string',
            'allergies' => 'nullable|string',
            'blood_group' => 'nullable|string|max:10',
        ];

        $data = $request->validate($rules);

        if ($request->filled('name')) {
            $parts = explode(' ', trim($request->name), 2);
            $data['first_name'] = $parts[0];
            $data['last_name'] = $parts[1] ?? '';
        }

        if (empty($data['first_name']) || empty($data['last_name'])) {
            return $request->wantsJson()
                ? response()->json(['success' => false, 'message' => 'First and last name are required.'], 422)
                : back()->withErrors(['name' => 'First and last name are required.'])->withInput();
        }

        $data['mrn'] = 'MRN-' . now()->format('Y') . '-' . str_pad(Patient::count() + 1, 5, '0', STR_PAD_LEFT);

        $patient = Patient::create($data);

        ActivityLog::log('patient_registered', $patient, "Registered patient {$patient->fullName()}");

        if ($request->wantsJson()) {
            return response()->json(['success' => true, 'message' => "Patient {$patient->fullName()} registered.", 'patient' => $patient]);
        }

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

    public function changeDoctor(Request $request, Visit $visit)
    {
        $data = $request->validate([
            'doctor_id' => 'required|exists:users,id',
        ]);

        $visit->update(['doctor_id' => $data['doctor_id']]);

        ActivityLog::log('doctor_changed', $visit, "Changed doctor for visit {$visit->visit_number}");

        if ($request->wantsJson()) {
            return response()->json(['success' => true, 'message' => 'Doctor changed successfully.']);
        }

        return back()->with('status', 'Doctor changed successfully.');
    }

    public function storePayment(Request $request, Visit $visit, VisitWorkflow $flow)
    {
        $data = $request->validate([
            'amount' => 'required|numeric|min:0',
            'method' => 'required|in:cash,card,mobile_money,insurance',
            'reference' => 'nullable|string|max:255',
        ]);

        DB::transaction(function () use ($visit, $data, $flow) {
            $invoice = $visit->invoice;

            if (! $invoice) {
                $total = Setting::get('consultation_fee', 10000);
                $invoice = Invoice::create([
                    'invoice_number' => 'INV-' . now()->format('Y') . '-' . str_pad(Invoice::count() + 1, 6, '0', STR_PAD_LEFT),
                    'visit_id' => $visit->id,
                    'patient_id' => $visit->patient_id,
                    'total' => $total,
                    'paid' => 0,
                    'status' => 'unpaid',
                ]);
            }

            Payment::create([
                'invoice_id' => $invoice->id,
                'received_by' => auth()->id(),
                'amount' => $data['amount'],
                'method' => $data['method'],
                'reference' => $data['reference'] ?? null,
            ]);
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
