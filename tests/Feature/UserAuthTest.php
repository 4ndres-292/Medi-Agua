<?php

namespace Tests\Feature;

use App\Models\Rol;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
#
class UserAuthTest extends TestCase
{
    use RefreshDatabase;

    public function test_user_can_login_and_receive_access_token(): void
    {
        $role = Rol::create(['name' => 'Admin']);

        $user = User::create([
            'username' => 'Juan',
            'lastname' => 'Perez',
            'email' => 'juan@example.com',
            'password' => 'password123',
            'role_id' => $role->id,
        ]);

        $response = $this->postJson('/api/login', [
            'email' => $user->email,
            'password' => 'password123',
        ]);

        $response->assertStatus(200)
            ->assertJsonStructure([
                'success',
                'message',
                'data' => [
                    'access_token',
                    'token_type',
                    'user',
                ],
            ]);

        $this->assertNotNull($user->fresh()->api_token);
    }
}
