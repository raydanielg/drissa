<?php

namespace App\Http\Controllers;

use App\Models\Appointment;
use App\Models\Patient;
use App\Models\Setting;
use App\Models\User;
use App\Services\SmsService;
use Illuminate\Http\Request;

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

        $query = Appointment::with(['patient', 'doctor']);

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
        ]);

        $data['scheduled_at'] = $request->appointment_date . ' ' . $request->start_time . ':00';
        $appointment = Appointment::create($data);
        $appointment->load(['patient', 'doctor']);

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
        $clinicAddress = Setting::get('clinic_address', 'Dar es Salaam, Tanzania');

        $date = $appointment->scheduled_at->format('d/m/Y');
        $time = $appointment->scheduled_at->format('H:i');
        $patientName = $patient->first_name ?? 'Mteja';
        $mrn = $patient->mrn ?? 'Haijulikani';

        $message = "Uzazi Clinic\n"
            . "Karibu {$patientName}!\n"
            . "Miadi yako imewekwa kwa mafanikio.\n"
            . "Tarehe: {$date}\n"
            . "Saa: {$time}\n"
            . "MRN: {$mrn}\n"
            . "Tafadhali fika dakika 15 kabla ya muda wako.\n"
            . "Anwani: {$clinicAddress}\n"
            . "Kwa maswali piga: {$clinicPhone}\n"
            . "Asante kwa kuchagua {$clinicName}.";

        return SmsService::send($patient->phone, $message, auth()->user(), $patient->fullName());
    }
}
