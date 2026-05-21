<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class ProductCatalogSeeder extends Seeder
{
    /**
     * Seed units, categories, model-kit catalog, products, and variants.
     */
    public function run(): void
    {
        $now = now();

        foreach ([
            ['code' => 'PCS', 'name' => 'Cai', 'precision' => 0],
            ['code' => 'BOX', 'name' => 'Hop', 'precision' => 0],
            ['code' => 'SET', 'name' => 'Bo', 'precision' => 0],
        ] as $unit) {
            DB::table('units')->updateOrInsert(
                ['code' => $unit['code']],
                [...$unit, 'created_at' => $now, 'updated_at' => $now],
            );
        }

        DB::table('product_categories')->updateOrInsert(
            ['slug' => 'model-kits'],
            [
                'parent_id' => null,
                'name' => 'Model Kits',
                'description' => 'Cac bo mo hinh nhua lap rap va mecha model kit.',
                'created_at' => $now,
                'updated_at' => $now,
            ],
        );

        DB::table('product_categories')->updateOrInsert(
            ['slug' => 'gunpla'],
            [
                'parent_id' => DB::table('product_categories')->where('slug', 'model-kits')->value('id'),
                'name' => 'Gunpla',
                'description' => 'Model kit Gundam theo grade va scale.',
                'created_at' => $now,
                'updated_at' => $now,
            ],
        );

        DB::table('product_categories')->updateOrInsert(
            ['slug' => 'mecha-model-kits'],
            [
                'parent_id' => DB::table('product_categories')->where('slug', 'model-kits')->value('id'),
                'name' => 'Mecha Model Kits',
                'description' => 'Mo hinh robot/mecha ngoai dong Gundam.',
                'created_at' => $now,
                'updated_at' => $now,
            ],
        );

        DB::table('product_categories')->updateOrInsert(
            ['slug' => 'model-tools'],
            [
                'parent_id' => null,
                'name' => 'Dung cu va phu kien',
                'description' => 'Dung cu cat, decal, marker va phu kien cho model kit.',
                'created_at' => $now,
                'updated_at' => $now,
            ],
        );

        foreach ([
            [
                'name' => 'Bandai Spirits',
                'slug' => 'bandai-spirits',
                'country' => 'Japan',
                'website_url' => 'https://www.bandaispirits.co.jp',
                'description' => 'Hang san xuat Gunpla va figure model kit.',
            ],
            [
                'name' => 'Motor Nuclear',
                'slug' => 'motor-nuclear',
                'country' => 'China',
                'website_url' => null,
                'description' => 'Hang model kit mecha voi thiet ke nguyen ban.',
            ],
            [
                'name' => 'GSI Creos',
                'slug' => 'gsi-creos',
                'country' => 'Japan',
                'website_url' => null,
                'description' => 'Dung cu va vat tu ho tro lam mo hinh.',
            ],
        ] as $manufacturer) {
            DB::table('model_kit_manufacturers')->updateOrInsert(
                ['slug' => $manufacturer['slug']],
                [...$manufacturer, 'created_at' => $now, 'updated_at' => $now],
            );
        }

        $bandaiId = DB::table('model_kit_manufacturers')->where('slug', 'bandai-spirits')->value('id');
        $motorNuclearId = DB::table('model_kit_manufacturers')->where('slug', 'motor-nuclear')->value('id');
        $gsiCreosId = DB::table('model_kit_manufacturers')->where('slug', 'gsi-creos')->value('id');

        foreach ([
            [
                'manufacturer_id' => $bandaiId,
                'name' => 'Mobile Suit Gundam',
                'slug' => 'mobile-suit-gundam',
                'universe' => 'Universal Century',
                'description' => 'Cac kit dua tren dong Mobile Suit Gundam.',
            ],
            [
                'manufacturer_id' => $bandaiId,
                'name' => 'Gundam SEED',
                'slug' => 'gundam-seed',
                'universe' => 'Cosmic Era',
                'description' => 'Cac kit dua tren dong Gundam SEED.',
            ],
            [
                'manufacturer_id' => $motorNuclearId,
                'name' => 'Motor Nuclear Originals',
                'slug' => 'motor-nuclear-originals',
                'universe' => 'Original',
                'description' => 'Cac thiet ke mecha nguyen ban cua Motor Nuclear.',
            ],
            [
                'manufacturer_id' => $gsiCreosId,
                'name' => 'Modeling Tools',
                'slug' => 'modeling-tools',
                'universe' => null,
                'description' => 'Dung cu va vat tu ho tro lap rap, son, decal.',
            ],
        ] as $series) {
            DB::table('model_kit_series')->updateOrInsert(
                ['slug' => $series['slug']],
                [...$series, 'created_at' => $now, 'updated_at' => $now],
            );
        }

        $pcsId = DB::table('units')->where('code', 'PCS')->value('id');
        $gunplaId = DB::table('product_categories')->where('slug', 'gunpla')->value('id');
        $mechaId = DB::table('product_categories')->where('slug', 'mecha-model-kits')->value('id');
        $toolsId = DB::table('product_categories')->where('slug', 'model-tools')->value('id');
        $ucSeriesId = DB::table('model_kit_series')->where('slug', 'mobile-suit-gundam')->value('id');
        $seedSeriesId = DB::table('model_kit_series')->where('slug', 'gundam-seed')->value('id');
        $motorSeriesId = DB::table('model_kit_series')->where('slug', 'motor-nuclear-originals')->value('id');
        $toolSeriesId = DB::table('model_kit_series')->where('slug', 'modeling-tools')->value('id');

        $products = [
            [
                'slug' => 'hguc-rx-78-2-gundam-revive',
                'category_id' => $gunplaId,
                'base_unit_id' => $pcsId,
                'manufacturer_id' => $bandaiId,
                'series_id' => $ucSeriesId,
                'kit_code' => '5063364',
                'name' => 'HGUC RX-78-2 Gundam Revive',
                'grade' => 'HG',
                'scale' => '1/144',
                'material' => 'PS/PE',
                'runner_count' => 8,
                'release_date' => '2015-07-01',
                'box_art_url' => null,
                'description' => 'Gunpla co ban, de lap rap va phu hop nguoi moi bat dau.',
                'status' => 'active',
            ],
            [
                'slug' => 'mgex-strike-freedom-gundam',
                'category_id' => $gunplaId,
                'base_unit_id' => $pcsId,
                'manufacturer_id' => $bandaiId,
                'series_id' => $seedSeriesId,
                'kit_code' => 'BAN-MGEX-SF-001',
                'name' => 'MGEX Strike Freedom Gundam',
                'grade' => 'MGEX',
                'scale' => '1/100',
                'material' => 'PS/ABS/PET',
                'runner_count' => 24,
                'release_date' => '2022-11-01',
                'box_art_url' => null,
                'description' => 'Kit cao cap voi nhieu lop chi tiet va khung trong.',
                'status' => 'active',
            ],
            [
                'slug' => 'motor-nuclear-ao-bing-model-kit',
                'category_id' => $mechaId,
                'base_unit_id' => $pcsId,
                'manufacturer_id' => $motorNuclearId,
                'series_id' => $motorSeriesId,
                'kit_code' => 'MN-Q04',
                'name' => 'Motor Nuclear Ao Bing Model Kit',
                'grade' => 'Metal Frame',
                'scale' => '1/72',
                'material' => 'PS/ABS/Metal',
                'runner_count' => 18,
                'release_date' => null,
                'box_art_url' => null,
                'description' => 'Mecha model kit voi khung kim loai va thiet ke nguyen ban.',
                'status' => 'pre_order',
            ],
            [
                'slug' => 'gundam-marker-black',
                'category_id' => $toolsId,
                'base_unit_id' => $pcsId,
                'manufacturer_id' => $gsiCreosId,
                'series_id' => $toolSeriesId,
                'kit_code' => 'GM01-BLACK',
                'name' => 'Gundam Marker Black',
                'grade' => null,
                'scale' => null,
                'material' => 'Marker',
                'runner_count' => null,
                'release_date' => null,
                'box_art_url' => null,
                'description' => 'But marker den dung ke panel line va cham chi tiet.',
                'status' => 'active',
            ],
        ];

        foreach ($products as $product) {
            DB::table('products')->updateOrInsert(
                ['slug' => $product['slug']],
                [...$product, 'created_at' => $now, 'updated_at' => $now],
            );
        }

        $variants = [
            [
                'product_slug' => 'hguc-rx-78-2-gundam-revive',
                'sku' => 'SKU-HG-RX78-REVIVE',
                'barcode' => '893100000001',
                'variant_name' => 'Standard box',
                'edition' => 'standard',
                'box_condition' => 'new',
                'item_condition' => 'sealed',
                'has_manual' => true,
                'has_decals' => true,
                'purchase_price' => 180000,
                'sale_price' => 320000,
                'min_stock' => 5,
                'status' => 'active',
            ],
            [
                'product_slug' => 'mgex-strike-freedom-gundam',
                'sku' => 'SKU-MGEX-SF',
                'barcode' => '893100000002',
                'variant_name' => 'Standard box',
                'edition' => 'standard',
                'box_condition' => 'new',
                'item_condition' => 'sealed',
                'has_manual' => true,
                'has_decals' => true,
                'purchase_price' => 1800000,
                'sale_price' => 2350000,
                'min_stock' => 2,
                'status' => 'active',
            ],
            [
                'product_slug' => 'motor-nuclear-ao-bing-model-kit',
                'sku' => 'SKU-MN-AOBING',
                'barcode' => '893100000003',
                'variant_name' => 'Pre-order batch',
                'edition' => 'first_batch',
                'box_condition' => 'new',
                'item_condition' => 'sealed',
                'has_manual' => true,
                'has_decals' => true,
                'purchase_price' => 1050000,
                'sale_price' => 1490000,
                'min_stock' => 1,
                'status' => 'pre_order',
            ],
            [
                'product_slug' => 'gundam-marker-black',
                'sku' => 'SKU-GM01-BLACK',
                'barcode' => '893100000004',
                'variant_name' => 'Black marker',
                'edition' => 'standard',
                'box_condition' => 'new',
                'item_condition' => 'sealed',
                'has_manual' => false,
                'has_decals' => false,
                'purchase_price' => 55000,
                'sale_price' => 85000,
                'min_stock' => 10,
                'status' => 'active',
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
                    'created_at' => $now,
                    'updated_at' => $now,
                ],
            );
        }
    }
}
