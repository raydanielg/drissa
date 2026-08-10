<?php

namespace App\Http\Controllers;

use App\Models\Patient;
use Illuminate\Http\Request;

class PatientController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function index(Request $request)
    {
        $query = Patient::query();

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('first_name', 'like', "%{$search}%")
                  ->orWhere('last_name', 'like', "%{$search}%")
                  ->orWhere('mrn', 'like', "%{$search}%")
                  ->orWhere('phone', 'like', "%{$search}%");
            });
        }

        $patients = $query->latest()->paginate(20);
        return view('patients.index', compact('patients'));
    }

    public function show(Patient $patient)
    {
        $patient->loadCount(['visits', 'appointments', 'documents', 'clinicalRecords']);
        return view('patients.show', compact('patient'));
    }

    public function edit(Patient $patient)
    {
        if (request()->wantsJson()) {
            return response()->json(['patient' => $patient]);
        }
        return view('patients.edit', compact('patient'));
    }

    public function update(Request $request, Patient $patient)
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

        $patient->update($data);

        if ($request->wantsJson()) {
            return response()->json(['success' => true, 'message' => 'Patient updated successfully.', 'patient' => $patient]);
        }

        return redirect()->route('patients.show', $patient)->with('status', 'Patient updated successfully.');
    }

    public function destroy(Patient $patient)
    {
        $patient->delete();
        return redirect()->route('patients.index')->with('status', 'Patient deleted successfully.');
    }

    public function ajaxSearch(Request $request)
    {
        $search = $request->get('q');
        $patients = Patient::where(function($q) use ($search) {
            $q->where('first_name', 'like', "%{$search}%")
              ->orWhere('last_name', 'like', "%{$search}%")
              ->orWhere('mrn', 'like', "%{$search}%")
              ->orWhere('phone', 'like', "%{$search}%");
        })
        ->latest()
        ->limit(10)
        ->get()
        ->map(function($patient) {
            return [
                'id' => $patient->id,
                'text' => $patient->fullName() . " (" . $patient->mrn . ")",
                'phone' => $patient->phone,
                'gender' => $patient->gender,
                'age' => $patient->date_of_birth ? $patient->date_of_birth->age : 'N/A'
            ];
        });

        return response()->json($patients);
    }
}
