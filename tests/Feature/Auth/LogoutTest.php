<?php

namespace Tests\Feature\Auth;

use App\Models\Rol;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class LogoutTest extends TestCase
{
    use RefreshDatabase;

    private User $user;

    protected function setUp(): void
    {
        parent::setUp();

        $rol = Rol::create(['slug' => 'comun', 'name' => 'Comun']);

        $this->user = User::create([
            'username' => 'Juan',
            'lastname' => 'Pérez',
            'email' => 'juan@example.com',
            'password' => 'Secret123',
            'rol_id' => $rol->id,
        ]);
    }

    public function test_authenticated_user_can_logout(): void
    {
        $token = $this->user->createToken('auth-token')->plainTextToken;

        $response = $this->withHeaders([
            'Authorization' => 'Bearer ' . $token,
            'Accept' => 'application/json',
        ])->postJson('/api/logout');

        $response->assertStatus(200)
            ->assertJson([
                'success' => true,
                'message' => 'Sesión cerrada correctamente.',
            ]);

        $this->assertDatabaseCount('personal_access_tokens', 0);
    }

    public function test_unauthenticated_user_cannot_logout(): void
    {
        $response = $this->postJson('/api/logout');

        $response->assertStatus(401)
            ->assertJson([
                'success' => false,
            ]);
    }

    public function test_token_is_deleted_on_logout(): void
    {
        $token = $this->user->createToken('auth-token')->plainTextToken;

        $this->assertDatabaseCount('personal_access_tokens', 1);

        $this->withHeaders([
            'Authorization' => 'Bearer ' . $token,
            'Accept' => 'application/json',
        ])->postJson('/api/logout');

        $this->assertDatabaseCount('personal_access_tokens', 0);
    }

    public function test_logout_returns_success_response(): void
    {
        $token = $this->user->createToken('auth-token')->plainTextToken;

        $response = $this->withHeaders([
            'Authorization' => 'Bearer ' . $token,
            'Accept' => 'application/json',
        ])->postJson('/api/logout');

        $response->assertStatus(200)
            ->assertJsonStructure([
                'success',
                'message',
                'data',
                'errors',
            ])
            ->assertJson([
                'success' => true,
                'data' => null,
            ]);
    }

    public function test_only_current_token_is_deleted(): void
    {
        $token1 = $this->user->createToken('token-1')->plainTextToken;
        $token2 = $this->user->createToken('token-2')->plainTextToken;

        $this->assertDatabaseCount('personal_access_tokens', 2);

        $this->withHeaders([
            'Authorization' => 'Bearer ' . $token1,
            'Accept' => 'application/json',
        ])->postJson('/api/logout');

        $this->assertDatabaseCount('personal_access_tokens', 1);
    }

    public function test_invalid_token_returns_401_on_me(): void
    {
        $response = $this->withHeaders([
            'Authorization' => 'Bearer fake-invalid-token-12345',
            'Accept' => 'application/json',
        ])->getJson('/api/me');

        $response->assertStatus(401);
    }

    public function test_nonexistent_token_returns_401(): void
    {
        $response = $this->withHeaders([
            'Authorization' => 'Bearer invalid-token-12345',
            'Accept' => 'application/json',
        ])->getJson('/api/me');

        $response->assertStatus(401);
    }
}
