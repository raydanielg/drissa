<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $this->call([
            // Core: roles must exist before users can be assigned to them
            RoleSeeder::class,
            UserSeeder::class,

            // Organizational structure
            DepartmentSeeder::class,
            ShiftSeeder::class,
            ClinicRoomSeeder::class,

            // Inventory & services
            CategorySeeder::class,
            SupplierSeeder::class,
            ProductSeeder::class,
            ServiceSeeder::class,
            CatalogSeeder::class,
            LabEquipmentSeeder::class,

            // System configuration
            SystemDefaultsSeeder::class,
            SmsDefaultsSeeder::class,
            SmsTemplateSeeder::class,

            // Sample data (depends on users/roles above)
            SampleDataSeeder::class,
            AppointmentSeeder::class,
            ClinicalRecordSeeder::class,
            ChatSeeder::class,
        ]);
    }
}
