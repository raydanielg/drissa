<?php

namespace Database\Seeders;

use App\Models\Shift;
use Illuminate\Database\Seeder;

class ShiftSeeder extends Seeder
{
    public function run(): void
    {
        $shifts = [
            ['name' => 'Morning Shift', 'start_time' => '07:00', 'end_time' => '15:00', 'description' => 'Main morning shift.'],
            ['name' => 'Afternoon Shift', 'start_time' => '15:00', 'end_time' => '23:00', 'description' => 'Afternoon/evening shift.'],
            ['name' => 'Night Shift', 'start_time' => '23:00', 'end_time' => '07:00', 'description' => 'Overnight coverage.'],
        ];

        foreach ($shifts as $shift) {
            Shift::firstOrCreate(['name' => $shift['name']], $shift);
        }
    }
}
