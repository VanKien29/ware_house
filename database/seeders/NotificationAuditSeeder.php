<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class NotificationAuditSeeder extends Seeder
{
    /**
     * Seed notifications and audit logs.
     */
    public function run(): void
    {
        $now = now();
        $managerId = DB::table('users')->where('email', 'manager@warehouse.test')->value('id');
        $purchaseId = DB::table('users')->where('email', 'purchase@warehouse.test')->value('id');
        $transferId = DB::table('stock_transfers')->where('transfer_number', 'TR-2026-0001')->value('id');
        $countId = DB::table('inventory_counts')->where('count_number', 'CNT-2026-0001')->value('id');

        foreach ([
            [
                'user_id' => $managerId,
                'type' => 'count_variance',
                'title' => 'Kiem ke co chenh lech',
                'body' => 'CNT-2026-0001 lech thieu 1 SKU-RK61-BLK.',
                'data' => ['route' => '/inventory-counts/CNT-2026-0001', 'count_id' => $countId],
            ],
            [
                'user_id' => $purchaseId,
                'type' => 'low_stock',
                'title' => 'Can xem lai ton toi thieu',
                'body' => 'Mot so SKU sap cham nguong reorder point.',
                'data' => ['route' => '/reports/low-stock'],
            ],
            [
                'user_id' => $managerId,
                'type' => 'transfer_pending',
                'title' => 'Transfer da hoan tat',
                'body' => 'TR-2026-0001 da nhan tai kho HN01.',
                'data' => ['route' => '/stock-transfers/TR-2026-0001', 'transfer_id' => $transferId],
            ],
        ] as $notification) {
            DB::table('notifications')->updateOrInsert(
                [
                    'user_id' => $notification['user_id'],
                    'type' => $notification['type'],
                    'title' => $notification['title'],
                ],
                [
                    'body' => $notification['body'],
                    'data' => json_encode($notification['data']),
                    'read_at' => null,
                    'created_at' => $now,
                    'updated_at' => $now,
                ],
            );
        }

        DB::table('audit_logs')->where('user_agent', 'warehouse-seeder')->delete();

        $logs = [
            [
                'actor_id' => $managerId,
                'action' => 'goods_receipt.posted',
                'target_type' => 'goods_receipts',
                'target_id' => DB::table('goods_receipts')->where('receipt_number', 'GR-2026-0001')->value('id'),
                'before_values' => ['status' => 'draft'],
                'after_values' => ['status' => 'posted'],
            ],
            [
                'actor_id' => $managerId,
                'action' => 'stock_transfer.completed',
                'target_type' => 'stock_transfers',
                'target_id' => $transferId,
                'before_values' => ['status' => 'in_transit'],
                'after_values' => ['status' => 'completed'],
            ],
            [
                'actor_id' => $managerId,
                'action' => 'inventory_count.reconciled',
                'target_type' => 'inventory_counts',
                'target_id' => $countId,
                'before_values' => ['status' => 'counting'],
                'after_values' => ['status' => 'reconciled'],
            ],
        ];

        foreach ($logs as $log) {
            DB::table('audit_logs')->insert([
                'actor_id' => $log['actor_id'],
                'action' => $log['action'],
                'target_type' => $log['target_type'],
                'target_id' => $log['target_id'],
                'before_values' => json_encode($log['before_values']),
                'after_values' => json_encode($log['after_values']),
                'ip_address' => '127.0.0.1',
                'user_agent' => 'warehouse-seeder',
                'created_at' => $now,
            ]);
        }
    }
}
