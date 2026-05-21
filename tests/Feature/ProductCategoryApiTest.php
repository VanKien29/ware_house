<?php

namespace Tests\Feature;

use App\Models\ProductCategory;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class ProductCategoryApiTest extends TestCase
{
    use RefreshDatabase;

    public function test_product_category_routes_require_authentication(): void
    {
        $this->getJson('/api/product-categories')->assertUnauthorized();

        $this->postJson('/api/product-categories', [
            'name' => 'Thiet bi dien tu',
            'description' => 'Hang dien tu va phu kien.',
        ])->assertUnauthorized();
    }

    public function test_authenticated_user_can_list_product_categories(): void
    {
        $token = $this->createToken();

        ProductCategory::query()->create([
            'name' => 'Thiet bi dien tu',
            'slug' => 'thiet-bi-dien-tu',
            'description' => 'Hang dien tu va phu kien.',
        ]);
        ProductCategory::query()->create([
            'name' => 'Do dung van phong',
            'slug' => 'do-dung-van-phong',
            'description' => 'Do dung van phong.',
        ]);

        $this->withToken($token)
            ->getJson('/api/product-categories')
            ->assertOk()
            ->assertJsonFragment(['name' => 'Thiet bi dien tu', 'slug' => 'thiet-bi-dien-tu'])
            ->assertJsonFragment(['name' => 'Do dung van phong', 'slug' => 'do-dung-van-phong']);
    }

    public function test_authenticated_user_can_create_product_category(): void
    {
        $token = $this->createToken();

        $response = $this->withToken($token)->postJson('/api/product-categories', [
            'name' => 'Thiet bi dien tu',
            'description' => 'Hang dien tu va phu kien.',
        ]);

        $response
            ->assertCreated()
            ->assertJsonPath('message', 'Tạo danh mục sản phẩm thành công')
            ->assertJsonPath('product_category.name', 'Thiet bi dien tu')
            ->assertJsonPath('product_category.slug', 'thiet-bi-dien-tu');

        $this->assertDatabaseHas('product_categories', [
            'name' => 'Thiet bi dien tu',
            'slug' => 'thiet-bi-dien-tu',
        ]);
    }

    public function test_create_product_category_validates_name(): void
    {
        $token = $this->createToken();

        ProductCategory::query()->create([
            'name' => 'Thiet bi dien tu',
            'slug' => 'thiet-bi-dien-tu',
        ]);

        $this->withToken($token)
            ->postJson('/api/product-categories', [
                'name' => 'Thiet bi dien tu',
                'description' => 123,
            ])
            ->assertUnprocessable()
            ->assertJsonValidationErrors(['name', 'description']);
    }

    public function test_authenticated_user_can_show_product_category(): void
    {
        $token = $this->createToken();
        $category = ProductCategory::query()->create([
            'name' => 'Phu kien',
            'slug' => 'phu-kien',
            'description' => 'Phu kien ban kem.',
        ]);

        $this->withToken($token)
            ->getJson("/api/product-categories/{$category->id}")
            ->assertOk()
            ->assertJsonPath('name', 'Phu kien')
            ->assertJsonPath('slug', 'phu-kien');
    }

    public function test_authenticated_user_can_update_product_category_and_keep_same_name(): void
    {
        $token = $this->createToken();
        $category = ProductCategory::query()->create([
            'name' => 'Phu kien',
            'slug' => 'phu-kien',
            'description' => 'Mo ta cu.',
        ]);

        $response = $this->withToken($token)->putJson("/api/product-categories/{$category->id}", [
            'name' => 'Phu kien',
            'description' => 'Mo ta moi.',
        ]);

        $response
            ->assertOk()
            ->assertJsonPath('message', 'Cập nhật danh mục sản phẩm thành công')
            ->assertJsonPath('product_category.name', 'Phu kien')
            ->assertJsonPath('product_category.slug', 'phu-kien')
            ->assertJsonPath('product_category.description', 'Mo ta moi.');
    }

    public function test_update_product_category_validates_unique_name(): void
    {
        $token = $this->createToken();
        $category = ProductCategory::query()->create([
            'name' => 'Phu kien',
            'slug' => 'phu-kien',
        ]);
        ProductCategory::query()->create([
            'name' => 'Do dung van phong',
            'slug' => 'do-dung-van-phong',
        ]);

        $this->withToken($token)
            ->putJson("/api/product-categories/{$category->id}", [
                'name' => 'Do dung van phong',
            ])
            ->assertUnprocessable()
            ->assertJsonValidationErrors(['name']);
    }

    public function test_authenticated_user_can_delete_product_category(): void
    {
        $token = $this->createToken();
        $category = ProductCategory::query()->create([
            'name' => 'Phu kien',
            'slug' => 'phu-kien',
        ]);

        $this->withToken($token)
            ->deleteJson("/api/product-categories/{$category->id}")
            ->assertOk()
            ->assertJsonPath('message', 'Xóa danh mục sản phẩm thành công');

        $this->assertDatabaseMissing('product_categories', ['id' => $category->id]);
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
