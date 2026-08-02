<?php

namespace Tests\Feature\Auth;

use App\Models\Rol;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class LoginTest extends TestCase
{
    use RefreshDatabase;

    private array $validCredentials = [
        'email' => 'juan@example.com',
        'password' => 'Secret123',
    ];

    protected function setUp(): void
    {
        parent::setUp();

        $rol = Rol::create(['slug' => 'comun', 'name' => 'Comun']);

        User::create([
            'username' => 'Juan',
            'lastname' => 'Pérez',
            'email' => 'juan@example.com',
            'password' => 'Secret123',
            'rol_id' => $rol->id,
        ]);
    }

    public function test_user_can_login_with_valid_credentials(): void
    {
        $response = $this->postJson('/api/login', $this->validCredentials);

        $response->assertStatus(200)
            ->assertJsonStructure([
                'success',
                'message',
                'data' => [
                    'user' => ['id', 'username', 'lastname', 'email', 'role'],
                    'token',
                ],
            ])
            ->assertJson([
                'success' => true,
                'message' => 'Inicio de sesión exitoso.',
            ]);

        $this->assertDatabaseCount('personal_access_tokens', 1);
    }

    public function test_email_is_required(): void
    {
        $response = $this->postJson('/api/login', [
            'password' => 'Secret123',
        ]);

        $response->assertStatus(422)
            ->assertJsonValidationErrors(['email']);
    }

    public function test_password_is_required(): void
    {
        $response = $this->postJson('/api/login', [
            'email' => 'juan@example.com',
        ]);

        $response->assertStatus(422)
            ->assertJsonValidationErrors(['password']);
    }

    public function test_email_must_be_valid_format(): void
    {
        $response = $this->postJson('/api/login', [
            'email' => 'not-an-email',
            'password' => 'Secret123',
        ]);

        $response->assertStatus(422)
            ->assertJsonValidationErrors(['email']);
    }

    public function test_user_cannot_login_with_wrong_password(): void
    {
        $response = $this->postJson('/api/login', [
            'email' => 'juan@example.com',
            'password' => 'WrongPassword1',
        ]);

        $response->assertStatus(422)
            ->assertJsonValidationErrors(['password']);
    }

    public function test_user_cannot_login_with_nonexistent_email(): void
    {
        $response = $this->postJson('/api/login', [
            'email' => 'nonexistent@example.com',
            'password' => 'Secret123',
        ]);

        $response->assertStatus(422)
            ->assertJsonValidationErrors(['email']);
    }

    public function test_token_is_returned_on_successful_login(): void
    {
        $response = $this->postJson('/api/login', $this->validCredentials);

        $response->assertStatus(200)
            ->assertJsonStructure([
                'data' => ['token'],
            ]);

        $this->assertDatabaseCount('personal_access_tokens', 1);
    }

    public function test_user_data_is_returned_on_successful_login(): void
    {
        $response = $this->postJson('/api/login', $this->validCredentials);

        $response->assertStatus(200)
            ->assertJson([
                'data' => [
                    'user' => [
                        'username' => 'Juan',
                        'lastname' => 'Pérez',
                        'email' => 'juan@example.com',
                    ],
                ],
            ]);
    }

    public function test_role_is_included_in_user_data(): void
    {
        $response = $this->postJson('/api/login', $this->validCredentials);

        $response->assertStatus(200)
            ->assertJson([
                'data' => [
                    'user' => [
                        'role' => [
                            'name' => 'Comun',
                        ],
                    ],
                ],
            ]);
    }

    public function test_password_is_not_returned_in_response(): void
    {
        $response = $this->postJson('/api/login', $this->validCredentials);

        $response->assertStatus(200);

        $data = $response->json('data.user');
        $this->assertArrayNotHasKey('password', $data);
    }

    public function test_multiple_tokens_are_created_per_login(): void
    {
        $this->postJson('/api/login', $this->validCredentials);
        $this->postJson('/api/login', $this->validCredentials);

        $this->assertDatabaseCount('personal_access_tokens', 2);
    }

    public function test_response_format_matches_standard(): void
    {
        $response = $this->postJson('/api/login', $this->validCredentials);

        $response->assertStatus(200)
            ->assertJson([
                'success' => true,
                'errors' => null,
            ]);
    }

    public function test_email_field_is_case_insensitive(): void
    {
        $response = $this->postJson('/api/login', [
            'email' => 'JUAN@EXAMPLE.COM',
            'password' => 'Secret123',
        ]);

        $response->assertStatus(200);
    }
}
