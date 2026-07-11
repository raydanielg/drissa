<?php

namespace Database\Seeders;

use App\Models\Service;
use Illuminate\Database\Seeder;

class ServiceSeeder extends Seeder
{
    public function run(): void
    {
        $services = [
            ['name' => 'General Consultation', 'description' => 'Standard outpatient consultation.', 'price' => 10000, 'duration_minutes' => 15, 'color' => '#10b981'],
            ['name' => 'Dental Checkup', 'description' => 'Comprehensive dental examination.', 'price' => 25000, 'duration_minutes' => 30, 'color' => '#3b82f6'],
            ['name' => 'Malaria Test', 'description' => 'Rapid malaria diagnostic test.', 'price' => 8000, 'duration_minutes' => 15, 'color' => '#f59e0b'],
            ['name' => 'Blood Sugar Test', 'description' => 'Glucose level check.', 'price' => 7000, 'duration_minutes' => 10, 'color' => '#8b5cf6'],
            ['name' => 'Ultrasound Scan', 'description' => 'General ultrasound imaging.', 'price' => 50000, 'duration_minutes' => 45, 'color' => '#ec4899'],
            ['name' => 'X-Ray', 'description' => 'Radiology imaging service.', 'price' => 35000, 'duration_minutes' => 30, 'color' => '#6366f1'],
        ];

        foreach ($services as $service) {
            Service::firstOrCreate(['name' => $service['name']], $service);
        }
    }
}
