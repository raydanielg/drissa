<?php

namespace App\Http\Controllers;

use App\Enums\VisitStatus;
use App\Exceptions\InvalidTransitionException;
use App\Models\ActivityLog;
use App\Models\Appointment;
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
        $today = today();

        $todayVisits = Visit::with(['patient', 'doctor'])
            ->whereDate('registered_at', $today)
            ->latest('registered_at')
            ->get();

        $waitingForPayment = Visit::with(['patient', 'invoice'])
            ->where('status', VisitStatus::WaitingForPayment->value)
            ->latest('registered_at')
            ->get();

        $waitingForDoctor = Visit::with(['patient', 'doctor'])
            ->where('status', VisitStatus::WaitingForDoctor->value)
            ->latest('registered_at')
            ->get();

        $registeredVisits = Visit::with(['patient', 'invoice'])
            ->where('status', VisitStatus::Registered->value)
            ->latest('registered_at')
            ->get();

        $withDoctorVisits = Visit::with(['patient', 'doctor'])
            ->where('status', VisitStatus::WithDoctor->value)
            ->latest('registered_at')
            ->get();

        $todayAppointments = Appointment::with(['patient', 'doctor'])
            ->whereDate('scheduled_at', $today)
            ->orderBy('scheduled_at')
            ->get();

        $recentPatients = Patient::withCount('visits')
            ->latest()
            ->limit(8)
            ->get();

        $doctors = User::role('doctor')->get();
        $patientsList = Patient::orderBy('first_name')->get();
        $patientSearchData = $patientsList->map(fn ($patient) => [
            'id' => $patient->id,
            'name' => $patient->fullName(),
            'mrn' => $patient->mrn,
            'phone' => $patient->phone,
            'url' => route('patients.show', $patient),
        ])->values();

        $kpis = [
            'today_visits' => Visit::whereDate('registered_at', $today)->count(),
            'waiting_payment' => Visit::where('status', VisitStatus::WaitingForPayment->value)->count(),
            'today_revenue' => (float) Payment::whereDate('created_at', $today)->sum('amount'),
            'today_patients' => Patient::whereDate('created_at', $today)->count(),
            'waiting_doctor' => Visit::where('status', VisitStatus::WaitingForDoctor->value)->count(),
            'with_doctor' => Visit::where('status', VisitStatus::WithDoctor->value)->count(),
            'registered' => Visit::where('status', VisitStatus::Registered->value)->count(),
            'appointments_today' => $todayAppointments->count(),
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
            'waiting_for_doctor' => Visit::where('status', VisitStatus::WaitingForDoctor->value)->count(),
            'with_doctor' => Visit::where('status', VisitStatus::WithDoctor->value)->count(),
            'waiting_for_lab' => Visit::where('status', VisitStatus::WaitingForLab->value)->count(),
            'waiting_for_pharmacy' => Visit::where('status', VisitStatus::WaitingForPharmacy->value)->count(),
            'waiting_for_payment' => Visit::where('status', VisitStatus::WaitingForPayment->value)->count(),
            'completed' => Visit::where('status', VisitStatus::Completed->value)->count(),
            'cancelled' => Visit::where('status', VisitStatus::Cancelled->value)->count(),
        ];

        return view('reception.dashboard', compact(
            'todayVisits', 'waitingForPayment', 'waitingForDoctor', 'registeredVisits',
            'withDoctorVisits', 'todayAppointments', 'recentPatients', 'doctors',
            'patientsList', 'patientSearchData', 'kpis', 'visitTrend', 'visitLabels', 'statusCounts'
        ));
    }

    public function queue()
    {
        $today = today();

        $allQueues = Visit::with(['patient', 'doctor'])
            ->whereDate('registered_at', $today)
            ->whereIn('status', [
                VisitStatus::Registered->value,
                VisitStatus::WaitingForDoctor->value,
                VisitStatus::WithDoctor->value,
            ])
            ->latest('registered_at')
            ->get();

        $registeredQueue = Visit::with(['patient'])
            ->where('status', VisitStatus::Registered->value)
            ->latest('registered_at')
            ->get();

        $waitingForDoctorQueue = Visit::with(['patient', 'doctor'])
            ->where('status', VisitStatus::WaitingForDoctor->value)
            ->latest('registered_at')
            ->get();

        $withDoctorQueue = Visit::with(['patient', 'doctor'])
            ->where('status', VisitStatus::WithDoctor->value)
            ->latest('registered_at')
            ->get();

        $doctors = User::role('doctor')->get();
        $patientsList = Patient::orderBy('first_name')->get();
        $patientSearchData = $patientsList->map(fn ($patient) => [
            'id' => $patient->id,
            'name' => $patient->fullName(),
            'mrn' => $patient->mrn,
            'phone' => $patient->phone,
            'url' => route('patients.show', $patient),
        ])->values();

        return view('reception.queue', compact(
            'allQueues', 'registeredQueue', 'waitingForDoctorQueue', 'withDoctorQueue',
            'doctors', 'patientsList', 'patientSearchData'
        ));
    }

    public function callNotifications(Request $request)
    {
        $since = $request->get('since', now()->subMinutes(5)->toDateTimeString());

        $visits = Visit::with(['patient', 'doctor'])
            ->where('status', VisitStatus::WithDoctor->value)
            ->where('updated_at', '>=', $since)
            ->whereDate('registered_at', today())
            ->get()
            ->map(fn ($v) => [
                'id' => $v->id,
                'visit_number' => $v->visit_number,
                'patient_name' => $v->patient?->fullName(),
                'doctor_name' => $v->doctor?->name ?? 'Unassigned',
                'time' => $v->updated_at->format('H:i'),
            ]);

        return response()->json([
            'notifications' => $visits,
            'server_time' => now()->toDateTimeString(),
        ]);
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
            'waiting_doctor' => Visit::where('status', VisitStatus::WaitingForDoctor->value)->count(),
            'with_doctor' => Visit::where('status', VisitStatus::WithDoctor->value)->count(),
            'registered' => Visit::where('status', VisitStatus::Registered->value)->count(),
            'appointments_today' => Appointment::whereDate('scheduled_at', today())->count(),
            'avg_wait_minutes' => 12,
            'visit_trend' => $visitTrend,
            'visit_labels' => $visitLabels,
            'status_counts' => [
                'registered' => Visit::where('status', VisitStatus::Registered->value)->count(),
                'waiting_for_doctor' => Visit::where('status', VisitStatus::WaitingForDoctor->value)->count(),
                'with_doctor' => Visit::where('status', VisitStatus::WithDoctor->value)->count(),
                'waiting_for_lab' => Visit::where('status', VisitStatus::WaitingForLab->value)->count(),
                'waiting_for_pharmacy' => Visit::where('status', VisitStatus::WaitingForPharmacy->value)->count(),
                'waiting_for_payment' => Visit::where('status', VisitStatus::WaitingForPayment->value)->count(),
                'completed' => Visit::where('status', VisitStatus::Completed->value)->count(),
                'cancelled' => Visit::where('status', VisitStatus::Cancelled->value)->count(),
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

        $latestId = Patient::withTrashed()->max('id') ?? 0;
        $data['mrn'] = 'MRN-' . now()->format('Y') . '-' . str_pad($latestId + 1, 5, '0', STR_PAD_LEFT);

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
            'doctor_id' => 'nullable|exists:users,id',
            'chief_complaint' => 'nullable|string',
            'type' => 'required|in:outpatient,emergency,followup',
        ]);

        $visit = Visit::create([
            'visit_number' => 'VIS-' . now()->format('Y') . '-' . str_pad(Visit::count() + 1, 6, '0', STR_PAD_LEFT),
            'patient_id' => $data['patient_id'],
            'doctor_id' => $data['doctor_id'] ?? null,
            'received_by' => auth()->id(),
            'status' => VisitStatus::Registered->value,
            'chief_complaint' => $data['chief_complaint'] ?? null,
            'type' => $data['type'],
            'registered_at' => now(),
        ]);

        // Auto-create invoice with consultation fee
        $consultationFee = (float) Setting::get('consultation_fee', 10000);
        $invoice = Invoice::create([
            'invoice_number' => 'INV-' . now()->format('Y') . '-' . str_pad(Invoice::count() + 1, 6, '0', STR_PAD_LEFT),
            'visit_id' => $visit->id,
            'patient_id' => $visit->patient_id,
            'total' => $consultationFee,
            'paid' => 0,
            'status' => 'unpaid',
        ]);

        $invoice->items()->create([
            'description' => 'Consultation Fee',
            'quantity' => 1,
            'unit_price' => $consultationFee,
            'line_total' => $consultationFee,
        ]);

        ActivityLog::log('visit_created', $visit, "Created visit {$visit->visit_number} with consultation fee TSh {$consultationFee}");

        return redirect()->route('reception.dashboard')
            ->with('status', "Visit {$visit->visit_number} created. Collect consultation fee before sending to doctor.");
    }

    public function assignDoctor(Request $request, Visit $visit, VisitWorkflow $flow)
    {
        $data = $request->validate([
            'doctor_id' => 'required|exists:users,id',
        ]);

        // Check if payment has been made
        $invoice = $visit->invoice;
        if ($invoice && $invoice->status !== 'paid') {
            if ($request->wantsJson()) {
                return response()->json(['success' => false, 'message' => 'Payment must be collected before assigning a doctor.'], 422);
            }
            return back()->with('error', 'Payment must be collected before assigning a doctor.');
        }

        $visit->update(['doctor_id' => $data['doctor_id']]);
        $flow->transition($visit, VisitStatus::WaitingForDoctor);

        ActivityLog::log('doctor_assigned', $visit, "Assigned doctor to visit {$visit->visit_number}");

        if ($request->wantsJson()) {
            return response()->json(['success' => true, 'message' => 'Patient sent to doctor queue.']);
        }

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
                $total = (float) Setting::get('consultation_fee', 10000);
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

            // New flow: Registered → WaitingForDoctor after payment (only if doctor assigned)
            if ($invoice->status === 'paid' && $visit->status === VisitStatus::Registered->value) {
                if ($visit->doctor_id) {
                    $flow->transition($visit, VisitStatus::WaitingForDoctor);
                }
                // If no doctor assigned, stay Registered so receptionist can assign one
            }
            // Legacy flow: WaitingForPayment → Completed
            elseif ($invoice->status === 'paid' && $visit->status === VisitStatus::WaitingForPayment->value) {
                $flow->transition($visit, VisitStatus::Completed);
            }
        });

        ActivityLog::log('payment_recorded', $visit, "Recorded payment of TSh {$data['amount']} for visit {$visit->visit_number}");

        $message = 'Payment recorded. Patient can now see the doctor.';
        if ($request->wantsJson()) {
            $invoice->refresh();
            return response()->json([
                'success' => true,
                'message' => $message,
                'payment' => [
                    'amount' => (float) $data['amount'],
                    'method' => $data['method'],
                    'invoice_status' => $invoice->status,
                    'invoice_paid' => (float) $invoice->paid,
                    'invoice_total' => (float) $invoice->total,
                    'visit_status' => $visit->fresh()->status,
                ],
            ]);
        }

        return back()->with('status', $message);
    }

    public function closeVisit(Request $request, Visit $visit, VisitWorkflow $flow)
    {
        try {
            $flow->transition($visit, VisitStatus::Completed);
            ActivityLog::log('visit_closed', $visit, "Closed visit {$visit->visit_number}");

            if ($request->wantsJson()) {
                return response()->json(['success' => true, 'message' => 'Visit closed successfully.']);
            }

            return back()->with('status', 'Visit closed successfully.');
        } catch (InvalidTransitionException $e) {
            if ($request->wantsJson()) {
                return response()->json(['success' => false, 'message' => $e->getMessage()], 422);
            }

            return back()->with('status', $e->getMessage());
        }
    }

    public function cancelVisit(Request $request, Visit $visit, VisitWorkflow $flow)
    {
        try {
            $flow->transition($visit, VisitStatus::Cancelled);
            ActivityLog::log('visit_cancelled', $visit, "Cancelled visit {$visit->visit_number} from reception queue");

            if ($request->wantsJson()) {
                return response()->json(['success' => true, 'message' => 'Visit cancelled successfully.']);
            }

            return back()->with('status', 'Visit cancelled successfully.');
        } catch (InvalidTransitionException $e) {
            if ($request->wantsJson()) {
                return response()->json(['success' => false, 'message' => $e->getMessage()], 422);
            }

            return back()->with('status', $e->getMessage());
        }
    }

    public function markInvoicePaid(Request $request, Invoice $invoice, VisitWorkflow $flow)
    {
        DB::transaction(function () use ($invoice, $flow) {
            $invoice->update([
                'paid' => $invoice->total,
                'status' => 'paid',
            ]);

            $visit = $invoice->visit;
            if ($visit) {
                // New flow: Registered → WaitingForDoctor (only if doctor assigned)
                if ($visit->status === VisitStatus::Registered->value) {
                    if ($visit->doctor_id) {
                        $flow->transition($visit, VisitStatus::WaitingForDoctor);
                    }
                }
                // Legacy flow: WaitingForPayment → Completed
                elseif ($visit->status === VisitStatus::WaitingForPayment->value) {
                    $flow->transition($visit, VisitStatus::Completed);
                }
            }
        });

        ActivityLog::log('invoice_marked_paid', $invoice, "Invoice {$invoice->invoice_number} marked as paid");

        if ($request->wantsJson()) {
            return response()->json(['success' => true, 'message' => 'Invoice marked as paid and visit closed.']);
        }

        return back()->with('status', 'Invoice marked as paid and visit closed.');
    }

    public function markInvoiceUnpaid(Request $request, Invoice $invoice)
    {
        $invoice->update([
            'paid' => 0,
            'status' => 'unpaid',
        ]);

        ActivityLog::log('invoice_marked_unpaid', $invoice, "Invoice {$invoice->invoice_number} marked as unpaid");

        if ($request->wantsJson()) {
            return response()->json(['success' => true, 'message' => 'Invoice marked as unpaid.']);
        }

        return back()->with('status', 'Invoice marked as unpaid.');
    }

    public function payments()
    {
        $today = today();

        $pendingInvoices = Invoice::with(['visit.patient', 'patient', 'payments'])
            ->whereIn('status', ['unpaid', 'partial'])
            ->latest()
            ->get();

        $recentPayments = Payment::with(['invoice.visit.patient', 'invoice.patient', 'receivedBy'])
            ->latest()
            ->limit(50)
            ->get();

        $allInvoices = Invoice::with(['visit.patient', 'patient', 'payments'])
            ->latest()
            ->limit(100)
            ->get();

        $todayPayments = (float) Payment::whereDate('created_at', $today)->sum('amount');
        $todayInvoicesPaid = Invoice::whereDate('updated_at', $today)->where('status', 'paid')->count();
        $pendingCount = Invoice::whereIn('status', ['unpaid', 'partial'])->count();
        $pendingAmount = (float) Invoice::whereIn('status', ['unpaid', 'partial'])
            ->sum(DB::raw('total - paid'));

        $todayTotal = (float) Invoice::whereDate('created_at', $today)->sum('total');
        $todayPaidAmount = (float) Invoice::whereDate('updated_at', $today)->where('status', 'paid')->sum('paid');
        $todayUnpaidAmount = (float) Invoice::whereDate('created_at', $today)->whereIn('status', ['unpaid', 'partial'])
            ->sum(DB::raw('total - paid'));
        $collectionRate = $todayTotal > 0 ? round(($todayPaidAmount / $todayTotal) * 100) : 0;
        $paidVsPending = [
            'paid' => $todayPaidAmount,
            'pending' => $todayUnpaidAmount,
        ];

        $paymentMethods = [
            'cash' => 'Cash',
            'card' => 'Card',
            'mobile_money' => 'Mobile Money',
            'insurance' => 'Insurance',
        ];

        return view('reception.payments', compact(
            'pendingInvoices', 'recentPayments', 'allInvoices', 'todayPayments',
            'todayInvoicesPaid', 'pendingCount', 'pendingAmount', 'todayTotal',
            'todayPaidAmount', 'todayUnpaidAmount', 'collectionRate', 'paidVsPending', 'paymentMethods'
        ));
    }
}
