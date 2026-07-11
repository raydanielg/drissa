<?php

namespace Database\Seeders;

use App\Models\ClinicalRecord;
use App\Models\Patient;
use App\Models\User;
use App\Models\Visit;
use Illuminate\Database\Seeder;

class ClinicalRecordSeeder extends Seeder
{
    public function run(): void
    {
        $patient = Patient::first();
        $doctor = User::role('doctor')->first();
        $visit = Visit::first();

        if (! $patient || ! $doctor) return;

        ClinicalRecord::create([
            'patient_id' => $patient->id,
            'visit_id' => $visit?->id,
            'doctor_id' => $doctor->id,
            'chief_complaint' => 'Kichwa kumaumivu na homa',
            'symptoms' => 'Homa, maumivu ya kichwa, uchovu',
            'diagnosis' => 'Malaria suspected',
            'treatment_plan' => 'Rest, fluids, antimalarial medication',
            'prescription' => "Paracetamol 500mg x 3 daily\nArtemether/Lumefantrine 1x2x3 days",
            'notes' => 'Patient advised to return after 3 days if no improvement.',
            'record_date' => now()->subDays(2),
        ]);
    }
}
