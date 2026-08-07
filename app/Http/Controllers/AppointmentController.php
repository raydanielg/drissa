<?php

namespace App\Http\Controllers;

use App\Models\Appointment;
use App\Models\Patient;
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

        $this->sendAppointmentSms($appointment);

        if ($request->wantsJson()) {
            return response()->json(['success' => true, 'message' => 'Appointment scheduled.', 'appointment' => $appointment]);
        }

        return redirect()->route('appointments.index')->with('status', 'Appointment scheduled.');
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

    private function sendAppointmentSms(Appointment $appointment): void
    {
        $patient = $appointment->patient;
        if (! $patient || ! $patient->phone) {
            return;
        }

        $clinicName = setting('clinic_name', config('app.name', 'Uzazi Clinic'));
        $clinicPhone = setting('clinic_phone', '+255 700 000 000');
        $clinicAddress = setting('clinic_address', 'Dar es Salaam, Tanzania');

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
            . "Lugha: Swahili/English\n"
            . "Tafadhali fika dakika 15 kabla ya muda wako.\n"
            . "Anwani: {$clinicAddress}\n"
            . "Kwa maswali piga: {$clinicPhone}\n"
            . "Asante kwa kuchagua {$clinicName}.";

        SmsService::send($patient->phone, $message, auth()->user(), $patient->fullName());
    }
}
