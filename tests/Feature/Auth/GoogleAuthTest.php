<?php

namespace Tests\Feature\Auth;

use App\Models\Rol;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Socialite\Facades\Socialite;
use Tests\TestCase;

class GoogleAuthTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        Rol::create(['slug' => 'administrador', 'name' => 'Administrador']);
        Rol::create(['slug' => 'contador', 'name' => 'Contador']);
        Rol::create(['slug' => 'lecturador', 'name' => 'Lecturador']);
        Rol::create(['slug' => 'comun', 'name' => 'Comun']);
    }

    private function mockGoogleUser(array $overrides = []): \Laravel\Socialite\Two\User
    {
        $defaults = [
            'id' => 'google-id-123456',
            'name' => 'Juan García',
            'email' => 'juan.garcia@gmail.com',
            'avatar' => 'https://lh3.googleusercontent.com/photo.jpg',
            'email_verified' => true,
        ];

        $data = array_merge($defaults, $overrides);

        $user = new \Laravel\Socialite\Two\User();
        $user->map($data);
        $user->setRaw($data);

        return $user;
    }

    /*
    |--------------------------------------------------------------------------
    | Redirect Tests
    |--------------------------------------------------------------------------
    */

    public function test_google_redirect_returns_302(): void
    {
        Socialite::shouldReceive('driver->stateless->redirect')
            ->andReturn(new \Illuminate\Http\RedirectResponse('https://accounts.google.com/o/oauth2/auth'));

        $response = $this->get('/api/auth/google/redirect');

        $response->assertStatus(302);
    }

    public function test_google_redirect_sends_to_google_oauth(): void
    {
        $expectedUrl = 'https://accounts.google.com/o/oauth2/auth?client_id=xxx&redirect_uri=xxx';

        Socialite::shouldReceive('driver->stateless->redirect')
            ->andReturn(new \Illuminate\Http\RedirectResponse($expectedUrl));

        $response = $this->get('/api/auth/google/redirect');

        $response->assertRedirect($expectedUrl);
    }

    /*
    |--------------------------------------------------------------------------
    | Callback - New User Tests
    |--------------------------------------------------------------------------
    */

    public function test_google_callback_creates_new_user(): void
    {
        Socialite::shouldReceive('driver->stateless->user')
            ->andReturn($this->mockGoogleUser());

        $response = $this->get('/api/auth/google/callback');

        $response->assertStatus(200)
            ->assertJson([
                'success' => true,
                'message' => 'Inicio de sesión con Google exitoso.',
            ]);

        $this->assertDatabaseHas('users', [
            'email' => 'juan.garcia@gmail.com',
            'google_id' => 'google-id-123456',
            'username' => 'Juan García',
        ]);
    }

    public function test_google_callback_assigns_comun_role(): void
    {
        Socialite::shouldReceive('driver->stateless->user')
            ->andReturn($this->mockGoogleUser());

        $this->get('/api/auth/google/callback');

        $user = User::where('email', 'juan.garcia@gmail.com')->first();

        $comunRole = Rol::where('slug', 'comun')->first();

        $this->assertNotNull($user);
        $this->assertEquals($comunRole->id, $user->rol_id);
        $this->assertEquals('comun', $user->rol->slug);
    }

    public function test_google_callback_generates_sanctum_token(): void
    {
        Socialite::shouldReceive('driver->stateless->user')
            ->andReturn($this->mockGoogleUser());

        $response = $this->get('/api/auth/google/callback');

        $response->assertStatus(200)
            ->assertJsonStructure([
                'data' => ['token'],
            ]);

        $this->assertDatabaseCount('personal_access_tokens', 1);
    }

    public function test_google_callback_returns_user_data(): void
    {
        Socialite::shouldReceive('driver->stateless->user')
            ->andReturn($this->mockGoogleUser());

        $response = $this->get('/api/auth/google/callback');

        $response->assertStatus(200)
            ->assertJson([
                'data' => [
                    'user' => [
                        'email' => 'juan.garcia@gmail.com',
                        'username' => 'Juan García',
                    ],
                ],
            ]);
    }

    public function test_google_callback_stores_avatar(): void
    {
        Socialite::shouldReceive('driver->stateless->user')
            ->andReturn($this->mockGoogleUser());

        $this->get('/api/auth/google/callback');

        $user = User::where('email', 'juan.garcia@gmail.com')->first();

        $this->assertEquals('https://lh3.googleusercontent.com/photo.jpg', $user->avatar);
    }

    public function test_google_callback_creates_random_password(): void
    {
        Socialite::shouldReceive('driver->stateless->user')
            ->andReturn($this->mockGoogleUser());

        $this->get('/api/auth/google/callback');

        $user = User::where('email', 'juan.garcia@gmail.com')->first();

        $this->assertNotNull($user->password);
        $this->assertNotEquals('', $user->password);
    }

    public function test_new_google_user_has_empty_lastname(): void
    {
        Socialite::shouldReceive('driver->stateless->user')
            ->andReturn($this->mockGoogleUser());

        $this->get('/api/auth/google/callback');

        $user = User::where('email', 'juan.garcia@gmail.com')->first();

        $this->assertEquals('', $user->lastname);
    }

    /*
    |--------------------------------------------------------------------------
    | Callback - Existing User Tests
    |--------------------------------------------------------------------------
    */

    public function test_google_callback_links_existing_user(): void
    {
        $comunRole = Rol::where('slug', 'comun')->first();

        $existingUser = User::create([
            'username' => 'Juan',
            'lastname' => 'García',
            'email' => 'juan.garcia@gmail.com',
            'password' => 'Password123',
            'rol_id' => $comunRole->id,
        ]);

        Socialite::shouldReceive('driver->stateless->user')
            ->andReturn($this->mockGoogleUser());

        $this->get('/api/auth/google/callback');

        $existingUser->refresh();

        $this->assertEquals('google-id-123456', $existingUser->google_id);
        $this->assertEquals('https://lh3.googleusercontent.com/photo.jpg', $existingUser->avatar);
    }

    public function test_google_callback_does_not_create_duplicate(): void
    {
        $comunRole = Rol::where('slug', 'comun')->first();

        User::create([
            'username' => 'Juan',
            'lastname' => 'García',
            'email' => 'juan.garcia@gmail.com',
            'password' => 'Password123',
            'rol_id' => $comunRole->id,
        ]);

        Socialite::shouldReceive('driver->stateless->user')
            ->andReturn($this->mockGoogleUser());

        $this->get('/api/auth/google/callback');

        $this->assertDatabaseCount('users', 1);
    }

    public function test_google_callback_preserves_existing_role(): void
    {
        $adminRole = Rol::where('slug', 'administrador')->first();

        User::create([
            'username' => 'Juan',
            'lastname' => 'García',
            'email' => 'juan.garcia@gmail.com',
            'password' => 'Password123',
            'rol_id' => $adminRole->id,
        ]);

        Socialite::shouldReceive('driver->stateless->user')
            ->andReturn($this->mockGoogleUser());

        $this->get('/api/auth/google/callback');

        $user = User::where('email', 'juan.garcia@gmail.com')->first();

        $this->assertEquals($adminRole->id, $user->rol_id);
    }

    public function test_existing_user_gets_token_on_google_login(): void
    {
        $comunRole = Rol::where('slug', 'comun')->first();

        User::create([
            'username' => 'Juan',
            'lastname' => 'García',
            'email' => 'juan.garcia@gmail.com',
            'password' => 'Password123',
            'rol_id' => $comunRole->id,
        ]);

        Socialite::shouldReceive('driver->stateless->user')
            ->andReturn($this->mockGoogleUser());

        $response = $this->get('/api/auth/google/callback');

        $response->assertStatus(200)
            ->assertJsonStructure([
                'data' => ['token'],
            ]);

        $this->assertDatabaseCount('personal_access_tokens', 1);
    }

    /*
    |--------------------------------------------------------------------------
    | Role Tests
    |--------------------------------------------------------------------------
    */

    public function test_google_user_role_comes_from_database(): void
    {
        $comunRole = Rol::where('slug', 'comun')->first();

        Socialite::shouldReceive('driver->stateless->user')
            ->andReturn($this->mockGoogleUser());

        $this->get('/api/auth/google/callback');

        $user = User::where('email', 'juan.garcia@gmail.com')->first();

        $this->assertEquals($comunRole->id, $user->rol_id);
    }

    public function test_google_user_role_not_hardcoded(): void
    {
        Rol::where('slug', 'comun')->update(['slug' => 'comun_custom']);

        Rol::create(['slug' => 'comun', 'name' => 'Comun']);

        Socialite::shouldReceive('driver->stateless->user')
            ->andReturn($this->mockGoogleUser());

        $this->get('/api/auth/google/callback');

        $user = User::where('email', 'juan.garcia@gmail.com')->first();

        $this->assertNotNull($user);
        $this->assertEquals('comun', $user->rol->slug);
    }

    /*
    |--------------------------------------------------------------------------
    | Error Handling Tests
    |--------------------------------------------------------------------------
    */

    public function test_google_callback_handles_invalid_state(): void
    {
        Socialite::shouldReceive('driver->stateless->user')
            ->andThrow(new \Exception('Invalid state'));

        $response = $this->get('/api/auth/google/callback');

        $response->assertStatus(500)
            ->assertJson([
                'success' => false,
                'message' => 'Error al autenticar con Google. Intente nuevamente.',
            ]);
    }

    public function test_google_callback_handles_general_exception(): void
    {
        Socialite::shouldReceive('driver->stateless->user')
            ->andThrow(new \Exception('Connection failed'));

        $response = $this->get('/api/auth/google/callback');

        $response->assertStatus(500)
            ->assertJson([
                'success' => false,
                'message' => 'Error al autenticar con Google. Intente nuevamente.',
            ]);
    }

    public function test_google_callback_rejects_unverified_email(): void
    {
        $googleUser = $this->mockGoogleUser(['email_verified' => false]);

        Socialite::shouldReceive('driver->stateless->user')
            ->andReturn($googleUser);

        $response = $this->get('/api/auth/google/callback');

        $response->assertStatus(422)
            ->assertJsonValidationErrors(['email']);
    }

    public function test_google_callback_rejects_null_email(): void
    {
        $googleUser = $this->mockGoogleUser(['email' => null]);

        Socialite::shouldReceive('driver->stateless->user')
            ->andReturn($googleUser);

        $response = $this->get('/api/auth/google/callback');

        $response->assertStatus(422)
            ->assertJsonValidationErrors(['email']);
    }

    /*
    |--------------------------------------------------------------------------
    | Response Format Tests
    |--------------------------------------------------------------------------
    */

    public function test_response_uses_standard_api_format(): void
    {
        Socialite::shouldReceive('driver->stateless->user')
            ->andReturn($this->mockGoogleUser());

        $response = $this->get('/api/auth/google/callback');

        $response->assertJsonStructure([
            'success',
            'message',
            'data' => [
                'user' => ['id', 'username', 'lastname', 'email', 'role'],
                'token',
            ],
            'errors',
        ]);
    }

    public function test_google_callback_returns_same_format_as_login(): void
    {
        Socialite::shouldReceive('driver->stateless->user')
            ->andReturn($this->mockGoogleUser());

        $response = $this->get('/api/auth/google/callback');

        $response->assertStatus(200)
            ->assertJsonStructure([
                'data' => [
                    'user' => ['id', 'username', 'lastname', 'email', 'role'],
                    'token',
                ],
            ]);
    }
}
