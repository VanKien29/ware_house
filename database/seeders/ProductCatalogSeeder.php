<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class ProductCatalogSeeder extends Seeder
{
    /**
     * Seed units, categories, products, and variants.
     */
    public function run(): void
    {
        $now = now();

        foreach ([
            ['code' => 'PCS', 'name' => 'Cai', 'precision' => 0],
            ['code' => 'BOX', 'name' => 'Hop', 'precision' => 0],
            ['code' => 'KG', 'name' => 'Kilogram', 'precision' => 3],
        ] as $unit) {
            DB::table('units')->updateOrInsert(
                ['code' => $unit['code']],
                [...$unit, 'created_at' => $now, 'updated_at' => $now],
            );
        }

        DB::table('product_categories')->updateOrInsert(
            ['slug' => 'electronics'],
            [
                'parent_id' => null,
                'name' => 'Thiet bi dien tu',
                'description' => 'Nhom san pham dien tu va phu kien cong nghe.',
                'created_at' => $now,
                'updated_at' => $now,
            ],
        );

        DB::table('product_categories')->updateOrInsert(
            ['slug' => 'accessories'],
            [
                'parent_id' => DB::table('product_categories')->where('slug', 'electronics')->value('id'),
                'name' => 'Phu kien',
                'description' => 'Phu kien thay the va phu kien ban kem.',
                'created_at' => $now,
                'updated_at' => $now,
            ],
        );

        DB::table('product_categories')->updateOrInsert(
            ['slug' => 'office-supplies'],
            [
                'parent_id' => null,
                'name' => 'Do dung van phong',
                'description' => 'Do dung va thiet bi ho tro van phong.',
                'created_at' => $now,
                'updated_at' => $now,
            ],
        );

        $pcsId = DB::table('units')->where('code', 'PCS')->value('id');
        $electronicsId = DB::table('product_categories')->where('slug', 'electronics')->value('id');
        $accessoriesId = DB::table('product_categories')->where('slug', 'accessories')->value('id');
        $officeId = DB::table('product_categories')->where('slug', 'office-supplies')->value('id');

        $products = [
            [
                'slug' => 'ban-phim-co-rk61',
                'category_id' => $electronicsId,
                'base_unit_id' => $pcsId,
                'name' => 'Ban phim co Royal Kludge RK61',
                'brand' => 'Royal Kludge',
                'description' => 'Ban phim co 61 phim dung cho hoc tap va lam viec.',
            ],
            [
                'slug' => 'switch-co-red',
                'category_id' => $accessoriesId,
                'base_unit_id' => $pcsId,
                'name' => 'Switch co Red',
                'brand' => 'Gateron',
                'description' => 'Switch co tuyen tinh dung thay the ban phim.',
            ],
            [
                'slug' => 'cap-usb-c-1m',
                'category_id' => $accessoriesId,
                'base_unit_id' => $pcsId,
                'name' => 'Cap USB-C 1m',
                'brand' => 'Anker',
                'description' => 'Cap sac va truyen du lieu USB-C dai 1m.',
            ],
            [
                'slug' => 'gia-do-laptop-nhom',
                'category_id' => $officeId,
                'base_unit_id' => $pcsId,
                'name' => 'Gia do laptop nhom',
                'brand' => 'OEM',
                'description' => 'Gia do laptop gap gon bang nhom.',
            ],
        ];

        foreach ($products as $product) {
            DB::table('products')->updateOrInsert(
                ['slug' => $product['slug']],
                [...$product, 'status' => 'active', 'created_at' => $now, 'updated_at' => $now],
            );
        }

        $variants = [
            [
                'product_slug' => 'ban-phim-co-rk61',
                'sku' => 'SKU-RK61-BLK',
                'barcode' => '893000000001',
                'variant_name' => 'Mau den',
                'purchase_price' => 620000,
                'sale_price' => 790000,
                'min_stock' => 10,
            ],
            [
                'product_slug' => 'switch-co-red',
                'sku' => 'SKU-MX-RED',
                'barcode' => '893000000002',
                'variant_name' => 'Red switch',
                'purchase_price' => 4500,
                'sale_price' => 7000,
                'min_stock' => 50,
            ],
            [
                'product_slug' => 'cap-usb-c-1m',
                'sku' => 'SKU-USB-C-1M',
                'barcode' => '893000000003',
                'variant_name' => '1 met',
                'purchase_price' => 55000,
                'sale_price' => 89000,
                'min_stock' => 20,
            ],
            [
                'product_slug' => 'gia-do-laptop-nhom',
                'sku' => 'SKU-LAPTOP-STAND',
                'barcode' => '893000000004',
                'variant_name' => 'Mau bac',
                'purchase_price' => 120000,
                'sale_price' => 189000,
                'min_stock' => 8,
            ],
        ];

        foreach ($variants as $variant) {
            $productId = DB::table('products')->where('slug', $variant['product_slug'])->value('id');
            unset($variant['product_slug']);

            DB::table('product_variants')->updateOrInsert(
                ['sku' => $variant['sku']],
                [
                    ...$variant,
                    'product_id' => $productId,
                    'status' => 'active',
                    'created_at' => $now,
                    'updated_at' => $now,
                ],
            );
        }
    }
}
