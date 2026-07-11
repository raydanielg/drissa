<?php

namespace App\Http\Controllers;

use App\Models\Appointment;
use App\Models\Patient;
use App\Models\User;
use Illuminate\Http\Request;

class AppointmentController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function index()
    {
        $appointments = Appointment::with(['patient', 'doctor'])
            ->whereDate('scheduled_at', '>=', today())
            ->orderBy('scheduled_at')
            ->paginate(20);

        return view('appointments.index', compact('appointments'));
    }

    public function create()
    {
        $patients = Patient::orderBy('first_name')->get();
        $doctors = User::role('doctor')->get();
        return view('appointments.create', compact('patients', 'doctors'));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'patient_id' => 'required|exists:patients,id',
            'doctor_id' => 'nullable|exists:users,id',
            'scheduled_at' => 'required|date',
            'type' => 'required|in:general,followup,emergency',
            'notes' => 'nullable|string',
        ]);

        $data['status'] = 'scheduled';
        Appointment::create($data);

        return redirect()->route('appointments.index')->with('status', 'Appointment scheduled.');
    }

    public function edit(Appointment $appointment)
    {
        $patients = Patient::orderBy('first_name')->get();
        $doctors = User::role('doctor')->get();
        return view('appointments.edit', compact('appointment', 'patients', 'doctors'));
    }

    public function update(Request $request, Appointment $appointment)
    {
        $data = $request->validate([
            'patient_id' => 'required|exists:patients,id',
            'doctor_id' => 'nullable|exists:users,id',
            'scheduled_at' => 'required|date',
            'status' => 'required|in:scheduled,confirmed,completed,cancelled,no_show',
            'type' => 'required|in:general,followup,emergency',
            'notes' => 'nullable|string',
        ]);

        $appointment->update($data);

        return redirect()->route('appointments.index')->with('status', 'Appointment updated.');
    }

    public function destroy(Appointment $appointment)
    {
        $appointment->delete();
        return back()->with('status', 'Appointment cancelled.');
    }
}
