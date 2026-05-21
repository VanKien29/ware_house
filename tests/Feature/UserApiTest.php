<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class UserApiTest extends TestCase
{
    use RefreshDatabase;

    public function test_user_routes_require_authentication(): void
    {
        $this->getJson('/api/users')->assertUnauthorized();

        $this->postJson('/api/users', [
            'name' => 'New User',
            'email' => 'new@example.com',
            'password' => 'password123',
            'role' => 'staff',
            'status' => 'active',
        ])->assertUnauthorized();
    }

    public function test_authenticated_user_can_list_users_with_search(): void
    {
        $token = $this->createToken();

        User::factory()->create([
            'name' => 'Nguyen Van An',
            'email' => 'an@example.com',
            'role' => 'staff',
            'status' => 'active',
        ]);
        User::factory()->create([
            'name' => 'Tran Thi Binh',
            'email' => 'binh@example.com',
            'role' => 'sales_staff',
            'status' => 'active',
        ]);

        $response = $this->withToken($token)->getJson('/api/users?search=Nguyen');

        $response
            ->assertOk()
            ->assertJsonStructure([
                'data',
                'current_page',
                'total',
            ])
            ->assertJsonFragment(['email' => 'an@example.com'])
            ->assertJsonMissing(['email' => 'binh@example.com']);
    }

    public function test_authenticated_user_can_create_user(): void
    {
        $token = $this->createToken();

        $response = $this->withToken($token)->postJson('/api/users', [
            'name' => 'Nhan Vien Moi',
            'email' => 'new-user@example.com',
            'password' => 'password123',
            'role' => 'staff',
            'status' => 'active',
        ]);

        $response
            ->assertCreated()
            ->assertJsonPath('message', 'Người dùng đã được tạo thành công')
            ->assertJsonPath('user.email', 'new-user@example.com')
            ->assertJsonMissingPath('user.password');

        $user = User::where('email', 'new-user@example.com')->firstOrFail();

        $this->assertSame('staff', $user->role);
        $this->assertSame('active', $user->status);
        $this->assertTrue(Hash::check('password123', $user->password));
    }

    public function test_create_user_validates_required_fields_and_unique_email(): void
    {
        $token = $this->createToken();

        User::factory()->create([
            'email' => 'taken@example.com',
            'role' => 'staff',
            'status' => 'active',
        ]);

        $response = $this->withToken($token)->postJson('/api/users', [
            'name' => '',
            'email' => 'taken@example.com',
            'password' => 'short',
            'role' => 'invalid-role',
            'status' => 'locked',
        ]);

        $response
            ->assertUnprocessable()
            ->assertJsonValidationErrors(['name', 'email', 'password', 'role', 'status']);
    }

    public function test_authenticated_user_can_show_user(): void
    {
        $token = $this->createToken();
        $user = User::factory()->create([
            'email' => 'show@example.com',
            'role' => 'warehouse_manager',
            'status' => 'active',
        ]);

        $this->withToken($token)
            ->getJson("/api/users/{$user->id}")
            ->assertOk()
            ->assertJsonPath('email', 'show@example.com')
            ->assertJsonMissingPath('password');
    }

    public function test_show_user_returns_not_found_for_missing_user(): void
    {
        $this->withToken($this->createToken())
            ->getJson('/api/users/999999')
            ->assertNotFound()
            ->assertJsonPath('message', 'Người dùng không tồn tại');
    }

    public function test_authenticated_user_can_update_user_and_keep_same_email(): void
    {
        $token = $this->createToken();
        $user = User::factory()->create([
            'name' => 'Old Name',
            'email' => 'update@example.com',
            'role' => 'staff',
            'status' => 'active',
        ]);

        $response = $this->withToken($token)->putJson("/api/users/{$user->id}", [
            'name' => 'Updated Name',
            'email' => 'update@example.com',
            'role' => 'warehouse_manager',
            'status' => 'inactive',
        ]);

        $response
            ->assertOk()
            ->assertJsonPath('message', 'Người dùng đã được cập nhật thành công')
            ->assertJsonPath('user.name', 'Updated Name')
            ->assertJsonPath('user.email', 'update@example.com')
            ->assertJsonPath('user.role', 'warehouse_manager')
            ->assertJsonPath('user.status', 'inactive');

        $this->assertDatabaseHas('users', [
            'id' => $user->id,
            'name' => 'Updated Name',
            'email' => 'update@example.com',
            'role' => 'warehouse_manager',
            'status' => 'inactive',
        ]);
    }

    public function test_update_user_validates_unique_email(): void
    {
        $token = $this->createToken();
        $user = User::factory()->create([
            'email' => 'first@example.com',
            'role' => 'staff',
            'status' => 'active',
        ]);
        User::factory()->create([
            'email' => 'second@example.com',
            'role' => 'staff',
            'status' => 'active',
        ]);

        $this->withToken($token)
            ->putJson("/api/users/{$user->id}", [
                'email' => 'second@example.com',
            ])
            ->assertUnprocessable()
            ->assertJsonValidationErrors(['email']);
    }

    public function test_authenticated_user_can_delete_user(): void
    {
        $token = $this->createToken();
        $user = User::factory()->create([
            'email' => 'delete@example.com',
            'role' => 'staff',
            'status' => 'active',
        ]);

        $this->withToken($token)
            ->deleteJson("/api/users/{$user->id}")
            ->assertOk()
            ->assertJsonPath('message', 'Người dùng đã được xóa thành công');

        $this->assertSoftDeleted('users', ['id' => $user->id]);

        $this->withToken($token)
            ->getJson("/api/users/{$user->id}")
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
