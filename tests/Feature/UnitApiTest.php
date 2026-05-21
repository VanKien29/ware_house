<?php

namespace Tests\Feature;

use App\Models\Unit;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class UnitApiTest extends TestCase
{
    use RefreshDatabase;

    public function test_unit_routes_require_authentication(): void
    {
        $this->getJson('/api/units')->assertUnauthorized();

        $this->postJson('/api/units', [
            'code' => 'PCS',
            'name' => 'Cai',
            'precision' => 0,
        ])->assertUnauthorized();
    }

    public function test_authenticated_user_can_list_units(): void
    {
        $token = $this->createToken();

        Unit::query()->create(['code' => 'PCS', 'name' => 'Cai', 'precision' => 0]);
        Unit::query()->create(['code' => 'KG', 'name' => 'Kilogram', 'precision' => 3]);

        $this->withToken($token)
            ->getJson('/api/units')
            ->assertOk()
            ->assertJsonFragment(['code' => 'PCS', 'name' => 'Cai', 'precision' => 0])
            ->assertJsonFragment(['code' => 'KG', 'name' => 'Kilogram', 'precision' => 3]);
    }

    public function test_authenticated_user_can_create_unit(): void
    {
        $token = $this->createToken();

        $response = $this->withToken($token)->postJson('/api/units', [
            'code' => 'BOX',
            'name' => 'Hop',
            'precision' => 0,
        ]);

        $response
            ->assertCreated()
            ->assertJsonPath('message', 'Tạo đơn vị thành công')
            ->assertJsonPath('unit.code', 'BOX')
            ->assertJsonPath('unit.name', 'Hop')
            ->assertJsonPath('unit.precision', 0);

        $this->assertDatabaseHas('units', [
            'code' => 'BOX',
            'name' => 'Hop',
            'precision' => 0,
        ]);
    }

    public function test_create_unit_validates_required_unique_and_precision_fields(): void
    {
        $token = $this->createToken();

        Unit::query()->create(['code' => 'PCS', 'name' => 'Cai', 'precision' => 0]);

        $response = $this->withToken($token)->postJson('/api/units', [
            'code' => 'PCS',
            'name' => 'Cai',
            'precision' => 7,
        ]);

        $response
            ->assertUnprocessable()
            ->assertJsonValidationErrors(['code', 'name', 'precision']);
    }

    public function test_authenticated_user_can_show_unit(): void
    {
        $token = $this->createToken();
        $unit = Unit::query()->create(['code' => 'KG', 'name' => 'Kilogram', 'precision' => 3]);

        $this->withToken($token)
            ->getJson("/api/units/{$unit->id}")
            ->assertOk()
            ->assertJsonPath('code', 'KG')
            ->assertJsonPath('name', 'Kilogram')
            ->assertJsonPath('precision', 3);
    }

    public function test_show_unit_returns_not_found_for_missing_unit(): void
    {
        $this->withToken($this->createToken())
            ->getJson('/api/units/999999')
            ->assertNotFound()
            ->assertJsonPath('message', 'Đơn vị không tồn tại');
    }

    public function test_authenticated_user_can_update_unit_and_keep_same_code_name(): void
    {
        $token = $this->createToken();
        $unit = Unit::query()->create(['code' => 'KG', 'name' => 'Kilogram', 'precision' => 3]);

        $response = $this->withToken($token)->putJson("/api/units/{$unit->id}", [
            'code' => 'KG',
            'name' => 'Kilogram',
            'precision' => 2,
        ]);

        $response
            ->assertOk()
            ->assertJsonPath('message', 'Cập nhật đơn vị thành công')
            ->assertJsonPath('unit.code', 'KG')
            ->assertJsonPath('unit.name', 'Kilogram')
            ->assertJsonPath('unit.precision', 2);

        $this->assertDatabaseHas('units', [
            'id' => $unit->id,
            'code' => 'KG',
            'name' => 'Kilogram',
            'precision' => 2,
        ]);
    }

    public function test_update_unit_validates_unique_code_and_name(): void
    {
        $token = $this->createToken();
        $unit = Unit::query()->create(['code' => 'PCS', 'name' => 'Cai', 'precision' => 0]);
        Unit::query()->create(['code' => 'KG', 'name' => 'Kilogram', 'precision' => 3]);

        $this->withToken($token)
            ->putJson("/api/units/{$unit->id}", [
                'code' => 'KG',
                'name' => 'Kilogram',
                'precision' => 0,
            ])
            ->assertUnprocessable()
            ->assertJsonValidationErrors(['code', 'name']);
    }

    public function test_authenticated_user_can_delete_unit(): void
    {
        $token = $this->createToken();
        $unit = Unit::query()->create(['code' => 'BOX', 'name' => 'Hop', 'precision' => 0]);

        $this->withToken($token)
            ->deleteJson("/api/units/{$unit->id}")
            ->assertOk()
            ->assertJsonPath('message', 'Xóa đơn vị thành công');

        $this->assertDatabaseMissing('units', ['id' => $unit->id]);

        $this->withToken($token)
            ->getJson("/api/units/{$unit->id}")
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
