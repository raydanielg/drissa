<?php

namespace Database\Seeders;

use App\Models\LabEquipment;
use Illuminate\Database\Seeder;

class LabEquipmentSeeder extends Seeder
{
    public function run(): void
    {
        $equipment = [
            [
                'name' => 'Hematology Analyzer',
                'model' => 'Sysmex XN-1000',
                'serial_number' => 'HN-' . now()->format('Y') . '-001',
                'manufacturer' => 'Sysmex',
                'purchase_date' => now()->subYears(2),
                'last_service_date' => now()->subMonths(2),
                'next_service_date' => now()->addMonths(4),
                'status' => 'active',
                'notes' => 'Used for CBC and blood profile tests.',
            ],
            [
                'name' => 'Chemistry Analyzer',
                'model' => 'Mindray BS-240',
                'serial_number' => 'CA-' . now()->format('Y') . '-002',
                'manufacturer' => 'Mindray',
                'purchase_date' => now()->subYears(1),
                'last_service_date' => now()->subMonths(1),
                'next_service_date' => now()->addMonths(5),
                'status' => 'active',
                'notes' => 'Used for liver, kidney and lipid panels.',
            ],
            [
                'name' => 'Microscope',
                'model' => 'Olympus CX23',
                'serial_number' => 'MI-' . now()->format('Y') . '-003',
                'manufacturer' => 'Olympus',
                'purchase_date' => now()->subYears(3),
                'last_service_date' => now()->subMonths(3),
                'next_service_date' => now()->addMonths(3),
                'status' => 'active',
                'notes' => 'Used for urine microscopy and stool exams.',
            ],
            [
                'name' => 'Centrifuge',
                'model' => 'Eppendorf 5804',
                'serial_number' => 'CE-' . now()->format('Y') . '-004',
                'manufacturer' => 'Eppendorf',
                'purchase_date' => now()->subYears(2)->subMonths(6),
                'last_service_date' => now()->subMonths(5),
                'next_service_date' => now()->addMonths(1),
                'status' => 'maintenance',
                'notes' => 'Scheduled for rotor replacement and calibration.',
            ],
            [
                'name' => 'Urinalysis Analyzer',
                'model' => 'Uritest 500B',
                'serial_number' => 'UA-' . now()->format('Y') . '-005',
                'manufacturer' => 'Urit',
                'purchase_date' => now()->subYear(),
                'last_service_date' => now()->subMonths(2),
                'next_service_date' => now()->addMonths(4),
                'status' => 'active',
                'notes' => 'Used for urine dipstick analysis.',
            ],
            [
                'name' => 'Electrolyte Analyzer',
                'model' => 'Medica Easylyte',
                'serial_number' => 'EA-' . now()->format('Y') . '-006',
                'manufacturer' => 'Medica',
                'purchase_date' => now()->subYears(4),
                'last_service_date' => now()->subMonths(8),
                'next_service_date' => null,
                'status' => 'retired',
                'notes' => 'Decommissioned due to age and spare parts unavailability.',
            ],
        ];

        foreach ($equipment as $item) {
            LabEquipment::firstOrCreate(
                ['serial_number' => $item['serial_number']],
                $item
            );
        }
    }
}
