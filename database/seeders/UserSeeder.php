<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use App\Models\User;

class UserSeeder extends Seeder
{
    public function run(): void
    {
        User::insert([
            [
                'name' => 'Admin User',
                'email' => 'admin@pos.com',
                'password' => Hash::make('admin123'),
                'role' => 'admin',
                'status' => 1,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'Manager User',
                'email' => 'manager@pos.com',
                'password' => Hash::make('manager123'),
                'role' => 'manager',
                'status' => 1,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'Cashier User',
                'email' => 'cashier@pos.com',
                'password' => Hash::make('cashier123'),
                'role' => 'cashier',
                'status' => 1,
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ]);
    }
}
