<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class StockTransferSeeder extends Seeder
{
    /**
     * Seed stock transfer demo data.
     */
    public function run(): void
    {
        $now = now();
        $fromWarehouseId = DB::table('warehouses')->where('code', 'HCM01')->value('id');
        $toWarehouseId = DB::table('warehouses')->where('code', 'HN01')->value('id');
        $managerId = DB::table('users')->where('email', 'manager@warehouse.test')->value('id');

        DB::table('stock_transfers')->updateOrInsert(
            ['transfer_number' => 'TR-2026-0001'],
            [
                'from_warehouse_id' => $fromWarehouseId,
                'to_warehouse_id' => $toWarehouseId,
                'status' => 'completed',
                'shipped_at' => '2026-05-09 09:00:00',
                'received_at' => '2026-05-10 11:00:00',
                'note' => 'Chuyen switch red tu HCM ra HN.',
                'created_by' => $managerId,
                'completed_by' => $managerId,
                'created_at' => $now,
                'updated_at' => $now,
            ],
        );

        $transferId = DB::table('stock_transfers')->where('transfer_number', 'TR-2026-0001')->value('id');
        $variantId = DB::table('product_variants')->where('sku', 'SKU-MX-RED')->value('id');
        $fromLocationId = DB::table('storage_locations')
            ->where('warehouse_id', $fromWarehouseId)
            ->where('code', 'A-01-02')
            ->value('id');
        $toLocationId = DB::table('storage_locations')
            ->where('warehouse_id', $toWarehouseId)
            ->where('code', 'B-01-01')
            ->value('id');

        DB::table('stock_transfer_items')->updateOrInsert(
            ['stock_transfer_id' => $transferId, 'product_variant_id' => $variantId],
            [
                'from_location_id' => $fromLocationId,
                'to_location_id' => $toLocationId,
                'quantity' => 10,
                'note' => null,
                'created_at' => $now,
                'updated_at' => $now,
            ],
        );
    }
}
