# 02 — Arquitectura

Arquitectura interna del módulo de autenticación de Medi-Agua.

## Visión general

El módulo sigue el patrón **Service Layer** de Laravel:

```
Request → Route → Controller → Service → Model → Database
                                │
                                └──▶ Resource → JSON
```

Cada capa tiene una responsabilidad única:

| Capa | Responsabilidad | Archivos |
|------|-----------------|----------|
| Route | Definir URL y middleware | `routes/api.php` |
| Controller | Recibir request, delegar al service | `AuthController.php` |
| Service | Lógica de negocio | `AuthService.php` |
| FormRequest | Validación de datos | `app/Http/Requests/Auth/` |
| Resource | Serializar respuesta | `LoginResource.php`, `UserResource.php` |
| Model | Interactuar con la DB | `User.php`, `Rol.php` |
| ApiResponse | Formato JSON uniforme | `ApiResponse.php` |

## Diagrama de capas

```
┌─────────────────────────────────────────────────────────┐
│                      FRONTEND                           │
│                                                         │
│  React ──▶ api.ts (Axios) ──▶ AuthContext              │
│                    │                                    │
│                    │  Authorization: Bearer <token>     │
│                    ▼                                    │
└─────────────────────────────────────────────────────────┘
                          │
                          │  HTTP/JSON
                          ▼
┌─────────────────────────────────────────────────────────┐
│                     BACKEND                             │
│                                                         │
│  ┌─────────────────────────────────────────────────┐   │
│  │              routes/api.php                      │   │
│  │  Route::post('/login', [AuthController::class,  │   │
│  │                'login'])                         │   │
│  └─────────────────────────────────────────────────┘   │
│                          │                              │
│                          ▼                              │
│  ┌─────────────────────────────────────────────────┐   │
│  │            AuthController.php                    │   │
│  │  - Recibe LoginRequest (validación)              │   │
│  │  - Llama a AuthService                           │   │
│  │  - Retorna ApiResponse                           │   │
│  └─────────────────────────────────────────────────┘   │
│                          │                              │
│                          ▼                              │
│  ┌─────────────────────────────────────────────────┐   │
│  │             AuthService.php                      │   │
│  │  - findUserByEmail()                             │   │
│  │  - validatePassword()                            │   │
│  │  - createAccessToken()                           │   │
│  │  - handleGoogleCallback()                        │   │
│  └─────────────────────────────────────────────────┘   │
│                          │                              │
│                          ▼                              │
│  ┌─────────────────────────────────────────────────┐   │
│  │               Models                             │   │
│  │  User.php ──▶ Rol.php                            │   │
│  │  personal_access_tokens (Sanctum)                │   │
│  └─────────────────────────────────────────────────┘   │
│                          │                              │
│                          ▼                              │
│  ┌─────────────────────────────────────────────────┐   │
│  │            PostgreSQL                            │   │
│  │  users, roles, personal_access_tokens            │   │
│  └─────────────────────────────────────────────────┘   │
└─────────────────────────────────────────────────────────┘
```

## Controllers

### AuthController

**Archivo:** `app/Http/Controllers/AuthController.php`

Controlador único que maneja los 9 endpoints de autenticación.

```
AuthController
├── login()              POST /login
├── register()           POST /register
├── me()                 GET /me
├── logout()             POST /logout
├── changePassword()     POST /change-password
├── forgotPassword()     POST /forgot-password
├── resetPassword()      POST /reset-password
├── googleRedirect()     GET /auth/google/redirect
└── googleCallback()     GET /auth/google/callback
```

**Responsabilidades:**
- Recibir la request HTTP
- Delegar validación a FormRequest
- Llamar a AuthService para la lógica de negocio
- Retornar respuesta usando ApiResponse

**Qué NO hace:**
- No valida datos (eso lo hace FormRequest)
- No interactúa con la base de datos (eso lo hace el Model)
- No tiene lógica de negocio (eso lo hace el Service)

## Services

### AuthService

**Archivo:** `app/Services/AuthService.php`

Servicio central que contiene toda la lógica de autenticación.

```
AuthService
├── login()                    Login con email/password
├── register()                 Registro de usuario nuevo
├── me()                       Obtener datos del usuario
├── logout()                   Eliminar token actual
├── changePassword()           Cambiar contraseña
├── getGoogleRedirectUrl()     URL de redirección a Google
├── handleGoogleCallback()     Procesar callback de Google
├── findOrCreateGoogleUser()   Buscar o crear usuario Google
├── getComunRoleId()           Obtener rol "Comun"
├── findUserByEmail()          Buscar usuario por email
├── validatePassword()         Verificar contraseña
└── createAccessToken()        Generar token Sanctum
```

**Responsabilidades:**
- Contener toda la lógica de negocio
- Interactuar con los modelos (User, Rol)
- Generar tokens de Sanctum
- Manejar la integración con Google (Socialite)

**Patrón de retorno:**

Todos los métodos que generan sesión retornan:
```php
[
    'user'  => User,    // Modelo del usuario
    'token' => string,  // Token Sanctum en texto plano
]
```

## FormRequests

Los FormRequests validan los datos ANTES de que lleguen al Controller.

```
app/Http/Requests/Auth/
├── LoginRequest.php          Valida email + password
├── RegisterRequest.php       Valida username, lastname, email, password
├── ChangePasswordRequest.php Valifica current_password + password
├── ForgotPasswordRequest.php Valida email
└── ResetPasswordRequest.php  Valida token, email, password
```

**Cómo funciona:**

1. El request llega al Controller
2. Laravel detecta el FormRequest en el tipo del parámetro
3. Laravel ejecuta `rules()` del FormRequest
4. Si falla, retorna 422 automáticamente
5. Si pasa, el Controller recibe los datos validados con `$request->validated()`

**Ejemplo:**

```php
// En AuthController
public function login(LoginRequest $request): JsonResponse
{
    // Si llegamos aquí, los datos ya son válidos
    $data = $request->validated(); // ['email' => '...', 'password' => '...']
    
    return $this->authService->login($data);
}
```

## Resources

Los Resources transforman los modelos en JSON.

```
app/Http/Resources/
├── LoginResource.php    Formato: { user, token }
└── UserResource.php     Formato: { id, username, lastname, email, role }
```

### LoginResource

Retorna el formato para login y register:

```json
{
    "user": {
        "id": 1,
        "username": "Juan",
        "lastname": "Pérez",
        "email": "juan@example.com",
        "role": {
            "id": 4,
            "name": "Comun"
        }
    },
    "token": "1|abc123..."
}
```

### UserResource

Retorna los datos del usuario sin token:

```json
{
    "id": 1,
    "username": "Juan",
    "lastname": "Pérez",
    "email": "juan@example.com",
    "role": {
        "id": 4,
        "name": "Comun"
    }
}
```

## Models

### User

**Archivo:** `app/Models/User.php`

```php
class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable;

    protected $fillable = [
        'username', 'lastname', 'email', 'password',
        'rol_id', 'google_id', 'avatar',
    ];

    protected $hidden = ['password'];

    protected function casts(): array
    {
        return ['password' => 'hashed'];
    }

    public function rol(): BelongsTo { ... }
    public function lecturas(): HasMany { ... }
}
```

**Puntos importantes:**
- `HasApiTokens` — Habilita la generación de tokens de Sanctum
- `cast 'hashed'` — La contraseña se hashea automáticamente al guardar
- `$hidden = ['password']` — Nunca se retorna la contraseña en JSON

### Rol

**Archivo:** `app/Models/Rol.php`

```php
class Rol extends Model
{
    protected $table = 'roles';
    protected $fillable = ['slug', 'name'];
    
    public function users(): HasMany { ... }
}
```

## Middleware

### auth:sanctum

Protege rutas que requieren autenticación. Si no hay token válido, retorna 401.

```php
Route::middleware('auth:sanctum')->group(function () {
    Route::get('/me', ...);
    Route::post('/logout', ...);
});
```

### role:admin

Verifica que el usuario autenticado tenga el rol especificado.

```php
Route::middleware('role:admin')->group(function () {
    Route::apiResource('users', UserController::class);
});
```

### throttle:5,1

Limita a 5 intentos por minuto. Solo aplicado a `/login`.

```php
Route::post('/login', ...)->middleware('throttle:5,1');
```

## Sanctum

Laravel Sanctum provee tokens de acceso personal para autenticación basada en tokens.

**Flujo:**

```
1. Usuario hace login
2. Backend crea token: $user->createToken('auth-token')
3. Backend retorna token en texto plano: "1|abc123..."
4. Frontend guarda token en localStorage
5. Frontend envía token en cada request: Authorization: Bearer 1|abc123...
6. Sanctum valida el token en la DB
7. Si es válido, permite el acceso
```

**Tabla `personal_access_tokens`:**

| Campo | Descripción |
|-------|-------------|
| id | ID del token |
| tokenable_type | Tipo de modelo (User) |
| tokenable_id | ID del usuario |
| name | Nombre del token ('auth-token') |
| token | Hash del token |
| abilities | Permisos del token |
| last_used_at | Último uso |
| expires_at | Fecha de expiración (null = nunca) |

## Password Broker

Laravel maneja la recuperación de contraseña nativamente.

**Flujo:**

```
1. POST /forgot-password con email
2. Password::sendResetLink() genera token
3. Token se guarda en password_reset_tokens
4. Email se envía (en dev, se guarda en logs)
5. Usuario recibe token
6. POST /reset-password con token + nueva contraseña
7. Password::reset() valida token y actualiza contraseña
8. Todos los tokens del usuario se eliminan
```

**Tabla `password_reset_tokens`:**

| Campo | Descripción |
|-------|-------------|
| email | Email del usuario |
| token | Hash del token de reset |
| created_at | Cuándo se creó |

## Socialite

Laravel Socialite simplifica la autenticación OAuth con proveedores externos.

**Flujo:**

```
1. Frontend redirige a /auth/google/redirect
2. Socialite genera URL de Google con scopes
3. Google muestra pantalla de autorización
4. Usuario autoriza
5. Google redirige a /auth/google/callback
6. Socialite intercambia code por token
7. Socialite obtiene datos del usuario (id, email, name, avatar)
8. AuthService busca o crea el usuario
9. Se genera token Sanctum
10. Se retorna al frontend
```

## ApiResponse

Formato JSON uniforme para todas las respuestas.

**Respuesta exitosa:**
```json
{
    "success": true,
    "message": "Operación exitosa.",
    "data": { ... },
    "errors": null
}
```

**Respuesta de error:**
```json
{
    "success": false,
    "message": "Error occurred.",
    "data": null,
    "errors": { ... }
}
```

## Flujo completo de Register

```
Frontend                Backend                   DB
   │                      │                        │
   │  POST /register      │                        │
   │  { username, email,  │                        │
   │    password }        │                        │
   │─────────────────────▶│                        │
   │                      │                        │
   │                      │  RegisterRequest       │
   │                      │  Valida:               │
   │                      │  - username (required,  │
   │                      │    regex \p{L})         │
   │                      │  - lastname (required,  │
   │                      │    regex \p{L})         │
   │                      │  - email (required,     │
   │                      │    unique)              │
   │                      │  - password (required,  │
   │                      │    min:8, confirmed)    │
   │                      │                        │
   │                      │  AuthService::register │
   │                      │  User::create()        │
   │                      │───────────────────────▶│
   │                      │                        │ INSERT users
   │                      │                        │
   │                      │  $user->load('rol')    │
   │                      │───────────────────────▶│
   │                      │                        │ SELECT roles
   │                      │                        │
   │                      │  createToken()         │
   │                      │───────────────────────▶│
   │                      │                        │ INSERT tokens
   │                      │                        │
   │                      │  LoginResource         │
   │                      │  ApiResponse::success  │
   │                      │                        │
   │  201 + { user, token }│                       │
   │◀─────────────────────│                        │
```

## Flujo completo de Login

```
Frontend                Backend                   DB
   │                      │                        │
   │  POST /login         │                        │
   │  { email, password } │                        │
   │─────────────────────▶│                        │
   │                      │                        │
   │                      │  LoginRequest          │
   │                      │  Valida:               │
   │                      │  - email (required,     │
   │                      │    email)               │
   │                      │  - password (required,  │
   │                      │    min:8)               │
   │                      │                        │
   │                      │  AuthService::login    │
   │                      │                        │
   │                      │  findUserByEmail()     │
   │                      │───────────────────────▶│
   │                      │                        │ SELECT users
   │                      │                        │
   │                      │  validatePassword()    │
   │                      │  Hash::check()         │
   │                      │                        │
   │                      │  load('rol')           │
   │                      │───────────────────────▶│
   │                      │                        │ SELECT roles
   │                      │                        │
   │                      │  createToken()         │
   │                      │───────────────────────▶│
   │                      │                        │ INSERT tokens
   │                      │                        │
   │  200 + { user, token }│                       │
   │◀─────────────────────│                        │
```
