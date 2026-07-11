<?php

namespace Database\Seeders;

use App\Models\Product;
use Illuminate\Database\Seeder;

class ProductSeeder extends Seeder
{
    public function run(): void
    {
        $products = [
            [
                'name' => 'Paracetamol 500mg Tablets',
                'sku' => 'PAR-500-001',
                'category' => 'Pain Relief',
                'description' => 'Oral analgesic and antipyretic tablets, 500mg.',
                'cost_price' => 1200,
                'selling_price' => 1800,
                'quantity' => 500,
                'reorder_level' => 100,
                'unit' => 'tablets',
                'is_active' => true,
            ],
            [
                'name' => 'Amoxicillin 250mg Capsules',
                'sku' => 'AMX-250-002',
                'category' => 'Antibiotics',
                'description' => 'Broad-spectrum antibiotic capsules.',
                'cost_price' => 2500,
                'selling_price' => 3500,
                'quantity' => 200,
                'reorder_level' => 50,
                'unit' => 'capsules',
                'is_active' => true,
            ],
            [
                'name' => 'Ibuprofen 400mg Tablets',
                'sku' => 'IBU-400-003',
                'category' => 'Pain Relief',
                'description' => 'Non-steroidal anti-inflammatory drug.',
                'cost_price' => 1500,
                'selling_price' => 2200,
                'quantity' => 350,
                'reorder_level' => 75,
                'unit' => 'tablets',
                'is_active' => true,
            ],
            [
                'name' => 'ORS Sachets',
                'sku' => 'ORS-001-004',
                'category' => 'Rehydration',
                'description' => 'Oral rehydration salts for diarrhoea management.',
                'cost_price' => 300,
                'selling_price' => 500,
                'quantity' => 80,
                'reorder_level' => 100,
                'unit' => 'sachets',
                'is_active' => true,
            ],
            [
                'name' => 'Vitamin C 1000mg',
                'sku' => 'VIT-C-005',
                'category' => 'Supplements',
                'description' => 'Immune support vitamin C tablets.',
                'cost_price' => 4000,
                'selling_price' => 5500,
                'quantity' => 120,
                'reorder_level' => 30,
                'unit' => 'tablets',
                'is_active' => true,
            ],
            [
                'name' => 'Cetirizine 10mg Tablets',
                'sku' => 'CET-10-006',
                'category' => 'Antihistamines',
                'description' => 'Antihistamine for allergies and hay fever.',
                'cost_price' => 900,
                'selling_price' => 1400,
                'quantity' => 0,
                'reorder_level' => 40,
                'unit' => 'tablets',
                'is_active' => true,
            ],
            [
                'name' => 'Metronidazole 400mg Tablets',
                'sku' => 'MET-400-007',
                'category' => 'Antibiotics',
                'description' => 'Antiprotozoal and antibacterial tablets.',
                'cost_price' => 1800,
                'selling_price' => 2600,
                'quantity' => 45,
                'reorder_level' => 50,
                'unit' => 'tablets',
                'is_active' => true,
            ],
            [
                'name' => 'Disposable Syringes 5ml',
                'sku' => 'SYR-5ML-008',
                'category' => 'Medical Supplies',
                'description' => 'Sterile single-use syringes, 5ml.',
                'cost_price' => 150,
                'selling_price' => 300,
                'quantity' => 1000,
                'reorder_level' => 200,
                'unit' => 'pieces',
                'is_active' => true,
            ],
            [
                'name' => 'Bandages 2 inch',
                'sku' => 'BAN-2IN-009',
                'category' => 'Medical Supplies',
                'description' => 'Elastic adhesive bandage roll, 2 inches.',
                'cost_price' => 800,
                'selling_price' => 1300,
                'quantity' => 60,
                'reorder_level' => 50,
                'unit' => 'rolls',
                'is_active' => true,
            ],
            [
                'name' => 'Aspirin 300mg Tablets',
                'sku' => 'ASP-300-010',
                'category' => 'Pain Relief',
                'description' => 'Analgesic and antiplatelet tablets.',
                'cost_price' => 700,
                'selling_price' => 1100,
                'quantity' => 30,
                'reorder_level' => 40,
                'unit' => 'tablets',
                'is_active' => false,
            ],
        ];

        foreach ($products as $product) {
            Product::firstOrCreate(
                ['sku' => $product['sku']],
                $product
            );
        }
    }
}
