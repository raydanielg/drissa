<?php

namespace App\Http\Controllers;

use App\Models\Patient;
use Illuminate\Support\Str;

class PatientHistoryController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function show(Patient $patient)
    {
        $patient->load(['visits.doctor', 'clinicalRecords.doctor', 'appointments.doctor', 'documents']);

        $timeline = collect()
            ->merge($patient->visits->map(fn($v) => [
                'date' => $v->registered_at ?? $v->created_at,
                'type' => 'visit',
                'title' => 'Visit ' . $v->visit_number,
                'subtitle' => 'Status: ' . str_replace('_', ' ', $v->status),
                'link' => $v->clinicalRecord ? route('clinical-records.show', $v->clinicalRecord) : null,
            ]))
            ->merge($patient->clinicalRecords->map(fn($r) => [
                'date' => $r->record_date,
                'type' => 'record',
                'title' => 'Clinical Record',
                'subtitle' => 'Diagnosis: ' . Str::limit($r->diagnosis, 40),
                'link' => route('clinical-records.show', $r),
            ]))
            ->merge($patient->appointments->map(fn($a) => [
                'date' => $a->scheduled_at,
                'type' => 'appointment',
                'title' => 'Appointment',
                'subtitle' => 'Status: ' . str_replace('_', ' ', $a->status),
                'link' => route('appointments.show', $a),
            ]))
            ->merge($patient->documents->map(fn($d) => [
                'date' => $d->created_at,
                'type' => 'document',
                'title' => 'Document: ' . $d->title,
                'subtitle' => 'Category: ' . ucfirst($d->category),
                'link' => $d->fileUrl(),
            ]))
            ->sortByDesc('date')
            ->values();

        return view('patients.history', compact('patient', 'timeline'));
    }
}
