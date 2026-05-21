<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class WarehouseSeeder extends Seeder
{
    /**
     * Seed warehouses and storage locations.
     */
    public function run(): void
    {
        $now = now();
        $managerId = DB::table('users')->where('email', 'manager@warehouse.test')->value('id');

        $warehouses = [
            ['code' => 'HCM01', 'name' => 'Kho Ho Chi Minh', 'address' => 'Quan 7, TP. Ho Chi Minh'],
            ['code' => 'HN01', 'name' => 'Kho Ha Noi', 'address' => 'Cau Giay, Ha Noi'],
        ];

        foreach ($warehouses as $warehouse) {
            DB::table('warehouses')->updateOrInsert(
                ['code' => $warehouse['code']],
                [
                    'name' => $warehouse['name'],
                    'address' => $warehouse['address'],
                    'manager_id' => $managerId,
                    'status' => 'active',
                    'created_at' => $now,
                    'updated_at' => $now,
                ],
            );
        }

        $locations = [
            ['warehouse' => 'HCM01', 'code' => 'A-01-01', 'name' => 'Ke A - Tang 01 - O 01', 'type' => 'rack', 'is_pickable' => true],
            ['warehouse' => 'HCM01', 'code' => 'A-01-02', 'name' => 'Ke A - Tang 01 - O 02', 'type' => 'rack', 'is_pickable' => true],
            ['warehouse' => 'HCM01', 'code' => 'QC-01', 'name' => 'Khu kiem hang', 'type' => 'staging', 'is_pickable' => false],
            ['warehouse' => 'HN01', 'code' => 'B-01-01', 'name' => 'Ke B - Tang 01 - O 01', 'type' => 'rack', 'is_pickable' => true],
        ];

        foreach ($locations as $location) {
            $warehouseId = DB::table('warehouses')->where('code', $location['warehouse'])->value('id');

            DB::table('storage_locations')->updateOrInsert(
                ['warehouse_id' => $warehouseId, 'code' => $location['code']],
                [
                    'name' => $location['name'],
                    'type' => $location['type'],
                    'is_pickable' => $location['is_pickable'],
                    'created_at' => $now,
                    'updated_at' => $now,
                ],
            );
        }
    }
}
