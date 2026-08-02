<?php

namespace Tests\Feature\Auth;

use App\Models\Rol;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

class ChangePasswordTest extends TestCase
{
    use RefreshDatabase;

    private User $user;
    private string $currentPassword;

    protected function setUp(): void
    {
        parent::setUp();

        Rol::create(['slug' => 'administrador', 'name' => 'Administrador']);

        $this->currentPassword = 'Current123';
        $this->user = User::create([
            'username' => 'Test',
            'lastname' => 'User',
            'email' => 'test@example.com',
            'password' => $this->currentPassword,
            'rol_id' => 1,
        ]);
    }

    public function test_password_changed_correctly(): void
    {
        Sanctum::actingAs($this->user);

        $response = $this->postJson('/api/change-password', [
            'current_password' => $this->currentPassword,
            'password' => 'NewPassword123',
            'password_confirmation' => 'NewPassword123',
        ]);

        $response->assertStatus(200)
            ->assertJson([
                'success' => true,
                'message' => 'Contraseña actualizada correctamente.',
            ]);

        $this->user->refresh();
        $this->assertTrue(\Illuminate\Support\Facades\Hash::check('NewPassword123', $this->user->password));
    }

    public function test_current_password_incorrect(): void
    {
        Sanctum::actingAs($this->user);

        $response = $this->postJson('/api/change-password', [
            'current_password' => 'wrongpassword',
            'password' => 'NewPassword123',
            'password_confirmation' => 'NewPassword123',
        ]);

        $response->assertStatus(422)
            ->assertJsonValidationErrors(['current_password']);
    }

    public function test_password_confirmation_does_not_match(): void
    {
        Sanctum::actingAs($this->user);

        $response = $this->postJson('/api/change-password', [
            'current_password' => $this->currentPassword,
            'password' => 'NewPassword123',
            'password_confirmation' => 'different123',
        ]);

        $response->assertStatus(422)
            ->assertJsonValidationErrors(['password']);
    }

    public function test_password_too_short(): void
    {
        Sanctum::actingAs($this->user);

        $response = $this->postJson('/api/change-password', [
            'current_password' => $this->currentPassword,
            'password' => 'short',
            'password_confirmation' => 'short',
        ]);

        $response->assertStatus(422)
            ->assertJsonValidationErrors(['password']);
    }

    public function test_required_fields(): void
    {
        Sanctum::actingAs($this->user);

        $response = $this->postJson('/api/change-password', []);

        $response->assertStatus(422)
            ->assertJsonValidationErrors(['current_password', 'password']);
    }

    public function test_tokens_are_invalidated_after_change(): void
    {
        $token1 = $this->user->createToken('token-1')->plainTextToken;
        $token2 = $this->user->createToken('token-2')->plainTextToken;

        $this->assertDatabaseCount('personal_access_tokens', 2);

        $response = $this->withHeader('Authorization', "Bearer $token1")
            ->postJson('/api/change-password', [
                'current_password' => $this->currentPassword,
                'password' => 'NewPassword123',
                'password_confirmation' => 'NewPassword123',
            ]);

        $response->assertStatus(200);

        $this->assertDatabaseCount('personal_access_tokens', 0);
    }

    public function test_unauthenticated_request_returns_401(): void
    {
        $response = $this->postJson('/api/change-password', [
            'current_password' => $this->currentPassword,
            'password' => 'NewPassword123',
            'password_confirmation' => 'NewPassword123',
        ]);

        $response->assertStatus(401);
    }
}
