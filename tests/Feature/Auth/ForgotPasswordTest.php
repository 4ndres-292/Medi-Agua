<?php

namespace Tests\Feature\Auth;

use App\Models\Rol;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Password as PasswordFacade;
use Tests\TestCase;

class ForgotPasswordTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        Rol::create(['slug' => 'administrador', 'name' => 'Administrador']);

        User::create([
            'username' => 'Test',
            'lastname' => 'User',
            'email' => 'test@example.com',
            'password' => 'password123',
            'rol_id' => 1,
        ]);
    }

    public function test_existing_email_returns_success(): void
    {
        $response = $this->postJson('/api/forgot-password', [
            'email' => 'test@example.com',
        ]);

        $response->assertStatus(200)
            ->assertJson([
                'success' => true,
                'message' => 'Si el correo existe, se enviará un enlace para restablecer la contraseña.',
            ]);
    }

    public function test_nonexistent_email_returns_same_success(): void
    {
        $response = $this->postJson('/api/forgot-password', [
            'email' => 'nonexistent@example.com',
        ]);

        $response->assertStatus(200)
            ->assertJson([
                'success' => true,
                'message' => 'Si el correo existe, se enviará un enlace para restablecer la contraseña.',
            ]);
    }

    public function test_invalid_email_returns_422(): void
    {
        $response = $this->postJson('/api/forgot-password', [
            'email' => 'not-an-email',
        ]);

        $response->assertStatus(422)
            ->assertJsonValidationErrors(['email']);
    }

    public function test_email_is_required(): void
    {
        $response = $this->postJson('/api/forgot-password', []);

        $response->assertStatus(422)
            ->assertJsonValidationErrors(['email']);
    }

    public function test_response_is_identical_for_existing_and_nonexistent_email(): void
    {
        $responseExisting = $this->postJson('/api/forgot-password', [
            'email' => 'test@example.com',
        ]);

        $responseNonexistent = $this->postJson('/api/forgot-password', [
            'email' => 'nonexistent@example.com',
        ]);

        $this->assertEquals(
            $responseExisting->json('message'),
            $responseNonexistent->json('message')
        );

        $this->assertEquals(
            $responseExisting->status(),
            $responseNonexistent->status()
        );
    }

    public function test_reset_token_is_created_for_existing_user(): void
    {
        $this->postJson('/api/forgot-password', [
            'email' => 'test@example.com',
        ]);

        $this->assertDatabaseHas('password_reset_tokens', [
            'email' => 'test@example.com',
        ]);
    }

    public function test_no_token_created_for_nonexistent_user(): void
    {
        $this->postJson('/api/forgot-password', [
            'email' => 'nonexistent@example.com',
        ]);

        $this->assertDatabaseCount('password_reset_tokens', 0);
    }
}
