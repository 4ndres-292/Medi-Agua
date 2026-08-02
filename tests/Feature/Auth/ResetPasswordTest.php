<?php

namespace Tests\Feature\Auth;

use App\Models\Rol;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Password;
use Tests\TestCase;

class ResetPasswordTest extends TestCase
{
    use RefreshDatabase;

    private User $user;

    protected function setUp(): void
    {
        parent::setUp();

        Rol::create(['slug' => 'administrador', 'name' => 'Administrador']);

        $this->user = User::create([
            'username' => 'Test',
            'lastname' => 'User',
            'email' => 'test@example.com',
            'password' => 'OldPassword123',
            'rol_id' => 1,
        ]);
    }

    private function generateResetToken(): string
    {
        return Password::createToken($this->user);
    }

    public function test_password_reset_successfully(): void
    {
        $token = $this->generateResetToken();

        $response = $this->postJson('/api/reset-password', [
            'token' => $token,
            'email' => 'test@example.com',
            'password' => 'NewPassword123',
            'password_confirmation' => 'NewPassword123',
        ]);

        $response->assertStatus(200)
            ->assertJson([
                'success' => true,
                'message' => 'Contraseña restablecida correctamente.',
            ]);

        $this->user->refresh();
        $this->assertTrue(\Illuminate\Support\Facades\Hash::check('NewPassword123', $this->user->password));
    }

    public function test_invalid_token_returns_422(): void
    {
        $response = $this->postJson('/api/reset-password', [
            'token' => 'invalid-token-12345',
            'email' => 'test@example.com',
            'password' => 'NewPassword123',
            'password_confirmation' => 'NewPassword123',
        ]);

        $response->assertStatus(422)
            ->assertJson([
                'success' => false,
            ]);
    }

    public function test_tokens_are_invalidated_after_reset(): void
    {
        $this->user->createToken('token-1')->plainTextToken;
        $this->user->createToken('token-2')->plainTextToken;

        $this->assertDatabaseCount('personal_access_tokens', 2);

        $token = $this->generateResetToken();

        $response = $this->postJson('/api/reset-password', [
            'token' => $token,
            'email' => 'test@example.com',
            'password' => 'NewPassword123',
            'password_confirmation' => 'NewPassword123',
        ]);

        $response->assertStatus(200);

        $this->assertDatabaseCount('personal_access_tokens', 0);
    }

    public function test_password_confirmation_required(): void
    {
        $token = $this->generateResetToken();

        $response = $this->postJson('/api/reset-password', [
            'token' => $token,
            'email' => 'test@example.com',
            'password' => 'NewPassword123',
        ]);

        $response->assertStatus(422)
            ->assertJsonValidationErrors(['password']);
    }

    public function test_password_confirmation_must_match(): void
    {
        $token = $this->generateResetToken();

        $response = $this->postJson('/api/reset-password', [
            'token' => $token,
            'email' => 'test@example.com',
            'password' => 'NewPassword123',
            'password_confirmation' => 'different123',
        ]);

        $response->assertStatus(422)
            ->assertJsonValidationErrors(['password']);
    }

    public function test_password_too_short(): void
    {
        $token = $this->generateResetToken();

        $response = $this->postJson('/api/reset-password', [
            'token' => $token,
            'email' => 'test@example.com',
            'password' => 'short',
            'password_confirmation' => 'short',
        ]);

        $response->assertStatus(422)
            ->assertJsonValidationErrors(['password']);
    }

    public function test_required_fields(): void
    {
        $response = $this->postJson('/api/reset-password', []);

        $response->assertStatus(422)
            ->assertJsonValidationErrors(['token', 'email', 'password']);
    }

    public function test_invalid_email_returns_422(): void
    {
        $token = $this->generateResetToken();

        $response = $this->postJson('/api/reset-password', [
            'token' => $token,
            'email' => 'not-an-email',
            'password' => 'NewPassword123',
            'password_confirmation' => 'NewPassword123',
        ]);

        $response->assertStatus(422)
            ->assertJsonValidationErrors(['email']);
    }
}
