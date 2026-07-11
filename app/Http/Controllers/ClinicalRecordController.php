<?php

namespace App\Http\Controllers;

use App\Models\Appointment;
use App\Models\ClinicalRecord;
use App\Models\Patient;
use App\Models\User;
use App\Models\Visit;
use Illuminate\Http\Request;

class ClinicalRecordController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function index()
    {
        $records = ClinicalRecord::with(['patient', 'doctor', 'visit'])
            ->latest('record_date')
            ->paginate(20);
        return view('clinical_records.index', compact('records'));
    }

    public function create(Request $request)
    {
        $patients = Patient::orderBy('first_name')->get();
        $doctors = User::role('doctor')->get();
        $visits = Visit::with('patient')->latest()->limit(50)->get();
        $appointments = Appointment::with('patient')->latest()->limit(50)->get();

        $preselected = [];
        if ($request->filled('patient_id')) $preselected['patient_id'] = $request->patient_id;
        if ($request->filled('visit_id')) $preselected['visit_id'] = $request->visit_id;
        if ($request->filled('appointment_id')) $preselected['appointment_id'] = $request->appointment_id;

        return view('clinical_records.create', compact('patients', 'doctors', 'visits', 'appointments', 'preselected'));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'patient_id' => 'required|exists:patients,id',
            'visit_id' => 'nullable|exists:visits,id',
            'appointment_id' => 'nullable|exists:appointments,id',
            'doctor_id' => 'required|exists:users,id',
            'chief_complaint' => 'nullable|string',
            'symptoms' => 'nullable|string',
            'diagnosis' => 'nullable|string',
            'treatment_plan' => 'nullable|string',
            'notes' => 'nullable|string',
            'prescription' => 'nullable|string',
            'record_date' => 'required|date',
        ]);

        $record = ClinicalRecord::create($data);

        if ($request->wantsJson()) {
            return response()->json(['success' => true, 'message' => 'Clinical record saved.', 'record' => $record]);
        }

        return redirect()->route('clinical-records.index')->with('status', 'Clinical record saved.');
    }

    public function show(ClinicalRecord $clinicalRecord)
    {
        $clinicalRecord->load(['patient', 'doctor', 'visit', 'appointment']);
        return view('clinical_records.show', compact('clinicalRecord'));
    }

    public function edit(ClinicalRecord $clinicalRecord)
    {
        $patients = Patient::orderBy('first_name')->get();
        $doctors = User::role('doctor')->get();
        $visits = Visit::with('patient')->latest()->limit(50)->get();
        $appointments = Appointment::with('patient')->latest()->limit(50)->get();
        return view('clinical_records.edit', compact('clinicalRecord', 'patients', 'doctors', 'visits', 'appointments'));
    }

    public function update(Request $request, ClinicalRecord $clinicalRecord)
    {
        $data = $request->validate([
            'patient_id' => 'required|exists:patients,id',
            'visit_id' => 'nullable|exists:visits,id',
            'appointment_id' => 'nullable|exists:appointments,id',
            'doctor_id' => 'required|exists:users,id',
            'chief_complaint' => 'nullable|string',
            'symptoms' => 'nullable|string',
            'diagnosis' => 'nullable|string',
            'treatment_plan' => 'nullable|string',
            'notes' => 'nullable|string',
            'prescription' => 'nullable|string',
            'record_date' => 'required|date',
        ]);

        $clinicalRecord->update($data);

        return redirect()->route('clinical-records.index')->with('status', 'Clinical record updated.');
    }

    public function destroy(ClinicalRecord $clinicalRecord)
    {
        $clinicalRecord->delete();
        return redirect()->route('clinical-records.index')->with('status', 'Clinical record deleted.');
    }

    public function createFromVisit(Visit $visit)
    {
        return redirect()->route('clinical-records.create', [
            'patient_id' => $visit->patient_id,
            'visit_id' => $visit->id,
        ]);
    }

    public function createFromAppointment(Appointment $appointment)
    {
        return redirect()->route('clinical-records.create', [
            'patient_id' => $appointment->patient_id,
            'appointment_id' => $appointment->id,
        ]);
    }
}
