<?php

namespace Tests\Feature\Auth;

use App\Models\Rol;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class RegisterTest extends TestCase
{
    use RefreshDatabase;

    private array $validData = [
        'username' => 'Juan',
        'lastname' => 'Pérez',
        'email' => 'juan@example.com',
        'password' => 'Secret123',
        'password_confirmation' => 'Secret123',
    ];

    protected function setUp(): void
    {
        parent::setUp();

        Rol::create(['slug' => 'administrador', 'name' => 'Administrador']);
        Rol::create(['slug' => 'contador', 'name' => 'Contador']);
        Rol::create(['slug' => 'lecturador', 'name' => 'Lecturador']);
        Rol::create(['slug' => 'comun', 'name' => 'Comun']);
    }

    public function test_new_users_can_register(): void
    {
        $response = $this->postJson('/api/register', $this->validData);

        $response->assertStatus(201)
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
                'message' => 'Usuario registrado exitosamente.',
            ]);

        $this->assertDatabaseHas('users', [
            'email' => 'juan@example.com',
            'username' => 'Juan',
            'lastname' => 'Pérez',
        ]);
    }

    public function test_email_is_required(): void
    {
        $data = $this->validData;
        unset($data['email']);

        $response = $this->postJson('/api/register', $data);

        $response->assertStatus(422)
            ->assertJsonValidationErrors(['email']);
    }

    public function test_email_must_be_valid(): void
    {
        $data = $this->validData;
        $data['email'] = 'not-an-email';

        $response = $this->postJson('/api/register', $data);

        $response->assertStatus(422)
            ->assertJsonValidationErrors(['email']);
    }

    public function test_email_must_be_unique(): void
    {
        User::create([
            'username' => 'Existente',
            'lastname' => 'Usuario',
            'email' => 'juan@example.com',
            'password' => 'password123',
            'rol_id' => 4,
        ]);

        $response = $this->postJson('/api/register', $this->validData);

        $response->assertStatus(422)
            ->assertJsonValidationErrors(['email']);
    }

    public function test_password_is_required(): void
    {
        $data = $this->validData;
        unset($data['password']);
        unset($data['password_confirmation']);

        $response = $this->postJson('/api/register', $data);

        $response->assertStatus(422)
            ->assertJsonValidationErrors(['password']);
    }

    public function test_password_must_be_at_least_8_characters(): void
    {
        $data = $this->validData;
        $data['password'] = 'short';
        $data['password_confirmation'] = 'short';

        $response = $this->postJson('/api/register', $data);

        $response->assertStatus(422)
            ->assertJsonValidationErrors(['password']);
    }

    public function test_password_confirmation_is_required(): void
    {
        $data = $this->validData;
        unset($data['password_confirmation']);

        $response = $this->postJson('/api/register', $data);

        $response->assertStatus(422)
            ->assertJsonValidationErrors(['password']);
    }

    public function test_password_confirmation_must_match(): void
    {
        $data = $this->validData;
        $data['password_confirmation'] = 'different123';

        $response = $this->postJson('/api/register', $data);

        $response->assertStatus(422)
            ->assertJsonValidationErrors(['password']);
    }

    public function test_username_is_required(): void
    {
        $data = $this->validData;
        unset($data['username']);

        $response = $this->postJson('/api/register', $data);

        $response->assertStatus(422)
            ->assertJsonValidationErrors(['username']);
    }

    public function test_lastname_is_required(): void
    {
        $data = $this->validData;
        unset($data['lastname']);

        $response = $this->postJson('/api/register', $data);

        $response->assertStatus(422)
            ->assertJsonValidationErrors(['lastname']);
    }

    public function test_username_only_accepts_letters(): void
    {
        $data = $this->validData;
        $data['username'] = 'Juan123';

        $response = $this->postJson('/api/register', $data);

        $response->assertStatus(422)
            ->assertJsonValidationErrors(['username']);
    }

    public function test_lastname_only_accepts_letters(): void
    {
        $data = $this->validData;
        $data['lastname'] = 'Pérez123';

        $response = $this->postJson('/api/register', $data);

        $response->assertStatus(422)
            ->assertJsonValidationErrors(['lastname']);
    }

    public function test_user_gets_default_role(): void
    {
        $response = $this->postJson('/api/register', $this->validData);

        $response->assertStatus(201);

        $user = User::where('email', 'juan@example.com')->first();
        $rolComun = Rol::where('slug', 'comun')->first();
        $this->assertEquals($rolComun->id, $user->rol_id);
    }

    public function test_password_is_hashed_in_database(): void
    {
        $response = $this->postJson('/api/register', $this->validData);

        $response->assertStatus(201);

        $user = User::where('email', 'juan@example.com')->first();
        $this->assertNotEquals('Secret123', $user->password);
        $this->assertTrue(\Illuminate\Support\Facades\Hash::check('Secret123', $user->password));
    }

    public function test_token_is_generated(): void
    {
        $response = $this->postJson('/api/register', $this->validData);

        $response->assertStatus(201)
            ->assertJsonStructure([
                'data' => ['token'],
            ]);

        $this->assertDatabaseCount('personal_access_tokens', 1);
    }

    public function test_response_contains_user_data(): void
    {
        $response = $this->postJson('/api/register', $this->validData);

        $response->assertStatus(201)
            ->assertJson([
                'data' => [
                    'user' => [
                        'username' => 'Juan',
                        'lastname' => 'Pérez',
                        'email' => 'juan@example.com',
                        'role' => [
                            'name' => 'Comun',
                        ],
                    ],
                ],
            ]);
    }

    public function test_password_must_contain_uppercase(): void
    {
        $data = $this->validData;
        $data['password'] = 'secret123';
        $data['password_confirmation'] = 'secret123';

        $response = $this->postJson('/api/register', $data);

        $response->assertStatus(422)
            ->assertJsonValidationErrors(['password']);
    }

    public function test_password_must_contain_lowercase(): void
    {
        $data = $this->validData;
        $data['password'] = 'SECRET123';
        $data['password_confirmation'] = 'SECRET123';

        $response = $this->postJson('/api/register', $data);

        $response->assertStatus(422)
            ->assertJsonValidationErrors(['password']);
    }

    public function test_password_must_contain_digit(): void
    {
        $data = $this->validData;
        $data['password'] = 'SecretSecret';
        $data['password_confirmation'] = 'SecretSecret';

        $response = $this->postJson('/api/register', $data);

        $response->assertStatus(422)
            ->assertJsonValidationErrors(['password']);
    }
}
