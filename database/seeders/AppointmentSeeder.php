<?php

namespace Database\Seeders;

use App\Models\Appointment;
use App\Models\Patient;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Database\Seeder;

class AppointmentSeeder extends Seeder
{
    public function run(): void
    {
        $patients = Patient::pluck('id');
        $doctors = User::role('doctor')->pluck('id');

        if ($patients->isEmpty() || $doctors->isEmpty()) {
            return;
        }

        $types = ['general', 'followup', 'emergency'];
        $statuses = ['scheduled', 'confirmed', 'completed', 'cancelled', 'no_show'];

        $slots = [
            ['day' => 0, 'time' => '08:00', 'status' => 'confirmed'],
            ['day' => 0, 'time' => '09:30', 'status' => 'scheduled'],
            ['day' => 0, 'time' => '11:00', 'status' => 'scheduled'],
            ['day' => 0, 'time' => '14:00', 'status' => 'confirmed'],
            ['day' => 1, 'time' => '08:30', 'status' => 'scheduled'],
            ['day' => 1, 'time' => '10:00', 'status' => 'confirmed'],
            ['day' => 1, 'time' => '13:30', 'status' => 'scheduled'],
            ['day' => 2, 'time' => '09:00', 'status' => 'scheduled'],
            ['day' => 2, 'time' => '15:00', 'status' => 'confirmed'],
            ['day' => -1, 'time' => '10:30', 'status' => 'completed'],
            ['day' => -1, 'time' => '12:00', 'status' => 'no_show'],
            ['day' => -2, 'time' => '09:00', 'status' => 'completed'],
            ['day' => 3, 'time' => '11:30', 'status' => 'scheduled'],
            ['day' => 4, 'time' => '14:30', 'status' => 'scheduled'],
        ];

        foreach ($slots as $slot) {
            $scheduledAt = Carbon::today()->addDays($slot['day'])->setTimeFromTimeString($slot['time']);

            Appointment::firstOrCreate(
                [
                    'patient_id' => $patients->random(),
                    'scheduled_at' => $scheduledAt,
                ],
                [
                    'doctor_id' => $doctors->random(),
                    'type' => $types[array_rand($types)],
                    'status' => $slot['status'],
                    'notes' => 'Sample appointment created by seeder.',
                ]
            );
        }
    }
}
