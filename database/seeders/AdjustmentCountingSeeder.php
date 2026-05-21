<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class AdjustmentCountingSeeder extends Seeder
{
    /**
     * Seed stock adjustment and inventory count demo data.
     */
    public function run(): void
    {
        $now = now();
        $warehouseId = DB::table('warehouses')->where('code', 'HCM01')->value('id');
        $managerId = DB::table('users')->where('email', 'manager@warehouse.test')->value('id');
        $staffId = DB::table('users')->where('email', 'staff@warehouse.test')->value('id');

        DB::table('stock_adjustments')->updateOrInsert(
            ['adjustment_number' => 'ADJ-2026-0001'],
            [
                'warehouse_id' => $warehouseId,
                'status' => 'posted',
                'reason' => 'damaged',
                'note' => 'Hang loi trong qua trinh kiem tra.',
                'created_by' => $managerId,
                'posted_by' => $managerId,
                'posted_at' => '2026-05-11 10:00:00',
                'created_at' => $now,
                'updated_at' => $now,
            ],
        );

        $adjustmentId = DB::table('stock_adjustments')->where('adjustment_number', 'ADJ-2026-0001')->value('id');
        $switchVariantId = DB::table('product_variants')->where('sku', 'SKU-MX-RED')->value('id');
        $switchLocationId = DB::table('storage_locations')
            ->where('warehouse_id', $warehouseId)
            ->where('code', 'A-01-02')
            ->value('id');

        DB::table('stock_adjustment_items')->updateOrInsert(
            ['stock_adjustment_id' => $adjustmentId, 'product_variant_id' => $switchVariantId],
            [
                'location_id' => $switchLocationId,
                'quantity_change' => -2,
                'unit_cost' => 4500,
                'note' => 'Loai bo 2 switch loi.',
                'created_at' => $now,
                'updated_at' => $now,
            ],
        );

        DB::table('inventory_counts')->updateOrInsert(
            ['count_number' => 'CNT-2026-0001'],
            [
                'warehouse_id' => $warehouseId,
                'status' => 'reconciled',
                'started_at' => '2026-05-12 08:00:00',
                'finished_at' => '2026-05-12 11:00:00',
                'note' => 'Kiem ke nhanh khu A.',
                'created_by' => $managerId,
                'reconciled_by' => $managerId,
                'reconciled_at' => '2026-05-12 11:30:00',
                'created_at' => $now,
                'updated_at' => $now,
            ],
        );

        $countId = DB::table('inventory_counts')->where('count_number', 'CNT-2026-0001')->value('id');
        $keyboardVariantId = DB::table('product_variants')->where('sku', 'SKU-RK61-BLK')->value('id');
        $keyboardLocationId = DB::table('storage_locations')
            ->where('warehouse_id', $warehouseId)
            ->where('code', 'A-01-01')
            ->value('id');

        DB::table('inventory_count_items')->updateOrInsert(
            ['inventory_count_id' => $countId, 'product_variant_id' => $keyboardVariantId, 'location_id' => $keyboardLocationId],
            [
                'system_quantity' => 26,
                'counted_quantity' => 25,
                'variance_quantity' => -1,
                'note' => 'Lech thieu 1 ban phim sau kiem ke.',
                'counted_by' => $staffId,
                'counted_at' => '2026-05-12 10:20:00',
                'created_at' => $now,
                'updated_at' => $now,
            ],
        );
    }
}
