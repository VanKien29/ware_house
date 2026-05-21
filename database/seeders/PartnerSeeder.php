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
                'code' => 'SUP-HOBBY',
                'name' => 'Hobby Link Viet Nam',
                'contact_name' => 'Nguyen Minh',
                'phone' => '0901000001',
                'email' => 'sales@hobby-link.test',
                'address' => 'Binh Thanh, TP. Ho Chi Minh',
                'tax_code' => '0312345678',
            ],
            [
                'code' => 'SUP-IMPORT',
                'name' => 'Mecha Import Co.',
                'contact_name' => 'Tran An',
                'phone' => '0901000002',
                'email' => 'contact@mecha-import.test',
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
                'name' => 'Hobby Store Alpha',
                'contact_name' => 'Le Bao',
                'phone' => '0902000001',
                'email' => 'alpha@hobby-store.test',
                'address' => 'Thu Duc, TP. Ho Chi Minh',
            ],
            [
                'code' => 'CUS-COLLECTOR',
                'name' => 'Collector Club',
                'contact_name' => 'Pham Linh',
                'phone' => '0902000002',
                'email' => 'linh@collector-club.test',
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
