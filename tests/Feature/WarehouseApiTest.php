<?php

namespace Tests\Feature;

use App\Models\User;
use App\Models\Warehouse;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class WarehouseApiTest extends TestCase
{
    use RefreshDatabase;

    public function test_warehouse_routes_require_authentication(): void
    {
        $this->getJson('/api/warehouses')->assertUnauthorized();

        $this->postJson('/api/warehouses', [
            'code' => 'HCM01',
            'name' => 'Kho TP HCM',
            'address' => 'Quan 1, TP HCM',
            'status' => 'active',
        ])->assertUnauthorized();
    }

    public function test_authenticated_user_can_list_warehouses_with_search_and_status_filter(): void
    {
        $token = $this->createToken();
        $manager = $this->createManager();

        Warehouse::query()->create([
            'code' => 'HCM01',
            'name' => 'Kho TP HCM',
            'address' => 'Quan 1, TP HCM',
            'manager_id' => $manager->id,
            'status' => 'active',
        ]);
        Warehouse::query()->create([
            'code' => 'HN01',
            'name' => 'Kho Ha Noi',
            'address' => 'Cau Giay, Ha Noi',
            'status' => 'inactive',
        ]);

        $this->withToken($token)
            ->getJson('/api/warehouses?search=HCM&status=active')
            ->assertOk()
            ->assertJsonFragment(['code' => 'HCM01'])
            ->assertJsonMissing(['code' => 'HN01']);
    }

    public function test_authenticated_user_can_create_warehouse(): void
    {
        $token = $this->createToken();
        $manager = $this->createManager();

        $response = $this->withToken($token)->postJson('/api/warehouses', [
            'code' => 'HCM01',
            'name' => 'Kho TP HCM',
            'address' => 'Quan 1, TP HCM',
            'manager_id' => $manager->id,
            'status' => 'active',
        ]);

        $response
            ->assertCreated()
            ->assertJsonPath('message', 'Tạo kho hàng thành công')
            ->assertJsonPath('warehouse.code', 'HCM01')
            ->assertJsonPath('warehouse.name', 'Kho TP HCM')
            ->assertJsonPath('warehouse.manager.email', 'manager@example.com')
            ->assertJsonPath('warehouse.status', 'active');

        $this->assertDatabaseHas('warehouses', [
            'code' => 'HCM01',
            'name' => 'Kho TP HCM',
            'address' => 'Quan 1, TP HCM',
            'manager_id' => $manager->id,
            'status' => 'active',
        ]);
    }

    public function test_create_warehouse_validates_required_unique_foreign_key_and_status_fields(): void
    {
        $token = $this->createToken();

        Warehouse::query()->create([
            'code' => 'HCM01',
            'name' => 'Kho TP HCM',
            'address' => 'Quan 1, TP HCM',
            'status' => 'active',
        ]);

        $response = $this->withToken($token)->postJson('/api/warehouses', [
            'code' => 'HCM01',
            'name' => '',
            'address' => '',
            'manager_id' => 999999,
            'status' => 'invalid',
        ]);

        $response
            ->assertUnprocessable()
            ->assertJsonValidationErrors(['code', 'name', 'address', 'manager_id', 'status']);
    }

    public function test_authenticated_user_can_show_warehouse_with_manager(): void
    {
        $manager = $this->createManager();
        $warehouse = Warehouse::query()->create([
            'code' => 'HCM01',
            'name' => 'Kho TP HCM',
            'address' => 'Quan 1, TP HCM',
            'manager_id' => $manager->id,
            'status' => 'active',
        ]);

        $this->withToken($this->createToken())
            ->getJson("/api/warehouses/{$warehouse->id}")
            ->assertOk()
            ->assertJsonPath('code', 'HCM01')
            ->assertJsonPath('name', 'Kho TP HCM')
            ->assertJsonPath('manager.email', 'manager@example.com')
            ->assertJsonPath('status', 'active');
    }

    public function test_show_warehouse_returns_not_found_for_missing_record(): void
    {
        $this->withToken($this->createToken())
            ->getJson('/api/warehouses/999999')
            ->assertNotFound()
            ->assertJsonPath('message', 'Kho hàng không tồn tại');
    }

    public function test_authenticated_user_can_update_warehouse_and_keep_same_code(): void
    {
        $token = $this->createToken();
        $manager = $this->createManager();
        $warehouse = Warehouse::query()->create([
            'code' => 'HCM01',
            'name' => 'Kho TP HCM',
            'address' => 'Quan 1, TP HCM',
            'manager_id' => $manager->id,
            'status' => 'active',
        ]);

        $response = $this->withToken($token)->putJson("/api/warehouses/{$warehouse->id}", [
            'code' => 'HCM01',
            'name' => 'Kho TP HCM - Updated',
            'address' => 'Thu Duc, TP HCM',
            'manager_id' => $manager->id,
            'status' => 'inactive',
        ]);

        $response
            ->assertOk()
            ->assertJsonPath('message', 'Cập nhật kho hàng thành công')
            ->assertJsonPath('warehouse.code', 'HCM01')
            ->assertJsonPath('warehouse.name', 'Kho TP HCM - Updated')
            ->assertJsonPath('warehouse.address', 'Thu Duc, TP HCM')
            ->assertJsonPath('warehouse.manager.email', 'manager@example.com')
            ->assertJsonPath('warehouse.status', 'inactive');
    }

    public function test_update_warehouse_validates_unique_code(): void
    {
        $token = $this->createToken();
        $warehouse = Warehouse::query()->create([
            'code' => 'HCM01',
            'name' => 'Kho TP HCM',
            'address' => 'Quan 1, TP HCM',
            'status' => 'active',
        ]);
        Warehouse::query()->create([
            'code' => 'HN01',
            'name' => 'Kho Ha Noi',
            'address' => 'Cau Giay, Ha Noi',
            'status' => 'active',
        ]);

        $this->withToken($token)
            ->putJson("/api/warehouses/{$warehouse->id}", [
                'code' => 'HN01',
                'name' => 'Kho TP HCM',
                'address' => 'Quan 1, TP HCM',
                'status' => 'active',
            ])
            ->assertUnprocessable()
            ->assertJsonValidationErrors(['code']);
    }

    public function test_authenticated_user_can_delete_warehouse(): void
    {
        $token = $this->createToken();
        $warehouse = Warehouse::query()->create([
            'code' => 'HCM01',
            'name' => 'Kho TP HCM',
            'address' => 'Quan 1, TP HCM',
            'status' => 'active',
        ]);

        $this->withToken($token)
            ->deleteJson("/api/warehouses/{$warehouse->id}")
            ->assertOk()
            ->assertJsonPath('message', 'Xóa kho hàng thành công');

        $this->assertSoftDeleted('warehouses', ['id' => $warehouse->id]);

        $this->withToken($token)
            ->getJson("/api/warehouses/{$warehouse->id}")
            ->assertNotFound();
    }

    private function createManager(): User
    {
        return User::factory()->create([
            'name' => 'Warehouse Manager',
            'email' => 'manager@example.com',
            'password' => Hash::make('password123'),
            'role' => 'warehouse_manager',
            'status' => 'active',
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
