<?php

namespace App\Http\Controllers;

use App\Enums\VisitStatus;
use App\Models\ActivityLog;
use App\Models\Appointment;
use App\Models\Consultation;
use App\Models\Invoice;
use App\Models\LabOrder;
use App\Models\LabTest;
use App\Models\Medication;
use App\Models\Patient;
use App\Models\Prescription;
use App\Models\Visit;
use App\Services\VisitWorkflow;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class DoctorController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function dashboard()
    {
        $doctorId = auth()->id();

        $stats = [
            'today_visits' => Visit::where('doctor_id', $doctorId)->whereDate('registered_at', today())->count(),
            'waiting' => Visit::where('doctor_id', $doctorId)->where('status', VisitStatus::WaitingForDoctor->value)->count(),
            'with_me' => Visit::where('doctor_id', $doctorId)->where('status', VisitStatus::WithDoctor->value)->count(),
            'completed_today' => Visit::where('doctor_id', $doctorId)->where('status', VisitStatus::Completed->value)->whereDate('completed_at', today())->count(),
            'total_patients' => Visit::where('doctor_id', $doctorId)->distinct('patient_id')->count('patient_id'),
            'lab_pending' => Visit::where('doctor_id', $doctorId)->where('status', VisitStatus::WaitingForLab->value)->count(),
            'lab_ready' => Visit::where('doctor_id', $doctorId)->where('status', VisitStatus::LabCompleted->value)->count(),
            'prescriptions_today' => Prescription::where('doctor_id', $doctorId)->whereDate('created_at', today())->count(),
        ];

        $todayAppointments = Appointment::where('doctor_id', $doctorId)
            ->whereDate('scheduled_at', today())
            ->with('patient')
            ->orderBy('scheduled_at')
            ->get();

        $recentVisits = Visit::with('patient')
            ->where('doctor_id', $doctorId)
            ->latest('registered_at')
            ->limit(8)
            ->get();

        $waitingQueue = Visit::with('patient')
            ->where('doctor_id', $doctorId)
            ->whereIn('status', [VisitStatus::WaitingForDoctor->value, VisitStatus::WithDoctor->value])
            ->orderBy('registered_at')
            ->get();

        return view('doctor.dashboard', compact('stats', 'todayAppointments', 'recentVisits', 'waitingQueue'));
    }

    public function schedule()
    {
        $doctorId = auth()->id();

        $upcoming = Appointment::where('doctor_id', $doctorId)
            ->whereDate('scheduled_at', '>=', today())
            ->with('patient')
            ->orderBy('scheduled_at')
            ->paginate(20);

        $todayCount = Appointment::where('doctor_id', $doctorId)->whereDate('scheduled_at', today())->count();
        $weekCount = Appointment::where('doctor_id', $doctorId)
            ->whereBetween('scheduled_at', [now()->startOfWeek(), now()->endOfWeek()])->count();

        return view('doctor.schedule', compact('upcoming', 'todayCount', 'weekCount'));
    }

    public function patients()
    {
        $doctorId = auth()->id();

        $patients = Patient::whereIn('id', function ($q) use ($doctorId) {
            $q->select('patient_id')->from('visits')->where('doctor_id', $doctorId);
        })->withCount(['visits as my_visits' => function ($q) use ($doctorId) {
            $q->where('doctor_id', $doctorId);
        }])->latest()->paginate(20);

        return view('doctor.patients', compact('patients'));
    }

    public function reports()
    {
        $doctorId = auth()->id();

        $totalVisits = Visit::where('doctor_id', $doctorId)->count();
        $completed = Visit::where('doctor_id', $doctorId)->where('status', VisitStatus::Completed->value)->count();
        $cancelled = Visit::where('doctor_id', $doctorId)->where('status', VisitStatus::Cancelled->value)->count();
        $successRate = $totalVisits > 0 ? round(($completed / $totalVisits) * 100) : 0;

        $monthlyStats = collect();
        $monthLabels = [];
        for ($i = 5; $i >= 0; $i--) {
            $date = now()->subMonths($i);
            $count = Visit::where('doctor_id', $doctorId)
                ->whereMonth('registered_at', $date->month)
                ->whereYear('registered_at', $date->year)
                ->count();
            $monthlyStats->push($count);
            $monthLabels[] = $date->format('M Y');
        }

        $topDiagnoses = Consultation::where('doctor_id', $doctorId)
            ->select('diagnosis', DB::raw('count(*) as total'))
            ->whereNotNull('diagnosis')
            ->groupBy('diagnosis')
            ->orderByDesc('total')
            ->limit(10)
            ->get();

        $prescriptionCount = Prescription::where('doctor_id', $doctorId)->count();
        $labOrderCount = LabOrder::where('ordered_by', $doctorId)->count();

        return view('doctor.reports', compact(
            'totalVisits', 'completed', 'cancelled', 'successRate',
            'monthlyStats', 'monthLabels', 'topDiagnoses',
            'prescriptionCount', 'labOrderCount'
        ));
    }

    public function queue()
    {
        $visits = Visit::with([
                'patient',
                'vitals',
                'consultation',
                'labOrders.items.labTest',
                'labOrders.results',
                'prescriptions.items.medication',
            ])
            ->where('doctor_id', auth()->id())
            ->whereIn('status', [
                VisitStatus::WaitingForDoctor->value,
                VisitStatus::WithDoctor->value,
            ])
            ->orderBy('registered_at')
            ->get();

        $labTests = LabTest::where('is_active', true)->get();
        $medications = Medication::where('is_active', true)->get();

        return view('doctor.queue', compact('visits', 'labTests', 'medications'));
    }

    public function labResults()
    {
        $visits = Visit::with(['patient', 'vitals', 'labOrders.items.labTest', 'labOrders.results', 'labOrders.attachments'])
            ->where('doctor_id', auth()->id())
            ->whereIn('status', [
                VisitStatus::WaitingForLab->value,
                VisitStatus::InLab->value,
                VisitStatus::LabCompleted->value,
            ])
            ->orderBy('registered_at')
            ->get();

        $medications = Medication::where('is_active', true)->get();

        return view('doctor.lab-results', compact('visits', 'medications'));
    }

    public function returnFromLab(Visit $visit, VisitWorkflow $flow)
    {
        $flow->transition($visit, VisitStatus::WithDoctor);
        ActivityLog::log('lab_review_started', $visit, "Doctor started reviewing lab results for visit {$visit->visit_number}");
        return redirect()->route('doctor.lab-results')->with('status', 'Patient returned for review. Please write the prescription.');
    }

    public function callNext(Visit $visit, VisitWorkflow $flow)
    {
        $flow->transition($visit, VisitStatus::WithDoctor);
        return back()->with('status', 'Patient called in.');
    }

    public function markNoShow(Visit $visit, VisitWorkflow $flow)
    {
        $flow->transition($visit, VisitStatus::Cancelled, 'Patient did not show up');
        ActivityLog::log('visit_no_show', $visit, "Marked visit {$visit->visit_number} as no-show");
        return back()->with('status', 'Patient marked as no-show.');
    }

    public function saveConsultation(Request $request, Visit $visit)
    {
        $data = $request->validate([
            'history' => 'nullable|string',
            'examination' => 'nullable|string',
            'diagnosis' => 'nullable|string',
            'notes' => 'nullable|string',
        ]);

        $data['doctor_id'] = auth()->id();
        $data['visit_id'] = $visit->id;

        Consultation::updateOrCreate(
            ['visit_id' => $visit->id],
            $data
        );

        ActivityLog::log('consultation_saved', $visit, "Saved consultation for visit {$visit->visit_number}");

        return back()->with('status', 'Consultation saved.');
    }

    public function orderLab(Request $request, Visit $visit, VisitWorkflow $flow)
    {
        $data = $request->validate([
            'test_ids' => 'required|array',
            'test_ids.*' => 'exists:lab_tests,id',
            'notes' => 'nullable|string',
        ]);

        $order = LabOrder::create([
            'visit_id' => $visit->id,
            'ordered_by' => auth()->id(),
            'clinical_notes' => $data['notes'] ?? null,
        ]);

        foreach ($data['test_ids'] as $testId) {
            $order->items()->create(['lab_test_id' => $testId]);
        }

        $flow->transition($visit, VisitStatus::WaitingForLab);

        ActivityLog::log('lab_ordered', $visit, "Ordered lab tests for visit {$visit->visit_number}");

        return back()->with('status', 'Lab tests ordered.');
    }

    public function prescribe(Request $request, Visit $visit, VisitWorkflow $flow)
    {
        $data = $request->validate([
            'items' => 'required|array',
            'items.*.medication_id' => 'required|exists:medications,id',
            'items.*.quantity' => 'required|integer|min:1',
            'items.*.dosage' => 'required|string',
            'items.*.frequency' => 'required|string',
            'items.*.duration' => 'required|string',
            'items.*.instructions' => 'nullable|string',
        ]);

        DB::transaction(function () use ($visit, $data) {
            $prescription = Prescription::create([
                'visit_id' => $visit->id,
                'doctor_id' => auth()->id(),
            ]);

            foreach ($data['items'] as $item) {
                $prescription->items()->create($item);
            }
        });

        $flow->transition($visit, VisitStatus::WaitingForPharmacy);

        ActivityLog::log('prescription_created', $visit, "Created prescription for visit {$visit->visit_number}");

        return back()->with('status', 'Prescription sent to pharmacy.');
    }

    public function sendToPayment(Visit $visit, VisitWorkflow $flow)
    {
        // Generate a simple invoice for consultation only
        $total = 10000; // default consultation fee

        $invoice = Invoice::create([
            'invoice_number' => 'INV-' . now()->format('Y') . '-' . str_pad(Invoice::count() + 1, 6, '0', STR_PAD_LEFT),
            'visit_id' => $visit->id,
            'patient_id' => $visit->patient_id,
            'total' => $total,
        ]);

        $invoice->items()->create([
            'billable_type' => Consultation::class,
            'billable_id' => $visit->consultation?->id ?? 0,
            'description' => 'Consultation fee',
            'quantity' => 1,
            'unit_price' => $total,
            'line_total' => $total,
        ]);

        $flow->transition($visit, VisitStatus::WaitingForPayment);

        ActivityLog::log('sent_to_payment', $visit, "Sent visit {$visit->visit_number} to payment");

        return back()->with('status', 'Visit sent to reception for payment.');
    }
}
