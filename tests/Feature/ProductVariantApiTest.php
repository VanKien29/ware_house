<?php

namespace Tests\Feature;

use App\Models\ModelKitManufacturer;
use App\Models\ModelKitSeries;
use App\Models\Product;
use App\Models\ProductCategory;
use App\Models\ProductVariant;
use App\Models\Unit;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class ProductVariantApiTest extends TestCase
{
    use RefreshDatabase;

    public function test_product_variant_routes_require_authentication(): void
    {
        $this->getJson('/api/product-variants')->assertUnauthorized();

        $this->postJson('/api/product-variants', [
            'product_id' => 1,
            'sku' => 'SKU-HG-RX78-REVIVE',
            'has_manual' => true,
            'has_decals' => true,
            'status' => 'active',
        ])->assertUnauthorized();
    }

    public function test_authenticated_user_can_list_product_variants_with_search_and_filters(): void
    {
        $token = $this->createToken();
        $product = $this->createProduct();
        $otherProduct = $this->createProduct([
            'kit_code' => 'ZGMF-X20A-MGEX',
            'name' => 'MGEX Strike Freedom Gundam',
            'slug' => 'mgex-strike-freedom-gundam',
            'grade' => 'MGEX',
            'scale' => '1/100',
        ]);

        ProductVariant::query()->create([
            'product_id' => $product->id,
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
        ]);
        ProductVariant::query()->create([
            'product_id' => $otherProduct->id,
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
            'status' => 'pre_order',
        ]);

        $this->withToken($token)
            ->getJson("/api/product-variants?search=RX78&product_id={$product->id}&status=active")
            ->assertOk()
            ->assertJsonFragment(['sku' => 'SKU-HG-RX78-REVIVE'])
            ->assertJsonMissing(['sku' => 'SKU-MGEX-SF']);
    }

    public function test_authenticated_user_can_create_product_variant(): void
    {
        $token = $this->createToken();
        $product = $this->createProduct();

        $response = $this->withToken($token)->postJson('/api/product-variants', [
            'product_id' => $product->id,
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
            'min_stock' => 5.5,
            'status' => 'active',
        ]);

        $response
            ->assertCreated()
            ->assertJsonPath('message', 'Tạo biến thể sản phẩm thành công')
            ->assertJsonPath('variant.sku', 'SKU-HG-RX78-REVIVE')
            ->assertJsonPath('variant.barcode', '893100000001')
            ->assertJsonPath('variant.variant_name', 'Standard box')
            ->assertJsonPath('variant.has_manual', true)
            ->assertJsonPath('variant.has_decals', true)
            ->assertJsonPath('variant.status', 'active')
            ->assertJsonPath('variant.product.name', 'HGUC RX-78-2 Gundam Revive');

        $this->assertDatabaseHas('product_variants', [
            'product_id' => $product->id,
            'sku' => 'SKU-HG-RX78-REVIVE',
            'barcode' => '893100000001',
            'variant_name' => 'Standard box',
            'status' => 'active',
        ]);
    }

    public function test_create_product_variant_validates_required_unique_foreign_key_and_numeric_fields(): void
    {
        $token = $this->createToken();
        $product = $this->createProduct();

        ProductVariant::query()->create([
            'product_id' => $product->id,
            'sku' => 'SKU-HG-RX78-REVIVE',
            'barcode' => '893100000001',
            'variant_name' => 'Standard box',
            'has_manual' => true,
            'has_decals' => true,
            'status' => 'active',
        ]);

        $response = $this->withToken($token)->postJson('/api/product-variants', [
            'product_id' => 999999,
            'sku' => 'SKU-HG-RX78-REVIVE',
            'barcode' => '893100000001',
            'variant_name' => str_repeat('A', 256),
            'edition' => str_repeat('B', 101),
            'box_condition' => str_repeat('C', 101),
            'item_condition' => str_repeat('D', 101),
            'has_manual' => 'not-bool',
            'has_decals' => 'not-bool',
            'purchase_price' => -1,
            'sale_price' => -1,
            'min_stock' => -1,
            'status' => 'invalid',
        ]);

        $response
            ->assertUnprocessable()
            ->assertJsonValidationErrors([
                'product_id',
                'sku',
                'barcode',
                'variant_name',
                'edition',
                'box_condition',
                'item_condition',
                'has_manual',
                'has_decals',
                'purchase_price',
                'sale_price',
                'min_stock',
                'status',
            ]);
    }

    public function test_authenticated_user_can_show_product_variant_with_product(): void
    {
        $product = $this->createProduct();
        $variant = ProductVariant::query()->create([
            'product_id' => $product->id,
            'sku' => 'SKU-HG-RX78-REVIVE',
            'barcode' => '893100000001',
            'variant_name' => 'Standard box',
            'has_manual' => true,
            'has_decals' => true,
            'status' => 'active',
        ]);

        $this->withToken($this->createToken())
            ->getJson("/api/product-variants/{$variant->id}")
            ->assertOk()
            ->assertJsonPath('sku', 'SKU-HG-RX78-REVIVE')
            ->assertJsonPath('barcode', '893100000001')
            ->assertJsonPath('variant_name', 'Standard box')
            ->assertJsonPath('product.name', 'HGUC RX-78-2 Gundam Revive');
    }

    public function test_show_product_variant_returns_not_found_for_missing_record(): void
    {
        $this->withToken($this->createToken())
            ->getJson('/api/product-variants/999999')
            ->assertNotFound()
            ->assertJsonPath('message', 'Biến thể sản phẩm không tồn tại');
    }

    public function test_authenticated_user_can_update_product_variant_and_keep_same_sku_and_barcode(): void
    {
        $token = $this->createToken();
        $product = $this->createProduct();
        $variant = ProductVariant::query()->create([
            'product_id' => $product->id,
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
        ]);

        $response = $this->withToken($token)->putJson("/api/product-variants/{$variant->id}", [
            'product_id' => $product->id,
            'sku' => 'SKU-HG-RX78-REVIVE',
            'barcode' => '893100000001',
            'variant_name' => 'Standard box - reprint',
            'edition' => 'reprint',
            'box_condition' => 'new',
            'item_condition' => 'sealed',
            'has_manual' => true,
            'has_decals' => false,
            'purchase_price' => 190000,
            'sale_price' => 340000,
            'min_stock' => 4.5,
            'status' => 'active',
        ]);

        $response
            ->assertOk()
            ->assertJsonPath('message', 'Cập nhật biến thể sản phẩm thành công')
            ->assertJsonPath('variant.sku', 'SKU-HG-RX78-REVIVE')
            ->assertJsonPath('variant.barcode', '893100000001')
            ->assertJsonPath('variant.variant_name', 'Standard box - reprint')
            ->assertJsonPath('variant.has_decals', false)
            ->assertJsonPath('variant.product.name', 'HGUC RX-78-2 Gundam Revive');
    }

    public function test_update_product_variant_validates_unique_sku_and_barcode(): void
    {
        $token = $this->createToken();
        $product = $this->createProduct();
        $variant = ProductVariant::query()->create([
            'product_id' => $product->id,
            'sku' => 'SKU-HG-RX78-REVIVE',
            'barcode' => '893100000001',
            'variant_name' => 'Standard box',
            'has_manual' => true,
            'has_decals' => true,
            'status' => 'active',
        ]);
        ProductVariant::query()->create([
            'product_id' => $product->id,
            'sku' => 'SKU-HG-RX78-REVIVE-REPRINT',
            'barcode' => '893100000002',
            'variant_name' => 'Standard box',
            'has_manual' => true,
            'has_decals' => true,
            'status' => 'active',
        ]);

        $this->withToken($token)
            ->putJson("/api/product-variants/{$variant->id}", [
                'product_id' => $product->id,
                'sku' => 'SKU-HG-RX78-REVIVE-REPRINT',
                'barcode' => '893100000002',
                'variant_name' => 'Standard box',
                'has_manual' => true,
                'has_decals' => true,
                'status' => 'active',
            ])
            ->assertUnprocessable()
            ->assertJsonValidationErrors(['sku', 'barcode']);
    }

    public function test_authenticated_user_can_delete_product_variant(): void
    {
        $token = $this->createToken();
        $product = $this->createProduct();
        $variant = ProductVariant::query()->create([
            'product_id' => $product->id,
            'sku' => 'SKU-HG-RX78-REVIVE',
            'barcode' => '893100000001',
            'variant_name' => 'Standard box',
            'has_manual' => true,
            'has_decals' => true,
            'status' => 'active',
        ]);

        $this->withToken($token)
            ->deleteJson("/api/product-variants/{$variant->id}")
            ->assertOk()
            ->assertJsonPath('message', 'Xóa biến thể sản phẩm thành công');

        $this->assertSoftDeleted('product_variants', ['id' => $variant->id]);

        $this->withToken($token)
            ->getJson("/api/product-variants/{$variant->id}")
            ->assertNotFound();
    }

    /**
     * @param  array<string, mixed>  $overrides
     */
    private function createProduct(array $overrides = []): Product
    {
        $category = ProductCategory::query()->firstOrCreate(
            ['slug' => $overrides['category_slug'] ?? 'gunpla'],
            [
                'name' => $overrides['category_name'] ?? 'Gunpla',
                'description' => 'Model kit Gundam lap rap.',
            ],
        );
        $unit = Unit::query()->firstOrCreate(
            ['code' => $overrides['unit_code'] ?? 'PCS'],
            [
                'name' => 'Cai',
                'precision' => 0,
            ],
        );
        $manufacturer = ModelKitManufacturer::query()->firstOrCreate(
            ['slug' => $overrides['manufacturer_slug'] ?? 'bandai-spirits'],
            [
                'name' => $overrides['manufacturer_name'] ?? 'Bandai Spirits',
                'country' => 'Japan',
            ],
        );
        $series = ModelKitSeries::query()->firstOrCreate(
            ['slug' => $overrides['series_slug'] ?? 'mobile-suit-gundam'],
            [
                'manufacturer_id' => $manufacturer->id,
                'name' => $overrides['series_name'] ?? 'Mobile Suit Gundam',
                'universe' => 'Universal Century',
            ],
        );

        return Product::query()->create([
            'category_id' => $category->id,
            'base_unit_id' => $unit->id,
            'manufacturer_id' => $manufacturer->id,
            'series_id' => $series->id,
            'kit_code' => $overrides['kit_code'] ?? 'RX-78-2-HGUC',
            'name' => $overrides['name'] ?? 'HGUC RX-78-2 Gundam Revive',
            'slug' => $overrides['slug'] ?? 'hguc-rx-78-2-gundam-revive',
            'grade' => $overrides['grade'] ?? 'HG',
            'scale' => $overrides['scale'] ?? '1/144',
            'status' => $overrides['status'] ?? 'active',
        ]);
    }

    private function createToken(): string
    {
        $user = User::factory()->create([
            'email' => fake()->unique()->safeEmail(),
            'password' => Hash::make('password123'),
            'role' => 'admin',
            'status' => 'active',
        ]);

        return $user->createToken('test-token')->plainTextToken;
    }
}
