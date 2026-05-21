<?php

namespace Tests\Feature;

use App\Models\ModelKitManufacturer;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class ModelKitManufacturerApiTest extends TestCase
{
    use RefreshDatabase;

    public function test_model_kit_manufacturer_routes_require_authentication(): void
    {
        $this->getJson('/api/model-kit-manufacturers')->assertUnauthorized();

        $this->postJson('/api/model-kit-manufacturers', [
            'name' => 'Bandai Spirits',
            'slug' => 'bandai-spirits',
        ])->assertUnauthorized();
    }

    public function test_authenticated_user_can_list_model_kit_manufacturers_with_search(): void
    {
        $token = $this->createToken();

        ModelKitManufacturer::query()->create([
            'name' => 'Bandai Spirits',
            'slug' => 'bandai-spirits',
            'country' => 'Japan',
        ]);
        ModelKitManufacturer::query()->create([
            'name' => 'Motor Nuclear',
            'slug' => 'motor-nuclear',
            'country' => 'China',
        ]);

        $this->withToken($token)
            ->getJson('/api/model-kit-manufacturers?search=Japan')
            ->assertOk()
            ->assertJsonFragment(['name' => 'Bandai Spirits'])
            ->assertJsonMissing(['name' => 'Motor Nuclear']);
    }

    public function test_authenticated_user_can_create_model_kit_manufacturer(): void
    {
        $token = $this->createToken();

        $response = $this->withToken($token)->postJson('/api/model-kit-manufacturers', [
            'name' => 'Bandai Spirits',
            'slug' => 'bandai-spirits',
            'country' => 'Japan',
            'website_url' => 'https://www.bandaispirits.co.jp',
            'description' => 'Hang san xuat Gunpla va model kit.',
        ]);

        $response
            ->assertCreated()
            ->assertJsonPath('message', 'Tạo nhà sản xuất thành công')
            ->assertJsonPath('manufacturer.name', 'Bandai Spirits')
            ->assertJsonPath('manufacturer.slug', 'bandai-spirits')
            ->assertJsonPath('manufacturer.country', 'Japan');

        $this->assertDatabaseHas('model_kit_manufacturers', [
            'name' => 'Bandai Spirits',
            'slug' => 'bandai-spirits',
            'country' => 'Japan',
            'website_url' => 'https://www.bandaispirits.co.jp',
        ]);
    }

    public function test_create_model_kit_manufacturer_validates_required_unique_and_url_fields(): void
    {
        $token = $this->createToken();

        ModelKitManufacturer::query()->create([
            'name' => 'Bandai Spirits',
            'slug' => 'bandai-spirits',
        ]);

        $response = $this->withToken($token)->postJson('/api/model-kit-manufacturers', [
            'name' => 'Bandai Spirits',
            'slug' => 'bandai-spirits',
            'country' => str_repeat('A', 256),
            'website_url' => 'not-a-url',
            'description' => 123,
        ]);

        $response
            ->assertUnprocessable()
            ->assertJsonValidationErrors(['name', 'slug', 'country', 'website_url', 'description']);
    }

    public function test_authenticated_user_can_show_model_kit_manufacturer(): void
    {
        $manufacturer = ModelKitManufacturer::query()->create([
            'name' => 'Bandai Spirits',
            'slug' => 'bandai-spirits',
            'country' => 'Japan',
        ]);

        $this->withToken($this->createToken())
            ->getJson("/api/model-kit-manufacturers/{$manufacturer->id}")
            ->assertOk()
            ->assertJsonPath('name', 'Bandai Spirits')
            ->assertJsonPath('slug', 'bandai-spirits')
            ->assertJsonPath('country', 'Japan');
    }

    public function test_show_model_kit_manufacturer_returns_not_found_for_missing_record(): void
    {
        $this->withToken($this->createToken())
            ->getJson('/api/model-kit-manufacturers/999999')
            ->assertNotFound()
            ->assertJsonPath('message', 'Nhà sản xuất không tồn tại');
    }

    public function test_authenticated_user_can_update_model_kit_manufacturer_and_keep_same_name_and_slug(): void
    {
        $token = $this->createToken();
        $manufacturer = ModelKitManufacturer::query()->create([
            'name' => 'Bandai Spirits',
            'slug' => 'bandai-spirits',
            'country' => 'Japan',
        ]);

        $response = $this->withToken($token)->putJson("/api/model-kit-manufacturers/{$manufacturer->id}", [
            'name' => 'Bandai Spirits',
            'slug' => 'bandai-spirits',
            'country' => 'Japan',
            'website_url' => 'https://www.bandaispirits.co.jp',
            'description' => 'Thong tin moi.',
        ]);

        $response
            ->assertOk()
            ->assertJsonPath('message', 'Cập nhật nhà sản xuất thành công')
            ->assertJsonPath('manufacturer.name', 'Bandai Spirits')
            ->assertJsonPath('manufacturer.website_url', 'https://www.bandaispirits.co.jp');
    }

    public function test_update_model_kit_manufacturer_validates_unique_name_and_slug(): void
    {
        $token = $this->createToken();
        $manufacturer = ModelKitManufacturer::query()->create([
            'name' => 'Bandai Spirits',
            'slug' => 'bandai-spirits',
        ]);
        ModelKitManufacturer::query()->create([
            'name' => 'Motor Nuclear',
            'slug' => 'motor-nuclear',
        ]);

        $this->withToken($token)
            ->putJson("/api/model-kit-manufacturers/{$manufacturer->id}", [
                'name' => 'Motor Nuclear',
                'slug' => 'motor-nuclear',
            ])
            ->assertUnprocessable()
            ->assertJsonValidationErrors(['name', 'slug']);
    }

    public function test_authenticated_user_can_delete_model_kit_manufacturer(): void
    {
        $token = $this->createToken();
        $manufacturer = ModelKitManufacturer::query()->create([
            'name' => 'Bandai Spirits',
            'slug' => 'bandai-spirits',
        ]);

        $this->withToken($token)
            ->deleteJson("/api/model-kit-manufacturers/{$manufacturer->id}")
            ->assertOk()
            ->assertJsonPath('message', 'Xóa nhà sản xuất thành công');

        $this->assertSoftDeleted('model_kit_manufacturers', ['id' => $manufacturer->id]);

        $this->withToken($token)
            ->getJson("/api/model-kit-manufacturers/{$manufacturer->id}")
            ->assertNotFound();
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
