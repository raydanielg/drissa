<?php

namespace Database\Seeders;

use App\Models\ClinicRoom;
use Illuminate\Database\Seeder;

class ClinicRoomSeeder extends Seeder
{
    public function run(): void
    {
        $rooms = [
            ['name' => 'Consultation Room 1', 'code' => 'CR-001', 'type' => 'consultation', 'status' => 'available', 'capacity' => 1],
            ['name' => 'Consultation Room 2', 'code' => 'CR-002', 'type' => 'consultation', 'status' => 'available', 'capacity' => 1],
            ['name' => 'Procedure Room', 'code' => 'PR-001', 'type' => 'procedure', 'status' => 'available', 'capacity' => 2],
            ['name' => 'Emergency Bay', 'code' => 'ER-001', 'type' => 'emergency', 'status' => 'available', 'capacity' => 3],
            ['name' => 'General Ward', 'code' => 'WD-001', 'type' => 'ward', 'status' => 'available', 'capacity' => 10],
        ];

        foreach ($rooms as $room) {
            ClinicRoom::firstOrCreate(['code' => $room['code']], $room);
        }
    }
}
