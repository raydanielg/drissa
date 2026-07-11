<?php

namespace Database\Seeders;

use App\Enums\VisitStatus;
use App\Models\Patient;
use App\Models\User;
use App\Models\Visit;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class SampleDataSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $reception = User::role('reception')->first();
        $doctor = User::role('doctor')->first();

        $patients = [
            ['first_name' => 'Juma', 'last_name' => 'Hassan', 'gender' => 'male', 'phone' => '255700000001'],
            ['first_name' => 'Amina', 'last_name' => 'Omar', 'gender' => 'female', 'phone' => '255700000002'],
            ['first_name' => 'Peter', 'last_name' => 'Mushi', 'gender' => 'male', 'phone' => '255700000003'],
            ['first_name' => 'Grace', 'last_name' => 'John', 'gender' => 'female', 'phone' => '255700000004'],
        ];

        $createdPatients = [];
        foreach ($patients as $i => $data) {
            $createdPatients[] = Patient::firstOrCreate(
                ['phone' => $data['phone']],
                array_merge($data, [
                    'mrn' => 'MRN-' . now()->format('Y') . '-' . str_pad($i + 100, 5, '0', STR_PAD_LEFT),
                    'date_of_birth' => now()->subYears(rand(18, 65))->format('Y-m-d'),
                ])
            );
        }

        if ($reception && $doctor) {
            $statuses = [
                VisitStatus::Registered->value,
                VisitStatus::WaitingForDoctor->value,
                VisitStatus::Completed->value,
                VisitStatus::WaitingForPayment->value,
            ];

            foreach ($createdPatients as $i => $patient) {
                Visit::firstOrCreate(
                    ['visit_number' => 'VIS-' . now()->format('Y') . '-' . str_pad($i + 100, 6, '0', STR_PAD_LEFT)],
                    [
                        'patient_id' => $patient->id,
                        'doctor_id' => $doctor->id,
                        'received_by' => $reception->id,
                        'status' => $statuses[$i % count($statuses)],
                        'chief_complaint' => 'General checkup',
                        'type' => 'outpatient',
                        'registered_at' => now()->subDays(rand(0, 5)),
                    ]
                );
            }
        }
    }
}
