<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class SalesSeeder extends Seeder
{
    /**
     * Seed sales order demo data.
     */
    public function run(): void
    {
        $now = now();
        $customerId = DB::table('customers')->where('code', 'CUS-RETAIL')->value('id');
        $warehouseId = DB::table('warehouses')->where('code', 'HCM01')->value('id');
        $salesId = DB::table('users')->where('email', 'sales@warehouse.test')->value('id');
        $managerId = DB::table('users')->where('email', 'manager@warehouse.test')->value('id');

        DB::table('sales_orders')->updateOrInsert(
            ['so_number' => 'SO-2026-0001'],
            [
                'customer_id' => $customerId,
                'warehouse_id' => $warehouseId,
                'status' => 'issued',
                'order_date' => '2026-05-07',
                'required_date' => '2026-05-08',
                'note' => 'Don xuat hang mau cho cua hang retail.',
                'created_by' => $salesId,
                'confirmed_by' => $managerId,
                'confirmed_at' => '2026-05-07 15:00:00',
                'created_at' => $now,
                'updated_at' => $now,
            ],
        );

        $salesOrderId = DB::table('sales_orders')->where('so_number', 'SO-2026-0001')->value('id');
        $items = [
            ['sku' => 'SKU-RK61-BLK', 'ordered_quantity' => 4, 'issued_quantity' => 4, 'unit_price' => 790000],
            ['sku' => 'SKU-MX-RED', 'ordered_quantity' => 25, 'issued_quantity' => 25, 'unit_price' => 7000],
        ];

        foreach ($items as $item) {
            $variantId = DB::table('product_variants')->where('sku', $item['sku'])->value('id');

            DB::table('sales_order_items')->updateOrInsert(
                ['sales_order_id' => $salesOrderId, 'product_variant_id' => $variantId],
                [
                    'ordered_quantity' => $item['ordered_quantity'],
                    'issued_quantity' => $item['issued_quantity'],
                    'unit_price' => $item['unit_price'],
                    'note' => null,
                    'created_at' => $now,
                    'updated_at' => $now,
                ],
            );
        }
    }
}
