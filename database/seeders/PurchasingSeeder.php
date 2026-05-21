<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class PurchasingSeeder extends Seeder
{
    /**
     * Seed purchase order and goods receipt demo data.
     */
    public function run(): void
    {
        $now = now();
        $supplierId = DB::table('suppliers')->where('code', 'SUP-HOBBY')->value('id');
        $warehouseId = DB::table('warehouses')->where('code', 'HCM01')->value('id');
        $creatorId = DB::table('users')->where('email', 'purchase@warehouse.test')->value('id');
        $managerId = DB::table('users')->where('email', 'manager@warehouse.test')->value('id');

        DB::table('purchase_orders')->updateOrInsert(
            ['po_number' => 'PO-2026-0001'],
            [
                'supplier_id' => $supplierId,
                'warehouse_id' => $warehouseId,
                'status' => 'received',
                'order_date' => '2026-05-01',
                'expected_date' => '2026-05-05',
                'note' => 'Don nhap model kit Bandai cho kho HCM.',
                'created_by' => $creatorId,
                'approved_by' => $managerId,
                'approved_at' => '2026-05-01 10:00:00',
                'created_at' => $now,
                'updated_at' => $now,
            ],
        );

        $poId = DB::table('purchase_orders')->where('po_number', 'PO-2026-0001')->value('id');
        $items = [
            ['sku' => 'SKU-HG-RX78-REVIVE', 'ordered_quantity' => 8, 'received_quantity' => 8, 'unit_price' => 180000],
            ['sku' => 'SKU-MGEX-SF', 'ordered_quantity' => 3, 'received_quantity' => 3, 'unit_price' => 1800000],
        ];

        foreach ($items as $item) {
            $variantId = DB::table('product_variants')->where('sku', $item['sku'])->value('id');

            DB::table('purchase_order_items')->updateOrInsert(
                ['purchase_order_id' => $poId, 'product_variant_id' => $variantId],
                [
                    'ordered_quantity' => $item['ordered_quantity'],
                    'received_quantity' => $item['received_quantity'],
                    'unit_price' => $item['unit_price'],
                    'note' => null,
                    'created_at' => $now,
                    'updated_at' => $now,
                ],
            );
        }

        DB::table('goods_receipts')->updateOrInsert(
            ['receipt_number' => 'GR-2026-0001'],
            [
                'purchase_order_id' => $poId,
                'supplier_id' => $supplierId,
                'warehouse_id' => $warehouseId,
                'status' => 'posted',
                'received_at' => '2026-05-05 09:00:00',
                'note' => 'Nhap model kit theo PO-2026-0001.',
                'created_by' => $managerId,
                'posted_by' => $managerId,
                'posted_at' => '2026-05-05 09:30:00',
                'created_at' => $now,
                'updated_at' => $now,
            ],
        );

        $receiptId = DB::table('goods_receipts')->where('receipt_number', 'GR-2026-0001')->value('id');
        $locationId = DB::table('storage_locations')
            ->where('warehouse_id', $warehouseId)
            ->where('code', 'A-01-01')
            ->value('id');
        $premiumKitLocationId = DB::table('storage_locations')
            ->where('warehouse_id', $warehouseId)
            ->where('code', 'A-01-02')
            ->value('id');

        foreach ($items as $item) {
            $variantId = DB::table('product_variants')->where('sku', $item['sku'])->value('id');
            $poItemId = DB::table('purchase_order_items')
                ->where('purchase_order_id', $poId)
                ->where('product_variant_id', $variantId)
                ->value('id');

            DB::table('goods_receipt_items')->updateOrInsert(
                ['goods_receipt_id' => $receiptId, 'product_variant_id' => $variantId],
                [
                    'purchase_order_item_id' => $poItemId,
                    'location_id' => $item['sku'] === 'SKU-HG-RX78-REVIVE' ? $locationId : $premiumKitLocationId,
                    'received_quantity' => $item['received_quantity'],
                    'unit_cost' => $item['unit_price'],
                    'note' => null,
                    'created_at' => $now,
                    'updated_at' => $now,
                ],
            );
        }
    }
}
