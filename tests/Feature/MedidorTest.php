<?php

namespace Tests\Feature;

use App\Models\Medidor;
use App\Models\Rol;
use App\Models\Socio;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

class MedidorTest extends TestCase
{
    use RefreshDatabase;

    private User $admin;
    private Socio $socio;

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

        $this->socio = Socio::create([
            'nombres' => 'Maria Elena',
            'apellidos' => 'Garcia Lopez',
            'ci' => '12345678',
            'estado' => 'activo',
        ]);

        Sanctum::actingAs($this->admin);
    }

    private function medidorData(array $overrides = []): array
    {
        return array_merge([
            'codigo' => '000010',
            'socio_id' => $this->socio->id,
            'observacion' => 'Medidor principal',
        ], $overrides);
    }

    /*
    |--------------------------------------------------------------------------
    | GET /api/medidores
    |--------------------------------------------------------------------------
    */

    public function test_list_medidores(): void
    {
        Medidor::create($this->medidorData(['codigo' => '001']));
        Medidor::create($this->medidorData(['codigo' => '002']));

        $response = $this->getJson('/api/medidores');

        $response->assertStatus(200)
            ->assertJson([
                'success' => true,
                'message' => 'Lista de medidores obtenida correctamente.',
            ])
            ->assertJsonStructure([
                'success',
                'message',
                'data' => [
                    'current_page',
                    'data' => [
                        ['id', 'codigo', 'socio_id'],
                    ],
                ],
            ]);
    }

    public function test_list_medidores_includes_socio(): void
    {
        Medidor::create($this->medidorData());

        $response = $this->getJson('/api/medidores');

        $response->assertStatus(200);

        $firstMedidor = $response->json('data.data.0');
        $this->assertArrayHasKey('socio', $firstMedidor);
    }

    /*
    |--------------------------------------------------------------------------
    | POST /api/medidores
    |--------------------------------------------------------------------------
    */

    public function test_create_medidor(): void
    {
        $response = $this->postJson('/api/medidores', $this->medidorData());

        $response->assertStatus(201)
            ->assertJson([
                'success' => true,
                'message' => 'Medidor creado correctamente.',
            ]);

        $this->assertDatabaseHas('medidores', ['codigo' => '000010']);
    }

    public function test_create_medidor_validates_required_fields(): void
    {
        $response = $this->postJson('/api/medidores', []);

        $response->assertStatus(422)
            ->assertJsonValidationErrors(['codigo', 'socio_id']);
    }

    public function test_create_medidor_validates_codigo_unique(): void
    {
        Medidor::create($this->medidorData());

        $response = $this->postJson('/api/medidores', $this->medidorData());

        $response->assertStatus(422)
            ->assertJsonValidationErrors(['codigo']);
    }

    public function test_create_medidor_validates_socio_exists(): void
    {
        $response = $this->postJson('/api/medidores', $this->medidorData(['socio_id' => 99999]));

        $response->assertStatus(422)
            ->assertJsonValidationErrors(['socio_id']);
    }

    public function test_create_medidor_with_null_observacion(): void
    {
        $response = $this->postJson('/api/medidores', $this->medidorData(['observacion' => null]));

        $response->assertStatus(201);

        $this->assertDatabaseHas('medidores', [
            'codigo' => '000010',
        ]);
    }

    public function test_create_medidor_without_observacion(): void
    {
        $data = $this->medidorData();
        unset($data['observacion']);

        $response = $this->postJson('/api/medidores', $data);

        $response->assertStatus(201);
    }

    public function test_create_medidor_rejects_unknown_fields(): void
    {
        $response = $this->postJson('/api/medidores', $this->medidorData([
            'ubicacion' => 'Some location',
            'estado' => 'activo',
        ]));

        $response->assertStatus(201);

        $medidor = Medidor::where('codigo', '000010')->first();
        $this->assertNull($medidor->ubicacion ?? null);
        $this->assertNull($medidor->estado ?? null);
    }

    /*
    |--------------------------------------------------------------------------
    | GET /api/medidores/{medidor}
    |--------------------------------------------------------------------------
    */

    public function test_show_medidor(): void
    {
        $medidor = Medidor::create($this->medidorData());

        $response = $this->getJson("/api/medidores/{$medidor->id}");

        $response->assertStatus(200)
            ->assertJson([
                'success' => true,
                'message' => 'Medidor obtenido correctamente.',
            ]);

        $data = $response->json('data');
        $this->assertEquals('000010', $data['codigo']);
        $this->assertEquals($this->socio->id, $data['socio_id']);
    }

    public function test_show_medidor_includes_socio_and_lecturas(): void
    {
        $medidor = Medidor::create($this->medidorData());

        $response = $this->getJson("/api/medidores/{$medidor->id}");

        $response->assertStatus(200);

        $data = $response->json('data');
        $this->assertArrayHasKey('socio', $data);
        $this->assertArrayHasKey('lecturas', $data);
    }

    public function test_show_medidor_only_contains_allowed_fields(): void
    {
        $medidor = Medidor::create($this->medidorData());

        $response = $this->getJson("/api/medidores/{$medidor->id}");

        $data = $response->json('data');
        $this->assertArrayHasKey('id', $data);
        $this->assertArrayHasKey('codigo', $data);
        $this->assertArrayHasKey('socio_id', $data);
        $this->assertArrayHasKey('observacion', $data);
        $this->assertArrayNotHasKey('ubicacion', $data);
        $this->assertArrayNotHasKey('estado', $data);
    }

    /*
    |--------------------------------------------------------------------------
    | PUT /api/medidores/{medidor}
    |--------------------------------------------------------------------------
    */

    public function test_update_medidor(): void
    {
        $medidor = Medidor::create($this->medidorData());

        $response = $this->putJson("/api/medidores/{$medidor->id}", [
            'codigo' => '000099',
            'socio_id' => $this->socio->id,
            'observacion' => 'Updated observation',
        ]);

        $response->assertStatus(200)
            ->assertJson([
                'success' => true,
                'message' => 'Medidor actualizado correctamente.',
            ]);

        $this->assertDatabaseHas('medidores', [
            'id' => $medidor->id,
            'codigo' => '000099',
        ]);
    }

    public function test_update_medidor_allows_same_codigo(): void
    {
        $medidor = Medidor::create($this->medidorData());

        $response = $this->putJson("/api/medidores/{$medidor->id}", [
            'codigo' => '000010',
            'socio_id' => $this->socio->id,
        ]);

        $response->assertStatus(200);
    }

    public function test_update_medidor_rejects_duplicate_codigo(): void
    {
        Medidor::create($this->medidorData(['codigo' => '001']));
        $medidor2 = Medidor::create($this->medidorData(['codigo' => '002']));

        $response = $this->putJson("/api/medidores/{$medidor2->id}", [
            'codigo' => '001',
            'socio_id' => $this->socio->id,
        ]);

        $response->assertStatus(422)
            ->assertJsonValidationErrors(['codigo']);
    }

    /*
    |--------------------------------------------------------------------------
    | PATCH /api/medidores/{medidor}
    |--------------------------------------------------------------------------
    */

    public function test_patch_medidor(): void
    {
        $medidor = Medidor::create($this->medidorData());

        $response = $this->patchJson("/api/medidores/{$medidor->id}", [
            'codigo' => $medidor->codigo,
            'socio_id' => $this->socio->id,
            'observacion' => 'Patched observation',
        ]);

        $response->assertStatus(200);

        $this->assertDatabaseHas('medidores', [
            'id' => $medidor->id,
            'observacion' => 'Patched observation',
        ]);
    }

    /*
    |--------------------------------------------------------------------------
    | DELETE /api/medidores/{medidor}
    |--------------------------------------------------------------------------
    */

    public function test_delete_medidor(): void
    {
        $medidor = Medidor::create($this->medidorData());

        $response = $this->deleteJson("/api/medidores/{$medidor->id}");

        $response->assertStatus(204);

        $this->assertDatabaseMissing('medidores', ['id' => $medidor->id]);
    }

    public function test_delete_medidor_cascades_to_lecturas(): void
    {
        $medidor = Medidor::create($this->medidorData());
        $medidor->lecturas()->create([
            'lectura_anterior' => 0,
            'lectura_actual' => 100,
            'consumo' => 100,
            'usuario_id' => $this->admin->id,
            'fecha_lectura' => now()->toDateString(),
        ]);

        $this->deleteJson("/api/medidores/{$medidor->id}");

        $this->assertDatabaseCount('lecturas', 0);
    }

    /*
    |--------------------------------------------------------------------------
    | Auth
    |--------------------------------------------------------------------------
    */

    public function test_unauthenticated_request_returns_401(): void
    {
        $this->app['auth']->forgetGuards();

        $response = $this->getJson('/api/medidores');

        $response->assertStatus(401);
    }
}
