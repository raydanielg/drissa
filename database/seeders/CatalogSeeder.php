<?php

namespace Database\Seeders;

use App\Models\LabTest;
use App\Models\Medication;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class CatalogSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $labTests = [
            ['name' => 'CBC', 'code' => 'CBC', 'price' => 15000, 'unit' => 'mm3', 'reference_range' => 'Normal'],
            ['name' => 'Malaria', 'code' => 'MAL', 'price' => 10000],
            ['name' => 'Urinalysis', 'code' => 'URI', 'price' => 12000],
            ['name' => 'Blood Sugar', 'code' => 'GLU', 'price' => 10000, 'unit' => 'mg/dL'],
            ['name' => 'HIV Test', 'code' => 'HIV', 'price' => 8000],
            ['name' => 'X-Ray', 'code' => 'XRAY', 'price' => 35000],
        ];

        foreach ($labTests as $test) {
            LabTest::firstOrCreate(['code' => $test['code']], $test);
        }

        $medications = [
            ['name' => 'Paracetamol 500mg', 'generic_name' => 'Paracetamol', 'form' => 'tablet', 'stock_quantity' => 500, 'unit_price' => 200],
            ['name' => 'Amoxicillin 250mg', 'generic_name' => 'Amoxicillin', 'form' => 'capsule', 'stock_quantity' => 300, 'unit_price' => 500],
            ['name' => 'Ibuprofen 400mg', 'generic_name' => 'Ibuprofen', 'form' => 'tablet', 'stock_quantity' => 400, 'unit_price' => 300],
            ['name' => 'ORS Sachets', 'generic_name' => 'Oral Rehydration Salts', 'form' => 'sachet', 'stock_quantity' => 1000, 'unit_price' => 150],
            ['name' => 'Cough Syrup', 'generic_name' => 'Dextromethorphan', 'form' => 'syrup', 'stock_quantity' => 120, 'unit_price' => 2500],
        ];

        foreach ($medications as $med) {
            Medication::firstOrCreate(['name' => $med['name']], $med);
        }
    }
}
