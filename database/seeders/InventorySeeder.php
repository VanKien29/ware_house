<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class InventorySeeder extends Seeder
{
    /**
     * Seed stock levels and stock movement ledger.
     */
    public function run(): void
    {
        $now = now();

        DB::table('stock_movements')->where('note', 'like', '[seed]%')->delete();

        $levels = [
            ['warehouse' => 'HCM01', 'location' => 'A-01-01', 'sku' => 'SKU-HG-RX78-REVIVE', 'on_hand' => 15, 'reserved' => 0, 'reorder' => 5, 'max' => 40, 'last' => '2026-05-12 11:30:00'],
            ['warehouse' => 'HCM01', 'location' => 'A-01-02', 'sku' => 'SKU-MGEX-SF', 'on_hand' => 1, 'reserved' => 0, 'reorder' => 2, 'max' => 10, 'last' => '2026-05-11 10:00:00'],
            ['warehouse' => 'HN01', 'location' => 'B-01-01', 'sku' => 'SKU-MGEX-SF', 'on_hand' => 1, 'reserved' => 0, 'reorder' => 1, 'max' => 6, 'last' => '2026-05-10 11:00:00'],
            ['warehouse' => 'HN01', 'location' => 'B-01-01', 'sku' => 'SKU-MN-AOBING', 'on_hand' => 0, 'reserved' => 0, 'reorder' => 1, 'max' => 8, 'last' => '2026-05-01 08:00:00'],
            ['warehouse' => 'HCM01', 'location' => 'QC-01', 'sku' => 'SKU-GM01-BLACK', 'on_hand' => 30, 'reserved' => 0, 'reorder' => 10, 'max' => 80, 'last' => '2026-05-01 08:00:00'],
        ];

        foreach ($levels as $level) {
            DB::table('stock_levels')->updateOrInsert(
                [
                    'warehouse_id' => $this->warehouseId($level['warehouse']),
                    'location_id' => $this->locationId($level['warehouse'], $level['location']),
                    'product_variant_id' => $this->variantId($level['sku']),
                ],
                [
                    'quantity_on_hand' => $level['on_hand'],
                    'quantity_reserved' => $level['reserved'],
                    'reorder_point' => $level['reorder'],
                    'max_stock' => $level['max'],
                    'last_movement_at' => $level['last'],
                    'created_at' => $now,
                    'updated_at' => $now,
                ],
            );
        }

        $managerId = DB::table('users')->where('email', 'manager@warehouse.test')->value('id');
        $receiptId = DB::table('goods_receipts')->where('receipt_number', 'GR-2026-0001')->value('id');
        $salesOrderId = DB::table('sales_orders')->where('so_number', 'SO-2026-0001')->value('id');
        $transferId = DB::table('stock_transfers')->where('transfer_number', 'TR-2026-0001')->value('id');
        $adjustmentId = DB::table('stock_adjustments')->where('adjustment_number', 'ADJ-2026-0001')->value('id');
        $countId = DB::table('inventory_counts')->where('count_number', 'CNT-2026-0001')->value('id');

        $movements = [
            ['warehouse' => 'HCM01', 'location' => 'A-01-01', 'sku' => 'SKU-HG-RX78-REVIVE', 'type' => 'opening_balance', 'ref_type' => 'manual', 'ref_id' => null, 'change' => 10, 'after' => 10, 'cost' => 180000, 'note' => '[seed] Opening balance HG RX-78-2 HCM', 'at' => '2026-05-01 08:00:00'],
            ['warehouse' => 'HCM01', 'location' => 'A-01-02', 'sku' => 'SKU-MGEX-SF', 'type' => 'opening_balance', 'ref_type' => 'manual', 'ref_id' => null, 'change' => 1, 'after' => 1, 'cost' => 1800000, 'note' => '[seed] Opening balance MGEX Strike Freedom HCM', 'at' => '2026-05-01 08:00:00'],
            ['warehouse' => 'HN01', 'location' => 'B-01-01', 'sku' => 'SKU-MN-AOBING', 'type' => 'opening_balance', 'ref_type' => 'manual', 'ref_id' => null, 'change' => 0, 'after' => 0, 'cost' => 1050000, 'note' => '[seed] Opening balance Motor Nuclear preorder HN', 'at' => '2026-05-01 08:00:00'],
            ['warehouse' => 'HCM01', 'location' => 'QC-01', 'sku' => 'SKU-GM01-BLACK', 'type' => 'opening_balance', 'ref_type' => 'manual', 'ref_id' => null, 'change' => 30, 'after' => 30, 'cost' => 55000, 'note' => '[seed] Opening balance Gundam Marker HCM', 'at' => '2026-05-01 08:00:00'],
            ['warehouse' => 'HCM01', 'location' => 'A-01-01', 'sku' => 'SKU-HG-RX78-REVIVE', 'type' => 'purchase_receipt', 'ref_type' => 'goods_receipt', 'ref_id' => $receiptId, 'change' => 8, 'after' => 18, 'cost' => 180000, 'note' => '[seed] Goods receipt GR-2026-0001 HG RX-78-2', 'at' => '2026-05-05 09:30:00'],
            ['warehouse' => 'HCM01', 'location' => 'A-01-02', 'sku' => 'SKU-MGEX-SF', 'type' => 'purchase_receipt', 'ref_type' => 'goods_receipt', 'ref_id' => $receiptId, 'change' => 3, 'after' => 4, 'cost' => 1800000, 'note' => '[seed] Goods receipt GR-2026-0001 MGEX Strike Freedom', 'at' => '2026-05-05 09:30:00'],
            ['warehouse' => 'HCM01', 'location' => 'A-01-01', 'sku' => 'SKU-HG-RX78-REVIVE', 'type' => 'sales_issue', 'ref_type' => 'sales_order', 'ref_id' => $salesOrderId, 'change' => -2, 'after' => 16, 'cost' => 180000, 'note' => '[seed] Sales issue SO-2026-0001 HG RX-78-2', 'at' => '2026-05-07 15:00:00'],
            ['warehouse' => 'HCM01', 'location' => 'A-01-02', 'sku' => 'SKU-MGEX-SF', 'type' => 'sales_issue', 'ref_type' => 'sales_order', 'ref_id' => $salesOrderId, 'change' => -1, 'after' => 3, 'cost' => 1800000, 'note' => '[seed] Sales issue SO-2026-0001 MGEX Strike Freedom', 'at' => '2026-05-07 15:00:00'],
            ['warehouse' => 'HCM01', 'location' => 'A-01-02', 'sku' => 'SKU-MGEX-SF', 'type' => 'transfer_out', 'ref_type' => 'stock_transfer', 'ref_id' => $transferId, 'change' => -1, 'after' => 2, 'cost' => 1800000, 'note' => '[seed] Transfer out TR-2026-0001 MGEX Strike Freedom', 'at' => '2026-05-09 09:00:00'],
            ['warehouse' => 'HN01', 'location' => 'B-01-01', 'sku' => 'SKU-MGEX-SF', 'type' => 'transfer_in', 'ref_type' => 'stock_transfer', 'ref_id' => $transferId, 'change' => 1, 'after' => 1, 'cost' => 1800000, 'note' => '[seed] Transfer in TR-2026-0001 MGEX Strike Freedom', 'at' => '2026-05-10 11:00:00'],
            ['warehouse' => 'HCM01', 'location' => 'A-01-02', 'sku' => 'SKU-MGEX-SF', 'type' => 'adjustment', 'ref_type' => 'stock_adjustment', 'ref_id' => $adjustmentId, 'change' => -1, 'after' => 1, 'cost' => 1800000, 'note' => '[seed] Adjustment ADJ-2026-0001 damaged MGEX box', 'at' => '2026-05-11 10:00:00'],
            ['warehouse' => 'HCM01', 'location' => 'A-01-01', 'sku' => 'SKU-HG-RX78-REVIVE', 'type' => 'stock_count', 'ref_type' => 'inventory_count', 'ref_id' => $countId, 'change' => -1, 'after' => 15, 'cost' => 180000, 'note' => '[seed] Inventory count CNT-2026-0001 HG RX-78-2 variance', 'at' => '2026-05-12 11:30:00'],
        ];

        foreach ($movements as $movement) {
            DB::table('stock_movements')->insert([
                'warehouse_id' => $this->warehouseId($movement['warehouse']),
                'location_id' => $this->locationId($movement['warehouse'], $movement['location']),
                'product_variant_id' => $this->variantId($movement['sku']),
                'movement_type' => $movement['type'],
                'reference_type' => $movement['ref_type'],
                'reference_id' => $movement['ref_id'],
                'quantity_change' => $movement['change'],
                'quantity_after' => $movement['after'],
                'unit_cost' => $movement['cost'],
                'note' => $movement['note'],
                'created_by' => $managerId,
                'created_at' => $movement['at'],
            ]);
        }
    }

    private function warehouseId(string $code): int
    {
        return (int) DB::table('warehouses')->where('code', $code)->value('id');
    }

    private function locationId(string $warehouseCode, string $locationCode): int
    {
        return (int) DB::table('storage_locations')
            ->where('warehouse_id', $this->warehouseId($warehouseCode))
            ->where('code', $locationCode)
            ->value('id');
    }

    private function variantId(string $sku): int
    {
        return (int) DB::table('product_variants')->where('sku', $sku)->value('id');
    }
}
