<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    /**
     * Seed the application's users.
     */
    public function run(): void
    {
        $now = now();

        $users = [
            ['name' => 'Admin Kho', 'email' => 'admin@warehouse.test', 'role' => 'admin'],
            ['name' => 'Quan Ly Kho', 'email' => 'manager@warehouse.test', 'role' => 'warehouse_manager'],
            ['name' => 'Nhan Vien Mua Hang', 'email' => 'purchase@warehouse.test', 'role' => 'purchasing_staff'],
            ['name' => 'Nhan Vien Ban Hang', 'email' => 'sales@warehouse.test', 'role' => 'sales_staff'],
            ['name' => 'Nhan Vien Kho', 'email' => 'staff@warehouse.test', 'role' => 'staff'],
        ];

        foreach ($users as $user) {
            DB::table('users')->updateOrInsert(
                ['email' => $user['email']],
                [
                    'name' => $user['name'],
                    'password' => Hash::make('password'),
                    'role' => $user['role'],
                    'status' => 'active',
                    'email_verified_at' => $now,
                    'created_at' => $now,
                    'updated_at' => $now,
                ],
            );
        }
    }
}
