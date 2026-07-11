<?php

namespace Database\Seeders;

use App\Models\Category;
use Illuminate\Database\Seeder;

class CategorySeeder extends Seeder
{
    public function run(): void
    {
        $categories = [
            ['name' => 'Pain Relief', 'description' => 'Medicines used to relieve pain and reduce fever.', 'is_active' => true],
            ['name' => 'Antibiotics', 'description' => 'Medicines that fight bacterial infections.', 'is_active' => true],
            ['name' => 'Antihistamines', 'description' => 'Medicines used to treat allergies.', 'is_active' => true],
            ['name' => 'Supplements', 'description' => 'Vitamins, minerals and dietary supplements.', 'is_active' => true],
            ['name' => 'Rehydration', 'description' => 'Products for restoring body fluids and electrolytes.', 'is_active' => true],
            ['name' => 'Medical Supplies', 'description' => 'Disposable medical items and equipment.', 'is_active' => true],
            ['name' => 'Cough & Cold', 'description' => 'Medicines for cough, cold and flu symptoms.', 'is_active' => true],
            ['name' => 'Gastrointestinal', 'description' => 'Medicines for stomach and digestive issues.', 'is_active' => true],
        ];

        foreach ($categories as $category) {
            Category::firstOrCreate(
                ['name' => $category['name']],
                $category
            );
        }
    }
}
