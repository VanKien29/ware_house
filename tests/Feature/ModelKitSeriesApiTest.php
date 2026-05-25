<?php

namespace Tests\Feature;

use App\Models\ModelKitManufacturer;
use App\Models\ModelKitSeries;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class ModelKitSeriesApiTest extends TestCase
{
    use RefreshDatabase;

    public function test_model_kit_series_routes_require_authentication(): void
    {
        $this->getJson('/api/model-kit-series')->assertUnauthorized();

        $this->postJson('/api/model-kit-series', [
            'manufacturer_id' => 1,
            'name' => 'Mobile Suit Gundam',
        ])->assertUnauthorized();
    }

    public function test_authenticated_user_can_list_model_kit_series_with_search(): void
    {
        $token = $this->createToken();
        $manufacturer = $this->createManufacturer();

        ModelKitSeries::query()->create([
            'manufacturer_id' => $manufacturer->id,
            'name' => 'Mobile Suit Gundam',
            'slug' => 'mobile-suit-gundam',
            'universe' => 'Universal Century',
        ]);
        ModelKitSeries::query()->create([
            'manufacturer_id' => $manufacturer->id,
            'name' => 'Gundam SEED',
            'slug' => 'gundam-seed',
            'universe' => 'Cosmic Era',
        ]);

        $this->withToken($token)
            ->getJson('/api/model-kit-series?search=Universal')
            ->assertOk()
            ->assertJsonFragment(['name' => 'Mobile Suit Gundam'])
            ->assertJsonMissing(['name' => 'Gundam SEED']);
    }

    public function test_authenticated_user_can_create_model_kit_series(): void
    {
        $token = $this->createToken();
        $manufacturer = $this->createManufacturer();

        $response = $this->withToken($token)->postJson('/api/model-kit-series', [
            'manufacturer_id' => $manufacturer->id,
            'name' => 'Mobile Suit Gundam',
            'universe' => 'Universal Century',
            'description' => 'Dong Gunpla chinh cua Bandai.',
        ]);

        $response
            ->assertCreated()
            ->assertJsonPath('message', 'Tạo series thành công')
            ->assertJsonPath('series.name', 'Mobile Suit Gundam')
            ->assertJsonPath('series.slug', 'mobile-suit-gundam')
            ->assertJsonPath('series.universe', 'Universal Century')
            ->assertJsonPath('series.manufacturer.name', 'Bandai Spirits');

        $this->assertDatabaseHas('model_kit_series', [
            'manufacturer_id' => $manufacturer->id,
            'name' => 'Mobile Suit Gundam',
            'slug' => 'mobile-suit-gundam',
            'universe' => 'Universal Century',
        ]);
    }

    public function test_create_model_kit_series_validates_required_unique_and_foreign_key_fields(): void
    {
        $token = $this->createToken();
        $manufacturer = $this->createManufacturer();

        ModelKitSeries::query()->create([
            'manufacturer_id' => $manufacturer->id,
            'name' => 'Mobile Suit Gundam',
            'slug' => 'mobile-suit-gundam',
            'universe' => 'Universal Century',
        ]);

        $response = $this->withToken($token)->postJson('/api/model-kit-series', [
            'manufacturer_id' => 999999,
            'name' => 'Mobile Suit Gundam',
            'universe' => str_repeat('A', 256),
            'description' => 123,
        ]);

        $response
            ->assertUnprocessable()
            ->assertJsonValidationErrors(['manufacturer_id', 'name', 'universe', 'description']);
    }

    public function test_authenticated_user_can_show_model_kit_series_with_manufacturer(): void
    {
        $manufacturer = $this->createManufacturer();
        $series = ModelKitSeries::query()->create([
            'manufacturer_id' => $manufacturer->id,
            'name' => 'Mobile Suit Gundam',
            'slug' => 'mobile-suit-gundam',
            'universe' => 'Universal Century',
        ]);

        $this->withToken($this->createToken())
            ->getJson("/api/model-kit-series/{$series->id}")
            ->assertOk()
            ->assertJsonPath('name', 'Mobile Suit Gundam')
            ->assertJsonPath('slug', 'mobile-suit-gundam')
            ->assertJsonPath('universe', 'Universal Century')
            ->assertJsonPath('manufacturer.name', 'Bandai Spirits');
    }

    public function test_show_model_kit_series_returns_not_found_for_missing_record(): void
    {
        $this->withToken($this->createToken())
            ->getJson('/api/model-kit-series/999999')
            ->assertNotFound()
            ->assertJsonPath('message', 'Series không tồn tại');
    }

    public function test_authenticated_user_can_update_model_kit_series_and_keep_same_name(): void
    {
        $token = $this->createToken();
        $manufacturer = $this->createManufacturer();
        $series = ModelKitSeries::query()->create([
            'manufacturer_id' => $manufacturer->id,
            'name' => 'Mobile Suit Gundam',
            'slug' => 'mobile-suit-gundam',
            'universe' => 'Universal Century',
            'description' => 'Mo ta cu.',
        ]);

        $response = $this->withToken($token)->putJson("/api/model-kit-series/{$series->id}", [
            'manufacturer_id' => $manufacturer->id,
            'name' => 'Mobile Suit Gundam',
            'universe' => 'Universal Century',
            'description' => 'Mo ta moi.',
        ]);

        $response
            ->assertOk()
            ->assertJsonPath('message', 'Cập nhật series thành công')
            ->assertJsonPath('series.name', 'Mobile Suit Gundam')
            ->assertJsonPath('series.slug', 'mobile-suit-gundam')
            ->assertJsonPath('series.description', 'Mo ta moi.')
            ->assertJsonPath('series.manufacturer.name', 'Bandai Spirits');
    }

    public function test_update_model_kit_series_validates_unique_name(): void
    {
        $token = $this->createToken();
        $manufacturer = $this->createManufacturer();
        $series = ModelKitSeries::query()->create([
            'manufacturer_id' => $manufacturer->id,
            'name' => 'Mobile Suit Gundam',
            'slug' => 'mobile-suit-gundam',
        ]);
        ModelKitSeries::query()->create([
            'manufacturer_id' => $manufacturer->id,
            'name' => 'Gundam SEED',
            'slug' => 'gundam-seed',
        ]);

        $this->withToken($token)
            ->putJson("/api/model-kit-series/{$series->id}", [
                'manufacturer_id' => $manufacturer->id,
                'name' => 'Gundam SEED',
            ])
            ->assertUnprocessable()
            ->assertJsonValidationErrors(['name']);
    }

    public function test_authenticated_user_can_delete_model_kit_series(): void
    {
        $token = $this->createToken();
        $manufacturer = $this->createManufacturer();
        $series = ModelKitSeries::query()->create([
            'manufacturer_id' => $manufacturer->id,
            'name' => 'Mobile Suit Gundam',
            'slug' => 'mobile-suit-gundam',
        ]);

        $this->withToken($token)
            ->deleteJson("/api/model-kit-series/{$series->id}")
            ->assertOk()
            ->assertJsonPath('message', 'Xóa series thành công');

        $this->assertSoftDeleted('model_kit_series', ['id' => $series->id]);

        $this->withToken($token)
            ->getJson("/api/model-kit-series/{$series->id}")
            ->assertNotFound();
    }

    private function createManufacturer(): ModelKitManufacturer
    {
        return ModelKitManufacturer::query()->create([
            'name' => 'Bandai Spirits',
            'slug' => 'bandai-spirits',
            'country' => 'Japan',
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
