<?php

namespace Database\Seeders;

use App\Models\Department;
use Illuminate\Database\Seeder;

class DepartmentSeeder extends Seeder
{
    public function run(): void
    {
        $departments = [
            ['name' => 'Administration', 'code' => 'ADMIN', 'description' => 'Hospital administration and management.'],
            ['name' => 'Reception', 'code' => 'RECP', 'description' => 'Front desk and patient registration.'],
            ['name' => 'Outpatient', 'code' => 'OPD', 'description' => 'Outpatient consultation services.'],
            ['name' => 'Laboratory', 'code' => 'LAB', 'description' => 'Diagnostic laboratory services.'],
            ['name' => 'Pharmacy', 'code' => 'PHARM', 'description' => 'Medication dispensing and inventory.'],
            ['name' => 'Radiology', 'code' => 'RAD', 'description' => 'Imaging and radiology services.'],
            ['name' => 'Nursing', 'code' => 'NURS', 'description' => 'General nursing care.'],
            ['name' => 'Finance', 'code' => 'FIN', 'description' => 'Billing, payments and accounting.'],
        ];

        foreach ($departments as $department) {
            Department::firstOrCreate(['code' => $department['code']], $department);
        }
    }
}
