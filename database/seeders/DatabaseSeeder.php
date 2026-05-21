<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $this->call([
            UserSeeder::class,
            WarehouseSeeder::class,
            PartnerSeeder::class,
            ProductCatalogSeeder::class,
            PurchasingSeeder::class,
            SalesSeeder::class,
            StockTransferSeeder::class,
            AdjustmentCountingSeeder::class,
            InventorySeeder::class,
            NotificationAuditSeeder::class,
        ]);
    }
}
