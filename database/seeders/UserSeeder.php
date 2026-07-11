<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $users = [
            ['name' => 'Admin User', 'email' => 'admin@drissa.test', 'role' => 'admin'],
            ['name' => 'Reception One', 'email' => 'reception@drissa.test', 'role' => 'reception'],
            ['name' => 'Dr. John Doe', 'email' => 'doctor@drissa.test', 'role' => 'doctor'],
            ['name' => 'Lab Technician', 'email' => 'lab@drissa.test', 'role' => 'lab'],
            ['name' => 'Pharmacist Jane', 'email' => 'pharmacy@drissa.test', 'role' => 'pharmacy'],
        ];

        foreach ($users as $userData) {
            $role = $userData['role'];
            unset($userData['role']);

            $user = User::firstOrCreate(
                ['email' => $userData['email']],
                array_merge($userData, ['password' => Hash::make('password')])
            );

            if (! $user->hasRole($role)) {
                $user->assignRole($role);
            }
        }
    }
}
