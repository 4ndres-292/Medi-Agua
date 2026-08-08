<?php

namespace Tests\Feature;

use App\Models\Rol;
use App\Models\Socio;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

class SocioTest extends TestCase
{
    use RefreshDatabase;

    private User $admin;

    protected function setUp(): void
    {
        parent::setUp();

        $adminRol = Rol::create(['slug' => 'administrador', 'name' => 'Administrador']);
        $this->admin = User::create([
            'username' => 'Admin',
            'lastname' => 'Test',
            'email' => 'admin@test.com',
            'password' => 'Password123',
            'rol_id' => $adminRol->id,
        ]);

        Sanctum::actingAs($this->admin);
    }

    private function socioData(array $overrides = []): array
    {
        return array_merge([
            'nombres' => 'Maria Elena',
            'apellidos' => 'Garcia Lopez',
            'ci' => '12345678',
            'telefono' => '76543210',
            'direccion' => 'Av. Principal No. 123',
            'estado' => 'activo',
        ], $overrides);
    }

    /*
    |--------------------------------------------------------------------------
    | GET /api/socios
    |--------------------------------------------------------------------------
    */

    public function test_list_socios(): void
    {
        Socio::create($this->socioData(['ci' => '11111111']));
        Socio::create($this->socioData(['ci' => '22222222', 'nombres' => 'Juan']));

        $response = $this->getJson('/api/socios');

        $response->assertStatus(200)
            ->assertJson([
                'success' => true,
                'message' => 'Lista de socios obtenida correctamente.',
            ])
            ->assertJsonStructure([
                'success',
                'message',
                'data' => [
                    'current_page',
                    'data' => [
                        ['id', 'nombres', 'apellidos', 'ci'],
                    ],
                ],
            ]);
    }

    public function test_list_socios_includes_medidores(): void
    {
        $socio = Socio::create($this->socioData());
        $socio->medidores()->create(['codigo' => '001']);

        $response = $this->getJson('/api/socios');

        $response->assertStatus(200);

        $firstSocio = $response->json('data.data.0');
        $this->assertArrayHasKey('medidores', $firstSocio);
    }

    /*
    |--------------------------------------------------------------------------
    | POST /api/socios
    |--------------------------------------------------------------------------
    */

    public function test_create_socio(): void
    {
        $response = $this->postJson('/api/socios', $this->socioData());

        $response->assertStatus(201)
            ->assertJson([
                'success' => true,
                'message' => 'Socio creado correctamente.',
            ]);

        $this->assertDatabaseHas('socios', ['ci' => '12345678']);
    }

    public function test_create_socio_validates_required_fields(): void
    {
        $response = $this->postJson('/api/socios', []);

        $response->assertStatus(422)
            ->assertJsonValidationErrors(['nombres', 'apellidos', 'ci']);
    }

    public function test_create_socio_validates_names_letters_only(): void
    {
        $response = $this->postJson('/api/socios', $this->socioData(['nombres' => 'Maria123']));

        $response->assertStatus(422)
            ->assertJsonValidationErrors(['nombres']);
    }

    public function test_create_socio_validates_apellidos_letters_only(): void
    {
        $response = $this->postJson('/api/socios', $this->socioData(['apellidos' => 'Garcia123']));

        $response->assertStatus(422)
            ->assertJsonValidationErrors(['apellidos']);
    }

    public function test_create_socio_validates_ci_unique(): void
    {
        Socio::create($this->socioData());

        $response = $this->postJson('/api/socios', $this->socioData());

        $response->assertStatus(422)
            ->assertJsonValidationErrors(['ci']);
    }

    public function test_create_socio_validates_ci_digits_only(): void
    {
        $response = $this->postJson('/api/socios', $this->socioData(['ci' => 'ABC123']));

        $response->assertStatus(422)
            ->assertJsonValidationErrors(['ci']);
    }

    public function test_create_socio_validates_estado_values(): void
    {
        $response = $this->postJson('/api/socios', $this->socioData(['estado' => 'suspendido']));

        $response->assertStatus(422)
            ->assertJsonValidationErrors(['estado']);
    }

    public function test_create_socio_accepts_tilde_names(): void
    {
        $response = $this->postJson('/api/socios', $this->socioData([
            'nombres' => 'Maria Jose',
            'apellidos' => 'Garcia Lopez',
        ]));

        $response->assertStatus(201);

        $this->assertDatabaseHas('socios', [
            'nombres' => 'Maria Jose',
            'apellidos' => 'Garcia Lopez',
        ]);
    }

    /*
    |--------------------------------------------------------------------------
    | GET /api/socios/{socio}
    |--------------------------------------------------------------------------
    */

    public function test_show_socio(): void
    {
        $socio = Socio::create($this->socioData());

        $response = $this->getJson("/api/socios/{$socio->id}");

        $response->assertStatus(200)
            ->assertJson([
                'success' => true,
                'data' => [
                    'nombres' => 'Maria Elena',
                    'ci' => '12345678',
                ],
            ]);
    }

    public function test_show_socio_includes_related(): void
    {
        $socio = Socio::create($this->socioData());
        $socio->medidores()->create(['codigo' => '001']);
        $socio->notificaciones()->create([
            'tipo' => 'aviso',
            'mensaje' => 'Test',
            'estado' => 'pendiente',
            'fecha_envio' => now(),
        ]);

        $response = $this->getJson("/api/socios/{$socio->id}");

        $response->assertStatus(200);

        $data = $response->json('data');
        $this->assertArrayHasKey('medidores', $data);
        $this->assertArrayHasKey('facturas', $data);
        $this->assertArrayHasKey('notificaciones', $data);
    }

    public function test_show_nonexistent_socio_returns_404(): void
    {
        $response = $this->getJson('/api/socios/99999');

        $response->assertStatus(404);
    }

    /*
    |--------------------------------------------------------------------------
    | PUT /api/socios/{socio}
    |--------------------------------------------------------------------------
    */

    public function test_update_socio(): void
    {
        $socio = Socio::create($this->socioData());

        $response = $this->putJson("/api/socios/{$socio->id}", [
            'nombres' => 'Ana Maria',
            'apellidos' => 'Garcia Lopez',
            'ci' => '12345678',
            'telefono' => '65432109',
            'direccion' => 'Calle Secundaria No. 456',
            'estado' => 'inactivo',
        ]);

        $response->assertStatus(200)
            ->assertJson([
                'success' => true,
                'message' => 'Socio actualizado correctamente.',
            ]);

        $this->assertDatabaseHas('socios', [
            'id' => $socio->id,
            'nombres' => 'Ana Maria',
            'estado' => 'inactivo',
        ]);
    }

    public function test_update_socio_allows_same_ci(): void
    {
        $socio = Socio::create($this->socioData());

        $response = $this->putJson("/api/socios/{$socio->id}", $this->socioData());

        $response->assertStatus(200);
    }

    public function test_update_socio_rejects_duplicate_ci(): void
    {
        Socio::create($this->socioData(['ci' => '11111111']));
        $socio2 = Socio::create($this->socioData(['ci' => '22222222']));

        $response = $this->putJson("/api/socios/{$socio2->id}", $this->socioData(['ci' => '11111111']));

        $response->assertStatus(422)
            ->assertJsonValidationErrors(['ci']);
    }

    /*
    |--------------------------------------------------------------------------
    | PATCH /api/socios/{socio}
    |--------------------------------------------------------------------------
    */

    public function test_patch_socio(): void
    {
        $socio = Socio::create($this->socioData());

        $response = $this->patchJson("/api/socios/{$socio->id}", $this->socioData([
            'telefono' => '99999999',
        ]));

        $response->assertStatus(200);

        $this->assertDatabaseHas('socios', [
            'id' => $socio->id,
            'telefono' => '99999999',
        ]);
    }

    /*
    |--------------------------------------------------------------------------
    | DELETE /api/socios/{socio}
    |--------------------------------------------------------------------------
    */

    public function test_delete_socio(): void
    {
        $socio = Socio::create($this->socioData());

        $response = $this->deleteJson("/api/socios/{$socio->id}");

        $response->assertStatus(204);

        $this->assertDatabaseMissing('socios', ['id' => $socio->id]);
    }

    public function test_delete_socio_cascades_to_medidores(): void
    {
        $socio = Socio::create($this->socioData());
        $medidor = $socio->medidores()->create(['codigo' => '001']);

        $this->deleteJson("/api/socios/{$socio->id}");

        $this->assertDatabaseMissing('medidores', ['id' => $medidor->id]);
    }

    /*
    |--------------------------------------------------------------------------
    | Auth
    |--------------------------------------------------------------------------
    */

    public function test_unauthenticated_request_returns_401(): void
    {
        $this->app['auth']->forgetGuards();

        $response = $this->getJson('/api/socios');

        $response->assertStatus(401);
    }
}
