<?php

namespace Tests\Feature;

use App\Models\ModelKitManufacturer;
use App\Models\ModelKitSeries;
use App\Models\Product;
use App\Models\ProductCategory;
use App\Models\Unit;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class ProductApiTest extends TestCase
{
    use RefreshDatabase;

    public function test_product_routes_require_authentication(): void
    {
        $this->getJson('/api/products')->assertUnauthorized();

        $this->postJson('/api/products', [
            'name' => 'HG RX-78-2 Gundam',
            'base_unit_id' => 1,
            'status' => 'active',
        ])->assertUnauthorized();
    }

    public function test_authenticated_user_can_list_products_with_search(): void
    {
        $token = $this->createToken();
        [$category, $unit, $manufacturer, $series] = $this->createProductDependencies();

        Product::query()->create([
            'category_id' => $category->id,
            'base_unit_id' => $unit->id,
            'manufacturer_id' => $manufacturer->id,
            'series_id' => $series->id,
            'kit_code' => 'RX-78-2-HGUC',
            'name' => 'HGUC RX-78-2 Gundam Revive',
            'slug' => 'hguc-rx-78-2-gundam-revive',
            'grade' => 'HG',
            'scale' => '1/144',
            'status' => 'active',
        ]);
        Product::query()->create([
            'category_id' => $category->id,
            'base_unit_id' => $unit->id,
            'manufacturer_id' => $manufacturer->id,
            'series_id' => $series->id,
            'kit_code' => 'ZGMF-X20A-MGEX',
            'name' => 'MGEX Strike Freedom Gundam',
            'slug' => 'mgex-strike-freedom-gundam',
            'grade' => 'MGEX',
            'scale' => '1/100',
            'status' => 'active',
        ]);

        $this->withToken($token)
            ->getJson('/api/products?search=RX-78')
            ->assertOk()
            ->assertJsonFragment(['name' => 'HGUC RX-78-2 Gundam Revive'])
            ->assertJsonMissing(['name' => 'MGEX Strike Freedom Gundam']);
    }

    public function test_authenticated_user_can_filter_products_by_model_kit_fields(): void
    {
        $token = $this->createToken();
        [$category, $unit, $manufacturer, $series] = $this->createProductDependencies();
        $motorNuclear = ModelKitManufacturer::query()->create([
            'name' => 'Motor Nuclear',
            'slug' => 'motor-nuclear',
            'country' => 'China',
        ]);

        Product::query()->create([
            'category_id' => $category->id,
            'base_unit_id' => $unit->id,
            'manufacturer_id' => $manufacturer->id,
            'series_id' => $series->id,
            'kit_code' => 'RX-78-2-HGUC',
            'name' => 'HGUC RX-78-2 Gundam Revive',
            'slug' => 'hguc-rx-78-2-gundam-revive',
            'grade' => 'HG',
            'scale' => '1/144',
            'status' => 'active',
        ]);
        Product::query()->create([
            'category_id' => $category->id,
            'base_unit_id' => $unit->id,
            'manufacturer_id' => $motorNuclear->id,
            'kit_code' => 'MN-Q04',
            'name' => 'Motor Nuclear Ao Bing Model Kit',
            'slug' => 'motor-nuclear-ao-bing-model-kit',
            'grade' => 'Metal Frame',
            'scale' => '1/72',
            'status' => 'pre_order',
        ]);

        $this->withToken($token)
            ->getJson("/api/products?manufacturer_id={$motorNuclear->id}&scale=1/72&status=pre_order")
            ->assertOk()
            ->assertJsonFragment(['name' => 'Motor Nuclear Ao Bing Model Kit'])
            ->assertJsonMissing(['name' => 'HGUC RX-78-2 Gundam Revive']);
    }

    public function test_authenticated_user_can_create_product(): void
    {
        $token = $this->createToken();
        [$category, $unit, $manufacturer, $series] = $this->createProductDependencies();

        $response = $this->withToken($token)->postJson('/api/products', [
            'category_id' => $category->id,
            'base_unit_id' => $unit->id,
            'manufacturer_id' => $manufacturer->id,
            'series_id' => $series->id,
            'kit_code' => '5063364',
            'name' => 'HGUC RX-78-2 Gundam Revive',
            'grade' => 'HG',
            'scale' => '1/144',
            'material' => 'PS/PE',
            'runner_count' => 8,
            'release_date' => '2015-07-01',
            'box_art_url' => 'https://example.test/rx-78-2.jpg',
            'description' => 'Model kit snap-fit cho nguoi moi bat dau.',
            'status' => 'active',
        ]);

        $response
            ->assertCreated()
            ->assertJsonPath('message', 'Tạo sản phẩm thành công')
            ->assertJsonPath('product.name', 'HGUC RX-78-2 Gundam Revive')
            ->assertJsonPath('product.slug', 'hguc-rx-78-2-gundam-revive')
            ->assertJsonPath('product.kit_code', '5063364')
            ->assertJsonPath('product.grade', 'HG')
            ->assertJsonPath('product.scale', '1/144')
            ->assertJsonPath('product.status', 'active')
            ->assertJsonPath('product.manufacturer.name', 'Bandai Spirits')
            ->assertJsonPath('product.series.name', 'Mobile Suit Gundam');

        $this->assertDatabaseHas('products', [
            'category_id' => $category->id,
            'base_unit_id' => $unit->id,
            'manufacturer_id' => $manufacturer->id,
            'series_id' => $series->id,
            'kit_code' => '5063364',
            'name' => 'HGUC RX-78-2 Gundam Revive',
            'slug' => 'hguc-rx-78-2-gundam-revive',
            'grade' => 'HG',
            'scale' => '1/144',
            'material' => 'PS/PE',
            'runner_count' => 8,
            'release_date' => '2015-07-01 00:00:00',
            'status' => 'active',
        ]);
    }

    public function test_create_product_validates_required_unique_and_foreign_key_fields(): void
    {
        $token = $this->createToken();
        [$category, $unit, $manufacturer, $series] = $this->createProductDependencies();

        Product::query()->create([
            'category_id' => $category->id,
            'base_unit_id' => $unit->id,
            'manufacturer_id' => $manufacturer->id,
            'series_id' => $series->id,
            'kit_code' => '5063364',
            'name' => 'HGUC RX-78-2 Gundam Revive',
            'slug' => 'hguc-rx-78-2-gundam-revive',
            'status' => 'active',
        ]);

        $response = $this->withToken($token)->postJson('/api/products', [
            'category_id' => 999999,
            'base_unit_id' => 999999,
            'manufacturer_id' => 999999,
            'series_id' => 999999,
            'kit_code' => '5063364',
            'name' => 'HGUC RX-78-2 Gundam Revive',
            'grade' => str_repeat('A', 51),
            'scale' => str_repeat('B', 51),
            'material' => str_repeat('C', 101),
            'runner_count' => -1,
            'release_date' => 'not-a-date',
            'box_art_url' => 'not-a-url',
            'description' => 123,
            'status' => 'invalid',
        ]);

        $response
            ->assertUnprocessable()
            ->assertJsonValidationErrors([
                'category_id',
                'base_unit_id',
                'manufacturer_id',
                'series_id',
                'kit_code',
                'name',
                'grade',
                'scale',
                'material',
                'runner_count',
                'release_date',
                'box_art_url',
                'description',
                'status',
            ]);
    }

    public function test_authenticated_user_can_show_product_with_relations(): void
    {
        $token = $this->createToken();
        [$category, $unit, $manufacturer, $series] = $this->createProductDependencies();
        $product = Product::query()->create([
            'category_id' => $category->id,
            'base_unit_id' => $unit->id,
            'manufacturer_id' => $manufacturer->id,
            'series_id' => $series->id,
            'kit_code' => '5063364',
            'name' => 'HGUC RX-78-2 Gundam Revive',
            'slug' => 'hguc-rx-78-2-gundam-revive',
            'grade' => 'HG',
            'scale' => '1/144',
            'status' => 'active',
        ]);

        $this->withToken($token)
            ->getJson("/api/products/{$product->id}")
            ->assertOk()
            ->assertJsonPath('name', 'HGUC RX-78-2 Gundam Revive')
            ->assertJsonPath('kit_code', '5063364')
            ->assertJsonPath('category.name', 'Gunpla')
            ->assertJsonPath('base_unit.code', 'PCS')
            ->assertJsonPath('manufacturer.name', 'Bandai Spirits')
            ->assertJsonPath('series.name', 'Mobile Suit Gundam');
    }

    public function test_show_product_returns_not_found_for_missing_product(): void
    {
        $this->withToken($this->createToken())
            ->getJson('/api/products/999999')
            ->assertNotFound()
            ->assertJsonPath('message', 'Sản phẩm không tồn tại');
    }

    public function test_authenticated_user_can_update_product_and_keep_same_name_and_kit_code(): void
    {
        $token = $this->createToken();
        [$category, $unit, $manufacturer, $series] = $this->createProductDependencies();
        $product = Product::query()->create([
            'category_id' => $category->id,
            'base_unit_id' => $unit->id,
            'manufacturer_id' => $manufacturer->id,
            'series_id' => $series->id,
            'kit_code' => '5063364',
            'name' => 'HGUC RX-78-2 Gundam Revive',
            'slug' => 'hguc-rx-78-2-gundam-revive',
            'grade' => 'HG',
            'scale' => '1/144',
            'description' => 'Mo ta cu.',
            'status' => 'active',
        ]);

        $response = $this->withToken($token)->putJson("/api/products/{$product->id}", [
            'category_id' => $category->id,
            'base_unit_id' => $unit->id,
            'manufacturer_id' => $manufacturer->id,
            'series_id' => $series->id,
            'kit_code' => '5063364',
            'name' => 'HGUC RX-78-2 Gundam Revive',
            'grade' => 'HG',
            'scale' => '1/144',
            'material' => 'PS/PE',
            'runner_count' => 9,
            'release_date' => '2015-07-01',
            'description' => 'Mo ta moi.',
            'status' => 'pre_order',
        ]);

        $response
            ->assertOk()
            ->assertJsonPath('message', 'Cập nhật sản phẩm thành công')
            ->assertJsonPath('product.name', 'HGUC RX-78-2 Gundam Revive')
            ->assertJsonPath('product.slug', 'hguc-rx-78-2-gundam-revive')
            ->assertJsonPath('product.kit_code', '5063364')
            ->assertJsonPath('product.runner_count', 9)
            ->assertJsonPath('product.status', 'pre_order');
    }

    public function test_update_product_validates_unique_name_and_kit_code(): void
    {
        $token = $this->createToken();
        [$category, $unit, $manufacturer, $series] = $this->createProductDependencies();
        $product = Product::query()->create([
            'category_id' => $category->id,
            'base_unit_id' => $unit->id,
            'manufacturer_id' => $manufacturer->id,
            'series_id' => $series->id,
            'kit_code' => '5063364',
            'name' => 'HGUC RX-78-2 Gundam Revive',
            'slug' => 'hguc-rx-78-2-gundam-revive',
            'status' => 'active',
        ]);
        Product::query()->create([
            'category_id' => $category->id,
            'base_unit_id' => $unit->id,
            'manufacturer_id' => $manufacturer->id,
            'series_id' => $series->id,
            'kit_code' => 'ZGMF-X20A-MGEX',
            'name' => 'MGEX Strike Freedom Gundam',
            'slug' => 'mgex-strike-freedom-gundam',
            'status' => 'active',
        ]);

        $this->withToken($token)
            ->putJson("/api/products/{$product->id}", [
                'category_id' => $category->id,
                'base_unit_id' => $unit->id,
                'manufacturer_id' => $manufacturer->id,
                'series_id' => $series->id,
                'kit_code' => 'ZGMF-X20A-MGEX',
                'name' => 'MGEX Strike Freedom Gundam',
                'status' => 'active',
            ])
            ->assertUnprocessable()
            ->assertJsonValidationErrors(['name', 'kit_code']);
    }

    public function test_authenticated_user_can_delete_product(): void
    {
        $token = $this->createToken();
        [$category, $unit, $manufacturer, $series] = $this->createProductDependencies();
        $product = Product::query()->create([
            'category_id' => $category->id,
            'base_unit_id' => $unit->id,
            'manufacturer_id' => $manufacturer->id,
            'series_id' => $series->id,
            'kit_code' => '5063364',
            'name' => 'HGUC RX-78-2 Gundam Revive',
            'slug' => 'hguc-rx-78-2-gundam-revive',
            'status' => 'active',
        ]);

        $this->withToken($token)
            ->deleteJson("/api/products/{$product->id}")
            ->assertOk()
            ->assertJsonPath('message', 'Xóa sản phẩm thành công');

        $this->assertSoftDeleted('products', ['id' => $product->id]);

        $this->withToken($token)
            ->getJson("/api/products/{$product->id}")
            ->assertNotFound();
    }

    /**
     * @return array{ProductCategory, Unit, ModelKitManufacturer, ModelKitSeries}
     */
    private function createProductDependencies(): array
    {
        $category = ProductCategory::query()->create([
            'name' => 'Gunpla',
            'slug' => 'gunpla',
            'description' => 'Model kit Gundam lap rap.',
        ]);
        $unit = Unit::query()->create([
            'code' => 'PCS',
            'name' => 'Cai',
            'precision' => 0,
        ]);
        $manufacturer = ModelKitManufacturer::query()->create([
            'name' => 'Bandai Spirits',
            'slug' => 'bandai-spirits',
            'country' => 'Japan',
        ]);
        $series = ModelKitSeries::query()->create([
            'manufacturer_id' => $manufacturer->id,
            'name' => 'Mobile Suit Gundam',
            'slug' => 'mobile-suit-gundam',
            'universe' => 'Universal Century',
        ]);

        return [$category, $unit, $manufacturer, $series];
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
