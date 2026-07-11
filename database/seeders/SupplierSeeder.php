<?php

namespace Database\Seeders;

use App\Models\Supplier;
use Illuminate\Database\Seeder;

class SupplierSeeder extends Seeder
{
    public function run(): void
    {
        $suppliers = [
            ['name' => 'MedSupply Tanzania', 'contact_person' => 'John Daudi', 'phone' => '+255 711 111 111', 'email' => 'sales@medsupply.co.tz'],
            ['name' => 'Pharma Solutions Ltd', 'contact_person' => 'Grace Mushi', 'phone' => '+255 712 222 222', 'email' => 'orders@pharmasolutions.co.tz'],
            ['name' => 'LabTech Equipment', 'contact_person' => 'Peter Nko', 'phone' => '+255 713 333 333', 'email' => 'info@labtech.co.tz'],
        ];

        foreach ($suppliers as $supplier) {
            Supplier::firstOrCreate(['email' => $supplier['email']], $supplier);
        }
    }
}
