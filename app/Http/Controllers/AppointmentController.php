<?php

namespace App\Http\Controllers;

use App\Models\Appointment;
use App\Models\Invoice;
use App\Models\Patient;
use App\Models\Payment;
use App\Models\Setting;
use App\Models\User;
use App\Services\SmsService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class AppointmentController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function index(Request $request)
    {
        $filter = $request->get('filter', 'today');
        $date = $request->get('date');

        $query = Appointment::with(['patient', 'doctor', 'invoice']);

        if ($date) {
            $query->whereDate('scheduled_at', $date);
        } else {
            switch ($filter) {
                case 'today':
                    $query->whereDate('scheduled_at', today());
                    break;
                case 'tomorrow':
                    $query->whereDate('scheduled_at', today()->addDay());
                    break;
                case 'week':
                    $query->whereDate('scheduled_at', '>=', today()->startOfWeek())
                        ->whereDate('scheduled_at', '<=', today()->endOfWeek());
                    break;
                case 'upcoming':
                    $query->whereDate('scheduled_at', '>=', today());
                    break;
                case 'past':
                    $query->whereDate('scheduled_at', '<', today());
                    break;
                default:
                    // all
            }
        }

        $appointments = $query->orderBy('scheduled_at')->paginate(50)->withQueryString();

        $stats = [
            'today' => Appointment::whereDate('scheduled_at', today())->count(),
            'tomorrow' => Appointment::whereDate('scheduled_at', today()->addDay())->count(),
            'week' => Appointment::whereDate('scheduled_at', '>=', today()->startOfWeek())
                ->whereDate('scheduled_at', '<=', today()->endOfWeek())->count(),
            'upcoming' => Appointment::whereDate('scheduled_at', '>=', today())->count(),
            'past' => Appointment::whereDate('scheduled_at', '<', today())->count(),
            'total' => Appointment::count(),
        ];

        return view('appointments.index', compact('appointments', 'stats', 'filter', 'date'));
    }

    public function create()
    {
        $patients = Patient::orderBy('first_name')->get();
        $doctors = User::role('doctor')->get();
        if (request()->wantsJson()) {
            return response()->json(['patients' => $patients, 'doctors' => $doctors]);
        }
        return view('appointments.create', compact('patients', 'doctors'));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'patient_id' => 'required|exists:patients,id',
            'doctor_id' => 'nullable|exists:users,id',
            'appointment_date' => 'required|date',
            'start_time' => 'required|date_format:H:i',
            'type' => 'required|in:general,followup,emergency',
            'status' => 'required|in:scheduled,confirmed,completed,cancelled,no_show',
            'notes' => 'nullable|string',
            'collect_payment' => 'nullable|in:1',
            'payment_amount' => 'nullable|numeric|min:0',
            'payment_method' => 'nullable|in:cash,card,mobile_money,insurance',
        ]);

        $data['scheduled_at'] = $request->appointment_date . ' ' . $request->start_time . ':00';
        unset($data['collect_payment'], $data['payment_amount'], $data['payment_method']);

        DB::transaction(function () use ($request, &$data) {
            $appointment = Appointment::create($data);
            $appointment->load(['patient', 'doctor']);

            // Create invoice and record payment if requested
            if ($request->has('collect_payment') && $request->collect_payment == '1') {
                $consultationFee = (float) Setting::get('consultation_fee', 10000);
                $paymentAmount = (float) ($request->payment_amount ?? $consultationFee);
                $paymentMethod = $request->payment_method ?? 'cash';

                $invoice = Invoice::create([
                    'invoice_number' => 'INV-' . now()->format('Y') . '-' . str_pad(Invoice::withTrashed()->max('id') + 1, 6, '0', STR_PAD_LEFT),
                    'visit_id' => null,
                    'patient_id' => $appointment->patient_id,
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

                Payment::create([
                    'invoice_id' => $invoice->id,
                    'received_by' => auth()->id(),
                    'amount' => $paymentAmount,
                    'method' => $paymentMethod,
                ]);

                $totalPaid = $invoice->payments()->sum('amount');
                $invoice->update([
                    'paid' => $totalPaid,
                    'status' => $totalPaid >= $invoice->total ? 'paid' : ($totalPaid > 0 ? 'partial' : 'unpaid'),
                ]);

                $appointment->update(['invoice_id' => $invoice->id]);
            }

            $this->appointment = $appointment;
        });

        $appointment = $this->appointment;
        $appointment->load(['patient', 'doctor', 'invoice']);

        $smsResult = null;
        try {
            $smsResult = $this->sendAppointmentSms($appointment);
        } catch (\Throwable $e) {
            \Illuminate\Support\Facades\Log::error('SMS sending failed during appointment creation', [
                'error' => $e->getMessage(),
                'appointment_id' => $appointment->id,
            ]);
            $smsResult = ['success' => false, 'error' => $e->getMessage()];
        }

        $statusMsg = 'Appointment scheduled.';
        if ($appointment->invoice) {
            $invoice = $appointment->invoice;
            if ($invoice->status === 'paid') {
                $statusMsg .= ' Payment of ' . number_format($invoice->paid) . ' TSh collected.';
            } elseif ($invoice->status === 'partial') {
                $statusMsg .= ' Partial payment of ' . number_format($invoice->paid) . ' TSh collected. Balance: ' . number_format($invoice->total - $invoice->paid) . ' TSh.';
            }
        }
        if ($smsResult) {
            $statusMsg .= $smsResult['success']
                ? ' SMS sent to patient.'
                : ' SMS failed: ' . ($smsResult['error'] ?? 'Unknown error');
        } else {
            $statusMsg .= ' No phone number on patient record.';
        }

        if ($request->wantsJson()) {
            return response()->json(['success' => true, 'message' => $statusMsg, 'appointment' => $appointment, 'sms' => $smsResult]);
        }

        return redirect()->route('appointments.index')->with('status', $statusMsg);
    }

    public function edit(Appointment $appointment)
    {
        $patients = Patient::orderBy('first_name')->get();
        $doctors = User::role('doctor')->get();
        if (request()->wantsJson()) {
            return response()->json(['appointment' => $appointment, 'patients' => $patients, 'doctors' => $doctors]);
        }
        return view('appointments.edit', compact('appointment', 'patients', 'doctors'));
    }

    public function update(Request $request, Appointment $appointment)
    {
        $data = $request->validate([
            'patient_id' => 'required|exists:patients,id',
            'doctor_id' => 'nullable|exists:users,id',
            'appointment_date' => 'required|date',
            'start_time' => 'required|date_format:H:i',
            'status' => 'required|in:scheduled,confirmed,completed,cancelled,no_show',
            'type' => 'required|in:general,followup,emergency',
            'notes' => 'nullable|string',
        ]);

        $data['scheduled_at'] = $request->appointment_date . ' ' . $request->start_time . ':00';
        $appointment->update($data);
        $appointment->load(['patient', 'doctor']);

        if ($request->wantsJson()) {
            return response()->json(['success' => true, 'message' => 'Appointment updated.', 'appointment' => $appointment]);
        }

        return redirect()->route('appointments.index')->with('status', 'Appointment updated.');
    }

    public function destroy(Appointment $appointment)
    {
        $appointment->delete();
        return back()->with('status', 'Appointment cancelled.');
    }

    public function sendSms(Request $request, Appointment $appointment)
    {
        $appointment->load('patient');

        if (! $appointment->patient || ! $appointment->patient->phone) {
            return response()->json(['success' => false, 'error' => 'Patient has no phone number.'], 422);
        }

        try {
            $result = $this->sendAppointmentSms($appointment);
        } catch (\Throwable $e) {
            \Illuminate\Support\Facades\Log::error('SMS sending failed', [
                'error' => $e->getMessage(),
                'appointment_id' => $appointment->id,
            ]);
            return response()->json(['success' => false, 'error' => $e->getMessage()], 500);
        }

        if ($result && $result['success']) {
            return response()->json(['success' => true, 'message' => 'SMS sent to ' . $appointment->patient->fullName() . '.']);
        }

        return response()->json(['success' => false, 'error' => $result['error'] ?? 'Unknown error'], 500);
    }

    public function bulkSms(Request $request)
    {
        $request->validate([
            'date' => 'required|date',
        ]);

        $appointments = Appointment::with('patient')
            ->whereDate('scheduled_at', $request->date)
            ->whereIn('status', ['scheduled', 'confirmed'])
            ->get();

        if ($appointments->isEmpty()) {
            return response()->json(['success' => false, 'error' => 'No appointments found for this date.'], 422);
        }

        $sent = 0;
        $failed = 0;
        $errors = [];

        foreach ($appointments as $appointment) {
            if (! $appointment->patient || ! $appointment->patient->phone) {
                $failed++;
                continue;
            }

            try {
                $result = $this->sendAppointmentSms($appointment);
                if ($result && $result['success']) {
                    $sent++;
                } else {
                    $failed++;
                    $errors[] = $appointment->patient->fullName() . ': ' . ($result['error'] ?? 'Unknown error');
                }
            } catch (\Throwable $e) {
                $failed++;
                $errors[] = $appointment->patient->fullName() . ': ' . $e->getMessage();
            }
        }

        $message = "SMS sent to {$sent} patient(s).";
        if ($failed > 0) {
            $message .= " {$failed} failed.";
            if (count($errors) <= 3) {
                $message .= ' ' . implode(', ', $errors);
            }
        }

        return response()->json([
            'success' => $sent > 0,
            'message' => $message,
            'sent' => $sent,
            'failed' => $failed,
        ]);
    }

    private function sendAppointmentSms(Appointment $appointment): ?array
    {
        $patient = $appointment->patient;
        if (! $patient || ! $patient->phone) {
            return null;
        }

        $clinicName = Setting::get('clinic_name', config('app.name', 'Uzazi Clinic'));
        $clinicPhone = Setting::get('clinic_phone', '+255 700 000 000');

        $date = $appointment->scheduled_at->format('d/m/Y');
        $time = $appointment->scheduled_at->format('H:i');
        $patientName = $patient->first_name ?? 'Mteja';
        $mrn = $patient->mrn ?? 'Haijulikani';

        $message = "Hello {$patientName}, Karibu UZAZI CLINIC\n"
            . "Appointment Yako Ni: {$date} Saa {$time}\n"
            . "ID yako ni {$mrn}\n"
            . "Tafadhali fika On Time.\n"
            . "Kwa maswali Piga {$clinicPhone}";

        return SmsService::send($patient->phone, $message, auth()->user(), $patient->fullName());
    }
}
