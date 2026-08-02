# 08 — Tests

Documentación completa de la suite de tests del módulo de autenticación.

## Resumen

| Métrica | Valor |
|---------|-------|
| Total de tests | 86 |
| Aserciones | 252 |
| Tiempo de ejecución | ~3.7 segundos |
| Base de datos | SQLite en memoria |
| Framework de tests | PHPUnit (Laravel) |

## Cómo ejecutar todos los tests

```bash
php artisan test
```

## Cómo ejecutar solo los tests de autenticación

```bash
php artisan test --filter=Auth
```

## Cómo ejecutar un archivo específico

```bash
php artisan test --filter=RegisterTest
php artisan test --filter=GoogleAuthTest
```

## Cómo ejecutar un test específico

```bash
php artisan test --filter="RegisterTest::test_new_users_can_register"
```

## Organización de los tests

```
tests/Feature/Auth/
├── RegisterTest.php          19 tests
├── LoginTest.php             13 tests
├── LogoutTest.php             6 tests
├── UnauthenticatedTest.php    4 tests
├── ChangePasswordTest.php     7 tests
├── ForgotPasswordTest.php     7 tests
├── ResetPasswordTest.php      8 tests
└── GoogleAuthTest.php        22 tests
                            ───────
                            86 tests
```

## RegisterTest.php (19 tests)

**Archivo:** `tests/Feature/Auth/RegisterTest.php`

### Qué verifica

| Test | Qué verifica |
|------|-------------|
| `test_new_users_can_register` | Registro exitoso retorna 201 + token |
| `test_email_is_required` | Email es obligatorio |
| `test_email_must_be_valid` | Email debe tener formato válido |
| `test_email_must_be_unique` | Email no puede estar duplicado |
| `test_password_is_required` | Password es obligatorio |
| `test_password_must_be_at_least_8_characters` | Password mínimo 8 caracteres |
| `test_password_confirmation_is_required` | password_confirmation es obligatorio |
| `test_password_confirmation_must_match` | password_confirmation debe coincidir |
| `test_username_is_required` | username es obligatorio |
| `test_lastname_is_required` | lastname es obligatorio |
| `test_username_only_accepts_letters` | username solo acepta letras y espacios |
| `test_lastname_only_accepts_letters` | lastname solo acepta letras y espacios |
| `test_user_gets_default_role` | Usuario nuevo recibe rol "Comun" |
| `test_password_is_hashed_in_database` | Password se hashea en la DB |
| `test_token_is_generated` | Se genera token Sanctum |
| `test_response_contains_user_data` | Respuesta contiene datos del usuario |
| `test_password_must_contain_uppercase` | Password requiere al menos una mayúscula |
| `test_password_must_contain_lowercase` | Password requiere al menos una minúscula |
| `test_password_must_contain_digit` | Password requiere al menos un número |

### Ejemplo de test

```php
public function test_new_users_can_register(): void
{
    $response = $this->postJson('/api/register', $this->validData);

    $response->assertStatus(201)
        ->assertJsonStructure([
            'success', 'message',
            'data' => [
                'user' => ['id', 'username', 'lastname', 'email', 'role'],
                'token',
            ],
        ]);

    $this->assertDatabaseHas('users', [
        'email' => 'juan@example.com',
        'username' => 'Juan',
    ]);
}
```

## UnauthenticatedTest.php (4 tests)

**Archivo:** `tests/Feature/Auth/UnauthenticatedTest.php`

### Qué verifica

| Test | Qué verifica |
|------|-------------|
| `test_unauthenticated_request_returns_401` | Request sin token retorna 401 |
| `test_invalid_token_returns_401` | Token inválido retorna 401 |
| `test_deleted_token_returns_401` | Token eliminado retorna 401 |
| `test_response_is_json_not_html` | Respuesta es JSON, no HTML |

## LoginTest.php (13 tests)

**Archivo:** `tests/Feature/Auth/LoginTest.php`

### Qué verifica

| Test | Qué verifica |
|------|-------------|
| `test_user_can_login_with_valid_credentials` | Login exitoso retorna 200 + token |
| `test_email_is_required` | Email es obligatorio |
| `test_password_is_required` | Password es obligatorio |
| `test_email_must_be_valid_format` | Email debe tener formato válido |
| `test_user_cannot_login_with_wrong_password` | Password incorrecto retorna 422 |
| `test_user_cannot_login_with_nonexistent_email` | Email inexistente retorna 422 |
| `test_token_is_returned_on_successful_login` | Se genera token en login exitoso |
| `test_user_data_is_returned_on_successful_login` | Se retornan datos del usuario |
| `test_role_is_included_in_user_data` | El rol del usuario se incluye |
| `test_password_is_not_returned_in_response` | Password NO se retorna en respuesta |
| `test_multiple_tokens_are_created_per_login` | Cada login crea un nuevo token |
| `test_response_format_matches_standard` | Formato ApiResponse estándar |
| `test_email_field_is_case_insensitive` | Email no distingue mayúsculas/minúsculas |

## LogoutTest.php (6 tests)

**Archivo:** `tests/Feature/Auth/LogoutTest.php`

### Qué verifica

| Test | Qué verifica |
|------|-------------|
| `test_authenticated_user_can_logout` | Logout exitoso retorna 200 |
| `test_unauthenticated_user_cannot_logout` | Sin token retorna 401 |
| `test_token_is_deleted_on_logout` | Token se elimina de la DB |
| `test_logout_returns_success_response` | Formato ApiResponse estándar |
| `test_only_current_token_is_deleted` | Solo se elimina el token actual |
| `test_invalid_token_returns_401_on_me` | Token inválido retorna 401 en /me |

### Ejemplo de test

```php
public function test_unauthenticated_request_returns_401(): void
{
    $response = $this->getJson('/api/me');

    $response->assertStatus(401)
        ->assertJson([
            'success' => false,
            'message' => 'Unauthenticated.',
        ]);
}
```

## ChangePasswordTest.php (7 tests)

**Archivo:** `tests/Feature/Auth/ChangePasswordTest.php`

### Qué verifica

| Test | Qué verifica |
|------|-------------|
| `test_password_changed_correctly` | Contraseña se cambia exitosamente |
| `test_current_password_incorrect` | Password actual incorrecto retorna 422 |
| `test_password_confirmation_does_not_match` | Confirmación no coincide retorna 422 |
| `test_password_too_short` | Password menor a 8 caracteres retorna 422 |
| `test_required_fields` | Campos obligatorios retornan 422 |
| `test_tokens_are_invalidated_after_change` | TODOS los tokens se eliminan |
| `test_unauthenticated_request_returns_401` | Sin token retorna 401 |

### Ejemplo de test

```php
public function test_tokens_are_invalidated_after_change(): void
{
    $token1 = $this->user->createToken('token-1')->plainTextToken;
    $token2 = $this->user->createToken('token-2')->plainTextToken;

    $this->assertDatabaseCount('personal_access_tokens', 2);

    $response = $this->withHeader('Authorization', "Bearer $token1")
        ->postJson('/api/change-password', [
            'current_password' => $this->currentPassword,
            'password' => 'newpassword123',
            'password_confirmation' => 'newpassword123',
        ]);

    $response->assertStatus(200);
    $this->assertDatabaseCount('personal_access_tokens', 0);
}
```

## ForgotPasswordTest.php (7 tests)

**Archivo:** `tests/Feature/Auth/ForgotPasswordTest.php`

### Qué verifica

| Test | Qué verifica |
|------|-------------|
| `test_existing_email_returns_success` | Email existente retorna 200 |
| `test_nonexistent_email_returns_same_success` | Email inexistente retorna 200 (misma respuesta) |
| `test_invalid_email_returns_422` | Email inválido retorna 422 |
| `test_email_is_required` | Email es obligatorio |
| `test_response_is_identical_for_existing_and_nonexistent_email` | Respuesta idéntica para prevenir user enumeration |
| `test_reset_token_is_created_for_existing_user` | Token se crea en password_reset_tokens |
| `test_no_token_created_for_nonexistent_user` | No se crea token para email inexistente |

### Ejemplo de test

```php
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
}
```

## ResetPasswordTest.php (8 tests)

**Archivo:** `tests/Feature/Auth/ResetPasswordTest.php`

### Qué verifica

| Test | Qué verifica |
|------|-------------|
| `test_password_reset_successfully` | Reset exitoso retorna 200 |
| `test_invalid_token_returns_422` | Token inválido retorna 422 |
| `test_tokens_are_invalidated_after_reset` | TODOS los tokens se eliminan |
| `test_password_confirmation_required` | password_confirmation es obligatorio |
| `test_password_confirmation_must_match` | Confirmación debe coincidir |
| `test_password_too_short` | Password menor a 8 caracteres retorna 422 |
| `test_required_fields` | Campos obligatorios retornan 422 |
| `test_invalid_email_returns_422` | Email inválido retorna 422 |

### Ejemplo de test

```php
public function test_tokens_are_invalidated_after_reset(): void
{
    $this->user->createToken('token-1')->plainTextToken;
    $this->user->createToken('token-2')->plainTextToken;

    $this->assertDatabaseCount('personal_access_tokens', 2);

    $token = $this->generateResetToken();

    $response = $this->postJson('/api/reset-password', [
        'token' => $token,
        'email' => 'test@example.com',
        'password' => 'newpassword123',
        'password_confirmation' => 'newpassword123',
    ]);

    $response->assertStatus(200);
    $this->assertDatabaseCount('personal_access_tokens', 0);
}
```

## GoogleAuthTest.php (22 tests)

**Archivo:** `tests/Feature/Auth/GoogleAuthTest.php`

### Qué verifica

| Test | Qué verifica |
|------|-------------|
| `test_google_redirect_returns_302` | Redirect a Google retorna 302 |
| `test_google_redirect_sends_to_google_oauth` | URL correcta de Google |
| `test_google_callback_creates_new_user` | Crea usuario nuevo |
| `test_google_callback_assigns_comun_role` | Asigna rol "Comun" |
| `test_google_callback_generates_sanctum_token` | Genera token Sanctum |
| `test_google_callback_returns_user_data` | Retorna datos del usuario |
| `test_google_callback_stores_avatar` | Guarda avatar de Google |
| `test_google_callback_creates_random_password` | Crea password aleatorio |
| `test_new_google_user_has_empty_lastname` | lastname es vacío |
| `test_google_callback_links_existing_user` | Vincula usuario existente |
| `test_google_callback_does_not_create_duplicate` | No crea duplicados |
| `test_google_callback_preserves_existing_role` | Mantiene rol existente |
| `test_existing_user_gets_token_on_google_login` | Token para usuario existente |
| `test_google_user_role_comes_from_database` | Rol viene de la DB |
| `test_google_user_role_not_hardcoded` | Rol no está hardcodeado |
| `test_google_callback_handles_invalid_state` | Error manejado |
| `test_google_callback_handles_general_exception` | Excepción manejada |
| `test_google_callback_rejects_unverified_email` | Rechaza email no verificado |
| `test_google_callback_rejects_null_email` | Rechaza email null |
| `test_response_uses_standard_api_format` | Formato ApiResponse estándar |
| `test_google_callback_returns_same_format_as_login` | Mismo formato que login |

### Ejemplo de test

```php
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
    ]);
}
```

## Interpretación de resultados

### Todos los tests pasan

```
 Tests:  86 passed (252 assertions)
 Time:   3.7s
```

### Algún test falla

```
 Tests:  1 failed, 60 passed (175 assertions)

  • Tests\Feature\Auth\RegisterTest::test_email_is_required
  Failed asserting that 404 is identical to 422.
```

**Qué significa:** El test `test_email_is_required` falló porque esperaba 422 pero recibió 404.

### Errores de base de datos

```
 ErrorException: SQLSTATE[HY000]: General error: 1 no such table: users
```

**Solución:** Ejecutar migraciones de testing:

```bash
php artisan migrate --env=testing
```

## Cómo crear un nuevo test

### Paso 1: Crear archivo

```bash
php artisan make:test LoginTest
```

Esto crea `tests/Feature/LoginTest.php`.

### Paso 2: Escribir el test

```php
<?php

namespace Tests\Feature;

use App\Models\Rol;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class LoginTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        Rol::create(['slug' => 'comun', 'name' => 'Comun']);
    }

    public function test_user_can_login(): void
    {
        User::create([
            'username' => 'Juan',
            'lastname' => 'Pérez',
            'email' => 'juan@example.com',
            'password' => 'secret123',
            'rol_id' => 4,
        ]);

        $response = $this->postJson('/api/login', [
            'email' => 'juan@example.com',
            'password' => 'secret123',
        ]);

        $response->assertStatus(200)
            ->assertJsonStructure([
                'success', 'message',
                'data' => ['user', 'token'],
            ]);
    }
}
```

### Paso 3: Ejecutar

```bash
php artisan test --filter=LoginTest
```

## Cobertura actual

| Endpoint | Tests | Estado |
|----------|-------|--------|
| POST /register | 19 | ✅ Completo |
| POST /login | 13 | ✅ Completo |
| GET /me | 4 | ✅ Completo |
| POST /logout | 6 | ✅ Completo |
| POST /change-password | 7 | ✅ Completo |
| POST /forgot-password | 7 | ✅ Completo |
| POST /reset-password | 8 | ✅ Completo |
| GET /google/* | 22 | ✅ Completo |

## Configuración de testing

El archivo `phpunit.xml` configura el entorno de testing:

```xml
<php>
    <env name="APP_ENV" value="testing"/>
    <env name="BCRYPT_ROUNDS" value="4"/>
    <env name="DB_CONNECTION" value="sqlite"/>
    <env name="DB_DATABASE" value=":memory:"/>
    <env name="MAIL_MAILER" value="array"/>
    <env name="QUEUE_CONNECTION" value="sync"/>
    <env name="SESSION_DRIVER" value="array"/>
</php>
```

**Puntos importantes:**

- `DB_CONNECTION=sqlite` — Usa SQLite en memoria para tests rápidos
- `BCRYPT_ROUNDS=4` — Reduce rounds de bcrypt para velocidad
- `MAIL_MAILER=array` — No envía emails reales
- `QUEUE_CONNECTION=sync` — Cola síncrona (sin workers)
