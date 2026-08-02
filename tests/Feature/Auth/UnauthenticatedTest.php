<?php

namespace Tests\Feature\Auth;

use App\Models\Rol;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

class UnauthenticatedTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        Rol::create(['slug' => 'administrador', 'name' => 'Administrador']);
    }

    public function test_unauthenticated_request_returns_401(): void
    {
        $response = $this->getJson('/api/me');

        $response->assertStatus(401)
            ->assertJson([
                'success' => false,
                'message' => 'Unauthenticated.',
                'data' => null,
                'errors' => null,
            ]);
    }

    public function test_invalid_token_returns_401(): void
    {
        $response = $this->withHeader('Authorization', 'Bearer invalid-token-12345')
            ->getJson('/api/me');

        $response->assertStatus(401)
            ->assertJson([
                'success' => false,
                'message' => 'Unauthenticated.',
                'data' => null,
                'errors' => null,
            ]);
    }

    public function test_deleted_token_returns_401(): void
    {
        $user = User::create([
            'username' => 'Test',
            'lastname' => 'User',
            'email' => 'test@example.com',
            'password' => 'password123',
            'rol_id' => 1,
        ]);

        $token = $user->createToken('test-token')->plainTextToken;

        $user->tokens()->delete();

        $response = $this->withHeader('Authorization', "Bearer $token")
            ->getJson('/api/me');

        $response->assertStatus(401)
            ->assertJson([
                'success' => false,
                'message' => 'Unauthenticated.',
                'data' => null,
                'errors' => null,
            ]);
    }

    public function test_response_is_json_not_html(): void
    {
        $response = $this->getJson('/api/me');

        $response->assertHeader('Content-Type', 'application/json');
    }
}
