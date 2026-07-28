<?php

namespace App\Http\Controllers;

use App\Enums\VisitStatus;
use App\Models\ActivityLog;
use App\Models\Patient;
use App\Models\Visit;
use App\Models\Vital;
use Illuminate\Http\Request;

class NurseController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function dashboard()
    {
        $todayVisits = Visit::with(['patient', 'vitals'])
            ->whereDate('registered_at', today())
            ->count();

        $waitingVitals = Visit::with(['patient'])
            ->where('status', VisitStatus::Registered->value)
            ->orderBy('registered_at')
            ->get();

        $vitalsRecordedToday = Vital::whereDate('created_at', today())->count();
        $totalPatients = Patient::count();

        $recentVitals = Vital::with('visit.patient')
            ->latest()
            ->limit(10)
            ->get();

        return view('nurse.dashboard', compact(
            'todayVisits', 'waitingVitals', 'vitalsRecordedToday',
            'totalPatients', 'recentVitals'
        ));
    }

    public function queue()
    {
        $visits = Visit::with(['patient', 'vitals'])
            ->where('status', VisitStatus::Registered->value)
            ->orderBy('registered_at')
            ->get();

        return view('nurse.queue', compact('visits'));
    }

    public function saveVitals(Request $request, Visit $visit)
    {
        $data = $request->validate([
            'temperature' => 'nullable|numeric',
            'blood_pressure' => 'nullable|string|max:20',
            'pulse' => 'nullable|integer',
            'weight' => 'nullable|numeric',
            'height' => 'nullable|numeric',
            'respiratory_rate' => 'nullable|integer',
            'oxygen_saturation' => 'nullable|numeric',
            'notes' => 'nullable|string',
        ]);

        Vital::updateOrCreate(
            ['visit_id' => $visit->id],
            $data
        );

        ActivityLog::log('vitals_recorded', $visit, "Vitals recorded for visit {$visit->visit_number}");

        return back()->with('status', 'Vitals recorded successfully.');
    }

    public function patients()
    {
        $patients = Patient::withCount('visits')
            ->latest()
            ->paginate(20);

        return view('nurse.patients', compact('patients'));
    }
}
