<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class PartnerSeeder extends Seeder
{
    /**
     * Seed suppliers and customers.
     */
    public function run(): void
    {
        $now = now();

        $suppliers = [
            [
                'code' => 'SUP-TECH',
                'name' => 'Tech Supply Viet Nam',
                'contact_name' => 'Nguyen Minh',
                'phone' => '0901000001',
                'email' => 'sales@tech-supply.test',
                'address' => 'Binh Thanh, TP. Ho Chi Minh',
                'tax_code' => '0312345678',
            ],
            [
                'code' => 'SUP-OFFICE',
                'name' => 'Office Gear Co.',
                'contact_name' => 'Tran An',
                'phone' => '0901000002',
                'email' => 'contact@office-gear.test',
                'address' => 'Hai Ba Trung, Ha Noi',
                'tax_code' => '0109876543',
            ],
        ];

        foreach ($suppliers as $supplier) {
            DB::table('suppliers')->updateOrInsert(
                ['code' => $supplier['code']],
                [...$supplier, 'created_at' => $now, 'updated_at' => $now],
            );
        }

        $customers = [
            [
                'code' => 'CUS-RETAIL',
                'name' => 'Retail Store Alpha',
                'contact_name' => 'Le Bao',
                'phone' => '0902000001',
                'email' => 'alpha@retail.test',
                'address' => 'Thu Duc, TP. Ho Chi Minh',
            ],
            [
                'code' => 'CUS-B2B',
                'name' => 'B2B Office Client',
                'contact_name' => 'Pham Linh',
                'phone' => '0902000002',
                'email' => 'linh@b2b-office.test',
                'address' => 'Nam Tu Liem, Ha Noi',
            ],
        ];

        foreach ($customers as $customer) {
            DB::table('customers')->updateOrInsert(
                ['code' => $customer['code']],
                [...$customer, 'created_at' => $now, 'updated_at' => $now],
            );
        }
    }
}
